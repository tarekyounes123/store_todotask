@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Admin Orders Management') }}</h2>
        <div>
            <button id="refreshOrdersBtn" class="btn btn-primary me-2">
                <i class="bi bi-arrow-clockwise me-1"></i> {{ __('Refresh') }}
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Admin Dashboard') }}
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Orders') }}</h5>
            <div class="d-flex">
                <input type="text" id="searchInput" class="form-control form-control-sm me-2" placeholder="{{ __('Search orders...') }}">
                <select id="statusFilter" class="form-select form-select-sm">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="processing">{{ __('Processing') }}</option>
                    <option value="shipped">{{ __('Shipped') }}</option>
                    <option value="delivered">{{ __('Delivered') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div id="ordersListContainer">
                @include('admin.orders.partials.orders-list', ['orders' => $orders])
            </div>
        </div>
    </div>
</div>

<!-- Order Notifications Popup -->
<div id="orderNotificationPopup" class="position-fixed bottom-0 end-0 p-3" style="z-index: 10000; max-width: 350px; display: none;">
    <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-bell-fill me-2"></i>
                <span id="notificationMessage">You have a new order!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Function to show order notification popup
    function showOrderNotification(message) {
        const toastEl = document.querySelector('#orderNotificationPopup .toast');
        const toastBody = document.getElementById('notificationMessage');
        toastBody.textContent = message;

        const toast = new bootstrap.Toast(toastEl, {
            delay: 10000
        });

        // Show the popup container
        document.getElementById('orderNotificationPopup').style.display = 'block';
        toast.show();

        // Hide the container when toast is hidden
        toastEl.addEventListener('hidden.bs.toast', function () {
            document.getElementById('orderNotificationPopup').style.display = 'none';
        });
    }

    // Function to load orders via AJAX
    function loadOrders(search = '', statusFilter = '', page = 1) {
        // Show loading indicator
        const container = document.getElementById('ordersListContainer');
        container.innerHTML = '<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        // Prepare the query parameters using URLSearchParams for proper encoding
        const url = new URL('{{ route('admin.orders.getOrders') }}', window.location.origin);
        url.searchParams.append('page', page);
        if (search) url.searchParams.append('search', search);
        if (statusFilter) url.searchParams.append('status_filter', statusFilter);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest', // Important for Laravel to recognize it as AJAX
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('ordersListContainer').innerHTML = data.html;

            // Update pagination links to trigger AJAX
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const params = new URLSearchParams(link.search);
                    const page = params.get('page');
                    loadOrders(
                        document.getElementById('searchInput') ? document.getElementById('searchInput').value : '',
                        document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : '',
                        page
                    );
                });
            });
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            document.getElementById('ordersListContainer').innerHTML = '<div class="alert alert-danger">Error loading orders: ' + error.message + '</div>';
        });
    }

    // Initialize last check time
    let lastCheckTime = Date.now();

    function checkForNewOrders() {
        // Calculate time from last check in ISO format
        const lastCheckDate = new Date(lastCheckTime).toISOString();

        // Request to check for any new orders since last check
        fetch('{{ route("admin.orders.checkNew") }}?since=' + lastCheckDate, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.new_orders && data.new_orders.length > 0) {
                data.new_orders.forEach(order => {
                    showOrderNotification(`New order #${order.order_number} received! Total: $${order.total}`);
                });

                // Reload the page to show new orders with proper filters maintained
                location.reload();
            }

            // Update the last check time to now
            lastCheckTime = Date.now();
        })
        .catch(error => {
            console.error('Error checking for new orders:', error);
        });
    }

    // Setup event listeners for search and filter
    document.addEventListener('DOMContentLoaded', function() {
        // Search input
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadOrders(
                        this.value,
                        document.getElementById('statusFilter').value
                    );
                }, 500);
            });
        }

        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                loadOrders(
                    document.getElementById('searchInput').value,
                    this.value
                );
            });
        }

        // Refresh button
        const refreshBtn = document.getElementById('refreshOrdersBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                loadOrders(
                    document.getElementById('searchInput').value,
                    document.getElementById('statusFilter').value
                );
            });
        }
    });

    // Check for new orders periodically
    setInterval(checkForNewOrders, 15000); // Check every 15 seconds

    // Initial check when page loads
    setTimeout(checkForNewOrders, 3000); // Check after 3 seconds on page load
</script>
@endpush
@endsection