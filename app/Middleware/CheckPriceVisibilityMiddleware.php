<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Models\Setting;

class CheckPriceVisibilityMiddleware {
    public function handle(Request $request): ?Response {
        $priceVisibility = Setting::get('price_visibility', 'all'); // 'all' or 'registered_only'
        if ($priceVisibility === 'registered_only' && !Auth::getInstance()->check()) {
            \App\Core\View::share('hide_prices', true);
        } else {
            \App\Core\View::share('hide_prices', false);
        }
        return null;
    }
}
