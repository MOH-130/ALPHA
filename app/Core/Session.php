<?php

namespace App\Core;

class Session {
    private static ?Session $instance = null;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getInstance(): Session {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function set(string $key, $value): void {
        self::getInstance();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null) {
        self::getInstance();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        self::getInstance();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::getInstance();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, $value = null) {
        self::getInstance();
        if ($value === null) {
            $val = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $val;
        }
        $_SESSION['_flash'][$key] = $value;
    }

    public static function csrfToken(): string {
        self::getInstance();
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }

    public static function validateCsrf(?string $token): bool {
        self::getInstance();
        return !empty($token) && hash_equals($_SESSION['_token'] ?? '', $token);
    }

    public static function setOldInput(array $input): void {
        self::set('_old_input', $input);
    }

    public static function getOldInput(string $key, $default = '') {
        $old = self::get('_old_input', []);
        return $old[$key] ?? $default;
    }
}
