<?php

namespace App\Providers;

use App\Models\OrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share unread notifications count with all views for logged-in users
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $userId = auth()->id();

                // Only for admin users, show order notifications
                if (auth()->user()->isAdmin()) {
                    $unreadNotificationsCount = OrderNotification::where('read', false)
                        ->count();

                    $view->with('unreadNotificationsCount', $unreadNotificationsCount);
                } else {
                    // For regular users, we might want to show their own notifications later
                    $view->with('unreadNotificationsCount', 0);
                }
            } else {
                $view->with('unreadNotificationsCount', 0);
            }
        });
    }
}
