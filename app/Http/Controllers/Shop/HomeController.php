<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()->featured()->inStock()
            ->with('mainImage', 'category')
            ->take(8)
            ->get();

        $newProducts = Product::active()->where('is_new', true)->inStock()
            ->with('mainImage', 'category')
            ->take(8)
            ->get();

        $saleProducts = Product::active()->onSale()->inStock()
            ->with('mainImage', 'category')
            ->take(8)
            ->get();

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
