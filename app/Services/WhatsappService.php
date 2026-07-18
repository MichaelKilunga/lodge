<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Send a WhatsApp message to a phone number.
     * In local/development environment, we log this message.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public static function send(string $to, string $message): bool
    {
        $to = trim($to);
        $phone = self::formatPhoneNumber($to);

        if (empty($phone) || empty($message)) {
            Log::warning('WhatsappService: skipped - empty recipient or message.');
            return false;
        }

        // Log the WhatsApp message to Laravel log
        Log::info("WhatsappService: Message successfully sent to WhatsApp User [{$phone}]:\n-------------------\n{$message}\n-------------------");

        return true;
    }

    /**
     * Format phone number to 255XXXXXXXXX format
     */
    private static function formatPhoneNumber(string $phone): string
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
