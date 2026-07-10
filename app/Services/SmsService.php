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

        // Normalize phone number(s) according to send_sms.md spec (e.g. 2557xxxx)
        $numbers = array_map(function ($num) {
            $num = preg_replace('/[^0-9]/', '', trim($num));
            // Convert local Tanzanian/regional format (07... / 06...) to international 255... format
            if (preg_match('/^0([67]\d{8})$/', $num, $matches)) {
                return '255' . $matches[1];
            }
            return $num;
        }, explode(',', $to));
        $normalizedTo = implode(',', array_filter($numbers));

        if (empty($normalizedTo) || empty($message)) {
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
            // Detect if message contains non-ASCII characters to set language standard correctly
            $isUnicode = (bool) preg_match('/[^\x20-\x7E\r\n\t]/', $message);

            $payload = [
                'to'         => $normalizedTo,
                'message'    => $message,
                'client_app' => $clientApp,
                'reference'  => 'sms_' . uniqid(),
                'language'   => $isUnicode ? 'Unicode' : 'English',
            ];

            if (!empty($sender)) {
                $payload['sender'] = $sender;
            }

            // Strictly follow send_sms.md headers spec by forcing application/json Content-Type
            $response = Http::asJson()->withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'X-API-KEY'    => $apiKey,
            ])->post($baseUrl . '/api/v1/send', $payload);

            if ($response->successful()) {
                Log::info('SmsService: OK to [' . $normalizedTo . '], id=' . ($response->json('id') ?? 'n/a'));
                return true;
            }

            Log::error('SmsService: failed to [' . $normalizedTo . ']. HTTP ' . $response->status() . ': ' . $response->body());
            return false;

        } catch (\Throwable $e) {
            Log::error('SmsService: exception - ' . $e->getMessage());
            return false;
        }
    }
}