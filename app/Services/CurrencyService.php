<?php

namespace App\Services;

use App\Core\Session;
use App\Models\Currency;

class CurrencyService {
    public static function getCurrent(): Currency {
        $defaultCurrency = Currency::where('is_default', 1)->first();
        $defaultCode = $defaultCurrency ? $defaultCurrency->code : config('ecommerce.default_currency', 'DZD');
        $code = Session::get('app_currency', $defaultCode);
        $currency = Currency::where('code', $code)->where('status', 'active')->first();
        if (!$currency) {
            $currency = $defaultCurrency ?: Currency::first();
        }
        return $currency ?: new Currency([
            'code' => 'DZD',
            'name_ar' => 'دينار جزائري',
            'name_en' => 'Algerian Dinar',
            'symbol_ar' => 'د.ج',
            'symbol_en' => 'DA',
            'exchange_rate' => 1.0
        ]);
    }

    public static function setCurrency(string $code): void {
        $currency = Currency::where('code', $code)->where('status', 'active')->first();
        if ($currency) {
            Session::set('app_currency', $code);
        }
    }

    public static function convert(float $amountInBase, ?string $targetCurrencyCode = null): float {
        $target = $targetCurrencyCode ? Currency::where('code', $targetCurrencyCode)->first() : self::getCurrent();
        $rate = $target ? (float)$target->exchange_rate : 1.0;
        return round($amountInBase * $rate, 2);
    }

    public static function format(float $amountInBase, ?string $currencyCode = null): string {
        $curr = $currencyCode ? Currency::where('code', $currencyCode)->first() : self::getCurrent();
        $converted = self::convert($amountInBase, $curr ? $curr->code : null);
        $symbol = $curr ? $curr->getSymbolAttribute() : 'د.ج';
        $formattedNumber = number_format($converted, 2);

        if (is_rtl()) {
            return "{$formattedNumber} {$symbol}";
        }
        return "{$symbol} {$formattedNumber}";
    }

    public static function getRateInDzd(Currency $currency): float {
        $rate = (float)$currency->exchange_rate;
        if ($rate <= 0 || $currency->code === 'DZD') return 1.0;
        return round(1.0 / $rate, 2);
    }

    public static function getAllActive(): array {
        return Currency::where('status', 'active')->get();
    }
}
