<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;

// Публичные маршруты
Route::get('/', [BookingController::class, 'index']);
Route::get('/booking', [BookingController::class, 'create']);
Route::post('/booking', [BookingController::class, 'store']);
Route::get('/api/available-times', [BookingController::class, 'getAvailableTimes']);

// Админ маршруты
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::post('/appointments/{id}/status', [AdminController::class, 'updateStatus']);
    Route::delete('/appointments/{id}', [AdminController::class, 'destroy']);
});
