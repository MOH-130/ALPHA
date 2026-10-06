<?php

/**
 * NOVASTORE - ADVANCED FULL-STACK ECOMMERCE APPLICATION
 * Entry point and HTTP Kernel Dispatcher
 */

define('LARAVEL_START', microtime(true));

// Serve static assets when running via built-in PHP server (php -S)
if (php_sapi_name() === 'cli-server') {
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $file = __DIR__ . $uriPath;

    // Security Hardening: Block direct access to hidden files or executable/sensitive extensions
    if (preg_match('/(\/\.)|(\.(php|phtml|phar|sh|exe|py|sql|sqlite|db|env|log|bak|config|yml|yaml|json)$)/i', $uriPath)) {
        if ($uriPath !== '/' && $uriPath !== '/index.php') {
            http_response_code(403);
            header('Content-Type: text/plain; charset=utf-8');
            exit('403 Forbidden: Direct access denied.');
        }
    }

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
