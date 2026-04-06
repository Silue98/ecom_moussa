<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            // Hero principal
            ['key' => 'hero_badge',          'value' => '✅ Paiement à la réception · Garantie vendeur 3 mois', 'group' => 'hero'],
            ['key' => 'hero_title_line1',    'value' => 'Votre iPhone',                                          'group' => 'hero'],
            ['key' => 'hero_title_line2',    'value' => 'livré à Abidjan',                                       'group' => 'hero'],
            ['key' => 'hero_description',    'value' => 'Spécialiste iPhone depuis des années. Tous nos appareils sont neufs, débloqués tous opérateurs et livrés avec garantie. Vous vérifiez avant de payer.', 'group' => 'hero'],
            ['key' => 'hero_btn1_text',      'value' => '📱 Voir tous les iPhones',                             'group' => 'hero'],
            ['key' => 'hero_btn2_text',      'value' => '🆕 Nouveautés iPhone 16',                             'group' => 'hero'],

            // Cards Hero (droite)
            ['key' => 'hero_card1_title',    'value' => 'iPhone 16 Series',      'group' => 'hero'],
            ['key' => 'hero_card1_sub',      'value' => 'Dès 850 000 FCFA',      'group' => 'hero'],
            ['key' => 'hero_card2_title',    'value' => 'Neufs & Débloqués',     'group' => 'hero'],
            ['key' => 'hero_card2_sub',      'value' => 'Tous opérateurs CI',    'group' => 'hero'],
            ['key' => 'hero_card3_title',    'value' => 'Paiement à la réception', 'group' => 'hero'],
            ['key' => 'hero_card3_sub',      'value' => 'Vous vérifiez avant',   'group' => 'hero'],
            ['key' => 'hero_card4_title',    'value' => 'Garantie 3 mois',       'group' => 'hero'],
            ['key' => 'hero_card4_sub',      'value' => 'Service après-vente',   'group' => 'hero'],

            // Badges de confiance
            ['key' => 'badge1_title',        'value' => 'Livraison rapide',            'group' => 'hero'],
            ['key' => 'badge1_sub',          'value' => '24–48h à Abidjan',            'group' => 'hero'],
            ['key' => 'badge2_title',        'value' => 'Paiement à la réception',     'group' => 'hero'],
            ['key' => 'badge2_sub',          'value' => 'Vous vérifiez avant de payer','group' => 'hero'],
            ['key' => 'badge3_title',        'value' => 'Garantie vendeur',            'group' => 'hero'],
            ['key' => 'badge3_sub',          'value' => '3 mois sur chaque iPhone',    'group' => 'hero'],
            ['key' => 'badge4_title',        'value' => '100% neufs',                  'group' => 'hero'],
            ['key' => 'badge4_sub',          'value' => 'Débloqués tous opérateurs',   'group' => 'hero'],
        ];

        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(
                ['key' => $s['key']],
                array_merge($s, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'hero_badge','hero_title_line1','hero_title_line2','hero_description',
            'hero_btn1_text','hero_btn2_text',
            'hero_card1_title','hero_card1_sub','hero_card2_title','hero_card2_sub',
            'hero_card3_title','hero_card3_sub','hero_card4_title','hero_card4_sub',
            'badge1_title','badge1_sub','badge2_title','badge2_sub',
            'badge3_title','badge3_sub','badge4_title','badge4_sub',
        ])->delete();
    }
};
