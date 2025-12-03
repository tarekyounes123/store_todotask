        <div class="row">
            @forelse ($products as $product)
                <div class="col-md-4 mb-4">
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
                            <p class="card-text fw-bold text-primary fs-5 mb-2">${{ number_format($product->price, 2) }}</p>
                            <!-- Stock Status -->
                            <div class="mb-2">
                                @if($product->isInStock())
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>{{ __('In Stock') }}
                                    </span>
                                    <small class="text-muted">({{ $product->stock_quantity }} left)</small>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>{{ __('Out of Stock') }}
                                    </span>
                                @endif
                            </div>
                            <div class="mt-auto d-flex justify-content-between">
                                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary-gradient text-white flex-fill me-2">
                                    <i class="bi bi-eye me-1"></i>{{ __('View Details') }}
                                </a>
                                <div class="btn-group ms-2">
                                    @auth
                                        <button
                                            class="btn btn-outline-danger btn-sm favorite-button"
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
                                    @if($product->isInStock())
                                        <button class="btn btn-outline-success btn-sm ms-1" title="Add to Cart" onclick="addToCart({{ $product->id }}, 1)">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-outline-secondary btn-sm ms-1" title="Out of Stock" disabled>
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
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
