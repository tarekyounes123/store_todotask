@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-7">
            <div class="main-image-container mb-3">
                @if ($product->images->isNotEmpty())
                    <img src="{{ Storage::url($product->images->first()->image_path) }}" class="img-fluid rounded-lg shadow-sm" alt="{{ $product->name }}" id="main-product-image">
                @else
                    <img src="https://via.placeholder.com/600x600.png?text=No+Image" class="img-fluid rounded-lg shadow-sm" alt="No Image">
                @endif
            </div>
            @if ($product->images->count() > 1)
            <div class="thumbnail-images d-flex justify-content-center">
                @foreach ($product->images as $image)
                    <img src="{{ Storage::url($image->image_path) }}" class="thumbnail-item img-thumbnail mx-1" alt="{{ $product->name }}" onclick="changeMainImage('{{ Storage::url($image->image_path) }}')">
                @endforeach
            </div>
            @endif
        </div>
        <div class="col-lg-5">
            <div class="product-details p-4 rounded-lg shadow-sm bg-white">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                    </ol>
                </nav>
                <h1 class="product-title display-5 fw-bold mb-3">{{ $product->name }}</h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <span class="text-warning">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= round($product->average_rating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </span>
                        <span class="ms-1 fw-bold">{{ round($product->average_rating, 1) }}</span>
                    </div>
                    <a href="#reviews" class="text-muted text-decoration-none">{{ __($product->review_count . ' Reviews') }}</a>
                </div>
                <p class="product-price display-6 fw-normal text-primary mb-4" id="product-price">${{ number_format($product->price, 2) }}</p>
                <p class="mb-4">{{ $product->description }}</p>
                <div class="mb-4">
                    <p class="mb-1"><i class="bi bi-tag me-2"></i><strong>{{ __('Category:') }}</strong> {{ $product->category?->name ?? 'Uncategorized' }}</p>
                    <p class="mb-0"><i class="bi bi-box-seam me-2"></i><strong>{{ __('Stock:') }}</strong>
                        <span id="product-stock-status">
                            @if($product->stock_quantity > 0)
                                <span class="text-success">{{ $product->stock_quantity }} {{ __('items in stock') }}</span>
                            @else
                                <span class="text-danger">{{ __('Out of stock') }}</span>
                            @endif
                        </span>
                    </p>
                </div>

                {{-- Variant Selection --}}
                <div id="variant-selection-area" class="mb-4">
                    @php
                        $variantAttributes = $product->attributes->filter(fn($attr) => $attr->is_variant_attribute);
                    @endphp

                    @if ($variantAttributes->isNotEmpty() && $product->variants->isNotEmpty())
                        @foreach ($variantAttributes as $attribute)
                            <div class="mb-3">
                                <label for="attribute-{{ $attribute->id }}" class="form-label">{{ $attribute->name }}</label>
                                <select class="form-select variant-selector" data-attribute-id="{{ $attribute->id }}" id="attribute-{{ $attribute->id }}">
                                    <option value="">Select {{ $attribute->name }}</option>
                                    @foreach ($attribute->terms as $term)
                                        @php
                                            // Ensure this term is actually part of an available variant
                                            $termIsAvailableInVariants = false;
                                            foreach ($product->variants as $variant) {
                                                if ($variant->terms->contains($term->attributeTerm->id)) {
                                                    $termIsAvailableInVariants = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @if($termIsAvailableInVariants)
                                            <option value="{{ $term->attributeTerm->id }}">{{ $term->attributeTerm->value }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="d-grid gap-2">
                    <button id="add-to-cart-btn" class="btn btn-primary btn-lg" {{ $product->stock_quantity > 0 ? '' : 'disabled' }}>
                        <i class="bi bi-cart-plus me-2"></i>{{ __('Add to Cart') }}
                    </button>
                    <input type="hidden" id="selected-variant-id" value="">
                    @auth
                        <button class="btn btn-outline-danger btn-lg favorite-button" data-product-id="{{ $product->id }}" data-is-favorited="{{ $product->is_favorited_by_user ? 'true' : 'false' }}">
                            <i class="bi {{ $product->is_favorited_by_user ? 'bi-heart-fill' : 'bi-heart' }} me-2"></i>
                            <span class="favorite-button-text">{{ $product->is_favorited_by_user ? __('Remove from Favorites') : __('Add to Favorites') }}</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-danger btn-lg">
                            <i class="bi bi-heart me-2"></i>{{ __('Add to Favorites') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4" id="reviews">{{ __('Reviews & Comments') }}</h2>
                    <div class="row">
                        <div class="col-md-7">
                            <div id="reviews-container"></div>
                            <div id="comments-container" class="mt-5"></div>
                        </div>
                        <div class="col-md-5">
                            @auth
                                <div class="mb-5">
                                    <h4>{{ __('Write a Review') }}</h4>
                                    <form id="review-form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="mb-3">
                                            <label for="rating" class="form-label">{{ __('Your Rating') }}</label>
                                            <div class="rating-options">
                                                <button type="button" class="btn btn-sm btn-outline-warning me-1" data-rating="1">★</button>
                                                <button type="button" class="btn btn-sm btn-outline-warning me-1" data-rating="2">★</button>
                                                <button type="button" class="btn btn-sm btn-outline-warning me-1" data-rating="3">★</button>
                                                <button type="button" class="btn btn-sm btn-outline-warning me-1" data-rating="4">★</button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" data-rating="5">★</button>
                                                <input type="hidden" name="rating" id="rating-input" value="0">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <textarea class="form-control" id="review" name="review" rows="4" placeholder="{{ __('Share your thoughts...') }}"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">{{ __('Submit Review') }}</button>
                                    </form>
                                </div>
                                <div>
                                    <h4>{{ __('Add a Comment') }}</h4>
                                    <form id="comment-form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="mb-3">
                                            <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="{{ __('Ask a question or share a comment...') }}"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">{{ __('Post Comment') }}</button>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ __('Please') }} <a href="{{ route('login') }}">{{ __('login') }}</a> {{ __('to leave a review or comment.') }}
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .main-image-container {
        border-radius: .5rem;
        overflow: hidden;
    }
    .thumbnail-item {
        width: 80px;
        height: 80px;
        object-fit: cover;
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.2s ease-in-out;
    }
    .thumbnail-item:hover, .thumbnail-item.active {
        opacity: 1;
    }
    .product-details {
        background-color: #f8fafc;
    }
    .rating-options .btn {
        font-size: 1.5rem;
    }
    .rating-options .btn.active {
        color: #f59e0b;
    }
</style>
@endsection

@push('scripts')
<script>
    function changeMainImage(src) {
        document.getElementById('main-product-image').src = src;
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.classList.remove('active');
            if(item.src === src) {
                item.classList.add('active');
            }
        });
    }

    // Product data including variants for JavaScript
    const productData = @json($product);
    const variants = productData.variants;
    const initialPrice = productData.price;
    const initialStock = productData.stock_quantity;
    let selectedVariantId = null;

    document.addEventListener('DOMContentLoaded', function() {
        const firstThumbnail = document.querySelector('.thumbnail-item');
        if(firstThumbnail) {
            firstThumbnail.classList.add('active');
        }
        
        loadReviews();
        loadComments();

        // Variant selection logic
        const variantSelectors = document.querySelectorAll('.variant-selector');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const productPriceElement = document.getElementById('product-price');
        const productStockStatusElement = document.getElementById('product-stock-status');
        const selectedVariantIdInput = document.getElementById('selected-variant-id');

        // Initial check for variants
        if (variants.length > 0) {
            // Disable add to cart button initially for variant products until a valid selection is made
            addToCartBtn.disabled = true;
            productPriceElement.textContent = 'Select options';
            productStockStatusElement.innerHTML = '<span class="text-muted">Select options for stock</span>';
        } else if (initialStock > 0) {
            // For simple products (no variants), enable add to cart if in stock
            addToCartBtn.disabled = false;
        } else {
            // For simple products with no stock, disable button
            addToCartBtn.disabled = true;
        }


        variantSelectors.forEach(selector => {
            selector.addEventListener('change', updateVariantDisplay);
        });

        // Manually trigger initial update to handle any pre-selected options or initial state
        updateVariantDisplay();

        function updateVariantDisplay() {
            const selectedTerms = {};
            variantSelectors.forEach(selector => {
                if (selector.value) {
                    selectedTerms[selector.dataset.attributeId] = parseInt(selector.value);
                }
            });

            const selectedAttributeIds = Object.keys(selectedTerms);

            // Find matching variant - check if all selected attributes match a variant
            let matchingVariant = null;

            if (selectedAttributeIds.length > 0) {
                matchingVariant = variants.find(variant => {
                    // Map variant terms to their IDs for comparison
                    const variantTermIds = variant.terms.map(term => term.id);

                    // Check if all selected terms are present in this variant
                    const allSelectedTermsMatch = selectedAttributeIds.every(attrId =>
                        variantTermIds.includes(selectedTerms[attrId])
                    );

                    // Also check if the number of selected attributes matches the number of terms in the variant
                    // This ensures we have a complete match (not partial)
                    const hasCompleteMatch = selectedAttributeIds.length === variant.terms.length;

                    return allSelectedTermsMatch && hasCompleteMatch;
                });
            }

            if (matchingVariant) {
                // Update price
                productPriceElement.textContent = `$${parseFloat(matchingVariant.price).toFixed(2)}`;

                // Update stock
                if (matchingVariant.stock_quantity > 0) {
                    productStockStatusElement.innerHTML = `<span class="text-success">${matchingVariant.stock_quantity} items in stock</span>`;
                    addToCartBtn.disabled = false;
                } else {
                    productStockStatusElement.innerHTML = `<span class="text-danger">Out of stock</span>`;
                    addToCartBtn.disabled = true;
                }

                // Update selected variant ID for add to cart
                selectedVariantId = matchingVariant.id;
                selectedVariantIdInput.value = matchingVariant.id;

                // Update main image if variant has one (assuming image_path exists on variant)
                if (matchingVariant.image_path) {
                    changeMainImage(`{{ Storage::url('') }}${matchingVariant.image_path}`);
                } else {
                    // Revert to product's main image if variant has none
                    const firstProductImage = productData.images.length > 0 ? productData.images[0].image_path : null;
                    if (firstProductImage) {
                        changeMainImage(`{{ Storage::url('') }}${firstProductImage}`);
                    } else {
                        changeMainImage("https://via.placeholder.com/600x600.png?text=No+Image");
                    }
                }
            } else {
                // No matching variant or incomplete selection
                productPriceElement.textContent = `$${parseFloat(initialPrice).toFixed(2)}`;
                if (initialStock > 0) {
                    productStockStatusElement.innerHTML = `<span class="text-success">${initialStock} items in stock</span>`;
                } else {
                    productStockStatusElement.innerHTML = `<span class="text-danger">Out of stock</span>`;
                }

                // Revert main image to product's default if no variant selected or no match
                const firstProductImage = productData.images.length > 0 ? productData.images[0].image_path : null;
                if (firstProductImage) {
                    changeMainImage(`{{ Storage::url('') }}${firstProductImage}`);
                } else {
                    changeMainImage("https://via.placeholder.com/600x600.png?text=No+Image");
                }

                addToCartBtn.disabled = true;
                selectedVariantId = null;
                selectedVariantIdInput.value = '';
                productPriceElement.textContent = 'Select options';
                productStockStatusElement.innerHTML = '<span class="text-muted">Select options for stock</span>';

                // If no variants for product, enable add to cart if product is in stock
                if (variants.length === 0 && initialStock > 0) {
                    addToCartBtn.disabled = false;
                    productPriceElement.textContent = `$${parseFloat(initialPrice).toFixed(2)}`;
                    productStockStatusElement.innerHTML = `<span class="text-success">${initialStock} items in stock</span>`;
                } else if (variants.length === 0 && initialStock <= 0) {
                     addToCartBtn.disabled = true;
                    productPriceElement.textContent = `$${parseFloat(initialPrice).toFixed(2)}`;
                    productStockStatusElement.innerHTML = `<span class="text-danger">Out of stock</span>`;
                }
            }
        }

        // Manually trigger initial update if no variants or if some pre-selected options are present
        if (variants.length === 0) {
            updateVariantDisplay(); // To correctly enable/disable Add to Cart for simple products
        } else {
            // Check if any selectors already have values (e.g., from old input on validation error)
            const hasPreselected = Array.from(variantSelectors).some(selector => selector.value !== '');
            if (hasPreselected) {
                 updateVariantDisplay();
            }
        }
    });

    // --- Existing Functions ---
    // Rating selection functionality
    document.querySelectorAll('.rating-options button').forEach(button => {
        button.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            document.getElementById('rating-input').value = rating;

            document.querySelectorAll('.rating-options button').forEach(btn => btn.classList.remove('active'));
            for (let i = 1; i <= parseInt(rating); i++) {
                document.querySelector(`[data-rating="${i}"]`).classList.add('active');
            }
        });
    });

    function getProfilePictureHtml(user) {
        if (user.profile_picture) {
            return `<img src="/storage/${user.profile_picture}" alt="Profile" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">`;
        } else {
            return `<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width: 40px; height: 40px;"><i class="bi bi-person"></i></div>`;
        }
    }

    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="bi ${i <= rating ? 'bi-star-fill text-warning' : 'bi-star text-muted'}"></i>`;
        }
        return stars;
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function loadReviews() {
        fetch(`/products/{{ $product->id }}/reviews`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('reviews-container');
                container.innerHTML = '<h4>Reviews</h4>';
                if (data.reviews.length === 0) {
                    container.innerHTML += '<p>No reviews yet.</p>';
                    return;
                }
                data.reviews.forEach(review => {
                    const reviewEl = document.createElement('div');
                    reviewEl.className = 'd-flex align-items-start mb-4';
                    reviewEl.innerHTML = `
                        ${getProfilePictureHtml(review.user)}
                        <div class="ms-3">
                            <h6 class="mb-0">${review.user.name}</h6>
                            <div class="mb-1">${generateStars(review.rating)}</div>
                            <p>${review.review}</p>
                            <small class="text-muted">${formatDate(review.created_at)}</small>
                        </div>
                    `;
                    container.appendChild(reviewEl);
                });
            });
    }

    function loadComments() {
        fetch(`/products/{{ $product->id }}/comments`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('comments-container');
                container.innerHTML = '<h4>Comments</h4>';
                if (data.comments.length === 0) {
                    container.innerHTML += '<p>No comments yet.</p>';
                    return;
                }
                data.comments.forEach(comment => {
                    const commentEl = document.createElement('div');
                    commentEl.className = 'd-flex align-items-start mb-4';
                    commentEl.innerHTML = `
                        ${getProfilePictureHtml(comment.user)}
                        <div class="ms-3">
                            <h6 class="mb-0">${comment.user.name}</h6>
                            <p>${comment.review}</p>
                            <small class="text-muted">${formatDate(comment.created_at)}</small>
                        </div>
                    `;
                    container.appendChild(commentEl);
                });
            });
    }
    
    document.getElementById('review-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/reviews', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        }).then(() => {
            this.reset();
            loadReviews();
        });
    });

    document.getElementById('comment-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/comments', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        }).then(() => {
            this.reset();
            loadComments();
        });
    });

    document.querySelector('.favorite-button')?.addEventListener('click', function() {
        const productId = this.dataset.productId;
        let isFavorited = this.dataset.isFavorited === 'true';
        const url = isFavorited ? `/favorites/${productId}/remove` : `/favorites/${productId}/add`;

        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        }).then(response => response.json()).then(data => {
            if (data.success) {
                this.dataset.isFavorited = !isFavorited;
                this.querySelector('i').classList.toggle('bi-heart');
                this.querySelector('i').classList.toggle('bi-heart-fill');
                this.querySelector('.favorite-button-text').textContent = !isFavorited ? 'Remove from Favorites' : 'Add to Favorites';
            }
        });
    });

    document.getElementById('add-to-cart-btn')?.addEventListener('click', function() {
        const productId = productData.id;
        const quantity = 1; // Assuming always adding 1 for now

        // Check if we have variants but no variant selected
        if (variants.length > 0 && selectedVariantId === null) {
            alert('Please select all variant options before adding to cart.');
            return;
        }

        // Log the data being sent for debugging
        console.log('Adding to cart:', {
            product_id: productId,
            quantity: quantity,
            product_variant_id: selectedVariantId,
            variants_count: variants.length
        });

        // Show loading state
        const originalText = this.innerHTML;
        const originalDisabled = this.disabled;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
        this.disabled = true;

        fetch('/cart/add', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity,
                product_variant_id: selectedVariantId // This will be null for simple products
            })
        }).then(response => {
            console.log('Response status:', response.status);
            return response.json();
        }).then(data => {
            console.log('Response data:', data);
            if(data.success) {
                alert('Product added to cart!');
                updateCartCount();
            } else if (data.message) {
                alert(data.message);
            }
        }).catch(error => {
            console.error('Error adding to cart:', error);
            alert('An error occurred while adding the product to cart. Please try again.');
        }).finally(() => {
            // Restore original button state
            this.innerHTML = originalText;
            this.disabled = originalDisabled;
        });
    });

</script>
@endpush
