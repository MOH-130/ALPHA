<?php

namespace App\Core;

class Translator {
    private static string $locale = 'ar';
    private static array $translations = [];

    public static function setLocale(string $locale): void {
        self::$locale = in_array($locale, ['ar', 'en']) ? $locale : 'ar';
        Session::set('app_locale', self::$locale);
    }

    public static function getLocale(): string {
        if (Session::has('app_locale')) {
            self::$locale = Session::get('app_locale');
        }
        return self::$locale;
    }

    public static function isRtl(): bool {
        return self::getLocale() === 'ar';
    }

    public static function load(string $locale): void {
        if (!isset(self::$translations[$locale])) {
            self::$translations[$locale] = [];
            $langDir = __DIR__ . '/../../lang/' . $locale;
            if (is_dir($langDir)) {
                $files = glob($langDir . '/*.php');
                foreach ($files as $file) {
                    $name = basename($file, '.php');
                    $data = require $file;
                    if (is_array($data)) {
                        self::$translations[$locale][$name] = $data;
                        // Also merge into root for direct access like __('deals')
                        if ($name === 'messages') {
                            self::$translations[$locale] = array_merge(self::$translations[$locale], $data);
                        }
                    }
                }
            }
        }
    }

    public static function get(string $key, array $replace = [], ?string $locale = null): string {
        $locale = $locale ?: self::getLocale();
        self::load($locale);

        $line = self::findLine($key, $locale);
        if ($line === null) {
            $fallback = ($locale === 'ar') ? 'en' : 'ar';
            self::load($fallback);
            $line = self::findLine($key, $fallback);
        }

        if ($line === null) {
            $parts = explode('.', $key);
            $cleanKey = end($parts);
            return ucwords(str_replace('_', ' ', $cleanKey));
        }

        foreach ($replace as $placeholder => $value) {
            $line = str_replace(':' . $placeholder, (string)$value, $line);
        }

        return $line;
    }

    private static function findLine(string $key, string $locale): ?string {
        $array = self::$translations[$locale] ?? [];
        
        // 1. Direct match in root array (e.g. 'deals')
        if (isset($array[$key]) && is_string($array[$key])) {
            return $array[$key];
        }

        // 2. If starts with messages., check without messages. prefix
        if (str_starts_with($key, 'messages.')) {
            $sub = substr($key, 9);
            if (isset($array[$sub]) && is_string($array[$sub])) {
                return $array[$sub];
            }
        }

        // 3. Segment lookup (e.g. 'messages.deals' where messages is an array)
        $keys = explode('.', $key);
        $curr = $array;
        $found = true;
        foreach ($keys as $segment) {
            if (is_array($curr) && isset($curr[$segment])) {
                $curr = $curr[$segment];
            } else {
                $found = false;
                break;
            }
        }
        if ($found && is_string($curr)) {
            return $curr;
        }

        return null;
    }
}
