<?php

namespace App\Services;

use App\Jobs\SendOrderNotifications;
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
            $taxAmount      = 0;
            $threshold      = (float) setting('free_shipping_threshold', 30000);
            $shipPrice      = (float) setting('shipping_price', 2000);

            if (($data['delivery_type'] ?? 'delivery') === 'pickup') {
                $shippingAmount = 0;
            } else {
                $shippingAmount = $subtotal >= $threshold ? 0 : $shipPrice;
            }
            $total = $subtotal - $discountAmount + $shippingAmount;

            $userId = Auth::id();

            $order = Order::create([
                'user_id'          => $userId,
                'status'           => 'pending',
                'payment_status'   => 'pending',
                'payment_method'   => $data['payment_method'] ?? 'cod',
                'delivery_type'    => $data['delivery_type'] ?? 'delivery',
                'subtotal'         => $subtotal,
                'tax_amount'       => $taxAmount,
                'shipping_amount'  => $shippingAmount,
                'discount_amount'  => $discountAmount,
                'total'            => $total,
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
                // ── Vérification stock : variante ou produit ──────────────
                if ($item->variant_id && $item->variant && $item->variant->quantity > 0) {
                    // La variante a son propre stock
                    if ($item->variant->quantity < $item->quantity) {
                        throw new \Exception(
                            "Stock insuffisant pour la variante « {$item->variant->value} » " .
                            "de « {$item->product->name} » " .
                            "(disponible : {$item->variant->quantity}, demandé : {$item->quantity})."
                        );
                    }
                } else {
                    // Stock global du produit
                    if ($item->product->quantity < $item->quantity) {
                        throw new \Exception(
                            "Stock insuffisant pour « {$item->product->name} » " .
                            "(disponible : {$item->product->quantity}, demandé : {$item->quantity})."
                        );
                    }
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

                // ── Décrément du bon stock ────────────────────────────────
                if ($item->variant_id && $item->variant && $item->variant->quantity > 0) {
                    // Décrémenter le stock de la variante
                    $item->variant->decrement('quantity', $item->quantity);

                    // Alerte stock bas sur la variante
                    $freshVariant = $item->variant->fresh();
                    if ($freshVariant && $freshVariant->quantity <= 3 && $freshVariant->quantity > 0) {
                        User::whereIn('role', ['admin', 'manager'])
                            ->each(fn ($admin) => $admin->notify(new LowStockAlert($item->product->fresh())));
                    }
                } else {
                    // Décrémenter le stock global du produit
                    $item->product->decrement('quantity', $item->quantity);

                    // Alerte stock bas
                    $freshProduct = $item->product->fresh();
                    if ($freshProduct && $freshProduct->isLowStock()) {
                        User::whereIn('role', ['admin', 'manager'])
                            ->each(fn ($admin) => $admin->notify(new LowStockAlert($freshProduct)));
                    }
                }
            }

            $this->cartService->clear();

            return $order;
        });

        // Mémoriser la commande en session pour les invités
        if (! Auth::check()) {
            Session::put('guest_order_id', $order->id);
        }

        // ── Étape 2 : Envoi des notifications (hors transaction) ──────────
        $order->load('items');
        $this->dispatchNotifications($order);

        return $order;
    }

    protected function dispatchNotifications(Order $order): void
    {
        try {
            \App\Jobs\SendWhatsAppOrderConfirmation::dispatch($order->id)
                ->delay(now()->addSeconds(3));
            Log::info("[OrderService] Job WhatsApp dispatché pour commande #{$order->order_number}.");
        } catch (\Throwable $e) {
            Log::warning("[OrderService] Impossible de dispatcher WhatsApp pour #{$order->order_number} : {$e->getMessage()}");
        }

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

        User::whereIn('role', ['admin', 'manager'])
            ->each(fn ($admin) => $admin->notify(
                (new NewOrderAdmin($order))->onConnection('sync')
            ));
    }
}
