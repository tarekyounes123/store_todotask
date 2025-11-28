<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('welcome');
});

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
});
Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
