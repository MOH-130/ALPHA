<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $pdo = null;

    public static function getPdo(): PDO {
        if (self::$pdo === null) {
            $default = config('database.default', 'sqlite');
            $config = config("database.connections.{$default}", []);

            try {
                if ($default === 'sqlite') {
                    $dbPath = $config['database'] ?? (__DIR__ . '/../../storage/data/database.sqlite');
                    $dir = dirname($dbPath);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0777, true);
                    }
                    $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;
                    self::$pdo = new PDO("sqlite:" . $dbPath);
                    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    self::$pdo->exec("PRAGMA foreign_keys = ON;");

                    if ($isNew) {
                        self::initializeDatabase();
                    } else {
                        self::ensureColumnsExist();
                    }
                } else {
                    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
                    $pdoOptions = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ];
                    if (!empty($config['ssl']) || (isset($config['host']) && str_contains($config['host'], 'tidbcloud')) || (isset($config['port']) && $config['port'] == 4000)) {
                        if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
                            $pdoOptions[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                        }
                    }
                    self::$pdo = new PDO($dsn, $config['username'], $config['password'], $pdoOptions);
                }
            } catch (PDOException $e) {
                // If MySQL fails or database does not exist, fallback to SQLite automatically for zero-friction run
                if ($default === 'mysql') {
                    $fallbackPath = __DIR__ . '/../../storage/data/database.sqlite';
                    $dir = dirname($fallbackPath);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0777, true);
                    }
                    $isNew = !file_exists($fallbackPath) || filesize($fallbackPath) === 0;
                    self::$pdo = new PDO("sqlite:" . $fallbackPath);
                    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    self::$pdo->exec("PRAGMA foreign_keys = ON;");
                    if ($isNew) {
                        self::initializeDatabase();
                    }
                } else {
                    die("Database connection error: " . $e->getMessage());
                }
            }
        }
        return self::$pdo;
    }

    public static function statement(string $sql, array $params = []): bool {
        $stmt = self::getPdo()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function select(string $sql, array $params = []): array {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function selectOne(string $sql, array $params = []): ?array {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }

    public static function insert(string $sql, array $params = []): int {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return (int) self::getPdo()->lastInsertId();
    }

    public static function initializeDatabase(): void {
        require_once __DIR__ . '/../../database/migrations/create_all_tables.php';
        \Database\Migrations\CreateAllTables::up();

        require_once __DIR__ . '/../../database/seeders/DatabaseSeeder.php';
        \Database\Seeders\DatabaseSeeder::run();
    }

    public static function ensureColumnsExist(): void {
        if (!self::$pdo) return;
        try {
            $cols = self::$pdo->query("PRAGMA table_info(users)")->fetchAll(\PDO::FETCH_ASSOC);
            $names = array_column($cols, 'name');
            if (!in_array('otp_code', $names)) {
                self::$pdo->exec("ALTER TABLE users ADD COLUMN otp_code VARCHAR(255) NULL");
            }
            if (!in_array('otp_expires_at', $names)) {
                self::$pdo->exec("ALTER TABLE users ADD COLUMN otp_expires_at DATETIME NULL");
            }
            if (!in_array('otp_purpose', $names)) {
                self::$pdo->exec("ALTER TABLE users ADD COLUMN otp_purpose VARCHAR(50) NULL");
            }
            if (!in_array('two_factor_enabled', $names)) {
                self::$pdo->exec("ALTER TABLE users ADD COLUMN two_factor_enabled INTEGER DEFAULT 0");
            }
            if (!in_array('google_id', $names)) {
                self::$pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(191) NULL");
            }
        } catch (\Throwable $e) {}
    }
}
