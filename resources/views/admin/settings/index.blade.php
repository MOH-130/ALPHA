@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;">⚙️ <?= __('admin.settings') ?></h1>

<form action="<?= url('admin/settings') ?>" method="POST">
    <?= csrf_field() ?>
    <div style="display:flex; flex-direction:column; gap:1.5rem; max-width:850px;">
        
        <!-- 1. General Info -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">🏢 معلومات المتجر الأساسية</h3>
            </div>
            <div class="admin-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">اسم المتجر</label>
                        <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars(app_setting('site_name', 'NovaStore')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني الرسمي</label>
                        <input type="email" name="site_email" class="form-control" value="<?= htmlspecialchars(app_setting('site_email', 'support@novastore.com')) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">رقم الهاتف / خدمة العملاء</label>
                        <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars(app_setting('contact_phone', '+966 50 123 4567')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رقم الواتساب (WhatsApp)</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="<?= htmlspecialchars(app_setting('whatsapp_number', '+966501234567')) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">العنوان الفعلي للمتجر والمستودعات</label>
                    <input type="text" name="store_address" class="form-control" value="<?= htmlspecialchars(app_setting('store_address', 'الرياض، المملكة العربية السعودية')) ?>">
                </div>
            </div>
        </div>

        <!-- 2. Policies & Visibility -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">🔒 سياسات التسجيل وإظهار الأسعار</h3>
            </div>
            <div class="admin-card-body">
                <div class="form-group">
                    <label class="form-label">إظهار الأسعار في المتجر:</label>
                    <select name="price_visibility" class="form-control">
                        <option value="all" <?= app_setting('price_visibility') === 'all' ? 'selected' : '' ?>>متاحة للجميع (الزوار والمسجلين)</option>
                        <option value="registered_only" <?= app_setting('price_visibility') === 'registered_only' ? 'selected' : '' ?>>للمستخدمين المسجلين فقط (B2B Mode)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">نظام قبول تسجيل العملاء الجدد:</label>
                    <select name="registration_requires_approval" class="form-control">
                        <option value="0" <?= app_setting('registration_requires_approval') === '0' ? 'selected' : '' ?>>مباشر وتلقائي (Direct Registration)</option>
                        <option value="1" <?= app_setting('registration_requires_approval') === '1' ? 'selected' : '' ?>>يتطلب موافقة الإدارة أولاً قبل التفعيل</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Taxes & Shipping Defaults -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">💰 الضرائب والشحن الافتراضي</h3>
            </div>
            <div class="admin-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">نسبة ضريبة القيمة المضافة (%)</label>
                        <input type="number" step="0.1" name="tax_percentage" class="form-control" value="<?= app_setting('tax_percentage', '15.0') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">الحد الأدنى للشحن المجاني (د.ج - DZD)</label>
                        <input type="number" step="0.1" name="free_shipping_threshold" class="form-control" value="<?= app_setting('free_shipping_threshold', '5000.0') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Social Links -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">🌐 روابط التواصل الاجتماعي</h3>
            </div>
            <div class="admin-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">رابط فيسبوك</label>
                        <input type="url" name="social_facebook" class="form-control" value="<?= htmlspecialchars(app_setting('social_facebook', '')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رابط منصة X (تويتر)</label>
                        <input type="url" name="social_twitter" class="form-control" value="<?= htmlspecialchars(app_setting('social_twitter', '')) ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">رابط انستغرام</label>
                        <input type="url" name="social_instagram" class="form-control" value="<?= htmlspecialchars(app_setting('social_instagram', '')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رابط تيليجرام</label>
                        <input type="url" name="social_telegram" class="form-control" value="<?= htmlspecialchars(app_setting('social_telegram', '')) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Production SMTP Mailer Status & Test Tool -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">📧 خادم البريد الحقيقي (Production SMTP) واختبار الإرسال</h3>
            </div>
            <div class="admin-card-body">
                <div style="background: rgba(0, 210, 255, 0.05); border: 1px solid rgba(0, 210, 255, 0.2); border-radius: 8px; padding: 12px 16px; margin-bottom: 1.25rem; font-size: 0.85rem; color: #cbd5e1;">
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <div><strong>خادم SMTP:</strong> <code style="color: #00d2ff;"><?= htmlspecialchars(config('mail.mailers.smtp.host', env('MAIL_HOST', 'smtp.gmail.com'))) ?></code></div>
                        <div><strong>المنفذ:</strong> <code style="color: #00d2ff;"><?= (int)config('mail.mailers.smtp.port', env('MAIL_PORT', 587)) ?></code></div>
                        <div><strong>التشفير:</strong> <code style="color: #00d2ff;"><?= htmlspecialchars(config('mail.mailers.smtp.encryption', env('MAIL_ENCRYPTION', 'tls'))) ?></code></div>
                        <div><strong>بريد الإرسال:</strong> <code style="color: #00ff88;"><?= htmlspecialchars(config('mail.from.address', env('MAIL_FROM_ADDRESS', ''))) ?></code></div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <input type="email" id="test-smtp-email" class="form-control" placeholder="أدخل بريدك لاختبار وصول رسائل OTP الحقيقية..." style="max-width: 380px;">
                    <button type="button" id="btn-test-smtp" onclick="executeSmtpTest()" class="btn btn-secondary btn-sm" style="height: 38px; font-weight: 700;">
                        🚀 إرسال رسالة اختبار الآن
                    </button>
                    <span id="smtp-test-status" style="font-size: 0.85rem; font-weight: 700;"></span>
                </div>
            </div>
        </div>

        <script>
        function executeSmtpTest() {
            const emailInput = document.getElementById('test-smtp-email');
            const statusEl = document.getElementById('smtp-test-status');
            const btn = document.getElementById('btn-test-smtp');
            const email = emailInput.value.trim();

            if (!email) {
                alert('يرجى كتابة بريد إلكتروني صالح لاختبار الإرسال.');
                emailInput.focus();
                return;
            }

            btn.disabled = true;
            btn.innerText = 'جاري الإرسال...';
            statusEl.innerText = '⏳ جاري الاتصال بخادم البريد...';
            statusEl.style.color = '#ffaa00';

            fetch('<?= url('admin/settings/test-smtp') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: '_token=<?= csrf_token() ?>&email=' + encodeURIComponent(email)
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = '🚀 إرسال رسالة اختبار الآن';
                if (data.success) {
                    statusEl.innerText = '✅ ' + data.message;
                    statusEl.style.color = '#00ff88';
                } else {
                    statusEl.innerText = '❌ ' + data.message;
                    statusEl.style.color = '#ef4444';
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerText = '🚀 إرسال رسالة اختبار الآن';
                statusEl.innerText = '❌ خطأ في الاتصال بالخادم.';
                statusEl.style.color = '#ef4444';
            });
        }
        </script>

        <button type="submit" class="btn btn-primary btn-lg">
            💾 <?= __('admin.settings_saved_success') ?>
        </button>
    </div>
</form>
@endsection
