@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Email Verification Successful') }}</div>

                <div class="card-body text-center">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h4 class="text-success">{{ __('Email Verified Successfully!') }}</h4>
                    <p class="mb-4">{{ __('Your email address has been successfully verified.') }}</p>

                    <div class="mb-4">
                        <p>{{ __('You may now access all features of our application.') }}</p>
                    </div>

                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        {{ __('Go to Dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection