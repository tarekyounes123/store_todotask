<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PublicProductController; // Add this import
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController; // Import FavoriteController
use App\Http\Controllers\Admin\StockManagementController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SettingsController;

Route::get('/', function () {
    return view('welcome');
});

// Public product routes
Route::get('/products', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [PublicProductController::class, 'show'])->name('products.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/create', [ChatController::class, 'create'])->name('chat.create');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{chat}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{chat}/messages', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');
    Route::get('/chat/{chat}/messages/fetch', [ChatController::class, 'fetchNewMessages'])->name('chat.fetchMessages');
    Route::delete('/chat/{chat}/messages', [ChatController::class, 'deleteMessages'])->name('chat.deleteMessages');
    Route::delete('/chat/{chat}/clear', [ChatController::class, 'clearChat'])->name('chat.clear');
    Route::get('/chat/attachment/{attachment}', [ChatController::class, 'downloadAttachment'])->name('chat.downloadAttachment');
    Route::get('/chat/attachment/{attachment}/show', [ChatController::class, 'showAttachment'])->name('chat.showAttachment');

    // Review and rating routes
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/comments', [ReviewController::class, 'storeComment'])->name('comments.store');
    Route::get('/products/{product}/reviews', [ReviewController::class, 'getReviews'])->name('products.reviews');
    Route::get('/products/{product}/comments', [ReviewController::class, 'getComments'])->name('products.comments');
    Route::get('/products/{product}/rating-stats', [ReviewController::class, 'getRatingStats'])->name('products.rating-stats');

    // Favorite products routes
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{product}/add', [FavoriteController::class, 'add'])->name('favorites.add');
    Route::post('/favorites/{product}/remove', [FavoriteController::class, 'remove'])->name('favorites.remove');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.removeItem');
    Route::delete('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::get('/cart/summary', [CartController::class, 'getCartSummary'])->name('cart.summary');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Orders routes
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrdersController::class, 'show'])->name('orders.show');
});
Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
    Route::get('orders-get', [\App\Http\Controllers\Admin\OrderController::class, 'getOrders'])->name('orders.getOrders');
    Route::get('/api/check-new-orders', [\App\Http\Controllers\Admin\OrderController::class, 'checkNewOrders'])->name('orders.checkNew');

    // Stock Management routes
    Route::get('/stock-management', [\App\Http\Controllers\Admin\StockManagementController::class, 'index'])->name('stock-management.index');
    Route::get('/stock-management/summary', [\App\Http\Controllers\Admin\StockManagementController::class, 'summary'])->name('stock-management.summary');
    Route::get('/stock-management/adjustments', [\App\Http\Controllers\Admin\StockManagementController::class, 'adjustments'])->name('stock-management.adjustments');
    Route::post('/stock-management/adjustments', [\App\Http\Controllers\Admin\StockManagementController::class, 'processAdjustment'])->name('stock-management.process-adjustment');

    // Analytics routes
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/profit', [\App\Http\Controllers\Admin\AnalyticsController::class, 'profit'])->name('analytics.profit');
    Route::get('/analytics/products', [\App\Http\Controllers\Admin\AnalyticsController::class, 'products'])->name('analytics.products');

    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/reset-table', [\App\Http\Controllers\Admin\SettingsController::class, 'resetTable'])->name('settings.reset-table');
    Route::post('/settings/backup', [\App\Http\Controllers\Admin\SettingsController::class, 'createBackup'])->name('settings.backup');
    Route::post('/settings/restore', [\App\Http\Controllers\Admin\SettingsController::class, 'restoreBackup'])->name('settings.restore');
    Route::delete('/settings/backup', [\App\Http\Controllers\Admin\SettingsController::class, 'deleteBackup'])->name('settings.delete-backup');
});

// API routes for notifications
Route::get('/api/check-new-orders', function () {
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Get orders created after the last check time (for demo purposes, we'll get all recent)
    // In a real implementation, we would track the last checked time

    // For demo, let's get all unread notifications for admin users
    $notifications = \App\Models\OrderNotification::where('read', false)
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
});

require __DIR__.'/auth.php';
