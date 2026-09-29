<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Services\GoogleAuthService;
use App\Models\User;
use App\Core\Session;

echo "========================================================\n";
echo "   GOOGLE OAUTH 2.0 DUAL-ENVIRONMENT AUDIT & TEST SUITE   \n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(string $title, bool $condition, string $details = '') {
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
// TEST 1: Redirect URI in Local Environment
// -----------------------------------------------------------
echo "\n--- 1. Local Environment Redirect URI ---\n";
unset($_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_PROTO'], $_SERVER['HTTP_X_FORWARDED_HOST']);
$_SERVER['HTTP_HOST'] = 'localhost:8000';
$_ENV['APP_URL'] = 'http://localhost:8000';
putenv('APP_URL=http://localhost:8000');

$localUri = GoogleAuthService::getRedirectUri();
assertTest(
    "Local Redirect URI should be exactly http://localhost:8000/auth/google/callback",
    $localUri === 'http://localhost:8000/auth/google/callback',
    "Result: {$localUri}"
);

// -----------------------------------------------------------
// TEST 2: Redirect URI in Production on Render (Explicit APP_URL)
// -----------------------------------------------------------
echo "\n--- 2. Production Environment Redirect URI (Explicit APP_URL) ---\n";
$_ENV['APP_URL'] = 'https://alpha-efxl.onrender.com';
putenv('APP_URL=https://alpha-efxl.onrender.com');
unset($_SERVER['HTTP_HOST'], $_SERVER['HTTP_X_FORWARDED_HOST'], $_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_PROTO']);

$prodUri = GoogleAuthService::getRedirectUri();
assertTest(
    "Production Redirect URI should be https://alpha-efxl.onrender.com/auth/google/callback",
    $prodUri === 'https://alpha-efxl.onrender.com/auth/google/callback',
    "Result: {$prodUri}"
);

// -----------------------------------------------------------
// TEST 3: Redirect URI in Production behind Render Load Balancer (Reverse Proxy Headers)
// -----------------------------------------------------------
echo "\n--- 3. Reverse Proxy & SSL Offloading Detection (Render Load Balancer) ---\n";
// Even if APP_URL in .env was left as localhost
$_ENV['APP_URL'] = 'http://localhost:8000';
putenv('APP_URL=http://localhost:8000');
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
$_SERVER['HTTP_HOST'] = 'alpha-efxl.onrender.com';

$proxyUri = GoogleAuthService::getRedirectUri();
assertTest(
    "Reverse proxy with live host detects HTTPS and production domain automatically",
    $proxyUri === 'https://alpha-efxl.onrender.com/auth/google/callback',
    "Result: {$proxyUri}"
);

// -----------------------------------------------------------
// TEST 4: Google Auth URL Generation & Security Parameters
// -----------------------------------------------------------
echo "\n--- 4. Google Auth URL Generation & Cryptographic State ---\n";
$_ENV['APP_URL'] = 'https://alpha-efxl.onrender.com';
putenv('APP_URL=https://alpha-efxl.onrender.com');

$authUrl = GoogleAuthService::getAuthUrl();
$parsedUrl = parse_url($authUrl);
parse_str($parsedUrl['query'] ?? '', $queryParams);

assertTest(
    "Auth URL points to Google OAuth 2.0 endpoint",
    $parsedUrl['host'] === 'accounts.google.com' && $parsedUrl['path'] === '/o/oauth2/v2/auth'
);

assertTest(
    "Response type is code",
    ($queryParams['response_type'] ?? '') === 'code'
);

assertTest(
    "Scope includes openid, email, profile",
    ($queryParams['scope'] ?? '') === 'openid email profile'
);

assertTest(
    "Prompt is select_account",
    ($queryParams['prompt'] ?? '') === 'select_account'
);

assertTest(
    "Redirect URI in params matches production callback",
    ($queryParams['redirect_uri'] ?? '') === 'https://alpha-efxl.onrender.com/auth/google/callback',
    "redirect_uri: " . ($queryParams['redirect_uri'] ?? '')
);

$stateInSession = $_SESSION['_google_oauth_state'] ?? null;
assertTest(
    "Cryptographic state token generated (64 hex characters) and saved in session",
    !empty($stateInSession) && strlen($stateInSession) === 64 && ($queryParams['state'] ?? '') === $stateInSession,
    "State: {$stateInSession}"
);

// -----------------------------------------------------------
// TEST 5: CSRF State Verification & Replay Protection
// -----------------------------------------------------------
echo "\n--- 5. Anti-CSRF State Verification & Single-Use Replay Protection ---\n";

// 5a. Tampered state must be rejected
$tamperedResult = GoogleAuthService::verifyState('tampered_fake_state_123');
assertTest(
    "Tampered state is rejected",
    $tamperedResult === false
);

// Re-generate valid state for test
$_SESSION['_google_oauth_state'] = $stateInSession;
$validResult = GoogleAuthService::verifyState($stateInSession);
assertTest(
    "Legitimate state is accepted",
    $validResult === true
);

// 5b. Single-use check: same state submitted again must fail
$replayResult = GoogleAuthService::verifyState($stateInSession);
assertTest(
    "Replay attack prevention: Same state used a second time is rejected",
    $replayResult === false
);

// 5c. Empty/null state must fail
assertTest(
    "Empty or null state is rejected",
    GoogleAuthService::verifyState(null) === false && GoogleAuthService::verifyState('') === false
);

// -----------------------------------------------------------
// TEST 6: User Management (Sub ID & Email Linking)
// -----------------------------------------------------------
echo "\n--- 6. User Sub ID & Account Linking ---\n";

$testGoogleSub = 'google_sub_' . bin2hex(random_bytes(8));
$testEmail = 'oauth_test_' . time() . '@alphagaming.store';

// 6a. Create new user via Google
$newUser = GoogleAuthService::findOrCreateUser([
    'id' => $testGoogleSub,
    'email' => $testEmail,
    'name' => 'Alpha Tester',
    'avatar' => 'https://ui-avatars.com/api/?name=Alpha+Tester',
    'verified' => true
]);

assertTest(
    "User created with Google Sub ID as unique identifier",
    !empty($newUser->id) && $newUser->google_id === $testGoogleSub && $newUser->email === $testEmail,
    "User ID: {$newUser->id}, Google ID: {$newUser->google_id}"
);

// 6b. Subsequent login by same Google ID returns same user
$sameUser = GoogleAuthService::findOrCreateUser([
    'id' => $testGoogleSub,
    'email' => $testEmail,
    'name' => 'Alpha Tester Updated',
    'verified' => true
]);

assertTest(
    "Subsequent Google login with same Sub ID returns existing account (no duplicate)",
    $sameUser->id === $newUser->id
);

// 6c. Existing user linking with new Google ID
$traditionalEmail = 'traditional_' . time() . '@alphagaming.store';
$traditionalUser = User::create([
    'name' => 'Traditional User',
    'email' => $traditionalEmail,
    'password' => password_hash('Secret123!', PASSWORD_DEFAULT),
    'role' => 'customer',
    'status' => 'active',
    'is_approved' => 1
]);

$linkedSub = 'google_sub_linked_' . bin2hex(random_bytes(8));
$linkedUser = GoogleAuthService::findOrCreateUser([
    'id' => $linkedSub,
    'email' => $traditionalEmail,
    'name' => 'Traditional User',
    'verified' => true
]);

assertTest(
    "Existing user account matched by verified email and securely linked with Google Sub ID",
    $linkedUser->id === $traditionalUser->id && $linkedUser->google_id === $linkedSub
);

// Cleanup test rows
$newUser->delete();
$traditionalUser->delete();

// -----------------------------------------------------------
// TEST 7: Helper url() and base_url() Dual-Environment Check
// -----------------------------------------------------------
echo "\n--- 7. Helper url() and base_url() Dual-Environment Check ---\n";

$_ENV['APP_URL'] = 'https://alpha-efxl.onrender.com';
putenv('APP_URL=https://alpha-efxl.onrender.com');
assertTest(
    "url('auth/google') resolves to Render HTTPS domain in production",
    url('auth/google') === 'https://alpha-efxl.onrender.com/auth/google',
    "URL: " . url('auth/google')
);

$_ENV['APP_URL'] = 'http://localhost:8000';
putenv('APP_URL=http://localhost:8000');
unset($_SERVER['HTTP_HOST'], $_SERVER['HTTP_X_FORWARDED_PROTO']);
assertTest(
    "url('auth/google') resolves to Local HTTP domain in development",
    url('auth/google') === 'http://localhost:8000/auth/google',
    "URL: " . url('auth/google')
);

// -----------------------------------------------------------
// SUMMARY
// -----------------------------------------------------------
echo "\n========================================================\n";
echo "AUDIT RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
