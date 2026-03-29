<?php

namespace App\Services;

use App\Jobs\SendOrderNotifications;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Notifications\LowStockAlert;
use App\Notifications\NewOrderAdmin;
use App\Notifications\OrderConfirmed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrderService
{
    public function __construct(protected CartService $cartService) {}

    public function createFromCart(array $data): Order
    {
        // ── Étape 1 : Créer la commande en base (transaction) ─────────────
        $order = DB::transaction(function () use ($data) {

            $cart  = $this->cartService->getCart();
            $items = $cart->items->load('product', 'variant');

            if ($items->isEmpty()) {
                throw new \Exception('Le panier est vide.');
            }

            // Calculs financiers
            $subtotal       = $items->sum('subtotal');
            $discountAmount = 0;
            $coupon         = null;
            $couponId       = null;
            $couponCode     = null;

            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', $data['coupon_code'])->first();
                if ($coupon && $coupon->isValid() && $subtotal >= ($coupon->min_order_amount ?? 0)) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $couponId       = $coupon->id;
                    $couponCode     = $coupon->code;
                }
            }

            // TVA à 0 côté client — l'admin gère les prix TTC directement dans les produits
            $taxAmount      = 0;
            $threshold      = (float) setting('free_shipping_threshold', 30000);
            $shipPrice      = (float) setting('shipping_price', 2000);

            // Si retrait en boutique → frais de livraison = 0
            if (($data['delivery_type'] ?? 'delivery') === 'pickup') {
                $shippingAmount = 0;
            } else {
                $shippingAmount = $subtotal >= $threshold ? 0 : $shipPrice;
            }
            $total          = $subtotal - $discountAmount + $shippingAmount;

            // user_id : connecté ou invité (null)
            $userId = Auth::id();

            $order = Order::create([
                'user_id'          => $userId,
                'status'           => 'pending',
                'payment_status'   => 'pending',
                'payment_method'   => $data['payment_method'] ?? 'cod',
                'subtotal'         => $subtotal,
                'tax_amount'       => $taxAmount,
                'shipping_amount'  => $shippingAmount,
                'discount_amount'  => $discountAmount,
                'total'            => $total,
                'coupon_id'        => $couponId,
                'coupon_code'      => $couponCode,
                'shipping_name'    => $data['shipping_name'],
                'shipping_email'   => $data['shipping_email'],
                'shipping_phone'   => $data['shipping_phone'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'shipping_city'    => $data['shipping_city'],
                'shipping_state'   => $data['shipping_state'] ?? null,
                'shipping_zip'     => $data['shipping_zip'] ?? '',
                'shipping_country' => $data['shipping_country'] ?? 'CI',
                'billing_name'     => $data['billing_name'] ?? $data['shipping_name'],
                'billing_email'    => $data['billing_email'] ?? $data['shipping_email'],
                'billing_address'  => $data['billing_address'] ?? $data['shipping_address'],
                'billing_city'     => $data['billing_city'] ?? $data['shipping_city'],
                'billing_zip'      => $data['billing_zip'] ?? $data['shipping_zip'] ?? '',
                'billing_country'  => $data['billing_country'] ?? $data['shipping_country'] ?? 'CI',
                'notes'            => $data['notes'] ?? null,
            ]);

            // Lignes de commande + vérification stock + décrément
            foreach ($items as $item) {
                // ── Vérification stock suffisant ──────────────────────────
                if ($item->product->quantity < $item->quantity) {
                    throw new \Exception(
                        "Stock insuffisant pour « {$item->product->name} » " .
                        "(disponible : {$item->product->quantity}, demandé : {$item->quantity})."
                    );
                }

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name'       => $item->product->name,
                    'sku'        => $item->product->sku ?? null,
                    'quantity'   => $item->quantity,
                    'price'      => $item->price,
                    'total'      => $item->subtotal,
                    'options'    => $item->options,
                ]);

                $item->product->decrement('quantity', $item->quantity);

                // ── Alerte stock bas après décrément ──────────────────────
                $freshProduct = $item->product->fresh();
                if ($freshProduct && $freshProduct->isLowStock()) {
                    User::whereIn('role', ['admin', 'manager'])
                        ->each(fn ($admin) => $admin->notify(new LowStockAlert($freshProduct)));
                }
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $this->cartService->clear();

            return $order;
        });

        // Mémoriser la commande en session pour les invités (accès à la page succès)
        if (! Auth::check()) {
            Session::put('guest_order_id', $order->id);
        }

        // ── Étape 2 : Envoi des notifications (hors transaction) ──────────
        $order->load('items');
        $this->dispatchNotifications($order);

        return $order;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Notifications : envoi immédiat, fallback sur queue si mail KO
    // ─────────────────────────────────────────────────────────────────────────

    protected function dispatchNotifications(Order $order): void
    {
        // ── WhatsApp : toujours dispatché EN PREMIER, indépendamment du mail ──
        try {
            \App\Jobs\SendWhatsAppOrderConfirmation::dispatch($order->id)
                ->delay(now()->addSeconds(3));
            Log::info("[OrderService] Job WhatsApp dispatché pour commande #{$order->order_number}.");
        } catch (\Throwable $e) {
            Log::warning("[OrderService] Impossible de dispatcher WhatsApp pour #{$order->order_number} : {$e->getMessage()}");
        }

        // ── Mail : envoi immédiat, fallback queue si KO ───────────────────
        try {
            $this->sendNow($order);
            Log::info("[OrderService] Notifications email envoyées pour commande #{$order->order_number}.");
        } catch (\Throwable $e) {
            Log::warning("[OrderService] Mail KO pour #{$order->order_number} : {$e->getMessage()} → mis en queue.");
            SendOrderNotifications::dispatch($order->id)->delay(now()->addMinutes(2));
        }
    }

    protected function sendNow(Order $order): void
    {
        if ($order->user) {
            $order->user->notify(
                (new OrderConfirmed($order))->onConnection('sync')
            );
        } elseif ($order->shipping_email) {
            \Illuminate\Support\Facades\Mail::to($order->shipping_email)
                ->send(new \App\Mail\GuestOrderConfirmed($order));
        }

        // Admins / managers
        User::whereIn('role', ['admin', 'manager'])
            ->each(fn ($admin) => $admin->notify(
                (new NewOrderAdmin($order))->onConnection('sync')
            ));
    }
}