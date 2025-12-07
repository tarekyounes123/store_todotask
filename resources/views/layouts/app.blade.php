<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Dynamic Site Title -->
    @php
        try {
            $titleSetting = \App\Models\SiteSetting::where('setting_key', \App\Models\SiteSetting::TITLE_SETTINGS_KEY)->first();
            $siteTitle = $titleSetting ? ($titleSetting->setting_value['app_name'] ?? config('app.name', 'ToDoTask')) : config('app.name', 'ToDoTask');
        } catch (\Exception $e) {
            $siteTitle = config('app.name', 'ToDoTask');
        }
    @endphp

    <title>{{ $siteTitle }}</title>

    <!-- Favicon - Always use the one from public directory -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #f9fafb;
            --accent-color: #ec4899;
            --text-color: #1f2937;
            --light-bg: #f8fafc;
        }

        body {
            font-family: 'Roboto', sans-serif;
            overflow-x: hidden;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), #7c3aed) !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
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
            padding: 0.5rem 0.75rem !important;
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

        .bg-light-gradient {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        }

        .main-container {
            flex: 1;
            background-color: var(--light-bg);
            min-height: calc(100vh - 120px); /* Account for navbar and footer */
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #7c3aed);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, var(--primary-color), #7c3aed);
            border: none;
            color: white;
        }

        .btn-primary-gradient:hover {
            background: linear-gradient(135deg, #4338ca, #6d28d9);
            color: white;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .container-custom {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .nav-link {
                padding: 0.5rem !important;
                font-size: 0.9rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .container-custom {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
        }

        @media (max-width: 768px) {
            .navbar-collapse {
                max-height: 70vh;
                overflow-y: auto;
            }

            .main-container {
                min-height: calc(100vh - 150px);
            }
        }

        @media (min-width: 768px) and (max-width: 992px) {
            .container-custom {
                padding-left: 1.25rem !important;
                padding-right: 1.25rem !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="d-flex flex-column min-vh-100">
        @section('navbar')
        <nav class="navbar navbar-expand-md navbar-dark sticky-top">
            <div class="container-fluid container-custom">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-cart me-1"></i>{{ $siteTitle }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="bi bi-shop me-1"></i> {{ __('Products') }}
                            </a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                                    <i class="bi bi-list-check me-1"></i> {{ __('Tasks') }}
                                </a>
                            </li>
                            @if(Auth::user()->isAdmin())
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ str_starts_with(request()->route()->getName(), 'admin.') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-journal-check me-1"></i> {{ __('Admin') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-1"></i> {{ __('Dashboard') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                            <i class="bi bi-people me-1"></i> {{ __('User Management') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                            <i class="bi bi-tags me-1"></i> {{ __('Category Management') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                                            <i class="bi bi-box-seam me-1"></i> {{ __('Product Management') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                                            <i class="bi bi-receipt me-1"></i> {{ __('Order Management') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.stock-management.*') ? 'active' : '' }}" href="{{ route('admin.stock-management.index') }}">
                                            <i class="bi bi-bar-chart-line me-1"></i> {{ __('Stock Management') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}" href="{{ route('admin.analytics.index') }}">
                                            <i class="bi bi-bar-chart me-1"></i> {{ __('Analytics') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                                            <i class="bi bi-gear me-1"></i> {{ __('Settings') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.landing-page-sections.*') ? 'active' : '' }}" href="{{ route('admin.landing-page-sections.index') }}">
                                            <i class="bi bi-layout-wtf me-1"></i> {{ __('Landing Pages') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.landing-page-builder.*') ? 'active' : '' }}" href="{{ route('admin.landing-page-builder.index') }}">
                                            <i class="bi bi-palette me-1"></i> {{ __('Page Builder') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}" href="{{ route('admin.site-settings.edit') }}">
                                            <i class="bi bi-gear-fill me-1"></i> {{ __('Site Settings') }}
                                        </a></li>
                                    </ul>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}">
                                    <i class="bi bi-chat-dots me-1"></i> {{ __('Chat') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}" href="{{ route('favorites.index') }}">
                                    <i class="bi bi-heart me-1"></i> {{ __('Favorites') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                                    <i class="bi bi-receipt me-1"></i> {{ __('My Orders') }}
                                    @auth
                                        @if(auth()->user()->isAdmin())
                                            <!-- Admin can see total orders count -->
                                            <span class="order-notification-badge ms-1 badge bg-danger" style="display: none;" id="new-order-count">0</span>
                                        @endif
                                    @endauth
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <!-- Cart Link -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cart.index') }}" title="Shopping Cart">
                                    <i class="bi bi-cart me-1"></i>
                                    <span class="cart-count">0</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @if (Auth::user()->profile_picture)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="rounded-circle me-1" style="width: 30px; height: 30px; object-fit: cover;">
                                    @else
                                        <i class="bi bi-person-circle me-1"></i>
                                    @endif
                                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person me-1"></i> {{ __('Profile') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">
                                        <i class="bi bi-list-check me-1"></i> {{ __('My Orders') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-1"></i> {{ __('Logout') }}
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
        @show
        <div class="main-container">
            <!-- Flash Messages -->
            @if(session('status'))
                <div class="container mt-3">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="container mt-3">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="container mt-3">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
        // Update cart count when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });

        // Function to update the cart count in the navbar
        function updateCartCount() {
            // Check if we're on the cart page, and skip the AJAX call to avoid unnecessary requests
            if (window.location.pathname === '/cart' || window.location.pathname.includes('/cart')) {
                return;
            }

            fetch('/cart/summary', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cart_count;
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching cart count:', error);
            });
        }
    </script>
    <!-- Order Notifications Popup -->
    <div id="orderNotificationPopup" class="position-fixed bottom-0 end-0 p-3" style="z-index: 10000; max-width: 350px; display: none;">
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-bell-fill me-2"></i>
                    <span id="notificationMessage">You have a new order!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        // Function to show order notification popup
        function showOrderNotification(message) {
            const toastEl = document.querySelector('#orderNotificationPopup .toast');
            const toastBody = document.getElementById('notificationMessage');
            toastBody.textContent = message;

            const toast = new bootstrap.Toast(toastEl, {
                delay: 10000
            });

            // Show the popup container
            document.getElementById('orderNotificationPopup').style.display = 'block';
            toast.show();

            // Hide the container when toast is hidden
            toastEl.addEventListener('hidden.bs.toast', function () {
                document.getElementById('orderNotificationPopup').style.display = 'none';
            });
        }

        // Listen for order created events via AJAX polling
        let lastNotificationCheck = localStorage.getItem('lastNotificationCheck') || Date.now();

        function checkForNewOrders() {
            // This would normally be a WebSocket or SSE implementation
            // For simplicity, we'll use a periodic AJAX call
            fetch('/api/check-new-orders', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.new_orders && data.new_orders.length > 0) {
                    data.new_orders.forEach(order => {
                        showOrderNotification(`New order #${order.order_number} received! Total: $${order.total}`);
                    });
                }
            })
            .catch(error => {
                console.error('Error checking for new orders:', error);
            });
        }

        // Check for new orders periodically
        setInterval(checkForNewOrders, 30000); // Check every 30 seconds

        // Initial check
        setTimeout(checkForNewOrders, 5000); // Check after 5 seconds on page load
    </script>

    @stack('scripts')
</body>
</html>