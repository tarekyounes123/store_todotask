<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\OrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        // Create notification for the new order
        OrderNotification::create([
            'order_id' => $event->order->id,
            'title' => 'New Order Received',
            'message' => "You have received a new order (#{$event->order->order_number}). Order total: $" . number_format($event->order->total, 2),
        ]);
    }
}
