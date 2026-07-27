@php
$creditEnabled = setting('credit_enabled', '0') === '1';
$groupeBRaw = strtolower(setting('credit_groupe_b_keywords', '15 pro max,16,17'));
$groupeARaw = strtolower(setting('credit_groupe_a_keywords', 'xr,11,12,13,14,15 pro'));
$groupeAPct = (int)   setting('credit_groupe_a_acompte', 40);
$groupeBPct = (int)   setting('credit_groupe_b_acompte', 50);
$nbMois     = (int)   setting('credit_nb_mois', 12);
$tauxMois   = (float) setting('credit_taux_mensuel', 1.5);

$nomProduit = strtolower(preg_replace('/\s+/', ' ', trim($product->name)));
$groupe     = null;
$acomptePct = 0;

foreach (array_map('trim', explode(',', $groupeBRaw)) as $kw) {
    if ($kw !== '' && str_contains($nomProduit, $kw)) {
        $groupe = 'B'; $acomptePct = $groupeBPct; break;
    }
}
if (!$groupe) {
    foreach (array_map('trim', explode(',', $groupeARaw)) as $kw) {
        if ($kw !== '' && str_contains($nomProduit, $kw)) {
            $groupe = 'A'; $acomptePct = $groupeAPct; break;
        }
    }
}

$creditEligible = $creditEnabled && $groupe !== null;

if ($creditEligible) {
    $prix       = (float) $product->price;
    $acompte    = (int) round($prix * $acomptePct / 100);
    $reste      = $prix - $acompte;
    $mensualite = (int) round($reste * (1 + $tauxMois / 100) / $nbMois);
    $total      = $acompte + ($mensualite * $nbMois);
    $surcout    = $total - $prix;

    $shopWaPhone = preg_replace('/[^0-9]/', '', setting('shop_phone', ''));
    if (strlen($shopWaPhone) === 10) { $shopWaPhone = '225' . substr($shopWaPhone, 2); }

    $waCreditMsg = urlencode(
        "Bonjour TrustPhone CI ! 👋\n\n" .
        "Je souhaite acheter à crédit :\n" .
        "📱 " . $product->name . "\n" .
        "💰 Prix : " . number_format($prix, 0, ',', ' ') . " FCFA\n\n" .
        "📋 Mon plan de paiement :\n" .
        "  • Acompte aujourd'hui : " . number_format($acompte, 0, ',', ' ') . " FCFA (" . $acomptePct . "%)\n" .
        "  • semaines × " . $nbMois . " : " . number_format($mensualite, 0, ',', ' ') . " FCFA/mois\n" .
        "  • Total : " . number_format($total, 0, ',', ' ') . " FCFA\n\n" .
        "Je viendrai en boutique avec ma CNI. Merci !"
    );

    $creditDocs = setting('credit_documents', '');
    $docsList   = $creditDocs ? array_filter(array_map('trim', explode(PHP_EOL, $creditDocs))) : [];
}
@endphp

