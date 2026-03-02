@extends('layouts.app')

@section('title', 'Mes Commandes')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">📦 Mes Commandes</h1>

    @if($orders->count())
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="font-bold text-lg">{{ $order->order_number }}</div>
                        <div class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y à H:i') }}</div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ match($order->status) {
                                    'delivered' => 'bg-green-100 text-green-700',
                                    'shipped' => 'bg-blue-100 text-blue-700',
                                    'processing' => 'bg-yellow-100 text-yellow-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                } }}">
                                {{ match($order->status) {
                                    'pending' => '⏳ En attente',
                                    'processing' => '⚙️ En traitement',
                                    'shipped' => '🚚 Expédié',
                                    'delivered' => '✅ Livré',
                                    'cancelled' => '❌ Annulé',
                                    default => $order->status
                                } }}
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $order->payment_status === 'paid' ? '💳 Payé' : '💰 En attente' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($order->total, 2) }} FCFA</div>
                        <div class="text-sm text-gray-500">{{ $order->items->count() }} article(s)</div>
                        <a href="{{ route('account.order', $order) }}" class="mt-2 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                            Voir détails →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-16 text-center">
            <div class="text-6xl mb-4">📦</div>
            <h2 class="text-xl font-semibold mb-2">Aucune commande</h2>
            <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition">
                Commencer à acheter
            </a>
        </div>
    @endif
</div>
@endsection
