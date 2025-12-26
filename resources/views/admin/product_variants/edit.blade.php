@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Product Variant</h1>

    <form action="{{ route('admin.product-variants.update', $productVariant) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Product</label>
            <select name="product_id" class="form-control" required>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                        {{ $productVariant->product_id == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>SKU</label>
            <input type="text" name="sku" class="form-control" value="{{ $productVariant->sku }}" required>
        </div>

        <div class="mb-3">
            <label>Price</label>
            <input type="text" name="price" class="form-control" value="{{ $productVariant->price }}" required>
        </div>

        <div class="mb-3">
            <label>Stock Quantity</label>
            <input type="number" name="stock_quantity" class="form-control" value="{{ $productVariant->stock_quantity }}" required>
        </div>

        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image_path" class="form-control">
            @if($productVariant->image_path)
                <img src="{{ asset('storage/'.$productVariant->image_path) }}" alt="Image" width="50" class="mt-2">
            @endif
        </div>

        <div class="form-check mb-3">
            <input type="hidden" name="is_enabled" value="0"> <!-- Hidden field to ensure 'is_enabled' is sent as 0 if checkbox is unchecked -->
            <input type="checkbox" name="is_enabled" class="form-check-input" id="is_enabled" value="1" {{ $productVariant->is_enabled ? 'checked' : '' }}>
            <label class="form-check-label" for="is_enabled">Enabled</label>
        </div>


        <button type="submit" class="btn btn-success">Update Variant</button>
    </form>
</div>
@endsection
