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
                'Referrer-Policy' => 'strict-origin-when-cross-origin'
            ];
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
