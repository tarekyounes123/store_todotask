<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    /**
     * Show the form for editing site settings.
     */
    public function edit()
    {
        // Get the footer settings or create default if not exists
        $footerSetting = SiteSetting::where('setting_key', SiteSetting::FOOTER_SETTINGS_KEY)->first();

        if (!$footerSetting) {
            // Create default footer settings
            $footerSetting = SiteSetting::create([
                'setting_key' => SiteSetting::FOOTER_SETTINGS_KEY,
                'setting_value' => [
                    'company_info' => [
                        'name' => config('app.name', 'Laravel'),
                        'description' => 'Your premier destination for quality products at unbeatable prices. We\'re committed to providing excellent customer service and the best shopping experience.',
                        'address' => '123 Commerce Street, City, State 12345',
                        'phone' => '+1 (555) 123-4567',
                        'email' => 'support@example.com'
                    ],
                    'social_links' => [
                        ['name' => 'Facebook', 'url' => '#', 'icon' => 'fab fa-facebook-f'],
                        ['name' => 'Twitter', 'url' => '#', 'icon' => 'fab fa-twitter'],
                        ['name' => 'Instagram', 'url' => '#', 'icon' => 'fab fa-instagram'],
                        ['name' => 'YouTube', 'url' => '#', 'icon' => 'fab fa-youtube']
                    ],
                    'shop_links' => [
                        ['name' => 'All Products', 'url' => '/products'],
                        ['name' => 'Featured Items', 'url' => '#'],
                        ['name' => 'New Arrivals', 'url' => '#'],
                        ['name' => 'Best Sellers', 'url' => '#'],
                        ['name' => 'Sale', 'url' => '#']
                    ],
                    'company_links' => [
                        ['name' => 'About Us', 'url' => '#'],
                        ['name' => 'Contact', 'url' => '#'],
                        ['name' => 'Careers', 'url' => '#'],
                        ['name' => 'Blog', 'url' => '#'],
                        ['name' => 'Press', 'url' => '#']
                    ]
                ],
                'description' => 'Footer content settings'
            ]);
        }

        // Get the logo settings or create default if not exists
        $logoSetting = SiteSetting::where('setting_key', SiteSetting::LOGO_SETTINGS_KEY)->first();

        if (!$logoSetting) {
            // Create default logo settings
            $logoSetting = SiteSetting::create([
                'setting_key' => SiteSetting::LOGO_SETTINGS_KEY,
                'setting_value' => [
                    'favicon' => null,
                    'logo' => null,
                ],
                'description' => 'Logo and favicon settings'
            ]);
        }

        // Get the title settings or create default if not exists
        $titleSetting = SiteSetting::where('setting_key', SiteSetting::TITLE_SETTINGS_KEY)->first();

        if (!$titleSetting) {
            // Create default title settings
            $titleSetting = SiteSetting::create([
                'setting_key' => SiteSetting::TITLE_SETTINGS_KEY,
                'setting_value' => [
                    'app_name' => config('app.name', 'ToDoTask'),
                ],
                'description' => 'Website title settings'
            ]);
        }

        return view('admin.site-settings.edit', compact('footerSetting', 'logoSetting', 'titleSetting'));
    }

    /**
     * Update site settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_info.name' => 'sometimes|string|max:255',
            'company_info.description' => 'sometimes|string',
            'company_info.address' => 'sometimes|string',
            'company_info.phone' => 'sometimes|string',
            'company_info.email' => 'sometimes|email',
            'social_links' => 'sometimes|array',
            'shop_links' => 'sometimes|array',
            'company_links' => 'sometimes|array',
            'favicon' => 'nullable|mimes:png,jpg,jpeg,gif,bmp,svg,ico|max:2048',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,gif,bmp,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update footer settings
        $footerSetting = SiteSetting::where('setting_key', SiteSetting::FOOTER_SETTINGS_KEY)->firstOrFail();
        $footerSetting->setting_value = [
            'company_info' => $request->input('company_info', []),
            'social_links' => $request->input('social_links', []),
            'shop_links' => $request->input('shop_links', []),
            'company_links' => $request->input('company_links', []),
        ];
        $footerSetting->save();

        // Handle logo and favicon uploads
        $logoSetting = SiteSetting::where('setting_key', SiteSetting::LOGO_SETTINGS_KEY)->first();
        if (!$logoSetting) {
            $logoSetting = SiteSetting::create([
                'setting_key' => SiteSetting::LOGO_SETTINGS_KEY,
                'setting_value' => [
                    'favicon' => null,
                    'logo' => null,
                ],
                'description' => 'Logo and favicon settings'
            ]);
        }

        $logoSettingsValue = $logoSetting->setting_value;

        if ($request->hasFile('favicon')) {
            $faviconFile = $request->file('favicon');

            // Check if the file is an ICO file or an image file that needs conversion
            $extension = strtolower($faviconFile->getClientOriginalExtension());

            // Define valid image extensions that can be used as favicon
            $validImageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'svg'];
            $isValidExtension = $extension === 'ico' || in_array($extension, $validImageExtensions);

            if (!$isValidExtension) {
                return redirect()->back()->withErrors(['favicon' => 'Invalid file type. Please upload a valid image or ICO file.']);
            }

            // Get the current favicon path
            $oldFaviconPath = $logoSettingsValue['favicon'] ?? null;

            // Move the uploaded file to the public directory as favicon.ico
            $faviconFile->move(public_path(), 'favicon.ico');

            // Update the favicon path in settings to indicate it's using the default public file
            $logoSettingsValue['favicon'] = 'favicon.ico';

            // If there was an old favicon file in storage, delete it
            if ($oldFaviconPath && $oldFaviconPath !== 'favicon.ico') {
                Storage::disk('public')->delete($oldFaviconPath);
            }
        }

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($logoSettingsValue['logo']) {
                Storage::disk('public')->delete($logoSettingsValue['logo']);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
            $logoSettingsValue['logo'] = $logoPath;
        }

        $logoSetting->setting_value = $logoSettingsValue;
        $logoSetting->save();

        // Handle site title settings
        $titleSetting = SiteSetting::where('setting_key', SiteSetting::TITLE_SETTINGS_KEY)->first();
        if (!$titleSetting) {
            $titleSetting = SiteSetting::create([
                'setting_key' => SiteSetting::TITLE_SETTINGS_KEY,
                'setting_value' => [
                    'app_name' => config('app.name', 'ToDoTask'),
                ],
                'description' => 'Website title settings'
            ]);
        }

        $titleSettingsValue = $titleSetting->setting_value;

        if ($request->filled('app_name')) {
            $titleSettingsValue['app_name'] = $request->input('app_name');
        }

        $titleSetting->setting_value = $titleSettingsValue;
        $titleSetting->save();

        return redirect()->back()->with('success', 'Site settings updated successfully!');
    }
}
