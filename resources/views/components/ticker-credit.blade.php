@php
    $creditEnabled = setting('credit_enabled', '0') === '1';
    $tickerText    = setting('credit_ticker_text', '');
    $showTicker    = $creditEnabled && $tickerText;
@endphp

@if($showTicker)
{{-- ══ RUBAN DÉFILANT CRÉDIT ══════════════════════════════════════════════ --}}
<div class="ticker-wrap overflow-hidden" style="background: linear-gradient(90deg,#92400e,#d97706,#f59e0b,#d97706,#92400e); background-size:400% 100%;">
    <style>
    .ticker-wrap { height: 36px; display: flex; align-items: center; }
    .ticker-track {
        display: flex;
        white-space: nowrap;
        animation: ticker-scroll 28s linear infinite;
        will-change: transform;
    }
    .ticker-track:hover { animation-play-state: paused; cursor: pointer; }
    .ticker-item {
        display: inline-flex;
        align-items: center;
        padding: 0 2.5rem;
        font-size: 0.78rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .ticker-sep {
        color: rgba(255,255,255,0.45);
        font-size: 1rem;
        padding: 0 0.5rem;
    }
    @keyframes ticker-scroll {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    </style>

    {{-- On duplique le texte pour un défilement infini sans saut --}}
    @php
        // Découper le texte en items (séparateur : ·)
        $items = array_filter(array_map('trim', explode('·', $tickerText)));
        if (empty($items)) $items = [$tickerText];
    @endphp

    <a href="{{ route('credit.info') }}" class="block w-full" style="text-decoration:none;">
        <div class="ticker-track">
            {{-- 1ère copie --}}
            @foreach($items as $item)
                <span class="ticker-item">{{ $item }}</span>
                <span class="ticker-sep">·</span>
            @endforeach
            {{-- 2ème copie (pour boucle infinie) --}}
            @foreach($items as $item)
                <span class="ticker-item">{{ $item }}</span>
                <span class="ticker-sep">·</span>
            @endforeach
        </div>
    </a>
</div>
@endif
