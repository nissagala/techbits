<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\MessageController;

// Admin login (public — no EnsureAdmin)
Route::get('/', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::get('/otp', [AuthController::class, 'showOtp'])->name('admin.login.otp');
Route::post('/otp', [AuthController::class, 'verifyOtp'])->name('admin.login.otp.submit');
Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('admin.login.otp.resend');

// Admin authenticated routes
Route::middleware('ensure.admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::get('/dashboard', [DashboardController::class, 'show'])->name('admin.dashboard');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('admin.products.images.destroy');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{order}/advance', [OrderController::class, 'advance'])->name('admin.orders.advance');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('admin.orders.cancel');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::post('/customers/{user}/toggle', [CustomerController::class, 'toggle'])->name('admin.customers.toggle');

    // Contact messages
    Route::get('/messages', [MessageController::class, 'index'])->name('admin.messages.index');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('admin.messages.show');
    Route::post('/messages/{message}/unread', [MessageController::class, 'markUnread'])->name('admin.messages.unread');
});
