<?php

namespace App\Models;

use App\Core\Model;

class Deal extends Model {
    protected string $table = 'deals';
    protected array $fillable = [
        'title_ar', 'title_en', 'banner', 'discount_percentage',
        'start_date', 'end_date', 'status'
    ];

    public function getTitleAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->title_en) ? $this->title_en : ($this->title_ar ?? '');
    }
}
