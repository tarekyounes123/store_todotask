@extends('layouts.app')

@section('content')
<style>
    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.25rem;
        margin-top: 1rem;
    }

    .product-card {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .product-image-container {
        position: relative;
        height: 180px;
        overflow: hidden;
        background: #f8fafc;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    .product-category {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        background: rgba(255, 255, 255, 0.9);
        color: #374151;
        padding: 0.25rem 0.5rem;
        border-radius: 1.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
    }

    .product-info {
        padding: 1rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        line-height: 1.4;
        height: 2.8rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.75rem;
    }

    .product-actions {
        margin-top: auto;
        display: flex;
        gap: 0.5rem;
    }

    .view-details-btn {
        flex: 1;
        background: linear-gradient(135deg, var(--primary-color), #7c3aed);
        color: white;
        border: none;
        padding: 0.6rem 0.8rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        font-size: 0.875rem;
    }

    .view-details-btn:hover {
        background: linear-gradient(135deg, #4338ca, #6d28d9);
        transform: translateY(-1px);
    }

    .favorite-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 2px solid #d1d5db;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #ef4444;
    }

    .favorite-btn:hover {
        transform: scale(1.1);
        border-color: #ef4444;
    }

    .favorite-btn.active {
        background: #ef4444;
        border-color: #ef4444;
        color: white;
    }

    .empty-favorites {
        text-align: center;
        padding: 2rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        max-width: 400px;
        margin: 2rem auto;
    }

    .empty-heart {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .empty-subtitle {
        color: #6b7280;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }

    .browse-products-btn {
        background: linear-gradient(135deg, var(--primary-color), #7c3aed);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 2rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .browse-products-btn:hover {
        background: linear-gradient(135deg, #4338ca, #6d28d9);
        transform: translateY(-2px);
    }

    .pagination-container {
        margin-top: 2rem;
        text-align: center;
    }

    .success-alert {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 0.8rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .favorites-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .product-actions {
            flex-direction: column;
        }

        .view-details-btn {
            margin-bottom: 0.5rem;
        }
    }

    @media (max-width: 480px) {
        .favorites-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container container-custom py-4">
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
            <div class="col-md-3">
                {{-- Empty space for potential sidebar, to match products/index design --}}
            </div>
            <div class="col-md-9">
                @if (session('success'))
                    <div class="success-alert">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="favorites-grid" id="product-list-container">
                    @forelse ($favoriteProducts as $product)
                        <div class="product-card">
                            <div class="product-image-container">
                                @if ($product->images->isNotEmpty())
                                    <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                         class="product-image"
                                         alt="{{ $product->name }}">
                                @else
                                    <img src="https://placehold.co/300x180/e2e8f0/64748b?text=No+Image"
                                         class="product-image"
                                         alt="No Image">
                                @endif
                                <div class="product-category">
                                    <i class="bi bi-tag me-1"></i>{{ $product->category?->name ?? 'Uncategorized' }}
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">{{ $product->name }}</h3>
                                <div class="product-price">
                                    ${{ number_format($product->price, 2) }}
                                </div>
                                <div class="product-actions">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="view-details-btn">
                                        <i class="bi bi-eye"></i>
                                        {{ __('View Details') }}
                                    </a>
                                    <div>
                                        @auth
                                            <button
                                                class="favorite-btn {{ $product->is_favorited_by_user ? 'active' : '' }}"
                                                data-product-id="{{ $product->id }}"
                                                data-is-favorited="{{ $product->is_favorited_by_user ? 'true' : 'false' }}"
                                                title="{{ $product->is_favorited_by_user ? 'Remove from Favorites' : 'Add to Favorites' }}"
                                            >
                                                <i class="bi {{ $product->is_favorited_by_user ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}"
                                               class="favorite-btn"
                                               title="Login to add to Favorites">
                                                <i class="bi bi-heart"></i>
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-favorites">
                            <i class="bi bi-heart empty-heart"></i>
                            <h3 class="empty-title">{{ __('You have no favorite products yet.') }}</h3>
                            <p class="empty-subtitle">{{ __('Browse our products and add some to your favorites!') }}</p>
                            <a href="{{ route('products.index') }}" class="browse-products-btn">
                                <i class="bi bi-shop"></i>
                                {{ __('Browse Products') }}
                            </a>
                        </div>
                    @endforelse
                </div>

                <div class="pagination-container">
                    {{ $favoriteProducts->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productListContainer = document.getElementById('product-list-container');

        // Handle favorite button clicks
        productListContainer.addEventListener('click', function(e) {
            const favoriteButton = e.target.closest('.favorite-btn');
            if (favoriteButton && !favoriteButton.classList.contains('view-details-btn')) {
                e.preventDefault();
                const productId = favoriteButton.dataset.productId ||
                                 favoriteButton.closest('.product-card').querySelector('.favorite-btn').dataset.productId;
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

                        // Update button classes and icon
                        if (isFavorited) {
                            favoriteButton.classList.add('active');
                            favoriteButton.innerHTML = '<i class="bi bi-heart-fill"></i>';
                            favoriteButton.title = 'Remove from Favorites';
                        } else {
                            favoriteButton.classList.remove('active');
                            favoriteButton.innerHTML = '<i class="bi bi-heart"></i>';
                            favoriteButton.title = 'Add to Favorites';
                        }

                        alert(data.message);

                        // Optionally remove product from list if unfavorited on favorites page
                        if (!isFavorited && window.location.pathname.includes('/favorites')) {
                            const card = favoriteButton.closest('.product-card');
                            if (card) {
                                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                                card.style.opacity = '0';
                                card.style.transform = 'translateY(-10px)';
                                setTimeout(() => {
                                    card.remove();

                                    // If no more favorites, show empty state
                                    if (document.querySelectorAll('.product-card').length === 0) {
                                        productListContainer.innerHTML = `
                                            <div class="empty-favorites">
                                                <i class="bi bi-heart empty-heart"></i>
                                                <h3 class="empty-title">{{ __('You have no favorite products yet.') }}</h3>
                                                <p class="empty-subtitle">{{ __('Browse our products and add some to your favorites!') }}</p>
                                                <a href="{{ route('products.index') }}" class="browse-products-btn">
                                                    <i class="bi bi-shop"></i>
                                                    {{ __('Browse Products') }}
                                                </a>
                                            </div>
                                        `;
                                    }
                                }, 300);
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
@endsection
