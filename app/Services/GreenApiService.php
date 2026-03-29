<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

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

        // Nettoyer le numéro (garder uniquement les chiffres)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Garder les 8 derniers chiffres + code pays 225 (Côte d'Ivoire)
        $phone = '225' . substr($phone, -8);

        $chatId = $phone . '@c.us';
        $url    = "{$this->baseUrl}/waInstance{$instanceId}/sendMessage/{$apiToken}";

        Log::info("[GreenAPI] Tentative d'envoi à {$phone}.", ['chatId' => $chatId]);

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode([
                    'chatId'  => $chatId,
                    'message' => $message,
                ]),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT        => 15,
            ]);

            $responseBody = curl_exec($ch);
            $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError    = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error("[GreenAPI] ❌ Erreur cURL : {$curlError}");
                return false;
            }

            $responseData = json_decode($responseBody, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info("[GreenAPI] ✅ Message envoyé à {$phone}.", ['response' => $responseData]);
                return true;
            }

            Log::warning("[GreenAPI] ❌ Échec envoi à {$phone}.", [
                'status'   => $httpCode,
                'response' => $responseBody,
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