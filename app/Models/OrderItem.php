<?php

namespace App\Models;

use App\Core\Model;

class OrderItem extends Model {
    protected string $table = 'order_items';
    protected array $fillable = [
        'order_id', 'product_id', 'product_name', 'product_image', 'sku',
        'unit_price', 'quantity', 'subtotal', 'attributes_json'
    ];

    public function product(): ?Product {
        return $this->product_id ? Product::find($this->product_id) : null;
    }

    public function getAttributesList(): array {
        return json_decode($this->attributes_json ?? '[]', true) ?: [];
    }
}
