@php
    $creditEnabled   = setting('credit_enabled', '0') === '1';
    $creditMontantMin= (float) setting('credit_montant_min', 100000);
    $creditEligible  = $creditEnabled && $product->price >= $creditMontantMin;
    $shopWaPhone     = preg_replace('/[^0-9]/', '', setting('shop_phone', ''));
    if (strlen($shopWaPhone) === 10) { $shopWaPhone = '225' . substr($shopWaPhone, 2); }
    if ($creditEligible) {
        $creditNb   = (int)   setting('credit_nb_echeances', 3);
        $creditTaux = (float) setting('credit_taux_interet', 0);
        $creditPcts = array_map('intval', explode(',', str_replace(' ', '', setting('credit_pourcentages', '30,40,30'))));
        $creditDocs = setting('credit_documents', '');
        $docsList   = $creditDocs ? array_filter(array_map('trim', explode(PHP_EOL, $creditDocs))) : [];
        $prixTotal  = (int) round($product->price * (1 + $creditTaux / 100));
        $echeances  = [];
        $restant    = $prixTotal;
        foreach ($creditPcts as $cidx => $pct) {
            $montant     = ($cidx === count($creditPcts) - 1) ? $restant : (int) round($prixTotal * $pct / 100);
            $restant    -= $montant;
            $echeances[] = ['pct' => $pct, 'montant' => $montant, 'idx' => $cidx];
        }
        $echLines = '';
        foreach ($echeances as $ck => $ech) {
            $when     = ($ck === 0) ? 'Aujourd hui' : 'Dans ' . $ck . ' mois';
            $echLines .= ($ck+1) . '. Versement ' . ($ck+1) . ' (' . $ech['pct'] . '%) : ' . number_format($ech['montant'], 0, ',', ' ') . ' FCFA - ' . $when . PHP_EOL;
        }
        $waCreditMsg = urlencode(
            'Bonjour TrustPhone CI !' . PHP_EOL . PHP_EOL .
            'Je veux acheter a credit :' . PHP_EOL .
            'iPhone : ' . $product->name . PHP_EOL .
            'Prix : ' . number_format($product->price, 0, ',', ' ') . ' FCFA' . PHP_EOL .
            ($creditTaux > 0 ? 'Interets (' . $creditTaux . '%) : +' . number_format($prixTotal - $product->price, 0, ',', ' ') . ' FCFA' . PHP_EOL : '') .
            'Total : ' . number_format($prixTotal, 0, ',', ' ') . ' FCFA' . PHP_EOL . PHP_EOL .
            'Echeancier (' . $creditNb . ' versements) :' . PHP_EOL .
            $echLines .
            PHP_EOL . 'Je viendrai en boutique avec mes documents. Merci !'
        );
    }
@endphp

@if($creditEligible)
<div class="border-2 border-amber-400 rounded-xl overflow-hidden mb-3">

    <div class="bg-amber-400 px-4 py-3 flex items-center gap-3">
        <span style="font-size:20px;">💳</span>
        <div>
            <p class="font-bold text-amber-900 text-sm">Acheter à crédit en {{ $creditNb }} échéances</p>
            <p class="text-xs text-amber-800">Repartez aujourd'hui — payez en plusieurs fois en boutique</p>
        </div>
    </div>

    <div class="bg-white p-4 space-y-3">

        <div class="space-y-1 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Prix iPhone</span>
                <span class="font-semibold">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($creditTaux > 0)
            <div class="flex justify-between">
                <span class="text-gray-500">Intérêts ({{ $creditTaux }}%)</span>
                <span class="font-semibold text-amber-700">+ {{ number_format($prixTotal - $product->price, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
            <div class="flex justify-between border-t border-gray-100 pt-1">
                <span class="font-bold">Total à rembourser</span>
                <span class="font-bold text-amber-700 text-base">{{ number_format($prixTotal, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <div>
            <p class="text-xs font-bold text-gray-600 mb-2">📅 Votre échéancier</p>
            <div class="space-y-1.5">

                @foreach($echeances as $ech)
                @php
                    $isFirst   = ($ech['idx'] === 0);
                    $rowCls    = $isFirst ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 border border-gray-100';
                    $numCls    = $isFirst ? 'bg-amber-400 text-white' : 'bg-white border-2 border-amber-300 text-amber-700';
                    $lblCls    = $isFirst ? 'text-amber-800' : 'text-gray-700';
                    $dateCls   = $isFirst ? 'text-amber-600' : 'text-gray-400';
                    $amtCls    = $isFirst ? 'text-amber-800' : 'text-gray-800';
                    $versLabel = $isFirst ? '1er versement' : (($ech['idx'] + 1) . 'ème versement');
                    $dateLabel = $isFirst ? "Aujourd'hui — en boutique" : ('Dans ' . $ech['idx'] . ' mois');
                @endphp
                <div class="flex items-center gap-2 rounded-xl px-3 py-2 {{ $rowCls }}">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $numCls }}">
                        {{ $ech['idx'] + 1 }}
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-bold {{ $lblCls }}">
                            {{ $versLabel }}
                            <span class="font-normal text-gray-400 ml-1">({{ $ech['pct'] }}%)</span>
                        </div>
                        <div class="text-xs {{ $dateCls }}">{{ $dateLabel }}</div>
                    </div>
                    <span class="text-sm font-bold {{ $amtCls }}">
                        {{ number_format($ech['montant'], 0, ',', ' ') }} FCFA
                    </span>
                </div>
                @endforeach

                <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-3 py-2">
                    <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">✓</div>
                    <div class="flex-1 text-xs font-bold text-green-800">iPhone entièrement payé</div>
                    <span class="text-sm font-bold text-green-700">{{ number_format($prixTotal, 0, ',', ' ') }} FCFA</span>
                </div>

            </div>
        </div>

        @if(count($docsList) > 0)
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
        @endif

        @if($shopWaPhone)
        <a href="https://wa.me/{{ $shopWaPhone }}?text={{ $waCreditMsg }}"
           target="_blank" rel="noopener"
           class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl transition text-sm">
            <svg viewBox="0 0 24 24" class="w-5 h-5 fill-white flex-shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.017.5 3.926 1.381 5.601L0 24l6.545-1.364A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.013-1.376l-.36-.214-3.727.977.996-3.631-.235-.374A9.789 9.789 0 012.182 12C2.182 6.578 6.578 2.182 12 2.182S21.818 6.578 21.818 12 17.422 21.818 12 21.818z"/></svg>
            Je veux acheter cet iPhone à crédit
        </a>
        @endif

        <p class="text-center text-xs text-gray-400">⚠️ L'achat à crédit se finalise uniquement en boutique</p>
    </div>
</div>
@endif