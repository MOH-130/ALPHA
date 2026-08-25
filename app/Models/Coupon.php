<?php

namespace App\Models;

use App\Core\Model;

class Coupon extends Model {
    protected string $table = 'coupons';
    protected array $fillable = [
        'code', 'type', 'value', 'min_order_amount', 'max_discount_amount',
        'usage_limit', 'used_count', 'start_date', 'end_date', 'status'
    ];

    public function isValid(float $cartTotal): bool {
        if ($this->status !== 'active') return false;
        $now = date('Y-m-d H:i:s');
        if ($this->start_date && $this->start_date > $now) return false;
        if ($this->end_date && $this->end_date < $now) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($this->min_order_amount && $cartTotal < $this->min_order_amount) return false;
        return true;
    }

    public function calculateDiscount(float $cartTotal): float {
        if (!$this->isValid($cartTotal)) return 0.0;
        
        $discount = 0.0;
        if ($this->type === 'percentage') {
            $discount = ($cartTotal * $this->value) / 100.0;
        } else {
            $discount = (float) $this->value;
        }

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = (float) $this->max_discount_amount;
        }

        return min($discount, $cartTotal);
    }
}
