@extends('layouts.app')

@section('title', 'Mon Compte')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <aside>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-2xl font-bold text-blue-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="font-bold">{{ $user->name }}</div>
                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('account') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition {{ request()->routeIs('account') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                        🏠 Tableau de bord
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition text-gray-700">
                        📦 Mes commandes
                    </a>
                    <a href="{{ route('account.wishlist') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition text-gray-700">
                        ❤️ Mes favoris
                    </a>
                    <a href="{{ route('account.profile') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition text-gray-700">
                        👤 Mon profil
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div class="lg:col-span-3">
            <h1 class="text-2xl font-bold mb-6">Bienvenue, {{ $user->name }} ! 👋</h1>

            <!-- Quick stats -->
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $user->orders()->count() }}</div>
                    <div class="text-sm text-gray-500">Commandes</div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $user->wishlist()->count() }}</div>
                    <div class="text-sm text-gray-500">Favoris</div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($user->orders()->where('payment_status', 'paid')->sum('total'), 0) }}</div>
                    <div class="text-sm text-gray-500">XOF dépensés</div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="font-bold text-lg mb-4">Commandes récentes</h2>
                @if($orders->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-gray-500">
                                    <th class="pb-3">N° Commande</th>
                                    <th class="pb-3">Date</th>
                                    <th class="pb-3">Statut</th>
                                    <th class="pb-3">Total</th>
                                    <th class="pb-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr class="border-b last:border-0 hover:bg-gray-50">
                                    <td class="py-3 font-medium">{{ $order->order_number }}</td>
                                    <td class="py-3 text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ match($order->status) {
                                                'delivered' => 'bg-green-100 text-green-700',
                                                'shipped' => 'bg-blue-100 text-blue-700',
                                                'processing' => 'bg-yellow-100 text-yellow-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-700'
                                            } }}">
                                            {{ match($order->status) {
                                                'pending' => 'En attente',
                                                'processing' => 'En traitement',
                                                'shipped' => 'Expédié',
                                                'delivered' => 'Livré',
                                                'cancelled' => 'Annulé',
                                                default => $order->status
                                            } }}
                                        </span>
                                    </td>
                                    <td class="py-3 font-semibold">{{ number_format($order->total, 2) }} FCFA</td>
                                    <td class="py-3">
                                        <a href="{{ route('account.order', $order) }}" class="text-blue-600 hover:underline">Voir →</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('account.orders') }}" class="block text-center text-blue-600 hover:underline text-sm mt-4">Voir toutes mes commandes</a>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-4xl mb-3">📦</div>
                        <p>Vous n'avez pas encore de commandes</p>
                        <a href="{{ route('products.index') }}" class="mt-3 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                            Commencer à acheter
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