@if($creditEligible)
<div class="rounded-2xl overflow-hidden mb-4 shadow-md" style="border:2px solid #d97706;">

    {{-- En-tête --}}
    <div style="background:linear-gradient(135deg,#92400e 0%,#d97706 50%,#fbbf24 100%);" class="px-4 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <span style="font-size:20px;">💳</span>
                </div>
                <div>
                    <p class="font-extrabold text-white text-base leading-tight">Achetez à crédit — Repartez aujourd'hui !</p>
                    <p class="text-amber-100 text-xs mt-0.5">Paiement en {{ $nbMois }} Semaines après acompte</p>
                </div>
            </div>
            <div class="bg-white/25 rounded-full px-3 py-1 text-xs font-bold text-white flex-shrink-0">
                {{ $acomptePct }}% acompte
            </div>
        </div>
    </div>

    {{-- Corps --}}
    <div class="bg-white px-4 py-4 space-y-4">

        {{-- Résumé 3 colonnes --}}
        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="bg-amber-50 border border-amber-200 rounded-xl py-3 px-1">
                <p class="text-xs text-amber-600 font-semibold mb-1">💰 Acompte</p>
                <p class="text-base font-extrabold text-amber-800">{{ number_format($acompte, 0, ',', ' ') }}</p>
                <p class="text-xs text-amber-500">FCFA aujourd'hui</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl py-3 px-1">
                <p class="text-xs text-blue-600 font-semibold mb-1">📅 /Sem</p>
                <p class="text-base font-extrabold text-blue-800">{{ number_format($mensualite, 0, ',', ' ') }}</p>
                <p class="text-xs text-blue-500">FCFA × {{ $nbMois }} Sem</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl py-3 px-1">
                <p class="text-xs text-green-600 font-semibold mb-1">✅ Total</p>
                <p class="text-base font-extrabold text-green-800">{{ number_format($total, 0, ',', ' ') }}</p>
                <p class="text-xs text-green-500">FCFA au total</p>
            </div>
        </div>

        {{-- Détail financier --}}
        <div class="bg-gray-50 rounded-xl p-3 space-y-1.5 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Prix du téléphone</span>
                <span class="font-semibold">{{ number_format($prix, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Acompte ({{ $acomptePct }}%) — aujourd'hui</span>
                <span class="font-bold text-amber-700">− {{ number_format($acompte, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="flex justify-between border-t border-gray-200 pt-1">
                <span class="text-gray-500">Reste à financer</span>
                <span class="font-semibold">{{ number_format($reste, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="flex justify-between text-xs text-gray-400">
                <span>Intérêt crédit ({{ $tauxMois }}% sur {{ $nbMois }} mois)</span>
                <span>+ {{ number_format($surcout, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="flex justify-between border-t border-gray-300 pt-1.5">
                <span class="font-bold text-gray-800">Mensualité / mois</span>
                <span class="font-extrabold text-blue-700 text-base">{{ number_format($mensualite, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        {{-- Échéancier simplifié --}}
        <div>
            <p class="text-xs font-bold text-gray-600 mb-2">📅 Votre échéancier</p>
            <div class="space-y-1.5">

                {{-- Acompte J0 --}}
                <div class="flex items-center gap-2 rounded-xl px-3 py-2.5 bg-amber-50 border border-amber-300">
                    <div class="w-7 h-7 rounded-full bg-amber-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">0</div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-amber-800">Acompte — aujourd'hui en boutique</p>
                        <p class="text-xs text-amber-600">{{ $acomptePct }}% du prix — obligatoire pour repartir avec l'iPhone</p>
                    </div>
                    <span class="text-sm font-extrabold text-amber-800">{{ number_format($acompte, 0, ',', ' ') }} FCFA</span>
                </div>

                {{-- Mensualités résumées en 1 ligne --}}
                <div class="flex items-center gap-2 rounded-xl px-3 py-2.5 bg-blue-50 border border-blue-200">
                    <div class="w-7 h-7 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">×{{ $nbMois }}</div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-blue-800">{{ $nbMois }} semaine — de la semaine 1 à la semaine {{ $nbMois }}</p>
                        <p class="text-xs text-blue-600">Même montant chaque semaine</p>
                    </div>
                    <span class="text-sm font-extrabold text-blue-800">{{ number_format($mensualite, 0, ',', ' ') }} FCFA</span>
                </div>

                {{-- Solde final --}}
                <div class="flex items-center gap-2 rounded-xl px-3 py-2.5 bg-green-50 border border-green-300">
                    <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">✓</div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-green-800">iPhone entièrement payé !</p>
                        <p class="text-xs text-green-600">Après {{ $nbMois }} semaines</p>
                    </div>
                    <span class="text-sm font-extrabold text-green-700">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>

            </div>
        </div>

        {{-- Documents --}}
        @if(count($docsList ?? []) > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
            <p class="text-xs font-bold text-blue-800 mb-1.5">📋 Documents à apporter en boutique</p>
            <ul class="space-y-1">
                @foreach($docsList as $doc)
                <li class="flex items-start gap-2 text-xs text-blue-700">
                    <span class="text-blue-500 font-bold flex-shrink-0">✓</span>
                    <span>{{ $doc }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
            <p class="text-xs font-bold text-blue-800 mb-1.5">📋 Documents à apporter en boutique</p>
            <ul class="space-y-1 text-xs text-blue-700">
                <li class="flex items-start gap-2"><span class="font-bold text-blue-500">✓</span> Carte Nationale d'Identité (CNI)</li>
                <li class="flex items-start gap-2"><span class="font-bold text-blue-500">✓</span> Justificatif de domicile récent</li>
                <li class="flex items-start gap-2"><span class="font-bold text-blue-500">✓</span> Numéro de téléphone valide</li>
            </ul>
        </div>
        @endif

        {{-- CTA WhatsApp --}}
        @if($shopWaPhone ?? false)
        <a href="https://wa.me/{{ $shopWaPhone }}?text={{ $waCreditMsg }}"
           target="_blank" rel="noopener"
           class="flex items-center justify-center gap-2 w-full font-bold py-3.5 rounded-xl transition text-sm text-white"
           style="background:linear-gradient(135deg,#16a34a,#22c55e);">
            <svg viewBox="0 0 24 24" class="w-5 h-5 fill-white flex-shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.017.5 3.926 1.381 5.601L0 24l6.545-1.364A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.013-1.376l-.36-.214-3.727.977.996-3.631-.235-.374A9.789 9.789 0 012.182 12C2.182 6.578 6.578 2.182 12 2.182S21.818 6.578 21.818 12 17.422 21.818 12 21.818z"/></svg>
            💬 Je veux acheter cet iPhone à crédit
        </a>
        @endif

        <p class="text-center text-xs text-gray-400">⚠️ Le crédit se finalise uniquement en boutique · Sous réserve d'acceptation</p>
    </div>
</div>
@endif