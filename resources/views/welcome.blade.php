<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #f9fafb;
            --accent-color: #ec4899;
            --text-color: #1f2937;
            --light-bg: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #374151;
        }

        /* Navigation Styles */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), #7c3aed) !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            margin-right: 0.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 3c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM14 89c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29-60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zM30 59c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm54-11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm-16-5c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm13-13c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm12 21c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zM19 30c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zM54 39c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM65 25c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm25 35c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM65 54c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-8-14c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm-8-24c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm-2-12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-15 3c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm-16 7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm-6 31c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm3-9c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm25 20c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM30 89c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm27-43c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm2-25c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zM52 50c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM53 69c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM53 81c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm-11-6c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm-15-3c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm-6-10c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-5-16c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm17-15c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zM20 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E") repeat;
            opacity: 0.1;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-btns {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-hero {
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #e5e7eb 100%);
            color: var(--primary-color);
            border: none;
        }

        .btn-primary-gradient:hover {
            background: linear-gradient(135deg, #f3f4f6 0%, #d1d5db 100%);
            color: #3730a3;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-light {
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: transparent;
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background-color: white;
        }

        .feature-card {
            text-align: center;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), #7c3aed);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .feature-icon i {
            font-size: 2rem;
            color: white;
        }

        /* Products Section */
        .products {
            padding: 80px 0;
            background-color: #f9fafb;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        .section-title p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .product-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
        }

        .product-price {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .product-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, var(--primary-color), #7c3aed);
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .cta p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Newsletter Section */
        .newsletter {
            padding: 60px 0;
            background-color: white;
        }

        .newsletter-form {
            max-width: 500px;
            margin: 0 auto;
        }

        /* Footer */
        .footer {
            background-color: #1f2937;
            color: white;
            padding: 60px 0 30px;
        }

        .footer h5 {
            color: #f3f4f6;
            margin-bottom: 1.5rem;
        }

        .footer-links a {
            color: #d1d5db;
            text-decoration: none;
            display: block;
            margin-bottom: 0.75rem;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease;
        }

        .social-icon:hover {
            background-color: var(--primary-color);
        }

        .copyright {
            border-top: 1px solid #374151;
            padding-top: 2rem;
            margin-top: 2rem;
            text-align: center;
            color: #9ca3af;
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .hero {
                padding: 60px 0;
            }

            .hero h1 {
                font-size: 2rem;
                line-height: 1.2;
            }

            .hero p {
                font-size: 1rem;
            }

            .hero-btns {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .feature-card {
                margin-bottom: 1.5rem;
            }

            .section-title h2 {
                font-size: 1.75rem;
            }

            .product-card {
                margin-bottom: 2rem;
            }

            .footer .social-icons {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero-btns {
                flex-direction: column;
                align-items: center;
            }

            .feature-card {
                margin-bottom: 1.5rem;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .product-image {
                height: 180px;
            }

            .footer .row > [class*="col-"] {
                margin-bottom: 2rem;
            }
        }

        @media (min-width: 768px) and (max-width: 992px) {
            .hero h1 {
                font-size: 2.8rem;
            }

            .section-title h2 {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <i class="bi bi-cart me-2"></i>
                <span class="fw-bold">{{ config('app.name', 'Laravel') }}</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-cart me-1"></i>{{ \App\Models\Cart::where('user_id', auth()->id())->first()?->cartItems->sum('quantity') ?? 0 }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('cart.index') }}">View Cart</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">My Orders</a>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navbarDropdownUser" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
                                @if(Auth::user()->isAdmin())
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Panel</a>
                                @endif
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>


    <!-- Dynamic Landing Page Sections -->
    @if(isset($sections) && $sections->count() > 0)
        @foreach($sections as $section)
            @if($section->section_type == 'hero')
                <!-- Hero Section -->
                <section class="hero">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h1 class="mb-4">{{ $section->title ?? 'Discover Amazing Products at Unbeatable Prices' }}</h1>
                                <p class="mb-5">{!! App\Helpers\HtmlSanitizer::sanitize($section->content ?? 'Shop our extensive collection of premium quality items. Fast shipping, competitive prices, and exceptional customer service.') !!}</p>
                                <div class="hero-btns">
                                    <a href="{{ route('products.index') }}" class="btn btn-primary-gradient btn-hero">
                                        <i class="bi bi-shop me-2"></i> Shop Now
                                    </a>
                                    <a href="#products" class="btn btn-outline-light btn-hero">
                                        <i class="bi bi-arrow-down me-2"></i> Explore Products
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-5 mt-lg-0">
                                <div class="row">
                                    @if($section->elements->count() > 0)
                                        @foreach($section->elements->take(4) as $element)
                                            <div class="col-md-6 mb-4">
                                                <div class="card bg-transparent border-0">
                                                    <div class="card-body text-center">
                                                        <div class="feature-icon mx-auto mb-3">
                                                            <i class="{{ $element->content ?? 'bi bi-truck' }}"></i>
                                                        </div>
                                                        <h5 class="text-white">{{ $element->name ?? 'Feature' }}</h5>
                                                        <p class="text-white-50">{{ $element->attributes['title'] ?? 'Feature description' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <!-- Default features if no elements exist -->
                                        <div class="col-md-6 mb-4">
                                            <div class="card bg-transparent border-0">
                                                <div class="card-body text-center">
                                                    <div class="feature-icon mx-auto mb-3">
                                                        <i class="bi bi-truck"></i>
                                                    </div>
                                                    <h5 class="text-white">Free Shipping</h5>
                                                    <p class="text-white-50">On orders over $50</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="card bg-transparent border-0">
                                                <div class="card-body text-center">
                                                    <div class="feature-icon mx-auto mb-3">
                                                        <i class="bi bi-lock"></i>
                                                    </div>
                                                    <h5 class="text-white">Secure Payment</h5>
                                                    <p class="text-white-50">Safe and encrypted</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-transparent border-0">
                                                <div class="card-body text-center">
                                                    <div class="feature-icon mx-auto mb-3">
                                                        <i class="bi bi-headset"></i>
                                                    </div>
                                                    <h5 class="text-white">24/7 Support</h5>
                                                    <p class="text-white-50">Dedicated assistance</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-transparent border-0">
                                                <div class="card-body text-center">
                                                    <div class="feature-icon mx-auto mb-3">
                                                        <i class="bi bi-arrow-return-right"></i>
                                                    </div>
                                                    <h5 class="text-white">Easy Returns</h5>
                                                    <p class="text-white-50">30-day guarantee</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @elseif($section->section_type == 'features')
                <!-- Features Section -->
                <section id="{{ $section->name ?? 'features' }}" class="features">
                    <div class="container">
                        <div class="section-title">
                            <h2>{{ $section->title ?? 'Why Choose Us' }}</h2>
                            <p>{!! App\Helpers\HtmlSanitizer::sanitize($section->content ?? 'We provide the best shopping experience with quality products') !!}</p>
                        </div>

                        <div class="row">
                            @if($section->elements->count() > 0)
                                @foreach($section->elements as $element)
                                    <div class="col-md-4 mb-4">
                                        <div class="feature-card">
                                            <div class="feature-icon">
                                                <i class="{{ App\Helpers\HtmlSanitizer::convertIcons($element->content) ?: 'bi bi-shield-lock' }}"></i>
                                            </div>
                                            <h4>{{ $element->name ?? 'Feature' }}</h4>
                                            <p>{!! App\Helpers\HtmlSanitizer::sanitize($element->attributes['description'] ?? $element->content ?? 'Feature description') !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Default features if no elements exist -->
                                <div class="col-md-4 mb-4">
                                    <div class="feature-card">
                                        <div class="feature-icon">
                                            <i class="bi bi-shield-lock"></i>
                                        </div>
                                        <h4>Quality Guaranteed</h4>
                                        <p>All our products are carefully selected and guaranteed for quality. We stand behind every purchase with our quality promise.</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="feature-card">
                                        <div class="feature-icon">
                                            <i class="bi bi-tag"></i>
                                        </div>
                                        <h4>Best Prices</h4>
                                        <p>We offer competitive pricing on all products with regular promotions and discounts for our valued customers.</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="feature-card">
                                        <div class="feature-icon">
                                            <i class="bi bi-headset"></i>
                                        </div>
                                        <h4>Support Team</h4>
                                        <p>Our dedicated support team is available to assist you with any questions or concerns you may have.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @elseif($section->section_type == 'products')
                <!-- Products Section -->
                <section id="{{ $section->name ?? 'products' }}" class="products">
                    <div class="container">
                        <div class="section-title">
                            <h2>{{ $section->title ?? 'Featured Products' }}</h2>
                            <p>{!! App\Helpers\HtmlSanitizer::sanitize($section->content ?? 'Check out our most popular items') !!}</p>
                        </div>

                        <div class="row">
                            @php
                                // Get featured products from settings if available, otherwise get random products
                                if(isset($featuredProductsSetting) && $featuredProductsSetting) {
                                    $featuredProductIds = $featuredProductsSetting->setting_value['product_ids'] ?? [];
                                    if(!empty($featuredProductIds)) {
                                        $featuredProducts = \App\Models\Product::with('images')
                                            ->whereIn('id', $featuredProductIds)
                                            ->orderByRaw('FIELD(id, ' . implode(',', $featuredProductIds) . ')') // Maintain order
                                            ->get();
                                    } else {
                                        // Use the custom limit if available, otherwise default to 8
                                        $limit = 8;
                                        if(isset($featuredProductsLimitSetting) && $featuredProductsLimitSetting) {
                                            $limit = $featuredProductsLimitSetting->setting_value['max_limit'] ?? 8;
                                        }
                                        $featuredProducts = \App\Models\Product::with('images')->inRandomOrder()->limit($limit)->get();
                                    }
                                } else {
                                    // Use the custom limit if available, otherwise default to 8
                                    $limit = 8;
                                    if(isset($featuredProductsLimitSetting) && $featuredProductsLimitSetting) {
                                        $limit = $featuredProductsLimitSetting->setting_value['max_limit'] ?? 8;
                                    }
                                    $featuredProducts = \App\Models\Product::with('images')->inRandomOrder()->limit($limit)->get();
                                }
                            @endphp

                            @forelse($featuredProducts as $product)
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card product-card h-100">
                                    @if($product->images->first())
                                        <img src="{{ Storage::url($product->images->first()->image_path) }}" class="card-img-top product-image" alt="{{ $product->name }}">
                                    @else
                                        <img src="https://via.placeholder.com/300x200" class="card-img-top product-image" alt="{{ $product->name }}">
                                    @endif

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">{{ Str::limit($product->name, 30) }}</h5>
                                        <p class="card-text text-muted">{{ Str::limit($product->description, 60) }}</p>
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="product-price">${{ number_format($product->price, 2) }}</span>
                                                <div class="product-actions">
                                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @auth
                                                        <button
                                                            class="btn btn-sm btn-outline-success add-to-cart-btn"
                                                            data-product-id="{{ $product->id }}"
                                                            title="Add to Cart">
                                                            <i class="bi bi-cart-plus"></i>
                                                        </button>
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
                                    <p class="mt-3 text-muted">No products available at the moment.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>

                        <div class="text-center mt-5">
                            <a href="{{ route('products.index') }}" class="btn btn-primary-gradient btn-hero">
                                <i class="bi bi-arrow-right me-2"></i> View All Products
                            </a>
                        </div>
                    </div>
                </section>
            @elseif($section->section_type == 'cta')
                <!-- CTA Section -->
                <section class="cta">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 text-center">
                                <h2>{{ $section->title ?? 'Ready to Start Shopping?' }}</h2>
                                <p>{!! App\Helpers\HtmlSanitizer::sanitize($section->content ?? 'Become a member today and enjoy exclusive benefits, special discounts, and early access to new products.') !!}</p>
                                <a href="{{ route('register') }}" class="btn btn-primary-gradient btn-hero">
                                    <i class="bi bi-person-plus me-2"></i> Join Now
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            @elseif($section->section_type == 'newsletter')
                <!-- Newsletter Section -->
                <section class="newsletter">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-6">
                                <div class="text-center mb-5">
                                    <h2>{{ $section->title ?? 'Stay Updated' }}</h2>
                                    <p>{!! App\Helpers\HtmlSanitizer::sanitize($section->content ?? 'Subscribe to our newsletter to receive updates and offers.') !!}</p>
                                </div>

                                <form class="newsletter-form">
                                    <div class="input-group">
                                        <input type="email" class="form-control" placeholder="Enter your email address" required>
                                        <button class="btn btn-primary" type="submit">Subscribe</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            @else
                <!-- Default section -->
                <section class="py-5">
                    <div class="container">
                        <h2>{{ $section->title }}</h2>
                        <div>{!! App\Helpers\HtmlSanitizer::sanitize($section->content) !!}</div>
                    </div>
                </section>
            @endif
        @endforeach
    @endif

    <!-- Footer - Dynamic (Site Settings) or Static -->
    @if(isset($siteSettings) && $siteSettings)
        <!-- Site Settings Based Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-5 mb-lg-0">
                        <h5>{{ $siteSettings->setting_value['company_info']['name'] ?? config('app.name', 'Laravel') }}</h5>
                        <p>{{ $siteSettings->setting_value['company_info']['description'] ?? 'Your premier destination for quality products at unbeatable prices.' }}</p>
                        <div class="social-icons mt-4">
                            @foreach($siteSettings->setting_value['social_links'] ?? [] as $social)
                                <a href="{{ $social['url'] ?? '#' }}" class="social-icon" title="{{ $social['name'] ?? '' }}">
                                    <i class="{{ App\Helpers\HtmlSanitizer::convertIcons($social['icon']) ?: 'bi bi-facebook' }}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 mb-5 mb-md-0">
                        <h5>Shop</h5>
                        <div class="footer-links">
                            @foreach($siteSettings->setting_value['shop_links'] ?? [] as $link)
                                <a href="{{ $link['url'] ?? '#' }}">{{ $link['name'] ?? 'Link' }}</a><br>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 mb-5 mb-md-0">
                        <h5>Company</h5>
                        <div class="footer-links">
                            @foreach($siteSettings->setting_value['company_links'] ?? [] as $link)
                                <a href="{{ $link['url'] ?? '#' }}">{{ $link['name'] ?? 'Link' }}</a><br>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <h5>Contact Info</h5>
                        <div class="footer-links">
                            <p><i class="bi bi-geo-alt me-2"></i> {{ $siteSettings->setting_value['company_info']['address'] ?? '123 Commerce St' }}</p>
                            <p><i class="bi bi-telephone me-2"></i> {{ $siteSettings->setting_value['company_info']['phone'] ?? '+1 (555) 123-4567' }}</p>
                            <p><i class="bi bi-envelope me-2"></i> {{ $siteSettings->setting_value['company_info']['email'] ?? 'support@example.com' }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-top mt-4 pt-4 text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ $siteSettings->setting_value['company_info']['name'] ?? config('app.name', 'Laravel') }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    @else
        <!-- Static Footer (fallback if no site settings exist) -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-5 mb-lg-0">
                        <h5>{{ config('app.name', 'Laravel') }}</h5>
                        <p>Your premier destination for quality products at unbeatable prices. We're committed to providing excellent customer service and the best shopping experience.</p>
                        <div class="social-icons mt-4">
                            <a href="#" class="social-icon">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="bi bi-youtube"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 mb-5 mb-md-0">
                        <h5>Shop</h5>
                        <div class="footer-links">
                            <a href="{{ route('products.index') }}">All Products</a>
                            <a href="#">Featured Items</a>
                            <a href="#">New Arrivals</a>
                            <a href="#">Best Sellers</a>
                            <a href="#">Sale</a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 mb-5 mb-md-0">
                        <h5>Company</h5>
                        <div class="footer-links">
                            <a href="#">About Us</a>
                            <a href="#">Contact</a>
                            <a href="#">Careers</a>
                            <a href="#">Blog</a>
                            <a href="#">Press</a>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <h5>Contact Info</h5>
                        <div class="footer-links">
                            <p><i class="bi bi-geo-alt me-2"></i> 123 Commerce Street, City, State 12345</p>
                            <p><i class="bi bi-telephone me-2"></i> +1 (555) 123-4567</p>
                            <p><i class="bi bi-envelope me-2"></i> support@example.com</p>
                        </div>
                    </div>
                </div>

                <div class="border-top mt-4 pt-4 text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add to cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: parseInt(productId),
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart count in navbar
                        document.querySelectorAll('.dropdown-toggle[aria-label="Cart"]').forEach(element => {
                            // This part updates the cart count in the navbar
                            const cartBadge = element.querySelector('.badge');
                            if (cartBadge) {
                                cartBadge.textContent = data.cart_count;
                            }
                        });

                        // Show success message
                        alert('Product added to cart successfully!');
                    } else {
                        alert('Error: ' + (data.message || 'Could not add product to cart'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding product to cart.');
                });
            });
        });
    </script>
</body>
</html>