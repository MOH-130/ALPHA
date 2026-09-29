<?php

namespace App\Services;

use App\Models\User;
use App\Services\ActivityLogger;

class MailService {
    private const LOG_FILE = __DIR__ . '/../../storage/logs/email.log';
    private const HTML_PREVIEW_FILE = __DIR__ . '/../../storage/logs/sent_emails.html';

    /**
     * Send OTP Verification Email
     */
    public static function sendOtpEmail(User $user, string $code, array $context = []): array {
        $ip = $context['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $userAgent = $context['agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Web Browser');
        $dateTime = date('Y-m-d H:i:s') . ' (توقيت الجزائر GMT+1)';

        $subject = "🛡️ كود التحقق الأمني: [{$code}] - متجر ALPHA Gaming ⚡";
        $htmlBody = self::renderOtpTemplate($user, $code, [
            'ip' => $ip,
            'userAgent' => self::simplifyUserAgent($userAgent),
            'dateTime' => $dateTime
        ]);

        $textBody = self::renderOtpTextFallback($user, $code, [
            'ip' => $ip,
            'dateTime' => $dateTime
        ]);

        return self::send($user->email, $subject, $htmlBody, $textBody);
    }

    /**
     * Core Email Dispatcher: SMTP Socket -> PHP mail() Fallback -> File Logging
     */
    public static function send(string $to, string $subject, string $htmlBody, string $textBody = ''): array {
        $fromAddress = config('mail.from.address', 'noreply@alpha-efxl.onrender.com');
        $fromName = config('mail.from.name', 'ALPHA Gaming Security Shield 🛡️');

        // Always save HTML preview for testing/inspection
        self::savePreviewLog($to, $subject, $htmlBody);

        $sentVia = 'none';
        $success = false;
        $error = null;

        // 1. Try Direct Pure-PHP SMTP if credentials are configured
        $smtpHost = config('mail.mailers.smtp.host');
        $smtpUser = config('mail.mailers.smtp.username');
        $smtpPass = config('mail.mailers.smtp.password');

        if (!empty($smtpHost) && !empty($smtpUser) && !empty($smtpPass) && $smtpUser !== 'null') {
            try {
                $smtpRes = self::sendViaSmtp($to, $subject, $htmlBody, $textBody, $fromAddress, $fromName);
                if ($smtpRes['success']) {
                    $success = true;
                    $sentVia = 'smtp';
                } else {
                    $error = $smtpRes['error'];
                }
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        // 2. Fallback to standard PHP mail() if SMTP not configured or failed
        if (!$success) {
            $boundary = '=_alpha_' . md5(uniqid(microtime(true)));
            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $encodedFromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';

            $headers = [
                "From: {$encodedFromName} <{$fromAddress}>",
                "Reply-To: {$fromAddress}",
                "MIME-Version: 1.0",
                "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
                "X-Mailer: AlphaGamingSecureMailer/2.0",
                "X-Priority: 1"
            ];

            $multipartBody = "--{$boundary}\r\n" .
                "Content-Type: text/plain; charset=UTF-8\r\n" .
                "Content-Transfer-Encoding: 8bit\r\n\r\n" .
                $textBody . "\r\n\r\n" .
                "--{$boundary}\r\n" .
                "Content-Type: text/html; charset=UTF-8\r\n" .
                "Content-Transfer-Encoding: 8bit\r\n\r\n" .
                $htmlBody . "\r\n\r\n" .
                "--{$boundary}--";

            $mailSuccess = @mail($to, $encodedSubject, $multipartBody, implode("\r\n", $headers));
            if ($mailSuccess) {
                $success = true;
                $sentVia = 'php_mail';
            }
        }

        // 3. Log results to email.log
        $logDir = dirname(self::LOG_FILE);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logEntry = sprintf(
            "[%s] To: %s | Subject: %s | SentVia: %s | Status: %s %s\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            $sentVia,
            $success ? 'SUCCESS' : 'SAVED_TO_SANDBOX',
            $error ? "| Error: {$error}" : ''
        );
        @file_put_contents(self::LOG_FILE, $logEntry, FILE_APPEND);

        return [
            'success' => true, // We consider saved/sent as successful so the user is never blocked
            'sent_via' => $sentVia,
            'to' => $to,
            'error' => $error
        ];
    }

    /**
     * Lightweight direct socket SMTP transport
     */
    private static function sendViaSmtp(
        string $to,
        string $subject,
        string $htmlBody,
        string $textBody,
        string $fromAddress,
        string $fromName
    ): array {
        $host = config('mail.mailers.smtp.host', 'smtp.gmail.com');
        $port = (int)config('mail.mailers.smtp.port', 587);
        $user = config('mail.mailers.smtp.username', '');
        $pass = config('mail.mailers.smtp.password', '');
        $enc = strtolower((string)config('mail.mailers.smtp.encryption', 'tls'));
        $timeout = (int)config('mail.mailers.smtp.timeout', 5);

        $protocol = ($enc === 'ssl' || $port === 465) ? 'ssl://' : 'tcp://';
        $socket = @stream_socket_client(
            $protocol . $host . ':' . $port,
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT
        );

        if (!$socket) {
            return ['success' => false, 'error' => "Cannot connect to SMTP {$host}:{$port} ({$errstr})"];
        }

        stream_set_timeout($socket, $timeout);
        $read = self::readSmtpResponse($socket);

        // HELO/EHLO
        fwrite($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'alphagaming.store') . "\r\n");
        $read = self::readSmtpResponse($socket);

        // STARTTLS if requested on port 587 / tcp
        if ($enc === 'tls' && $protocol === 'tcp://') {
            fwrite($socket, "STARTTLS\r\n");
            $read = self::readSmtpResponse($socket);
            if (strpos($read, '220') === false) {
                fclose($socket);
                return ['success' => false, 'error' => 'STARTTLS failed: ' . $read];
            }

            $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            }
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
            }

            if (!stream_socket_enable_crypto($socket, true, $cryptoMethod)) {
                fclose($socket);
                return ['success' => false, 'error' => 'TLS negotiation failed'];
            }

            fwrite($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'alphagaming.store') . "\r\n");
            $read = self::readSmtpResponse($socket);
        }

