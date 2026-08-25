<?php

namespace App\Models;

use App\Core\Model;

class Order extends Model {
    protected string $table = 'orders';
    protected array $fillable = [
        'order_number', 'user_id', 'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'shipping_city', 'shipping_country', 'shipping_postal_code',
        'subtotal', 'discount_amount', 'coupon_code', 'tax_amount', 'shipping_fee', 'total_amount',
        'currency_code', 'currency_rate', 'status', 'payment_status', 'payment_method',
        'shipping_method', 'tracking_number', 'notes', 'affiliate_id', 'affiliate_commission'
    ];

    public function items(): array {
        return OrderItem::where('order_id', $this->id)->get();
    }

    public function user(): ?User {
        return $this->user_id ? User::find($this->user_id) : null;
    }

    public function history(): array {
        return OrderHistory::where('order_id', $this->id)->orderBy('created_at', 'ASC')->get();
    }

    public function returns(): array {
        return ReturnRequest::where('order_id', $this->id)->get();
    }

    public function getStatusBadgeClass(): string {
        return match($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'processing' => 'bg-blue-100 text-blue-800 border-blue-300',
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'completed' => 'bg-green-100 text-green-800 border-green-300',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-300',
            'refunded' => 'bg-purple-100 text-purple-800 border-purple-300',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPaymentStatusBadgeClass(): string {
        return match($this->payment_status) {
            'paid' => 'bg-green-100 text-green-800 border-green-300',
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'failed' => 'bg-rose-100 text-rose-800 border-rose-300',
            'refunded' => 'bg-purple-100 text-purple-800 border-purple-300',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
