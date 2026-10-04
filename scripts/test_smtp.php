<?php

/**
 * ALPHA GAMING SHOP - CLI SMTP Diagnostics & Tester
 * Usage: php scripts/test_smtp.php recipient@gmail.com
 */

require_once __DIR__ . '/../bootstrap/app.php';

use App\Services\MailService;

$recipient = $argv[1] ?? '';

if (empty($recipient) || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    echo "========================================================\n";
    echo "   ALPHA GAMING SHOP - SMTP TEST UTILITY (CLI)         \n";
    echo "========================================================\n";
    echo "Usage: php scripts/test_smtp.php recipient@example.com\n\n";
    exit(1);
}

$host = config('mail.mailers.smtp.host', env('MAIL_HOST', 'smtp.gmail.com'));
$port = config('mail.mailers.smtp.port', env('MAIL_PORT', 587));
$enc = config('mail.mailers.smtp.encryption', env('MAIL_ENCRYPTION', 'tls'));
$user = config('mail.mailers.smtp.username', env('MAIL_USERNAME', ''));

echo "========================================================\n";
echo "   ALPHA GAMING SHOP - SMTP TEST UTILITY (CLI)         \n";
echo "========================================================\n";
echo "Host:       {$host}\n";
echo "Port:       {$port}\n";
echo "Encryption: {$enc}\n";
echo "Username:   " . (!empty($user) ? substr($user, 0, 3) . '***@' . (explode('@', $user)[1] ?? '') : '(None)') . "\n";
echo "Recipient:  {$recipient}\n";
echo "--------------------------------------------------------\n";
echo "Sending test email...\n";

$result = MailService::sendTestEmail($recipient);

if ($result['success']) {
    echo "\n>>> [SUCCESS] Test email sent successfully to {$recipient} via {$result['sent_via']}! <<<\n";
    exit(0);
} else {
    echo "\n>>> [FAILED] Could not send email. <<<\n";
    echo "Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    exit(1);
}
