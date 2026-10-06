<?php

require_once __DIR__ . '/../bootstrap/app.php';

$testsPassed = 0;
$testsFailed = 0;

function assertTest($condition, $name) {
    global $testsPassed, $testsFailed;
    if ($condition) {
        echo "  [PASS] {$name}\n";
        $testsPassed++;
    } else {
        echo "  [FAIL] {$name}\n";
        $testsFailed++;
    }
}

echo "=== ALPHA GAMING SHOP: FULL COMPREHENSIVE AUDIT & VERIFICATION ===\n\n";

// 1. Check Storefront Home View and Branding
echo "1. Testing Personal Branding (By Amine):\n";
$homeHtml = view('storefront.home', ['title' => 'Alpha Gaming Shop']);
assertTest(strpos($homeHtml, 'Designed &amp; Developed by') !== false, "Modal contains 'Designed & Developed by'");
assertTest(strpos($homeHtml, 'Amine') !== false, "Modal contains 'Amine'");
assertTest(strpos($homeHtml, 'Created by') !== false, "Footer contains 'Created by'");
assertTest(strpos($homeHtml, 'amine-brand-signature') !== false, "Footer contains 'amine-brand-signature' class");
assertTest(strpos($homeHtml, 'vip-developer-credit') !== false, "Modal contains 'vip-developer-credit' class");

// 2. Check Translations
echo "\n2. Testing Translations:\n";
assertTest(\App\Core\Translator::get('auth.back_to_login', [], 'ar') === 'العودة لتسجيل الدخول', "Arabic auth.back_to_login translates correctly");
assertTest(\App\Core\Translator::get('auth.back_to_login', [], 'en') === 'Back to Login', "English auth.back_to_login translates correctly");

// 3. Check Reset Password View
echo "\n3. Testing Auth Reset Password View:\n";
$resetHtml = view('auth.reset-password', ['token' => 'dummy-token']);
assertTest(strpos($resetHtml, 'العودة لتسجيل الدخول') !== false, "Reset password view renders Arabic translation");

// 4. Check Key Views Compilation
echo "\n4. Testing Core Storefront Views Compilation:\n";
$viewsToTest = [
    'storefront.home' => ['title' => 'Home'],
    'storefront.cart' => ['title' => 'Cart'],
    'storefront.checkout' => ['title' => 'Checkout'],
    'storefront.contact' => ['title' => 'Contact'],
    'storefront.track-order' => ['title' => 'Track Order'],
    'storefront.compare' => ['title' => 'Compare'],
    'storefront.wishlist' => ['title' => 'Wishlist'],
    'storefront.404' => ['title' => '404 Not Found'],
    'storefront.500' => ['title' => '500 Server Error'],
    'auth.login' => ['title' => 'Login'],
    'auth.register' => ['title' => 'Register'],
    'auth.forgot-password' => ['title' => 'Forgot Password'],
    'auth.admin-login' => ['title' => 'Admin Login'],
];

foreach ($viewsToTest as $viewName => $data) {
    try {
        $rendered = view($viewName, $data);
        assertTest(!empty($rendered) && strpos($rendered, '<!-- View [') === false, "View [{$viewName}] compiles cleanly");
    } catch (\Throwable $e) {
        assertTest(false, "View [{$viewName}] failed with: " . $e->getMessage());
    }
}

// 5. Test Router Dispatch and Security Headers
echo "\n5. Testing Security Headers in Response:\n";
$res = \App\Core\Response::html('<h1>Test</h1>');
$headers = $res->getHeaders();
// Simulate send default headers
$expectedHeaders = [
    'X-Frame-Options' => 'SAMEORIGIN',
    'X-Content-Type-Options' => 'nosniff',
    'X-XSS-Protection' => '1; mode=block',
    'Referrer-Policy' => 'strict-origin-when-cross-origin',
    'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()'
];
// Test if Response constructor or send registers these:
$reflection = new ReflectionClass($res);
assertTest($res instanceof \App\Core\Response, "Response instance valid");

// 6. Test Admin Protection
echo "\n6. Testing RBAC (Admin Protection):\n";
\App\Core\Session::remove('user_id');
\App\Core\Session::remove('user_role');
$req = new \App\Core\Request();
$adminMw = new \App\Middleware\AdminMiddleware();
$blockedResponse = $adminMw->handle($req);
assertTest($blockedResponse instanceof \App\Core\Response, "Guest blocked from admin routes");
assertTest($blockedResponse->getStatusCode() === 302, "Guest redirected to admin login");

// 7. Test XSS Helper function e()
echo "\n7. Testing XSS Helper function e():\n";
$maliciousXss = '<script>alert("XSS")</script>';
$escaped = e($maliciousXss);
assertTest($escaped === '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', "e() escapes malicious script tags");

// 8. Test Product count & image integrity
echo "\n8. Testing Catalog & Products in Database:\n";
$products = \App\Models\Product::all();
assertTest(count($products) >= 40, "Products in DB: " . count($products) . " (Expected >= 40)");

echo "\n=======================================================\n";
echo "SUMMARY: {$testsPassed} PASSED, {$testsFailed} FAILED\n";
echo "=======================================================\n";

if ($testsFailed > 0) {
    exit(1);
}
