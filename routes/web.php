<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Account\DashboardController as AccountDashboardController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Account\PasswordController as AccountPasswordController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

// Storefront
Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/category/{category}', [ProductController::class, 'category'])->name('category.show');
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// Cart (guest + customer)
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');

// Static pages
Route::get('/about', [StorefrontController::class, 'about'])->name('about');
Route::get('/terms', [StorefrontController::class, 'terms'])->name('terms');
Route::get('/privacy', [StorefrontController::class, 'privacy'])->name('privacy');
Route::get('/shipping', [StorefrontController::class, 'shippingInfo'])->name('shipping');
Route::get('/faq', [StorefrontController::class, 'faq'])->name('faq');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Guest-only auth routes
Route::middleware('redirect.if.auth')->group(function () {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'submit'])->name('register.submit');

    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'submit'])->name('password.email');
    Route::get('/forgot-password/sent', [ForgotPasswordController::class, 'sent'])->name('password.sent');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'submit'])->name('password.update');
});

// OTP screens (pending state required)
Route::middleware('ensure.otp.verified')->group(function () {
    Route::get('/register/verify', [OtpController::class, 'showRegistration'])->name('register.verify');
    Route::post('/register/verify', [OtpController::class, 'verifyRegistration'])->name('register.verify.submit');
    Route::post('/register/resend', [OtpController::class, 'resendRegistration'])->name('register.resend');
    Route::get('/login/verify', [OtpController::class, 'showLogin'])->name('login.verify');
    Route::post('/login/verify', [OtpController::class, 'verifyLogin'])->name('login.verify.submit');
    Route::post('/login/resend', [OtpController::class, 'resendLogin'])->name('login.resend');
});

// Authenticated logout
Route::middleware('ensure.customer')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Customer account
    Route::get('/account', [AccountDashboardController::class, 'show'])->name('account.dashboard');
    Route::get('/account/profile', [ProfileController::class, 'show'])->name('account.profile');
    Route::post('/account/profile', [ProfileController::class, 'update'])->name('account.profile.update');
    Route::get('/account/password', [AccountPasswordController::class, 'show'])->name('account.password');
    Route::post('/account/password', [AccountPasswordController::class, 'update'])->name('account.password.update');

    // Addresses
    Route::get('/account/addresses', [AddressController::class, 'index'])->name('account.addresses.index');
    Route::get('/account/addresses/create', [AddressController::class, 'create'])->name('account.addresses.create');
    Route::post('/account/addresses', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::get('/account/addresses/{address}/edit', [AddressController::class, 'edit'])->name('account.addresses.edit');
    Route::put('/account/addresses/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/account/addresses/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::post('/account/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('account.addresses.default');

    // Orders
    Route::get('/account/orders', [AccountOrderController::class, 'index'])->name('account.orders.index');
    Route::get('/account/orders/{order}', [AccountOrderController::class, 'show'])->name('account.orders.show');

    // Checkout
    Route::get('/checkout/shipping', [CheckoutController::class, 'shipping'])->name('checkout.shipping');
    Route::post('/checkout/shipping', [CheckoutController::class, 'saveShipping'])->name('checkout.shipping.save');
    Route::get('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/payment', [CheckoutController::class, 'savePayment'])->name('checkout.payment.save');
    Route::get('/checkout/review', [CheckoutController::class, 'review'])->name('checkout.review');
    Route::post('/checkout/place', [CheckoutController::class, 'place'])->name('checkout.place');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
});
