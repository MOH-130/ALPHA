<?php

require_once __DIR__ . '/bootstrap/app.php';

$pdo = \App\Core\Database::getPdo();

$adminHash = password_hash('password123', PASSWORD_BCRYPT);
$customerHash = password_hash('password123', PASSWORD_BCRYPT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$adminHash, 'admin@store.com']);
$stmt->execute([$customerHash, 'customer@store.com']);

echo "Updated passwords successfully!" . PHP_EOL;

// Test login attempt
$auth = \App\Core\Auth::getInstance();
$adminOk = $auth->attempt('admin@store.com', 'password123');
echo "Admin login test with 'password123': " . ($adminOk ? "SUCCESS" : "FAILED") . PHP_EOL;

$customerOk = $auth->attempt('customer@store.com', 'password123');
echo "Customer login test with 'password123': " . ($customerOk ? "SUCCESS" : "FAILED") . PHP_EOL;
