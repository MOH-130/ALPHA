<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware {
    public function handle(Request $request): ?Response {
        if (!Auth::getInstance()->check()) {
            Session::flash('error', __('auth.please_login_first'));
            Session::set('intended_url', $request->url());
            if ($request->isAjax()) {
                return Response::json(['success' => false, 'message' => __('auth.unauthenticated')], 401);
            }
            return redirect(url('login'));
        }
        return null;
    }
}
