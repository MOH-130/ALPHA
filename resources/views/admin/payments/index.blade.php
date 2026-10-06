@extends('layouts.admin')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem; font-weight:800; margin:0; color:#fff;">💳 بوابات وطرق الدفع (Payment Gateways)</h1>
        <p style="color:var(--admin-text-muted); font-size:0.875rem; margin-top:4px;">
            تحكم كامل في الحسابات البنكية، بريدي موب، CCP، والعملات الرقمية الظاهرة للعملاء في صفحة الشراء (Checkout).
        </p>
    </div>
</div>

<form action="<?= url('admin/payments') ?>" method="POST">
    <?= csrf_field() ?>
    <div style="display:flex; flex-direction:column; gap:1.5rem; max-width:900px;">

        <!-- 1. BaridiMob -->
        <div class="admin-card" style="border:1px solid rgba(0, 210, 255, 0.3);">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center; background:rgba(0, 210, 255, 0.05);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:1.5rem;">💳</span>
                    <div>
                        <h3 class="admin-card-title" style="margin:0; color:#fff;">بريدي موب (BaridiMob)</h3>
                        <small style="color:var(--admin-text-muted);">طريقة الدفع الأساسية والأكثر استخداماً بالجزائر</small>
                    </div>
                </div>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="payment_baridimob_enabled" value="1" <?= app_setting('payment_baridimob_enabled', '1') === '1' ? 'checked' : '' ?>>
                    <strong>تفعيل الطريقة</strong>
                </label>
            </div>
            <div class="admin-card-body" style="display:flex; flex-direction:column; gap:1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight:700; color:#fff;">
                        رقم الحساب الكامل (RIP الكامل):
                        <span style="color:#00d2ff; font-size:0.85rem;">(يظهر للعميل في صفحة الدفع)</span>
                    </label>
                    <input type="text" name="payment_baridimob_rip" class="form-control" value="<?= htmlspecialchars(app_setting('payment_baridimob_rip', '007999990026652222646')) ?>" required style="font-family:monospace; font-size:1.15rem; font-weight:800; letter-spacing:1px; color:#00d2ff; background:rgba(0,0,0,0.3);">
                    <small style="color:var(--admin-text-muted);">الرقم الكامل الرسمي المكون من 20 رقم أو أكثر، مثل: <code>007999990026652222646</code></small>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">رقم الحساب (N° de compte):</label>
                        <input type="text" name="payment_baridimob_account" class="form-control" value="<?= htmlspecialchars(app_setting('payment_baridimob_account', '0026652226')) ?>" style="font-family:monospace; font-size:1.05rem; font-weight:700;">
                        <small style="color:var(--admin-text-muted);">مثال: <code>0026652226</code></small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">المفتاح (Clé):</label>
                        <input type="text" name="payment_baridimob_key" class="form-control" value="<?= htmlspecialchars(app_setting('payment_baridimob_key', '46')) ?>" style="font-family:monospace; font-size:1.05rem; font-weight:700;">
                        <small style="color:var(--admin-text-muted);">مثال: <code>46</code></small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">اسم صاحب الحساب (Nom du titulaire):</label>
                    <input type="text" name="payment_baridimob_holder" class="form-control" value="<?= htmlspecialchars(app_setting('payment_baridimob_holder', 'Alpha Gaming Shop Algérie')) ?>">
                </div>
            </div>
        </div>

        <!-- 2. CCP (الحساب البريدي الجاري) -->
        <div class="admin-card">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:1.5rem;">📮</span>
                    <div>
                        <h3 class="admin-card-title" style="margin:0; color:#fff;">الحساب البريدي الجاري (CCP)</h3>
                        <small style="color:var(--admin-text-muted);">الدفع عبر مكاتب بريد الجزائر</small>
                    </div>
                </div>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="payment_ccp_enabled" value="1" <?= app_setting('payment_ccp_enabled', '1') === '1' ? 'checked' : '' ?>>
                    <strong>تفعيل الطريقة</strong>
                </label>
            </div>
            <div class="admin-card-body" style="display:flex; flex-direction:column; gap:1rem;">
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">رقم الحساب CCP:</label>
                        <input type="text" name="payment_ccp_account" class="form-control" value="<?= htmlspecialchars(app_setting('payment_ccp_account', '12345678')) ?>" style="font-family:monospace; font-size:1.05rem;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">المفتاح (Clé):</label>
                        <input type="text" name="payment_ccp_key" class="form-control" value="<?= htmlspecialchars(app_setting('payment_ccp_key', '90')) ?>" style="font-family:monospace; font-size:1.05rem;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">اسم صاحب الحساب:</label>
                    <input type="text" name="payment_ccp_holder" class="form-control" value="<?= htmlspecialchars(app_setting('payment_ccp_holder', 'Alpha Gaming Shop Algérie')) ?>">
                </div>
            </div>
        </div>

        <!-- 3. Official Contact & Support Notice (بديل رقم الهاتف) -->
        <div class="admin-card" style="border:1px solid rgba(0, 255, 136, 0.3);">
            <div class="admin-card-header" style="background:rgba(0, 255, 136, 0.05);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:1.5rem;">🌐</span>
                    <div>
                        <h3 class="admin-card-title" style="margin:0; color:#fff;">معلومات التواصل والدعم أثناء الدفع (بديل رقم الهاتف)</h3>
                        <small style="color:var(--admin-text-muted);">تظهر للزبون عند إتمام الدفع أو إرسال وصل التحويل بدلاً من أرقام الهواتف الشخصية</small>
                    </div>
                </div>
            </div>
            <div class="admin-card-body" style="display:flex; flex-direction:column; gap:1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight:700; color:#fff;">عبارة التواصل المعروضة للزبون:</label>
                    <input type="text" name="payment_contact_notice" class="form-control" value="<?= htmlspecialchars(app_setting('payment_contact_notice', 'للتواصل والاستفسارات، تواصلوا معنا عبر صفحتنا الرسمية.')) ?>" required>
                    <small style="color:var(--admin-text-muted);">هذه العبارة تحل محل رقم الهاتف في جميع خطوات إرسال الوصل أو الدفع.</small>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight:700; color:#fff;">رابط الصفحة الرسمية (Official Social Link):</label>
                    <input type="url" name="payment_official_page_url" class="form-control" value="<?= htmlspecialchars(app_setting('payment_official_page_url', 'https://www.facebook.com/AlphaGamingShop1/')) ?>" required dir="ltr">
                    <small style="color:var(--admin-text-muted);">رابط صفحة الفيسبوك أو الإنستغرام الرسمية للمتجر (مثل <code>https://www.facebook.com/AlphaGamingShop1/</code>)</small>
                </div>
            </div>
        </div>

        <!-- 4. Binance Pay / Crypto -->
        <div class="admin-card">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:1.5rem;">🪙</span>
                    <div>
                        <h3 class="admin-card-title" style="margin:0; color:#fff;">العملات الرقمية (Binance Pay / USDT P2P)</h3>
                        <small style="color:var(--admin-text-muted);">الدفع الفوري بالدولار الرقمي USDT</small>
                    </div>
                </div>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="payment_binance_enabled" value="1" <?= app_setting('payment_binance_enabled', '1') === '1' ? 'checked' : '' ?>>
                    <strong>تفعيل الطريقة</strong>
                </label>
            </div>
            <div class="admin-card-body" style="display:flex; flex-direction:column; gap:1rem;">
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Binance Pay ID:</label>
                        <input type="text" name="payment_binance_pay_id" class="form-control" value="<?= htmlspecialchars(app_setting('payment_binance_pay_id', '987654321')) ?>" style="font-family:monospace; color:#f59e0b; font-weight:700;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">الشبكة المدعومة (Network):</label>
                        <input type="text" name="payment_binance_network" class="form-control" value="<?= htmlspecialchars(app_setting('payment_binance_network', 'USDT (TRC20 / BEP20)')) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. PayPal -->
        <div class="admin-card">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:1.5rem;">🅿️</span>
                    <div>
                        <h3 class="admin-card-title" style="margin:0; color:#fff;">باي بال (PayPal Express)</h3>
                        <small style="color:var(--admin-text-muted);">دفع دولي بالعملات الأجنبية للعملاء خارج الجزائر</small>
                    </div>
                </div>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="payment_paypal_enabled" value="1" <?= app_setting('payment_paypal_enabled', '1') === '1' ? 'checked' : '' ?>>
                    <strong>تفعيل الطريقة</strong>
                </label>
            </div>
            <div class="admin-card-body">
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني لحساب PayPal:</label>
                    <input type="email" name="payment_paypal_email" class="form-control" value="<?= htmlspecialchars(app_setting('payment_paypal_email', '')) ?>" placeholder="payments@alphagamingshop.dz" dir="ltr">
                </div>
            </div>
        </div>

        <!-- 6. COD & Bank Transfer -->
        <div class="admin-card">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="admin-card-title" style="margin:0; color:#fff;">💵 الدفع عند الاستلام & التحويل البنكي المباشر</h3>
            </div>
            <div class="admin-card-body" style="display:flex; flex-direction:column; gap:1rem;">
                <div style="display:flex; gap:2rem; flex-wrap:wrap;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="payment_cod_enabled" value="1" <?= app_setting('payment_cod_enabled') === '1' ? 'checked' : '' ?>>
                        <span>تفعيل الدفع عند الاستلام (COD)</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="payment_bank_enabled" value="1" <?= app_setting('payment_bank_enabled') === '1' ? 'checked' : '' ?>>
                        <span>تفعيل التحويل البنكي المباشر (Bank Transfer)</span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="form-label">تفاصيل إضافية للحسابات البنكية (IBAN):</label>
                    <textarea name="bank_account_details" rows="3" class="form-control"><?= htmlspecialchars(app_setting('bank_account_details', '')) ?></textarea>
                </div>
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary btn-lg" style="padding:0.9rem 2.5rem; font-size:1.05rem; font-weight:800;">
                💾 حفظ كافة إعدادات وبوابات الدفع
            </button>
        </div>
    </div>
</form>
@endsection
