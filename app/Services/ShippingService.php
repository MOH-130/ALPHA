<?php

namespace App\Services;

use App\Models\ShippingMethod;
use App\Models\Setting;

class ShippingService {
    public static function getAvailableMethods(float $cartSubtotal = 0.0, string $country = 'Algeria'): array {
        $methods = ShippingMethod::where('status', 'active')->orderBy('sort_order', 'ASC')->get();
        if (empty($methods)) {
            // Fallback default shipping
            $freeThreshold = (float) Setting::get('free_shipping_threshold', 250.0);
            $standardCost = (float) Setting::get('standard_shipping_cost', 25.0);
            $cost = ($cartSubtotal >= $freeThreshold) ? 0.0 : $standardCost;

            return [
                [
                    'id' => 1,
                    'code' => 'standard',
                    'name_ar' => 'الشحن السريع للمنزل',
                    'name_en' => 'Express Home Delivery',
                    'description_ar' => 'التوصيل خلال 2-4 أيام عمل.',
                    'description_en' => 'Delivery within 2-4 business days.',
                    'cost' => $cost,
                    'is_free' => ($cost === 0.0)
                ]
            ];
        }

        $result = [];
        foreach ($methods as $m) {
            $cost = (float)$m->cost;
            if ($m->free_threshold && $cartSubtotal >= (float)$m->free_threshold) {
                $cost = 0.0;
            }
            $result[] = [
                'id' => $m->id,
                'code' => $m->code,
                'name_ar' => $m->name_ar,
                'name_en' => $m->name_en,
                'description_ar' => $m->description_ar,
                'description_en' => $m->description_en,
                'cost' => $cost,
                'is_free' => ($cost === 0.0)
            ];
        }
        return $result;
    }
}
