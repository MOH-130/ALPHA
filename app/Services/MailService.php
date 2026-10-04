<?php

namespace App\Services;

use App\Models\User;
use App\Services\ActivityLogger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private const LOG_FILE = __DIR__ . '/../../storage/logs/email.log';

    /**
     * Send Real OTP Verification Email via SMTP
     */
    public static function sendOtp(string $to, string $code, string $purpose = 'login'): bool {
        $subject = "رمز التحقق - ALPHA GAMING SHOP";

        $htmlBody = self::renderOtpTemplate($to, $code, $purpose);
        $textBody = self::renderOtpTextFallback($code);

        return self::send($to, $subject, $htmlBody, $textBody);
    }

    /**
     * Send Real Password Reset Email via SMTP
     */
    public static function sendPasswordReset(string $to, string $code): bool {
        $subject = "استعادة كلمة المرور - ALPHA GAMING SHOP";

        $htmlBody = self::renderPasswordResetTemplate($to, $code);
        $textBody = self::renderPasswordResetTextFallback($code);

        return self::send($to, $subject, $htmlBody, $textBody);
    }

    /**
     * Send Real Welcome Email via SMTP
     */
    public static function sendWelcomeEmail(string $to, string $name): bool {
        $subject = "مرحباً بك في ALPHA GAMING SHOP 🎮";

        $htmlBody = self::renderWelcomeTemplate($to, $name);
        $textBody = "مرحباً {$name}،\n\nأهلاً بك في متجر ALPHA GAMING SHOP! لقد تم تفعيل حسابك بنجاح.\n\nALPHA GAMING SHOP";

        return self::send($to, $subject, $htmlBody, $textBody);
    }

    /**
     * Send Real Google Account Welcome / Linked Email via SMTP
     */
    public static function sendGoogleWelcomeEmail(string $to, string $name): bool {
        $subject = "مرحباً بك في ALPHA GAMING SHOP 🎉";

        $htmlBody = self::renderGoogleWelcomeTemplate($to, $name);
        $textBody = self::renderGoogleWelcomeTextFallback($to, $name);

        return self::send($to, $subject, $htmlBody, $textBody);
    }

    /**
     * Send SMTP Diagnostic Test Email (Admin / CLI only)
     */
    public static function sendTestEmail(string $to): array {
        $subject = "اختبار إعدادات البريد SMTP - ALPHA GAMING SHOP";
        $time = date('Y-m-d H:i:s');
        $host = $_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST') ?: config('mail.mailers.smtp.host', 'smtp.gmail.com');
        $port = (int)($_ENV['MAIL_PORT'] ?? getenv('MAIL_PORT') ?: config('mail.mailers.smtp.port', 587));
        $encryption = $_ENV['MAIL_ENCRYPTION'] ?? getenv('MAIL_ENCRYPTION') ?: config('mail.mailers.smtp.encryption', 'tls');

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="UTF-8"><title>SMTP Test</title></head>
<body style="margin:0; padding:20px; background-color:#050811; font-family:'Segoe UI', Tahoma, sans-serif; direction:rtl; text-align:right; color:#e2e8f0;">
    <div style="max-width:550px; margin:0 auto; background:#0c1326; border:1px solid #00d2ff; border-radius:14px; padding:25px; box-shadow:0 10px 30px rgba(0,0,0,0.7);">
        <h2 style="color:#00ff88; margin-top:0;">⚡ نجاح اختبار اتصال SMTP - ALPHA GAMING SHOP</h2>
        <p style="color:#cbd5e1; font-size:14px; line-height:1.7;">
            تم إرسال هذه الرسالة بنجاح عبر خادم البريد الحقيقي (SMTP) للتحقق من إمكانية إرسال رسائل التحقق (OTP) للمستخدمين.
        </p>
        <table style="width:100%; border-collapse:collapse; margin:20px 0; font-size:13px; color:#94a3b8;">
            <tr><td style="padding:6px 0;"><strong>خادم SMTP:</strong></td><td style="color:#00d2ff; direction:ltr; text-align:left;">{$host}</td></tr>
            <tr><td style="padding:6px 0;"><strong>المنفذ (Port):</strong></td><td style="color:#00d2ff; direction:ltr; text-align:left;">{$port}</td></tr>
            <tr><td style="padding:6px 0;"><strong>التشفير:</strong></td><td style="color:#00d2ff; direction:ltr; text-align:left;">{$encryption}</td></tr>
            <tr><td style="padding:6px 0;"><strong>وقت الإرسال:</strong></td><td style="color:#ffffff;">{$time}</td></tr>
        </table>
        <div style="text-align:center; font-size:12px; color:#64748b; border-top:1px solid #1e293b; padding-top:15px;">
            ALPHA GAMING SHOP • نظام الحماية وإدارة البريد الإلكتروني
        </div>
    </div>
</body>
</html>
HTML;

        $textBody = "نجاح اختبار اتصال SMTP - ALPHA GAMING SHOP\n\nتم إرسال هذه الرسالة بنجاح في {$time}.\nالخادم: {$host}:{$port} ({$encryption})\n\nALPHA GAMING SHOP";

        $smtpPass = $_ENV['MAIL_PASSWORD'] ?? getenv('MAIL_PASSWORD') ?: config('mail.mailers.smtp.password', '');
        $ok = self::send($to, $subject, $htmlBody, $textBody);

        return [
            'success' => $ok,
            'sent_via' => $ok ? 'smtp' : 'failed',
            'to' => $to,
            'error' => $ok ? null : 'فشل الاتصال بخادم SMTP. يرجى مراجعة إعدادات البريد.'
        ];
    }

    /**
     * Core SMTP Dispatcher using PHPMailer.
     * Strictly requires real SMTP credentials.
     */
    public static function send(string $to, string $subject, string $htmlBody, string $textBody = ''): bool {
        // Read strictly from Environment Variables (or config fallback)
        $smtpHost = $_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST') ?: config('mail.mailers.smtp.host', 'smtp.gmail.com');
        $smtpPort = (int)($_ENV['MAIL_PORT'] ?? getenv('MAIL_PORT') ?: config('mail.mailers.smtp.port', 587));
        $smtpUser = $_ENV['MAIL_USERNAME'] ?? getenv('MAIL_USERNAME') ?: config('mail.mailers.smtp.username', '');
        $smtpPass = $_ENV['MAIL_PASSWORD'] ?? getenv('MAIL_PASSWORD') ?: config('mail.mailers.smtp.password', '');
        $smtpEnc  = strtolower((string)($_ENV['MAIL_ENCRYPTION'] ?? getenv('MAIL_ENCRYPTION') ?: config('mail.mailers.smtp.encryption', 'tls')));
        $fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? getenv('MAIL_FROM_ADDRESS') ?: config('mail.from.address', $smtpUser);
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? getenv('MAIL_FROM_NAME') ?: config('mail.from.name', 'ALPHA GAMING SHOP');
        $timeout  = 12;

        // Verify valid production SMTP credentials
        $hasSmtpCredentials = !empty($smtpHost) && !empty($smtpUser) && !empty($smtpPass)
            && $smtpUser !== 'null' && $smtpPass !== 'null'
            && !str_contains($smtpUser, 'YOUR_GMAIL')
            && !str_contains($smtpPass, 'YOUR_GOOGLE');

        if (!$hasSmtpCredentials) {
            self::logTransaction($to, $subject, 'FAILED', 'SMTP credentials not configured or placeholder values');
            return false;
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->Port       = $smtpPort;
            $mail->Timeout    = $timeout;
            $mail->CharSet    = 'UTF-8';

            if ($smtpEnc === 'ssl' || $smtpPort === 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $textBody;

            $mail->send();

            self::logTransaction($to, $subject, 'SUCCESS');
            return true;
        } catch (\Throwable $e) {
            $safeError = self::sanitizeError($e->getMessage(), $smtpPass);
            self::logTransaction($to, $subject, 'FAILED', $safeError);
            return false;
        }
    }

    /**
     * Dark Gaming RTL Template for OTP
     */
    private static function renderOtpTemplate(string $to, string $code, string $purpose = 'login'): string {
        $codeDigits = str_split($code);
        $digitsHtml = '';
        foreach ($codeDigits as $d) {
            $digitsHtml .= '<span style="display:inline-block; width:44px; height:54px; line-height:54px; margin:0 4px; background:#040711; border:2px solid #00d2ff; border-radius:10px; font-size:32px; font-weight:900; color:#00ff88; text-align:center; font-family:monospace, Courier New, sans-serif; box-shadow:0 0 15px rgba(0,210,255,0.3);">' . htmlspecialchars($d) . '</span>';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق - ALPHA GAMING SHOP</title>
</head>
<body style="margin:0; padding:0; background-color:#050811; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction:rtl; text-align:right; -webkit-font-smoothing:antialiased; color:#e2e8f0;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#050811; padding:30px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:580px; background:#0c1326; border-radius:18px; border:1px solid #1e293b; box-shadow:0 25px 60px rgba(0,0,0,0.85); overflow:hidden;">
                    <tr>
                        <td style="height:4px; background:linear-gradient(90deg, #00d2ff 0%, #00ff88 50%, #7928ca 100%); font-size:0; line-height:0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding:30px 36px 20px; text-align:center; background:#080e1c;">
                            <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(0,210,255,0.1); border:1px solid rgba(0,210,255,0.3); border-radius:999px; padding:6px 16px; font-size:12px; font-weight:800; color:#00d2ff; text-transform:uppercase; margin-bottom:12px;">
                                <span>⚡ ALPHA GAMING SHOP</span>
                                <span>•</span>
                                <span style="color:#00ff88;">رمز التحقق 🛡️</span>
                            </div>
                            <h1 style="margin:0; font-size:24px; font-weight:900; color:#ffffff; letter-spacing:0.5px;">
                                ALPHA GAMING SHOP
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 36px;">
                            <p style="font-size:16px; line-height:1.7; color:#f8fafc; margin:0 0 16px;">
                                مرحباً،
                            </p>
                            <p style="font-size:15px; line-height:1.8; color:#cbd5e1; margin:0 0 24px;">
                                لقد طلبت رمز تحقق لحسابك في <strong>ALPHA GAMING SHOP</strong>.
                            </p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background:#060a17; border:2px dashed rgba(0,210,255,0.4); border-radius:14px; margin:24px 0; text-align:center;">
                                <tr>
                                    <td style="padding:24px 16px;">
                                        <div style="font-size:13px; font-weight:800; color:#94a3b8; margin-bottom:14px;">
                                            رمز التحقق:
                                        </div>
                                        <div style="direction:ltr; unicode-bidi:bidi-override; margin:10px 0 16px;">
                                            {$digitsHtml}
                                        </div>
                                        <div style="display:inline-block; background:rgba(255,170,0,0.12); border:1px solid rgba(255,170,0,0.3); border-radius:6px; padding:6px 16px; font-size:13px; font-weight:700; color:#ffaa00;">
                                            ⏳ هذا الرمز صالح لمدة 5 دقائق.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <p style="font-size:14px; line-height:1.7; color:#94a3b8; margin:24px 0 0;">
                                إذا لم تطلب هذا الرمز، تجاهل هذه الرسالة.
                            </p>
                            <p style="font-size:14px; font-weight:700; color:#00ff88; margin:20px 0 0;">
                                ALPHA GAMING SHOP
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 36px; background:#080e1c; text-align:center; border-top:1px solid #1e293b;">
                            <p style="margin:0; font-size:12px; color:#64748b;">
                                جميع الحقوق محفوظة © ALPHA GAMING SHOP 2026
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

    private static function renderPasswordResetTemplate(string $to, string $code): string {
        $codeDigits = str_split($code);
        $digitsHtml = '';
        foreach ($codeDigits as $d) {
            $digitsHtml .= '<span style="display:inline-block; width:44px; height:54px; line-height:54px; margin:0 4px; background:#040711; border:2px solid #00d2ff; border-radius:10px; font-size:32px; font-weight:900; color:#00ff88; text-align:center; font-family:monospace, Courier New, sans-serif; box-shadow:0 0 15px rgba(0,210,255,0.3);">' . htmlspecialchars($d) . '</span>';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="UTF-8"><title>استعادة كلمة المرور - ALPHA GAMING SHOP</title></head>
<body style="margin:0; padding:0; background-color:#050811; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction:rtl; text-align:right; color:#e2e8f0;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#050811; padding:30px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:580px; background:#0c1326; border-radius:18px; border:1px solid #1e293b; box-shadow:0 25px 60px rgba(0,0,0,0.85); overflow:hidden;">
                    <tr><td style="height:4px; background:linear-gradient(90deg, #ffaa00 0%, #00d2ff 100%);"></td></tr>
                    <tr>
                        <td style="padding:30px 36px 20px; text-align:center; background:#080e1c;">
                            <h1 style="margin:0; font-size:24px; font-weight:900; color:#ffffff;">استعادة كلمة المرور</h1>
                            <div style="color:#00d2ff; font-size:13px; font-weight:700; margin-top:4px;">ALPHA GAMING SHOP</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 36px;">
                            <p style="font-size:16px; color:#f8fafc; margin:0 0 16px;">مرحباً،</p>
                            <p style="font-size:15px; color:#cbd5e1; line-height:1.7;">لقد تلقينا طلباً لاستعادة كلمة المرور لحسابك في <strong>ALPHA GAMING SHOP</strong>.</p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background:#060a17; border:2px dashed rgba(255,170,0,0.4); border-radius:14px; margin:24px 0; text-align:center;">
                                <tr>
                                    <td style="padding:24px 16px;">
                                        <div style="font-size:13px; font-weight:800; color:#94a3b8; margin-bottom:14px;">رمز التحقق:</div>
                                        <div style="direction:ltr; margin:10px 0 16px;">{$digitsHtml}</div>
                                        <div style="display:inline-block; background:rgba(255,170,0,0.12); border:1px solid rgba(255,170,0,0.3); border-radius:6px; padding:6px 16px; font-size:13px; font-weight:700; color:#ffaa00;">⏳ هذا الرمز صالح لمدة 5 دقائق.</div>
                                    </td>
                                </tr>
                            </table>
                            <p style="font-size:14px; color:#94a3b8;">إذا لم تطلب استعادة كلمة المرور، يرجى تجاهل هذه الرسالة وتأمين حسابك.</p>
                            <p style="font-size:14px; font-weight:700; color:#00ff88; margin-top:20px;">ALPHA GAMING SHOP</p>
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

    private static function renderWelcomeTemplate(string $to, string $name): string {
        $safeName = htmlspecialchars($name);
        return <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="UTF-8"><title>مرحباً بك في ALPHA GAMING SHOP</title></head>
<body style="margin:0; padding:0; background-color:#050811; font-family:'Segoe UI', Tahoma, sans-serif; direction:rtl; text-align:right; color:#e2e8f0;">
    <table role="presentation" width="100%" style="background-color:#050811; padding:30px 10px;">
        <tr><td align="center">
            <div style="max-width:580px; background:#0c1326; border-radius:18px; border:1px solid #1e293b; padding:30px; text-align:right;">
                <h2 style="color:#00ff88; margin-top:0;">🎮 مرحباً بك يا {$safeName}!</h2>
                <p style="color:#cbd5e1; line-height:1.8;">سعداء بانضمامك إلى مجتمع <strong>ALPHA GAMING SHOP</strong>. حسابك الآن جاهز ومفعل بالكامل للاستمتاع بأفضل عروض الألعاب والعتاد الاحترافي.</p>
                <div style="margin-top:25px; padding-top:15px; border-top:1px solid #1e293b; text-align:center; color:#64748b; font-size:13px;">
                    ALPHA GAMING SHOP
                </div>
            </div>
        </td></tr>
    </table>
</body>
</html>
HTML;
    }

    private static function renderGoogleWelcomeTemplate(string $to, string $name): string {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($to, ENT_QUOTES, 'UTF-8');
        $storeUrl = function_exists('base_url') ? base_url() : 'https://alpha-1-mshm.onrender.com';

        return <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحباً بك في ALPHA GAMING SHOP 🎉</title>
</head>
<body style="margin:0; padding:0; background-color:#050811; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction:rtl; text-align:right; -webkit-font-smoothing:antialiased; color:#e2e8f0;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#050811; padding:35px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:580px; background:#0c1326; border-radius:18px; border:1px solid #1e293b; box-shadow:0 25px 60px rgba(0,0,0,0.85); overflow:hidden;">
                    <!-- Top Gradient Accent -->
                    <tr>
                        <td style="height:4px; background:linear-gradient(90deg, #4285f4 0%, #00d2ff 35%, #00ff88 70%, #ffaa00 100%); font-size:0; line-height:0;">&nbsp;</td>
                    </tr>
                    <!-- Header -->
                    <tr>
                        <td style="padding:32px 36px 20px; text-align:center; background:#080e1c;">
                            <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(66,133,244,0.12); border:1px solid rgba(66,133,244,0.35); border-radius:999px; padding:6px 18px; font-size:12px; font-weight:800; color:#60a5fa; text-transform:uppercase; margin-bottom:14px;">
                                <span>🌐 GOOGLE ACCOUNT LINKED</span>
                                <span>•</span>
                                <span style="color:#00ff88;">تم الربط بنجاح ✅</span>
                            </div>
                            <h1 style="margin:0; font-size:24px; font-weight:900; color:#ffffff; letter-spacing:0.5px;">
                                ALPHA GAMING SHOP
                            </h1>
                        </td>
                    </tr>
                    <!-- Content Body -->
                    <tr>
                        <td style="padding:30px 36px;">
                            <p style="font-size:18px; font-weight:700; line-height:1.6; color:#f8fafc; margin:0 0 16px;">
                                مرحباً {$safeName} 👋
                            </p>
                            <p style="font-size:15px; line-height:1.8; color:#cbd5e1; margin:0 0 20px;">
                                لقد تم ربط حساب Google الخاص بك (<strong style="color:#00d2ff;">{$safeEmail}</strong>) بنجاح مع <strong>ALPHA GAMING SHOP</strong>.
                            </p>

                            <!-- Welcome Highlight Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background:#060a17; border:1px solid rgba(0,210,255,0.3); border-radius:14px; margin:22px 0; text-align:right;">
                                <tr>
                                    <td style="padding:22px 24px;">
                                        <div style="font-size:16px; font-weight:800; color:#00ff88; margin-bottom:8px;">
                                            مرحباً بك في متجرنا! 🎉
                                        </div>
                                        <div style="font-size:14px; line-height:1.8; color:#94a3b8;">
                                            يمكنك الآن تسجيل الدخول بسهولة باستخدام حساب Google والاستفادة من جميع خدمات ومميزات حسابك:
                                        </div>
                                        <ul style="margin:12px 0 0 0; padding-right:20px; font-size:13.5px; line-height:1.9; color:#cbd5e1;">
                                            <li>🎮 تصفح وشراء أحدث ألعاب PC، PS5، Xbox وبكجات الألعاب الرقمية.</li>
                                            <li>⚡ تسليم فوري وتفعيلات رسمية موثوقة.</li>
                                            <li>🛡️ أمان كامل ودعم فني متواجد لمساعدتك 24/7.</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <div style="text-align:center; margin:30px 0 20px;">
                                <a href="{$storeUrl}" style="display:inline-block; background:linear-gradient(135deg, #00d2ff, #0077ff); color:#060911; font-weight:900; font-size:15px; text-decoration:none; padding:12px 32px; border-radius:10px; box-shadow:0 4px 20px rgba(0,210,255,0.35);">
                                    استكشف متجر الألعاب الآن 🔥
                                </a>
                            </div>

                            <p style="font-size:15px; line-height:1.7; color:#f8fafc; margin:25px 0 0;">
                                نتمنى لك تجربة رائعة معنا ❤️
                            </p>
                            <p style="font-size:14px; font-weight:800; color:#00d2ff; margin:10px 0 0;">
                                فريق ALPHA GAMING SHOP
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px 36px; background:#080e1c; text-align:center; border-top:1px solid #1e293b;">
                            <p style="margin:0; font-size:12px; color:#64748b;">
                                تم إرسال هذه الرسالة تلقائياً لتأكيد ارتباط حساب Google الخاص بك بمتجر ALPHA GAMING SHOP.
                            </p>
                            <p style="margin:6px 0 0; font-size:12px; color:#475569;">
                                جميع الحقوق محفوظة © ALPHA GAMING SHOP 2026
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

    private static function renderGoogleWelcomeTextFallback(string $to, string $name): string {
        return "مرحباً {$name} 👋\n\n"
            . "لقد تم ربط حساب Google الخاص بك بنجاح مع ALPHA GAMING SHOP.\n\n"
            . "مرحباً بك في متجرنا! 🎉\n"
            . "يمكنك الآن تسجيل الدخول بسهولة باستخدام حساب Google والاستفادة من جميع خدمات ومميزات حسابك.\n\n"
            . "نتمنى لك تجربة رائعة معنا ❤️\n\n"
            . "فريق ALPHA GAMING SHOP";
    }

    private static function renderOtpTextFallback(string $code): string {
        return "مرحباً،\n\nلقد طلبت رمز تحقق لحسابك في ALPHA GAMING SHOP.\n\nرمز التحقق:\n{$code}\n\nهذا الرمز صالح لمدة 5 دقائق.\n\nإذا لم تطلب هذا الرمز، تجاهل هذه الرسالة.\n\nALPHA GAMING SHOP";
    }

    private static function renderPasswordResetTextFallback(string $code): string {
        return "مرحباً،\n\nرمز استعادة كلمة المرور لحسابك في ALPHA GAMING SHOP هو:\n{$code}\n\nهذا الرمز صالح لمدة 5 دقائق.\n\nALPHA GAMING SHOP";
    }

    private static function logTransaction(string $to, string $subject, string $status, ?string $error = null): void {
        $logDir = dirname(self::LOG_FILE);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $errorPart = $error ? " | Error: {$error}" : "";
        $logEntry = "[{$timestamp}] To: {$to} | Subject: {$subject} | Status: {$status}{$errorPart}" . PHP_EOL;

        @file_put_contents(self::LOG_FILE, $logEntry, FILE_APPEND);
    }

    private static function sanitizeError(string $error, string $password): string {
        if (!empty($password)) {
            $error = str_replace($password, '********', $error);
        }
        return substr(strip_tags($error), 0, 300);
    }
}
