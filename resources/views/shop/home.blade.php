@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<!-- Hero Banner -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-20 flex flex-col md:flex-row items-center">
        <div class="md:w-1/2 mb-8 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">🛍️ Bienvenue dans notre boutique</h1>
            <p class="text-lg text-blue-100 mb-8">Découvrez des milliers de produits aux meilleurs prix. Livraison gratuite dès 500 XOF</p>
            <div class="flex gap-4">
                <a href="{{ route('products.index') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                    Voir les produits
                </a>
                <a href="{{ route('products.index', ['on_sale' => 1]) }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                    Promotions
                </a>
            </div>
        </div>
        <div class="md:w-1/2 flex justify-center">
            <div class="bg-white/10 backdrop-blur rounded-2xl p-8 text-center">
                <div class="text-6xl mb-4">🎁</div>
                <div class="text-2xl font-bold">Livraison gratuite</div>
                <div class="text-blue-200">dès 500 XOF d'achat</div>
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

<!-- CTA Banner -->
<section class="bg-gradient-to-r from-orange-500 to-red-500 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">🎉 Offre spéciale du moment</h2>
        <p class="text-lg mb-8 text-orange-100">Utilisez le code <span class="bg-white text-orange-600 px-3 py-1 rounded font-bold">BIENVENUE10</span> pour 10% de réduction</p>
        <a href="{{ route('products.index') }}" class="bg-white text-orange-600 px-8 py-3 rounded-lg font-semibold hover:bg-orange-50 transition">
            Profiter de l'offre
        </a>
    </div>
</section>
@endsection
