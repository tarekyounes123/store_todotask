@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Stock Management Summary') }}</h2>
        <div>
            <a href="{{ route('admin.stock-management.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-list me-1"></i> {{ __('Stock Log') }}
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Total Stock In') }}</h4>
                            <p class="card-text">{{ number_format($totalIn) }}</p>
                        </div>
                        <i class="bi bi-arrow-down-circle" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Total Stock Out') }}</h4>
                            <p class="card-text">{{ number_format($totalOut) }}</p>
                        </div>
                        <i class="bi bi-arrow-up-circle" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white 
                @if($netChange >= 0) bg-success 
                @else bg-warning 
                @endif">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Net Change') }}</h4>
                            <p class="card-text">{{ number_format($netChange) }}</p>
                        </div>
                        <i class="bi 
                            @if($netChange >= 0) bi-arrow-down-short 
                            @else bi-arrow-up-short 
                            @endif" 
                            style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ __('Recent Stock Movements') }}</h5>
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
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMovements as $movement)
                        <tr>
                            <td>{{ $movement->product->name ?? 'N/A' }}</td>
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
                            <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                {{ __('No recent stock movements.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ __('Stock Movements by Reason') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Reason') }}</th>
                            <th>{{ __('Movement Type') }}</th>
                            <th>{{ __('Total Quantity') }}</th>
                            <th>{{ __('Count') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movementsByReason as $movement)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $movement->movement_reason)) }}</td>
                            <td>
                                <span class="badge 
                                    @if($movement->movement_type == 'in') bg-success 
                                    @else bg-danger 
                                    @endif">
                                    {{ ucfirst($movement->movement_type) }}
                                </span>
                            </td>
                            <td>{{ $movement->total_quantity }}</td>
                            <td>{{ $movement->count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                {{ __('No stock movement statistics available.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection