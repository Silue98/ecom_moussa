<?php

if (! function_exists('formatPrice')) {
    /**
     * Formate un prix en FCFA
     */
    function formatPrice(float $price, string $currency = 'FCFA'): string
    {
        return number_format($price, 2, ',', ' ') . ' ' . $currency;
    }
}

if (! function_exists('setting')) {
    /**
     * Récupère un paramètre du site depuis la table settings
     * Utilise le cache Laravel (5 minutes) pour éviter une requête SQL à chaque appel
     */
    function setting(string $key, mixed $default = null): mixed
    {
        $settings = \Illuminate\Support\Facades\Cache::remember('app_settings', 300, function () {
            try {
                return \App\Models\Setting::pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('setting_forget')) {
    /**
     * Invalide le cache des settings (à appeler après une mise à jour)
     */
    function setting_forget(): void
    {
        \Illuminate\Support\Facades\Cache::forget('app_settings');
    }
}

if (! function_exists('cartCount')) {
    /**
     * Retourne le nombre d'articles dans le panier courant
     */
    function cartCount(): int
    {
        try {
            $cartService = app(\App\Services\CartService::class);
            $cart = $cartService->getCart();
            return $cart ? $cart->items->sum('quantity') : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}

if (! function_exists('currencySymbol')) {
    /**
     * Retourne le symbole de la monnaie configurée
     */
    function currencySymbol(): string
    {
        return setting('currency', 'FCFA');
    }
}

if (! function_exists('siteName')) {
    /**
     * Retourne le nom du site
     */
    function siteName(): string
    {
        return setting('site_name', config('app.name', 'E-Commerce'));
    }
}

if (! function_exists('isWishlisted')) {
    /**
     * Vérifie si un produit est dans les favoris de l'utilisateur connecté
     */
    function isWishlisted(int $productId): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return \App\Models\Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->exists();
    }
}
