@extends('layouts.app')

@section('title', $product->meta_title ?? $product->name)
@section('description', $product->meta_description ?? $product->short_description)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm mb-6 text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Accueil</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.index') }}" class="hover:text-blue-600">Produits</a>
        @if($product->category)
            <span class="mx-2">/</span>
            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-blue-600">{{ $product->category->name }}</a>
        @endif
        <span class="mx-2">/</span>
        <span class="text-gray-800">{{ $product->name }}</span>
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
            <span>⚠️</span> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow p-6 lg:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            {{-- Images --}}
            <div>
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden mb-4">
                    @if($product->images->count())
                        <img id="main-image"
                            src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-8xl">📦</span>
                        </div>
                    @endif
                </div>
                @if($product->images->count() > 1)
                <div class="flex gap-2 overflow-x-auto">
                    @foreach($product->images as $image)
                    <button onclick="document.getElementById('main-image').src='{{ asset('storage/' . $image->image_path) }}'"
                        class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-500 transition">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="" class="w-full h-full object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Détails --}}
            <div>
                @if($product->category)
                    <span class="bg-blue-100 text-blue-700 text-xs font-medium px-3 py-1 rounded-full">{{ $product->category->name }}</span>
                @endif

                <h1 class="text-2xl lg:text-3xl font-bold mt-3 mb-2">{{ $product->name }}</h1>

                {{-- Note moyenne --}}
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-gray-500 text-sm">{{ $product->average_rating }}/5 ({{ $product->reviews->count() }} avis)</span>
                </div>

                @if($product->short_description)
                    <p class="text-gray-600 mb-6">{{ $product->short_description }}</p>
                @endif

                {{-- Prix --}}
                <div class="flex items-center gap-4 mb-6">
                    <span class="text-3xl font-bold text-blue-600">{{ number_format($product->price, 0, ',', ' ') }} XOF</span>
                    @if($product->compare_price)
                        <span class="text-xl text-gray-400 line-through">{{ number_format($product->compare_price, 0, ',', ' ') }} XOF</span>
                        <span class="bg-red-100 text-red-600 text-sm font-bold px-2 py-1 rounded">-{{ $product->discount_percent }}%</span>
                    @endif
                </div>

                {{-- Stock --}}
                <div class="mb-6">
                    @if($product->quantity > 0)
                        <span class="text-green-600 font-medium">✅ En stock ({{ $product->quantity }} disponibles)</span>
                    @else
                        <span class="text-red-600 font-medium">❌ Rupture de stock</span>
                    @endif
                </div>

                {{-- Variantes --}}
                @if($product->variants->count())
                <div class="mb-6">
                    @foreach($product->variants->groupBy('name') as $variantName => $variantValues)
                    <div class="mb-3">
                        <label class="font-semibold text-sm mb-2 block">{{ $variantName }}</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($variantValues as $variant)
                            <label class="cursor-pointer">
                                <input type="radio" name="variant_{{ Str::slug($variantName) }}" value="{{ $variant->id }}" class="sr-only peer">
                                <span class="border-2 border-gray-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 px-3 py-1 rounded-lg text-sm hover:border-blue-400 transition">
                                    {{ $variant->value }}
                                    @if($variant->price_modifier > 0) (+{{ number_format($variant->price_modifier, 0, ',', ' ') }} XOF) @endif
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Ajouter au panier --}}
                <form method="POST" action="{{ route('cart.add') }}" class="flex gap-3 mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="flex items-center border rounded-lg">
                        <button type="button" onclick="this.nextElementSibling.value = Math.max(1, parseInt(this.nextElementSibling.value) - 1)"
                            class="px-3 py-2 hover:bg-gray-100 transition rounded-l-lg">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->quantity }}"
                            class="w-16 text-center border-x py-2 outline-none">
                        <button type="button" onclick="this.previousElementSibling.value = Math.min({{ $product->quantity }}, parseInt(this.previousElementSibling.value) + 1)"
                            class="px-3 py-2 hover:bg-gray-100 transition rounded-r-lg">+</button>
                    </div>
                    <button type="submit" {{ $product->quantity <= 0 ? 'disabled' : '' }}
                        class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        🛒 Ajouter au panier
                    </button>
                </form>

                {{-- Wishlist --}}
                @auth
                <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}">
                    @csrf
                    <button type="submit" class="w-full border border-gray-300 py-2 rounded-lg hover:bg-red-50 hover:border-red-400 transition text-sm text-gray-600">
                        ❤️ Ajouter aux favoris
                    </button>
                </form>
                @endauth

                {{-- Infos --}}
                <div class="border-t mt-4 pt-4 space-y-2 text-sm text-gray-600">
                    @if($product->sku)<div>SKU : <span class="font-medium">{{ $product->sku }}</span></div>@endif
                    @if($product->brand)<div>Marque : <span class="font-medium">{{ $product->brand->name }}</span></div>@endif
                </div>
            </div>
        </div>

        {{-- ── Onglets Description / Avis ─────────────────────────────────── --}}
        <div class="mt-12">
            <div class="border-b mb-6">
                <div class="flex gap-6">
                    <button onclick="showTab('description')" id="tab-description"
                        class="pb-3 border-b-2 border-blue-600 text-blue-600 font-medium text-sm">
                        Description
                    </button>
                    <button onclick="showTab('reviews')" id="tab-reviews"
                        class="pb-3 border-b-2 border-transparent text-gray-500 font-medium text-sm">
                        Avis ({{ $product->reviews->count() }})
                    </button>
                </div>
            </div>

            {{-- Description --}}
            <div id="content-description">
                <div class="prose max-w-none text-gray-700">
                    {!! $product->description !!}
                </div>
            </div>

            {{-- Avis --}}
            <div id="content-reviews" class="hidden">

                @php
                    $userReview = auth()->check()
                        ? $product->reviews->where('user_id', auth()->id())->first()
                        : null;

                    $isVerifiedPurchase = auth()->check()
                        ? \App\Models\Order::where('user_id', auth()->id())
                            ->where('status', 'delivered')
                            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                            ->exists()
                        : false;
                @endphp

                {{-- ── Formulaire laisser un avis ──────────────────────── --}}
                @auth
                    @if(! $userReview)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-8">
                        <h3 class="text-lg font-bold mb-1">✍️ Laisser un avis</h3>

                        @if($isVerifiedPurchase)
                            <p class="text-sm text-green-600 mb-4">✅ Achat vérifié — votre avis sera marqué comme certifié.</p>
                        @else
                            <p class="text-sm text-gray-500 mb-4">Vous pouvez laisser un avis même sans avoir acheté ce produit.</p>
                        @endif

                        @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 mb-4 text-sm">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form method="POST" action="{{ route('reviews.store', $product) }}">
                            @csrf

                            {{-- Étoiles interactives --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Note *</label>
                                <div class="flex gap-1" id="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="rating" value="{{ $i }}" class="sr-only"
                                            {{ old('rating') == $i ? 'checked' : '' }}>
                                        <svg class="w-8 h-8 text-gray-300 hover:text-yellow-400 transition star-icon"
                                            data-value="{{ $i }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </label>
                                    @endfor
                                </div>
                            </div>

                            {{-- Titre --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Titre (optionnel)</label>
                                <input type="text" name="title" value="{{ old('title') }}"
                                    placeholder="Résumez votre expérience..."
                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>

                            {{-- Commentaire --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire *</label>
                                <textarea name="body" rows="4" required
                                    placeholder="Décrivez votre expérience avec ce produit (min. 10 caractères)..."
                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ old('body') }}</textarea>
                            </div>

                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition text-sm">
                                📨 Soumettre mon avis
                            </button>
                            <p class="text-xs text-gray-400 mt-2">Votre avis sera publié après validation par notre équipe.</p>
                        </form>
                    </div>
                    @else
                    {{-- Avis déjà laissé --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-8 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-blue-800 font-semibold text-sm">✅ Vous avez déjà laissé un avis pour ce produit.</p>
                            @if(! $userReview->is_approved)
                                <p class="text-blue-600 text-sm mt-1">⏳ Il est en attente de modération.</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('reviews.destroy', $userReview) }}">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer votre avis ?')"
                                class="text-red-500 hover:text-red-700 text-sm underline">
                                Supprimer
                            </button>
                        </form>
                    </div>
                    @endif
                @else
                {{-- Non connecté --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-8 text-center">
                    <p class="text-gray-600 mb-3">Connectez-vous pour laisser un avis</p>
                    <a href="{{ route('login') }}"
                        class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition text-sm">
                        Se connecter
                    </a>
                </div>
                @endauth

                {{-- ── Liste des avis approuvés ────────────────────────── --}}
                @php $approvedReviews = $product->reviews->where('is_approved', true); @endphp

                @if($approvedReviews->count())
                <div class="space-y-4">
                    <h3 class="font-bold text-gray-800">{{ $approvedReviews->count() }} avis client(s)</h3>
                    @foreach($approvedReviews as $review)
                    <div class="border rounded-xl p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-sm">{{ $review->user->name ?? 'Anonyme' }}</span>
                                        @if($review->is_verified_purchase)
                                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">✅ Achat vérifié</span>
                                        @endif
                                    </div>
                                    <div class="flex mt-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        @if($review->title)
                            <div class="font-semibold text-sm mt-3">{{ $review->title }}</div>
                        @endif
                        <p class="text-gray-600 text-sm mt-1">{{ $review->body }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-10 text-gray-500">
                    <div class="text-5xl mb-3">💬</div>
                    <p class="font-medium">Aucun avis pour ce produit</p>
                    <p class="text-sm mt-1">Soyez le premier à donner votre avis !</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Produits similaires --}}
    @if($related->count())
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Produits similaires</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
            @foreach($related as $relatedProduct)
                @include('components.product-card', ['product' => $relatedProduct])
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Onglets
function showTab(tab) {
    ['description', 'reviews'].forEach(t => {
        document.getElementById('content-' + t).classList.add('hidden');
        document.getElementById('tab-' + t).classList.remove('border-blue-600', 'text-blue-600');
        document.getElementById('tab-' + t).classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('border-blue-600', 'text-blue-600');
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
}

// Étoiles interactives
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('#star-rating .star-icon');
    const inputs = document.querySelectorAll('#star-rating input[type=radio]');

    function highlightStars(value) {
        stars.forEach(star => {
            star.classList.toggle('text-yellow-400', parseInt(star.dataset.value) <= value);
            star.classList.toggle('text-gray-300', parseInt(star.dataset.value) > value);
        });
    }

    // Initialiser si old('rating') est défini
    const checked = document.querySelector('#star-rating input[type=radio]:checked');
    if (checked) highlightStars(parseInt(checked.value));

    stars.forEach(star => {
        star.addEventListener('mouseover', () => highlightStars(parseInt(star.dataset.value)));
        star.addEventListener('mouseout', () => {
            const selected = document.querySelector('#star-rating input[type=radio]:checked');
            highlightStars(selected ? parseInt(selected.value) : 0);
        });
        star.addEventListener('click', () => {
            const val = parseInt(star.dataset.value);
            inputs[val - 1].checked = true;
            highlightStars(val);
        });
    });

    // Ouvrir l'onglet avis si ancre #avis dans l'URL
    if (window.location.hash === '#avis') showTab('reviews');
});
</script>
@endpush
@endsection
