<div class="product-card" data-product-id="{{ $product->id }}">
    <div class="product-image-container">
        @if ($product->images->isNotEmpty())
            <img src="{{ Storage::url($product->images->first()->image_path) }}"
                 class="product-image"
                 alt="{{ $product->name }}"
                 loading="lazy">
        @else
            <img src="https://placehold.co/300x180/e2e8f0/64748b?text=No+Image"
                 class="product-image"
                 alt="No Image"
                 loading="lazy">
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
            <div class="d-flex gap-2">
                @if(isset($showCartButton) && $showCartButton !== false)
                    @if($product->isInStock())
                        <button class="btn btn-success btn-sm px-2 py-1" onclick="addToCart({{ $product->id }}, 1)" title="Add to Cart">
                            <i class="bi bi-cart-plus"></i>
                        </button>
                    @else
                        <button class="btn btn-secondary btn-sm px-2 py-1" disabled title="Out of Stock">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    @endif
                @endif
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