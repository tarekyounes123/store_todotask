@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Edit Product') }}
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">{{ __('Back to Products') }}</a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" id="description" rows="5" class="form-control" required>{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">{{ __('Selling Price') }}</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="buy_price" class="form-label">{{ __('Buy Price / Unit Cost') }}</label>
                            <input type="number" name="buy_price" id="buy_price" value="{{ old('buy_price', $product->buy_price ?? 0) }}" step="0.01" class="form-control" min="0" required>
                            <div class="form-text">{{ __('Cost of the product when you purchase it') }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label">{{ __('Stock Quantity') }}</label>
                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" class="form-control" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">{{ __('Category') }}</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">{{ __('Select a Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">{{ __('New Product Images') }}</label>
                            <input type="file" name="images[]" id="images" multiple class="form-control">
                            <div class="mt-2">
                                <label class="form-label">{{ __('Existing Images') }}</label>
                                <div class="d-flex flex-wrap">
                                    @foreach ($product->images as $image)
                                        <div class="me-2 mb-2 border p-1 rounded">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $image->id }}" id="delete_image_{{ $image->id }}">
                                                <label class="form-check-label" for="delete_image_{{ $image->id }}">
                                                    {{ __('Delete') }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Update Product') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
