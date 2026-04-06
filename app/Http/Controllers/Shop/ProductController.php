<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('mainImage', 'category', 'brand', 'reviews');

        if ($request->category) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where('category_id', $category->id);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                  ->orWhere('short_description', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%')
                  ->orWhere('sku', 'like', '%' . $term . '%')
                  ->orWhereJsonContains('tags', $term);
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->on_sale) {
            $query->onSale();
        }

        if ($request->is_new) {
            $query->where('is_new', true);
        }

        if ($request->in_stock) {
            $query->inStock();
        }

        $sort = $request->sort ?? 'newest';
        match($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            'popular'    => $query->orderBy('sort_order', 'asc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::active()->withCount('products')->orderBy('sort_order')->get();

        return view('shop.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (! $product->is_active) abort(404);

        $product->load('images', 'category', 'brand', 'variants', 'reviews.user', 'allReviews');

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('mainImage')
            ->take(4)
            ->get();

        $userReview = Auth::check()
            ? $product->allReviews->where('user_id', Auth::id())->first()
            : null;

        $isVerifiedPurchase = Auth::check()
            ? Order::where('user_id', Auth::id())
                ->where('status', 'delivered')
                ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                ->exists()
            : false;

        return view('shop.products.show', compact('product', 'related', 'userReview', 'isVerifiedPurchase'));
    }
}
