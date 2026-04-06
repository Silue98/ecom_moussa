<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['key' => 'credit_nb_echeances',  'value' => '3',           'group' => 'credit'],
            ['key' => 'credit_pourcentages',  'value' => '30,40,30',    'group' => 'credit'],
            ['key' => 'credit_taux_interet',  'value' => '5',           'group' => 'credit'],
            ['key' => 'credit_montant_min',   'value' => '100000',      'group' => 'credit'],
            ['key' => 'credit_documents',     'value' => "Carte Nationale d'Identité (CNI) valide\nJustificatif de domicile récent\nUne photo d'identité", 'group' => 'credit'],
        ];
        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(['key' => $s['key']], $s);
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'credit_nb_echeances','credit_pourcentages',
            'credit_taux_interet','credit_montant_min','credit_documents',
        ])->delete();
    }
};
