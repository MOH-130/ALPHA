<?php

return [
    'version' => '2.5.0',
    'default_currency' => 'DZD',
    'default_language' => 'ar',
    'low_stock_threshold' => 5,
    'order_prefix' => 'ALPHA-',
    'affiliate' => [
        'default_commission_rate' => 10.0, // 10%
        'cookie_lifetime_days' => 30,
        'min_payout' => 500.0,
    ],
    'tax_default_percentage' => 0.0,
    'free_shipping_threshold' => 0.0,
    'supported_currencies' => ['DZD', 'USDT', 'USD', 'EUR'],
    'supported_languages' => ['ar', 'en'],
];
