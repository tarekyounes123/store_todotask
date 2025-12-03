@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Order Details') }}</h2>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Orders') }}
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                {{ __('Admin Dashboard') }}
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h3>{{ __('Order #') . $order->order_number }}</h3>
                    <p class="text-muted mb-1">{{ __('Placed on') }} {{ $order->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="d-inline">
                        @csrf
                        @method('PUT')
                        <select name="status" class="form-select d-inline w-auto" onchange="this.form.submit()">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </form>
                    <span class="badge
                        @if($order->status == 'pending') bg-warning
                        @elseif($order->status == 'processing') bg-info
                        @elseif($order->status == 'shipped') bg-primary
                        @elseif($order->status == 'delivered') bg-success
                        @elseif($order->status == 'cancelled') bg-danger
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-3">{{ __('Order Items') }}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'Product Name' }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4 mb-3">{{ __('Customer Information') }}</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('Full Name:') }}</strong> {{ $order->first_name }} {{ $order->last_name }}</p>
                            <p><strong>{{ __('Email:') }}</strong> {{ $order->email }}</p>
                            @if($order->phone)
                                <p><strong>{{ __('Phone:') }}</strong> {{ $order->phone }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('Special Instructions:') }}</strong></p>
                            <p>{{ $order->special_instructions ?: 'None provided' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">{{ __('Order Summary') }}</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __('Subtotal') }}</span>
                                    <strong>${{ number_format($order->subtotal, 2) }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __('Shipping') }}</span>
                                    <strong>${{ number_format($order->shipping_cost, 2) }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __('Total') }}</span>
                                    <strong>${{ number_format($order->total, 2) }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">{{ __('Shipping Address') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>{{ $order->first_name . ' ' . $order->last_name }}</strong></p>
                            <p class="mb-1">{{ $order->address }}</p>
                            <p class="mb-1">{{ $order->city }}, {{ $order->state }} {{ $order->zip_code }}</p>
                            <p class="mb-1">{{ $order->country }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection