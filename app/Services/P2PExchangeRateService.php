<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Setting;
use App\Services\ActivityLogger;

class P2PExchangeRateService {
    // Default baseline fallback rates for Algerian Parallel Black Market (Square / P2P BaridiMob)
    public const DEFAULT_USDT_DZD = 250.00;
    public const DEFAULT_USD_DZD = 250.00;
    public const DEFAULT_EUR_DZD = 270.00;

    /**
     * Fetch live P2P USDT/DZD rate from Binance P2P
     */
    public static function fetchBinanceP2P(): ?float {
        $url = 'https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search';
        $payload = json_encode([
            'asset' => 'USDT',
            'fiat' => 'DZD',
            'merchantCheck' => false,
            'page' => 1,
            'payTypes' => [],
            'publisherType' => null,
            'rows' => 5,
            'tradeType' => 'BUY'
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: */*',
                'Origin: https://p2p.binance.com',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        if ($statusCode === 200 && $response) {
            $json = json_decode($response, true);
            if (!empty($json['data'])) {
                $prices = [];
                foreach ($json['data'] as $ad) {
                    if (!empty($ad['adv']['price'])) {
                        $prices[] = (float)$ad['adv']['price'];
                    }
                }
                if (!empty($prices)) {
                    return round(array_sum($prices) / count($prices), 2);
                }
            }
        }

        return null;
    }

    /**
     * Fetch live P2P USDT/DZD rate from Bybit P2P
     */
    public static function fetchBybitP2P(): ?float {
        $url = 'https://api2.bybit.com/fiat/otc/item/online';
        $payload = json_encode([
            'userId' => '',
            'tokenId' => 'USDT',
            'currencyId' => 'DZD',
            'payment' => [],
            'side' => '1',
            'size' => '5',
            'page' => '1'
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        if ($statusCode === 200 && $response) {
            $json = json_decode($response, true);
            if (!empty($json['result']['items'])) {
                $prices = [];
                foreach ($json['result']['items'] as $item) {
                    if (!empty($item['price'])) {
                        $prices[] = (float)$item['price'];
                    }
                }
                if (!empty($prices)) {
                    return round(array_sum($prices) / count($prices), 2);
                }
            }
        }

        return null;
    }

    /**
     * Get consolidated parallel market rates
     */
    public static function getParallelMarketRates(): array {
        $binance = self::fetchBinanceP2P();
        $bybit = self::fetchBybitP2P();

        $ratesAvailable = array_filter([$binance, $bybit]);
        if (!empty($ratesAvailable)) {
            $usdtRate = round(array_sum($ratesAvailable) / count($ratesAvailable), 2);
            $source = ($binance && $bybit) ? 'Binance P2P + Bybit P2P (Live Average)' : ($binance ? 'Binance P2P Live' : 'Bybit P2P Live');
        } else {
            $cachedRate = (float)Setting::get('p2p_usdt_rate', self::DEFAULT_USDT_DZD);
            $usdtRate = $cachedRate > 0 ? $cachedRate : self::DEFAULT_USDT_DZD;
            $source = 'السوق الموازي الجزائري (Square Port-Saïd & P2P Algeria Baseline)';
        }

        // Euro parallel market rate in Algeria is typically ~1.08x of USD/USDT
        $eurRate = round($usdtRate * 1.08, 2);

        return [
            'binance_usdt' => $binance,
            'bybit_usdt' => $bybit,
            'average_usdt' => $usdtRate,
            'usd_dzd' => $usdtRate,
            'eur_dzd' => $eurRate,
            'source' => $source,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Synchronize and update database currencies table with live P2P rates
     */
    public static function syncCurrencies(): array {
        $rates = self::getParallelMarketRates();
        $usdtDzd = $rates['average_usdt'];
        $eurDzd = $rates['eur_dzd'];

        // 1. Ensure DZD is base 1.0
        $dzd = Currency::where('code', 'DZD')->first();
        if ($dzd) {
            $dzd->update(['exchange_rate' => 1.0, 'is_default' => 1, 'status' => 'active']);
        }

        // 2. Update or Create USDT
        $usdt = Currency::where('code', 'USDT')->first();
        $usdtRate = round(1.0 / $usdtDzd, 6);
        if ($usdt) {
            $usdt->update([
                'name_ar' => 'تيذر رقمي (Binance P2P)',
                'name_en' => 'Tether USDT (P2P)',
                'symbol_ar' => 'USDT',
                'symbol_en' => 'USDT',
                'exchange_rate' => $usdtRate,
                'status' => 'active'
            ]);
        } else {
            Currency::create([
                'code' => 'USDT',
                'name_ar' => 'تيذر رقمي (Binance P2P)',
                'name_en' => 'Tether USDT (P2P)',
                'symbol_ar' => 'USDT',
                'symbol_en' => 'USDT',
                'exchange_rate' => $usdtRate,
                'is_default' => 0,
                'status' => 'active'
            ]);
        }

        // 3. Update or Create USD (using parallel market USDT rate)
        $usd = Currency::where('code', 'USD')->first();
        if ($usd) {
            $usd->update([
                'name_ar' => 'دولار أمريكي (سوق موازي)',
                'name_en' => 'US Dollar',
                'symbol_ar' => '$',
                'symbol_en' => '$',
                'exchange_rate' => $usdtRate,
                'status' => 'active'
            ]);
        } else {
            Currency::create([
                'code' => 'USD',
                'name_ar' => 'دولار أمريكي (سوق موازي)',
                'name_en' => 'US Dollar',
                'symbol_ar' => '$',
                'symbol_en' => '$',
                'exchange_rate' => $usdtRate,
                'is_default' => 0,
                'status' => 'active'
            ]);
        }

        // 4. Update or Create EUR (Square Port-Saïd parallel market)
        $eur = Currency::where('code', 'EUR')->first();
        $eurRate = round(1.0 / $eurDzd, 6);
        if ($eur) {
            $eur->update([
                'name_ar' => 'يورو أوروبي (السكوار)',
                'name_en' => 'Euro (Square Port-Saïd)',
                'symbol_ar' => '€',
                'symbol_en' => '€',
                'exchange_rate' => $eurRate,
                'status' => 'active'
            ]);
        } else {
            Currency::create([
                'code' => 'EUR',
                'name_ar' => 'يورو أوروبي (السكوار)',
                'name_en' => 'Euro (Square Port-Saïd)',
                'symbol_ar' => '€',
                'symbol_en' => '€',
                'exchange_rate' => $eurRate,
                'is_default' => 0,
                'status' => 'active'
            ]);
        }

        // 5. Remove or deactivate SAR if present
        $sar = Currency::where('code', 'SAR')->first();
        if ($sar) {
            $sar->delete();
        }

        // 6. Save Telemetry Settings
        Setting::set('p2p_last_synced_at', $rates['timestamp'], 'currency');
        Setting::set('p2p_usdt_rate', (string)$usdtDzd, 'currency');
        Setting::set('p2p_eur_rate', (string)$eurDzd, 'currency');
        Setting::set('p2p_binance_rate', (string)($rates['binance_usdt'] ?? $usdtDzd), 'currency');
        Setting::set('p2p_bybit_rate', (string)($rates['bybit_usdt'] ?? $usdtDzd), 'currency');
        Setting::set('p2p_market_source', $rates['source'], 'currency');

        ActivityLogger::log('P2P Currency Sync', 'Currencies', "Exchange rates updated to Algerian P2P market: 1 USDT = {$usdtDzd} DZD, 1 EUR = {$eurDzd} DZD");

        return $rates;
    }
}
