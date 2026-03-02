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
     */
    function setting(string $key, mixed $default = null): mixed
    {
        static $settings = null;

        if ($settings === null) {
            try {
                $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                $settings = [];
            }
        }

        return $settings[$key] ?? $default;
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
