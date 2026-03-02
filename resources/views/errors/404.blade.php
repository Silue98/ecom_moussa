@extends('layouts.app')

@section('title', 'Page introuvable')

@section('content')
<div class="min-h-screen flex items-center justify-center py-16 px-4">
    <div class="text-center max-w-lg">
        <div class="text-9xl font-black text-blue-100 leading-none select-none">404</div>
        <div class="text-6xl mb-6">🔍</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-3">Page introuvable</h1>
        <p class="text-gray-500 mb-8">La page que vous recherchez n'existe pas ou a été déplacée.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}"
               class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                🏠 Retour à l'accueil
            </a>
            <a href="{{ route('products.index') }}"
               class="border border-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                🛍️ Voir les produits
            </a>
        </div>
    </div>
</div>
@endsection
