<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute les paramètres de géolocalisation dans la table settings.
 * Exécuter avec : php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        $geoSettings = [
            [
                'key'   => 'shop_latitude',
                'value' => '5.3041',   // Treichville par défaut
                'group' => 'geo',
            ],
            [
                'key'   => 'shop_longitude',
                'value' => '-4.0024',  // Treichville par défaut
                'group' => 'geo',
            ],
        ];

        foreach ($geoSettings as $setting) {
            // Insert seulement si la clé n'existe pas encore
            DB::table('settings')->insertOrIgnore($setting);
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['shop_latitude', 'shop_longitude'])->delete();
    }
};
