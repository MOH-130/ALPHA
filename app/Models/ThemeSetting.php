<?php

namespace App\Models;

use App\Core\Model;

class ThemeSetting extends Model {
    protected string $table = 'theme_settings';
    protected array $fillable = ['key', 'value'];

    private static array $cache = [];

    public static function get(string $key, $default = null) {
        if (!empty(self::$cache)) {
            return self::$cache[$key] ?? $default;
        }

        $all = self::all();
        foreach ($all as $item) {
            self::$cache[$item->key] = $item->value;
        }

        return self::$cache[$key] ?? $default;
    }

    public static function set(string $key, $value): void {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create(['key' => $key, 'value' => $value]);
        }
        self::$cache[$key] = $value;
    }
}
