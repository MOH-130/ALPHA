<?php

use App\Core\Router;
use App\Controllers\AdminController;

Router::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'locale']], function() {
    Router::get('/', [AdminController::class, 'dashboard'])->name('admin.index');
    Router::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Products
    Router::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Router::get('/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Router::post('/products/store', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Router::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Router::post('/products/{id}/update', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Router::get('/products/{id}/delete', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');

    // Categories
    Router::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Router::post('/categories/store', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Router::get('/categories/{id}/delete', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');

    // Brands
    Router::get('/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Router::post('/brands/store', [AdminController::class, 'storeBrand'])->name('admin.brands.store');
    Router::get('/brands/{id}/delete', [AdminController::class, 'deleteBrand'])->name('admin.brands.delete');

    // Orders
    Router::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Router::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Router::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update_status');
    Router::get('/orders/{id}/invoice', [AdminController::class, 'printInvoice'])->name('admin.orders.invoice');

    // Returns
    Router::get('/returns', [AdminController::class, 'returns'])->name('admin.returns');
    Router::post('/returns/{id}/status', [AdminController::class, 'updateReturnStatus'])->name('admin.returns.update');

    // Customers
    Router::get('/customers', [AdminController::class, 'customers'])->name('admin.customers');
    Router::post('/customers/{id}/status', [AdminController::class, 'updateCustomerStatus'])->name('admin.customers.update');

    // Marketing & Coupons
    Router::get('/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Router::post('/coupons/store', [AdminController::class, 'storeCoupon'])->name('admin.coupons.store');
    Router::get('/coupons/{id}/delete', [AdminController::class, 'deleteCoupon'])->name('admin.coupons.delete');

    // Affiliates
    Router::get('/affiliates', [AdminController::class, 'affiliates'])->name('admin.affiliates');
    Router::post('/affiliates/{id}/status', [AdminController::class, 'updateAffiliateCommission'])->name('admin.affiliates.update');

    // Reviews
    Router::get('/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Router::get('/reviews/{id}/toggle', [AdminController::class, 'toggleReview'])->name('admin.reviews.toggle');
    Router::get('/reviews/{id}/delete', [AdminController::class, 'deleteReview'])->name('admin.reviews.delete');

    // Theme & Design Customizer
    Router::get('/theme', [AdminController::class, 'theme'])->name('admin.theme');
    Router::post('/theme', [AdminController::class, 'theme'])->name('admin.theme.update');

    // Settings
    Router::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Router::post('/settings', [AdminController::class, 'settings'])->name('admin.settings.update');

    // Shipping & Payments
    Router::get('/shipping', [AdminController::class, 'shipping'])->name('admin.shipping');
    Router::post('/shipping/{id}', [AdminController::class, 'updateShippingMethod'])->name('admin.shipping.update');
    Router::get('/payments', [AdminController::class, 'payments'])->name('admin.payments');
    Router::post('/payments', [AdminController::class, 'updatePayments'])->name('admin.payments.update');

    // Currencies & Languages
    Router::get('/currencies', [AdminController::class, 'currencies'])->name('admin.currencies');
    Router::post('/currencies/store', [AdminController::class, 'storeCurrency'])->name('admin.currencies.store');
    Router::get('/languages', [AdminController::class, 'languages'])->name('admin.languages');

    // Pages
    Router::get('/pages', [AdminController::class, 'pages'])->name('admin.pages');
    Router::post('/pages/store', [AdminController::class, 'storePage'])->name('admin.pages.store');

    // Visitors & Activity Logs
    Router::get('/visitors', [AdminController::class, 'visitors'])->name('admin.visitors');
    Router::get('/logs', [AdminController::class, 'logs'])->name('admin.logs');
});
