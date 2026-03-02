<?php

namespace App\Services;

use App\Jobs\SendOrderNotifications;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
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

            $taxRate        = 0.20;
            $taxAmount      = ($subtotal - $discountAmount) * $taxRate;
            $shippingAmount = $subtotal >= 30000 ? 0 : 2000;
            $total          = $subtotal - $discountAmount + $taxAmount + $shippingAmount;

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

            // Lignes de commande + décrément stock
            foreach ($items as $item) {
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
        try {
            $this->sendNow($order);
            Log::info("[OrderService] Notifications envoyées pour commande #{$order->order_number}.");
        } catch (\Throwable $e) {
            Log::warning("[OrderService] Mail KO pour #{$order->order_number} : {$e->getMessage()} → mis en queue.");
            SendOrderNotifications::dispatch($order->id)->delay(now()->addMinutes(2));
        }
    }

    protected function sendNow(Order $order): void
    {
        // Notification client (connecté uniquement)
        if ($order->user) {
            $order->user->notify(new OrderConfirmed($order));
        } elseif ($order->shipping_email) {
            // Invité : envoi direct par email sans notification Laravel
            \Illuminate\Support\Facades\Mail::to($order->shipping_email)
                ->send(new \App\Mail\GuestOrderConfirmed($order));
        }

        // Admins / managers
        User::whereIn('role', ['admin', 'manager'])
            ->each(fn ($admin) => $admin->notify(new NewOrderAdmin($order)));
    }
}
