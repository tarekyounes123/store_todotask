<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ToDotask') }}</title>
    <!-- Fonts -->
    <link href="https://cdn.jsdelivr.net/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .hero {
            background: #f8f9fa;
            padding: 3rem 0;
        }
        .hero h1 {
            font-weight: 700;
            font-size: 2.5rem;
        }
        .hero .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body class="antialiased">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid px-3 px-md-0">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">ToDoTask</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary me-2">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="btn btn-primary me-2 mb-2 mb-lg-0">Log in</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-secondary mb-2 mb-lg-0">Register</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero text-center">
        <div class="container px-3 px-md-0">
            <h1 class="mb-4">{{ __('Welcome to ToDotask!') }}</h1>
            <p class="lead mb-4">{{ __('Your ultimate solution for managing tasks, collaborating with your team, and staying productive.') }}</p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Get Started Today!') }}</a>
        </div>
    </div>

    <div class="container mt-5 px-3 px-md-0">
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <h2>{{ __('Organize Your Work') }}</h2>
                <p>{{ __('Create, categorize, and track your tasks with ease. Never miss a deadline again with our intuitive task management system.') }}</p>
            </div>
            <div class="col-12 col-md-4">
                <h2>{{ __('Collaborate Seamlessly') }}</h2>
                <p>{{ __('Assign tasks to team members, track progress, and communicate directly within each task. Boost team productivity and efficiency.') }}</p>
            </div>
            <div class="col-12 col-md-4">
                <h2>{{ __('Stay Updated') }}</h2>
                <p>{{ __('Receive real-time notifications for task updates, messages, and deadlines. Always be in the loop, no matter where you are.') }}</p>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 py-3 bg-light">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'ToDotask') }}. {{ __('All rights reserved.') }}</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>