<?php

namespace App\Models;

use App\Core\Model;

class ShippingMethod extends Model {
    protected string $table = 'shipping_methods';
    protected array $fillable = [
        'code', 'name_ar', 'name_en', 'description_ar', 'description_en',
        'cost', 'free_threshold', 'status', 'sort_order'
    ];

    public function getNameAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->name_en) ? $this->name_en : ($this->name_ar ?? $this->name_en ?? '');
    }

    public function getDescriptionAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->description_en) ? $this->description_en : ($this->description_ar ?? '');
    }
}
