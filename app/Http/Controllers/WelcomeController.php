<?php

namespace App\Http\Controllers;

use App\Services\LandingPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WelcomeController extends Controller
{
    protected $landingPageService;

    public function __construct(LandingPageService $landingPageService)
    {
        $this->landingPageService = $landingPageService;
    }

    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        try {
            \Log::info('WelcomeController@index called');
            $sections = $this->landingPageService->getActiveSections();
            $footerContent = $this->landingPageService->getFooterContent();

            // Get site settings for footer
            $siteSettings = \App\Models\SiteSetting::where('setting_key', \App\Models\SiteSetting::FOOTER_SETTINGS_KEY)->first();

            \Log::info('Sections loaded: ' . $sections->count());
        } catch (\Exception $e) {
            // If there's an error with the service, fall back to empty collection
            \Log::error('Error loading landing page sections: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' line: ' . $e->getLine());
            $sections = collect(); // Empty collection as fallback
            $footerContent = null;
            $siteSettings = null;
        }

        return view('welcome', compact('sections', 'footerContent', 'siteSettings'));
    }
}
