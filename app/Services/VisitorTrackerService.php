<?php

namespace App\Services;

use App\Core\Request;
use App\Models\Visitor;

class VisitorTrackerService {
    public static function recordVisit(Request $request): void {
        $ip = $request->ip();
        // Anonymize last octet for privacy
        $ipParts = explode('.', $ip);
        if (count($ipParts) === 4) {
            $ipParts[3] = 'xxx';
            $anonymizedIp = implode('.', $ipParts);
        } else {
            $anonymizedIp = substr($ip, 0, 8) . '::xxx';
        }

        $ua = $request->userAgent();
        $device = self::detectDevice($ua);
        $browser = self::detectBrowser($ua);
        $url = $request->path();
        $ref = $request->referrer();

        // Update or insert visitor
        $now = date('Y-m-d H:i:s');
        $visitor = Visitor::where('ip_address', $anonymizedIp)->first();
        if ($visitor) {
            $visitor->update([
                'current_url' => $url,
                'referrer' => $ref,
                'user_agent' => substr($ua, 0, 255),
                'device' => $device,
                'browser' => $browser,
                'last_activity' => $now
            ]);
        } else {
            Visitor::create([
                'ip_address' => $anonymizedIp,
                'current_url' => $url,
                'referrer' => $ref,
                'user_agent' => substr($ua, 0, 255),
                'device' => $device,
                'browser' => $browser,
                'last_activity' => $now
            ]);
        }
    }

    public static function getActiveOnlineCount(int $minutes = 15): int {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        return Visitor::where('last_activity', '>=', $cutoff)->count();
    }

    private static function detectDevice(string $ua): string {
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $ua)) {
            return 'Tablet';
        }
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile|iphone)/i', $ua)) {
            return 'Mobile';
        }
        return 'Desktop';
    }

    private static function detectBrowser(string $ua): string {
        if (strpos($ua, 'Edge') !== false || strpos($ua, 'Edg') !== false) return 'Edge';
        if (strpos($ua, 'Chrome') !== false) return 'Chrome';
        if (strpos($ua, 'Safari') !== false) return 'Safari';
        if (strpos($ua, 'Firefox') !== false) return 'Firefox';
        if (strpos($ua, 'MSIE') !== false || strpos($ua, 'Trident') !== false) return 'IE';
        return 'Other';
    }
}
