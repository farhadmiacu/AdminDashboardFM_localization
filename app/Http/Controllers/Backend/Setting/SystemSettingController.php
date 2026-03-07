<?php

namespace App\Http\Controllers\Backend\Setting;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class SystemSettingController extends Controller
{
    public function edit()
    {
        $setting = SystemSetting::first();
        return view('backend.layouts.settings.system_settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'tag_line' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'company_address' => 'nullable|string',
            'copyright_text' => 'nullable|string|max:255',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:5120',
            'favicon' => 'nullable|image|mimes:png,jpg,jpeg,svg,ico|max:5120',
        ]);

        $setting = SystemSetting::first();
        if (!$setting) {
            $setting = new SystemSetting();
            $setting->id = 1; // manually assign
        }

        $directory = 'uploads/system-settings-images/';

        // Ensure directory exists
        if (!File::exists(public_path($directory))) {
            File::makeDirectory(public_path($directory), 0775, true);
        }

        // ---------- Remove Images if requested ----------
        if ($request->input('remove_logo') && $setting->logo && file_exists(public_path($setting->logo))) {
            unlink(public_path($setting->logo));
            $setting->logo = null;
        }
        if ($request->input('remove_favicon') && $setting->favicon && file_exists(public_path($setting->favicon))) {
            unlink(public_path($setting->favicon));
            $setting->favicon = null;
        }

        // Logo
        if ($request->file('logo')) {
            if ($setting->logo && file_exists(public_path($setting->logo))) {
                unlink(public_path($setting->logo));
            }
            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $resizedLogo = Image::make($logo)->resize(187, 35);
            $resizedLogo->save(public_path($directory . $logoName));
            $setting->logo = $directory . $logoName;
        }


        // Favicon
        if ($request->file('favicon')) {
            if ($setting->favicon && file_exists(public_path($setting->favicon))) {
                unlink(public_path($setting->favicon));
            }
            $favicon = $request->file('favicon');
            $faviconName = 'favicon_' . time() . '_' . uniqid() . '.' . $favicon->getClientOriginalExtension();
            $resizedFavicon = Image::make($favicon)->resize(128, 128);
            $resizedFavicon->save(public_path($directory . $faviconName));
            $setting->favicon = $directory . $faviconName;
        }

        // Other fields
        $setting->company_name = $request->company_name;
        $setting->tag_line = $request->tag_line;
        $setting->phone_number = $request->phone_number;
        $setting->whatsapp_number = $request->whatsapp_number;
        $setting->email = $request->email;
        $setting->company_address = $request->company_address;
        $setting->copyright_text = $request->copyright_text;
        $setting->tax_percentage = $request->tax_percentage;

        $setting->save();

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
