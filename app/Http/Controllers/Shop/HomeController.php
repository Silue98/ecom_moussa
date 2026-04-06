<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Chargement optimisé : une seule requête pour tous les produits actifs en stock
        // puis filtrage en mémoire pour éviter 3 requêtes SQL distinctes
        $allProducts = Product::active()->inStock()
            ->with('mainImage', 'category', 'reviews')
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = $allProducts->filter(fn ($p) => $p->is_featured)->take(8)->values();
        $newProducts      = $allProducts->filter(fn ($p) => $p->is_new)->take(8)->values();
        $saleProducts     = $allProducts->filter(fn ($p) => $p->on_sale)->take(8)->values();

        $categories = Category::active()
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        return view('shop.home', compact(
            'featuredProducts', 'newProducts', 'saleProducts', 'categories'
        ));
    }
}
