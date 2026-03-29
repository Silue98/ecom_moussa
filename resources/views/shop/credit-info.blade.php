@extends('layouts.app')
@section('title', 'Achat à crédit — Trust Phone CI')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-amber-600 to-amber-500 text-white rounded-2xl p-8 mb-8 text-center">
        <div class="text-5xl mb-3">💳</div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-2">Achetez votre smartphone à crédit !</h1>
        <p class="text-amber-100 text-base">Repartez aujourd'hui avec votre téléphone et payez en plusieurs fois.</p>
    </div>

    {{-- Message personnalisé depuis l'admin --}}
    @php $creditMessage = setting('credit_message', ''); @endphp
    @if($creditMessage)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6 text-amber-800 text-sm leading-relaxed">
        {{ $creditMessage }}
    </div>
    @endif

    {{-- Avantages --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-bold text-gray-800 text-lg mb-4">✅ Les avantages du crédit</h2>
        <div class="space-y-3">
            <div class="flex items-start gap-3 p-3 bg-green-50 rounded-xl">
                <span class="text-xl flex-shrink-0">📱</span>
                <div>
                    <p class="font-semibold text-green-800 text-sm">Repartez avec votre téléphone le jour même</p>
                    <p class="text-xs text-green-600 mt-0.5">Pas besoin d'attendre d'avoir la totalité du montant</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-green-50 rounded-xl">
                <span class="text-xl flex-shrink-0">💰</span>
                <div>
                    <p class="font-semibold text-green-800 text-sm">Paiement en plusieurs mensualités</p>
                    <p class="text-xs text-green-600 mt-0.5">Durée et montant définis selon votre capacité de remboursement</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-green-50 rounded-xl">
                <span class="text-xl flex-shrink-0">🛍️</span>
                <div>
                    <p class="font-semibold text-green-800 text-sm">Tous nos smartphones sont éligibles</p>
                    <p class="text-xs text-green-600 mt-0.5">iPhone, Samsung, Xiaomi, Tecno, Infinix et plus encore</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-green-50 rounded-xl">
                <span class="text-xl flex-shrink-0">🤝</span>
                <div>
                    <p class="font-semibold text-green-800 text-sm">Accord simple et rapide</p>
                    <p class="text-xs text-green-600 mt-0.5">Inscription en boutique en quelques minutes seulement</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Conditions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-bold text-gray-800 text-lg mb-4">📋 Comment ça marche ?</h2>
        <div class="space-y-4">
            <div class="flex gap-4 items-start">
                <div class="w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">1</div>
                <div>
                    <p class="font-semibold text-sm">Choisissez votre smartphone sur le site</p>
                    <p class="text-xs text-gray-500 mt-0.5">Repérez le modèle qui vous intéresse dans notre catalogue</p>
                </div>
            </div>
            <div class="flex gap-4 items-start">
                <div class="w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">2</div>
                <div>
                    <p class="font-semibold text-sm">Venez en boutique avec votre CNI</p>
                    <p class="text-xs text-gray-500 mt-0.5">Carte Nationale d'Identité ivoirienne obligatoire</p>
                </div>
            </div>
            <div class="flex gap-4 items-start">
                <div class="w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">3</div>
                <div>
                    <p class="font-semibold text-sm">Définissez votre accord avec nous</p>
                    <p class="text-xs text-gray-500 mt-0.5">Acompte, mensualités et durée selon vos possibilités</p>
                </div>
            </div>
            <div class="flex gap-4 items-start">
                <div class="w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">4</div>
                <div>
                    <p class="font-semibold text-sm">Repartez avec votre téléphone !</p>
                    <p class="text-xs text-gray-500 mt-0.5">Paiement à la livraison ou Wave / Orange Money selon accord</p>
                </div>
            </div>
        </div>

        {{-- Conditions admin --}}
        @php $conditions = setting('credit_conditions', ''); @endphp
        @if($conditions)
        <div class="mt-4 bg-gray-50 rounded-xl p-4 text-xs text-gray-600 border border-gray-200">
            <p class="font-semibold text-gray-700 mb-1">📌 Conditions :</p>
            {{ $conditions }}
        </div>
        @endif
    </div>

    {{-- Localisation boutique --}}
    @php
        $address  = setting('shop_address', '');
        $phone    = setting('shop_phone', '');
        $hours    = setting('shop_hours', '');
        $gmapsUrl = setting('shop_gmaps_url', '');
    @endphp
    @if($address || $phone)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6">
        <h2 class="font-bold text-amber-800 text-base mb-3">📍 Où s'inscrire ?</h2>
        @if($address)<p class="text-sm text-amber-700 mb-1">📍 {{ $address }}</p>@endif
        @if($hours)<p class="text-sm text-amber-700 mb-1">🕐 {{ $hours }}</p>@endif
        @if($phone)<p class="text-sm text-amber-700 mb-3">📞 {{ $phone }}</p>@endif
        <div class="flex flex-col sm:flex-row gap-3">
            @if($gmapsUrl && $gmapsUrl !== 'https://maps.google.com')
            <a href="{{ $gmapsUrl }}" target="_blank"
               class="flex-1 bg-amber-600 text-white font-semibold py-3 rounded-xl text-center text-sm hover:bg-amber-700 transition">
                📍 Voir sur Google Maps
            </a>
            @endif
            @if($phone)
            <a href="tel:{{ $phone }}"
               class="flex-1 bg-white border border-amber-300 text-amber-700 font-semibold py-3 rounded-xl text-center text-sm hover:bg-amber-50 transition">
                📞 Appeler maintenant
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Retour --}}
    <div class="text-center">
        <a href="{{ route('products.index') }}"
           class="bg-blue-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-blue-700 transition inline-block">
            📱 Voir nos smartphones
        </a>
    </div>

</div>
@endsection
