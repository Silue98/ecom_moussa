<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService  $cartService,
        protected OrderService $orderService
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('info', 'Votre panier est vide.');
        }

        // Utilisateur connecté ou invité
        $user    = Auth::user();
        $address = $user?->addresses()->where('is_default', true)->first();
        $couponCode = session('coupon_code');

        return view('shop.checkout', compact('cart', 'user', 'address', 'couponCode'));
    }

    public function store(CheckoutRequest $request)
    {
        $cart = $this->cartService->getCart();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Votre panier est vide.');
        }

        try {
            $order = $this->orderService->createFromCart(array_merge(
                $request->validated(),
                ['coupon_code' => session('coupon_code')]
            ));

            session()->forget('coupon_code');

            return redirect()
                ->route('checkout.success', $order)
                ->with('success', '🎉 Commande #' . $order->order_number . ' passée avec succès !');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors de la commande : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function success(Order $order)
    {
        // Accessible par l'utilisateur connecté OU si la commande est en session (invité)
        $guestOrderId = session('guest_order_id');

        if (Auth::check()) {
            if ($order->user_id !== Auth::id()) {
                abort(403);
            }
        } elseif ($guestOrderId !== $order->id) {
            abort(403);
        }

        $order->load('items');

        return view('shop.checkout-success', compact('order'));
    }
}
