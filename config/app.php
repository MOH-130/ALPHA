<?php

return [
    'name' => env('APP_NAME', 'NovaStore Pro eCommerce'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost:8000'),
    'timezone' => 'Asia/Riyadh',
    'locale' => 'ar',
    'fallback_locale' => 'en',
    'key' => env('APP_KEY', 'base64:NovaStoreSecretKey98765432100000000000000='),
];
