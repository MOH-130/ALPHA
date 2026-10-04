<?php

if (!function_exists('env')) {
    function env($key, $default = null) {
        $val = getenv($key);
        if ($val === false) {
            if (isset($_ENV[$key])) {
                $val = $_ENV[$key];
            } elseif (isset($_SERVER[$key])) {
                $val = $_SERVER[$key];
            } else {
                return $default;
            }
        }
        if ($val === 'true' || $val === '(true)') return true;
        if ($val === 'false' || $val === '(false)') return false;
        if ($val === 'null' || $val === '(null)') return null;
        if ($val === 'empty' || $val === '(empty)') return '';
        return $val;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        static $configs = [];
        $parts = explode('.', $key);
        $file = array_shift($parts);

        if (!isset($configs[$file])) {
            $path = __DIR__ . '/../../config/' . $file . '.php';
            if (file_exists($path)) {
                $configs[$file] = require $path;
            } else {
                $configs[$file] = [];
            }
        }

        $current = $configs[$file];
        foreach ($parts as $part) {
            if (is_array($current) && isset($current[$part])) {
                $current = $current[$part];
            } else {
                return $default;
            }
        }
        return $current;
    }
}

if (!function_exists('session')) {
    function session($key = null, $default = null) {
        if ($key === null) {
            return \App\Core\Session::getInstance();
        }
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                \App\Core\Session::set($k, $v);
            }
            return null;
        }
        return \App\Core\Session::get($key, $default);
    }
}

if (!function_exists('auth')) {
    function auth() {
        return \App\Core\Auth::getInstance();
    }
}

if (!function_exists('trans')) {
    function trans($key, $replace = [], $locale = null) {
        return \App\Core\Translator::get($key, $replace, $locale);
    }
}

if (!function_exists('__')) {
    function __($key, $replace = [], $locale = null) {
        return trans($key, $replace, $locale);
    }
}

if (!function_exists('current_locale')) {
    function current_locale() {
        return \App\Core\Translator::getLocale();
    }
}

if (!function_exists('is_rtl')) {
    function is_rtl() {
        return \App\Core\Translator::isRtl();
    }
}

if (!function_exists('current_currency')) {
    function current_currency() {
        return \App\Services\CurrencyService::getCurrent();
    }
}

if (!function_exists('format_price')) {
    function format_price($amount, $currencyCode = null) {
        return \App\Services\CurrencyService::format($amount, $currencyCode);
    }
}

if (!function_exists('app_setting')) {
    function app_setting($key, $default = null) {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('theme_setting')) {
    function theme_setting($key, $default = null) {
        return \App\Models\ThemeSetting::get($key, $default);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        return \App\Core\Session::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token()) . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field($method) {
        return '<input type="hidden" name="_method" value="' . htmlspecialchars($method) . '">';
    }
}

if (!function_exists('old')) {
    function old($key, $default = '') {
        return \App\Core\Session::getOldInput($key, $default);
    }
}

if (!function_exists('base_url')) {
    function base_url(): string {
        $appUrl = env('APP_URL');
        if (empty($appUrl) && isset($_ENV['APP_URL'])) {
            $appUrl = $_ENV['APP_URL'];
        }
        if (empty($appUrl)) {
            $appUrl = config('app.url');
        }
        if (!empty($appUrl) && $appUrl !== 'http://localhost:8000') {
            return rtrim($appUrl, '/');
        }

        // Check if incoming request is from a public host or behind proxy (e.g. Render)
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? '');
        $isLocalHost = empty($host) || str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');

        if (!empty($host) && !$isLocalHost) {
            $isHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
                || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
                || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
                || (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
            $scheme = $isHttps ? 'https' : 'http';
            return "{$scheme}://{$host}";
        }

        return rtrim($appUrl ?: 'http://localhost:8000', '/');
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }
        $baseUrl = base_url();
        $path = ltrim($path, '/');
        return $baseUrl . '/' . $path;
    }
}

if (!function_exists('product_image_url')) {
    function product_image_url(?string $image): string {
        if (empty($image)) {
            return asset('uploads/products/default-product.png');
        }
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '//')) {
            return $image;
        }
        return asset('uploads/products/' . $image);
    }
}

if (!function_exists('url')) {
    function url($path = '', $params = []) {
        $baseUrl = base_url();
        $path = ltrim($path, '/');
        $url = $baseUrl . '/' . $path;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }
}

if (!function_exists('route')) {
    function route($name, $params = []) {
        return \App\Core\Router::getNamedUrl($name, $params);
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $status = 302) {
        return new \App\Core\Response('', $status, ['Location' => $url]);
    }
}

if (!function_exists('view')) {
    function view($view, $data = []) {
        return \App\Core\View::render($view, $data);
    }
}

if (!function_exists('json_response')) {
    function json_response($data = [], $status = 200) {
        return \App\Core\Response::json($data, $status);
    }
}

if (!function_exists('slugify')) {
    function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = mb_strtolower($text, 'UTF-8');
        if (empty($text)) {
            return 'n-a-' . time();
        }
        return $text;
    }
}
