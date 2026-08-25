@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;">💳 <?= __('admin.payments') ?></h1>

<form action="<?= url('admin/payments') ?>" method="POST">
    <?= csrf_field() ?>
    <div style="display:flex; flex-direction:column; gap:1.5rem; max-width:800px;">
        
        <!-- 1. COD -->
        <div class="admin-card">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="admin-card-title">💵 الدفع عند الاستلام (Cash on Delivery)</h3>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="payment_cod_enabled" value="1" <?= app_setting('payment_cod_enabled') === '1' ? 'checked' : '' ?>>
                    <strong>تفعيل الطريقة</strong>
                </label>
            </div>
            <div class="admin-card-body">
                <p style="color:var(--admin-text-muted); font-size:0.875rem; margin:0;">
                    السماح للعملاء بالدفع نقداً لمندوب التوصيل عند استلام الطلب.
                </p>
            </div>
        </div>

        <!-- 2. Bank Transfer -->
        <div class="admin-card">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="admin-card-title">🏦 التحويل البنكي المباشر (Bank Transfer)</h3>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="payment_bank_enabled" value="1" <?= app_setting('payment_bank_enabled') === '1' ? 'checked' : '' ?>>
                    <strong>تفعيل الطريقة</strong>
                </label>
            </div>
            <div class="admin-card-body">
                <div class="form-group">
                    <label class="form-label">تفاصيل الحسابات البنكية ورقم الآيبان (IBAN):</label>
                    <textarea name="bank_account_details" rows="3" class="form-control"><?= htmlspecialchars(app_setting('bank_account_details', '')) ?></textarea>
                </div>
            </div>
        </div>

        <!-- 3. Credit Card / Mada / Visa -->
        <div class="admin-card">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="admin-card-title">💳 البطاقات الائتمانية ومدى (Credit Card / Mada)</h3>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="payment_card_enabled" value="1" <?= app_setting('payment_card_enabled') === '1' ? 'checked' : '' ?>>
                    <strong>تفعيل الطريقة</strong>
                </label>
            </div>
            <div class="admin-card-body">
                <p style="color:var(--admin-text-muted); font-size:0.875rem; margin:0;">
                    دفع إلكتروني مباشر ومشفر عبر البطاقات البنكية المحلية والدولية (Stripe / HyperPay / Checkout.com Ready).
                </p>
            </div>
        </div>

        <!-- 4. PayPal -->
        <div class="admin-card">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="admin-card-title">🅿️ باي بال (PayPal Express)</h3>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="payment_paypal_enabled" value="1" <?= app_setting('payment_paypal_enabled') === '1' ? 'checked' : '' ?>>
                    <strong>تفعيل الطريقة</strong>
                </label>
            </div>
            <div class="admin-card-body">
                <p style="color:var(--admin-text-muted); font-size:0.875rem; margin:0;">
                    بوابة الدفع العالمية السريعة عبر رصيد وبطاقات PayPal.
                </p>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
            💾 حفظ إعدادات الدفع
        </button>
    </div>
</form>
@endsection
