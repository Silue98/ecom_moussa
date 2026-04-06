<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        $products   = Product::active()->select('slug', 'updated_at')->get();
        $categories = Category::active()->select('slug', 'updated_at')->get();

        $content = view('shop.sitemap', compact('products', 'categories'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
