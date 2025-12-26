@extends('layouts.app') <!-- use your layout -->

@section('content')
<div class="container">
    <h1>Product Variants</h1>
    <a href="{{ route('admin.product-variants.create') }}" class="btn btn-primary mb-3">Add Variant</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Enabled</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $variant)
            <tr>
                <td>{{ $variant->id }}</td>
                <td>{{ $variant->product->name }}</td>
                <td>{{ $variant->sku }}</td>
                <td>{{ $variant->price }}</td>
                <td>{{ $variant->stock_quantity }}</td>
                <td>
                    @if($variant->image_path)
                        <img src="{{ asset('storage/'.$variant->image_path) }}" alt="Image" width="50">
                    @endif
                </td>
                <td>{{ $variant->is_enabled ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('admin.product-variants.edit', $variant) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.product-variants.destroy', $variant) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
