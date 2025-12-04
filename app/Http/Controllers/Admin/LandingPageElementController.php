<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPageElement;
use App\Models\LandingPageSection;
use App\Services\LandingPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingPageElementController extends Controller
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
        $elements = LandingPageElement::with('section')->orderBy('position')->get();
        return view('admin.landing-page.elements.index', compact('elements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = LandingPageSection::all();
        return view('admin.landing-page.elements.create', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'element_type' => 'required|string|in:text,image,button,heading,paragraph,icon',
            'content' => 'nullable|string',
            'section_id' => 'required|exists:landing_page_sections,id',
            'position' => 'required|integer',
            'is_active' => 'boolean',
            'attributes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $element = LandingPageElement::create($data);

        // Clear the cache to refresh the landing page
        $this->landingPageService->clearCache();

        return redirect()->route('admin.landing-page-elements.index')->with('success', 'Landing page element created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $element = LandingPageElement::with('section')->findOrFail($id);
        return view('admin.landing-page.elements.show', compact('element'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $element = LandingPageElement::findOrFail($id);
        $sections = LandingPageSection::all();
        return view('admin.landing-page.elements.edit', compact('element', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $element = LandingPageElement::findOrFail($id);

        // Prepare data for validation, handling checkbox properly
        $requestData = $request->all();
        $requestData['is_active'] = $request->has('is_active');

        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:255',
            'element_type' => 'required|string|in:text,image,button,heading,paragraph,icon',
            'content' => 'nullable|string',
            'section_id' => 'required|exists:landing_page_sections,id',
            'position' => 'required|integer',
            'is_active' => 'boolean',
            'attributes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        // Set is_active based on checkbox presence
        $data['is_active'] = $request->has('is_active');

        // Debug: Log the data being updated
        \Log::info('Updating element with data:', $data);

        $element->update($data);

        // Debug: Log the updated element
        \Log::info('Updated element:', $element->toArray());

        // Clear the cache to refresh the landing page
        $this->landingPageService->clearCache();

        return redirect()->route('admin.landing-page-elements.index')->with('success', 'Landing page element updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $element = LandingPageElement::findOrFail($id);
        $element->delete();

        // Clear the cache to refresh the landing page
        $this->landingPageService->clearCache();

        return redirect()->route('admin.landing-page-elements.index')->with('success', 'Landing page element deleted successfully.');
    }

    /**
     * Sort elements by position within a section
     */
    public function sort(Request $request)
    {
        $elementIds = $request->input('elements', []);

        foreach ($elementIds as $index => $elementId) {
            LandingPageElement::where('id', $elementId)->update(['position' => $index]);
        }

        // Clear the cache to refresh the landing page
        $this->landingPageService->clearCache();

        return response()->json(['success' => true]);
    }
}
