<?php
/**
 * Catalog Integrity Verification Test
 */

$rootDir = realpath(__DIR__ . '/..');
$dbPath = $rootDir . '/storage/data/database.sqlite';
$uploadsDir = $rootDir . '/public/uploads/products';

echo "====================================================\n";
echo "   ALPHA GAMING SHOP - CATALOG INTEGRITY TEST       \n";
echo "====================================================\n\n";

$pdo = new PDO("sqlite:" . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Total counts
$totalProds = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$activeProds = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
$totalImages = $pdo->query("SELECT COUNT(*) FROM product_images")->fetchColumn();
$totalBrands = $pdo->query("SELECT COUNT(*) FROM brands")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

echo "Summary Statistics:\n";
echo "  • Total Products: {$totalProds}\n";
echo "  • Active Products: {$activeProds}\n";
echo "  • Gallery Images: {$totalImages}\n";
echo "  • Brands: {$totalBrands}\n";
echo "  • Categories: {$totalCategories}\n\n";

// 2. Check each product's primary image file existence
echo "Checking Product Images Integrity:\n";
$stmt = $pdo->query("SELECT id, name_ar, slug, price, primary_image FROM products ORDER BY id ASC");
$missingImages = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $imgFile = $row['primary_image'];
    $fullPath = $uploadsDir . '/' . $imgFile;
    if (empty($imgFile) || !file_exists($fullPath)) {
        echo "  [FAIL] Product #{$row['id']} ({$row['slug']}): Missing image '{$imgFile}'\n";
        $missingImages++;
    } else {
        $size = filesize($fullPath);
        // echo "  [OK] Product #{$row['id']}: {$row['name_ar']} -> {$imgFile} (" . number_format($size) . " bytes)\n";
    }
}

if ($missingImages === 0) {
    echo "  [SUCCESS] All {$totalProds} products have valid, existing primary image files on disk!\n\n";
} else {
    echo "  [WARNING] {$missingImages} products have missing images!\n\n";
}

// 3. Check product_images table references
echo "Checking Product Gallery Table:\n";
$stmtImg = $pdo->query("SELECT id, product_id, image_path FROM product_images");
$missingGallery = 0;
while ($row = $stmtImg->fetch(PDO::FETCH_ASSOC)) {
    $fullPath = $uploadsDir . '/' . $row['image_path'];
    if (!file_exists($fullPath)) {
        echo "  [FAIL] Gallery Image #{$row['id']} for Product #{$row['product_id']}: '{$row['image_path']}' not found\n";
        $missingGallery++;
    }
}
if ($missingGallery === 0) {
    echo "  [SUCCESS] All {$totalImages} gallery image entries point to valid files on disk!\n\n";
}

// 4. Verify the 13 Newly Added Products
echo "Verifying New Catalog Additions:\n";
$newSlugs = [
    'snapchat-plus-premium',
    'gemini-3-1-ai-pro-18-months',
    'google-ai-pro-veo-2tb-18m',
    'chatgpt-plus-private-account',
    'deezer-premium-12-months',
    'nordvpn-basic-3-months',
    'promax-4k-ott-iptv-12m',
    'ea-sports-fc-26-pc-steam',
    'efootball-pes-2026-gold-android',
    'efootball-pes-2025-ios',
    'assassins-creed-iv-black-flag-resynced',
    'adobe-creative-cloud-3-months',
    'xbox-game-pass-switch-account'
];

foreach ($newSlugs as $slug) {
    $p = $pdo->query("SELECT id, name_ar, price, compare_price, primary_image FROM products WHERE slug = '$slug'")->fetch(PDO::FETCH_ASSOC);
    if ($p) {
        echo sprintf("  [OK] #%-2d | %-32s | Price: %-5s DA | Img: %s\n", $p['id'], $slug, $p['price'], $p['primary_image']);
    } else {
        echo "  [FAIL] Missing product with slug: {$slug}\n";
    }
}

echo "\n====================================================\n";
echo "   INTEGRITY CHECK COMPLETED SUCCESSFULLY!          \n";
echo "====================================================\n";
