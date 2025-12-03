@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Business Analytics') }}</h2>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-speedometer2 me-1"></i> {{ __('Admin Dashboard') }}
            </a>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Total Orders') }}</h4>
                            <p class="card-text">{{ number_format($totalOrders) }}</p>
                        </div>
                        <i class="bi bi-receipt" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Total Revenue') }}</h4>
                            <p class="card-text">${{ number_format($totalRevenue, 2) }}</p>
                        </div>
                        <i class="bi bi-currency-dollar" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Total Profit') }}</h4>
                            <p class="card-text">${{ number_format($totalProfit, 2) }}</p>
                        </div>
                        <i class="bi bi-graph-up" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Avg. Order Value') }}</h4>
                            <p class="card-text">${{ number_format($avgOrderValue, 2) }}</p>
                        </div>
                        <i class="bi bi-bar-chart" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales Chart -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Sales Trend (Last 30 Days)') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Recent Orders') }}</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($recentOrders as $order)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-0">{{ $order->order_number }}</h6>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <strong>${{ number_format($order->total, 2) }}</strong>
                                        <div class="small">
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
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                {{ __('No recent orders') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Profit by Category -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Profit by Category') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryProfitChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Profit Products -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Top Profit Products') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Sold') }}</th>
                                    <th>{{ __('Profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProfitProducts as $product)
                                    <tr>
                                        <td>{{ $product->product_name }}</td>
                                        <td>{{ $product->total_quantity }}</td>
                                        <td><strong>${{ number_format($product->profit, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">{{ __('No profit data available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($salesData as $data)
                    '{{ $data->date }}',
                @endforeach
            ],
            datasets: [{
                label: 'Sales ($)',
                data: [
                    @foreach($salesData as $data)
                        {{ $data->total_sales }},
                    @endforeach
                ],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Category Profit Chart
    const categoryCtx = document.getElementById('categoryProfitChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($profitByCategory as $category)
                    '{{ $category->category_name }}',
                @endforeach
            ],
            datasets: [{
                label: 'Profit ($)',
                data: [
                    @foreach($profitByCategory as $category)
                        {{ $category->profit }},
                    @endforeach
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection