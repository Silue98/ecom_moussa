@extends('layouts.app')

@section('title', 'Mes Favoris')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">❤️ Mes Favoris</h1>

    @if($wishlist->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($wishlist as $item)
                @if($item->product)
                    @include('components.product-card', ['product' => $item->product])
                @endif
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-16 text-center">
            <div class="text-6xl mb-4">❤️</div>
            <h2 class="text-xl font-semibold mb-2">Votre liste de favoris est vide</h2>
            <p class="text-gray-500 mb-6">Ajoutez des produits à vos favoris pour les retrouver facilement</p>
            <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition">
                Découvrir nos produits
            </a>
        </div>
    @endif
</div>
@endsection
