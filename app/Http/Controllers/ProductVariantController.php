<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with('product')->get();
        return view('admin.product_variants.index', compact('variants'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.product_variants.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'sku' => 'required|string|unique:product_variants,sku',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'image_path' => 'nullable|image|max:2048',
             'is_enabled' => 'required|in:0,1', // allow only 0 or 1
        ]);

        $data = $request->all();

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('product_variants', 'public');
        }

      

        ProductVariant::create($data);

        return redirect()->route('admin.product-variants.index')->with('success', 'Variant created successfully.');
    }

    public function edit(ProductVariant $productVariant)
    {
        $products = Product::all();
        return view('admin.product_variants.edit', compact('productVariant', 'products'));
    }

  public function update(Request $request, ProductVariant $productVariant)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'sku' => 'required|string|unique:product_variants,sku,' . $productVariant->id,
        'price' => 'required|numeric',
        'stock_quantity' => 'required|integer',
        'image_path' => 'nullable|image|max:2048',
        'is_enabled' => 'required|in:0,1', // allow only 0 or 1
    ]);

    $data = $request->all();

    if ($request->hasFile('image_path')) {
        if ($productVariant->image_path) {
            Storage::disk('public')->delete($productVariant->image_path);
        }
        $data['image_path'] = $request->file('image_path')->store('product_variants', 'public');
    }

    // Cast to boolean before saving
    $data['is_enabled'] = (bool) $request->input('is_enabled');

    $productVariant->update($data);

    return redirect()->route('admin.product-variants.index')->with('success', 'Variant updated successfully.');
}


    public function destroy(ProductVariant $productVariant)
    {
        if ($productVariant->image_path) {
            Storage::disk('public')->delete($productVariant->image_path);
        }

        $productVariant->delete();

        return redirect()->route('admin.product-variants.index')->with('success', 'Variant deleted successfully.');
    }
}
?>
