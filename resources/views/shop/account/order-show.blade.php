@extends('layouts.app')

@section('title', 'Commande ' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
    <a href="{{ route('account.orders') }}" class="text-blue-600 hover:underline text-sm">← Retour aux commandes</a>
    <a href="{{ route('account.order.tracking', $order) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">📍 Suivre ma commande</a>
</div>

    <h1 class="text-2xl font-bold mb-2">Commande {{ $order->order_number }}</h1>
    <p class="text-gray-500 mb-8">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-bold mb-3">📍 Adresse de livraison</h3>
            <div class="text-gray-700 text-sm space-y-1">
                <div class="font-medium">{{ $order->shipping_name }}</div>
                <div>{{ $order->shipping_address }}</div>
                <div>{{ $order->shipping_city }}, {{ $order->shipping_zip }}</div>
                <div>{{ $order->shipping_country }}</div>
                @if($order->shipping_phone)<div>📞 {{ $order->shipping_phone }}</div>@endif
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-bold mb-3">💳 Informations de paiement</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Méthode</span>
                    <span class="font-medium">{{ match($order->payment_method) {
                        'cod' => '💵 Paiement à la livraison',
                        'card' => '💳 Carte bancaire',
                        'bank_transfer' => '🏦 Virement',
                        default => $order->payment_method
                    } }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Statut paiement</span>
                    <span class="{{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }} font-medium">
                        {{ $order->payment_status === 'paid' ? '✅ Payé' : '⏳ En attente' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Statut commande</span>
                    <span class="font-medium">{{ $order->status }}</span>
                </div>
                @if($order->tracking_number)
                <div class="flex justify-between">
                    <span class="text-gray-500">N° suivi</span>
                    <span class="font-medium">{{ $order->tracking_number }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-bold mb-4">📦 Articles commandés</h3>
        <div class="space-y-4">
            @foreach($order->items as $item)
            <div class="flex items-center gap-4 border-b pb-4 last:border-0 last:pb-0">
                <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    @if($item->product && $item->product->mainImage)
                        <img src="{{ asset('storage/' . $item->product->mainImage->image_path) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-xl">📦</div>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="font-medium">{{ $item->name }}</div>
                    <div class="text-sm text-gray-500">{{ number_format($item->price, 2) }} FCFA × {{ $item->quantity }}</div>
                </div>
                <div class="font-bold">{{ number_format($item->total, 2) }} FCFA</div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 space-y-2 text-sm border-t pt-4">
            <div class="flex justify-between"><span class="text-gray-500">Sous-total</span><span>{{ number_format($order->subtotal, 2) }} FCFA</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Livraison</span><span>{{ $order->shipping_amount > 0 ? number_format($order->shipping_amount, 2) . ' FCFA' : 'Gratuite' }}</span></div>
            <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                <span>Total</span>
                <span class="text-blue-600">{{ number_format($order->total, 2) }} FCFA</span>
            </div>
        </div>
    </div>
</div>
@endsection
