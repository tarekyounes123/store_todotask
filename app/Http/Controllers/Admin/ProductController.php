<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Products\Attribute; // Import the Attribute model
use App\Http\Requests\ProductRequest; // Import the ProductRequest
use App\Services\ProductVariantService; // Import the ProductVariantService
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Added Str facade

class ProductController extends Controller
{
    protected $productVariantService;

    public function __construct(ProductVariantService $productVariantService)
    {
        $this->productVariantService = $productVariantService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category', 'images')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $attributes = Attribute::with('terms')->get(); // Fetch all global attributes
        return view('admin.products.create', compact('categories', 'attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request) // Use ProductRequest for validation
    {
        $validatedData = $request->validated();

        $product = Product::create([
            'name' => $validatedData['name'],
            'slug' => Str::slug($validatedData['name']),
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'buy_price' => $validatedData['buy_price'],
            'stock_quantity' => $validatedData['stock_quantity'],
            'category_id' => $validatedData['category_id'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageInfo = getimagesize($image->getRealPath());
                if (!$imageInfo) {
                    return redirect()->back()->withErrors(['images' => 'Invalid image file.'])->withInput();
                }

                $extension = strtolower($image->getClientOriginalExtension());
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                if (!in_array($extension, $allowedExtensions)) {
                    return redirect()->back()->withErrors(['images' => 'Invalid image file type.'])->withInput();
                }

                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        // Handle product attributes and variants
        if (isset($validatedData['attributes']) || isset($validatedData['variants'])) {
            $this->productVariantService->createOrUpdateVariants($product, $validatedData);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('admin.products.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $attributes = Attribute::with('terms')->get(); // Fetch all global attributes
        // Load existing product attributes and variants for the form
        $product->load(['attributes.attribute', 'attributes.terms.attributeTerm', 'variants.terms.attribute']);

        return view('admin.products.edit', compact('product', 'categories', 'attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product) // Use ProductRequest for validation
    {
        $validatedData = $request->validated();

        $product->update([
            'name' => $validatedData['name'],
            'slug' => Str::slug($validatedData['name']),
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'buy_price' => $validatedData['buy_price'],
            'stock_quantity' => $validatedData['stock_quantity'],
            'category_id' => $validatedData['category_id'],
        ]);

        // Handle image deletions
        if ($request->has('delete_images')) {
            foreach ($request->input('delete_images') as $imageId) {
                $image = $product->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageInfo = getimagesize($image->getRealPath());
                if (!$imageInfo) {
                    return redirect()->back()->withErrors(['images' => 'Invalid image file.'])->withInput();
                }

                $extension = strtolower($image->getClientOriginalExtension());
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                if (!in_array($extension, $allowedExtensions)) {
                    return redirect()->back()->withErrors(['images' => 'Invalid image file type.'])->withInput();
                }

                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        // Handle product attributes and variants
        if (isset($validatedData['attributes']) || isset($validatedData['variants'])) {
            $this->productVariantService->createOrUpdateVariants($product, $validatedData);
        } else {
            // If no attributes/variants are submitted, clear them out
            $product->attributes()->delete();
            $product->variants()->delete();
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete associated images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
