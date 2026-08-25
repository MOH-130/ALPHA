<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model {
    protected string $table = 'categories';
    protected array $fillable = [
        'parent_id', 'name_ar', 'name_en', 'slug', 'description_ar', 'description_en',
        'image', 'icon', 'sort_order', 'is_featured', 'status'
    ];

    public function getNameAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->name_en) ? $this->name_en : ($this->name_ar ?? $this->name_en ?? '');
    }

    public function getDescriptionAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->description_en) ? $this->description_en : ($this->description_ar ?? '');
    }

    public function parent(): ?Category {
        return $this->parent_id ? Category::find($this->parent_id) : null;
    }

    public function children(): array {
        return Category::where('parent_id', $this->id)->where('status', 'active')->orderBy('sort_order', 'ASC')->get();
    }

    public function productsCount(): int {
        return Product::where('category_id', $this->id)->where('status', 'active')->count();
    }
}
