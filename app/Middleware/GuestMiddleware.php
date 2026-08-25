<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class GuestMiddleware {
    public function handle(Request $request): ?Response {
        if (Auth::getInstance()->check()) {
            if (Auth::getInstance()->isAdmin()) {
                return redirect(url('admin/dashboard'));
            }
            return redirect(url('account/dashboard'));
        }
        return null;
    }
}
