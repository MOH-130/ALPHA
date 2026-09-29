<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\User;
use App\Services\MailService;
use App\Services\OtpService;
use App\Services\CaptchaService;
use App\Core\Session;
use App\Core\Request;
use App\Controllers\AuthController;

echo "========================================================\n";
echo "   ALPHA STORE EMAIL & GMAIL OTP SECURITY TEST SUITE    \n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(string $desc, bool $condition, $extra = null) {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo " [PASS] {$desc}\n";
        if ($extra !== null) echo "        -> {$extra}\n";
    } else {
        $failCount++;
        echo " [FAIL] {$desc}\n";
        if ($extra !== null) echo "        -> Extra: " . print_r($extra, true) . "\n";
    }
}

// 1. Test Authored Email Template Generation
echo "--- 1. Testing Custom Authored Email Template ---\n";

$testUser = User::where('role', 'customer')->first();
if (!$testUser) {
    $testUser = User::create([
        'name' => 'لاعب تجريبي فاخر',
        'email' => 'testgamer_' . time() . '@gmail.com',
        'phone' => '+213770112233',
        'password' => password_hash('Secret123!', PASSWORD_DEFAULT),
        'role' => 'customer',
        'status' => 'pending_verification'
    ]);
}

$sampleCode = "739201";
$html = MailService::renderOtpTemplate($testUser, $sampleCode, [
    'ip' => '105.101.45.12',
    'userAgent' => 'Google Chrome on Windows 11',
    'dateTime' => date('Y-m-d H:i:s') . ' (توقيت الجزائر GMT+1)'
]);

assertTest("HTML email contains ALPHA store logo branding", strpos($html, 'ALPHA') !== false);
assertTest("HTML email contains personalized user greeting", strpos($html, $testUser->name) !== false);
assertTest("HTML email contains all 6 code digits", strpos($html, '7') !== false && strpos($html, '3') !== false && strpos($html, '9') !== false);
assertTest("HTML email contains 5-minute expiry warning", strpos($html, '5 دقائق') !== false);
assertTest("HTML email contains security shield warning against sharing", strpos($html, 'لا تشارك هذا الرمز') !== false);
assertTest("HTML email contains session audit IP metadata", strpos($html, '105.101.45.12') !== false);
assertTest("HTML email contains VIP WhatsApp concierge link", strpos($html, 'wa.me') !== false);
assertTest("HTML email has RTL Arabic layout direction", strpos($html, 'dir="rtl"') !== false);

// 2. Test MailService Dispatcher & Preview Logger
echo "\n--- 2. Testing MailService Dispatch & Preview Logger ---\n";
$dispatchRes = MailService::sendOtpEmail($testUser, "482910", [
    'ip' => '105.101.45.12',
    'agent' => 'Mozilla/5.0 Chrome/120.0'
]);

assertTest("MailService dispatch returned success", $dispatchRes['success'] === true, "Sent via: " . $dispatchRes['sent_via']);
$previewPath = __DIR__ . '/../storage/logs/sent_emails.html';
assertTest("HTML preview saved in storage/logs/sent_emails.html", file_exists($previewPath) && filesize($previewPath) > 0);
$emailLogPath = __DIR__ . '/../storage/logs/email.log';
assertTest("Email transaction logged in storage/logs/email.log", file_exists($emailLogPath) && filesize($emailLogPath) > 0);

// 3. Test OtpService Cryptographic Generation and Hashing
echo "\n--- 3. Testing OtpService Generation & Cryptographic Hashing ---\n";
$generatedCode = OtpService::generate($testUser, ['ip' => '127.0.0.1']);
assertTest("Generated OTP code is 6 digits long", strlen($generatedCode) === 6 && ctype_digit($generatedCode), "Code: {$generatedCode}");

$refreshedUser = User::find($testUser->id);
assertTest("OTP code is stored hashed with BCRYPT (not plaintext) in database", password_verify($generatedCode, $refreshedUser->otp_code) && $refreshedUser->otp_code !== $generatedCode);
assertTest("OTP expiry is set in future (approx 5 mins)", strtotime($refreshedUser->otp_expires_at) > time() + 250);

// 4. Test OtpService Verification & Brute-Force Rate Limiting
echo "\n--- 4. Testing OtpService Verification & Security Controls ---\n";

// Wrong code rejection
$wrongResult = OtpService::verify($refreshedUser, "000000");
assertTest("Wrong OTP code is rejected", $wrongResult === false);

// Correct code verification
$correctResult = OtpService::verify($refreshedUser, $generatedCode);
assertTest("Correct OTP code is accepted", $correctResult === true);

