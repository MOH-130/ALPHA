<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Services\GoogleAuthService;
use App\Controllers\AuthController;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Models\User;

echo "====================================================================\n";
echo "   HARDENED GOOGLE OAUTH SECURITY TEST SUITE (PRODUCTION & LOCAL)   \n";
echo "====================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertCheck(string $title, bool $condition, string $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo " [PASS] {$title}\n";
        if ($details) echo "        -> {$details}\n";
    } else {
        $failCount++;
        echo "![FAIL] {$title}\n";
        if ($details) echo "        -> {$details}\n";
    }
}

// -----------------------------------------------------------
// TEST 1: Sandbox & Simulation Code Completely Eradicated
// -----------------------------------------------------------
echo "\n--- 1. Sandbox & Simulation Elimination Check ---\n";

$sandboxViewPath = __DIR__ . '/../resources/views/auth/google-sandbox.blade.php';
assertCheck(
    "google-sandbox.blade.php view must be deleted from filesystem",
    !file_exists($sandboxViewPath),
    "File exists: " . (file_exists($sandboxViewPath) ? 'YES' : 'NO')
);

$hasSimulateMethodService = method_exists(GoogleAuthService::class, 'simulateGoogleAccount');
assertCheck(
    "GoogleAuthService::simulateGoogleAccount method must NOT exist",
    !$hasSimulateMethodService
);

$hasSimulateMethodController = method_exists(AuthController::class, 'googleSimulate');
assertCheck(
    "AuthController::googleSimulate method must NOT exist",
    !$hasSimulateMethodController
);

// Check web routes to ensure /auth/google/simulate returns 404 Not Found
require __DIR__ . '/../routes/web.php';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/auth/google/simulate';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_GET = [];
$_POST = [];
$simulateReq = new Request();
$simulateRes = Router::dispatch($simulateReq);
assertCheck(
    "Route POST /auth/google/simulate must return 404 Not Found",
    $simulateRes->getStatusCode() === 404,
    "Status code: " . $simulateRes->getStatusCode()
);

// -----------------------------------------------------------
// TEST 2: isConfigured() Strict Validation
// -----------------------------------------------------------
echo "\n--- 2. isConfigured() Strict Validation ---\n";

// Case A: Empty credentials
$_ENV['GOOGLE_CLIENT_ID'] = '';
$_ENV['GOOGLE_CLIENT_SECRET'] = '';
putenv('GOOGLE_CLIENT_ID=');
putenv('GOOGLE_CLIENT_SECRET=');
assertCheck(
    "isConfigured() returns false when credentials are empty",
    GoogleAuthService::isConfigured() === false
);

// Case B: Placeholder credentials
$_ENV['GOOGLE_CLIENT_ID'] = 'YOUR_GOOGLE_CLIENT_ID';
$_ENV['GOOGLE_CLIENT_SECRET'] = 'YOUR_GOOGLE_CLIENT_SECRET';
putenv('GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID');
putenv('GOOGLE_CLIENT_SECRET=YOUR_GOOGLE_CLIENT_SECRET');
assertCheck(
    "isConfigured() returns false for placeholder strings",
    GoogleAuthService::isConfigured() === false
);

// Case C: 'null' strings
$_ENV['GOOGLE_CLIENT_ID'] = 'null';
$_ENV['GOOGLE_CLIENT_SECRET'] = 'null';
putenv('GOOGLE_CLIENT_ID=null');
putenv('GOOGLE_CLIENT_SECRET=null');
assertCheck(
    "isConfigured() returns false for 'null' strings",
    GoogleAuthService::isConfigured() === false
);

// Case D: Valid formatted credentials
$_ENV['GOOGLE_CLIENT_ID'] = 'test-client-id.apps.googleusercontent.com';
$_ENV['GOOGLE_CLIENT_SECRET'] = 'GOCSPX-ValidTestSecret123456';
putenv('GOOGLE_CLIENT_ID=test-client-id.apps.googleusercontent.com');
putenv('GOOGLE_CLIENT_SECRET=GOCSPX-ValidTestSecret123456');
assertCheck(
    "isConfigured() returns true when valid credentials are set",
    GoogleAuthService::isConfigured() === true
);

