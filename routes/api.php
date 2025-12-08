<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make sure to return JSON.
|
*/

// API routes for notifications - accessible only to authenticated admin users
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/check-new-orders', [OrderNotificationController::class, 'checkNewOrders'])->name('api.check-new-orders');
});

// Alternative - using Laravel's built-in authentication but ensuring JSON responses
Route::get('/check-new-orders', [OrderNotificationController::class, 'checkNewOrders'])
    ->middleware(['auth'])
    ->name('api.check-new-orders-json');