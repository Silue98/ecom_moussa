@php
    $creditEnabled = setting('credit_enabled', '0') === '1';
    $groupeAPct    = (int) setting('credit_groupe_a_acompte', 40);
    $groupeBPct    = (int) setting('credit_groupe_b_acompte', 50);
@endphp

@if($creditEnabled)
<div class="rounded-xl overflow-hidden mb-4" style="background: linear-gradient(90deg,#92400e,#d97706,#f59e0b);">
    <div class="flex items-center justify-between px-4 py-3 gap-3 flex-wrap">
        <div class="flex items-center gap-3">
            <span class="text-xl">💳</span>
            <div>
                <p class="font-extrabold text-white text-sm leading-tight">Achetez à crédit — Repartez aujourd'hui !</p>
                <p class="text-amber-100 text-xs">
                    XR → 15 Pro : <strong class="text-white">{{ $groupeAPct }}% acompte</strong>
                    &nbsp;·&nbsp;
                    15 PM → 17 PM : <strong class="text-white">{{ $groupeBPct }}% acompte</strong>
                    &nbsp;·&nbsp;
                    <strong class="text-yellow-200">12 mensualités</strong>
                </p>
            </div>
        </div>
        <a href="{{ route('credit.info') }}"
           class="bg-white text-amber-800 font-bold px-4 py-2 rounded-lg text-xs hover:bg-amber-50 transition flex-shrink-0">
            En savoir + →
        </a>
    </div>
</div>
@endif