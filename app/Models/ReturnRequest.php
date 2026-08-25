<?php

namespace App\Models;

use App\Core\Model;

class ReturnRequest extends Model {
    protected string $table = 'return_requests';
    protected array $fillable = [
        'order_id', 'user_id', 'product_id', 'reason', 'details',
        'images_json', 'status', 'admin_notes', 'refund_amount'
    ];

    public function order(): ?Order {
        return $this->order_id ? Order::find($this->order_id) : null;
    }

    public function user(): ?User {
        return $this->user_id ? User::find($this->user_id) : null;
    }

    public function product(): ?Product {
        return $this->product_id ? Product::find($this->product_id) : null;
    }

    public function getImages(): array {
        return json_decode($this->images_json ?? '[]', true) ?: [];
    }

    public function getStatusBadgeClass(): string {
        return match($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'approved' => 'bg-blue-100 text-blue-800 border-blue-300',
            'refunded' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
