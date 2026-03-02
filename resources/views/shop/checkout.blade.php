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

                {{-- Mode de paiement --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold mb-4">💳 Mode de paiement</h2>
                    <div class="space-y-3">

                        <label class="flex items-center p-4 border-2 border-blue-500 rounded-lg cursor-pointer bg-blue-50">
                            <input type="radio" name="payment_method" value="cod" checked class="mr-3">
                            <span class="text-2xl mr-3">💵</span>
                            <div>
                                <div class="font-semibold">Paiement à la livraison</div>
                                <div class="text-sm text-gray-500">Payez en espèces à la réception</div>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300">
                            <input type="radio" name="payment_method" value="card" class="mr-3">
                            <span class="text-2xl mr-3">💳</span>
                            <div>
                                <div class="font-semibold">Carte bancaire</div>
                                <div class="text-sm text-gray-500">Visa, Mastercard sécurisé</div>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300">
                            <input type="radio" name="payment_method" value="bank_transfer" class="mr-3">
                            <span class="text-2xl mr-3">🏦</span>
                            <div>
                                <div class="font-semibold">Virement bancaire / Mobile Money</div>
                                <div class="text-sm text-gray-500">Orange Money, Wave, MTN MoMo</div>
                            </div>
                        </label>

                    </div>
                    @error('payment_method')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
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
                        $shipping = $subtotal >= 30000 ? 0 : 2000;
                        $tax      = $subtotal * 0.20;
                        $discount = 0;
                        if (session('coupon_code')) {
                            $coupon = \App\Models\Coupon::where('code', session('coupon_code'))->first();
                            if ($coupon) $discount = $coupon->calculateDiscount($subtotal);
                        }
                        $total = $subtotal + $shipping + $tax - $discount;
                    @endphp

                    <div class="border-t pt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total</span>
                            <span>{{ number_format($subtotal, 0, ',', ' ') }} XOF</span>
                        </div>
                        @if($discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Réduction ({{ session('coupon_code') }})</span>
                            <span>-{{ number_format($discount, 0, ',', ' ') }} XOF</span>
                        </div>
                        @endif
                        <input type="hidden" name="coupon_code" value="{{ session('coupon_code') }}">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Livraison</span>
                            <span>{{ $shipping > 0 ? number_format($shipping, 0, ',', ' ') . ' XOF' : '🎉 Gratuite' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">TVA (20%)</span>
                            <span>{{ number_format($tax, 0, ',', ' ') }} XOF</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between font-bold text-lg">
                            <span>Total</span>
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
