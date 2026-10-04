<?php

namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected string $table = 'users';
    protected array $fillable = [
        'name', 'email', 'password', 'phone', 'role', 'status', 'avatar',
        'is_approved', 'affiliate_code', 'email_verified_at', 'address', 'city', 'country',
        'google_id', 'otp_code', 'otp_expires_at', 'otp_purpose', 'two_factor_enabled'
    ];

    public function orders(): array {
        return Order::where('user_id', $this->id)->orderByDesc('id')->get();
    }

    public function reviews(): array {
        return Review::where('user_id', $this->id)->get();
    }

    public function wishlists(): array {
        return Wishlist::where('user_id', $this->id)->get();
    }

    public function affiliateCommissions(): array {
        return AffiliateCommission::where('affiliate_id', $this->id)->orderByDesc('id')->get();
    }
}
