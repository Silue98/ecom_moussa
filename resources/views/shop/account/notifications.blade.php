@extends('layouts.app')
@section('title', 'Mes notifications')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🔔 Mes notifications</h1>
            <p class="text-gray-500 text-sm mt-1">Historique de vos alertes et mises à jour</p>
        </div>
        <a href="{{ route('account') }}" class="text-sm text-blue-600 hover:underline">← Mon compte</a>
    </div>

    @if($notifications->isEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-16 text-center">
        <p class="text-5xl mb-4">🔔</p>
        <p class="text-gray-500">Aucune notification pour le moment.</p>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @foreach($notifications as $notification)
        <div class="flex gap-4 items-start px-5 py-4 border-b border-gray-50 hover:bg-gray-50 transition">
            <span class="text-2xl flex-shrink-0 mt-0.5">
                @switch($notification->data['type'] ?? '')
                    @case('order_confirmed') ✅ @break
                    @case('order_status_changed') 📦 @break
                    @case('new_order') 🛒 @break
                    @case('low_stock') ⚠️ @break
                    @default 🔔
                @endswitch
            </span>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">{{ $notification->data['title'] ?? 'Notification' }}</p>
                <p class="text-gray-500 text-sm mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }} — {{ $notification->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            @if(isset($notification->data['url']))
            <a href="{{ $notification->data['url'] }}" class="text-xs text-blue-600 hover:underline flex-shrink-0 mt-1">Voir →</a>
            @endif
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
