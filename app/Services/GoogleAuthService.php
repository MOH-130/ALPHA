<?php

namespace App\Services;

use App\Models\User;
use App\Models\Setting;
use App\Core\Session;
use App\Services\ActivityLogger;

class GoogleAuthService {
    public static function getClientId(): string {
        $envVal = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
        if (!empty($envVal) && !str_contains($envVal, 'YOUR_GOOGLE')) {
            return trim($envVal);
        }
        return trim(Setting::get('google_client_id', ''));
    }

    public static function getClientSecret(): string {
        $envVal = $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');
        if (!empty($envVal) && !str_contains($envVal, 'YOUR_GOOGLE')) {
            return trim($envVal);
        }
        return trim(Setting::get('google_client_secret', ''));
    }

    public static function getRedirectUri(): string {
        // 1. Check incoming request headers for HTTPS (supports Render reverse proxy & SSL termination)
        $isHttps = false;
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            $isHttps = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            $isHttps = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') {
            $isHttps = true;
        } elseif (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) {
            $isHttps = true;
        }

        // 2. Check if request host is live public host (e.g. alpha-efxl.onrender.com)
        $hostHeader = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? '');
        $isLocalHostHeader = empty($hostHeader) || str_contains($hostHeader, 'localhost') || str_contains($hostHeader, '127.0.0.1');

        if (!empty($hostHeader) && !$isLocalHostHeader) {
            $scheme = $isHttps ? 'https' : 'http';
            return "{$scheme}://{$hostHeader}/auth/google/callback";
        }

        // 3. Fall back to APP_URL from .env or config
        $appUrl = env('APP_URL');
        if (empty($appUrl) && isset($_ENV['APP_URL'])) {
            $appUrl = $_ENV['APP_URL'];
        }
        if (empty($appUrl) && function_exists('config')) {
            $appUrl = config('app.url');
        }

        if (!empty($appUrl) && is_string($appUrl)) {
            $cleanUrl = rtrim(trim($appUrl), '/');
            if ($isHttps && str_starts_with($cleanUrl, 'http://')) {
                $cleanUrl = 'https://' . substr($cleanUrl, 7);
            }
            return "{$cleanUrl}/auth/google/callback";
        }

        // 4. Default fallback for local environment
        $scheme = $isHttps ? 'https' : 'http';
        $host = !empty($hostHeader) ? $hostHeader : 'localhost:8000';
        return "{$scheme}://{$host}/auth/google/callback";
    }

    public static function isConfigured(): bool {
        $id = self::getClientId();
        $sec = self::getClientSecret();
        return !empty($id) && !empty($sec);
    }

    public static function getAuthUrl(): string {
        Session::getInstance();
        $state = bin2hex(random_bytes(32));
        $_SESSION['_google_oauth_state'] = $state;

        $params = [
            'client_id' => self::getClientId(),
            'redirect_uri' => self::getRedirectUri(),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    public static function verifyState(?string $state): bool {
        Session::getInstance();
        $expected = $_SESSION['_google_oauth_state'] ?? null;
        unset($_SESSION['_google_oauth_state']);
        return !empty($state) && !empty($expected) && hash_equals($expected, $state);
    }

    public static function exchangeCodeForTokens(string $code): ?array {
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $code,
            'client_id' => self::getClientId(),
            'client_secret' => self::getClientSecret(),
            'redirect_uri' => self::getRedirectUri(),
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || !$response) {
            return null;
        }

        $data = json_decode($response, true);
        return is_array($data) && isset($data['access_token']) ? $data : null;
    }

    public static function fetchUserInfo(string $accessToken): ?array {
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo';

        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || !$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['sub']) || empty($data['email'])) {
            return null;
        }

        return [
            'id' => (string)$data['sub'],
            'email' => strtolower(trim($data['email'])),
            'name' => $data['name'] ?? explode('@', $data['email'])[0],
            'avatar' => $data['picture'] ?? null,
            'verified' => !empty($data['email_verified'])
        ];
    }

    public static function findOrCreateUser(array $googleUser): User {
        $googleId = (string)$googleUser['id'];
        $email = strtolower(trim($googleUser['email']));
        $name = trim($googleUser['name'] ?? 'Google User');
        $avatar = $googleUser['avatar'] ?? null;

        // 1. Try finding by Google ID
        $user = User::where('google_id', $googleId)->first();
        if ($user) {
            if (empty($user->avatar) && $avatar) {
                $user->update(['avatar' => $avatar]);
            }
            ActivityLogger::log('Google Login', 'Auth', "User {$user->email} logged in via Google OAuth");
            return $user;
        }

        // 2. Try finding by Email and link Google account
        $user = User::where('email', $email)->first();
        if ($user) {
            $updates = ['google_id' => $googleId];
            if (empty($user->email_verified_at)) {
                $updates['email_verified_at'] = date('Y-m-d H:i:s');
            }
            if (empty($user->avatar) && $avatar) {
                $updates['avatar'] = $avatar;
            }
            $user->update($updates);
            ActivityLogger::log('Google Account Linked', 'Auth', "Existing user {$user->email} linked with Google ID");
            return $user;
        }

        // 3. Create fresh verified customer
        $affiliateCode = 'REF-' . strtoupper(substr(md5(uniqid()), 0, 6));
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => '',
            'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
            'role' => 'customer',
            'status' => 'active',
            'is_approved' => 1,
            'google_id' => $googleId,
            'avatar' => $avatar,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'affiliate_code' => $affiliateCode
        ]);

        ActivityLogger::log('Google Register', 'Auth', "New user registered via Google OAuth: {$user->email}");
        return $user;
    }

    public static function simulateGoogleAccount(string $email, string $name = 'Google Gamer'): User {
        $email = strtolower(trim($email));
        $googleId = 'google_demo_' . md5($email);
        $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=00d2ff&color=0a0e17&size=200';

        return self::findOrCreateUser([
            'id' => $googleId,
            'email' => $email,
            'name' => $name,
            'avatar' => $avatar,
            'verified' => true
        ]);
    }
}
