@auth
@php
    $notifications = auth()->user()->unreadNotifications()->latest()->take(5)->get();
    $unreadCount   = auth()->user()->unreadNotifications()->count();
@endphp

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600 transition">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden"
    >
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 text-sm">Notifications</h3>
            @if($unreadCount > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button class="text-xs text-blue-600 hover:text-blue-800">Tout marquer lu</button>
            </form>
            @endif
        </div>

        <div class="max-h-72 overflow-y-auto">
            @forelse($notifications as $notification)
            <a href="{{ $notification->data['url'] ?? '#' }}"
               class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 transition {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}"
               onclick="markRead('{{ $notification->id }}')">
                <div class="flex gap-3 items-start">
                    <span class="text-lg flex-shrink-0">
                        @switch($notification->data['type'] ?? '')
                            @case('order_confirmed') ✅ @break
                            @case('order_status_changed') 📦 @break
                            @case('new_order') 🛒 @break
                            @case('low_stock') ⚠️ @break
                            @default 🔔
                        @endswitch
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800">{{ $notification->data['title'] ?? 'Notification' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(is_null($notification->read_at))
                    <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"></span>
                    @endif
                </div>
            </a>
            @empty
            <div class="px-4 py-8 text-center">
                <p class="text-4xl mb-2">🔔</p>
                <p class="text-sm text-gray-500">Aucune notification</p>
            </div>
            @endforelse
        </div>

        @if($unreadCount > 5)
        <div class="px-4 py-2 border-t border-gray-100 text-center">
            <a href="{{ route('account.notifications') }}" class="text-xs text-blue-600 hover:underline">
                Voir toutes les notifications ({{ $unreadCount }})
            </a>
        </div>
        @endif
    </div>
</div>

<script>
function markRead(id) {
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    });
}
</script>
@endauth
