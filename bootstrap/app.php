<?php

// Autoloader for App Classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }

    $dbPrefix = 'Database\\';
    $dbBaseDir = __DIR__ . '/../database/';
    $dbLen = strlen($dbPrefix);
    if (strncmp($dbPrefix, $class, $dbLen) === 0) {
        $relativeClass = substr($class, $dbLen);
        $file = $dbBaseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Load Global Helpers
require_once __DIR__ . '/../app/Core/Helper.php';

// Initialize Session
\App\Core\Session::getInstance();

// Initialize Database connection and auto-migrate if needed
\App\Core\Database::getPdo();

// Load Web, Admin, and API Routes
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/admin.php';
require_once __DIR__ . '/../routes/api.php';
