<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Handle file uploads separately
        $fileFields = ['logo_path', 'favicon_path', 'hero_image_path', 'rooms_hero_image_path'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('img/branding'), $filename);
                Setting::updateOrCreate(['key' => $field], ['value' => 'img/branding/' . $filename]);
            }
        }

        // If logo_path was uploaded but favicon_path wasn't, sync favicon_path to the new logo
        if ($request->hasFile('logo_path') && !$request->hasFile('favicon_path')) {
            $currentLogo = Setting::where('key', 'logo_path')->value('value');
            if ($currentLogo) {
                Setting::updateOrCreate(['key' => 'favicon_path'], ['value' => $currentLogo]);
            }
        }

        // Handle all other (non-file) fields
        $data = $request->except(array_merge(['_token', '_method'], $fileFields));

        if (isset($data['location_map_iframe'])) {
            $data['location_map_iframe'] = \App\Helpers\Helper::cleanEmbedUrl($data['location_map_iframe'], 'map');
        }
        if (isset($data['location_youtube_video'])) {
            $data['location_youtube_video'] = \App\Helpers\Helper::cleanEmbedUrl($data['location_youtube_video'], 'youtube');
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        // Bust the cached global_settings for this request cycle
        $allSettings = Setting::all()->pluck('value', 'key')->toArray();
        if (!empty($allSettings['location_map_iframe'])) {
            $allSettings['location_map_iframe'] = \App\Helpers\Helper::cleanEmbedUrl($allSettings['location_map_iframe'], 'map');
        }
        if (!empty($allSettings['location_youtube_video'])) {
            $allSettings['location_youtube_video'] = \App\Helpers\Helper::cleanEmbedUrl($allSettings['location_youtube_video'], 'youtube');
        }

        \Illuminate\Support\Facades\View::share(
            'global_settings',
            $allSettings
        );

        self::regenerateManifest($allSettings);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public static function regenerateManifest($settings = null)
    {
        if (!$settings) {
            $settings = Setting::all()->pluck('value', 'key')->toArray();
        }

        $iconPath = !empty($settings['favicon_path']) ? $settings['favicon_path'] : (!empty($settings['logo_path']) ? $settings['logo_path'] : 'img/logo/sip.png');
        $iconUrl = '/' . ltrim($iconPath, '/');
        $ext = strtolower(pathinfo($iconUrl, PATHINFO_EXTENSION));
        $iconMime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : ($ext === 'svg' ? 'image/svg+xml' : 'image/png');

        $hotelName = $settings['hotel_name'] ?? 'Bella Vista Lodge';
        $shortName = strlen($hotelName) > 15 ? 'BV Lodge' : $hotelName;
        $primaryColor = $settings['primary_color'] ?? '#0f172a';
        $accentColor = $settings['accent_color'] ?? '#3b82f6';

        $manifestData = [
            "name" => $hotelName,
            "short_name" => $shortName,
            "description" => $settings['hotel_tagline'] ?? ($hotelName . " Management System"),
            "start_url" => "/dashboard",
            "scope" => "/",
            "display" => "standalone",
            "orientation" => "portrait-primary",
            "background_color" => $primaryColor,
            "theme_color" => $accentColor,
            "lang" => "en",
            "categories" => ["business", "productivity", "travel"],
            "icons" => [
                [
                    "src" => $iconUrl,
                    "sizes" => "any",
                    "type" => $iconMime,
                    "purpose" => "any maskable"
                ],
                [
                    "src" => $iconUrl,
                    "sizes" => "192x192",
                    "type" => $iconMime,
                    "purpose" => "any maskable"
                ],
                [
                    "src" => $iconUrl,
                    "sizes" => "512x512",
                    "type" => $iconMime,
                    "purpose" => "any maskable"
                ]
            ],
            "shortcuts" => [
                [
                    "name" => "Dashboard",
                    "short_name" => "Dashboard",
                    "description" => "View hotel dashboard",
                    "url" => "/dashboard",
                    "icons" => [["src" => $iconUrl, "sizes" => "any", "type" => $iconMime]]
                ],
                [
                    "name" => "New Reservation",
                    "short_name" => "Reserve",
                    "description" => "Create a new reservation",
                    "url" => "/transaction/reservation/create-identity",
                    "icons" => [["src" => $iconUrl, "sizes" => "any", "type" => $iconMime]]
                ]
            ],
            "screenshots" => [],
            "related_applications" => [],
            "prefer_related_applications" => false
        ];

        file_put_contents(public_path('manifest.json'), json_encode($manifestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
