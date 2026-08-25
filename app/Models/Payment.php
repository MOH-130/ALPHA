<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model {
    protected string $table = 'payments';
    protected array $fillable = [
        'order_id', 'gateway', 'transaction_id', 'amount', 'currency',
        'status', 'receipt_image', 'payload_json'
    ];

    public function order(): ?Order {
        return $this->order_id ? Order::find($this->order_id) : null;
    }
}
