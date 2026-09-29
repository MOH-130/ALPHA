<?php

namespace App\Services;

use App\Models\User;
use App\Core\Session;
use App\Services\ActivityLogger;
use App\Services\MailService;

class OtpService {
    public const EXPIRY_SECONDS = 300; // 5 minutes
    public const RESEND_COOLDOWN = 60; // 60 seconds
    private const LOG_FILE = __DIR__ . '/../../storage/logs/otp.log';

    /**
     * Generate a cryptographic 6-digit OTP code, save hashed version, and send email
     */
    public static function generate(User $user, array $context = []): string {
        Session::getInstance();

        $code = (string)random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', time() + self::EXPIRY_SECONDS);

        // Store hashed OTP code in user record for high security
        $user->update([
            'otp_code' => password_hash($code, PASSWORD_DEFAULT),
            'otp_expires_at' => $expiresAt
        ]);

        $_SESSION['_otp_last_sent_' . $user->id] = time();
        $_SESSION['_otp_attempts_' . $user->id] = 0;

        self::send($user, $code, $context);

        return $code;
    }

    /**
     * Verify the entered OTP code with rate-limiting and replay protection
     */
    public static function verify(User $user, ?string $inputCode): bool {
        Session::getInstance();

        if (empty($inputCode) || strlen(trim($inputCode)) !== 6) {
            return false;
        }

        // Increment attempts
        $attemptsKey = '_otp_attempts_' . $user->id;
        $attempts = ($_SESSION[$attemptsKey] ?? 0) + 1;
        $_SESSION[$attemptsKey] = $attempts;

        if ($attempts > 5) {
            self::clear($user);
            return false;
        }

        if (empty($user->otp_code) || empty($user->otp_expires_at)) {
            return false;
        }

        if (time() > strtotime($user->otp_expires_at)) {
            self::clear($user);
            return false;
        }

        if (password_verify(trim($inputCode), $user->otp_code)) {
            self::clear($user);
            unset($_SESSION[$attemptsKey]);

            // Mark email as verified and account active
            $user->update([
                'email_verified_at' => $user->email_verified_at ?: date('Y-m-d H:i:s'),
                'status' => ($user->status === 'pending_verification') ? 'active' : $user->status
            ]);

            return true;
        }

        return false;
    }

    public static function canResend(User $user): bool {
        Session::getInstance();
        $lastSent = $_SESSION['_otp_last_sent_' . $user->id] ?? 0;
        return (time() - $lastSent) >= self::RESEND_COOLDOWN;
    }

    public static function secondsUntilResend(User $user): int {
        Session::getInstance();
        $lastSent = $_SESSION['_otp_last_sent_' . $user->id] ?? 0;
        return max(0, self::RESEND_COOLDOWN - (time() - $lastSent));
    }

    public static function remainingAttempts(User $user): int {
        Session::getInstance();
        $attempts = $_SESSION['_otp_attempts_' . $user->id] ?? 0;
        return max(0, 5 - $attempts);
    }

    public static function clear(User $user): void {
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null
        ]);
        Session::getInstance();
        unset($_SESSION['_otp_attempts_' . $user->id]);
    }

    /**
     * Dispatch OTP to user's email via MailService and write activity logs
     */
    public static function send(User $user, string $code, array $context = []): void {
        $logDir = dirname(self::LOG_FILE);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] OTP for {$user->email} (User ID: {$user->id}): {$code} (Expires in 5m)" . PHP_EOL;
        @file_put_contents(self::LOG_FILE, $logEntry, FILE_APPEND);

        ActivityLogger::log('OTP Security Code', 'Auth', "OTP generated for {$user->email}");

        // Store OTP in flash for dev/demo display helper so user is never locked out during development
        Session::flash('_latest_otp_hint', $code);
        Session::flash('_latest_otp_email', self::maskEmail($user->email));

        // Dispatch VIP Authored HTML Email via MailService
        MailService::sendOtpEmail($user, $code, $context);
    }

    public static function maskEmail(string $email): string {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;
        $name = $parts[0];
        $domain = $parts[1];
        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '*';
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', max(3, $len - 3)) . substr($name, -1);
        }
        return $maskedName . '@' . $domain;
    }
}
