<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class GreenApiService
{
    private string $baseUrl = 'https://api.green-api.com';

    private function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function isConfigured(): bool
    {
        return (bool) $this->getSetting('greenapi_enabled', '0')
            && ! empty($this->getSetting('greenapi_instance_id', ''))
            && ! empty($this->getSetting('greenapi_api_token', ''));
    }

    public function sendMessage(string $phone, string $message): bool
    {
        $enabled    = (bool) $this->getSetting('greenapi_enabled', '0');
        $instanceId = $this->getSetting('greenapi_instance_id', '');
        $apiToken   = $this->getSetting('greenapi_api_token', '');

        if (! $enabled || empty($instanceId) || empty($apiToken)) {
            Log::info('[GreenAPI] Désactivé ou non configuré — message non envoyé.');
            return false;
        }

        $phone  = preg_replace('/[^0-9]/', '', $phone);
        $phone  = '225' . substr($phone, -8);
        $chatId = $phone . '@c.us';
        $url    = "{$this->baseUrl}/waInstance{$instanceId}/sendMessage/{$apiToken}";

        Log::info("[GreenAPI] Tentative d'envoi à {$phone}.", ['chatId' => $chatId]);

        try {
            $response = Http::timeout(15)
                ->when(! app()->isProduction(), fn ($http) => $http->withoutVerifying())
                ->post($url, [
                    'chatId'  => $chatId,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info("[GreenAPI] ✅ Message envoyé à {$phone}.", ['response' => $response->json()]);
                return true;
            }

            Log::warning("[GreenAPI] ❌ Échec envoi à {$phone}.", [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error("[GreenAPI] ❌ Exception lors de l'envoi à {$phone}.", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
