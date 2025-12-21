<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Products\Attribute;
use App\Models\Products\AttributeTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    /**
     * Display a listing of the attributes.
     */
    public function index()
    {
        $attributes = Attribute::with('terms')->get();
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new attribute.
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created attribute in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
        ]);

        Attribute::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute created successfully.');
    }

    /**
     * Show the form for editing the specified attribute.
     */
    public function edit(Attribute $attribute)
    {
        return view('admin.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified attribute in storage.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id,
        ]);

        $attribute->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully.');
    }

    /**
     * Remove the specified attribute from storage.
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted successfully.');
    }

    /**
     * Show the form for creating a new attribute term.
     */
    public function createTerm(Attribute $attribute)
    {
        return view('admin.attributes.terms.create', compact('attribute'));
    }

    /**
     * Store a newly created attribute term in storage.
     */
    public function storeTerm(Request $request, Attribute $attribute)
    {
        $request->validate([
            'value' => [
                'required',
                'string',
                'max:255',
                'unique:attribute_terms,value,NULL,id,attribute_id,' . $attribute->id,
            ],
        ]);

        $attribute->terms()->create([
            'value' => $request->value,
            'slug' => Str::slug($request->value),
        ]);

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'Attribute term created successfully.');
    }

    /**
     * Show the form for editing the specified attribute term.
     */
    public function editTerm(Attribute $attribute, AttributeTerm $term)
    {
        return view('admin.attributes.terms.edit', compact('attribute', 'term'));
    }

    /**
     * Update the specified attribute term in storage.
     */
    public function updateTerm(Request $request, Attribute $attribute, AttributeTerm $term)
    {
        $request->validate([
            'value' => [
                'required',
                'string',
                'max:255',
                'unique:attribute_terms,value,' . $term->id . ',id,attribute_id,' . $attribute->id,
            ],
        ]);

        $term->update([
            'value' => $request->value,
            'slug' => Str::slug($request->value),
        ]);

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'Attribute term updated successfully.');
    }

    /**
     * Remove the specified attribute term from storage.
     */
    public function destroyTerm(Attribute $attribute, AttributeTerm $term)
    {
        $term->delete();
        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'Attribute term deleted successfully.');
    }
}