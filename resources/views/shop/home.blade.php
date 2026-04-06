@extends('layouts.app')

@section('title', 'Accueil')
@section('description', setting('hero_description', 'TrustPhone CI — Spécialiste iPhone en Côte d\'Ivoire. iPhones neufs et débloqués, paiement à la réception.'))

@section('content')

{{-- ══ Hero ══ --}}
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-10 md:py-20 flex flex-col md:flex-row items-center gap-8">
        <div class="md:w-1/2 text-center md:text-left">
            <span class="inline-flex items-center gap-2 bg-white/15 border border-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-5">
                {{ setting('hero_badge', '✅ Paiement à la réception · Garantie vendeur 3 mois') }}
            </span>
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold mb-3 md:mb-4 leading-tight">
                {{ setting('hero_title_line1', 'Votre iPhone') }}<br>
                <span class="text-blue-200">{{ setting('hero_title_line2', 'livré à Abidjan') }}</span>
            </h1>
            <p class="text-sm sm:text-base text-blue-100 mb-6 md:mb-8 leading-relaxed">
                {{ setting('hero_description', 'Spécialiste iPhone depuis des années. Tous nos appareils sont neufs, débloqués tous opérateurs et livrés avec garantie. Vous vérifiez avant de payer.') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                <a href="{{ route('products.index') }}"
                   class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition text-center text-sm sm:text-base">
                    {{ setting('hero_btn1_text', '📱 Voir tous les iPhones') }}
                </a>
                <a href="{{ route('products.index', ['is_new' => 1]) }}"
                   class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition text-center text-sm sm:text-base">
                    {{ setting('hero_btn2_text', '🆕 Nouveautés iPhone 16') }}
                </a>
            </div>
        </div>
        <div class="md:w-1/2 flex justify-center">
            <div class="grid grid-cols-2 gap-3 w-full max-w-xs">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 text-center">
                    <div class="text-3xl mb-2">📱</div>
                    <div class="text-sm font-semibold">{{ setting('hero_card1_title', 'iPhone 16 Series') }}</div>
                    <div class="text-xs text-blue-200 mt-0.5">{{ setting('hero_card1_sub', 'Dès 850 000 FCFA') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 text-center">
                    <div class="text-3xl mb-2">✅</div>
                    <div class="text-sm font-semibold">{{ setting('hero_card2_title', 'Neufs & Débloqués') }}</div>
                    <div class="text-xs text-blue-200 mt-0.5">{{ setting('hero_card2_sub', 'Tous opérateurs CI') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 text-center">
                    <div class="text-3xl mb-2">💵</div>
                    <div class="text-sm font-semibold">{{ setting('hero_card3_title', 'Paiement à la réception') }}</div>
                    <div class="text-xs text-blue-200 mt-0.5">{{ setting('hero_card3_sub', 'Vous vérifiez avant') }}</div>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-2xl p-4 text-center border border-white/30">
                    <div class="text-3xl mb-2">🛡️</div>
                    <div class="text-sm font-semibold">{{ setting('hero_card4_title', 'Garantie 3 mois') }}</div>
                    <div class="text-xs text-blue-200 mt-0.5">{{ setting('hero_card4_sub', 'Service après-vente') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ Badges de confiance ══ --}}
<section class="bg-white py-6 border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="flex flex-col items-center gap-1.5">
                <span class="text-2xl">🚚</span>
                <div class="font-semibold text-sm text-gray-800">{{ setting('badge1_title', 'Livraison rapide') }}</div>
                <div class="text-gray-400 text-xs">{{ setting('badge1_sub', '24–48h à Abidjan') }}</div>
            </div>
            <div class="flex flex-col items-center gap-1.5">
                <span class="text-2xl">💵</span>
                <div class="font-semibold text-sm text-gray-800">{{ setting('badge2_title', 'Paiement à la réception') }}</div>
                <div class="text-gray-400 text-xs">{{ setting('badge2_sub', 'Vous vérifiez avant de payer') }}</div>
            </div>
            <div class="flex flex-col items-center gap-1.5">
                <span class="text-2xl">🛡️</span>
                <div class="font-semibold text-sm text-gray-800">{{ setting('badge3_title', 'Garantie vendeur') }}</div>
                <div class="text-gray-400 text-xs">{{ setting('badge3_sub', '3 mois sur chaque iPhone') }}</div>
            </div>
            <div class="flex flex-col items-center gap-1.5">
                <span class="text-2xl">📱</span>
                <div class="font-semibold text-sm text-gray-800">{{ setting('badge4_title', '100% neufs') }}</div>
                <div class="text-gray-400 text-xs">{{ setting('badge4_sub', 'Débloqués tous opérateurs') }}</div>
            </div>
        </div>
    </div>
</section>

{{-- ══ Catégories iPhone ══ --}}
@if($categories->count())
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900">📂 Nos gammes iPhone</h2>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline text-sm font-medium">Tous les modèles →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
               class="bg-white border border-gray-200 hover:border-blue-400 rounded-2xl p-5 text-center shadow-sm hover:shadow-md transition hover:-translate-y-0.5 group">
                <div class="w-14 h-14 bg-gray-50 group-hover:bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3 transition">
                    <span class="text-2xl">📱</span>
                </div>
                <div class="font-semibold text-gray-800 text-sm group-hover:text-blue-600 transition">{{ $category->name }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $category->products_count }} modèle(s)</div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ iPhones en vedette ══ --}}
@if($featuredProducts->count())
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">⭐ Nos meilleures ventes</h2>
                <p class="text-gray-500 text-sm mt-1">Les iPhones les plus demandés par nos clients</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline text-sm font-medium hidden sm:block">Voir tout →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ Promotions ══ --}}
@if($saleProducts->count())
<section class="py-12 bg-gradient-to-b from-red-50 to-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">🔥 Promotions en cours</h2>
                <p class="text-gray-500 text-sm mt-1">Offres à durée limitée sur des iPhones sélectionnés</p>
            </div>
            <a href="{{ route('products.index', ['on_sale' => 1]) }}" class="text-red-600 hover:underline text-sm font-medium hidden sm:block">Voir tout →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($saleProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ Nouveautés iPhone 16 ══ --}}
@if($newProducts->count())
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">🆕 iPhone 16 — Derniers arrivés</h2>
                <p class="text-gray-500 text-sm mt-1">Disponibles dès maintenant en boutique et en livraison</p>
            </div>
            <a href="{{ route('products.index', ['is_new' => 1]) }}" class="text-blue-600 hover:underline text-sm font-medium hidden sm:block">Voir tout →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($newProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ Pourquoi TrustPhone CI ══ --}}
<section class="py-14 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Pourquoi choisir TrustPhone CI ?</h2>
        <p class="text-gray-500 mb-10">Nous sommes le spécialiste iPhone de confiance en Côte d'Ivoire</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-left">
            <div class="bg-gray-50 rounded-2xl p-6">
                <div class="text-3xl mb-3">🔒</div>
                <h3 class="font-bold text-gray-800 mb-2">100% Authentique</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Chaque iPhone est vérifié et authentifié avant expédition. Numéro IMEI communiqué sur demande.</p>
            </div>
            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                <div class="text-3xl mb-3">💵</div>
                <h3 class="font-bold text-blue-800 mb-2">Paiement à la réception</h3>
                <p class="text-blue-600 text-sm leading-relaxed">Vous recevez votre iPhone, vous le vérifiez en présence du livreur, puis vous payez. Aucun risque.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6">
                <div class="text-3xl mb-3">🛡️</div>
                <h3 class="font-bold text-gray-800 mb-2">Garantie & SAV</h3>
                <p class="text-gray-500 text-sm leading-relaxed">3 mois de garantie vendeur sur tous nos appareils. Service après-vente disponible en boutique.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══ Section Boutique & Crédit ══ --}}
