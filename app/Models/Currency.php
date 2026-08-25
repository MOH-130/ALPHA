<?php

namespace App\Models;

use App\Core\Model;

class Currency extends Model {
    protected string $table = 'currencies';
    protected array $fillable = [
        'code', 'name_ar', 'name_en', 'symbol_ar', 'symbol_en',
        'exchange_rate', 'is_default', 'status'
    ];

    public function getNameAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->name_en) ? $this->name_en : ($this->name_ar ?? $this->name_en ?? '');
    }

    public function getSymbolAttribute(): string {
        $locale = current_locale();
        return $locale === 'en' && !empty($this->symbol_en) ? $this->symbol_en : ($this->symbol_ar ?? $this->symbol_en ?? $this->code);
    }
}
