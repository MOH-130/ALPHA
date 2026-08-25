<?php

namespace App\Services;

use App\Models\Setting;

class PaymentService {
    public static function getActiveMethods(): array {
        $methods = [];

        if (Setting::get('payment_cod_enabled', '1') === '1') {
            $methods['cod'] = [
                'code' => 'cod',
                'name_ar' => 'الدفع عند الاستلام (COD)',
                'name_en' => 'Cash on Delivery (COD)',
                'description_ar' => 'ادفع نقداً عند استلام طلبك لباب منزلك.',
                'description_en' => 'Pay in cash when your order arrives at your doorstep.',
                'icon' => 'truck'
            ];
        }

        if (Setting::get('payment_bank_enabled', '1') === '1') {
            $methods['bank_transfer'] = [
                'code' => 'bank_transfer',
                'name_ar' => 'تحويل بنكي مباشر',
                'name_en' => 'Direct Bank Transfer',
                'description_ar' => 'قم بالتحويل لحسابنا البنكي (الراجحي / الأهلي).',
                'description_en' => 'Transfer directly to our bank account.',
                'icon' => 'building-library'
            ];
        }

        if (Setting::get('payment_card_enabled', '1') === '1') {
            $methods['credit_card'] = [
                'code' => 'credit_card',
                'name_ar' => 'بطاقة ائتمان / مدى / فيزا / ماستركارد',
                'name_en' => 'Credit Card / Mada / Visa / MasterCard',
                'description_ar' => 'دفع فوري وآمن ومشفر بتقنية 3D Secure.',
                'description_en' => 'Instant, secure 3D encrypted checkout.',
                'icon' => 'credit-card'
            ];
        }

        if (Setting::get('payment_paypal_enabled', '1') === '1') {
            $methods['paypal'] = [
                'code' => 'paypal',
                'name_ar' => 'باي بال (PayPal)',
                'name_en' => 'PayPal Express',
                'description_ar' => 'ادفع بأمان عبر حساب باي بال الخاص بك.',
                'description_en' => 'Pay securely using your PayPal balance or linked cards.',
                'icon' => 'wallet'
            ];
        }

        return $methods;
    }
}
