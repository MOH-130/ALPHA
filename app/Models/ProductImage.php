<?php

namespace App\Models;

use App\Core\Model;

class ProductImage extends Model {
    protected string $table = 'product_images';
    protected array $fillable = ['product_id', 'image_path', 'alt_text', 'sort_order', 'is_primary'];
}
