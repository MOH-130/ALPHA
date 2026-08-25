<?php

namespace App\Models;

use App\Core\Model;

class Visitor extends Model {
    protected string $table = 'visitors';
    protected array $fillable = [
        'ip_address', 'current_url', 'referrer', 'user_agent',
        'device', 'browser', 'last_activity'
    ];
}
