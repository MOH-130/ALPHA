<?php

namespace App\Services;

use App\Models\User;
use App\Core\Session;
use App\Services\ActivityLogger;
use App\Services\MailService;

class OtpService {
    public const EXPIRY_SECONDS = 300; // 5 minutes
    public const RESEND_COOLDOWN = 60; // 60 seconds cooldown
    public const MAX_ATTEMPTS = 5;     // Maximum failed attempts before invalidation

    /**
     * Generate secure 6-digit OTP, store cryptographic hash in DB, and dispatch real email via SMTP.
     * NEVER returns the OTP code string to Controller or View!
     *
     * @param User|string $userOrEmail
     * @param string $purpose ('login', 'register', 'reset')
     * @param array $context
     * @return bool True if generated and dispatched via SMTP successfully, false otherwise.
     */
    public static function sendOtp(User|string $userOrEmail, string $purpose = 'login', array $context = []): bool {
        Session::getInstance();

        // 1. Resolve User Model
        if ($userOrEmail instanceof User) {
            $user = $userOrEmail;
        } else {
            $email = strtolower(trim((string)$userOrEmail));
            $user = User::where('email', $email)->first();
            if (!$user) {
                return false;
            }
        }

        // 2. Cooldown check
        if (!self::canResend($user)) {
            return false;
        }

        // 3. Generate 6-digit random code using Cryptographically Secure Pseudo-Random Number Generator (CSPRNG)
        $code = (string)random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', time() + self::EXPIRY_SECONDS);

        // 4. Store ONLY cryptographic BCRYPT hash in database (Never plaintext)
        $user->update([
            'otp_code' => password_hash($code, PASSWORD_DEFAULT),
            'otp_expires_at' => $expiresAt,
            'otp_purpose' => $purpose
        ]);

        // 5. Track cooldown and reset attempts in session
        $_SESSION['_otp_last_sent_' . $user->id] = time();
        $_SESSION['_otp_attempts_' . $user->id] = 0;
        $_SESSION['_otp_purpose_' . $user->id] = $purpose;

        // 6. Dispatch real email via SMTP
        if ($purpose === 'reset') {
            $sent = MailService::sendPasswordReset($user->email, $code);
        } else {
            $sent = MailService::sendOtp($user->email, $code, $purpose);
        }

        // 7. Security: Immediately wipe raw code from memory
        unset($code);

        // 8. If email delivery failed, invalidate DB record immediately
        if (!$sent) {
            self::clear($user);
            return false;
        }

        ActivityLogger::log('OTP Security Code', 'Auth', "Secure OTP sent to {$user->email} for purpose: {$purpose}");
        return true;
    }

    /**
     * Verify the entered OTP code against the database hash.
     * Enforces rate limiting, attempt lockout, purpose validation, and immediate invalidation.
     */
    public static function verify(User $user, ?string $inputCode, ?string $expectedPurpose = null): bool {
        Session::getInstance();

        if (empty($inputCode) || strlen(trim($inputCode)) !== 6 || !ctype_digit(trim($inputCode))) {
            return false;
        }

        // Increment attempts count
        $attemptsKey = '_otp_attempts_' . $user->id;
        $attempts = ($_SESSION[$attemptsKey] ?? 0) + 1;
        $_SESSION[$attemptsKey] = $attempts;

        // Lockout after exceeding maximum attempts
        if ($attempts > self::MAX_ATTEMPTS) {
            self::clear($user);
            return false;
        }

        if (empty($user->otp_code) || empty($user->otp_expires_at)) {
            return false;
        }

        // Expiry check (5 minutes)
        if (time() > strtotime($user->otp_expires_at)) {
            self::clear($user);
            return false;
        }

        // Purpose isolation check
        if ($expectedPurpose !== null && !empty($user->otp_purpose) && $user->otp_purpose !== $expectedPurpose) {
            return false;
        }

        // Verify cryptographic hash
        if (password_verify(trim($inputCode), $user->otp_code)) {
            // Immediate invalidation on success to prevent replay attacks
            self::clear($user);
            unset($_SESSION[$attemptsKey]);

            // Activate account and verify email if not a reset purpose
            if ($expectedPurpose !== 'reset') {
                $user->update([
                    'email_verified_at' => $user->email_verified_at ?: date('Y-m-d H:i:s'),
                    'status' => ($user->status === 'pending_verification') ? 'active' : $user->status
                ]);
            }

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
        return max(0, self::MAX_ATTEMPTS - $attempts);
    }

    public static function clear(User $user): void {
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_purpose' => null
        ]);
        Session::getInstance();
        unset($_SESSION['_otp_attempts_' . $user->id]);
        unset($_SESSION['_otp_purpose_' . $user->id]);
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
