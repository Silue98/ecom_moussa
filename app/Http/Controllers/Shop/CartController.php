<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        return view('shop.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:100',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $this->cartService->addToCart(
            $request->product_id,
            $request->quantity ?? 1,
            $request->variant_id,
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté au panier',
                'count' => $this->cartService->getCount(),
            ]);
        }

        return back()->with('success', 'Produit ajouté au panier !');
    }

    public function update(Request $request, int $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);
        $this->cartService->updateItem($itemId, $request->quantity);

        if ($request->ajax()) {
            $cart = $this->cartService->getCart();
            return response()->json([
                'success' => true,
                'cart_total' => $cart->total,
                'cart_count' => $this->cartService->getCount(),
            ]);
        }

        return back()->with('success', 'Panier mis à jour');
    }

    public function remove(int $itemId)
    {
        $this->cartService->removeItem($itemId);
        return back()->with('success', 'Article supprimé du panier');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);
        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

        if (!$coupon || !$coupon->isValid()) {
            return back()->with('error', 'Code promo invalide ou expiré');
        }

        session(['coupon_code' => $coupon->code]);
        return back()->with('success', 'Code promo appliqué !');
    }

    public function removeCoupon()
    {
        session()->forget('coupon_code');
        return back()->with('success', 'Code promo supprimé');
    }
}
