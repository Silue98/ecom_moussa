@extends('layouts.app')

@section('title', 'Finaliser ma commande')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">💳 Finaliser ma commande</h1>

    {{-- Erreurs de validation --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">
        <p class="font-semibold mb-1">⚠️ Veuillez corriger les erreurs suivantes :</p>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf

        {{-- Champ pays caché (Côte d'Ivoire par défaut) --}}
        <input type="hidden" name="shipping_country" value="{{ old('shipping_country', 'CI') }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ── Colonne gauche : formulaire ── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Connexion optionnelle --}}
                @guest
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                    <span class="text-2xl">💡</span>
                    <div>
                        <p class="font-semibold text-blue-800">Vous pouvez commander sans créer de compte</p>
                        <p class="text-sm text-blue-700 mt-1">
                            Vous avez déjà un compte ?
                            <a href="{{ route('login') }}" class="underline font-semibold">Connectez-vous</a>
                            pour retrouver vos commandes facilement.
                        </p>
                    </div>
                </div>
                @endguest

                {{-- Adresse de livraison --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold mb-4">📦 Adresse de livraison</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                            <input type="text" name="shipping_name"
                                value="{{ old('shipping_name', $user->name ?? '') }}"
                                required
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('shipping_name') border-red-500 @enderror">
                            @error('shipping_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="shipping_email"
                                value="{{ old('shipping_email', $user->email ?? '') }}"
                                required
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('shipping_email') border-red-500 @enderror">
                            @error('shipping_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="tel" name="shipping_phone"
                                value="{{ old('shipping_phone', $user->phone ?? '') }}"
                                placeholder="+225 07 00 00 00 00"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                            <input type="text" name="shipping_address"
                                value="{{ old('shipping_address', $address->address ?? '') }}"
                                required
                                placeholder="Quartier, rue, numéro..."
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('shipping_address') border-red-500 @enderror">
                            @error('shipping_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                            <input type="text" name="shipping_city"
                                value="{{ old('shipping_city', $address->city ?? '') }}"
                                required
                                placeholder="Ex: Abidjan"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('shipping_city') border-red-500 @enderror">
                            @error('shipping_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Commune / Quartier</label>
                            <input type="text" name="shipping_state"
                                value="{{ old('shipping_state') }}"
                                placeholder="Ex: Cocody, Plateau..."
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                    </div>
                </div>

                {{-- Mode de paiement : COD uniquement --}}
                <input type="hidden" name="payment_method" value="cod">
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold mb-4">💳 Mode de paiement</h2>
                    <div class="flex items-start gap-4 p-4 border-2 border-green-400 rounded-xl bg-green-50">
                        <span class="text-3xl mt-1">💵</span>
                        <div>
                            <p class="font-semibold text-green-800 text-base">Paiement à la livraison</p>
                            <p class="text-sm text-green-700 mt-1">
                                Vous réglez en espèces directement au livreur lors de la réception de votre commande.
                                Aucune information bancaire requise.
                            </p>
                        </div>
                        <span class="ml-auto text-green-500 text-xl">✅</span>
                    </div>
                    {{-- Prochainement : Mobile Money (Wave CI, Orange Money, MTN CI) --}}
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold mb-4">📝 Notes (optionnel)</h2>
                    <textarea name="notes" rows="3"
                        placeholder="Instructions spéciales pour la livraison..."
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>

            </div>

            {{-- ── Colonne droite : récapitulatif ── --}}
            <div>
                <div class="bg-white rounded-xl shadow p-6 sticky top-24">
                    <h2 class="text-lg font-bold mb-4">Votre commande</h2>

                    <div class="space-y-3 mb-4">
                        @foreach($cart->items as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                @if($item->product && $item->product->mainImage)
                                    <img src="{{ asset('storage/' . $item->product->mainImage->image_path) }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-lg">📦</div>
                                @endif
                            </div>
                            <div class="flex-1 text-sm">
                                <div class="font-medium">{{ $item->product->name ?? 'Produit' }}</div>
                                <div class="text-gray-500">× {{ $item->quantity }}</div>
                            </div>
                            <span class="font-medium text-sm">{{ number_format($item->subtotal, 0, ',', ' ') }} XOF</span>
                        </div>
                        @endforeach
                    </div>

                    @php
                        $subtotal = $cart->total;
                        $threshold = (float) (\App\Models\Setting::get('free_shipping_threshold', 30000));
                        $shipping = $subtotal >= $threshold ? 0 : (float) (\App\Models\Setting::get('shipping_price', 2000));
                        $discount = 0;
                        if (session('coupon_code')) {
                            $coupon = \App\Models\Coupon::where('code', session('coupon_code'))->first();
                            if ($coupon) $discount = $coupon->calculateDiscount($subtotal);
                        }
                        $total = $subtotal + $shipping - $discount;
                    @endphp

                    <div class="border-t pt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total</span>
                            <span class="font-medium">{{ number_format($subtotal, 0, ',', ' ') }} XOF</span>
                        </div>
                        @if($discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Réduction ({{ session('coupon_code') }})</span>
                            <span>-{{ number_format($discount, 0, ',', ' ') }} XOF</span>
                        </div>
                        @endif
                        <input type="hidden" name="coupon_code" value="{{ session('coupon_code') }}">

                        {{-- Frais de livraison --}}
                        <div class="flex justify-between {{ $shipping == 0 ? 'text-green-600' : 'text-gray-600' }}">
                            <span>Frais de livraison</span>
                            @if($shipping == 0)
                                <span class="font-medium">🎉 Gratuite</span>
                            @else
                                <span class="font-medium">{{ number_format($shipping, 0, ',', ' ') }} XOF</span>
                            @endif
                        </div>

                        @if($shipping > 0)
                        <div class="bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-700">
                            💡 Livraison gratuite dès {{ number_format($threshold, 0, ',', ' ') }} XOF d'achat
                            (il vous manque {{ number_format($threshold - $subtotal, 0, ',', ' ') }} XOF)
                        </div>
                        @endif

                        <div class="border-t pt-2 flex justify-between font-bold text-lg mt-1">
                            <span>Total à payer</span>
                            <span class="text-blue-600">{{ number_format($total, 0, ',', ' ') }} XOF</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="mt-6 w-full bg-green-600 text-white py-4 rounded-lg font-bold text-lg hover:bg-green-700 transition">
                        ✅ Confirmer la commande
                    </button>

                    <div class="flex items-center justify-center gap-2 mt-3 text-xs text-gray-500">
                        <span>🔒</span>
                        <span>Commande 100% sécurisée</span>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
