<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Compare;
use App\Services\CartService;
use App\Services\CurrencyService;

class ApiController {
    public function search(Request $request) {
        $q = $request->getQuery('q', '');
        if (mb_strlen($q) < 2) {
            return Response::json(['results' => []]);
        }

        $products = Product::where('status', 'active')
            ->whereRaw("(name_ar LIKE ? OR name_en LIKE ? OR sku LIKE ?)", ["%{$q}%", "%{$q}%", "%{$q}%"])
            ->limit(8)
            ->get();

        $results = [];
        foreach ($products as $p) {
            $results[] = [
                'id' => $p->id,
                'title' => is_rtl() ? $p->name_ar : $p->name_en,
                'slug' => $p->slug,
                'url' => url('product/' . $p->slug),
                'price_formatted' => format_price($p->getEffectivePrice()),
                'image' => asset('uploads/products/' . ($p->primary_image ?: 'default-product.png')),
                'in_stock' => $p->isInStock()
            ];
        }

        return Response::json(['results' => $results]);
    }

    public function addToCart(Request $request) {
        $productId = (int)$request->input('product_id');
        $qty = (int)($request->input('quantity', 1) ?: 1);
        $attributes = (array)$request->input('attributes', []);

        $result = CartService::add($productId, $qty, $attributes);
        $result['cart_subtotal_formatted'] = format_price(CartService::getSubtotal());
        return Response::json($result);
    }

    public function updateCart(Request $request) {
        $itemKey = (string)$request->input('item_key');
        $qty = (int)$request->input('quantity');

        $result = CartService::update($itemKey, $qty);
        $result['subtotal_formatted'] = format_price(CartService::getSubtotal());
        $result['discount_formatted'] = format_price(CartService::getDiscountAmount());
        $result['tax_formatted'] = format_price(CartService::getTaxAmount());
        $result['total_formatted'] = format_price(CartService::getTotal());
        return Response::json($result);
    }

    public function removeCart(Request $request) {
        $itemKey = (string)$request->input('item_key');
        $result = CartService::remove($itemKey);
        $result['subtotal_formatted'] = format_price(CartService::getSubtotal());
        $result['total_formatted'] = format_price(CartService::getTotal());
        return Response::json($result);
    }

    public function toggleWishlist(Request $request) {
        $productId = (int)$request->input('product_id');
        $userId = Auth::getInstance()->id();
        $sessionId = session_id();

        $query = Wishlist::where('product_id', $productId);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $existing = $query->first();
        if ($existing) {
            $existing->delete();
            return Response::json(['success' => true, 'added' => false, 'message' => __('messages.removed_from_wishlist')]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'product_id' => $productId
        ]);

        return Response::json(['success' => true, 'added' => true, 'message' => __('messages.added_to_wishlist')]);
    }

    public function toggleCompare(Request $request) {
        $productId = (int)$request->input('product_id');
        $userId = Auth::getInstance()->id();
        $sessionId = session_id();

        $query = Compare::where('product_id', $productId);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $existing = $query->first();
        if ($existing) {
            $existing->delete();
            return Response::json(['success' => true, 'added' => false, 'message' => __('messages.removed_from_compare')]);
        }

        Compare::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'product_id' => $productId
        ]);

        return Response::json(['success' => true, 'added' => true, 'message' => __('messages.added_to_compare')]);
    }

    public function applyCoupon(Request $request) {
        $code = (string)$request->input('coupon_code');
        $result = CartService::applyCoupon($code);
        $result['discount_formatted'] = format_price(CartService::getDiscountAmount());
        $result['total_formatted'] = format_price(CartService::getTotal());
        return Response::json($result);
    }
}
