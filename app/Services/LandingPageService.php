<?php

namespace App\Services;

use App\Models\LandingPageSection;
use Illuminate\Support\Facades\Cache;

class LandingPageService
{
    public function getActiveSections()
    {
        return Cache::remember('landing_page_sections', 60, function () { // Cache for 1 minute instead of 1 hour
            \Log::info('LandingPageService: Loading sections from database');
            $sections = LandingPageSection::where('is_active', true)
                ->with(['elements' => function ($query) {
                    $query->where('is_active', true)
                          ->orderBy('position');
                }])
                ->orderBy('position')
                ->get();
            \Log::info('LandingPageService: Loaded ' . $sections->count() . ' sections');
            return $sections;
        });
    }

    public function getFooterContent()
    {
        // Try to get the dynamic footer section first
        $footerSection = LandingPageSection::where('section_type', 'footer')
            ->where('is_active', true)
            ->with(['elements' => function ($query) {
                $query->where('is_active', true)
                      ->orderBy('position');
            }])
            ->first();

        if ($footerSection) {
            return [
                'type' => 'dynamic',
                'section' => $footerSection
            ];
        }

        // If no dynamic footer, return null to indicate using static footer
        return [
            'type' => 'static',
            'section' => null
        ];
    }

    public function getSectionByType($sectionType)
    {
        return LandingPageSection::where('is_active', true)
            ->where('section_type', $sectionType)
            ->with(['elements' => function ($query) {
                $query->where('is_active', true)
                      ->orderBy('position');
            }])
            ->first();
    }

    public function clearCache()
    {
        Cache::forget('landing_page_sections');
    }
}