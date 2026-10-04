<?php
require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

echo "--- Checking Category Slugs ---\n";
$catSlugs = ['pc-console-games', 'steam-mega-packs', 'ea-sports-fc', 'minecraft', 'subscriptions'];
foreach ($catSlugs as $slug) {
    $cat = Category::where('slug', $slug)->first();
    if ($cat) {
        $count = Product::where('category_id', $cat->id)->where('status', 'active')->count();
        echo "  [OK] Category: {$cat->name_ar} (slug: {$slug}) -> {$count} active products\n";
    } else {
        echo "  [FAIL] Category missing: {$slug}\n";
    }
}

echo "\n--- Checking Brand Slugs ---\n";
$brandSlugs = ['xbox', 'playstation', 'steam', 'ubisoft', 'rockstar-games', 'ea-sports', 'adobe'];
foreach ($brandSlugs as $slug) {
    $brand = Brand::where('slug', $slug)->first();
    if ($brand) {
        $count = Product::where('brand_id', $brand->id)->where('status', 'active')->count();
        echo "  [OK] Brand: {$brand->name_ar} (slug: {$slug}) -> {$count} active products\n";
    } else {
        echo "  [FAIL] Brand missing: {$slug}\n";
    }
}
