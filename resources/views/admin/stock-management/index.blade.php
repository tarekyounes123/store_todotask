@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Stock Movement Log') }}</h2>
        <div>
            <a href="{{ route('admin.stock-management.adjustments') }}" class="btn btn-outline-success me-2">
                <i class="bi bi-plus-circle me-1"></i> {{ __('Manual Adjustment') }}
            </a>
            <a href="{{ route('admin.stock-management.summary') }}" class="btn btn-outline-info me-2">
                <i class="bi bi-bar-chart me-1"></i> {{ __('Summary') }}
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ __('Filters') }}</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.stock-management.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="product_id" class="form-label">{{ __('Product') }}</label>
                        <select name="product_id" id="product_id" class="form-select">
                            <option value="">{{ __('All Products') }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="movement_type" class="form-label">{{ __('Movement Type') }}</label>
                        <select name="movement_type" id="movement_type" class="form-select">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="in" {{ request('movement_type') == 'in' ? 'selected' : '' }}>{{ __('Stock In') }}</option>
                            <option value="out" {{ request('movement_type') == 'out' ? 'selected' : '' }}>{{ __('Stock Out') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="movement_reason" class="form-label">{{ __('Reason') }}</label>
                        <select name="movement_reason" id="movement_reason" class="form-select">
                            <option value="">{{ __('All Reasons') }}</option>
                            <option value="order" {{ request('movement_reason') == 'order' ? 'selected' : '' }}>{{ __('Order') }}</option>
                            <option value="cancelled_order" {{ request('movement_reason') == 'cancelled_order' ? 'selected' : '' }}>{{ __('Cancelled Order') }}</option>
                            <option value="restock" {{ request('movement_reason') == 'restock' ? 'selected' : '' }}>{{ __('Restock') }}</option>
                            <option value="adjustment" {{ request('movement_reason') == 'adjustment' ? 'selected' : '' }}>{{ __('Adjustment') }}</option>
                            <option value="return" {{ request('movement_reason') == 'return' ? 'selected' : '' }}>{{ __('Return') }}</option>
                            <option value="damaged" {{ request('movement_reason') == 'damaged' ? 'selected' : '' }}>{{ __('Damaged') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">{{ __('From Date') }}</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">{{ __('To Date') }}</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">{{ __('Apply Filters') }}</button>
                        <a href="{{ route('admin.stock-management.index') }}" class="btn btn-outline-secondary">{{ __('Clear Filters') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stock Movements Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ __('Stock Movement Log') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Movement Type') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Reason') }}</th>
                            <th>{{ __('Order') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td>
                                <a href="{{ route('admin.products.edit', $movement->product) }}" class="text-decoration-none">
                                    {{ $movement->product->name ?? 'N/A' }}
                                </a>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($movement->movement_type == 'in') bg-success 
                                    @else bg-danger 
                                    @endif">
                                    {{ ucfirst($movement->movement_type) }}
                                </span>
                            </td>
                            <td>{{ $movement->quantity }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $movement->movement_reason)) }}</td>
                            <td>
                                @if($movement->order)
                                    <a href="{{ route('admin.orders.show', $movement->order) }}">
                                        {{ $movement->order->order_number }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $movement->description }}</td>
                            <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
                                <p class="mt-2">{{ __('No stock movements found.') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $movements->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection