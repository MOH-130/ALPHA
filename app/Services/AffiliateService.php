<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Setting;
use App\Models\AffiliateCommission;

class AffiliateService {
    public static function isEnabled(): bool {
        return Setting::get('affiliate_enabled', '1') === '1';
    }

    public static function getCommissionRate(): float {
        return (float) Setting::get('affiliate_commission_rate', 10.0);
    }

    public static function trackReferral(string $code): void {
        if (!self::isEnabled()) return;
        $user = User::where('affiliate_code', $code)->first();
        if ($user) {
            $days = (int) config('ecommerce.affiliate.cookie_lifetime_days', 30);
            setcookie('affiliate_ref', (string)$user->id, time() + ($days * 86400), '/');
            $_SESSION['affiliate_ref'] = $user->id;
        }
    }

    public static function getAffiliateIdFromCookie(): ?int {
        if (!self::isEnabled()) return null;
        if (isset($_SESSION['affiliate_ref'])) {
            return (int) $_SESSION['affiliate_ref'];
        }
        if (isset($_COOKIE['affiliate_ref'])) {
            return (int) $_COOKIE['affiliate_ref'];
        }
        return null;
    }

    public static function recordCommission(Order $order, int $affiliateId, float $amount): void {
        if (!self::isEnabled()) return;
        AffiliateCommission::create([
            'affiliate_id' => $affiliateId,
            'order_id' => $order->id,
            'order_total' => $order->subtotal,
            'commission_rate' => self::getCommissionRate(),
            'commission_amount' => $amount,
            'status' => 'pending',
            'notes' => __('messages.commission_from_order', ['order' => $order->order_number])
        ]);
    }
}
