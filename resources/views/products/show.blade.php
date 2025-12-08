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
                <p class="product-price display-6 fw-normal text-primary mb-4">${{ number_format($product->price, 2) }}</p>
                <p class="mb-4">{{ $product->description }}</p>
                <div class="mb-4">
                    <p class="mb-1"><i class="bi bi-tag me-2"></i><strong>{{ __('Category:') }}</strong> {{ $product->category?->name ?? 'Uncategorized' }}</p>
                    <p class="mb-0"><i class="bi bi-box-seam me-2"></i><strong>{{ __('Stock:') }}</strong>
                        @if($product->stock_quantity > 0)
                            <span class="text-success">{{ $product->stock_quantity }} {{ __('items in stock') }}</span>
                        @else
                            <span class="text-danger">{{ __('Out of stock') }}</span>
                        @endif
                    </p>
                </div>

                <div class="d-grid gap-2">
                    @if($product->isInStock())
                        <button id="add-to-cart-btn" class="btn btn-primary btn-lg" onclick="addToCart({{ $product->id }}, 1)">
                            <i class="bi bi-cart-plus me-2"></i>{{ __('Add to Cart') }}
                        </button>
                    @else
                        <button class="btn btn-secondary btn-lg" disabled>
                            <i class="bi bi-x-circle me-2"></i>{{ __('Out of Stock') }}
                        </button>
                    @endif
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

    document.addEventListener('DOMContentLoaded', function() {
        const firstThumbnail = document.querySelector('.thumbnail-item');
        if(firstThumbnail) {
            firstThumbnail.classList.add('active');
        }
        
        loadReviews();
        loadComments();
    });

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
        });
    }
</script>
@endpush