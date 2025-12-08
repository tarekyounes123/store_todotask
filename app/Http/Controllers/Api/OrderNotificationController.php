<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\OrderNotification; // Assuming this model exists

class OrderNotificationController extends Controller
{
    /**
     * Check for new orders and return JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNewOrders(Request $request): JsonResponse
    {
        // Check if user is authenticated and is admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        try {
            // Get orders/notifications created after the last check time
            $notifications = OrderNotification::where('read', false)
                ->orderBy('created_at', 'desc')
                ->take(5)  // Limit to last 5
                ->get();

            // Mark them as read after returning them
            $notifications->each(function($notification) {
                $notification->update(['read' => true, 'read_at' => now()]);
            });

            return response()->json([
                'new_orders' => $notifications->map(function($notification) {
                    return [
                        'id' => $notification->id,
                        'order_number' => $notification->order->order_number ?? 'unknown',
                        'total' => $notification->order->total ?? 0,
                        'message' => $notification->message
                    ];
                }),
                'count' => $notifications->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error'
            ], 500);
        }
    }
}