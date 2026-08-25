<?php

namespace App\Models;

use App\Core\Model;

class ShippingRate extends Model {
    protected string $table = 'shipping_rates';
    protected array $fillable = ['shipping_method_id', 'country', 'city', 'cost', 'min_weight', 'max_weight'];
}
