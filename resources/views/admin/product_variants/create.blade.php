@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Product Variant</h1>

    <form action="{{ route('admin.product-variants.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Product</label>
            <select name="product_id" class="form-control" required>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>SKU</label>
            <input type="text" name="sku" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Price</label>
            <input type="text" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Stock Quantity</label>
            <input type="number" name="stock_quantity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image_path" class="form-control">
        </div>

        <div class="form-check mb-3">
            <input type="hidden" name="is_enabled" value="0">
            <input type="checkbox" name="is_enabled" class="form-check-input" id="is_enabled" value="1" checked>
            <label class="form-check-label" for="is_enabled">Enabled</label>
        </div>

        <button type="submit" class="btn btn-success">Add Variant</button>
    </form>
</div>
@endsection
