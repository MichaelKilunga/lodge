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

        // Normalize phone number(s) using the exact proven formatPhoneNumber logic from PMS workspace
        $numbers = array_map([self::class, 'formatPhoneNumber'], explode(',', $to));
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
            $payload = [
                'to'         => $normalizedTo,
                'message'    => $message,
                'client_app' => $clientApp ?: 'HMS',
                'reference'  => 'sms_' . uniqid(),
            ];

            if (!empty($sender)) {
                $payload['sender']    = $sender;
                $payload['sender_id'] = $sender; // CRITICAL: Must be passed alongside sender to avoid Undefined variable $params error
            }

            // Explicitly enforce application/json headers
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'X-API-KEY'    => $apiKey,
            ])->post($baseUrl . '/api/v1/send', $payload);

            $charCount = mb_strlen($message);
            $pageCount = (int) ceil($charCount / 160);

            \App\Models\SmsLog::create([
                'recipient'       => $normalizedTo,
                'message'         => $message,
                'status'          => $response->successful() ? 'Success' : 'Failed',
                'response'        => $response->body(),
                'character_count' => $charCount,
                'page_count'      => $pageCount,
            ]);

            if ($response->successful()) {
                Log::info('SmsService: OK to [' . $normalizedTo . '], id=' . ($response->json('id') ?? 'n/a'));
                return true;
            }

            Log::error('SmsService: failed to [' . $normalizedTo . ']. HTTP ' . $response->status() . ': ' . $response->body());
            return false;

        } catch (\Throwable $e) {
            Log::error('SmsService: exception - ' . $e->getMessage());

            try {
                \App\Models\SmsLog::create([
                    'recipient'       => $normalizedTo,
                    'message'         => $message,
                    'status'          => 'Failed',
                    'response'        => $e->getMessage(),
                    'character_count' => mb_strlen($message),
                    'page_count'      => (int) ceil(mb_strlen($message) / 160),
                ]);
            } catch (\Throwable $dbEx) {
                Log::error('SmsService: database log failed - ' . $dbEx->getMessage());
            }

            return false;
        }
    }

    /**
     * Get the current SMS credit balance from the Skypush gateway.
     *
     * @return array|null
     */
    public static function getBalance(): ?array
    {
        $baseUrl = rtrim(config('services.pushsms.url', 'https://pushsms.rehospace.com'), '/');

        $dbSettings = \App\Models\Setting::whereIn('key', ['sms_api_key', 'sms_sender_id', 'sms_client_app'])->pluck('value', 'key');
        $apiKey    = $dbSettings->get('sms_api_key') ?: config('services.pushsms.api_key', '');
        $sender    = $dbSettings->get('sms_sender_id') ?: config('services.pushsms.sender', '');
        $clientApp = $dbSettings->get('sms_client_app') ?: config('services.pushsms.client_app', 'HMS');

        if (empty($apiKey)) {
            return null;
        }

        try {
            // Identify sender/client_app to prevent platform-wide balance exposure
            $identifier = $sender ?: $clientApp;
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'X-API-KEY'    => $apiKey,
                'X-Client-App' => $identifier,
            ])->get($baseUrl . '/api/v1/balance', [
                'sender' => $identifier,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            return null;
        } catch (\Throwable $e) {
            Log::error('SmsService: getBalance exception - ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Format phone number to 255XXXXXXXXX exactly like PMS workspace formatPhoneNumber
     */
    private static function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', trim($phone));

        if (substr($phone, 0, 1) === '0') {
            $phone = '255' . substr($phone, 1);
        }

        if (substr($phone, 0, 4) === '2550') {
             $phone = '255' . substr($phone, 4);
        }

        if (strlen($phone) === 9) {
            $phone = '255' . $phone;
        }

        return $phone;
    }
}