@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="mb-3">{{ __('Order Confirmed!') }}</h2>
                    <p class="lead mb-4">{{ __('Thank you for your order. Your order has been placed successfully.') }}</p>
                    
                    <div class="alert alert-success" role="alert">
                        {{ __('Your order number is #') . date('Ymd') . auth()->id() . rand(1000, 9999) }}.
                        {{ __('Your order will be processed shortly.') }}
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-primary me-2">
                            {{ __('Continue Shopping') }}
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            {{ __('Go to Dashboard') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection