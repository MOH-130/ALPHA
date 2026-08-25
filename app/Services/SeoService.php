<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;

class SeoService {
    public static function generateProductSchema(Product $product): string {
        $currency = CurrencyService::getCurrent();
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => is_rtl() ? $product->name_ar : $product->name_en,
            'image' => [asset('uploads/products/' . ($product->primary_image ?: 'default.png'))],
            'description' => strip_tags(is_rtl() ? ($product->short_description_ar ?: $product->description_ar) : ($product->short_description_en ?: $product->description_en)),
            'sku' => $product->sku ?: ('SKU-' . $product->id),
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand() ? (is_rtl() ? $product->brand()->name_ar : $product->brand()->name_en) : config('app.name')
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => url('product/' . $product->slug),
                'priceCurrency' => $currency->code,
                'price' => $product->getEffectivePrice(),
                'priceValidUntil' => date('Y-12-31'),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $product->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
            ]
        ];

        if ($product->reviewsCount() > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->averageRating(),
                'reviewCount' => $product->reviewsCount()
            ];
        }

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    public static function generateOrganizationSchema(): string {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => Setting::get('site_name', config('app.name')),
            'url' => url('/'),
            'logo' => asset('assets/images/logo.png'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => Setting::get('contact_phone', '+966500000000'),
                'contactType' => 'Customer Service',
                'availableLanguage' => ['Arabic', 'English']
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    public static function generateBreadcrumbSchema(array $crumbs): string {
        $itemList = [];
        $i = 1;
        foreach ($crumbs as $title => $link) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $i++,
                'name' => $title,
                'item' => $link
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    public static function renderSitemapXml(): string {
        $products = Product::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Home
        $xml .= "  <url><loc>" . htmlspecialchars(url('/')) . "</loc><priority>1.0</priority><changefreq>daily</changefreq></url>" . PHP_EOL;
        $xml .= "  <url><loc>" . htmlspecialchars(url('shop')) . "</loc><priority>0.9</priority><changefreq>daily</changefreq></url>" . PHP_EOL;

        // Categories
        foreach ($categories as $cat) {
            $xml .= "  <url><loc>" . htmlspecialchars(url('category/' . $cat->slug)) . "</loc><priority>0.8</priority><changefreq>weekly</changefreq></url>" . PHP_EOL;
        }

        // Products
        foreach ($products as $prod) {
            $xml .= "  <url><loc>" . htmlspecialchars(url('product/' . $prod->slug)) . "</loc><priority>0.7</priority><changefreq>daily</changefreq></url>" . PHP_EOL;
        }

        $xml .= '</urlset>';
        return $xml;
    }
}