@php
    $pickupEnabled    = setting('pickup_enabled', '0') === '1';
    $creditEnabled    = setting('credit_enabled', '0') === '1';
    $shopAddress   = setting('shop_address', '');
    $shopCity      = setting('shop_city', '');
    $shopPhone     = setting('shop_phone', '');
    $shopHours     = setting('shop_hours', '');
    $shopGmapsUrl  = setting('shop_gmaps_url', '');
    $shopLat       = setting('shop_latitude', null);
    $shopLng       = setting('shop_longitude', null);
    $hasCoords     = !empty($shopLat) && !empty($shopLng);
    $pickupMessage = setting('pickup_message', '');
@endphp

@if($pickupEnabled || $creditEnabled)
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 {{ $pickupEnabled && $creditEnabled ? 'md:grid-cols-2' : '' }} gap-6">

        {{-- Retrait en boutique --}}
        @if($pickupEnabled)
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 text-white rounded-2xl p-8">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">🏪</div>
                <div>
                    <h2 class="text-xl font-bold">Venez nous rendre visite !</h2>
                    <p class="text-gray-400 text-sm mt-1">Retrait en boutique — <span class="font-semibold text-white">Gratuit et immédiat</span></p>
                </div>
            </div>
            @if($pickupMessage)
            <p class="text-gray-300 text-sm mb-6 leading-relaxed">{{ $pickupMessage }}</p>
            @endif
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                @if($shopAddress)
                <div class="bg-white/10 rounded-xl p-3">
                    <div class="text-gray-400 text-xs mb-1">📍 Adresse</div>
                    <div class="font-medium text-sm">{{ $shopAddress }}</div>
                </div>
                @endif
                @if($shopHours)
                <div class="bg-white/10 rounded-xl p-3">
                    <div class="text-gray-400 text-xs mb-1">🕐 Horaires</div>
                    <div class="font-medium text-sm">{{ $shopHours }}</div>
                </div>
                @endif
                @if($shopPhone)
                <div class="bg-white/10 rounded-xl p-3">
                    <div class="text-gray-400 text-xs mb-1">📞 Téléphone</div>
                    <div class="font-medium text-sm">{{ $shopPhone }}</div>
                </div>
                @endif
                @if($shopCity)
                <div class="bg-blue-600/30 rounded-xl p-3">
                    <div class="text-blue-300 text-xs mb-1">🌍 Ville</div>
                    <div class="font-medium text-sm">{{ $shopCity }}</div>
                </div>
                @endif
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                @if($shopGmapsUrl && $shopGmapsUrl !== 'https://maps.google.com')
                <a href="{{ $shopGmapsUrl }}" target="_blank"
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-center text-sm transition">
                    📍 Voir sur Google Maps
                </a>
                @elseif($hasCoords)
                <a href="https://www.google.com/maps?q={{ $shopLat }},{{ $shopLng }}" target="_blank"
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-center text-sm transition">
                    📍 Voir sur Google Maps
                </a>
                @endif
                @if($shopPhone)
                <a href="tel:{{ $shopPhone }}"
                   class="flex-1 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold py-3 rounded-xl text-center text-sm transition">
                    📞 Appeler la boutique
                </a>
                @endif
                <a href="{{ route('boutique.info') }}"
                   class="flex-1 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold py-3 rounded-xl text-center text-sm transition">
                    🏪 Infos boutique
                </a>
            </div>
        </div>
        @endif

        {{-- Achat à crédit --}}
        @if($creditEnabled)
        @php
            $creditMessage    = setting('credit_message', '');
            $shopPhone        = setting('shop_phone', '');
            $shopAddress      = setting('shop_address', '');
            $shopHours        = setting('shop_hours', '');
        @endphp
        <div class="bg-gradient-to-br from-amber-700 to-amber-600 text-white rounded-2xl p-8">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">💳</div>
                <div>
                    <h2 class="text-xl font-bold">Achetez à crédit !</h2>
                    <p class="text-amber-100 text-sm mt-1">Repartez <span class="font-semibold text-white">aujourd'hui</span>, payez en plusieurs fois</p>
                </div>
            </div>
            @if($creditMessage)
            <p class="text-amber-100 text-sm mb-6 leading-relaxed">{{ $creditMessage }}</p>
            @endif
            <div class="space-y-2 mb-6">
                <div class="flex items-center gap-3 bg-white/15 rounded-xl px-4 py-3 text-sm">
                    <span class="text-lg">✅</span><span>Repartez avec votre iPhone le jour même</span>
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
                @if($shopAddress)<p class="text-amber-100">{{ $shopAddress }}</p>@endif
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
