<?php

namespace App\Core;

class Request {
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private array $json;

    public function __construct() {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        $raw = file_get_contents('php://input');
        $this->json = json_decode($raw, true) ?: [];
    }

    public function method(): string {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($this->post['_method'])) {
            return strtoupper($this->post['_method']);
        }
        return $method;
    }

    public function isPost(): bool {
        return $this->method() === 'POST';
    }

    public function isGet(): bool {
        return $this->method() === 'GET';
    }

    public function isAjax(): bool {
        return (!empty($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (strpos($this->server['HTTP_ACCEPT'] ?? '', 'application/json') !== false);
    }

    public function path(): string {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $parts = explode('?', $uri);
        $path = $parts[0];
        
        $scriptName = dirname($this->server['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '/' && $scriptName !== '\\' && strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        }
        
        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    public function url(): string {
        $scheme = (isset($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . ($this->server['REQUEST_URI'] ?? '/');
    }

    public function input(?string $key = null, $default = null) {
        if ($key === null) {
            return array_merge($this->get, $this->post, $this->json);
        }
        if (isset($this->post[$key])) return $this->post[$key];
        if (isset($this->get[$key])) return $this->get[$key];
        if (isset($this->json[$key])) return $this->json[$key];
        return $default;
    }

    public function all(): array {
        return $this->input();
    }

    public function getQuery(string $key, $default = null) {
        return $this->get[$key] ?? $default;
    }

    public function file(string $key): ?array {
        if (isset($this->files[$key]) && !empty($this->files[$key]['name'])) {
            return $this->files[$key];
        }
        return null;
    }

    public function hasFile(string $key): bool {
        return isset($this->files[$key]) && !empty($this->files[$key]['tmp_name']) && is_uploaded_file($this->files[$key]['tmp_name']);
    }

    public function ip(): string {
        return $this->server['HTTP_CF_CONNECTING_IP'] 
            ?? $this->server['HTTP_X_FORWARDED_FOR'] 
            ?? $this->server['REMOTE_ADDR'] 
            ?? '127.0.0.1';
    }

    public function userAgent(): string {
        return $this->server['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public function referrer(): string {
        return $this->server['HTTP_REFERER'] ?? '';
    }

    public function validate(array $rules): array {
        $errors = [];
        $data = $this->all();

        foreach ($rules as $field => $fieldRules) {
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $val = $data[$field] ?? null;

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && (is_null($val) || $val === '')) {
                    $errors[$field][] = __("validation.required", ['attribute' => $field]);
                }
                if ($rule === 'email' && !empty($val) && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = __("validation.email", ['attribute' => $field]);
                }
                if (strpos($rule, 'min:') === 0 && !empty($val)) {
                    $min = (int) substr($rule, 4);
                    if (is_string($val) && mb_strlen($val) < $min) {
                        $errors[$field][] = __("validation.min", ['attribute' => $field, 'min' => $min]);
                    }
                }
                if (strpos($rule, 'max:') === 0 && !empty($val)) {
                    $max = (int) substr($rule, 4);
                    if (is_string($val) && mb_strlen($val) > $max) {
                        $errors[$field][] = __("validation.max", ['attribute' => $field, 'max' => $max]);
                    }
                }
                if ($rule === 'numeric' && !empty($val) && !is_numeric($val)) {
                    $errors[$field][] = __("validation.numeric", ['attribute' => $field]);
                }
            }
        }

        if (!empty($errors)) {
            Session::setOldInput($data);
            Session::flash('errors', $errors);
            Session::flash('error', __("messages.validation_failed"));
            if ($this->isAjax()) {
                Response::json(['success' => false, 'errors' => $errors], 422)->send();
                exit;
            }
            $referer = $this->server['HTTP_REFERER'] ?? '/';
            redirect($referer)->send();
            exit;
        }

        return $data;
    }
}
