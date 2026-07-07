<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS message via the Skypush Push SMS API.
     *
     * Failures are caught, logged, and silently swallowed so they never
     * interrupt the main application flow (same policy as mail try/catch).
     *
     * @param  string  $to       E.164-style or local phone number(s).
     *                           Multiple numbers can be comma-separated.
     * @param  string  $message  SMS body text (max 1000 chars).
     * @return bool              True on success, false on any failure.
     */
    public static function send(string $to, string $message): bool
    {
        $to = trim($to);

        if (empty($to) || empty($message)) {
            Log::warning('SmsService: skipped - empty recipient or message.');
            return false;
        }

        $baseUrl = rtrim(config('services.pushsms.url', 'https://pushsms.rehospace.com'), '/');

        // Prefer values stored in the admin Settings panel; fall back to .env
        $dbSettings = \App\Models\Setting::whereIn('key', ['sms_api_key', 'sms_sender_id', 'sms_client_app'])
            ->pluck('value', 'key');

        $apiKey    = $dbSettings->get('sms_api_key') ?: config('services.pushsms.api_key', '');
        $sender    = $dbSettings->get('sms_sender_id') ?: config('services.pushsms.sender', '');
        $clientApp = $dbSettings->get('sms_client_app') ?: config('services.pushsms.client_app', 'HMS');

        if (empty($apiKey)) {
            Log::warning('SmsService: skipped - PUSHSMS_API_KEY is not configured.');
            return false;
        }

        try {
            $payload = [
                'to'         => $to,
                'message'    => $message,
                'client_app' => $clientApp,
            ];

            if (!empty($sender)) {
                $payload['sender'] = $sender;
            }

            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Accept'    => 'application/json',
            ])->post($baseUrl . '/api/v1/send', $payload);

            if ($response->successful()) {
                Log::info('SmsService: OK to [' . $to . '], id=' . ($response->json('id') ?? 'n/a'));
                return true;
            }

            Log::error('SmsService: failed to [' . $to . ']. HTTP ' . $response->status() . ': ' . $response->body());
            return false;

        } catch (\Throwable $e) {
            Log::error('SmsService: exception - ' . $e->getMessage());
            return false;
        }
    }
}