<?php

namespace App\Models;

use App\Core\Model;

class Brand extends Model {
    protected string $table = 'brands';
    protected array $fillable = [
        'name_ar', 'name_en', 'slug', 'logo', 'description_ar', 'description_en', 'is_featured', 'status'
    ];

    public function getNameAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->name_en) ? $this->name_en : ($this->name_ar ?? $this->name_en ?? '');
    }

    public function productsCount(): int {
        return Product::where('brand_id', $this->id)->where('status', 'active')->count();
    }
}
