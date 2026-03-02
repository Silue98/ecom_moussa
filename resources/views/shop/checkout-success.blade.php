@extends('layouts.app')

@section('title', 'Commande confirmée')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-2xl shadow-lg p-10">
        <div class="text-7xl mb-6">🎉</div>
        <h1 class="text-3xl font-bold text-green-600 mb-2">Commande confirmée !</h1>
        <p class="text-gray-600 mb-2">Merci pour votre achat, {{ $order->shipping_name }} !</p>
        <p class="text-sm text-gray-500 mb-8">Votre numéro de commande : <span class="font-bold text-gray-800">{{ $order->order_number }}</span></p>

        <div class="bg-gray-50 rounded-xl p-6 text-left mb-8">
            <h3 class="font-bold mb-4">Récapitulatif</h3>
            @foreach($order->items as $item)
            <div class="flex justify-between text-sm py-2 border-b last:border-0">
                <span>{{ $item->name }} x{{ $item->quantity }}</span>
                <span class="font-medium">{{ number_format($item->total, 2) }} FCFA</span>
            </div>
            @endforeach
            <div class="flex justify-between font-bold mt-4 pt-2 border-t">
                <span>Total payé</span>
                <span class="text-blue-600 text-lg">{{ number_format($order->total, 2) }} FCFA</span>
            </div>
        </div>

        <div class="bg-blue-50 rounded-xl p-4 text-sm text-blue-700 mb-8">
            📧 Un email de confirmation a été envoyé à <strong>{{ $order->shipping_email }}</strong>
        </div>

        <div class="flex gap-4 justify-center">
            <a href="{{ route('account.orders') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Voir mes commandes
            </a>
            <a href="{{ route('products.index') }}" class="border border-gray-300 px-6 py-3 rounded-lg hover:bg-gray-50 transition">
                Continuer mes achats
            </a>
        </div>
    </div>
</div>
@endsection