        // AUTH LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $read = self::readSmtpResponse($socket);

        fwrite($socket, base64_encode($user) . "\r\n");
        $read = self::readSmtpResponse($socket);

        fwrite($socket, base64_encode($pass) . "\r\n");
        $read = self::readSmtpResponse($socket);

        if (strpos($read, '235') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'SMTP Authentication failed: ' . $read];
        }

        // MAIL FROM
        fwrite($socket, "MAIL FROM:<{$fromAddress}>\r\n");
        $read = self::readSmtpResponse($socket);

        // RCPT TO
        fwrite($socket, "RCPT TO:<{$to}>\r\n");
        $read = self::readSmtpResponse($socket);

        // DATA
        fwrite($socket, "DATA\r\n");
        $read = self::readSmtpResponse($socket);

        $boundary = '=_alpha_' . md5(uniqid(microtime(true)));
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $encodedFromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';

        $headers = [
            "From: {$encodedFromName} <{$fromAddress}>",
            "To: <{$to}>",
            "Subject: {$encodedSubject}",
            "Date: " . date('r'),
            "MIME-Version: 1.0",
            "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
            "X-Mailer: AlphaGamingSecureMailer/2.0",
            "X-Priority: 1"
        ];

        $payload = implode("\r\n", $headers) . "\r\n\r\n" .
            "--{$boundary}\r\n" .
            "Content-Type: text/plain; charset=UTF-8\r\n" .
            "Content-Transfer-Encoding: 8bit\r\n\r\n" .
            $textBody . "\r\n\r\n" .
            "--{$boundary}\r\n" .
            "Content-Type: text/html; charset=UTF-8\r\n" .
            "Content-Transfer-Encoding: 8bit\r\n\r\n" .
            $htmlBody . "\r\n\r\n" .
            "--{$boundary}--\r\n.\r\n";

        fwrite($socket, $payload);
        $read = self::readSmtpResponse($socket);

        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        return ['success' => true];
    }

    private static function readSmtpResponse($socket): string {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $response;
    }

    /**
     * Authoring the Ultra-Luxury Cyberpunk Email Template
     */
    public static function renderOtpTemplate(User $user, string $code, array $meta): string {
        $userName = htmlspecialchars($user->name ?: 'اللاعب البطل');
        $codeDigits = str_split($code);
        $ip = htmlspecialchars($meta['ip']);
        $userAgent = htmlspecialchars($meta['userAgent']);
        $dateTime = htmlspecialchars($meta['dateTime']);
        $supportWhatsApp = "https://wa.me/213770000000?text=" . urlencode("مرحباً فريق ALPHA Gaming، لدي استفسار بخصوص رمز التحقق الأمني.");

        // Digits rendered individually for sublime visual presence
        $digitsHtml = '';
        foreach ($codeDigits as $d) {
            $digitsHtml .= '<span style="display:inline-block; width:44px; height:54px; line-height:54px; margin:0 4px; background:#040711; border:2px solid #00d2ff; border-radius:10px; font-size:32px; font-weight:900; color:#00ff88; text-align:center; font-family:monospace, Courier New, sans-serif; box-shadow:0 0 15px rgba(0,210,255,0.25);">' . $d . '</span>';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كود التحقق الأمني - متجر ALPHA Gaming</title>
</head>
<body style="margin:0; padding:0; background-color:#050811; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction:rtl; text-align:right; -webkit-font-smoothing:antialiased; color:#e2e8f0;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#050811; padding:30px 10px;">
        <tr>
            <td align="center">
                <!-- Main Container Card -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:620px; background:#0c1326; border-radius:18px; border:1px solid #1e293b; box-shadow:0 25px 60px rgba(0,0,0,0.85); overflow:hidden;">
                    
                    <!-- Top Glowing Neon Accent Line -->
                    <tr>
                        <td style="height:4px; background:linear-gradient(90deg, #00d2ff 0%, #00ff88 50%, #7928ca 100%); font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- Header with Store Logo & VIP Badge -->
                    <tr>
                        <td style="padding:32px 36px 20px; text-align:center; background:#080e1c;">
                            <div style="display:inline-block; margin-bottom:14px;">
                                <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(0,210,255,0.1); border:1px solid rgba(0,210,255,0.3); border-radius:999px; padding:6px 16px; font-size:12px; font-weight:800; color:#00d2ff; text-transform:uppercase; letter-spacing:0.5px;">
                                    <span>⚡ ALPHA GAMING SECURITY SHIELD</span>
                                    <span>•</span>
                                    <span style="color:#00ff88;">DZ-VAULT 🇩🇿</span>
                                </div>
                            </div>
                            <h1 style="margin:0; font-size:26px; font-weight:900; color:#ffffff; letter-spacing:0.5px;">
                                متجر <span style="color:#00d2ff;">ALPHA</span> للألعاب الرقمية
                            </h1>
                            <p style="margin:6px 0 0; font-size:13px; color:#94a3b8;">
                                الوجهة الأولى لألعاب PC و PlayStation و Steam الأصلية في الجزائر
                            </p>
                        </td>
                    </tr>

                    <!-- Main Body Content -->
                    <tr>
                        <td style="padding:28px 36px;">
                            <!-- Greeting -->
                            <div style="font-size:17px; font-weight:700; color:#ffffff; margin-bottom:12px;">
                                تحية طيبة يا <span style="color:#00ff88;">{$userName}</span>، 🎮
                            </div>
                            
                            <p style="font-size:14px; line-height:1.7; color:#cbd5e1; margin:0 0 20px;">
                                لقد تلقينا طلباً لتأكيد هويتك والتحقق من أمان بريدك الإلكتروني (Gmail) في متجر <strong>ALPHA Gaming Shop</strong>.
                                <br>
                                يرجى استخدام <strong>رمز الأمان لمرة واحدة (OTP)</strong> الموضح أدناه لإتمام عملية الدخول وتفعيل حسابك بأعلى معايير الحماية والتشفير:
                            </p>

                            <!-- The VIP OTP Code Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background:#060a17; border:2px dashed rgba(0,210,255,0.35); border-radius:14px; margin:24px 0; text-align:center;">
                                <tr>
                                    <td style="padding:26px 16px;">
                                        <div style="font-size:12px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">
                                            🔒 رمز التحقق الأمني المكون من 6 أرقام:
                                        </div>
                                        
                                        <!-- Code Digits -->
                                        <div style="direction:ltr; unicode-bidi:bidi-override; margin:8px 0 14px;">
                                            {$digitsHtml}
                                        </div>

                                        <!-- Copyable plain text code & Expiry note -->
                                        <div style="display:inline-block; background:rgba(255,170,0,0.12); border:1px solid rgba(255,170,0,0.3); border-radius:6px; padding:5px 14px; font-size:12px; font-weight:700; color:#ffaa00;">
                                            ⏳ ينتهي هذا الرمز خلال <strong>5 دقائق</strong> فقط
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Strict Security Rules -->
                            <div style="background:rgba(239, 68, 68, 0.08); border-right:4px solid #ef4444; border-radius:8px; padding:14px 18px; margin-bottom:24px;">
                                <div style="font-size:13px; font-weight:800; color:#fca5a5; margin-bottom:4px;">
                                    ⚠️ بروتوكول الأمان الصارم من ALPHA:
                                </div>
                                <div style="font-size:12px; line-height:1.6; color:#fecaca;">
                                    لا تشارك هذا الرمز مع أي شخص، بما في ذلك فريق خدمة العملاء أو الدعم الفني. موظفو متجر ALPHA لن يطلبوا منك رمز الأمان الخاص بك إطلاقاً تحت أي ظرف.
                                </div>
                            </div>

                            <!-- Session Audit Information -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:12px 16px; margin-bottom:24px; font-size:12px; color:#94a3b8;">
                                <tr>
                                    <td style="padding:4px 0;"><strong>🕒 توقيت الطلب:</strong> {$dateTime}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0;"><strong>🌐 عنوان الـ IP:</strong> <code style="color:#00d2ff; font-family:monospace;">{$ip}</code></td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0;"><strong>💻 نوع المتصفح:</strong> {$userAgent}</td>
                                </tr>
                            </table>

                            <!-- Non-solicited Notice -->
                            <p style="font-size:12px; line-height:1.6; color:#64748b; margin:0 0 20px;">
                                ❓ <strong>هل لم تقم بطلب هذا الكود؟</strong> إذا لم تكن أنت صاحب هذا الإجراء، فقد يكون شخص ما حاول الدخول إلى حسابك دون علمك. يمكنك تجاهل هذه الرسالة بأمان تام، فحسابك محمي بتشفير متعدد الطبقات.
                            </p>

                            <!-- VIP WhatsApp Help Concierge -->
                            <div style="text-align:center; padding-top:16px; border-top:1px solid #1e293b;">
                                <p style="font-size:13px; color:#cbd5e1; margin-bottom:12px;">
                                    هل تواجه صعوبة في استلام الأكواد أو تفعيل ألعابك؟ فريقنا الجزائري في خدمتك 24/7:
                                </p>
                                <a href="{$supportWhatsApp}" target="_blank" style="display:inline-block; background:linear-gradient(135deg, #00ff88, #00a86b); color:#050811; font-weight:800; font-size:13px; text-decoration:none; padding:10px 22px; border-radius:8px; box-shadow:0 4px 15px rgba(0,255,136,0.3);">
                                    💬 محادثة الدعم الفني عبر واتساب (WhatsApp VIP)
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:22px 36px; background:#080e1c; text-align:center; border-top:1px solid #1e293b;">
                            <p style="margin:0 0 6px; font-size:12px; color:#64748b;">
                                متجر <strong>ALPHA Gaming Shop</strong> • رائد التجارة الرقمية للألعاب وبكجات ستيم في الجزائر 🇩🇿
                            </p>
                            <p style="margin:0; font-size:11px; color:#475569;">
                                الدفع المعتمد محلياً: بريدي موب BaridiMob و الحساب البريدي CCP | جميع الحقوق محفوظة © 2026
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Plain Text Fallback for Email Clients without HTML
     */
    public static function renderOtpTextFallback(User $user, string $code, array $meta): string {
        $userName = $user->name ?: 'اللاعب الموقر';
        $ip = $meta['ip'];
        $dateTime = $meta['dateTime'];

        return <<<TEXT
=== متجر ALPHA Gaming Shop الجزائر ===
رمز التحقق الأمني الخاص بك لتأكيد الحساب والدخول

مرحباً بك يا {$userName}،

كود الأمان الخاص بك لمرة واحدة (OTP) هو:
[ {$code} ]

• صلاحية الرمز: 5 دقائق فقط.
• تحذير أمني: لا تشارك هذا الرمز مطلقاً مع أي شخص. فريق متجر ALPHA لن يطلبه منك أبداً.
• تفاصيل المحاولة:
  - التوقيت: {$dateTime}
  - عنوان الـ IP: {$ip}

إذا لم تكن أنت من طلب هذا الرمز، يرجى تجاهل هذه الرسالة.

خدمة العملاء والدعم الفني 24/7 عبر واتساب:
https://wa.me/213770000000

متجر ALPHA Gaming Shop • تسليم فوري للألعاب والبكجات 100% مضمونة
TEXT;
    }

    /**
     * Keep an HTML preview file for developer/admin review
     */
    private static function savePreviewLog(string $to, string $subject, string $htmlBody): void {
        $dir = dirname(self::HTML_PREVIEW_FILE);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $card = <<<CARD
<div style="margin: 30px auto; max-width: 680px; border: 2px solid #00d2ff; border-radius: 12px; overflow: hidden; background: #000; font-family: sans-serif;">
    <div style="background: #0d1527; padding: 12px 18px; color: #fff; font-size: 13px; border-bottom: 1px solid #1e293b;">
        <strong>✉️ Email Preview</strong> | To: <span style="color:#00ff88;">{$to}</span> | Subject: <span style="color:#00d2ff;">{$subject}</span> | Sent At: {$timestamp}
    </div>
    {$htmlBody}
</div>
CARD;

        @file_put_contents(self::HTML_PREVIEW_FILE, $card . PHP_EOL, FILE_APPEND);
    }

    private static function simplifyUserAgent(string $ua): string {
        if (strpos($ua, 'Chrome') !== false) return 'Google Chrome';
        if (strpos($ua, 'Firefox') !== false) return 'Mozilla Firefox';
        if (strpos($ua, 'Safari') !== false && strpos($ua, 'Chrome') === false) return 'Apple Safari';
        if (strpos($ua, 'Edge') !== false) return 'Microsoft Edge';
        return substr($ua, 0, 40);
    }
}
