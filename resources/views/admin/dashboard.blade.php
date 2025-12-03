@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Admin Dashboard') }}</h2>
        <span class="text-muted">{{ __('Welcome, administrator!') }}</span>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Users') }}</h4>
                            <p class="card-text">{{ \App\Models\User::count() }}</p>
                        </div>
                        <i class="bi bi-people" style="font-size: 2.5rem;"></i>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Products') }}</h4>
                            <p class="card-text">{{ \App\Models\Product::count() }}</p>
                        </div>
                        <i class="bi bi-box" style="font-size: 2.5rem;"></i>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-sm mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Categories') }}</h4>
                            <p class="card-text">{{ \App\Models\Category::count() }}</p>
                        </div>
                        <i class="bi bi-tags" style="font-size: 2.5rem;"></i>
                    </div>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Orders') }}</h4>
                            <p class="card-text">{{ \App\Models\Order::count() }}</p>
                        </div>
                        <i class="bi bi-receipt" style="font-size: 2.5rem;"></i>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('In Stock') }}</h4>
                            <p class="card-text">{{ \App\Models\Product::where('stock_quantity', '>', 0)->count() }}</p>
                        </div>
                        <i class="bi bi-box" style="font-size: 2.5rem;"></i>
                    </div>
                    <a href="{{ route('admin.stock-management.index') }}" class="btn btn-light btn-sm mt-2">Manage</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Out of Stock') }}</h4>
                            <p class="card-text">{{ \App\Models\Product::where('stock_quantity', 0)->count() }}</p>
                        </div>
                        <i class="bi bi-x-circle" style="font-size: 2.5rem;"></i>
                    </div>
                    <a href="{{ route('admin.stock-management.index') }}" class="btn btn-light btn-sm mt-2">Manage</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title">{{ __('Quick Actions') }}</h5>
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary d-block mb-2">
                        <i class="bi bi-people me-1"></i> {{ __('Manage Users') }}
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-success d-block mb-2">
                        <i class="bi bi-box me-1"></i> {{ __('Manage Products') }}
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-info d-block mb-2">
                        <i class="bi bi-tags me-1"></i> {{ __('Manage Categories') }}
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-warning d-block mb-2">
                        <i class="bi bi-receipt me-1"></i> {{ __('Manage Orders') }}
                    </a>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <a href="{{ route('admin.stock-management.index') }}" class="btn btn-outline-info d-block mb-2">
                        <i class="bi bi-bar-chart-line me-1"></i> {{ __('Stock Log') }}
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.stock-management.summary') }}" class="btn btn-outline-success d-block mb-2">
                        <i class="bi bi-pie-chart me-1"></i> {{ __('Stock Summary') }}
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.stock-management.adjustments') }}" class="btn btn-outline-warning d-block mb-2">
                        <i class="bi bi-plus-circle me-1"></i> {{ __('Adjust Stock') }}
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-primary d-block mb-2">
                        <i class="bi bi-graph-up me-1"></i> {{ __('Analytics') }}
                    </a>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <a href="{{ route('admin.analytics.profit') }}" class="btn btn-outline-success d-block mb-2">
                        <i class="bi bi-currency-dollar me-1"></i> {{ __('Profit Analytics') }}
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.analytics.products') }}" class="btn btn-outline-info d-block mb-2">
                        <i class="bi bi-box me-1"></i> {{ __('Product Analytics') }}
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-warning d-block mb-2">
                        <i class="bi bi-gear me-1"></i> {{ __('Database Settings') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection