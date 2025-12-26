<?php

use App\Http\Controllers\ProductVariantController; 
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Admin\StockManagementController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\ProductController; // Corrected namespace

Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

// Contact routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');

// Public products
Route::get('/products', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [PublicProductController::class, 'show'])->name('products.show');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat routes
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

    // Reviews & comments
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/comments', [ReviewController::class, 'storeComment'])->name('comments.store');
    Route::get('/products/{product}/reviews', [ReviewController::class, 'getReviews'])->name('products.reviews');
    Route::get('/products/{product}/comments', [ReviewController::class, 'getComments'])->name('products.comments');
    Route::get('/products/{product}/rating-stats', [ReviewController::class, 'getRatingStats'])->name('products.rating-stats');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{product}/add', [FavoriteController::class, 'add'])->name('favorites.add');
    Route::post('/favorites/{product}/remove', [FavoriteController::class, 'remove'])->name('favorites.remove');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.removeItem');
    Route::delete('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::get('/cart/summary', [CartController::class, 'getCartSummary'])->name('cart.summary');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Orders
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrdersController::class, 'show'])->name('orders.show');

    // Tasks
    Route::resource('tasks', TaskController::class);
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () { return view('admin.dashboard'); })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
    Route::get('orders-get', [\App\Http\Controllers\Admin\OrderController::class, 'getOrders'])->name('orders.getOrders');
    Route::get('/api/check-new-orders', [\App\Http\Controllers\Admin\OrderController::class, 'checkNewOrders'])->name('orders.checkNew');

    // Stock Management
    Route::get('/stock-management', [StockManagementController::class, 'index'])->name('stock-management.index');
    Route::get('/stock-management/summary', [StockManagementController::class, 'summary'])->name('stock-management.summary');
    Route::get('/stock-management/adjustments', [StockManagementController::class, 'adjustments'])->name('stock-management.adjustments');
    Route::post('/stock-management/adjustments', [StockManagementController::class, 'processAdjustment'])->name('stock-management.process-adjustment');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/profit', [AnalyticsController::class, 'profit'])->name('analytics.profit');
    Route::get('/analytics/products', [AnalyticsController::class, 'products'])->name('analytics.products');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/reset-table', [SettingsController::class, 'resetTable'])->name('settings.reset-table');
    Route::post('/settings/backup', [SettingsController::class, 'createBackup'])->name('settings.backup');
    Route::post('/settings/restore', [SettingsController::class, 'restoreBackup'])->name('settings.restore');
    Route::delete('/settings/backup', [SettingsController::class, 'deleteBackup'])->name('settings.delete-backup');

    // Landing Page
    Route::resource('landing-page-sections', \App\Http\Controllers\Admin\LandingPageSectionController::class);
    Route::post('/landing-page-sections/sort', [\App\Http\Controllers\Admin\LandingPageSectionController::class, 'sort'])->name('landing-page-sections.sort');
    Route::resource('landing-page-elements', \App\Http\Controllers\Admin\LandingPageElementController::class);
    Route::post('/landing-page-elements/sort', [\App\Http\Controllers\Admin\LandingPageElementController::class, 'sort'])->name('landing-page-elements.sort');
    Route::get('/landing-page-builder', [\App\Http\Controllers\Admin\LandingPageSectionController::class, 'builder'])->name('landing-page-builder.index');
    Route::post('/landing-page-builder/save', [\App\Http\Controllers\Admin\LandingPageSectionController::class, 'saveBuilder'])->name('landing-page-builder.save');
    Route::get('/landing-page-builder/load', [\App\Http\Controllers\Admin\LandingPageSectionController::class, 'loadBuilder'])->name('landing-page-builder.load');

    // Site settings
    Route::get('/site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'edit'])->name('site-settings.edit');
    Route::put('/site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('site-settings.update');

    // Product attributes
    Route::resource('attributes', AttributeController::class);
    Route::get('attributes/{attribute}/terms/create', [AttributeController::class, 'createTerm'])->name('attributes.terms.create');
    Route::post('attributes/{attribute}/terms', [AttributeController::class, 'storeTerm'])->name('attributes.terms.store');
    Route::get('attributes/{attribute}/terms/{term}/edit', [AttributeController::class, 'editTerm'])->name('attributes.terms.edit');
    Route::put('attributes/{attribute}/terms/{term}', [AttributeController::class, 'updateTerm'])->name('attributes.terms.update');
    Route::delete('attributes/{attribute}/terms/{term}', [AttributeController::class, 'destroyTerm'])->name('attributes.terms.destroy');

    // Product Variants
    Route::resource('product-variants', ProductVariantController::class);
});

// API notification route
Route::get('/api/check-new-orders', function () {
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    $controller = new \App\Http\Controllers\Api\OrderNotificationController();
    return $controller->checkNewOrders(request());
})->name('api.check-new-orders');

require __DIR__.'/auth.php';
