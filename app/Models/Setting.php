<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model {
    protected string $table = 'settings';
    protected array $fillable = ['key', 'value', 'group'];

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

    public static function set(string $key, $value, string $group = 'general'): void {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value, 'group' => $group]);
        } else {
            self::create(['key' => $key, 'value' => $value, 'group' => $group]);
        }
        self::$cache[$key] = $value;
    }
}
