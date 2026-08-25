<?php

/**
 * NOVASTORE - ADVANCED FULL-STACK ECOMMERCE APPLICATION
 * Entry point and HTTP Kernel Dispatcher
 */

define('LARAVEL_START', microtime(true));

// Serve static assets when running via built-in PHP server (php -S)
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

// Bootstrap Application
require_once __DIR__ . '/../bootstrap/app.php';

// Dispatch Request through Router
$request = new \App\Core\Request();
$response = \App\Core\Router::dispatch($request);
$response->send();
