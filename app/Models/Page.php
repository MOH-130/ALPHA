<?php

namespace App\Models;

use App\Core\Model;

class Page extends Model {
    protected string $table = 'pages';
    protected array $fillable = [
        'slug', 'title_ar', 'title_en', 'content_ar', 'content_en',
        'meta_title', 'meta_description', 'status'
    ];

    public function getTitleAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->title_en) ? $this->title_en : ($this->title_ar ?? '');
    }

    public function getContentAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->content_en) ? $this->content_en : ($this->content_ar ?? '');
    }
}
