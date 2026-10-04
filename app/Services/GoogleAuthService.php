<?php

namespace App\Services;

use App\Models\User;
use App\Models\Setting;
use App\Core\Session;
use App\Services\ActivityLogger;

class GoogleAuthService {
    public static ?string $lastError = null;

    public static function getClientId(): string {
        $val = function_exists('env') ? env('GOOGLE_CLIENT_ID') : null;
        if (empty($val)) {
            $val = $_SERVER['GOOGLE_CLIENT_ID'] ?? ($_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID'));
        }

        if (is_string($val)) {
            $val = trim($val);
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = trim(substr($val, 1, -1));
            }
            if (!empty($val) && !str_contains($val, 'YOUR_GOOGLE') && $val !== 'null') {
                return $val;
            }
        }

        return trim((string)Setting::get('google_client_id', ''));
    }

    public static function getClientSecret(): string {
        $val = function_exists('env') ? env('GOOGLE_CLIENT_SECRET') : null;
        if (empty($val)) {
            $val = $_SERVER['GOOGLE_CLIENT_SECRET'] ?? ($_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET'));
        }

        if (is_string($val)) {
            $val = trim($val);
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = trim(substr($val, 1, -1));
            }
            if (!empty($val) && !str_contains($val, 'YOUR_GOOGLE') && $val !== 'null') {
                return $val;
            }
        }

        return trim((string)Setting::get('google_client_secret', ''));
    }

    public static function getRedirectUri(): string {
        // 1. Explicit GOOGLE_REDIRECT_URI environment variable has top priority
        $configuredUri = function_exists('env') ? env('GOOGLE_REDIRECT_URI') : null;
        if (empty($configuredUri)) {
            $configuredUri = $_SERVER['GOOGLE_REDIRECT_URI'] ?? ($_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI'));
        }
        if (!empty($configuredUri) && is_string($configuredUri)) {
            $configuredUri = trim($configuredUri);
            if (!str_contains($configuredUri, 'YOUR_GOOGLE') && $configuredUri !== 'null') {
                return $configuredUri;
            }
        }

        // 2. Check incoming request headers for HTTPS (supports Render reverse proxy & SSL termination)
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

        // 3. Check if request host is live public host (e.g. alpha-1-mshm.onrender.com)
        $rawHost = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? '');
        $hostHeader = trim(explode(',', $rawHost)[0]);
        if (str_ends_with($hostHeader, ':443')) {
            $hostHeader = substr($hostHeader, 0, -4);
        } elseif (str_ends_with($hostHeader, ':80')) {
            $hostHeader = substr($hostHeader, 0, -3);
        }

        $isLocalHostHeader = empty($hostHeader) || str_contains($hostHeader, 'localhost') || str_contains($hostHeader, '127.0.0.1');

        if (!empty($hostHeader) && !$isLocalHostHeader) {
            $scheme = $isHttps ? 'https' : 'http';
            return "{$scheme}://{$hostHeader}/auth/google/callback";
        }

        // 4. Fall back to APP_URL from .env or config
        $appUrl = function_exists('env') ? env('APP_URL') : null;
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

        // 5. Default fallback for local environment
        $scheme = $isHttps ? 'https' : 'http';
        $host = !empty($hostHeader) ? $hostHeader : 'localhost:8000';
        return "{$scheme}://{$host}/auth/google/callback";
    }

    public static function isConfigured(): bool {
        $id = self::getClientId();
        $sec = self::getClientSecret();
        return !empty($id) && !empty($sec) && $id !== 'null' && $sec !== 'null' && !str_contains($id, 'YOUR_GOOGLE') && !str_contains($sec, 'YOUR_GOOGLE');
    }

