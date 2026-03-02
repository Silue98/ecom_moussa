<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

// Shop Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/produits/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Cart
Route::get('/panier', [CartController::class, 'index'])->name('cart');
Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
Route::patch('/panier/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/panier/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::delete('/panier/coupon/supprimer', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Checkout
Route::get('/commande', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/commande', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/commande/confirmation/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// Auth
Route::get('/connexion', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/connexion', [LoginController::class, 'login']);
Route::post('/deconnexion', [LoginController::class, 'logout'])->name('logout');
Route::get('/inscription', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/inscription', [RegisterController::class, 'register']);

// Account (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/compte', [AccountController::class, 'index'])->name('account');
    Route::get('/compte/commandes', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/compte/commandes/{order}', [AccountController::class, 'orderShow'])->name('account.order');
    Route::get('/compte/profil', [AccountController::class, 'profile'])->name('account.profile');
    Route::patch('/compte/profil', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::patch('/compte/mot-de-passe', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/compte/favoris', [AccountController::class, 'wishlist'])->name('account.wishlist');
    Route::post('/compte/favoris/{product}', [AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');

    // Notifications
    Route::get('/compte/notifications', [AccountController::class, 'notifications'])->name('account.notifications');
    Route::post('/notifications/{id}/read', [AccountController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [AccountController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
});

// Reviews
Route::middleware('auth')->group(function () {
    Route::post('/produits/{product:slug}/avis', [App\Http\Controllers\Shop\ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/avis/{review}', [App\Http\Controllers\Shop\ReviewController::class, 'destroy'])->name('reviews.destroy');
});
