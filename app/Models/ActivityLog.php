<?php

namespace App\Models;

use App\Core\Model;

class ActivityLog extends Model {
    protected string $table = 'activity_logs';
    protected array $fillable = [
        'user_id', 'user_name', 'action', 'module', 'details', 'ip_address'
    ];

    public function user(): ?User {
        return $this->user_id ? User::find($this->user_id) : null;
    }
}
