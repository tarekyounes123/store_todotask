<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        return view('admin.site-settings.edit', compact('footerSetting'));
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $footerSetting = SiteSetting::where('setting_key', SiteSetting::FOOTER_SETTINGS_KEY)->firstOrFail();
        $footerSetting->setting_value = $request->all();
        $footerSetting->save();

        return redirect()->back()->with('success', 'Footer settings updated successfully!');
    }
}
