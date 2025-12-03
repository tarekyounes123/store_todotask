@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h2 class="mb-1">{{ __('Order Details') }}</h2>
                    <p class="text-muted mb-2">{{ __('Order #') . $order->order_number }}</p>
                </div>
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
                            @if($order->phone)
                                <p class="mb-1">{{ $order->phone }}</p>
                            @endif
                            <p class="mb-0">{{ $order->email }}</p>
                        </div>
                    </div>

                    @if($order->special_instructions)
                    <div class="card mt-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">{{ __('Special Instructions') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $order->special_instructions }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection