@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    @if ($product->images->isNotEmpty())
                        <div id="productCarousel" class="carousel slide rounded" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($product->images as $key => $image)
                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="d-block w-100" alt="{{ $product->name }}" style="max-height: 500px; object-fit: contain; background-color: #f8fafc;">
                                    </div>
                                @endforeach
                            </div>
                            @if ($product->images->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>
                    @else
                        <img src="https://via.placeholder.com/500x500.png?text=No+Image" class="d-block w-100 rounded" alt="No Image" style="max-height: 500px; object-fit: contain; background-color: #f8fafc;">
                    @endif
                </div>
                <div class="col-md-6">
                    <h1 class="mb-3">{{ $product->name }}</h1>

                    <!-- Rating Section -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center">
                            <div class="fs-5 me-3">
                                <span class="text-warning" id="average-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= round($product->average_rating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </span>
                                <span class="ms-1 fw-bold">{{ round($product->average_rating, 1) }}</span>
                            </div>
                            <div class="text-muted">
                                <a href="#reviews" class="text-decoration-none" id="review-count">{{ __($product->review_count . ' Reviews') }}</a>
                            </div>
                        </div>
                    </div>

                    <p class="lead fw-bold text-primary fs-3 mb-4">${{ number_format($product->price, 2) }}</p>

                    <div class="mb-4">
                        <p class="mb-1"><i class="bi bi-tag me-2"></i><strong>{{ __('Category:') }}</strong> {{ $product->category?->name ?? 'Uncategorized' }}</p>
                        <p class="mb-0"><i class="bi bi-calendar me-2"></i><strong>{{ __('Added:') }}</strong> {{ $product->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="mb-4">
                        <h5><i class="bi bi-info-circle me-2"></i>{{ __('Description') }}</h5>
                        <p>{{ $product->description }}</p>
                    </div>

                    <div class="d-grid gap-3 mt-5">
                        @auth
                            <button
                                class="btn btn-danger btn-lg favorite-button"
                                data-product-id="{{ $product->id }}"
                                data-is-favorited="{{ $product->is_favorited_by_user ? 'true' : 'false' }}"
                                title="{{ $product->is_favorited_by_user ? 'Remove from Favorites' : 'Add to Favorites' }}"
                            >
                                <i class="bi {{ $product->is_favorited_by_user ? 'bi-heart-fill' : 'bi-heart' }} me-2"></i>
                                <span class="favorite-button-text">
                                    {{ $product->is_favorited_by_user ? __('Remove from Favorites') : __('Add to Favorites') }}
                                </span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-danger btn-lg" title="Login to add to Favorites">
                                <i class="bi bi-heart me-2"></i>{{ __('Add to Favorites') }}
                            </a>
                        @endauth
                        <!-- Add to Cart Button -->
                        @if($product->isInStock())
                        <button
                            id="add-to-cart-btn"
                            class="btn btn-primary btn-lg"
                            title="Add to Cart"
                            onclick="addToCart({{ $product->id }}, 1)"
                        >
                            <i class="bi bi-cart-plus me-2"></i>{{ __('Add to Cart') }}
                        </button>
                        @else
                        <button
                            class="btn btn-secondary btn-lg"
                            title="Out of Stock"
                            disabled
                        >
                            <i class="bi bi-x-circle me-2"></i>{{ __('Out of Stock') }}
                        </button>
                        @endif
                        <small class="text-muted">
                            @if($product->stock_quantity > 0)
                                {{ $product->stock_quantity }} {{ __('items in stock') }}
                            @else
                                {{ __('Out of stock') }}
                            @endif
                        </small>
                        <button class="btn btn-success btn-lg" title="Purchase">
                            <i class="bi bi-bag-check me-2"></i>{{ __('Buy Now') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="mt-5" id="reviews">
                <div class="row">
                    <div class="col-md-6">
                        <h4><i class="bi bi-chat-square-text me-2"></i>{{ __('Reviews') }}</h4>

                        <!-- Reviews will be loaded here dynamically -->
                        <div id="reviews-container">
                            <!-- Reviews will be loaded here via AJAX -->
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Add Review Form -->
                        <h4><i class="bi bi-pencil-square me-2"></i>{{ __('Write a Review') }}</h4>

                        @auth
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
                                <label for="review" class="form-label">{{ __('Your Review') }}</label>
                                <textarea class="form-control" id="review" name="review" rows="4" placeholder="{{ __('Share your experience with this product...') }}"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary-gradient text-white">
                                <i class="bi bi-send me-1"></i>{{ __('Submit Review') }}
                            </button>
                        </form>
                        @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-1"></i>
                            {{ __('Please') }} <a href="{{ route('login') }}">{{ __('login') }}</a> {{ __('to leave a review.') }}
                        </div>
                        @endauth
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="mt-5">
                    <h4><i class="bi bi-chat-left-text me-2"></i>{{ __('Comments') }}</h4>

                    @auth
                    <form id="comment-form" class="mb-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="mb-3">
                            <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="{{ __('Add a comment...') }}"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-gradient text-white btn-sm">
                            <i class="bi bi-send me-1"></i>{{ __('Post Comment') }}
                        </button>
                    </form>
                    @else
                    <div class="alert alert-info alert-sm mb-4">
                        <i class="bi bi-info-circle me-1"></i>
                        {{ __('Please') }} <a href="{{ route('login') }}">{{ __('login') }}</a> {{ __('to add a comment.') }}
                    </div>
                    @endauth

                    <!-- Comments will be loaded here dynamically -->
                    <div id="comments-container">
                        <!-- Comments will be loaded here via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rating-options .btn {
        color: #a0aec0; /* Light gray */
        padding: 0.25rem 0.5rem;
        font-size: 1.5rem;
    }

    .rating-options .btn.active,
    .rating-options .btn:hover {
        color: #f59e0b; /* Yellow for active/star */
    }

    .comments-list .card {
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .review-item {
        margin-bottom: 1rem;
    }

    .review-item .card-body {
        padding: 0.75rem;
    }

    /* Styles for carousel navigation icons */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
        border-radius: 0.25rem; /* Slightly rounded corners */
        padding: 0.75rem; /* Some padding around the icon */
    }
</style>

@push('scripts')
<script>
    // Rating selection functionality
    document.querySelectorAll('.rating-options button').forEach(button => {
        button.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            document.getElementById('rating-input').value = rating;

            // Update button states
            document.querySelectorAll('.rating-options button').forEach(btn => {
                btn.classList.remove('btn-warning', 'btn-outline-warning');
                btn.classList.add('btn-outline-warning');
            });

            // Apply active state to selected and previous stars
            for (let i = 1; i <= parseInt(rating); i++) {
                const starBtn = document.querySelector(`[data-rating="${i}"]`);
                starBtn.classList.remove('btn-outline-warning');
                starBtn.classList.add('btn-warning');
            }
        });
    });

    // Load reviews and comments when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadReviews();
        loadComments();
    });

    // Generate HTML for profile picture or default icon
    function getProfilePictureHtml(user) {
        if (user.profile_picture) {
            return `<img src="/storage/${user.profile_picture}" alt="Profile" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">`;
        } else {
            return `<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width: 30px; height: 30px;"><i class="bi bi-person"></i></div>`;
        }
    }

    // Load reviews from the server
    function loadReviews() {
        fetch(`/products/{{ $product->id }}/reviews`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('reviews-container');
                    container.innerHTML = '';

                    if (data.reviews.length === 0) {
                        container.innerHTML = `
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ __('No reviews yet. Be the first to review this product!') }}
                            </div>
                        `;
                        return;
                    }

                    data.reviews.forEach(review => {
                        const reviewElement = document.createElement('div');
                        reviewElement.className = 'card mb-3 review-item';
                        reviewElement.innerHTML = `
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        ${getProfilePictureHtml(review.user)}
                                        <h6 class="card-subtitle mb-0 text-muted">${review.user.name}</h6>
                                    </div>
                                    <div class="text-warning">
                                        ${generateStars(review.rating)}
                                    </div>
                                </div>
                                <p class="card-text">${review.review}</p>
                                <small class="text-muted">${formatDate(review.created_at)}</small>
                            </div>
                        `;
                        container.appendChild(reviewElement);
                    });
                }
            });
    }

    // Load comments from the server
    function loadComments() {
        fetch(`/products/{{ $product->id }}/comments`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('comments-container');
                    container.innerHTML = '';

                    if (data.comments.length === 0) {
                        container.innerHTML = `
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ __('No comments yet. Be the first to comment!') }}
                            </div>
                        `;
                        return;
                    }

                    data.comments.forEach(comment => {
                        const commentElement = document.createElement('div');
                        commentElement.className = 'd-flex mb-3';
                        commentElement.innerHTML = `
                            <div class="flex-shrink-0 d-flex align-items-center">
                                ${getProfilePictureHtml(comment.user)}
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="card">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="card-subtitle mb-1 text-muted">${comment.user.name}</h6>
                                            <small class="text-muted">${formatDate(comment.created_at)}</small>
                                        </div>
                                        <p class="card-text mb-1">${comment.review}</p>
                                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-reply me-1"></i>{{ __('Reply') }}</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.appendChild(commentElement);
                    });
                }
            });
    }

    // Generate star HTML for a given rating
    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="bi bi-star-fill"></i>';
            } else {
                stars += '<i class="bi bi-star"></i>';
            }
        }
        return stars;
    }

    // Format date to a readable format
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Submit review form
    document.getElementById('review-form')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/reviews', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset the form
                document.getElementById('review-form').reset();
                document.getElementById('rating-input').value = 0;

                // Clear rating buttons
                document.querySelectorAll('.rating-options button').forEach(btn => {
                    btn.classList.remove('btn-warning', 'btn-outline-warning');
                    btn.classList.add('btn-outline-warning');
                });

                // Show success message
                alert(data.message);

                // Reload the reviews section
                loadReviews();

                // Reload rating stats
                loadRatingStats();
            } else {
                alert('Error: ' + (data.message || 'Could not submit review'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting your review.');
        });
    });

    // Submit comment form
    document.getElementById('comment-form')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/comments', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset the form
                document.getElementById('comment-form').reset();

                // Show success message
                alert(data.message);

                // Reload the comments section
                loadComments();
            } else {
                alert('Error: ' + (data.message || 'Could not post comment'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while posting your comment.');
        });
    });

    // Load rating stats to update the display
    function loadRatingStats() {
        fetch(`/products/{{ $product->id }}/rating-stats`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the average rating display
                    const avgRatingElement = document.getElementById('average-rating');
                    if (avgRatingElement) {
                        avgRatingElement.innerHTML = '';
                        for (let i = 1; i <= 5; i++) {
                            if (i <= Math.round(data.average_rating)) {
                                avgRatingElement.innerHTML += '<i class="bi bi-star-fill"></i>';
                            } else {
                                avgRatingElement.innerHTML += '<i class="bi bi-star"></i>';
                            }
                        }

                        // Update the numerical rating
                        const avgRatingNum = avgRatingElement.nextElementSibling;
                        if (avgRatingNum) {
                            avgRatingNum.textContent = data.average_rating;
                        }

                        // Update the review count
                        document.getElementById('review-count').textContent = `${data.review_count} Reviews`;
                    }
                }
            });
    }

    // Handle favorite button clicks (for product show page)
    document.addEventListener('DOMContentLoaded', function() {
        const favoriteButton = document.querySelector('.favorite-button');

        if (favoriteButton) {
            favoriteButton.addEventListener('click', function(e) {
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
                        const textSpan = favoriteButton.querySelector('.favorite-button-text');

                        if (isFavorited) {
                            icon.classList.remove('bi-heart');
                            icon.classList.add('bi-heart-fill');
                            favoriteButton.title = 'Remove from Favorites';
                            if (textSpan) textSpan.textContent = 'Remove from Favorites';
                        } else {
                            icon.classList.remove('bi-heart-fill');
                            icon.classList.add('bi-heart');
                            favoriteButton.title = 'Add to Favorites';
                            if (textSpan) textSpan.textContent = 'Add to Favorites';
                        }
                        alert(data.message);
                    } else {
                        alert('Error: ' + (data.message || 'Could not update favorite status.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating favorite status.');
                });
            });
        }
    });

    // Add product to cart
    function addToCart(productId, quantity) {
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);

                // Update cart count in header
                updateCartCount();
            } else {
                alert('Error: ' + (data.message || 'Could not add item to cart'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the item to cart.');
        });
    }
</script>
@endpush
@endsection
