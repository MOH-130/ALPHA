<?php

use App\Core\Router;
use App\Controllers\ApiController;

Router::group(['prefix' => 'api', 'middleware' => ['locale']], function() {
    Router::get('/search', [ApiController::class, 'search'])->name('api.search');
    Router::post('/cart/add', [ApiController::class, 'addToCart'])->name('api.cart.add');
    Router::post('/cart/update', [ApiController::class, 'updateCart'])->name('api.cart.update');
    Router::post('/cart/remove', [ApiController::class, 'removeCart'])->name('api.cart.remove');
    Router::post('/wishlist/toggle', [ApiController::class, 'toggleWishlist'])->name('api.wishlist.toggle');
    Router::post('/compare/toggle', [ApiController::class, 'toggleCompare'])->name('api.compare.toggle');
    Router::post('/coupon/apply', [ApiController::class, 'applyCoupon'])->name('api.coupon.apply');
});
