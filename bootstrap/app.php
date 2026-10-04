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

// Load Composer Autoloader if available (PHPMailer, etc.)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Load .env variables into environment if file exists and variables not already provided by system
$envFilePath = __DIR__ . '/../.env';
if (file_exists($envFilePath)) {
    $envLines = @file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($envLines)) {
        foreach ($envLines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                list($envKey, $envVal) = explode('=', $line, 2);
                $envKey = trim($envKey);
                $envVal = trim($envVal);
                if ((str_starts_with($envVal, '"') && str_ends_with($envVal, '"')) ||
                    (str_starts_with($envVal, "'") && str_ends_with($envVal, "'"))) {
                    $envVal = substr($envVal, 1, -1);
                }
                if (!array_key_exists($envKey, $_SERVER) && !array_key_exists($envKey, $_ENV) && getenv($envKey) === false) {
                    putenv("{$envKey}={$envVal}");
                    $_ENV[$envKey] = $envVal;
                    $_SERVER[$envKey] = $envVal;
                }
            }
        }
    }
}

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
