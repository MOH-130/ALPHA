<?php

namespace App\Services;

class RateLimiter {
    private static string $storageDir = __DIR__ . '/../../storage/data/rate_limits';

    private static function initStorage(): void {
        if (!is_dir(self::$storageDir)) {
            @mkdir(self::$storageDir, 0777, true);
        }
    }

    private static function getFilePath(string $key): string {
        self::initStorage();
        $hash = md5($key);
        return self::$storageDir . '/' . $hash . '.json';
    }

    public static function hit(string $key, int $decaySeconds = 600): int {
        $file = self::getFilePath($key);
        $data = self::read($key);

        $now = time();
        if ($data && $now < $data['expires_at']) {
            $data['attempts']++;
        } else {
            $data = [
                'attempts' => 1,
                'expires_at' => $now + $decaySeconds,
                'created_at' => $now
            ];
        }

        @file_put_contents($file, json_encode($data));
        return $data['attempts'];
    }

    public static function tooManyAttempts(string $key, int $maxAttempts = 5): bool {
        $data = self::read($key);
        if (!$data) {
            return false;
        }

        if (time() >= $data['expires_at']) {
            self::clear($key);
            return false;
        }

        return $data['attempts'] >= $maxAttempts;
    }

    public static function attempts(string $key): int {
        $data = self::read($key);
        if (!$data || time() >= $data['expires_at']) {
            return 0;
        }
        return $data['attempts'];
    }

    public static function remaining(string $key, int $maxAttempts = 5): int {
        $attempts = self::attempts($key);
        return max(0, $maxAttempts - $attempts);
    }

    public static function availableIn(string $key): int {
        $data = self::read($key);
        if (!$data) {
            return 0;
        }
        return max(0, $data['expires_at'] - time());
    }

    public static function clear(string $key): void {
        $file = self::getFilePath($key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    private static function read(string $key): ?array {
        $file = self::getFilePath($key);
        if (!file_exists($file)) {
            return null;
        }
        $content = @file_get_contents($file);
        if (!$content) {
            return null;
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : null;
    }
}
