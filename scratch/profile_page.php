<?php
require 'bootstrap/app.php';

$pdo = \App\Core\Database::getPdo();

$queryCount = 0;
$queryLog = [];

// Profile all queries using PDO wrapper or custom execution
// Let's test StoreController directly
$start = microtime(true);
$ctrl = new \App\Controllers\StoreController();
$req = new \App\Core\Request();

ob_start();
$res = $ctrl->index($req);
if ($res instanceof \App\Core\Response) {
    $res->send();
} elseif (is_string($res)) {
    echo $res;
}
$content = ob_get_clean();
$end = microtime(true);

echo "Total Page Render Time: " . round(($end - $start) * 1000, 2) . " ms\n";

// Count queries in StoreController@index
$catCount = \App\Models\Category::where('status', 'active')->where('is_featured', 1)->count();
echo "Featured Categories: " . $catCount . "\n";
$dealCount = \App\Models\Product::where('status', 'active')->where('is_deal', 1)->count();
echo "Deals Count: " . $dealCount . "\n";
$bestCount = \App\Models\Product::where('status', 'active')->count();
echo "Products Count: " . $bestCount . "\n";
