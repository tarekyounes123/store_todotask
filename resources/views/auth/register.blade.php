@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary-gradient text-center py-4 rounded-top-4" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;">
                <h3 class="mb-0 fw-bold text-white">{{ __('Create Account') }}</h3>
                <p class="mb-0 text-light">{{ __('Join us today') }}</p>
            </div>

            <div class="card-body p-5">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="form-label fw-medium">{{ __('Full Name') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="{{ __('Enter your full name') }}">
                        </div>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-medium">{{ __('Email Address') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('Enter your email') }}">
                        </div>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-medium">{{ __('Password') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Create a password') }}">
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password-confirm" class="form-label fw-medium">{{ __('Confirm Password') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm your password') }}">
                        </div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary-gradient btn-lg">
                            {{ __('Sign Up') }}
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="d-flex align-items-center mb-4">
                    <hr class="flex-grow-1">
                    <span class="px-3 text-muted">{{ __('OR') }}</span>
                    <hr class="flex-grow-1">
                </div>

                <!-- Google Login Button -->
                <div class="d-grid gap-2">
                    <a href="{{ route('auth.google') }}" class="btn btn-outline-danger btn-lg">
                        <i class="bi bi-google me-2"></i>{{ __('Sign up with Google') }}
                    </a>
                </div>

                <div class="text-center mt-4">
                    <p class="mb-0">{{ __('Already have an account?') }}
                        <a href="{{ route('login') }}" class="text-decoration-none fw-medium">
                            {{ __('Sign In') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
