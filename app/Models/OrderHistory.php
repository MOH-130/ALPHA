<?php

namespace App\Models;

use App\Core\Model;

class OrderHistory extends Model {
    protected string $table = 'order_histories';
    protected array $fillable = ['order_id', 'status', 'comment', 'notified_customer', 'created_by'];
}
