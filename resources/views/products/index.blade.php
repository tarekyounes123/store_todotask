@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="container container-custom">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <h1 class="page-title">{{ __('Our Products') }}</h1>
                <p class="lead">{{ __('Discover our amazing collection of products') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="container container-custom py-4">
    <div class="row">
        {{-- Sidebar for Filters --}}
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary-gradient text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>{{ __('Filters') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.index') }}" method="GET" id="sidebar-filter-form">
                        {{-- Search Filter --}}
                        <div class="mb-3">
                            <label for="search_sidebar" class="form-label fw-bold">{{ __('Search Products') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" id="search_sidebar" class="form-control" value="{{ request('search') }}" placeholder="{{ __('Product Name') }}">
                            </div>
                        </div>

                        {{-- Category Filter --}}
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

                        {{-- Price Range Filter --}}
                        <div class="mb-3">
                            <label for="min_price" class="form-label fw-bold">{{ __('Price Range') }}</label>
                            <div class="row">
                                <div class="col">
                                    <input type="number" name="min_price" id="min_price" class="form-control" value="{{ request('min_price') }}" placeholder="{{ __('Min') }}">
                                </div>
                                <div class="col">
                                    <input type="number" name="max_price" id="max_price" class="form-control" value="{{ request('max_price') }}" placeholder="{{ __('Max') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Sort By --}}
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

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary-gradient text-white"><i class="bi bi-funnel me-1"></i>{{ __('Apply Filters') }}</button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('Clear Filters') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Product Listing --}}
        <div class="col-md-9">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="mb-0 text-muted">
                        {{ __('Showing') }} <span id="product-count">{{ $products->total() }}</span> {{ __('products') }}
                    </p>
                </div>
                <div>
                    <div class="input-group w-auto">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="top-search-input" class="form-control me-2" placeholder="{{ __('Search products...') }}" value="{{ request('search') }}">
                    </div>
                </div>
            </div>

            <div id="product-list-container">
                @include('products._product_list')
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const topSearchInput = document.getElementById('top-search-input');
        const sidebarForm = document.getElementById('sidebar-filter-form');
        const productListContainer = document.getElementById('product-list-container');
        let debounceTimer;

        function getFilterParams(page = 1) {
            const params = new URLSearchParams();

            // Collect params from the top search bar
            if (topSearchInput.value) {
                params.set('search', topSearchInput.value);
            }

            // Collect params from the sidebar form
            const sidebarInputs = sidebarForm.elements;
            for (let i = 0; i < sidebarInputs.length; i++) {
                const input = sidebarInputs[i];
                if (input.name && input.value && (input.type !== 'checkbox' || input.checked)) {
                    // Avoid duplicating 'search' parameter if already set by top search
                    if (input.name === 'search' && params.has('search') && params.get('search') === topSearchInput.value) {
                        continue;
                    }
                    params.set(input.name, input.value);
                }
            }
            params.set('page', page);
            return params;
        }

        function updateProductList(page = 1) {
            const params = getFilterParams(page);
            const queryString = params.toString();
            const requestUrl = `{{ route('products.index') }}?${queryString}`;

            fetch(requestUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                productListContainer.innerHTML = html;
                // Update browser URL without reloading
                window.history.pushState({}, '', requestUrl);
            })
            .catch(error => {
                console.error('Error fetching products:', error);
            });
        }

        // Debounce function
        function debounce(func, delay) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(func, delay);
        }

        // Event listener for the top search input (live search)
        topSearchInput.addEventListener('input', function() {
            debounce(() => updateProductList(), 300);
        });

        // Event listeners for sidebar filter changes (apply filters on change, not just submit)
        sidebarForm.querySelectorAll('input:not([type="submit"]), select').forEach(input => {
            input.addEventListener('change', function() {
                 debounce(() => updateProductList(), 300);
            });
             // For text/number inputs in sidebar filters, also debounce input event
             if (input.type === 'text' || input.type === 'number') {
                input.addEventListener('input', function() {
                    debounce(() => updateProductList(), 300);
                });
            }
        });

        // Handle pagination links via AJAX
        productListContainer.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('.pagination a');
            if (paginationLink) {
                e.preventDefault();
                const url = new URL(paginationLink.href);
                const page = url.searchParams.get('page');
                updateProductList(page);
            }
        });

        // Handle Clear Filters button in the sidebar form
        const clearFiltersButton = sidebarForm.querySelector('a.btn-outline-secondary');
        clearFiltersButton.addEventListener('click', function(e) {
            e.preventDefault();
            // Clear all input fields in the sidebar form
            sidebarForm.querySelectorAll('input:not([type="submit"]), select').forEach(input => {
                if (input.type === 'text' || input.type === 'number') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.value = ''; // Reset select to first option
                }
            });
            // Clear the top search input
            topSearchInput.value = '';

            // Update product list with cleared filters
            debounce(() => updateProductList(), 0); // Trigger immediately
        });

        // Handle favorite button clicks
        productListContainer.addEventListener('click', function(e) {
            const favoriteButton = e.target.closest('.favorite-button');
            if (favoriteButton) {
                e.preventDefault();
                const productId = favoriteButton.dataset.productId;
                let isFavorited = favoriteButton.dataset.isFavorited === 'true';
                const url = isFavorited ? `/favorites/${productId}/remove` : `/favorites/${productId}/add`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json', // Keep JSON for these requests
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        isFavorited = !isFavorited; // Toggle state
                        favoriteButton.dataset.isFavorited = isFavorited;
                        const icon = favoriteButton.querySelector('i');
                        if (isFavorited) {
                            icon.classList.remove('bi-heart');
                            icon.classList.add('bi-heart-fill');
                            favoriteButton.title = 'Remove from Favorites';
                        } else {
                            icon.classList.remove('bi-heart-fill');
                            icon.classList.add('bi-heart');
                            favoriteButton.title = 'Add to Favorites';
                        }
                        alert(data.message);
                    } else {
                        alert('Error: ' + (data.message || 'Could not update favorite status.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating favorite status.');
                });
            }
        });
    });

    // Function to update the cart count in the navbar
    function updateCartCount() {
        fetch('/cart/summary', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = data.cart_count;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching cart count:', error);
        });
    }

    // Add product to cart
    function addToCart(productId, quantity) {
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);

                // Update cart count in header
                updateCartCount();
            } else {
                alert('Error: ' + (data.message || 'Could not add item to cart'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the item to cart.');
        });
    }
</script>
@endpush