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
                        <p class="text-blue-600 font-bold mt-1">{{ number_format($item->price, 0, ',', ' ') }} FCFA</p>
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
                            <div class="text-right font-bold mt-1">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-xl shadow p-6 sticky top-24">
                    <h2 class="text-lg font-bold mb-4">Récapitulatif</h2>

                    <div class="space-y-3 text-sm">
                        @php
                            $threshold = (float) setting('free_shipping_threshold', 30000);
                            $shipPrice = (float) setting('shipping_price', 2000);
                            $shipping  = $cart->total >= $threshold ? 0 : $shipPrice;
                            $cartTotal = $cart->total + $shipping;
                        @endphp
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total</span>
                            <span class="font-medium">{{ number_format($cart->total, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between {{ $shipping == 0 ? 'text-green-600' : 'text-gray-600' }}">
                            <span>Frais de livraison</span>
                            <span class="font-medium">{{ $shipping == 0 ? '🎉 Gratuite' : number_format($shipping, 0, ',', ' ') . ' FCFA' }}</span>
                        </div>
                        @if($cart->total < $threshold)
                        <div class="text-xs text-blue-700 bg-blue-50 border border-blue-100 p-2.5 rounded-lg">
                            💡 Livraison gratuite dès {{ number_format($threshold, 0, ',', ' ') }} FCFA
                            (il vous manque {{ number_format($threshold - $cart->total, 0, ',', ' ') }} FCFA)
                        </div>
                        @endif
                        <div class="border-t pt-3 flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-blue-600">{{ number_format($cartTotal, 0, ',', ' ') }} FCFA</span>
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
