@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header bg-primary-gradient text-white">
                    <h4 class="mb-0">{{ __('Dashboard') }}</h4>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h5 class="card-title">{{ __('Welcome back,') }} {{ Auth::user()->name }}!</h5>
                    <p class="card-text">{{ __('You are logged in to your account.') }}</p>

                    <div class="mt-4 d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="{{ route('tasks.index') }}" class="btn btn-primary-gradient me-2 mb-2 mb-md-0 flex-fill">
                            <i class="bi bi-list-task me-2"></i>{{ __('View Tasks') }}
                        </a>
                        <a href="{{ route('chat.index') }}" class="btn btn-outline-primary flex-fill">
                            <i class="bi bi-chat-dots me-2"></i>{{ __('View Chats') }}
                        </a>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-cart text-primary" style="font-size: 2rem;"></i>
                                    <h5 class="card-title mt-2">{{ __('My Orders') }}</h5>
                                    <p class="card-text">{{ \App\Models\Order::where('user_id', Auth::id())->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-heart text-danger" style="font-size: 2rem;"></i>
                                    <h5 class="card-title mt-2">{{ __('Favorites') }}</h5>
                                    <p class="card-text">{{ Auth::user()->favoriteProducts()->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
