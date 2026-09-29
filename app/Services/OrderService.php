<?php

namespace App\Services;

use App\Core\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\User;

class OrderService {
    public static function createOrder(array $data): Order {
        $cart = CartService::getCart();
        if (empty($cart)) {
            throw new \Exception(__('messages.cart_is_empty'));
        }

        $subtotal = CartService::getSubtotal();
        $discount = CartService::getDiscountAmount();
        $coupon = CartService::getAppliedCoupon();
        $tax = CartService::getTaxAmount();
        $shippingCost = (float)($data['shipping_cost'] ?? 0.0);
        $total = CartService::getTotal($shippingCost);

        $currency = CurrencyService::getCurrent();
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        // Check for affiliate referral
        $affiliateId = AffiliateService::getAffiliateIdFromCookie();
        $affiliateCommission = 0.0;
        if ($affiliateId) {
            $affiliateUser = User::find($affiliateId);
            if ($affiliateUser && $affiliateUser->id !== Auth::getInstance()->id()) {
                $rate = AffiliateService::getCommissionRate();
                $affiliateCommission = round(($subtotal * $rate) / 100.0, 2);
            } else {
                $affiliateId = null;
            }
        }

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => Auth::getInstance()->id(),
            'customer_name' => $data['name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],
            'shipping_address' => $data['address'],
            'shipping_city' => $data['city'],
            'shipping_country' => $data['country'] ?? 'Algeria',
            'shipping_postal_code' => $data['postal_code'] ?? '',
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'coupon_code' => $coupon['code'] ?? null,
            'tax_amount' => $tax,
            'shipping_fee' => $shippingCost,
            'total_amount' => $total,
            'currency_code' => $currency->code,
            'currency_rate' => $currency->exchange_rate,
            'status' => 'pending',
            'payment_status' => $data['payment_method'] === 'cod' ? 'pending' : 'paid',
            'payment_method' => $data['payment_method'],
            'shipping_method' => $data['shipping_method_name'] ?? 'Standard Shipping',
            'notes' => $data['notes'] ?? '',
            'affiliate_id' => $affiliateId,
            'affiliate_commission' => $affiliateCommission
        ]);

        // Create Order Items and decrease product inventory
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => is_rtl() ? $item['name_ar'] : $item['name_en'],
                'product_image' => $item['image'],
                'sku' => $item['sku'] ?? '',
                'unit_price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
                'attributes_json' => json_encode($item['attributes'])
            ]);

            // Decrement product stock & increment sales count
            $product = Product::find($item['product_id']);
            if ($product) {
                $newStock = max(0, $product->stock_quantity - $item['quantity']);
                $newSales = (int)$product->sales_count + $item['quantity'];
                $product->update([
                    'stock_quantity' => $newStock,
                    'sales_count' => $newSales,
                    'status' => $newStock === 0 ? 'out_of_stock' : $product->status
                ]);
            }
        }

        // Increment coupon used count if used
        if ($coupon) {
            $couponModel = Coupon::find($coupon['id']);
            if ($couponModel) {
                $couponModel->update(['used_count' => (int)$couponModel->used_count + 1]);
            }
        }

        // Record Initial Order History
        OrderHistory::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'comment' => __('messages.order_placed_successfully'),
            'notified_customer' => 1,
            'created_by' => Auth::getInstance()->user() ? Auth::getInstance()->user()->name : 'Customer'
        ]);

        // Record Payment record
        Payment::create([
            'order_id' => $order->id,
            'gateway' => $data['payment_method'],
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
            'amount' => $total,
            'currency' => $currency->code,
            'status' => $data['payment_method'] === 'cod' ? 'pending' : 'completed',
            'payload_json' => json_encode(['method' => $data['payment_method'], 'created_at' => date('Y-m-d H:i:s')])
        ]);

        // Record Affiliate Commission if applicable
        if ($affiliateId && $affiliateCommission > 0) {
            AffiliateService::recordCommission($order, $affiliateId, $affiliateCommission);
        }

        // Clear cart
        CartService::clear();

        return $order;
    }

    public static function updateStatus(Order $order, string $newStatus, string $comment = '', bool $notify = true): void {
        $oldStatus = $order->status;
        $order->update(['status' => $newStatus]);

        if ($newStatus === 'completed' || $newStatus === 'paid') {
            $order->update(['payment_status' => 'paid']);
        }

        OrderHistory::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'comment' => $comment ?: __('messages.order_status_updated_to', ['status' => $newStatus]),
            'notified_customer' => $notify ? 1 : 0,
            'created_by' => Auth::getInstance()->user() ? Auth::getInstance()->user()->name : 'Admin'
        ]);
    }
}
