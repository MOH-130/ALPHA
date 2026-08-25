<?php

namespace App\Models;

use App\Core\Model;

class AffiliateCommission extends Model {
    protected string $table = 'affiliate_commissions';
    protected array $fillable = [
        'affiliate_id', 'order_id', 'order_total', 'commission_rate',
        'commission_amount', 'status', 'payout_date', 'notes'
    ];

    public function affiliate(): ?User {
        return $this->affiliate_id ? User::find($this->affiliate_id) : null;
    }

    public function order(): ?Order {
        return $this->order_id ? Order::find($this->order_id) : null;
    }
}
