@extends('layouts.app')
@section('title', 'Notre Boutique — Phone Store CI')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    @php
        $shopName = setting('shop_name', 'Phone Store CI');
        $address  = setting('shop_address', '');
        $city     = setting('shop_city', '');
        $phone    = setting('shop_phone', '');
        $hours    = setting('shop_hours', '');
        $gmapsUrl = setting('shop_gmaps_url', '');
        $pickup   = setting('pickup_message', '');
    @endphp

    {{-- Header --}}
    <div class="bg-gradient-to-br from-emerald-700 to-emerald-500 text-white rounded-2xl p-8 mb-8 text-center">
        <div class="text-5xl mb-3">🏪</div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-2">Visitez notre boutique</h1>
        <p class="text-emerald-100 text-base">Essayez nos smartphones en main propre avant d'acheter.</p>
    </div>

    {{-- Infos pratiques --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-bold text-gray-800 text-lg mb-5">📍 Informations pratiques</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($address)
            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                <span class="text-2xl flex-shrink-0">📍</span>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">Adresse</p>
                    <p class="text-gray-600 text-sm mt-0.5">{{ $address }}</p>
                    @if($city)<p class="text-gray-500 text-xs">{{ $city }}</p>@endif
                </div>
            </div>
            @endif
            @if($hours)
            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                <span class="text-2xl flex-shrink-0">🕐</span>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">Horaires d'ouverture</p>
                    <p class="text-gray-600 text-sm mt-0.5">{{ $hours }}</p>
                </div>
            </div>
            @endif
            @if($phone)
            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                <span class="text-2xl flex-shrink-0">📞</span>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">Téléphone</p>
                    <a href="tel:{{ $phone }}" class="text-blue-600 text-sm font-medium hover:underline mt-0.5 block">{{ $phone }}</a>
                </div>
            </div>
            @endif
            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                <span class="text-2xl flex-shrink-0">🚗</span>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">Retrait commande</p>
                    <p class="text-gray-600 text-sm mt-0.5">Gratuit — Disponible sous 24h</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Google Maps --}}
    @if($gmapsUrl && $gmapsUrl !== 'https://maps.google.com')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-bold text-gray-800 text-base mb-4">🗺️ Nous trouver</h2>
        <a href="{{ $gmapsUrl }}" target="_blank"
           class="flex items-center justify-center gap-3 bg-emerald-600 text-white font-semibold py-4 rounded-xl hover:bg-emerald-700 transition text-base">
            <span class="text-2xl">📍</span>
            Ouvrir dans Google Maps
        </a>
        <p class="text-xs text-gray-500 text-center mt-2">Cliquez pour obtenir l'itinéraire depuis votre position</p>
    </div>
    @endif

    {{-- Pourquoi venir --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-bold text-gray-800 text-lg mb-4">💡 Pourquoi venir en boutique ?</h2>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <span class="text-xl">📱</span>
                <div>
                    <p class="font-semibold text-sm text-gray-800">Essayez avant d'acheter</p>
                    <p class="text-xs text-gray-500">Prenez en main tous nos modèles avant de faire votre choix</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-xl">🎯</span>
                <div>
                    <p class="font-semibold text-sm text-gray-800">Conseils personnalisés</p>
                    <p class="text-xs text-gray-500">Notre équipe vous guide vers le smartphone qui correspond à vos besoins</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-xl">📦</span>
                <div>
                    <p class="font-semibold text-sm text-gray-800">Récupérez votre commande gratuitement</p>
                    <p class="text-xs text-gray-500">Commandez en ligne et venez retirer en boutique sans frais de livraison</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-xl">💳</span>
                <div>
                    <p class="font-semibold text-sm text-gray-800">Achat à crédit disponible</p>
                    <p class="text-xs text-gray-500">Repartez avec votre téléphone et payez en plusieurs fois</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3">
        @if($phone)
        <a href="tel:{{ $phone }}"
           class="flex-1 bg-emerald-600 text-white font-semibold py-4 rounded-xl text-center hover:bg-emerald-700 transition">
            📞 Appeler la boutique
        </a>
        @endif
        <a href="{{ route('products.index') }}"
           class="flex-1 bg-blue-600 text-white font-semibold py-4 rounded-xl text-center hover:bg-blue-700 transition">
            📱 Voir nos produits
        </a>
        <a href="{{ route('home') }}#section-credit"
           class="flex-1 bg-amber-500 text-white font-semibold py-4 rounded-xl text-center hover:bg-amber-600 transition">
            💳 Infos crédit
        </a>
    </div>

</div>
@endsection
