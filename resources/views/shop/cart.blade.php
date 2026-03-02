@extends('layouts.app')

@section('title', 'Mon Panier')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">🛒 Mon Panier</h1>

    @if($cart->items->isEmpty())
        <div class="bg-white rounded-2xl shadow p-16 text-center">
            <div class="text-7xl mb-4">🛒</div>
            <h2 class="text-xl font-semibold mb-2">Votre panier est vide</h2>
            <p class="text-gray-500 mb-6">Découvrez nos produits et ajoutez-en à votre panier</p>
            <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition">
                Continuer vos achats
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                <div class="bg-white rounded-xl shadow p-4 flex gap-4">
                    <!-- Image -->
                    <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                        @if($item->product && $item->product->mainImage)
                            <img src="{{ asset('storage/' . $item->product->mainImage->image_path) }}"
                                alt="{{ $item->product->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-2xl">📦</div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800">{{ $item->product->name ?? 'Produit supprimé' }}</h3>
                        @if($item->variant)
                            <p class="text-sm text-gray-500">{{ $item->variant->name }}: {{ $item->variant->value }}</p>
                        @endif
                        <p class="text-blue-600 font-bold mt-1">{{ number_format($item->price, 2) }} FCFA</p>
                    </div>

                    <!-- Quantity & Remove -->
                    <div class="flex flex-col items-end justify-between">
                        <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 transition text-sm">✕</button>
                        </form>
                        <div>
                            <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center border rounded-lg">
                                @csrf @method('PATCH')
                                <button type="button" onclick="let i=this.nextElementSibling;i.value=Math.max(1,i.value-1);i.form.submit()"
                                    class="px-2 py-1 hover:bg-gray-100 rounded-l-lg">−</button>
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                    class="w-12 text-center text-sm outline-none">
                                <button type="button" onclick="let i=this.previousElementSibling;i.value=parseInt(i.value)+1;i.form.submit()"
                                    class="px-2 py-1 hover:bg-gray-100 rounded-r-lg">+</button>
                            </form>
                            <div class="text-right font-bold mt-1">{{ number_format($item->subtotal, 2) }} FCFA</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-xl shadow p-6 sticky top-24">
                    <h2 class="text-lg font-bold mb-4">Récapitulatif</h2>

                    <!-- Coupon -->
                    @if(session('coupon_code'))
                        <div class="bg-green-50 border border-green-300 rounded-lg p-3 mb-4 flex items-center justify-between">
                            <span class="text-green-700 text-sm font-medium">✅ Code: {{ session('coupon_code') }}</span>
                            <form method="POST" action="{{ route('cart.coupon.remove') }}">
                                @csrf @method('DELETE')
                                <button class="text-red-500 text-sm hover:text-red-700">Supprimer</button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('cart.coupon') }}" class="flex gap-2 mb-4">
                            @csrf
                            <input type="text" name="coupon_code" placeholder="Code promo"
                                class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded-lg text-sm hover:bg-gray-700 transition">
                                Appliquer
                            </button>
                        </form>
                    @endif

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total</span>
                            <span>{{ number_format($cart->total, 2) }} FCFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Livraison</span>
                            <span class="{{ $cart->total >= 30000 ? 'text-green-600' : '' }}">
                                {{ $cart->total >= 30000 ? 'Gratuite' : '2 000 FCFA' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">TVA (20%)</span>
                            <span>{{ number_format($cart->total * 0.20, 2) }} FCFA</span>
                        </div>
                        @if($cart->total < 500)
                            <div class="text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                Ajoutez {{ number_format(30000 - $cart->total, 2) }} FCFA pour la livraison gratuite
                            </div>
                        @endif
                        <div class="border-t pt-3 flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-blue-600">{{ number_format($cart->total + ($cart->total * 0.20) + ($cart->total >= 30000 ? 0 : 2000), 2) }} FCFA</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout') }}" class="mt-6 block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Passer la commande →
                    </a>
                    <a href="{{ route('products.index') }}" class="mt-3 block w-full text-center text-gray-500 text-sm hover:text-blue-600">
                        Continuer mes achats
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
