@extends('layouts.storefront')

@section('content')
<?php
$cart = $cart ?? [];
$subtotal = $subtotal ?? 0;
$discount = $discount ?? 0;
$tax = $tax ?? 0;
$shippingMethods = $shippingMethods ?? [];
$total = $total ?? 0;
$user = $user ?? null;
?>
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <h1 style="font-size:1.85rem; font-weight:800; margin-bottom:2rem; color:#fff;">
        🔐 <?= __('messages.checkout') ?>
    </h1>

    <form action="<?= url('checkout/place-order') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="checkout-layout">
            
            <!-- Left: Checkout Details (Customer, Address, Shipping, Payment) -->
            <div style="display:flex; flex-direction:column; gap:2rem;">
                
                <!-- 1. Customer & Shipping Info -->
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:1.5rem; display:flex; align-items:center; gap:8px; color:#fff;">
                        <span>📍</span> <?= __('messages.billing_shipping_details') ?> (الجزائر 🇩🇿)
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label"><?= __('messages.full_name') ?> *</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user ? $user->name : old('name')) ?>" required placeholder="الاسم واللقب الكامل">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?= __('messages.email_address') ?> *</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user ? $user->email : old('email')) ?>" required placeholder="example@gmail.com (لاستلام بيانات الحساب)">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label"><?= __('messages.phone_number') ?> (واتساب) *</label>
                            <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user ? $user->phone : old('phone')) ?>" placeholder="+213 774 44 44 44" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">الولاية / المدينة *</label>
                            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user ? $user->city : old('city', 'الجزائر العاصمة')) ?>" required placeholder="الجزائر العاصمة، وهران، قسنطينة...">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">العنوان أو ملاحظات الحساب (اختياري)</label>
                        <textarea name="address" rows="2" class="form-control" placeholder="اسم المستخدم في ستيم أو ملاحظات التسليم..."><?= htmlspecialchars($user ? $user->address : old('address')) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= __('messages.order_notes') ?></label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="أي تفاصيل إضافية لتسهيل التواصل عبر واتساب..."></textarea>
                    </div>
                </div>

                <!-- 2. Shipping / Delivery Method -->
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:1.5rem; display:flex; align-items:center; gap:8px; color:#fff;">
                        <span>⚡</span> طريقة التسليم
                    </h3>

                    <div style="display:flex; flex-direction:column; gap:1rem;">
                        <label style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border:2px solid var(--color-primary); border-radius:var(--btn-radius); cursor:pointer; background:rgba(0, 210, 255, 0.12);" class="shipping-radio-label">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <input type="radio" name="shipping_method" value="instant_delivery" checked style="accent-color:var(--color-primary);">
                                <div>
                                    <div style="font-weight:700; font-size:0.95rem; color:#fff;">⚡ تسليم فوري وتلقائي عبر واتساب والإيميل</div>
                                    <div style="font-size:0.8rem; color:var(--text-muted);">استلام كود التفعيل أو بيانات الحساب في دقائق بعد تأكيد الدفع</div>
                                </div>
                            </div>
                            <span style="font-weight:800; font-size:1rem; color:var(--color-secondary);">
                                مجاناً 0.00 د.ج
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 3. Payment Method Selection -->
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:1.5rem; display:flex; align-items:center; gap:8px; color:#fff;">
                        <span>💳</span> <?= __('messages.select_payment_method') ?> (طرق الدفع بالجزائر 🇩🇿)
                    </h3>

                    <div style="display:flex; flex-direction:column; gap:1rem;">
                        <!-- BaridiMob Option -->
                        <label style="display:flex; align-items:flex-start; gap:12px; padding:1.25rem; border:1px solid rgba(0, 210, 255, 0.4); border-radius:var(--btn-radius); cursor:pointer; background:rgba(0, 210, 255, 0.08);">
                            <input type="radio" name="payment_method" value="baridimob" checked style="margin-top:4px; accent-color:var(--color-primary);">
                            <div style="flex:1;">
                                <div style="font-weight:800; font-size:1.05rem; margin-bottom:4px; color:#fff;">💳 الدفع عبر تطبيق بريدي موب (BaridiMob)</div>
                                <div style="font-size:0.85rem; color:#cbd5e1; line-height:1.6;">
                                    التحويل المباشر السريع من هاتفك إلى حساب بريدي موب الخاص بالمتجر.
                                </div>
                                <div style="background:var(--bg-input); border:1px dashed var(--border-color); border-radius:8px; padding:0.85rem; margin-top:0.75rem; font-size:0.85rem; line-height:1.7; color:#fff;">
                                    <strong>RIP بريدي موب:</strong> <code style="color:var(--color-primary); font-size:0.95rem; font-weight:800;">00799999000123456789</code><br>
                                    <strong>الاسم:</strong> Alpha Gaming Shop Algérie<br>
                                    <span style="color:var(--color-secondary);">📲 أرسل صورة الوصل على واتساب: <strong>+213 774 44 44 44</strong></span>
                                </div>
                            </div>
                        </label>

                        <!-- CCP Option -->
                        <label style="display:flex; align-items:flex-start; gap:12px; padding:1.25rem; border:1px solid var(--border-color); border-radius:var(--btn-radius); cursor:pointer; background:var(--bg-input);">
                            <input type="radio" name="payment_method" value="ccp" style="margin-top:4px; accent-color:var(--color-primary);">
                            <div style="flex:1;">
                                <div style="font-weight:800; font-size:1.05rem; margin-bottom:4px; color:#fff;">📮 الدفع عبر الحساب البريدي الجاري (CCP)</div>
                                <div style="font-size:0.85rem; color:var(--text-muted); line-height:1.6;">
                                    عبر مكاتب بريد الجزائر في جميع الولايات.
                                </div>
                                <div style="background:rgba(0,0,0,0.3); border:1px dashed var(--border-color); border-radius:8px; padding:0.85rem; margin-top:0.75rem; font-size:0.85rem; line-height:1.7; color:#fff;">
                                    <strong>رقم الحساب CCP:</strong> <code style="color:var(--color-primary); font-size:0.95rem; font-weight:800;">12345678 المفتاح 90</code><br>
                                    <strong>الاسم:</strong> Alpha Gaming Shop Algérie
                                </div>
                            </div>
                        </label>

                        <!-- Binance Pay / USDT P2P Option -->
                        <label style="display:flex; align-items:flex-start; gap:12px; padding:1.25rem; border:1px solid rgba(245, 158, 11, 0.4); border-radius:var(--btn-radius); cursor:pointer; background:rgba(245, 158, 11, 0.06);">
                            <input type="radio" name="payment_method" value="binance_pay" style="margin-top:4px; accent-color:#f59e0b;">
                            <div style="flex:1;">
                                <div style="font-weight:800; font-size:1.05rem; margin-bottom:4px; color:#fff;">🪙 الدفع بالعملات الرقمية (Binance Pay / USDT P2P)</div>
                                <div style="font-size:0.85rem; color:#cbd5e1; line-height:1.6;">
                                    تحويل فوري بدون عمولة عبر حساب Binance Pay (USDT) بسعر الصرف الموازي المعتمد.
                                </div>
                                <div style="background:rgba(0,0,0,0.3); border:1px dashed rgba(245, 158, 11, 0.4); border-radius:8px; padding:0.85rem; margin-top:0.75rem; font-size:0.85rem; line-height:1.7; color:#fff;">
                                    <strong>Binance Pay ID:</strong> <code style="color:#f59e0b; font-size:0.95rem; font-weight:800;">987654321</code><br>
                                    <strong>الشبكة المدعومة:</strong> USDT (TRC20 / BEP20)
                                </div>
                            </div>
                        </label>

                        <!-- PayPal Option -->
                        <label style="display:flex; align-items:flex-start; gap:12px; padding:1.25rem; border:1px solid var(--border-color); border-radius:var(--btn-radius); cursor:pointer; background:var(--bg-input);">
                            <input type="radio" name="payment_method" value="paypal" style="margin-top:4px; accent-color:var(--color-primary);">
                            <div style="flex:1;">
                                <div style="font-weight:800; font-size:1.05rem; margin-bottom:4px; color:#fff;">🅿️ PayPal (الدفع الدولي بالعملات الأجنبية)</div>
                                <div style="font-size:0.85rem; color:var(--text-muted);">
                                    دفع آمن بالدولار الأمريكي أو اليورو للعملاء من خارج الجزائر.
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary Sidebar -->
            <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem; position:sticky; top:90px; box-shadow: var(--shadow-md);">
                <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:1.25rem; border-bottom:1px solid var(--border-subtle); padding-bottom:0.75rem; color:#fff;">
                    <?= __('messages.order_summary') ?> (<?= count($cart) ?>)
                </h3>

                <!-- Cart Items List -->
                <div style="display:flex; flex-direction:column; gap:12px; max-height:260px; overflow-y:auto; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid var(--border-subtle);">
                    <?php foreach ($cart as $item): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; font-size:0.875rem;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <img src="<?= asset('uploads/products/' . ($item['image'] ?: 'default-product.png')) ?>" style="width:40px; height:40px; object-fit:cover; border-radius:6px; background:#070a12;">
                                <div>
                                    <div style="font-weight:700; color:#fff;"><?= is_rtl() ? $item['name_ar'] : $item['name_en'] ?></div>
                                    <div style="font-size:0.75rem; color:var(--text-muted);"><?= $item['quantity'] ?> × <?= format_price($item['price']) ?></div>
                                </div>
                            </div>
                            <span style="font-weight:700; color:#fff;"><?= format_price($item['price'] * $item['quantity']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totals Breakdown -->
                <div style="display:flex; flex-direction:column; gap:0.75rem; font-size:0.925rem; border-bottom:1px solid var(--border-subtle); padding-bottom:1.25rem; margin-bottom:1.25rem;">
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);"><?= __('messages.subtotal') ?>:</span>
                        <span style="font-weight:700; color:#fff;"><?= format_price($subtotal) ?></span>
                    </div>

                    <?php if ($discount > 0): ?>
                        <div style="display:flex; justify-content:space-between; color:var(--color-secondary);">
                            <span><?= __('messages.discount') ?>:</span>
                            <span style="font-weight:700;">-<?= format_price($discount) ?></span>
                        </div>
                    <?php endif; ?>

                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);"><?= __('messages.shipping_fee') ?>:</span>
                        <span style="font-weight:700; color:var(--color-secondary);">مجاناً ⚡</span>
                    </div>
                </div>

                <!-- Grand Total -->
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:1.75rem;">
                    <span style="font-size:1.15rem; font-weight:800; color:#fff;"><?= __('messages.total') ?>:</span>
                    <span style="font-size:1.75rem; font-weight:900; color:var(--color-primary);">
                        <?= format_price($total) ?>
                    </span>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:0.8rem; color:#cbd5e1; cursor:pointer;">
                        <input type="checkbox" required checked style="accent-color:var(--color-primary);">
                        <span>أوافق على الشروط والضمان والاستلام الفوري</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="font-weight:800; font-size:1.05rem;">
                    🔒 تأكيد الطلب واستلام بيانات الحساب 🚀
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
