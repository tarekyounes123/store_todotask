@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Profit Analytics') }}</h2>
        <div>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-bar-chart me-1"></i> {{ __('Analytics Dashboard') }}
            </a>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.analytics.profit') }}">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Time Period') }}</label>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="period" id="monthlyPeriod" value="monthly" {{ request('period', 'monthly') === 'monthly' ? 'checked' : '' }}>
                            <label class="form-check-label" for="monthlyPeriod">{{ __('Monthly') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="period" id="yearlyPeriod" value="yearly" {{ request('period') === 'yearly' ? 'checked' : '' }}>
                            <label class="form-check-label" for="yearlyPeriod">{{ __('Yearly') }}</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> {{ __('Apply Filter') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Profit Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Total Revenue') }}</h4>
                            <p class="card-text">${{ number_format($profitData->sum('revenue'), 2) }}</p>
                        </div>
                        <i class="bi bi-currency-dollar" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Total Profit') }}</h4>
                            <p class="card-text">${{ number_format($profitData->sum('profit'), 2) }}</p>
                        </div>
                        <i class="bi bi-graph-up" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Total Cost of Goods') }}</h4>
                            <p class="card-text">${{ number_format($profitData->sum('cost_of_goods_sold'), 2) }}</p>
                        </div>
                        <i class="bi bi-cart" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Chart -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ __('Revenue, Profit & Cost Trend') }}</h5>
        </div>
        <div class="card-body">
            <canvas id="profitChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Profit Data Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ __('Profit Details') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Period') }}</th>
                            <th>{{ __('Revenue') }}</th>
                            <th>{{ __('Cost of Goods') }}</th>
                            <th>{{ __('Profit') }}</th>
                            <th>{{ __('Profit Margin') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profitData as $data)
                            <tr>
                                <td>
                                    @if(request('period', 'monthly') === 'monthly')
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $data->month)->format('M Y') }}
                                    @else
                                        {{ $data->year }}
                                    @endif
                                </td>
                                <td>${{ number_format($data->revenue, 2) }}</td>
                                <td>${{ number_format($data->cost_of_goods_sold, 2) }}</td>
                                <td><strong>${{ number_format($data->profit, 2) }}</strong></td>
                                <td>
                                    {{ $data->revenue > 0 ? number_format(($data->profit / $data->revenue) * 100, 2) : '0.00' }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    {{ __('No profit data available for the selected period.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Profit Chart
    const profitCtx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(profitCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($profitData as $data)
                    @if(request('period', 'monthly') === 'monthly')
                        '{{ \Carbon\Carbon::createFromFormat('Y-m', $data->month)->format('M Y') }}',
                    @else
                        '{{ $data->year }}',
                    @endif
                @endforeach
            ],
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: [
                        @foreach($profitData as $data)
                            {{ $data->revenue }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Profit ($)',
                    data: [
                        @foreach($profitData as $data)
                            {{ $data->profit }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Cost of Goods ($)',
                    data: [
                        @foreach($profitData as $data)
                            {{ $data->cost_of_goods_sold }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
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
</script>
@endpush
@endsection