<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur n'a pas déjà laissé un avis
        $existingReview = Review::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour ce produit.');
        }

        // Vérifier si achat vérifié (commande livrée contenant ce produit)
        $isVerifiedPurchase = Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();

        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'title'   => ['nullable', 'string', 'max:100'],
            'body'    => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'rating.required'  => 'Veuillez choisir une note.',
            'body.required'    => 'Veuillez écrire un commentaire.',
            'body.min'         => 'Votre commentaire doit faire au moins 10 caractères.',
        ]);

        Review::create([
            'product_id'          => $product->id,
            'user_id'             => $user->id,
            'rating'              => $request->rating,
            'title'               => $request->title,
            'body'                => $request->body,
            'is_approved'         => false,         // en attente de modération
            'is_verified_purchase'=> $isVerifiedPurchase,
        ]);

        return back()->with('success', '✅ Merci pour votre avis ! Il sera publié après validation.');
    }

    public function destroy(Review $review)
    {
        // Seul l'auteur peut supprimer son avis
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Votre avis a été supprimé.');
    }
}
