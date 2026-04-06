@extends('layouts.app')
@section('title', 'Comparer les produits')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold">⚖️ Comparer les produits</h1>
        @if($products->count() > 0)
        <a href="{{ route('compare.clear') }}" class="text-sm text-red-500 hover:text-red-700 hover:underline">
            Vider le comparateur
        </a>
        @endif
    </div>

    @if($products->count() < 2)
    <div class="bg-white rounded-2xl shadow p-16 text-center">
        <div class="text-6xl mb-4">⚖️</div>
        <h2 class="text-xl font-semibold mb-2">Ajoutez au moins 2 produits à comparer</h2>
        <p class="text-gray-500 mb-6">Cliquez sur "Comparer" sur les fiches produits pour les ajouter ici.</p>
        <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition">
            Voir les produits
        </a>
    </div>
    @else

    {{-- Tableau comparatif --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                {{-- En-têtes produits --}}
                <thead>
                    <tr class="border-b">
                        <th class="p-4 text-left text-sm text-gray-500 font-medium w-40">Produit</th>
                        @foreach($products as $product)
                        <th class="p-4 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-24 h-24 bg-gray-100 rounded-xl overflow-hidden">
                                    @if($product->mainImage)
                                        <img src="{{ asset('storage/' . $product->mainImage->image_path) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-3xl">📦</div>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('products.show', $product) }}"
                                       class="font-semibold text-sm hover:text-blue-600 transition line-clamp-2">
                                        {{ $product->name }}
                                    </a>
                                </div>
                                <a href="{{ route('compare.remove', $product) }}"
                                   class="text-xs text-red-400 hover:text-red-600">✕ Retirer</a>
                            </div>
                        </th>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)
                        <th class="p-4 text-center">
                            <div class="w-24 h-24 border-2 border-dashed border-gray-200 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <span class="text-3xl text-gray-300">+</span>
                            </div>
                            <a href="{{ route('products.index') }}" class="text-sm text-blue-600 hover:underline">
                                Ajouter un produit
                            </a>
                        </th>
                        @endfor
                    </tr>
                </thead>

                <tbody>
                    {{-- Prix --}}
                    <tr class="border-b bg-blue-50">
                        <td class="p-4 text-sm font-semibold text-gray-700">Prix</td>
                        @foreach($products as $product)
                        <td class="p-4 text-center">
                            <div class="text-xl font-bold text-blue-600">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>
                            @if($product->compare_price)
                                <div class="text-sm text-gray-400 line-through">{{ number_format($product->compare_price, 0, ',', ' ') }} FCFA</div>
                            @endif
                        </td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)<td></td>@endfor
                    </tr>

                    {{-- Stock --}}
                    <tr class="border-b">
                        <td class="p-4 text-sm font-medium text-gray-600">Disponibilité</td>
                        @foreach($products as $product)
                        <td class="p-4 text-center">
                            @if($product->quantity > 0)
                                <span class="inline-flex items-center gap-1 text-green-600 text-sm font-medium">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span> En stock
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-red-500 text-sm">
                                    <span class="w-2 h-2 rounded-full bg-red-400"></span> Rupture
                                </span>
                            @endif
                        </td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)<td></td>@endfor
                    </tr>

                    {{-- Note --}}
                    <tr class="border-b">
                        <td class="p-4 text-sm font-medium text-gray-600">Note clients</td>
                        @foreach($products as $product)
                        <td class="p-4 text-center">
                            <div class="flex justify-center gap-0.5 mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-xs text-gray-400">{{ $product->average_rating }}/5 ({{ $product->reviews->count() }} avis)</span>
                        </td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)<td></td>@endfor
                    </tr>

                    {{-- Catégorie & Marque --}}
                    <tr class="border-b bg-gray-50">
                        <td class="p-4 text-sm font-medium text-gray-600">Catégorie</td>
                        @foreach($products as $product)
                        <td class="p-4 text-center text-sm text-gray-700">{{ $product->category->name ?? '—' }}</td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)<td></td>@endfor
                    </tr>
                    <tr class="border-b">
                        <td class="p-4 text-sm font-medium text-gray-600">Marque</td>
                        @foreach($products as $product)
                        <td class="p-4 text-center text-sm text-gray-700">{{ $product->brand->name ?? '—' }}</td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)<td></td>@endfor
                    </tr>

                    {{-- Caractéristiques techniques --}}
                    @php
                        $allSpecs = collect($products)->flatMap(fn($p) => array_keys($p->specifications ?? []))->unique()->values();
                    @endphp
                    @if($allSpecs->count() > 0)
                    <tr class="border-b">
                        <td colspan="{{ $products->count() + 1 }}" class="px-4 py-2 bg-gray-100">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Caractéristiques techniques</span>
                        </td>
                    </tr>
                    @foreach($allSpecs as $spec)
                    <tr class="border-b {{ $loop->odd ? 'bg-gray-50' : '' }}">
                        <td class="p-4 text-sm font-medium text-gray-600">{{ $spec }}</td>
                        @foreach($products as $product)
                        @php $val = ($product->specifications ?? [])[$spec] ?? null; @endphp
                        <td class="p-4 text-center text-sm {{ $val ? 'text-gray-800 font-medium' : 'text-gray-300' }}">
                            {{ $val ?? '—' }}
                        </td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)<td></td>@endfor
                    </tr>
                    @endforeach
                    @endif

                    {{-- Bouton acheter --}}
                    <tr>
                        <td class="p-4"></td>
                        @foreach($products as $product)
                        <td class="p-4 text-center">
                            @if($product->quantity > 0)
                            <form method="POST" action="{{ route('cart.add') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit"
                                    class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition text-sm">
                                    🛒 Ajouter au panier
                                </button>
                            </form>
                            @else
                            <button disabled class="w-full bg-gray-200 text-gray-400 py-2.5 rounded-lg font-semibold text-sm cursor-not-allowed">
                                Indisponible
                            </button>
                            @endif
                        </td>
                        @endforeach
                        @for($i = $products->count(); $i < 3; $i++)<td></td>@endfor
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
