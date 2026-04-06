<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
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

        try {
            $this->cartService->addToCart(
                $request->product_id,
                $request->quantity ?? 1,
                $request->variant_id,
            );
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté au panier',
                'count'   => $this->cartService->getCount(),
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
}