// -----------------------------------------------------------
// TEST 3: googleRedirect() Behavior (Unconfigured vs Configured)
// -----------------------------------------------------------
echo "\n--- 3. googleRedirect() Security Behavior ---\n";

$authController = new AuthController();

// When unconfigured: MUST redirect to /login with flash error (NEVER show sandbox)
$_ENV['GOOGLE_CLIENT_ID'] = '';
$_ENV['GOOGLE_CLIENT_SECRET'] = '';
putenv('GOOGLE_CLIENT_ID=');
putenv('GOOGLE_CLIENT_SECRET=');
$_SESSION = [];

$unconfiguredResponse = $authController->googleRedirect();
$redirectLocation = $unconfiguredResponse->getHeaders()['Location'] ?? '';
$flashError = Session::flash('error');

assertCheck(
    "When unconfigured, googleRedirect() redirects to login page",
    strpos($redirectLocation, '/login') !== false,
    "Location: {$redirectLocation}"
);
assertCheck(
    "When unconfigured, friendly Arabic error is flashed",
    !empty($flashError) && strpos($flashError, 'غير مفعلة حالياً') !== false,
    "Flash: {$flashError}"
);

// When configured: MUST redirect to accounts.google.com
$_ENV['GOOGLE_CLIENT_ID'] = 'test-client-id.apps.googleusercontent.com';
$_ENV['GOOGLE_CLIENT_SECRET'] = 'GOCSPX-ValidTestSecret123456';
putenv('GOOGLE_CLIENT_ID=test-client-id.apps.googleusercontent.com');
putenv('GOOGLE_CLIENT_SECRET=GOCSPX-ValidTestSecret123456');

$configuredResponse = $authController->googleRedirect();
$googleAuthLocation = $configuredResponse->getHeaders()['Location'] ?? '';

assertCheck(
    "When configured, googleRedirect() redirects to official accounts.google.com endpoint",
    strpos($googleAuthLocation, 'https://accounts.google.com/o/oauth2/v2/auth') === 0,
    "Auth URL prefix: " . substr($googleAuthLocation, 0, 50) . "..."
);

assertCheck(
    "Auth URL includes client_id, response_type=code, state, and scopes",
    strpos($googleAuthLocation, 'client_id=test-client-id.apps.googleusercontent.com') !== false &&
    strpos($googleAuthLocation, 'response_type=code') !== false &&
    strpos($googleAuthLocation, 'state=') !== false &&
    strpos($googleAuthLocation, 'openid') !== false
);

// -----------------------------------------------------------
// TEST 4: Anti-CSRF State Protection & Single-Use Enforcement
// -----------------------------------------------------------
echo "\n--- 4. Callback CSRF Defense & Single-Use State ---\n";

// 4a. Tampered state in callback
$_GET = [
    'code' => 'test_authorization_code_123',
    'state' => 'forged_or_expired_state_token'
];
$_POST = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SESSION = [];
// Session has some other state
Session::set('_google_oauth_state', 'genuine_state_' . bin2hex(random_bytes(16)));

$req1 = new Request();
$tamperedCallbackResponse = $authController->googleCallback($req1);
$tamperedLocation = $tamperedCallbackResponse->getHeaders()['Location'] ?? '';
$tamperedError = Session::flash('error');

assertCheck(
    "Callback with tampered/mismatched state redirects to login",
    strpos($tamperedLocation, '/login') !== false
);
assertCheck(
    "Callback with tampered state flashes CSRF mismatch error",
    !empty($tamperedError) && strpos($tamperedError, 'CSRF State Mismatch') !== false,
    "Flash: {$tamperedError}"
);

// 4b. Missing state in callback
$_GET = ['code' => 'test_code'];
$req2 = new Request();
$missingStateResponse = $authController->googleCallback($req2);
$missingStateError = Session::flash('error');
assertCheck(
    "Callback with missing state is rejected",
    strpos($missingStateResponse->getHeaders()['Location'] ?? '', '/login') !== false &&
    !empty($missingStateError)
);

