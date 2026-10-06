<?php

namespace App\Core;

class Response {
    private string $content;
    private int $statusCode;
    private array $headers;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = []) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function json(array $data, int $statusCode = 200): self {
        return new self(
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            $statusCode,
            ['Content-Type' => 'application/json; charset=utf-8']
        );
    }

    public static function html(string $content, int $statusCode = 200): self {
        return new self($content, $statusCode, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    public function header(string $name, string $value): self {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function send(): void {
        if (!headers_sent()) {
            http_response_code($this->statusCode);

            // Default Hardened Security Headers
            $defaultSecurityHeaders = [
                'X-Frame-Options' => 'SAMEORIGIN',
                'X-Content-Type-Options' => 'nosniff',
                'X-XSS-Protection' => '1; mode=block',
                'Referrer-Policy' => 'strict-origin-when-cross-origin',
                'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
                'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' https://accounts.google.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com data:; img-src 'self' data: https: blob:; connect-src 'self' https://accounts.google.com https://oauth2.googleapis.com; frame-src 'self' https://accounts.google.com; object-src 'none'; base-uri 'self';"
            ];

            // Strict-Transport-Security on HTTPS
            $isHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
                || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
                || (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
            if ($isHttps) {
                $defaultSecurityHeaders['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
            }

            foreach ($defaultSecurityHeaders as $name => $val) {
                if (!isset($this->headers[$name])) {
                    header("{$name}: {$val}");
                }
            }

            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }
        echo $this->content;
    }
}
