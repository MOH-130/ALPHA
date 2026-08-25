<?php

namespace App\Models;

use App\Core\Model;

class ProductAttribute extends Model {
    protected string $table = 'product_attributes';
    protected array $fillable = ['product_id', 'name_ar', 'name_en', 'values_json', 'price_modifier'];

    public function getNameAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->name_en) ? $this->name_en : ($this->name_ar ?? $this->name_en ?? '');
    }

    public function getValues(): array {
        return json_decode($this->values_json ?? '[]', true) ?: [];
    }
}