    public static function getAuthUrl(): string {
        Session::getInstance();
        $state = bin2hex(random_bytes(32));
        $redirectUri = self::getRedirectUri();

        // Persist state AND exact redirect_uri in user session to prevent any byte-level mismatch
        $_SESSION['_google_oauth_state'] = $state;
        $_SESSION['_google_oauth_redirect_uri'] = $redirectUri;

        $params = [
            'client_id' => self::getClientId(),
            'redirect_uri' => $redirectUri,
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

    public static function exchangeCodeForTokens(string $code, ?string $explicitRedirectUri = null): ?array {
        self::$lastError = null;
        Session::getInstance();

        // Use the exact redirect_uri stored when generating the auth URL, or fallback to current
        $redirectUri = $explicitRedirectUri 
            ?: ($_SESSION['_google_oauth_redirect_uri'] ?? self::getRedirectUri());
        unset($_SESSION['_google_oauth_redirect_uri']);

        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $clientId = self::getClientId();
        $clientSecret = self::getClientSecret();

        if (empty($clientId) || empty($clientSecret)) {
            $err = 'Google Client ID or Client Secret is missing from server environment';
            self::$lastError = $err;
            error_log("[Google OAuth] exchangeCodeForTokens failed: {$err}");
            return null;
        }

        $postData = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'User-Agent: AlphaGamingShop-OAuth/1.0'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        $curlErrNo = curl_errno($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        if ($curlErr || !$response) {
            $errDetail = "Network/cURL error (#{$curlErrNo}): " . ($curlErr ?: 'Empty response');
            self::$lastError = $errDetail;
            error_log("[Google OAuth] Token exchange network error: {$errDetail}");
            ActivityLogger::log('Google OAuth Failed', 'Auth', "Token exchange cURL error: {$errDetail}");
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['access_token'])) {
            $errCode = $data['error'] ?? 'unknown_error';
            $errDesc = $data['error_description'] ?? ($response ?: 'Unknown error');
            self::$lastError = "{$errCode}: {$errDesc}";
            error_log("[Google OAuth] Token exchange rejected by Google (HTTP {$httpCode}): error={$errCode}, description={$errDesc}, redirect_uri={$redirectUri}");
            ActivityLogger::log('Google OAuth Failed', 'Auth', "Google rejected token exchange (HTTP {$httpCode}): error={$errCode}");
            return null;
        }

        return $data;
    }

    public static function fetchUserInfo(string $accessToken): ?array {
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo';

        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json',
            'User-Agent: AlphaGamingShop-OAuth/1.0'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        if ($err || !$response) {
            error_log("[Google OAuth] fetchUserInfo cURL error: " . $err);
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['sub']) || empty($data['email'])) {
            error_log("[Google OAuth] fetchUserInfo failed (HTTP {$httpCode}): " . json_encode($data));
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
        if (empty($googleUser['id']) || empty($googleUser['email']) || !filter_var($googleUser['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('بيانات حساب Google المستلمة غير صالحة أو ينقصها المعرف الفريد والبريد الموثق.');
        }

        $googleId = (string)$googleUser['id'];
        $email = strtolower(trim($googleUser['email']));
        $name = trim($googleUser['name'] ?? 'Google User');
        $avatar = $googleUser['avatar'] ?? null;

        // 1. Try finding by Google Subject ID (Permanent immutable unique ID)
        $user = User::where('google_id', $googleId)->first();
        if ($user) {
            if (empty($user->avatar) && $avatar) {
                $user->update(['avatar' => $avatar]);
            }
            ActivityLogger::log('Google Login', 'Auth', "User {$user->email} logged in via verified Google OAuth");
            $user->is_new_google_link = false;
            return $user;
        }

        // 2. Try finding by verified Email and link Google account securely
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
            $user->is_new_google_link = true;
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
            'google_welcome_sent' => 0,
            'avatar' => $avatar,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'affiliate_code' => $affiliateCode
        ]);

        ActivityLogger::log('Google Register', 'Auth', "New user registered via Google OAuth: {$user->email}");
        $user->is_new_google_link = true;
        return $user;
    }

    /**
     * Send Google welcome email securely without blocking or failing login.
     * Guaranteed to never send duplicate emails for the same account.
     */
    public static function sendWelcomeEmailSafely(User $user): bool {
        // Prevent duplicate sending if already sent
        if ((int)($user->google_welcome_sent ?? 0) === 1) {
            return false;
        }

        try {
            $sent = MailService::sendGoogleWelcomeEmail($user->email, $user->name);
            // Mark as sent in DB regardless to avoid duplicate sends
            $user->update(['google_welcome_sent' => 1]);

            if ($sent) {
                ActivityLogger::log('Google Welcome Email Sent', 'Mail', "Welcome email sent successfully to {$user->email}");
                return true;
            } else {
                error_log("[Google OAuth] Could not deliver welcome email to {$user->email} via SMTP.");
                ActivityLogger::log('Google Welcome Email Failed', 'Mail', "Welcome email delivery failed for {$user->email}");
                return false;
            }
        } catch (\Throwable $e) {
            error_log("[Google OAuth] Welcome email exception for {$user->email}: " . $e->getMessage());
            $user->update(['google_welcome_sent' => 1]);
            return false;
        }
    }
}
