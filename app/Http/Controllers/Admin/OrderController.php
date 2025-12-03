<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        $orders = Order::with('user', 'items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Get orders for AJAX refresh
     */
    public function getOrders(Request $request)
    {
        $query = Order::with('user', 'items.product');

        // Apply status filter if provided
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')
                     ->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.orders.partials.orders-list', compact('orders'))->render(),
                'currentPage' => $orders->currentPage(),
                'lastPage' => $orders->lastPage(),
                'total' => $orders->total(),
                'perPage' => $orders->perPage()
            ]);
        }

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load('user', 'items.product');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Update timestamps based on status
        switch ($request->status) {
            case 'shipped':
                if (!$order->shipped_at) {
                    $order->update(['shipped_at' => now()]);
                }
                break;
            case 'delivered':
                if (!$order->delivered_at) {
                    $order->update(['delivered_at' => now()]);
                }
                break;
        }

        // Optionally, you can add a notification or log that the status changed
        \Log::info("Order {$order->id} status changed from {$oldStatus} to {$request->status} by admin user " . auth()->id());

        return redirect()->route('admin.orders.show', $order)
                         ->with('success', 'Order status updated successfully.');
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load('user', 'items.product');

        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage (used for updating status).
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Handle stock adjustments based on status change
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            // If order is being cancelled, restore stock
            $order->restoreStock();
        } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            // If order status is changing from cancelled back to another status,
            // reduce stock again
            $order->reduceStockFromCancelled();
        }

        $order->update(['status' => $newStatus]);

        // Update timestamps based on status
        switch ($newStatus) {
            case 'shipped':
                if (!$order->shipped_at) {
                    $order->update(['shipped_at' => now()]);
                }
                break;
            case 'delivered':
                if (!$order->delivered_at) {
                    $order->update(['delivered_at' => now()]);
                }
                break;
        }

        // Optionally, you can add a notification or log that the status changed
        \Log::info("Order {$order->id} status changed from {$oldStatus} to {$newStatus} by admin user " . auth()->id());

        return redirect()->route('admin.orders.show', $order)
                         ->with('success', 'Order status updated successfully.');
    }

    /**
     * Check for new orders since last check
     */
    public function checkNewOrders(Request $request)
    {
        // Get the timestamp from the last check or default to 15 minutes ago to avoid missing any orders
        $since = $request->input('since', now()->subMinutes(15));

        // Convert to Carbon if it's a string
        if (!is_a($since, '\Carbon\Carbon')) {
            $since = \Carbon\Carbon::parse($since);
        }

        // Get orders created since the last check that are not cancelled
        $newOrders = Order::where('created_at', '>', $since)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(10) // Limit to last 10 orders
            ->get();

        return response()->json([
            'new_orders' => $newOrders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => number_format($order->total, 2),
                    'created_at' => $order->created_at->toISOString()
                ];
            }),
            'count' => $newOrders->count(),
            'timestamp' => now()->toISOString()
        ]);
    }
}