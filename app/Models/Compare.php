<?php

namespace App\Models;

use App\Core\Model;

class Compare extends Model {
    protected string $table = 'compares';
    protected array $fillable = ['user_id', 'session_id', 'product_id'];

    public function product(): ?Product {
        return $this->product_id ? Product::find($this->product_id) : null;
    }
}
