<div class="product-grid-container">
    @forelse ($products as $product)
        <div class="product-card">
            <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                <div class="product-image-container">
                    @if ($product->images->isNotEmpty())
                        <img src="{{ Storage::url($product->images->first()->image_path) }}" alt="{{ $product->name }}">
                    @else
                        <img src="https://via.placeholder.com/300x200.png?text=No+Image" alt="No Image">
                    @endif

                    @auth
                        <button class="favorite-button {{ $product->is_favorited_by_user ? 'active' : '' }}" data-product-id="{{ $product->id }}" data-is-favorited="{{ $product->is_favorited_by_user ? 'true' : 'false' }}">
                            <i class="bi {{ $product->is_favorited_by_user ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        </button>
                    @else
                        <button class="favorite-button login-required">
                            <i class="bi bi-heart"></i>
                        </button>
                    @endauth
                </div>
            </a>

            <div class="product-info">
                <div class="product-category">{{ $product->category?->name ?? 'Uncategorized' }}</div>
                <h3 class="product-name">{{ $product->name }}</h3>
                <div class="product-price">${{ number_format($product->price, 2) }}</div>

                <div class="product-actions">
                    <a href="{{ route('products.show', $product->slug) }}" class="view-details-btn">
                        <i class="bi bi-eye"></i> {{ __('View Details') }}
                    </a>

                    @if($product->isInStock())
                        <button class="add-to-cart-btn" onclick="addToCart({{ $product->id }}, 1)">
                            <i class="bi bi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    @else
                        <button class="add-to-cart-btn" disabled>
                            <i class="bi bi-x-circle"></i> {{ __('Out of Stock') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="empty-products">
            <div class="empty-products-content">
                <i class="bi bi-inbox empty-icon"></i>
                <h4 class="empty-title">{{ __('No products available yet.') }}</h4>
                <p class="empty-subtitle">{{ __('Check back later for new products.') }}</p>
            </div>
        </div>
    @endforelse
</div>

<div class="pagination-wrapper">
    {{ $products->withQueryString()->links() }}
</div>

<style>
    .product-grid-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        padding: 0;
        margin: 0;
    }

    .product-card {
        background: white;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        border: 1px solid rgba(226, 232, 240, 0.5);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .product-image-container {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: #f8fafc;
    }

    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image-container img {
        transform: scale(1.05);
    }

    .favorite-button {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 5;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .favorite-button:hover {
        transform: scale(1.1) rotate(5deg);
        background: #fee2e2;
    }

    .favorite-button i {
        color: #ef4444;
        font-size: 1.1rem;
    }

    .favorite-button.active i {
        color: #ef4444;
    }

    .product-info {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .product-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.75rem;
        line-height: 1.4;
        height: auto;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-price {
        font-size: 1.4rem;
        font-weight: 700;
        color: #4f46e5;
        margin-bottom: 1.25rem;
    }

    .product-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: auto;
    }

    .view-details-btn {
        flex: 1;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
        padding: 0.875rem;
        border-radius: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .view-details-btn:hover {
        background: linear-gradient(135deg, #4338ca, #6d28d9);
        transform: translateY(-2px);
    }

    .add-to-cart-btn {
        flex: 1;
        background: white;
        color: #4f46e5;
        border: 2px solid #e0e7ff;
        padding: 0.875rem;
        border-radius: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .add-to-cart-btn:hover {
        background: #e0e7ff;
        border-color: #c7d2fe;
        transform: translateY(-2px);
    }

    .add-to-cart-btn:disabled {
        background: #f1f5f9;
        color: #94a3b8;
        border-color: #cbd5e1;
        cursor: not-allowed;
        transform: none;
    }

    .empty-products {
        grid-column: 1 / -1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 3rem;
    }

    .empty-products-content {
        text-align: center;
        max-width: 400px;
    }

    .empty-icon {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .empty-subtitle {
        color: #9ca3af;
        font-size: 1rem;
    }

    .pagination-wrapper {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .product-grid-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .product-card {
            border-radius: 1.1rem;
        }
    }

    @media (max-width: 575px) {
        .product-grid-container {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .product-actions {
            flex-direction: column;
        }

        .view-details-btn,
        .add-to-cart-btn {
            margin-bottom: 0.5rem;
        }

        .product-card {
            border-radius: 1rem;
        }
    }
</style>
