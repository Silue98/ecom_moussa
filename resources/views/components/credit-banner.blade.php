@php
    $creditEnabled = setting('credit_enabled', '0') === '1';
    $groupeAPct    = (int) setting('credit_groupe_a_acompte', 40);
    $groupeBPct    = (int) setting('credit_groupe_b_acompte', 50);
@endphp

@if($creditEnabled)
<div style="background:linear-gradient(90deg,#92400e,#d97706);border-radius:12px;padding:10px 16px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:18px;">💳</span>
        <div>
            <p style="font-weight:700;color:white;font-size:13px;margin:0;">Achetez à crédit — Repartez aujourd'hui !</p>
            <p style="color:#fde68a;font-size:11px;margin:0;">
                XR → 15 Pro : <strong style="color:white;">{{ $groupeAPct }}%</strong>
                &nbsp;·&nbsp;
                15 PM → 17 Pro Max : <strong style="color:white;">{{ $groupeBPct }}%</strong>
                &nbsp;·&nbsp;
                <strong style="color:#fef08a;">12 semaines</strong>
            </p>
        </div>
    </div>
    <a href="{{ route('credit.info') }}" style="background:white;color:#92400e;font-weight:700;padding:6px 14px;border-radius:8px;font-size:12px;text-decoration:none;white-space:nowrap;">
        En savoir + →
    </a>
</div>
@endif