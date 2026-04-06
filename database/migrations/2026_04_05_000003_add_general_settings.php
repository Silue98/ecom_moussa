<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['key' => 'site_name',               'value' => 'TrustPhone CI',              'group' => 'general'],
            ['key' => 'site_email',              'value' => 'commandes@trustphone-ci.com', 'group' => 'general'],
            ['key' => 'currency',                'value' => 'FCFA',                        'group' => 'general'],
            ['key' => 'free_shipping_threshold', 'value' => '30000',                       'group' => 'general'],
            ['key' => 'shipping_price',          'value' => '2000',                        'group' => 'general'],
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
            'site_name','site_email','currency',
            'free_shipping_threshold','shipping_price',
        ])->delete();
    }
};
