<?php

namespace App\Models;

use App\Core\Model;

class Product extends Model {
    protected string $table = 'products';
    protected array $fillable = [
        'category_id', 'brand_id', 'name_ar', 'name_en', 'slug', 'sku', 'barcode',
        'short_description_ar', 'short_description_en', 'description_ar', 'description_en',
        'price', 'compare_price', 'cost_price', 'stock_quantity', 'low_stock_threshold',
        'primary_image', 'video_url', 'is_featured', 'is_best_seller', 'is_deal',
        'deal_price', 'deal_start', 'deal_end', 'meta_title', 'meta_description', 'meta_keywords',
        'status', 'views_count', 'sales_count', 'weight'
    ];

    public function getNameAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->name_en) ? $this->name_en : ($this->name_ar ?? $this->name_en ?? '');
    }

    public function getShortDescriptionAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->short_description_en) ? $this->short_description_en : ($this->short_description_ar ?? '');
    }

    public function getDescriptionAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->description_en) ? $this->description_en : ($this->description_ar ?? '');
    }

    public function category(): ?Category {
        return $this->category_id ? Category::find($this->category_id) : null;
    }

    public function brand(): ?Brand {
        return $this->brand_id ? Brand::find($this->brand_id) : null;
    }

    public function images(): array {
        return ProductImage::where('product_id', $this->id)->orderBy('sort_order', 'ASC')->get();
    }

    public function attributes(): array {
        return ProductAttribute::where('product_id', $this->id)->get();
    }

    public function reviews(): array {
        return Review::where('product_id', $this->id)->where('is_approved', 1)->orderByDesc('id')->get();
    }

    public function averageRating(): float {
        $avg = Review::where('product_id', $this->id)->where('is_approved', 1)->avg('rating');
        return round($avg, 1) ?: 5.0;
    }

    public function reviewsCount(): int {
        return Review::where('product_id', $this->id)->where('is_approved', 1)->count();
    }

    public function getEffectivePrice(): float {
        if ($this->is_deal && !empty($this->deal_price) && $this->isDealActive()) {
            return (float) $this->deal_price;
        }
        return (float) $this->price;
    }

    public function getDiscountPercentage(): int {
        $effective = $this->getEffectivePrice();
        $compare = (float) ($this->compare_price ?: 0);
        if ($compare > $effective && $compare > 0) {
            return (int) round((($compare - $effective) / $compare) * 100);
        }
        return 0;
    }

    public function isDealActive(): bool {
        if (!$this->is_deal) return false;
        $now = date('Y-m-d H:i:s');
        if ($this->deal_start && $this->deal_start > $now) return false;
        if ($this->deal_end && $this->deal_end < $now) return false;
        return true;
    }

    public function isInStock(): bool {
        return $this->stock_quantity > 0 && $this->status === 'active';
    }

    public function isLowStock(): bool {
        return $this->stock_quantity <= ($this->low_stock_threshold ?: 5) && $this->stock_quantity > 0;
    }
}
