@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Checkout') }}</li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="mb-4">{{ __('Checkout') }}</h2>

            <div class="row">
                <div class="col-md-8">
                    <form method="POST" action="{{ route('checkout.process') }}">
                        @csrf

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ __('Shipping Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label fw-bold">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                               id="first_name" name="first_name"
                                               value="{{ old('first_name', $user->first_name ?? '') }}"
                                               required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label fw-bold">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                               id="last_name" name="last_name"
                                               value="{{ old('last_name', $user->last_name ?? '') }}"
                                               required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email"
                                           value="{{ old('email', $user->email ?? '') }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-bold">{{ __('Phone Number') }}</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone"
                                           value="{{ old('phone', $user->phone_number ?? '') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label fw-bold">{{ __('Address') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                           id="address" name="address"
                                           value="{{ old('address', $user->address ?? '') }}"
                                           required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label fw-bold">{{ __('City') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                               id="city" name="city"
                                               value="{{ old('city', $user->city ?? '') }}"
                                               required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="state" class="form-label fw-bold">{{ __('State/Province') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('state') is-invalid @enderror"
                                               id="state" name="state"
                                               value="{{ old('state', $user->state ?? '') }}"
                                               required>
                                        @error('state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="zip_code" class="form-label fw-bold">{{ __('ZIP/Postal Code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror"
                                               id="zip_code" name="zip_code"
                                               value="{{ old('zip_code', $user->zip_code ?? '') }}"
                                               required>
                                        @error('zip_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="country" class="form-label fw-bold">{{ __('Country') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('country') is-invalid @enderror"
                                            id="country" name="country" required>
                                        <option value="">{{ __('Select Country') }}</option>
                                        @include('partials.countries', ['selected' => old('country', $user->country ?? '')])
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ __('Additional Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="special_instructions" class="form-label fw-bold">{{ __('Special Instructions') }}</label>
                                    <textarea class="form-control @error('special_instructions') is-invalid @enderror"
                                              id="special_instructions" name="special_instructions"
                                              rows="3"
                                              placeholder="{{ __('Any special delivery instructions, notes about your order, etc.') }}">{{ old('special_instructions') }}</textarea>
                                    @error('special_instructions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary me-md-2">
                                {{ __('Back to Cart') }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                {{ __('Place Order') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">{{ __('Order Summary') }}</h5>
                        </div>
                        <div class="card-body">
                            <!-- Cart summary will be loaded here -->
                            <div id="cart-summary">
                                @include('partials.cart-summary')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
