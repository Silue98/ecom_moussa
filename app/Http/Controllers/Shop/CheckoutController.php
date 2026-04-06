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

        $user       = Auth::user();
        $address    = $user?->addresses()->where('is_default', true)->first();
        return view('shop.checkout', compact('cart', 'user', 'address'));
    }

    public function store(CheckoutRequest $request)
    {
        $cart = $this->cartService->getCart();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Votre panier est vide.');
        }

        try {
            $order = $this->orderService->createFromCart($request->validated());

            // Stocker le numéro de commande en session flash pour la page succès
            session()->flash('order_confirmed_id', $order->id);
            session()->flash('order_confirmed_number', $order->order_number);

            return redirect()->route('checkout.success', $order);

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors de la commande : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function success(Order $order)
    {
        // Vérification souple : utilisateur connecté et propriétaire
        // OU commande confirmée en session (flash)
        $confirmedId = session('order_confirmed_id');

        if (Auth::check()) {
            // Utilisateur connecté : doit être le propriétaire
            if ($order->user_id !== Auth::id()) {
                abort(403);
            }
        } else {
            // Invité : vérifier via session flash
            if ((int) $confirmedId !== (int) $order->id) {
                // Aussi vérifier guest_order_id (mis par OrderService)
                $guestOrderId = session('guest_order_id');
                if ((int) $guestOrderId !== (int) $order->id) {
                    abort(403);
                }
            }
        }

        $order->load('items.product.mainImage');

        return view('shop.checkout-success', compact('order'));
    }
}
