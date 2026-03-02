<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): Cart
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            // Merge session cart if exists
            $sessionId = Session::getId();
            $sessionCart = Cart::where('session_id', $sessionId)->where('user_id', null)->first();
            if ($sessionCart) {
                foreach ($sessionCart->items as $item) {
                    $this->addToCart($item->product_id, $item->quantity, $item->variant_id, $item->options);
                }
                $sessionCart->delete();
            }
        } else {
            $cart = Cart::firstOrCreate(['session_id' => Session::getId(), 'user_id' => null]);
        }
        return $cart->load('items.product.mainImage', 'items.variant');
    }

    public function addToCart(int $productId, int $quantity = 1, ?int $variantId = null, ?array $options = null): void
    {
        $cart = $this->getCart();
        $product = Product::findOrFail($productId);
        $price = $product->price;

        if ($variantId) {
            $variant = $product->variants()->findOrFail($variantId);
            $price += $variant->price_modifier;
        }

        $existingItem = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
                'options' => $options,
            ]);
        }
    }

    public function updateItem(int $itemId, int $quantity): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($itemId);

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }
    }

    public function removeItem(int $itemId): void
    {
        $cart = $this->getCart();
        $cart->items()->findOrFail($itemId)->delete();
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
    }

    public function getCount(): int
    {
        try {
            $cart = $this->getCart();
            return $cart->items->sum('quantity');
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getSubtotal(): float
    {
        try {
            $cart = $this->getCart();
            return $cart->items->load('product', 'variant')->sum(function ($item) {
                $price = $item->variant?->final_price ?? $item->product?->price ?? 0;
                return $price * $item->quantity;
            });
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    public function getTotal(?string $couponCode = null): array
    {
        $subtotal = $this->getSubtotal();
        $discount = 0;

        if ($couponCode) {
            $coupon = \App\Models\Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid() && $subtotal >= ($coupon->min_order_amount ?? 0)) {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }

        $tax      = ($subtotal - $discount) * 0.20;
        $shipping = $subtotal >= 30000 ? 0 : 2000;
        $total    = $subtotal - $discount + $tax + $shipping;

        return compact('subtotal', 'discount', 'tax', 'shipping', 'total');
    }

    public function mergGuestCart(int $userId): void
    {
        $sessionId   = session()->getId();
        $guestCart   = \App\Models\Cart::where('session_id', $sessionId)->first();
        $userCart    = \App\Models\Cart::firstOrCreate(['user_id' => $userId]);

        if ($guestCart && $guestCart->items->isNotEmpty()) {
            foreach ($guestCart->items as $item) {
                $existing = $userCart->items()
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->first();

                if ($existing) {
                    $existing->increment('quantity', $item->quantity);
                } else {
                    $userCart->items()->create($item->only(['product_id', 'variant_id', 'quantity', 'options']));
                }
            }
            $guestCart->delete();
        }
    }
}
