@extends('layouts.app')

@section('title', 'Commande confirmée ✅')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

    {{-- ═══ BANDEAU SUCCÈS PRINCIPAL ═══ --}}
    <div class="bg-green-500 text-white rounded-2xl p-8 text-center mb-6 shadow-lg">
        <div class="text-6xl mb-3">✅</div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-2">Commande confirmée !</h1>
        <p class="text-green-100 text-base">Merci <strong>{{ $order->shipping_name }}</strong>, votre commande a bien été enregistrée.</p>
        <div class="mt-4 bg-white/20 rounded-xl px-4 py-2 inline-block">
            <span class="text-sm text-green-100">Numéro de commande</span>
            <div class="text-xl font-bold">{{ $order->order_number }}</div>
        </div>
    </div>

    {{-- ═══ INFO EMAIL ═══ --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3 mb-6">
        <span class="text-2xl flex-shrink-0">📧</span>
        <div class="text-sm text-blue-800">
            <p class="font-semibold">Un email de confirmation a été envoyé</p>
            <p>Vérifiez votre boîte <strong>{{ $order->shipping_email }}</strong><br>
            <span class="text-blue-600">(Pensez à vérifier les spams si vous ne le trouvez pas)</span></p>
        </div>
    </div>

    {{-- ═══ RÉCAPITULATIF PRODUITS ═══ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span>📦</span> Récapitulatif de votre commande
        </h2>

        <div class="space-y-3 mb-4">
            @foreach($order->items as $item)
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    @if($item->product && $item->product->mainImage)
                        <img src="{{ asset('storage/' . $item->product->mainImage->image_path) }}"
                             alt="{{ $item->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-xl">📱</div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm text-gray-800 truncate">{{ $item->name }}</p>
                    <p class="text-xs text-gray-500">Qté : {{ $item->quantity }}</p>
                </div>
                <span class="font-semibold text-sm text-gray-800 flex-shrink-0">
                    {{ number_format($item->total, 0, ',', ' ') }} FCFA
                </span>
            </div>
            @endforeach
        </div>

        {{-- Totaux --}}
        <div class="border-t pt-3 space-y-1 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Sous-total</span>
                <span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Frais de livraison</span>
                <span>{{ $order->shipping_amount > 0 ? number_format($order->shipping_amount, 0, ',', ' ') . ' FCFA' : '🎉 Gratuite' }}</span>
            </div>
            <div class="flex justify-between font-bold text-base pt-2 border-t mt-2">
                <span>Total payé</span>
                <span class="text-blue-600 text-lg">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    {{-- ═══ LIVRAISON & PAIEMENT ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2"><span>🚚</span> Livraison</h3>
            <p class="text-sm text-gray-600">{{ $order->shipping_address }}</p>
            <p class="text-sm text-gray-600">{{ $order->shipping_city }}</p>
            @if($order->shipping_phone)
            <p class="text-sm text-gray-600 mt-1">📞 {{ $order->shipping_phone }}</p>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2"><span>💳</span> Paiement</h3>
            <div class="flex items-center gap-2">
                <span class="text-xl">💵</span>
                <div>
                    <p class="text-sm font-medium text-gray-800">À la livraison</p>
                    <p class="text-xs text-gray-500">Payez en espèces à la réception</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ SUIVI DE STATUT ═══ --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <span class="text-2xl flex-shrink-0">⏳</span>
            <div class="text-sm text-amber-800">
                <p class="font-semibold">Votre commande est en cours de traitement</p>
                <p class="mt-1">Statut actuel : <span class="font-bold">En attente de traitement</span></p>
                <p class="mt-1 text-amber-700">Vous recevrez un email dès que votre commande sera expédiée.</p>
            </div>
        </div>
    </div>

    {{-- ═══ ACTIONS ═══ --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        @auth
        <a href="{{ route('account.orders') }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition text-center">
            📦 Voir mes commandes
        </a>
        @endauth
        <a href="{{ route('products.index') }}"
           class="border border-gray-300 bg-white px-6 py-3 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 transition text-center">
            🛍️ Continuer mes achats
        </a>
    </div>

</div>
@endsection
