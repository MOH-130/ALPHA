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
                        <label class="form-label">الحد الأدنى للشحن المجاني (SAR)</label>
                        <input type="number" step="0.1" name="free_shipping_threshold" class="form-control" value="<?= app_setting('free_shipping_threshold', '300.0') ?>">
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

        <button type="submit" class="btn btn-primary btn-lg">
            💾 <?= __('admin.settings_saved_success') ?>
        </button>
    </div>
</form>
@endsection
