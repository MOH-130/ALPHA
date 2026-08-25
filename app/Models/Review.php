<?php

namespace App\Models;

use App\Core\Model;

class Review extends Model {
    protected string $table = 'reviews';
    protected array $fillable = [
        'product_id', 'user_id', 'author_name', 'author_email',
        'rating', 'comment', 'is_verified_purchase', 'is_approved'
    ];

    public function product(): ?Product {
        return $this->product_id ? Product::find($this->product_id) : null;
    }

    public function user(): ?User {
        return $this->user_id ? User::find($this->user_id) : null;
    }
}
