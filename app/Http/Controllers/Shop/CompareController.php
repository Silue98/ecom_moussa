<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    // Max 3 produits comparés
    const MAX = 3;

    public function index()
    {
        $ids     = session('compare', []);
        $products = Product::active()
            ->with('mainImage', 'category', 'brand')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn($p) => array_search($p->id, $ids));

        return view('shop.compare', compact('products'));
    }

    public function add(Request $request, Product $product)
    {
        $ids = session('compare', []);

        if (!in_array($product->id, $ids)) {
            if (count($ids) >= self::MAX) {
                array_shift($ids); // retirer le plus ancien
            }
            $ids[] = $product->id;
            session(['compare' => $ids]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'count'   => count($ids),
                'message' => "« {$product->name} » ajouté au comparateur.",
            ]);
        }

        return back()->with('success', "« {$product->name} » ajouté au comparateur.");
    }

    public function remove(Product $product)
    {
        $ids = array_filter(session('compare', []), fn($id) => $id !== $product->id);
        session(['compare' => array_values($ids)]);

        return back()->with('success', 'Produit retiré du comparateur.');
    }

    public function clear()
    {
        session()->forget('compare');
        return redirect()->route('products.index')->with('success', 'Comparateur vidé.');
    }
}
