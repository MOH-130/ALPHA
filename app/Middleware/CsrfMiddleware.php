<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class CsrfMiddleware {
    /**
     * URIs that should be exempted from CSRF verification
     */
    private array $except = [
        '/api/*',
        '/webhook/*',
    ];

    public function handle(Request $request): ?Response {
        $method = $request->method();

        // Safe methods do not require CSRF token
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return null;
        }

        $path = $request->path();
        foreach ($this->except as $pattern) {
            $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';
            if (preg_match($regex, $path)) {
                return null;
            }
        }

        $token = $request->input('_token');
        if (empty($token) && !empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }

        if (!Session::validateCsrf($token)) {
            if ($request->isAjax()) {
                return Response::json([
                    'success' => false,
                    'error' => 'CSRF Token Mismatch',
                    'message' => 'انتهت صلاحية جلسة الأمان. يرجى تحديث الصفحة والمحاولة مجدداً.'
                ], 419);
            }

            Session::flash('error', 'انتهت صلاحية جلسة الأمان (CSRF Token Mismatch). يرجى تحديث الصفحة والمحاولة مجدداً.');
            $referer = $request->referrer();
            return redirect($referer ?: '/');
        }

        return null;
    }
}
