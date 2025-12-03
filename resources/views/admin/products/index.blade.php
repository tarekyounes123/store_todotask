@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-0">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Products') }}
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">{{ __('Add New Product') }}</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Image') }}</th>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Category') }}</th>
                                    <th scope="col">{{ __('Selling Price') }}</th>
                                    <th scope="col">{{ __('Buy Price') }}</th>
                                    <th scope="col">{{ __('Profit') }}</th>
                                    <th scope="col">{{ __('Margin') }}</th>
                                    <th scope="col">{{ __('Stock') }}</th>
                                    <th scope="col">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>
                                            @php
                                                $imagePath = $product->images->first()?->image_path;
                                            @endphp
                                            @if ($imagePath)
                                                <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category?->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                        <td>${{ $product->buy_price ? number_format($product->buy_price, 2) : 'N/A' }}</td>
                                        <td>
                                            ${{ $product->buy_price ? number_format($product->getProfitPerUnitAttribute(), 2) : 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $product->buy_price ? number_format($product->getProfitMarginAttribute(), 2) : 'N/A' }}%
                                        </td>
                                        <td>
                                            <span class="{{ $product->isInStock() ? 'text-success' : 'text-danger' }}">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-info">{{ __('Edit') }}</a>
                                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this product?') }}')">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">{{ __('No products found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection