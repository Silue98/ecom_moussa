@extends('layouts.app')

@section('title', 'Tous les iPhones')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="text-sm mb-6 text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Accueil</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800">Produits</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl shadow p-6 sticky top-24">
                <h3 class="font-bold text-lg mb-4">🔍 Filtres</h3>

                <form method="GET" action="{{ route('products.index') }}" id="filter-form">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Categories -->
                    <div class="mb-6">
                        <h4 class="font-semibold mb-3">Catégories</h4>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="category" value=""
                                    {{ !request('category') ? 'checked' : '' }}
                                    class="mr-2" onchange="this.form.submit()">
                                <span class="text-sm">Toutes les catégories</span>
                            </label>
                            @foreach($categories as $category)
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="category" value="{{ $category->slug }}"
                                    {{ request('category') === $category->slug ? 'checked' : '' }}
                                    class="mr-2" onchange="this.form.submit()">
                                <span class="text-sm">{{ $category->name }}</span>
                                <span class="ml-auto text-xs text-gray-400">{{ $category->products_count }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-6">
                        <h4 class="font-semibold mb-3">Prix</h4>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" placeholder="Min"
                                value="{{ request('min_price') }}"
                                class="w-full border rounded px-2 py-1 text-sm">
                            <input type="number" name="max_price" placeholder="Max"
                                value="{{ request('max_price') }}"
                                class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                    </div>

                    <!-- Promotions -->
                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="on_sale" value="1"
                                {{ request('on_sale') ? 'checked' : '' }}
                                class="mr-2" onchange="this.form.submit()">
                            <span class="text-sm font-medium">🔥 En promotion seulement</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        Appliquer les filtres
                    </button>
                    <a href="{{ route('products.index') }}" class="block w-full text-center text-gray-500 py-2 text-sm mt-2 hover:text-blue-600">
                        Réinitialiser
                    </a>
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Sort & count bar -->
            <div class="flex items-center justify-between mb-6 bg-white rounded-xl shadow px-4 py-3">
                <span class="text-gray-600 text-sm">
                    <strong>{{ $products->total() }}</strong> produit(s) trouvé(s)
                </span>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Trier par:</label>
                    <select name="sort" onchange="window.location.href='{{ route('products.index') }}?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)),sort:this.value})"
                        class="border rounded px-2 py-1 text-sm">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Plus récents</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nom A-Z</option>
                    </select>
                </div>
            </div>

            @if($products->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow p-12 text-center">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-500 mb-6">Essayez d'autres critères de recherche</p>
                    <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Voir tous les produits
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
