<?php

namespace App\Services;

use App\Core\Session;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Setting;

class CartService {
    public static function getCart(): array {
        return Session::get('cart', []);
    }

    public static function count(): int {
        $cart = self::getCart();
        $count = 0;
        foreach ($cart as $item) {
            $count += (int)($item['quantity'] ?? 1);
        }
        return $count;
    }

    public static function add(int $productId, int $quantity = 1, array $attributes = []): array {
        $product = Product::find($productId);
        if (!$product || !$product->isInStock()) {
            return ['success' => false, 'message' => __('messages.product_out_of_stock')];
        }

        $cart = self::getCart();
        $itemKey = self::generateItemKey($productId, $attributes);

        $currentQty = isset($cart[$itemKey]) ? $cart[$itemKey]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product->stock_quantity) {
            return ['success' => false, 'message' => __('messages.stock_not_enough', ['available' => $product->stock_quantity])];
        }

        $price = $product->getEffectivePrice();

        $cart[$itemKey] = [
            'product_id' => $product->id,
            'name_ar' => $product->name_ar,
            'name_en' => $product->name_en,
            'slug' => $product->slug,
            'image' => $product->primary_image ?: 'default-product.png',
            'price' => $price,
            'compare_price' => $product->compare_price,
            'sku' => $product->sku,
            'quantity' => $newQty,
            'attributes' => $attributes,
            'weight' => (float)($product->weight ?: 0.5)
        ];

        Session::set('cart', $cart);
        return ['success' => true, 'message' => __('messages.added_to_cart_success'), 'cart_count' => self::count()];
    }

    public static function update(string $itemKey, int $quantity): array {
        $cart = self::getCart();
        if (!isset($cart[$itemKey])) {
            return ['success' => false, 'message' => __('messages.item_not_found_in_cart')];
        }

        $product = Product::find($cart[$itemKey]['product_id']);
        if (!$product) {
            unset($cart[$itemKey]);
            Session::set('cart', $cart);
            return ['success' => false, 'message' => __('messages.product_no_longer_available')];
        }

        if ($quantity <= 0) {
            unset($cart[$itemKey]);
        } else {
            if ($quantity > $product->stock_quantity) {
                return ['success' => false, 'message' => __('messages.stock_not_enough', ['available' => $product->stock_quantity])];
            }
            $cart[$itemKey]['quantity'] = $quantity;
        }

        Session::set('cart', $cart);
        return ['success' => true, 'message' => __('messages.cart_updated_success'), 'cart_count' => self::count()];
    }

    public static function remove(string $itemKey): array {
        $cart = self::getCart();
        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            Session::set('cart', $cart);
        }
        return ['success' => true, 'message' => __('messages.item_removed_from_cart'), 'cart_count' => self::count()];
    }

    public static function clear(): void {
        Session::remove('cart');
        Session::remove('applied_coupon');
    }

    public static function getSubtotal(): float {
        $subtotal = 0.0;
        foreach (self::getCart() as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }
        return (float) $subtotal;
    }

    public static function applyCoupon(string $code): array {
        $code = trim(strtoupper($code));
        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            return ['success' => false, 'message' => __('messages.invalid_coupon')];
        }

        $subtotal = self::getSubtotal();
        if (!$coupon->isValid($subtotal)) {
            return ['success' => false, 'message' => __('messages.coupon_not_applicable')];
        }

        Session::set('applied_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);

        return ['success' => true, 'message' => __('messages.coupon_applied_success')];
    }

    public static function removeCoupon(): void {
        Session::remove('applied_coupon');
    }

    public static function getAppliedCoupon(): ?array {
        return Session::get('applied_coupon');
    }

    public static function getDiscountAmount(): float {
        $applied = self::getAppliedCoupon();
        if (!$applied) return 0.0;
        $coupon = Coupon::find($applied['id']);
        if (!$coupon) return 0.0;
        return $coupon->calculateDiscount(self::getSubtotal());
    }

    public static function getTaxAmount(): float {
        $subtotal = self::getSubtotal() - self::getDiscountAmount();
        $taxPercentage = (float) Setting::get('tax_percentage', 15.0);
        return round(($subtotal * $taxPercentage) / 100.0, 2);
    }

    public static function getTotal(float $shippingCost = 0.0): float {
        $subtotal = self::getSubtotal();
        $discount = self::getDiscountAmount();
        $tax = self::getTaxAmount();
        return max(0.0, ($subtotal - $discount + $tax + $shippingCost));
    }

    private static function generateItemKey(int $productId, array $attributes): string {
        ksort($attributes);
        return md5($productId . '_' . json_encode($attributes));
    }
}
