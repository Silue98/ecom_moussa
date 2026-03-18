<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            [
                'key'   => 'greenapi_enabled',
                'value' => '0',
                'group' => 'whatsapp',
                'type'  => 'string',
            ],
            [
                'key'   => 'greenapi_instance_id',
                'value' => '',
                'group' => 'whatsapp',
                'type'  => 'string',
            ],
            [
                'key'   => 'greenapi_api_token',
                'value' => '',
                'group' => 'whatsapp',
                'type'  => 'string',
            ],
            [
                'key'   => 'greenapi_default_country_code',
                'value' => '225',
                'group' => 'whatsapp',
                'type'  => 'string',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'greenapi_enabled',
            'greenapi_instance_id',
            'greenapi_api_token',
            'greenapi_default_country_code',
        ])->delete();
    }
};
