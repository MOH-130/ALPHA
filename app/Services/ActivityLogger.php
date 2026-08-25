<?php

namespace App\Services;

use App\Core\Auth;
use App\Models\ActivityLog;

class ActivityLogger {
    public static function log(string $action, string $module, string $details = ''): void {
        $user = Auth::getInstance()->user();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'System/Guest',
            'action' => $action,
            'module' => $module,
            'details' => $details,
            'ip_address' => $ip
        ]);
    }
}
