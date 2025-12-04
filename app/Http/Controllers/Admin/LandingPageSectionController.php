<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPageSection;
use App\Models\LandingPageElement;
use App\Services\LandingPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingPageSectionController extends Controller
{
    protected $landingPageService;

    public function __construct(LandingPageService $landingPageService)
    {
        $this->landingPageService = $landingPageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = LandingPageSection::orderBy('position')->get();
        return view('admin.landing-page.sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.landing-page.sections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'section_type' => 'required|string|in:hero,features,products,cta,newsletter,about,contact,footer',
            'position' => 'required|integer',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $section = LandingPageSection::create($data);

        // Clear the cache to refresh the landing page
        $this->landingPageService->clearCache();

        return redirect()->route('admin.landing-page-sections.index')->with('success', 'Landing page section created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $section = LandingPageSection::with('elements')->findOrFail($id);
        return view('admin.landing-page.sections.show', compact('section'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $section = LandingPageSection::findOrFail($id);
        return view('admin.landing-page.sections.edit', compact('section'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $section = LandingPageSection::findOrFail($id);

        // Prepare data for validation, handling checkbox properly
        $requestData = $request->all();
        $requestData['is_active'] = $request->has('is_active');

        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'section_type' => 'required|string|in:hero,features,products,cta,newsletter,about,contact,footer',
            'position' => 'required|integer',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        // Set is_active based on checkbox presence
        $data['is_active'] = $request->has('is_active');

        // Debug: Log the data being updated
        \Log::info('Updating section with data:', $data);

        $section->update($data);

        // Debug: Log the updated section
        \Log::info('Updated section:', $section->toArray());

        // Clear the cache to refresh the landing page
        $this->landingPageService->clearCache();

        return redirect()->route('admin.landing-page-sections.index')->with('success', 'Landing page section updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $section = LandingPageSection::findOrFail($id);
        $section->delete();

        // Clear the cache to refresh the landing page
        app('App\Services\LandingPageService')->clearCache();

        return redirect()->route('admin.landing-page-sections.index')->with('success', 'Landing page section deleted successfully.');
    }

    /**
     * Show the landing page builder interface
     */
    public function builder()
    {
        $sections = LandingPageSection::with('elements')->orderBy('position')->get();
        return view('admin.landing-page.builder', compact('sections'));
    }

    /**
     * Save the landing page builder layout
     */
    public function saveBuilder(Request $request)
    {
        $sectionData = $request->input('sections', []);

        // Update section positions
        foreach ($sectionData as $index => $sectionInfo) {
            if (isset($sectionInfo['id'])) {
                $section = LandingPageSection::find($sectionInfo['id']);
                if ($section) {
                    $section->update(['position' => $index]);
                }
            } else {
                // Create new section
                LandingPageSection::create([
                    'name' => $sectionInfo['name'] ?? 'New Section',
                    'title' => $sectionInfo['title'] ?? null,
                    'content' => $sectionInfo['content'] ?? null,
                    'section_type' => $sectionInfo['type'] ?? 'hero',
                    'position' => $index,
                    'is_active' => true,
                ]);
            }
        }

        // Clear the cache to refresh the landing page
        app('App\Services\LandingPageService')->clearCache();

        return response()->json(['success' => true]);
    }

    /**
     * Load the landing page builder data
     */
    public function loadBuilder()
    {
        $sections = LandingPageSection::with('elements')->orderBy('position')->get();

        return response()->json([
            'sections' => $sections,
        ]);
    }

    /**
     * Sort sections by position
     */
    public function sort(Request $request)
    {
        $sectionIds = $request->input('sections', []);

        foreach ($sectionIds as $index => $sectionId) {
            LandingPageSection::where('id', $sectionId)->update(['position' => $index]);
        }

        // Clear the cache to refresh the landing page
        app('App\Services\LandingPageService')->clearCache();

        return response()->json(['success' => true]);
    }
}
