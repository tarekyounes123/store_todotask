<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
    @forelse ($products as $product)
        <div class="col">
            <div class="card h-100 product-card border-0 shadow-sm">
                <div class="product-image-container">
                    @if ($product->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" class="card-img-top" alt="{{ $product->name }}">
                    @else
                        <img src="https://via.placeholder.com/300x200.png?text=No+Image" class="card-img-top" alt="No Image">
                    @endif
                    <div class="product-actions">
                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm"><i class="bi bi-eye"></i></a>
                        @auth
                            <button class="btn btn-light btn-sm favorite-button" data-product-id="{{ $product->id }}" data-is-favorited="{{ $product->is_favorited_by_user ? 'true' : 'false' }}">
                                <i class="bi {{ $product->is_favorited_by_user ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                            </button>
                        @endauth
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fs-6">{{ $product->name }}</h5>
                    <p class="card-text text-muted small">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <p class="fw-bold fs-5 mb-0">${{ number_format($product->price, 2) }}</p>
                        @if($product->isInStock())
                            <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }}, 1)">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled>Out of Stock</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                <h4 class="mt-3 text-muted">{{ __('No products available yet.') }}</h4>
                <p class="text-muted">{{ __('Check back later for new products.') }}</p>
            </div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $products->withQueryString()->links() }}
</div>

<style>
    .product-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .product-image-container {
        position: relative;
        overflow: hidden;
    }
    .product-image-container img {
        aspect-ratio: 1 / 1;
        object-fit: cover;
    }
    .product-actions {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
    .product-card:hover .product-actions {
        opacity: 1;
    }
</style>

