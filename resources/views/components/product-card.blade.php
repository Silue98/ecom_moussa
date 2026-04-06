@php
    $waPhone = preg_replace('/[^0-9]/', '', setting('shop_phone', ''));
    if (strlen($waPhone) === 10) { $waPhone = '225' . substr($waPhone, 0); }

    $creditEnabled  = setting('credit_enabled', '0') === '1';
    $montantMin     = (float) setting('credit_montant_min', 100000);
    $nbEcheances    = (int)   setting('credit_nb_echeances', 3);
    $taux           = (float) setting('credit_taux_interet', 0);
    $pourcentages   = array_map('intval', explode(',', setting('credit_pourcentages', '30,40,30')));
    $showCredit     = $creditEnabled && $product->price >= $montantMin;

    if ($showCredit) {
        $totalCredit   = $product->price * (1 + $taux / 100);
        $premierPct    = $pourcentages[0] ?? 0;
        $premierVers   = (int) ceil($totalCredit * $premierPct / 100);
    }

    $waMsg = urlencode(
        "Bonjour TrustPhone CI ! 👋\n\n" .
        "Je suis intéressé par :\n" .
        "📱 *" . $product->name . "*\n" .
        "💰 Prix : " . number_format($product->price, 0, ',', ' ') . " FCFA\n\n" .
        "Est-il disponible ?"
    );
@endphp

<div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col group">

    {{-- Image --}}
    <a href="{{ route('products.show', $product) }}" class="block relative overflow-hidden bg-gray-50 aspect-square">
        @if($product->mainImage)
            @php $imgSrc = str_starts_with($product->mainImage->image_path, 'http') ? $product->mainImage->image_path : asset('storage/' . $product->mainImage->image_path); @endphp
            <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center"><span class="text-6xl">📱</span></div>
        @endif
        @if($product->on_sale && $product->discount_percent > 0)
            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-lg">-{{ $product->discount_percent }}%</span>
        @endif
        @if($product->is_new)
            <span class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-lg">NOUVEAU</span>
        @endif
        @if($product->quantity > 0 && $product->quantity <= $product->low_stock_threshold)
            <span class="absolute bottom-2 left-0 right-0 mx-2 bg-orange-500 text-white text-xs font-semibold px-2 py-1 rounded-lg text-center">⚡ Plus que {{ $product->quantity }} en stock</span>
        @endif
        @if($product->quantity <= 0)
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                <span class="bg-white text-gray-700 text-xs font-bold px-3 py-1.5 rounded-full">Rupture de stock</span>
            </div>
        @endif
    </a>

    {{-- Contenu --}}
    <div class="p-3 flex flex-col flex-1 gap-1.5">
        @if($product->category)
            <span class="text-xs text-blue-600 font-semibold">{{ $product->category->name }}</span>
        @endif

        <a href="{{ route('products.show', $product) }}" class="block flex-1">
            <h3 class="font-semibold text-gray-800 hover:text-blue-600 transition line-clamp-2 text-sm leading-snug">{{ $product->name }}</h3>
        </a>

        {{-- Étoiles --}}
        <div class="flex items-center gap-1">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3 h-3 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endfor
            <span class="text-xs text-gray-400">({{ $product->reviews->count() }})</span>
        </div>

        {{-- Prix --}}
        <div>
            <div class="flex items-baseline gap-1.5 flex-wrap">
                <span class="font-bold text-gray-900 text-sm sm:text-base">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                @if($product->compare_price)
                    <span class="text-xs text-gray-400 line-through">{{ number_format($product->compare_price, 0, ',', ' ') }}</span>
                @endif
            </div>

            {{-- Badge crédit --}}
            @if($showCredit)
            <div class="flex items-center gap-1.5 bg-amber-50 border border-amber-300 rounded-lg px-2 py-1.5 mt-1.5">
                <span style="font-size:13px;">💳</span>
                <span class="text-xs font-bold text-amber-800">
                    Acheter à crédit en {{ $nbEcheances }} échéances dès {{ number_format($premierVers, 0, ',', ' ') }} FCFA
                </span>
            </div>
            @endif
        </div>

        {{-- Boutons --}}
        <div class="flex flex-col gap-1.5 mt-1">
            @if($product->quantity > 0)
            <form method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-xs font-bold transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Ajouter au panier
                </button>
            </form>
            @else
            <button disabled class="w-full bg-gray-100 text-gray-400 py-2 rounded-lg text-xs font-bold cursor-not-allowed">Rupture de stock</button>
            @endif

            <div class="flex gap-1.5">
                @if($waPhone)
                <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" rel="noopener"
                   class="flex-1 flex items-center justify-center gap-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg text-xs font-bold transition">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 fill-white flex-shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.017.5 3.926 1.381 5.601L0 24l6.545-1.364A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.013-1.376l-.36-.214-3.727.977.996-3.631-.235-.374A9.789 9.789 0 012.182 12C2.182 6.578 6.578 2.182 12 2.182S21.818 6.578 21.818 12 17.422 21.818 12 21.818z"/></svg>
                    WhatsApp
                </a>
                @endif
                <form method="POST" action="{{ route('compare.add', $product) }}">
                    @csrf
                    <button type="submit" title="Comparer" class="flex items-center justify-center gap-1 border border-gray-300 hover:border-blue-400 hover:bg-blue-50 text-gray-500 hover:text-blue-600 px-2.5 py-2 rounded-lg text-xs font-bold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Comparer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>