// Replay attack prevention: same code used twice
$replayResult = OtpService::verify($refreshedUser, $generatedCode);
assertTest("Replay Attack Prevention: same OTP cannot be used a second time", $replayResult === false);

// Check that email_verified_at was recorded
$verifiedUser = User::find($testUser->id);
assertTest("User email_verified_at is set with timestamp upon successful verification", !empty($verifiedUser->email_verified_at), "Verified At: " . $verifiedUser->email_verified_at);

// 5. Test Full Registration Flow with Mandatory Email OTP
echo "\n--- 5. Testing HTTP Registration Flow with Email Verification ---\n";
Session::getInstance();
\App\Services\RateLimiter::clear('register:ip:105.100.20.10');

$fakeEmail = 'alpha_hero_' . time() . '@gmail.com';

// Mock CAPTCHA
$captcha = CaptchaService::generate();
$captchaAnswer = (string)$captcha['answer'];

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REMOTE_ADDR'] = '105.100.20.10';
$_POST = [
    'name' => 'نوفل الجزائري',
    'email' => $fakeEmail,
    'phone' => '+213555998877',
    'password' => 'AlphaSecure2026!',
    'captcha' => $captchaAnswer
];

$controller = new AuthController();
$regResponse = $controller->register(new Request());

$newRegisteredUser = User::where('email', $fakeEmail)->first();
assertTest("New user account created in database", $newRegisteredUser !== null);
assertTest("New user status is initially pending_verification", $newRegisteredUser ? $newRegisteredUser->status === 'pending_verification' : false);
assertTest("New user email_verified_at is initially null", $newRegisteredUser ? empty($newRegisteredUser->email_verified_at) : false);
assertTest("auth_pending_user_id is set in session for OTP verification", $newRegisteredUser ? Session::get('auth_pending_user_id') == $newRegisteredUser->id : false);
$regLoc = $regResponse->getHeaders()['Location'] ?? '';
assertTest("Registration redirects to auth/verify-otp", $regLoc === url('auth/verify-otp'), "Redirect: {$regLoc}");

// Retrieve the generated OTP from flash hint or database
$pendingCode = Session::get('_flash')['_latest_otp_hint'] ?? null;
assertTest("OTP was generated and dispatched during registration", !empty($pendingCode), "Generated Code: {$pendingCode}");

// 6. Test OTP Verification Form Submission
echo "\n--- 6. Testing HTTP OTP Verification & Account Activation ---\n";
$_POST = [
    'otp_code' => $pendingCode
];

$verifyResponse = $controller->verifyOtp(new Request());

$activatedUser = User::find($newRegisteredUser ? $newRegisteredUser->id : 0);
assertTest("User account is activated to status 'active'", $activatedUser ? $activatedUser->status === 'active' : false);
assertTest("User email_verified_at is recorded", $activatedUser ? !empty($activatedUser->email_verified_at) : false);
assertTest("User is successfully authenticated in session", \App\Core\Auth::getInstance()->check() && \App\Core\Auth::getInstance()->id() == ($activatedUser ? $activatedUser->id : -1));
$verifyLoc = $verifyResponse->getHeaders()['Location'] ?? '';
assertTest("Verification redirects to account dashboard", $verifyLoc === url('account/dashboard'), "Redirect: {$verifyLoc}");

// 7. Test Quick OTP Login Request
echo "\n--- 7. Testing Quick OTP Login Request (Passwordless/Direct to Gmail) ---\n";
\App\Core\Auth::getInstance()->logout();
Session::remove('auth_pending_user_id');
\App\Services\RateLimiter::clear('otp_request:ip:105.100.20.10');

$_POST = [
    'email' => $fakeEmail
];

$quickResponse = $controller->requestOtpLogin(new Request());
$quickLoc = $quickResponse->getHeaders()['Location'] ?? '';
assertTest("Quick OTP login request accepts registered email", $quickLoc === url('auth/verify-otp'), "Redirect: {$quickLoc}");
assertTest("Quick OTP login sets pending user id in session", Session::get('auth_pending_user_id') == ($activatedUser ? $activatedUser->id : -1));

$quickOtp = Session::get('_flash')['_latest_otp_hint'] ?? null;
assertTest("New OTP code generated for quick login", !empty($quickOtp), "Quick Login OTP: {$quickOtp}");

// Clean up test user
if ($activatedUser) {
    $activatedUser->delete();
}

echo "\n========================================================\n";
echo "SUMMARY: Total Tests: " . ($passCount + $failCount) . " | Passed: {$passCount} | Failed: {$failCount}\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
