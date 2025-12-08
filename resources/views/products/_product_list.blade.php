<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
    @forelse ($products as $product)
        <div class="col">
            <div class="card h-100 product-card border-0 shadow-sm">
                <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                    <div class="product-image-container">
                        @if ($product->images->isNotEmpty())
                            <img src="{{ Storage::url($product->images->first()->image_path) }}" class="card-img-top" alt="{{ $product->name }}">
                        @else
                            <img src="https://via.placeholder.com/300x200.png?text=No+Image" class="card-img-top" alt="No Image">
                        @endif
                    </div>
                </a>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fs-6 text-dark">{{ $product->name }}</h5>
                    <p class="card-text text-muted small">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                    <div class="mt-auto">
                        <p class="fw-bold fs-5 mb-2">${{ number_format($product->price, 2) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                            <div class="d-flex gap-2">
                                @if($product->isInStock())
                                    <button class="btn btn-success btn-sm" onclick="addToCart({{ $product->id }}, 1)">
                                        <i class="bi bi-cart-plus"></i> Add
                                    </button>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>Out of Stock</button>
                                @endif
                                @auth
                                    <button class="btn btn-outline-danger btn-sm favorite-button" data-product-id="{{ $product->id }}" data-is-favorited="{{ $product->is_favorited_by_user ? 'true' : 'false' }}">
                                        <i class="bi {{ $product->is_favorited_by_user ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-heart"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
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
        background: linear-gradient(135deg, #ffffff, #f0f9ff);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-radius: 1rem;
        position: relative;
        border: none;
    }

    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
        opacity: 0.8;
        z-index: 10;
    }

    .product-card:hover {
        transform: translateY(-8px) rotate(0.5deg);
        box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.1), 0 10px 10px -5px rgba(59, 130, 246, 0.05) !important;
    }

    .product-info {
        background: linear-gradient(to bottom, rgba(255,255,255,1) 0%, rgba(240, 249, 255,1) 100%);
    }

    .product-image-container {
        overflow: hidden;
        border-radius: 1rem 1rem 0 0;
    }
    .product-image-container img {
        aspect-ratio: 1 / 1;
        object-fit: cover;
    }
</style>
