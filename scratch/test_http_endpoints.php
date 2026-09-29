<?php

function testHttp(string $name, string $url, array $options = [], callable $validator = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    foreach ($options as $opt => $val) {
        curl_setopt($ch, $opt, $val);
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $success = false;
    if ($validator) {
        $success = $validator($httpCode, $response);
    } else {
        $success = ($httpCode >= 200 && $httpCode < 400);
    }

    echo ($success ? " [PASS] " : "![FAIL] ") . "{$name} (HTTP {$httpCode})\n";
    return $success;
}

echo "Testing Local Server Endpoints on http://127.0.0.1:8000 ...\n\n";

// 1. Login page contains Google Sign-In button
testHttp("Login page has Google OAuth link", "http://127.0.0.1:8000/login", [], function($code, $res) {
    return $code === 200 && strpos($res, 'auth/google') !== false;
});

// 2. Google OAuth entry endpoint redirects or displays sandbox
testHttp("Google redirect route (/auth/google)", "http://127.0.0.1:8000/auth/google", [], function($code, $res) {
    return ($code === 200 || $code === 302);
});

// 3. Callback with access_denied error redirects to login cleanly
testHttp("Callback handling on user cancellation (?error=access_denied)", "http://127.0.0.1:8000/auth/google/callback?error=access_denied", [], function($code, $res) {
    return $code === 302 && strpos($res, 'Location:') !== false;
});

// 4. Callback with invalid state rejected
testHttp("Callback rejects missing/invalid state", "http://127.0.0.1:8000/auth/google/callback?code=some_fake_code&state=fake_state", [], function($code, $res) {
    return $code === 302 && strpos($res, 'Location:') !== false;
});

// 5. Admin profile page accessible
testHttp("Admin Login page accessible", "http://127.0.0.1:8000/admin/login", [], function($code, $res) {
    return $code === 200;
});
