@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<!-- Hero Banner -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-10 md:py-20 flex flex-col md:flex-row items-center gap-8">
        <div class="md:w-1/2 text-center md:text-left">
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold mb-3 md:mb-4">📱 Smartphones &amp; Accessoires</h1>
            <p class="text-sm sm:text-base text-blue-100 mb-6 md:mb-8">Les meilleurs smartphones aux meilleurs prix. Livraison rapide à Abidjan et partout en Côte d'Ivoire.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                <a href="{{ route('products.index') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition text-center text-sm sm:text-base">
                    Voir les produits
                </a>
                <a href="{{ route('products.index', ['on_sale' => 1]) }}" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition text-center text-sm sm:text-base">
                    Promotions
                </a>
            </div>
        </div>
        <div class="md:w-1/2 flex justify-center">
            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 md:p-8 text-center">
                <div class="text-5xl md:text-6xl mb-3">🎁</div>
                <div class="text-lg md:text-2xl font-bold">Livraison gratuite</div>
                <div class="text-blue-200 text-sm md:text-base">dès {{ number_format((float)setting('free_shipping_threshold', 30000), 0, ',', ' ') }} FCFA d'achat</div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="bg-white py-8 border-b">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="flex flex-col items-center">
                <span class="text-3xl mb-2">🚚</span>
                <div class="font-semibold text-sm">Livraison rapide</div>
                <div class="text-gray-500 text-xs">2-3 jours ouvrables</div>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-3xl mb-2">🔒</span>
                <div class="font-semibold text-sm">Paiement sécurisé</div>
                <div class="text-gray-500 text-xs">100% sécurisé</div>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-3xl mb-2">↩️</span>
                <div class="font-semibold text-sm">Retours gratuits</div>
                <div class="text-gray-500 text-xs">30 jours pour retourner</div>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-3xl mb-2">💬</span>
                <div class="font-semibold text-sm">Support 24/7</div>
                <div class="text-gray-500 text-xs">Toujours disponible</div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
@if($categories->count())
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-8">🗂️ Nos catégories</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                class="bg-white rounded-xl p-6 text-center shadow hover:shadow-lg transition hover:-translate-y-1">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                        class="w-16 h-16 object-cover rounded-full mx-auto mb-3">
                @else
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl">📦</span>
                    </div>
                @endif
                <div class="font-semibold text-gray-800">{{ $category->name }}</div>
                <div class="text-xs text-gray-500">{{ $category->products_count }} produits</div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products -->
@if($featuredProducts->count())
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold">⭐ Produits en vedette</h2>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline text-sm">Voir tout →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Sale Products -->
@if($saleProducts->count())
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold">🔥 Promotions</h2>
            <a href="{{ route('products.index', ['on_sale' => 1]) }}" class="text-blue-600 hover:underline text-sm">Voir tout →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($saleProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- New Products -->
@if($newProducts->count())
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold">🆕 Nouveautés</h2>
            <a href="{{ route('products.index', ['is_new' => 1]) }}" class="text-blue-600 hover:underline text-sm">Voir tout →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($newProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ Section Boutique & Crédit ══ --}}
@php
    $pickupEnabled    = setting('pickup_enabled', '0') === '1';
    $creditEnabled    = setting('credit_enabled', '0') === '1';
    $shopName         = setting('shop_name', '');
    $shopAddress      = setting('shop_address', '');
    $shopCity         = setting('shop_city', '');
    $shopPhone        = setting('shop_phone', '');
    $shopHours        = setting('shop_hours', '');
    $shopGmapsUrl     = setting('shop_gmaps_url', '');
    $shopLat          = setting('shop_latitude', null);
    $shopLng          = setting('shop_longitude', null);
    $hasCoords        = !empty($shopLat) && !empty($shopLng);
    $pickupMessage    = setting('pickup_message', '');
    $creditMessage    = setting('credit_message', '');
    $creditConditions = setting('credit_conditions', '');
@endphp

