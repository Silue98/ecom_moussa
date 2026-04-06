@extends('layouts.app')
@section('title', 'Suivi — Commande ' . $order->order_number)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <a href="{{ route('account.order', $order) }}" class="text-blue-600 hover:underline text-sm mb-6 inline-block">← Retour au détail</a>

    <div class="bg-white rounded-2xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold">Suivi de votre commande</h1>
                <p class="text-gray-500 text-sm mt-1">N° {{ $order->order_number }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-semibold
                {{ match($order->status) {
                    'delivered'  => 'bg-green-100 text-green-700',
                    'shipped'    => 'bg-blue-100 text-blue-700',
                    'processing' => 'bg-yellow-100 text-yellow-700',
                    'cancelled'  => 'bg-red-100 text-red-700',
                    default      => 'bg-gray-100 text-gray-700'
                } }}">
                {{ $order->status_label }}
            </span>
        </div>

        @php
            $steps = [
                ['key' => 'pending',    'label' => 'Commande reçue',      'icon' => '✅', 'desc' => 'Votre commande a été enregistrée avec succès.'],
                ['key' => 'processing', 'label' => 'En préparation',      'icon' => '📦', 'desc' => 'Nous préparons votre colis avec soin.'],
                ['key' => 'shipped',    'label' => 'En cours de livraison','icon' => '🚚', 'desc' => 'Votre colis est en route vers vous.'],
                ['key' => 'delivered',  'label' => 'Livré',               'icon' => '🎉', 'desc' => 'Votre commande a été livrée. Profitez-en !'],
            ];
            $statusOrder = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'delivered' => 3, 'cancelled' => -1];
            $currentIdx  = $statusOrder[$order->status] ?? 0;
        @endphp

        @if($order->status === 'cancelled')
        <div class="flex items-center gap-4 p-4 bg-red-50 border border-red-200 rounded-xl">
            <span class="text-3xl">❌</span>
            <div>
                <p class="font-semibold text-red-700">Commande annulée</p>
                <p class="text-sm text-red-600 mt-1">Cette commande a été annulée. Contactez-nous si vous avez des questions.</p>
            </div>
        </div>
        @else
        {{-- Timeline --}}
        <div class="relative">
            @foreach($steps as $idx => $step)
            @php
                $done    = $idx < $currentIdx;
                $current = $idx === $currentIdx;
                $future  = $idx > $currentIdx;
            @endphp
            <div class="flex items-start gap-4 {{ !$loop->last ? 'pb-8' : '' }} relative">
                {{-- Ligne verticale --}}
                @if(!$loop->last)
                <div class="absolute left-5 top-10 w-0.5 h-full {{ $done ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                @endif

                {{-- Icône étape --}}
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 z-10 border-2
                    {{ $done ? 'bg-blue-600 border-blue-600 text-white' :
                       ($current ? 'bg-white border-blue-500 text-blue-600' :
                       'bg-white border-gray-200 text-gray-300') }}">
                    @if($done)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @elseif($current)
                        <span class="text-lg">{{ $step['icon'] }}</span>
                    @else
                        <span class="w-3 h-3 rounded-full bg-gray-200"></span>
                    @endif
                </div>

                {{-- Contenu --}}
                <div class="flex-1 pt-1.5">
                    <p class="font-semibold text-sm {{ $future ? 'text-gray-400' : 'text-gray-800' }}">{{ $step['label'] }}</p>
                    @if($current || $done)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $step['desc'] }}</p>
                    @if($current && $order->tracking_number && $step['key'] === 'shipped')
                    <p class="text-xs text-blue-600 mt-1 font-medium">N° de suivi : {{ $order->tracking_number }}</p>
                    @endif
                    @php
                        $dateField = match($step['key']) {
                            'pending'    => $order->created_at,
                            'shipped'    => $order->shipped_at,
                            'delivered'  => $order->delivered_at,
                            default      => null,
                        };
                    @endphp
                    @if($dateField)
                    <p class="text-xs text-gray-400 mt-1">{{ $dateField->format('d/m/Y à H:i') }}</p>
                    @endif
                    @endif
                </div>

                @if($current)
                <div class="flex-shrink-0 mt-1">
                    <span class="flex h-2 w-2">
                        <span class="animate-ping absolute h-2 w-2 rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Infos livraison --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <h2 class="font-semibold mb-3">📍 Adresse de livraison</h2>
        <div class="text-sm text-gray-600 space-y-0.5">
            <p class="font-medium text-gray-800">{{ $order->shipping_name }}</p>
            @if($order->shipping_address)<p>{{ $order->shipping_address }}</p>@endif
            <p>{{ $order->shipping_city }} {{ $order->shipping_zip }}</p>
            @if($order->shipping_phone)<p>📞 {{ $order->shipping_phone }}</p>@endif
        </div>
    </div>

    {{-- Besoin d'aide ? --}}
    @php $waPhone = preg_replace('/[^0-9]/', '', setting('shop_phone', '')); @endphp
    @if($waPhone)
    @php if(strlen($waPhone) === 10) $waPhone = '225' . substr($waPhone, 2); @endphp
    <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Bonjour, j\'ai une question sur ma commande #' . $order->order_number) }}"
       target="_blank"
       class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-xl transition">
        <svg viewBox="0 0 24 24" class="w-5 h-5 fill-white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.017.5 3.926 1.381 5.601L0 24l6.545-1.364A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.013-1.376l-.36-.214-3.727.977.996-3.631-.235-.374A9.789 9.789 0 012.182 12C2.182 6.578 6.578 2.182 12 2.182S21.818 6.578 21.818 12 17.422 21.818 12 21.818z"/></svg>
        Une question sur ma commande ?
    </a>
    @endif
</div>
@endsection
