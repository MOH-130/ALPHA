<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AdminMiddleware {
    public function handle(Request $request): ?Response {
        $auth = Auth::getInstance();
        if (!$auth->check()) {
            Session::flash('error', __('auth.admin_login_required'));
            Session::set('intended_url', $request->url());
            return redirect(url('admin/login'));
        }

        if (!$auth->isAdmin()) {
            Session::flash('error', __('auth.unauthorized_access'));
            return redirect(url('/'));
        }

        return null;
    }
}