@if($pickupEnabled || $creditEnabled)
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 {{ $pickupEnabled && $creditEnabled ? 'md:grid-cols-2' : '' }} gap-6">

            {{-- Retrait en boutique --}}
            @if($pickupEnabled)
            <div id="section-boutique" class="bg-gradient-to-br from-emerald-700 to-emerald-600 text-white rounded-2xl p-6 sm:p-8">
                <div class="flex items-start gap-4 mb-5">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">🏪</div>
                    <div>
                        <h2 class="text-xl font-bold">Venez nous rendre visite !</h2>
                        <p class="text-emerald-100 text-sm mt-1">Retrait en boutique — <span class="font-semibold text-white">Gratuit</span></p>
                    </div>
                </div>

                @if($pickupMessage)
                <p class="text-emerald-100 text-sm mb-5 leading-relaxed">{{ $pickupMessage }}</p>
                @endif

                <div class="grid grid-cols-2 gap-3 mb-5">
                    @if($shopAddress)
                    <div class="bg-white/15 rounded-xl p-3">
                        <div class="text-emerald-200 text-xs mb-1">📍 Adresse</div>
                        <div class="font-medium text-sm">{{ $shopAddress }}</div>
                    </div>
                    @endif
                    @if($shopHours)
                    <div class="bg-white/15 rounded-xl p-3">
                        <div class="text-emerald-200 text-xs mb-1">🕐 Horaires</div>
                        <div class="font-medium text-sm">{{ $shopHours }}</div>
                    </div>
                    @endif
                    @if($shopPhone)
                    <div class="bg-white/15 rounded-xl p-3">
                        <div class="text-emerald-200 text-xs mb-1">📞 Téléphone</div>
                        <div class="font-medium text-sm">{{ $shopPhone }}</div>
                    </div>
                    @endif
                    @if($shopCity)
                    <div class="bg-white/15 rounded-xl p-3">
                        <div class="text-emerald-200 text-xs mb-1">🌍 Ville</div>
                        <div class="font-medium text-sm">{{ $shopCity }}</div>
                    </div>
                    @endif
                </div>

                {{-- ── Carte Leaflet (s'affiche uniquement si les coordonnées sont renseignées dans l'admin) ── --}}
                @if($hasCoords)
                <div class="rounded-xl overflow-hidden mb-4 border-2 border-white/20" style="height: 220px;">
                    <div id="boutique-map" style="height: 100%; width: 100%;"></div>
                </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3">
                    @if($shopGmapsUrl && $shopGmapsUrl !== 'https://maps.google.com')
                    <a href="{{ $shopGmapsUrl }}" target="_blank"
                       class="flex-1 bg-white text-emerald-700 font-semibold py-3 rounded-xl text-center text-sm hover:bg-emerald-50 transition">
                        📍 Voir sur Google Maps
                    </a>
                    @elseif($hasCoords)
                    <a href="https://www.google.com/maps?q={{ $shopLat }},{{ $shopLng }}" target="_blank"
                       class="flex-1 bg-white text-emerald-700 font-semibold py-3 rounded-xl text-center text-sm hover:bg-emerald-50 transition">
                        📍 Voir sur Google Maps
                    </a>
                    @endif
                    <a href="{{ route('boutique.info') }}"
                       class="flex-1 bg-white/20 text-white font-semibold py-3 rounded-xl text-center text-sm hover:bg-white/30 transition border border-white/30">
                        🏪 Plus d'infos boutique
                    </a>
                </div>
            </div>
            @endif

            {{-- Achat à crédit --}}
            @if($creditEnabled)
            <div id="section-credit" class="bg-gradient-to-br from-amber-700 to-amber-600 text-white rounded-2xl p-6 sm:p-8">
                <div class="flex items-start gap-4 mb-5">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">💳</div>
                    <div>
                        <h2 class="text-xl font-bold">Achetez à crédit !</h2>
                        <p class="text-amber-100 text-sm mt-1">Repartez <span class="font-semibold text-white">aujourd'hui</span>, payez en plusieurs fois</p>
                    </div>
                </div>

                @if($creditMessage)
                <p class="text-amber-100 text-sm mb-5 leading-relaxed">{{ $creditMessage }}</p>
                @endif

                <div class="space-y-2 mb-5">
                    <div class="flex items-center gap-3 bg-white/15 rounded-xl px-4 py-3 text-sm">
                        <span class="text-lg">✅</span><span>Repartez avec votre téléphone le jour même</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/15 rounded-xl px-4 py-3 text-sm">
                        <span class="text-lg">✅</span><span>Paiement en plusieurs mensualités</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/15 rounded-xl px-4 py-3 text-sm">
                        <span class="text-lg">✅</span><span>Inscription rapide en boutique (CNI requise)</span>
                    </div>
                </div>

                @if($shopAddress || $shopPhone)
                <div class="bg-white/15 rounded-xl p-4 mb-5 text-sm">
                    <p class="font-semibold mb-1">📍 Où s'inscrire ?</p>
                    @if($shopAddress)<p class="text-amber-100">{{ $shopAddress }}@if($shopCity), {{ $shopCity }}@endif</p>@endif
                    @if($shopHours)<p class="text-amber-100">{{ $shopHours }}</p>@endif
                </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('credit.info') }}"
                       class="flex-1 bg-white text-amber-700 font-semibold py-3 rounded-xl text-center text-sm hover:bg-amber-50 transition">
                        💳 En savoir plus sur le crédit
                    </a>
                    @if($shopPhone)
                    <a href="tel:{{ $shopPhone }}"
                       class="flex-1 bg-white/20 text-white font-semibold py-3 rounded-xl text-center text-sm hover:bg-white/30 transition border border-white/30">
                        📞 Nous contacter
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</section>
@endif

@endsection