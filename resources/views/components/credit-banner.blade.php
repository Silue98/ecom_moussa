@php
    $creditEnabled = setting('credit_enabled', '0') === '1';
    $groupeAKw     = strtolower(setting('credit_groupe_a_keywords', 'xr,11,12,13,14,15 pro'));
    $groupeBKw     = strtolower(setting('credit_groupe_b_keywords', '15 pro max,16,17'));
    $groupeAPct    = (int) setting('credit_groupe_a_acompte', 40);
    $groupeBPct    = (int) setting('credit_groupe_b_acompte', 50);
@endphp

@if($creditEnabled)
{{-- ══ BANNIÈRE CRÉDIT — page liste produits ═══════════════════════════════ --}}
<div class="rounded-2xl overflow-hidden mb-6 shadow-md" style="background: linear-gradient(135deg,#78350f 0%,#b45309 45%,#f59e0b 100%);">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-5 py-4">

        {{-- Texte gauche --}}
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 text-2xl">
                💳
            </div>
            <div>
                <p class="font-extrabold text-white text-base leading-tight">Achetez à crédit — Repartez aujourd'hui !</p>
                <p class="text-amber-200 text-xs mt-0.5">
                    iPhone XR → 15 Pro : <strong class="text-white">{{ $groupeAPct }}% d'acompte</strong>
                    &nbsp;·&nbsp;
                    15 Pro Max → 17 Pro Max : <strong class="text-white">{{ $groupeBPct }}% d'acompte</strong>
                    &nbsp;·&nbsp;
                    <strong class="text-yellow-300">12 mensualités</strong>
                </p>
            </div>
        </div>

        {{-- Badges + CTA --}}
        <div class="flex items-center gap-3 flex-shrink-0">
            <div class="hidden sm:flex flex-col items-center bg-white/20 rounded-xl px-3 py-2">
                <span class="text-xl font-extrabold text-white">{{ $groupeAPct }}%</span>
                <span class="text-amber-200 text-xs">XR → 15 Pro</span>
            </div>
            <div class="hidden sm:flex flex-col items-center bg-white/20 rounded-xl px-3 py-2">
                <span class="text-xl font-extrabold text-white">{{ $groupeBPct }}%</span>
                <span class="text-amber-200 text-xs">15 PM → 17 PM</span>
            </div>
            <a href="{{ route('credit.info') }}"
               class="bg-white text-amber-800 font-extrabold px-5 py-2.5 rounded-xl text-sm hover:bg-amber-50 transition flex-shrink-0 shadow">
                En savoir + →
            </a>
        </div>

    </div>
</div>
@endif
