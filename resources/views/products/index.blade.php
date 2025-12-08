@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="bg-light py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold" style="font-family: 'Inter', sans-serif;">{{ __('Our Products') }}</h1>
        <p class="lead text-muted" style="font-family: 'Inter', sans-serif;">{{ __('Discover our amazing collection of products') }}</p>
    </div>
</div>

<div class="container-fluid px-4 py-4">
    <!-- Modern Filters Card -->
    <div class="filters-card mb-5">
        <form action="{{ route('products.index') }}" method="GET" id="top-filter-form">
            <div class="filters-grid">
                <div class="filter-field">
                    <label class="filter-label">{{ __('Search') }}</label>
                    <input type="text" name="search" class="modern-input" value="{{ request('search') }}" placeholder="Search products...">
                </div>

                <div class="filter-field">
                    <label class="filter-label">{{ __('Category') }}</label>
                    <select name="category" class="modern-select">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label class="filter-label">{{ __('Price Range') }}</label>
                    <div class="price-range-group">
                        <input type="number" name="min_price" class="modern-input" value="{{ request('min_price') }}" placeholder="Min">
                        <span class="price-divider">-</span>
                        <input type="number" name="max_price" class="modern-input" value="{{ request('max_price') }}" placeholder="Max">
                    </div>
                </div>

                <div class="filter-field">
                    <label class="filter-label">{{ __('Sort By') }}</label>
                    <select name="sort_by" class="modern-select">
                        <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>{{ __('Latest') }}</option>
                        <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                        <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                        <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>{{ __('Name: A-Z') }}</option>
                        <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>{{ __('Name: Z-A') }}</option>
                    </select>
                </div>

                <div class="filter-field apply-btn-field">
                    <button type="submit" class="modern-apply-btn">
                        <i class="bi bi-funnel me-2"></i> {{ __('Apply Filters') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Product Grid -->
    <div id="product-list-container">
        @include('products._product_list')
    </div>
</div>

<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
    }

    .filters-card {
        background: white;
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(226, 232, 240, 0.6);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
        align-items: end;
    }

    .filter-field {
        display: flex;
        flex-direction: column;
    }

    .apply-btn-field {
        margin-bottom: 0.25rem;
    }

    .filter-label {
        font-weight: 600;
        color: #334155;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .modern-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background-color: #f8fafc;
        color: #1e293b;
    }

    .modern-input:focus {
        outline: none;
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
    }

    .modern-input::placeholder {
        color: #94a3b8;
    }

    .modern-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.9rem;
        background-color: #f8fafc;
        color: #1e293b;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }

    .modern-select:focus {
        outline: none;
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
    }

    .price-range-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .price-divider {
        color: #94a3b8;
        font-weight: 600;
    }

    .modern-apply-btn {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
        padding: 0.875rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 1rem;
        width: 100%;
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
    }

    .modern-apply-btn:hover {
        background: linear-gradient(135deg, #4338ca, #6d28d9);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(79, 70, 229, 0.4);
    }

    .modern-apply-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .filters-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 767px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }

        .price-range-group {
            flex-direction: column;
            gap: 0.5rem;
        }

        .price-divider {
            align-self: center;
        }
    }
</style>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('top-filter-form');
        const productListContainer = document.getElementById('product-list-container');

        function updateProducts() {
            // Get form values manually to handle empty values properly
            const search = document.querySelector('input[name="search"]').value.trim();
            const category = document.querySelector('select[name="category"]').value;
            const minPrice = document.querySelector('input[name="min_price"]').value.trim();
            const maxPrice = document.querySelector('input[name="max_price"]').value.trim();
            const sortBy = document.querySelector('select[name="sort_by"]').value;

            // Build URL with parameters
            let url = '{{ route('products.index') }}';
            const params = new URLSearchParams();

            if (search !== '') params.append('search', search);
            if (category !== '') params.append('category', category);
            if (minPrice !== '') params.append('min_price', minPrice);
            if (maxPrice !== '') params.append('max_price', maxPrice);
            if (sortBy !== 'latest') params.append('sort_by', sortBy);

            if (params.toString().length > 0) {
                url += '?' + params.toString();
            }

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                productListContainer.innerHTML = html;
                window.history.pushState({path: url}, '', url);
            })
            .catch(error => {
                console.error('Error fetching products:', error);
            });
        }

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateProducts();
        });

        // Add event listener with a small delay to avoid too many requests
        let debounceTimer;
        filterForm.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('change', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(updateProducts, 300);
            });
            // Also trigger on input for search box to provide immediate feedback
            if (element.name === 'search') {
                element.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(updateProducts, 500);
                });
            }
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
                })
                .catch(error => {
                    console.error('Error fetching paginated products:', error);
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
            }).catch(error => {
                console.error('Error adding to cart:', error);
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
                })
                .catch(error => {
                    console.error('Error updating cart count:', error);
                });
        }

        // Handle favorite button clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.favorite-button')) {
                e.preventDefault();
                const button = e.target.closest('.favorite-button');
                const productId = button.getAttribute('data-product-id');
                const isFavorited = button.getAttribute('data-is-favorited') === 'true';

                // Determine the API endpoint based on current state
                const url = isFavorited
                    ? `/favorites/${productId}/remove`
                    : `/favorites/${productId}/add`;

                // Change button appearance immediately for better UX
                const icon = button.querySelector('i');
                if (isFavorited) {
                    // Unfavorite
                    icon.className = 'bi bi-heart';
                    button.setAttribute('data-is-favorited', 'false');
                } else {
                    // Favorite
                    icon.className = 'bi bi-heart-fill';
                    button.setAttribute('data-is-favorited', 'true');
                }

                // Send AJAX request to toggle favorite status
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        // Revert the button state if the request failed
                        const icon = button.querySelector('i');
                        if (isFavorited) {
                            icon.className = 'bi bi-heart-fill';
                            button.setAttribute('data-is-favorited', 'true');
                        } else {
                            icon.className = 'bi bi-heart';
                            button.setAttribute('data-is-favorited', 'false');
                        }
                        alert(data.message || 'Error toggling favorite status');
                    }
                })
                .catch(error => {
                    console.error('Error toggling favorite:', error);
                    // Revert the button state if there was an error
                    const icon = button.querySelector('i');
                    if (isFavorited) {
                        icon.className = 'bi bi-heart-fill';
                        button.setAttribute('data-is-favorited', 'true');
                    } else {
                        icon.className = 'bi bi-heart';
                        button.setAttribute('data-is-favorited', 'false');
                    }
                    alert('Error toggling favorite status');
                });
            }
        });
    });
</script>
@endpush