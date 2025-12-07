@extends('layouts.app')

@section('content')
<div class="bg-light py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">{{ __('Our Products') }}</h1>
        <p class="lead text-muted">{{ __('Discover our amazing collection of products') }}</p>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>{{ __('Filters') }}</h5>
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#filter-content" aria-expanded="false" aria-controls="filter-content">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
                <div class="card-body collapse d-lg-block" id="filter-content">
                    <form action="{{ route('products.index') }}" method="GET" id="sidebar-filter-form">
                        <div class="mb-3">
                            <label for="search_sidebar" class="form-label fw-bold">{{ __('Search') }}</label>
                            <input type="text" name="search" id="search_sidebar" class="form-control" value="{{ request('search') }}" placeholder="Product name...">
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label fw-bold">{{ __('Category') }}</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Price Range') }}</label>
                            <div class="d-flex align-items-center">
                                <input type="number" name="min_price" class="form-control" value="{{ request('min_price') }}" placeholder="Min">
                                <span class="mx-2">-</span>
                                <input type="number" name="max_price" class="form-control" value="{{ request('max_price') }}" placeholder="Max">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="sort_by" class="form-label fw-bold">{{ __('Sort By') }}</label>
                            <select name="sort_by" id="sort_by" class="form-select">
                                <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>{{ __('Latest') }}</option>
                                <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                                <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>{{ __('Name: A-Z') }}</option>
                                <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>{{ __('Name: Z-A') }}</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> {{ __('Apply Filters') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div id="product-list-container">
                @include('products._product_list')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('sidebar-filter-form');
        const productListContainer = document.getElementById('product-list-container');

        function updateProducts() {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData).toString();
            const url = `{{ route('products.index') }}?${params}`;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                productListContainer.innerHTML = html;
                window.history.pushState({path:url}, '', url);
            });
        }

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateProducts();
        });

        filterForm.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('change', updateProducts);
        });

        productListContainer.addEventListener('click', function(e) {
            if (e.target.matches('.pagination a')) {
                e.preventDefault();
                const url = e.target.href;
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    productListContainer.innerHTML = html;
                    window.history.pushState({path:url}, '', url);
                });
            }
        });

        function addToCart(productId, quantity) {
            fetch('/cart/add', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            }).then(response => response.json()).then(data => {
                if(data.success) {
                    alert('Product added to cart!');
                    updateCartCount();
                }
            });
        }
        window.addToCart = addToCart;

        function updateCartCount() {
            fetch('/cart/summary')
                .then(response => response.json())
                .then(data => {
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cart_count;
                    }
                });
        }
    });
</script>
@endpush