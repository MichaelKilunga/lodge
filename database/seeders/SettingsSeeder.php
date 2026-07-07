<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // General
            'hotel_name'          => 'Bella Vista Lodge',
            'hotel_tagline'       => 'Experience unparalleled luxury, comfort, and exceptional service in the heart of the city.',
            'hotel_address'       => '123 Luxury Avenue, Paradise City',
            'contact_email'       => 'hello@bellavistalodge.com',
            'contact_phone'       => '+1 234 567 890',
            'whatsapp_number'     => '',
            'owner_email'         => '',

            // Branding
            'logo_path'           => '',       // path under public/
            'favicon_path'        => '',
            'hero_image_path'     => '',       // optional override for hero bg
            'primary_color'       => '#0f172a',
            'accent_color'        => '#d4af37',

            // Social Media
            'social_facebook'     => '#',
            'social_instagram'    => '#',
            'social_twitter'      => '#',
            'social_tiktok'       => '',
            'social_youtube'      => '',
            'social_linkedin'     => '',

            // Footer
            'footer_tagline'      => 'Experience unparalleled luxury, comfort, and exceptional service. Your perfect getaway awaits.',
            'footer_copyright'    => '',       // blank = auto generated from hotel_name + year

            // Booking / Operational
            'receipt_verify_time'   => '1-2 hours',
            'booking_email_note'    => '',       // extra note added to booking emails

            // SMS Notifications
            'admin_sms_recipient'   => '',       // phone number(s) to receive admin SMS alerts (comma-separated)
            'sms_api_key'           => '',       // Skypush API key (overrides PUSHSMS_API_KEY env var)
            'sms_sender_id'         => '',       // Sender ID shown on the SMS (max 11 chars, e.g. BELLA)
            'sms_client_app'        => 'HMS',    // Client app identifier sent to the SMS gateway
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
