@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="container container-custom">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <h1 class="page-title">{{ __('My Favorite Products') }}</h1>
                <p class="lead">{{ __('Products you have marked as favorites.') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="container container-custom py-4">
    <div class="row">
        {{-- Empty space for potential sidebar, to match products/index design --}}
        <div class="col-md-3">
            {{-- You can add filters or other sidebar content here if needed in the future --}}
        </div>

        {{-- Favorite Product Listing --}}
        <div class="col-md-9">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div id="product-list-container" class="row">
                @forelse ($favoriteProducts as $product)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 product-card shadow-sm border-0">
                            @if ($product->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                            @else
                                <img src="https://via.placeholder.com/300x200.png?text=No+Image" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text text-muted mb-1">
                                    <i class="bi bi-tag me-1"></i>{{ $product->category?->name ?? 'Uncategorized' }}
                                </p>
                                <p class="card-text fw-bold text-primary fs-5 mb-3">${{ number_format($product->price, 2) }}</p>
                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary-gradient text-white flex-fill me-2">
                                        <i class="bi bi-eye me-1"></i>{{ __('View Details') }}
                                    </a>
                                    <div class="btn-group ms-2">
                                        @auth
                                            <button
                                                class="btn btn-danger btn-sm favorite-button"
                                                data-product-id="{{ $product->id }}"
                                                data-is-favorited="{{ $product->is_favorited_by_user ? 'true' : 'false' }}"
                                                title="{{ $product->is_favorited_by_user ? 'Remove from Favorites' : 'Add to Favorites' }}"
                                            >
                                                <i class="bi {{ $product->is_favorited_by_user ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm" title="Login to add to Favorites">
                                                <i class="bi bi-heart"></i>
                                            </a>
                                        @endauth
                                        <button class="btn btn-outline-success btn-sm ms-1" title="Buy Now">
                                            <i class="bi bi-bag-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-heart" style="font-size: 3rem; color: #d1d5db;"></i>
                            <h4 class="mt-3 text-muted">{{ __('You have no favorite products yet.') }}</h4>
                            <p class="text-muted">{{ __('Browse our products and add some to your favorites!') }}</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary-gradient mt-3">{{ __('Browse Products') }}</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $favoriteProducts->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productListContainer = document.getElementById('product-list-container');

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
                        'Content-Type': 'application/json',
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
                        // Optionally remove product from list if it's the favorites page and unfavorited
                        if (!isFavorited && window.location.pathname.includes('/favorites')) {
                            favoriteButton.closest('.col-md-4').remove();
                            // If no more favorites, display empty message
                            if (productListContainer.children.length === 0) {
                                productListContainer.innerHTML = `
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="bi bi-heart" style="font-size: 3rem; color: #d1d5db;"></i>
                                            <h4 class="mt-3 text-muted">{{ __('You have no favorite products yet.') }}</h4>
                                            <p class="text-muted">{{ __('Browse our products and add some to your favorites!') }}</p>
                                            <a href="{{ route('products.index') }}" class="btn btn-primary-gradient mt-3">{{ __('Browse Products') }}</a>
                                        </div>
                                    </div>
                                `;
                            }
                        }

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
</script>
@endpush
