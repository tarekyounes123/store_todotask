@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Product Analytics') }}</h2>
        <div>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-bar-chart me-1"></i> {{ __('Analytics Dashboard') }}
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Best Selling Products -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Best Selling Products') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Sold') }}</th>
                                    <th>{{ __('Revenue') }}</th>
                                    <th>{{ __('Profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bestSelling as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->total_quantity_sold }}</td>
                                        <td>${{ number_format($product->total_revenue, 2) }}</td>
                                        <td><strong>${{ number_format($product->total_profit, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            {{ __('No sales data available.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Profitable Products -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Most Profitable Products') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Sold') }}</th>
                                    <th>{{ __('Revenue') }}</th>
                                    <th>{{ __('Profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mostProfitable as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->total_quantity_sold }}</td>
                                        <td>${{ number_format($product->total_revenue, 2) }}</td>
                                        <td><strong>${{ number_format($product->total_profit, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            {{ __('No profit data available.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Comparison -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ __('Product Profitability Comparison') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Selling Price') }}</th>
                            <th>{{ __('Buy Price') }}</th>
                            <th>{{ __('Profit per Unit') }}</th>
                            <th>{{ __('Profit Margin') }}</th>
                            <th>{{ __('Units Sold') }}</th>
                            <th>{{ __('Total Profit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mostProfitable as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>${{ number_format($product->buy_price, 2) }}</td>
                                <td>${{ number_format($product->price - $product->buy_price, 2) }}</td>
                                <td>{{ $product->buy_price ? number_format(($product->price - $product->buy_price) / $product->buy_price * 100, 2) : '0.00' }}%</td>
                                <td>{{ $product->total_quantity_sold }}</td>
                                <td><strong>${{ number_format($product->total_profit, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    {{ __('No product data available.') }}
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