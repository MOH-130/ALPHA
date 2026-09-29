<?php

use App\Core\Router;
use App\Controllers\StoreController;
use App\Controllers\CustomerController;
use App\Controllers\AuthController;

// Storefront Public Routes
Router::group(['middleware' => ['locale', 'visitor', 'price_visibility']], function() {
    Router::get('/', [StoreController::class, 'index'])->name('home');
    Router::get('/shop', [StoreController::class, 'catalog'])->name('catalog');
    Router::get('/catalog', [StoreController::class, 'catalog']);
    Router::get('/products', [StoreController::class, 'catalog']);
    Router::get('/category/{slug}', [StoreController::class, 'category'])->name('category');
    Router::get('/brand/{slug}', [StoreController::class, 'brand'])->name('brand');
    Router::get('/product/{slug}', [StoreController::class, 'product'])->name('product.show');
    Router::post('/product/{slug}/review', [StoreController::class, 'storeReview'])->name('product.review');

    // Cart & Checkout
    Router::get('/cart', [StoreController::class, 'cart'])->name('cart');
    Router::get('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Router::post('/checkout/place-order', [StoreController::class, 'placeOrder'])->name('checkout.place');
    Router::get('/order/success/{orderNumber}', [StoreController::class, 'orderSuccess'])->name('order.success');

    // Wishlist & Compare
    Router::get('/wishlist', [StoreController::class, 'wishlist'])->name('wishlist');
    Router::get('/compare', [StoreController::class, 'compare'])->name('compare');

    // Public Pages & Info
    Router::get('/page/{slug}', [StoreController::class, 'page'])->name('page');
    Router::get('/contact', [StoreController::class, 'contact'])->name('contact');
    Router::post('/contact', [StoreController::class, 'contact'])->name('contact.submit');
    Router::get('/track-order', [StoreController::class, 'trackOrder'])->name('track.order');

    // Locales & SEO
    Router::get('/lang/{locale}', [StoreController::class, 'setLocale'])->name('set.locale');
    Router::get('/currency/{currency}', [StoreController::class, 'setCurrency'])->name('set.currency');
    Router::get('/sitemap.xml', [StoreController::class, 'sitemap'])->name('sitemap');
    Router::get('/robots.txt', [StoreController::class, 'robots'])->name('robots');
});

// Authentication Routes
Router::group(['middleware' => ['locale', 'visitor']], function() {
    Router::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Router::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Router::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Router::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Router::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Router::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Router::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    // Admin Login Gateway
    Router::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Router::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.submit');

    // Google OAuth 2.0 Real Integration
    Router::get('/auth/google', [AuthController::class, 'googleRedirect'])->name('auth.google');
    Router::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');
    Router::post('/auth/google/simulate', [AuthController::class, 'googleSimulate'])->name('auth.google.simulate');

    // 2FA One-Time Password (OTP) Verification
    Router::get('/auth/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('auth.otp.show');
    Router::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');
    Router::post('/auth/resend-otp', [AuthController::class, 'resendOtp'])->name('auth.otp.resend');

    // Anti-Bot Dynamic CAPTCHA Refresh
    Router::get('/captcha/refresh', [AuthController::class, 'refreshCaptcha'])->name('captcha.refresh');
});

// Customer Account Protected Routes
Router::group(['prefix' => 'account', 'middleware' => ['auth', 'locale', 'visitor']], function() {
    Router::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    Router::get('/orders', [CustomerController::class, 'orders'])->name('customer.orders');
    Router::get('/orders/{id}', [CustomerController::class, 'orderDetails'])->name('customer.order.details');
    Router::get('/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Router::post('/profile', [CustomerController::class, 'profile'])->name('customer.profile.update');
    Router::get('/returns', [CustomerController::class, 'returns'])->name('customer.returns');
    Router::post('/returns', [CustomerController::class, 'storeReturn'])->name('customer.returns.store');
    Router::get('/affiliate', [CustomerController::class, 'affiliate'])->name('customer.affiliate');
});
