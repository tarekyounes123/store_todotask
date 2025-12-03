<div id="ordersListContainer">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Order #') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>
                        {{ $order->first_name }} {{ $order->last_name }}<br>
                        <small class="text-muted">{{ $order->email }}</small>
                    </td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </form>
                    </td>
                    <td>${{ number_format($order->total, 2) }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                            {{ __('View Details') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                        <p class="mt-3 text-muted">{{ __('No orders found.') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
    @endif
</div>