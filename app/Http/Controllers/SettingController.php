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
        $fileFields = ['logo_path', 'favicon_path', 'hero_image_path'];

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

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        // Bust the cached global_settings for this request cycle
        \Illuminate\Support\Facades\View::share(
            'global_settings',
            Setting::all()->pluck('value', 'key')->toArray()
        );

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