// 4c. Google OAuth error handling (e.g. user clicks cancel on Google consent screen)
$stateToken = bin2hex(random_bytes(32));
Session::set('_google_oauth_state', $stateToken);
$_GET = [
    'error' => 'access_denied',
    'state' => $stateToken
];
$reqError = new Request();
$deniedResponse = $authController->googleCallback($reqError);
$deniedError = Session::flash('error');
assertCheck(
    "Callback with error=access_denied is gracefully caught and state cleaned",
    strpos($deniedError, 'تم إلغاء عملية تسجيل الدخول') !== false &&
    !Session::has('_google_oauth_state')
);

// -----------------------------------------------------------
// TEST 5: Account Security Rules (Banned / Inactive / Unapproved)
// -----------------------------------------------------------
echo "\n--- 5. Account Status Enforcement (Banned / Inactive / Approval) ---\n";

// Create a banned user
$bannedEmail = 'banned_' . time() . '@alphagaming.store';
$bannedSub = 'sub_banned_' . bin2hex(random_bytes(8));
$bannedUser = User::create([
    'name' => 'Banned User',
    'email' => $bannedEmail,
    'password' => password_hash('Pass123!', PASSWORD_DEFAULT),
    'role' => 'customer',
    'status' => 'banned',
    'google_id' => $bannedSub,
    'is_approved' => 1
]);

// Attempting to resolve user via findOrCreateUser with banned user's data
$fetchedBannedUser = GoogleAuthService::findOrCreateUser([
    'id' => $bannedSub,
    'email' => $bannedEmail,
    'name' => 'Banned User',
    'verified' => true
]);

assertCheck(
    "Banned user retains banned status on OAuth lookup",
    $fetchedBannedUser->status === 'banned'
);

// Create an unapproved user (registration requires approval)
$unapprovedEmail = 'unapproved_' . time() . '@alphagaming.store';
$unapprovedSub = 'sub_unapproved_' . bin2hex(random_bytes(8));
$unapprovedUser = User::create([
    'name' => 'Unapproved User',
    'email' => $unapprovedEmail,
    'password' => password_hash('Pass123!', PASSWORD_DEFAULT),
    'role' => 'customer',
    'status' => 'active',
    'google_id' => $unapprovedSub,
    'is_approved' => 0
]);

$fetchedUnapproved = GoogleAuthService::findOrCreateUser([
    'id' => $unapprovedSub,
    'email' => $unapprovedEmail,
    'name' => 'Unapproved User',
    'verified' => true
]);

assertCheck(
    "Unapproved user retains is_approved = 0 on OAuth lookup",
    (int)$fetchedUnapproved->is_approved === 0
);

// Cleanup test users
$bannedUser->delete();
$unapprovedUser->delete();

// -----------------------------------------------------------
// TEST 6: Strict Data Validation on Google User Info
// -----------------------------------------------------------
echo "\n--- 6. Google User Info Strict Data Validation ---\n";

$threwMissingId = false;
try {
    GoogleAuthService::findOrCreateUser([
        'email' => 'noid@alphagaming.store',
        'name' => 'No ID'
    ]);
} catch (\InvalidArgumentException $e) {
    $threwMissingId = true;
}
assertCheck(
    "findOrCreateUser() throws exception if Google sub ID is missing",
    $threwMissingId
);

$threwInvalidEmail = false;
try {
    GoogleAuthService::findOrCreateUser([
        'id' => '123456789',
        'email' => 'invalid-email-string',
        'name' => 'Invalid Email'
    ]);
} catch (\InvalidArgumentException $e) {
    $threwInvalidEmail = true;
}
assertCheck(
    "findOrCreateUser() throws exception if email is invalid",
    $threwInvalidEmail
);

// -----------------------------------------------------------
// TEST 7: Summary
// -----------------------------------------------------------
echo "\n====================================================================\n";
echo "SECURITY TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "====================================================================\n";

if ($failCount > 0) {
    exit(1);
}
