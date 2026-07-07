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

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
