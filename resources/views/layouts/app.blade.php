<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Commerce') — {{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'Boutique en ligne de smartphones et accessoires en Côte d\'Ivoire')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

{{-- ═══════════════════════════════ NAVBAR ═══════════════════════════════ --}}
<nav class="bg-white shadow-md sticky top-0 z-50" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center space-x-2 flex-shrink-0">
                <span class="text-2xl">🛍️</span>
                <span class="font-bold text-xl text-blue-600 hidden sm:block">{{ config('app.name', 'E-Commerce') }}</span>
            </a>

            {{-- Recherche desktop --}}
            <div class="hidden md:flex flex-1 max-w-lg mx-8">
                <form action="{{ route('products.index') }}" method="GET" class="flex w-full">
                    <input type="text" name="search" placeholder="Rechercher un produit..."
                        value="{{ request('search') }}"
                        class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-r-lg hover:bg-blue-700 transition text-sm font-medium">
                        🔍
                    </button>
                </form>
            </div>

            {{-- Actions droite --}}
            <div class="flex items-center space-x-2 sm:space-x-4">

                {{-- Cloche notifications --}}
                <x-notification-bell />

                {{-- Compte utilisateur --}}
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="flex items-center space-x-1 text-gray-700 hover:text-blue-600 transition text-sm font-medium">
                            <span class="hidden sm:block max-w-24 truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition
                            class="absolute right-0 mt-2 w-52 bg-white border border-gray-100 rounded-xl shadow-xl py-1 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('account') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 transition">
                                👤 Mon compte
                            </a>
                            <a href="{{ route('account.orders') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 transition">
                                📦 Mes commandes
                            </a>
                            <a href="{{ route('account.wishlist') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 transition">
                                ❤️ Mes favoris
                            </a>
                            <a href="{{ route('account.notifications') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 transition">
                                🔔 Notifications
                                @php $unread = Auth::user()->unreadNotifications()->count() @endphp
                                @if($unread > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $unread }}</span>
                                @endif
                            </a>
                            <a href="{{ route('account.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 transition">
                                ⚙️ Mon profil
                            </a>
                            @if(Auth::user()->isAdmin())
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ url('/admin') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 transition font-medium">
                                    🎛️ Administration
                                </a>
                            @endif
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    🚪 Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 transition text-sm font-medium hidden sm:block">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        Inscription
                    </a>
                @endauth

                {{-- Panier --}}
                <a href="{{ route('cart') }}" class="relative flex items-center text-gray-700 hover:text-blue-600 transition p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span id="cart-count"
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                        {{ app(\App\Services\CartService::class)->getCount() }}
                    </span>
                </a>

                {{-- Bouton menu mobile --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-1 text-gray-600 hover:text-blue-600">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Recherche mobile --}}
        <div class="md:hidden pb-3">
            <form action="{{ route('products.index') }}" method="GET" class="flex">
                <input type="text" name="search" placeholder="Rechercher..."
                    value="{{ request('search') }}"
                    class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700 transition text-sm">🔍</button>
            </form>
        </div>

        {{-- Menu mobile --}}
        <div x-show="mobileOpen" x-transition class="md:hidden pb-4 border-t border-gray-100 pt-3">

            {{-- Catégories sur mobile --}}
            <div class="mb-3 pb-3 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-3 mb-2">Catégories</p>
                <div class="space-y-1">
                    <a href="{{ route('products.index') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-50 text-blue-600 font-medium">📱 Tous les produits</a>
                    @foreach(\App\Models\Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->take(6)->get() as $cat)
                        <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                           class="block px-3 py-2 text-sm rounded-lg hover:bg-gray-100 text-gray-700">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @auth
                <div class="space-y-1">
                    <a href="{{ route('account') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-gray-100">👤 Mon compte</a>
                    <a href="{{ route('account.orders') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-gray-100">📦 Mes commandes</a>
                    <a href="{{ route('account.wishlist') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-gray-100">❤️ Mes favoris</a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ url('/admin') }}" class="block px-3 py-2 text-sm rounded-lg text-blue-600 hover:bg-blue-50">🎛️ Administration</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 text-sm rounded-lg text-red-600 hover:bg-red-50">🚪 Déconnexion</button>
                    </form>
                </div>
            @else
                <div class="space-y-2">
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-gray-100">Connexion</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-sm rounded-lg bg-blue-600 text-white text-center">Inscription</a>
                </div>
            @endauth
        </div>
    </div>

    {{-- Barre catégories --}}
    <div class="bg-blue-600 text-white hidden md:block">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center space-x-6 py-2 overflow-x-auto scrollbar-none">
                <a href="{{ route('products.index') }}"
                    class="text-sm whitespace-nowrap hover:text-blue-200 transition font-medium {{ !request('category') ? 'text-white underline' : '' }}">
                    Tous les produits
                </a>
                @foreach(\App\Models\Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->take(8)->get() as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                        class="text-sm whitespace-nowrap hover:text-blue-200 transition {{ request('category') === $cat->slug ? 'text-white underline' : 'text-blue-100' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</nav>

{{-- ══ Barre info boutique & crédit (sticky sous navbar) ══ --}}
@php
    $pickupEnabled = setting('pickup_enabled', '0') === '1';
    $creditEnabled = setting('credit_enabled', '0') === '1';
    $shopPhone     = setting('shop_phone', '');
@endphp
@if($pickupEnabled || $creditEnabled)
<div class="bg-blue-700 text-white text-xs py-1.5 px-4 sticky top-16 z-40">
    <div class="max-w-7xl mx-auto flex items-center justify-center gap-4 sm:gap-8 flex-wrap">
        @if($pickupEnabled)
        <a href="{{ route('boutique.info') }}" class="flex items-center gap-1.5 hover:text-blue-200 transition">
            <span>🏪</span>
            <span class="font-medium">Retrait en boutique — Gratuit</span>
        </a>
        @endif
        @if($pickupEnabled && $creditEnabled)
            <span class="text-blue-400 hidden sm:inline">|</span>
        @endif
        @if($creditEnabled)
        <a href="{{ route('credit.info') }}" class="flex items-center gap-1.5 hover:text-blue-200 transition">
            <span>💳</span>
            <span class="font-medium">Achat à crédit disponible</span>
            <span class="bg-amber-400 text-amber-900 text-xs font-bold px-2 py-0.5 rounded-full ml-1">En savoir +</span>
        </a>
        @endif
        @if($shopPhone)
        <a href="tel:{{ $shopPhone }}" class="flex items-center gap-1.5 hover:text-blue-200 transition hidden sm:flex">
            <span>📞</span>
            <span>{{ $shopPhone }}</span>
        </a>
        @endif
    </div>
</div>
@endif

{{-- ═══════════════════════════════ FLASH MESSAGES ═══════════════════════ --}}
<x-flash-message />

{{-- ═══════════════════════════════ CONTENT ═══════════════════════════════ --}}
<main>
    @yield('content')
</main>

{{-- ═══════════════════════════════ FOOTER ════════════════════════════════ --}}
<footer class="bg-gray-900 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="font-bold text-lg mb-4">🛍️ {{ config('app.name') }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Votre boutique de smartphones et accessoires en Côte d'Ivoire. Livraison rapide à Abidjan et partout en CI.
                </p>
                <div class="flex space-x-3 mt-4">
                    <a href="#" class="text-gray-400 hover:text-white transition">📘</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">📸</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">🐦</a>
                </div>
            </div>
            <div>
                <h3 class="font-bold mb-4">Navigation</h3>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition">Accueil</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white transition">Tous les produits</a></li>
                    <li><a href="{{ route('products.index', ['on_sale' => 1]) }}" class="hover:text-white transition">Promotions</a></li>
                    <li><a href="{{ route('products.index', ['is_new' => 1]) }}" class="hover:text-white transition">Nouveautés</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-4">Mon compte</h3>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ route('account') }}" class="hover:text-white transition">Tableau de bord</a></li>
                    <li><a href="{{ route('account.orders') }}" class="hover:text-white transition">Mes commandes</a></li>
                    <li><a href="{{ route('account.wishlist') }}" class="hover:text-white transition">Mes favoris</a></li>
                    <li><a href="{{ route('account.profile') }}" class="hover:text-white transition">Mon profil</a></li>
                    <li><a href="{{ route('cart') }}" class="hover:text-white transition">Mon panier</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-4">Contact & Boutique</h3>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li>📧 <a href="mailto:{{ setting('site_email', 'commandes@phonestore.ci') }}" class="hover:text-white">{{ setting('site_email', 'commandes@phonestore.ci') }}</a></li>
                    @if(setting('shop_phone'))
                    <li><a href="tel:{{ setting('shop_phone') }}" class="hover:text-white">📞 {{ setting('shop_phone') }}</a></li>
                    @endif
                    @if(setting('shop_address'))
                    <li>
                        @if(setting('shop_gmaps_url') && setting('shop_gmaps_url') !== 'https://maps.google.com')
                        <a href="{{ setting('shop_gmaps_url') }}" target="_blank" class="hover:text-white">
                            📍 {{ setting('shop_address') }}<br>
                            <span class="text-xs text-blue-400">→ Voir sur Google Maps</span>
                        </a>
                        @else
                        <span>📍 {{ setting('shop_address') }}</span>
                        @endif
                    </li>
                    @endif
                    @if(setting('shop_hours'))
                    <li>🕐 {{ setting('shop_hours') }}</li>
                    @endif
                    <li class="pt-2">
                        <span class="bg-green-800 text-green-200 text-xs px-2 py-1 rounded-full">✅ Paiement sécurisé</span>
                    </li>
                    <li>
                        <span class="bg-blue-800 text-blue-200 text-xs px-2 py-1 rounded-full">🚚 Livraison 24-48h</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-gray-400 text-sm">© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés. Fait avec ❤️ et Laravel 12</p>
                <div class="flex items-center gap-4 text-gray-400 text-sm">
                    <span>💵 Paiement à la livraison</span>
                </div>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
