@php
    $creditEnabled = setting('credit_enabled', '0') === '1';
    $groupeAPct    = (int) setting('credit_groupe_a_acompte', 40);
    $groupeBPct    = (int) setting('credit_groupe_b_acompte', 50);
@endphp

@if($creditEnabled)
<div class="rounded-xl mb-4 px-4 py-3 flex items-center justify-between gap-3 flex-wrap"
     style="background: linear-gradient(90deg,#92400e,#d97706);">
    <div class="flex items-center gap-2">
        <span class="text-lg">💳</span>
        <div>
            <p class="font-bold text-white text-sm">Achetez à crédit — Repartez aujourd'hui !</p>
            <p class="text-amber-100 text-xs">
                XR → 15 Pro : <strong class="text-white">{{ $groupeAPct }}%</strong>
                &nbsp;·&nbsp;
                15 PM → 17 PM : <strong class="text-white">{{ $groupeBPct }}%</strong>
                &nbsp;·&nbsp;
                <strong class="text-yellow-200">12 mensualités</strong>
            </p>
        </div>
    </div>
    <a href="{{ route('credit.info') }}"
       class="bg-white text-amber-800 font-bold px-3 py-1.5 rounded-lg text-xs hover:bg-amber-50 transition whitespace-nowrap">
        En savoir + →
    </a>
</div>
@endif