@extends('layouts.admin')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <!-- Page Header & Badges -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(0, 210, 255, 0.1); border:1px solid rgba(0, 210, 255, 0.3); border-radius:999px; padding:4px 12px; font-size:0.75rem; font-weight:800; color:var(--color-primary); margin-bottom:0.5rem;">
                <span>🛡️ ALPHA SECURITY VAULT</span>
                <span>•</span>
                <span style="color:#00ff88;">حماية 100%</span>
            </div>
            <h1 style="font-size:1.75rem; font-weight:900; color:#ffffff; margin:0 0 0.35rem;">
                🔐 أمان حساب المشرف وتغيير بيانات الدخول
            </h1>
            <p style="color:#94a3b8; font-size:0.9rem; margin:0;">
                قم بتغيير بريدك الإلكتروني، كلمة المرور السرية، وتفعيل درع الإنتاج لحماية متجرك ومنع أي دخول غير مصرح به.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <span style="background:rgba(0, 255, 136, 0.1); border:1px solid rgba(0, 255, 136, 0.3); color:#00ff88; font-size:0.8rem; font-weight:800; padding:6px 14px; border-radius:8px;">
                🟢 الحساب مؤمن ومشفر
            </span>
        </div>
    </div>

    <!-- Flash Notifications -->
    <?php if ($msg = session()->flash('success')): ?>
        <div style="background:rgba(16, 185, 129, 0.15); color:#34d399; padding:1rem 1.25rem; border-radius:10px; border:1px solid rgba(16, 185, 129, 0.3); margin-bottom:1.5rem; font-weight:700; display:flex; align-items:center; gap:10px;">
            <span style="font-size:1.25rem;">✅</span>
            <span><?= $msg ?></span>
        </div>
    <?php endif; ?>

    <?php if ($msg = session()->flash('error')): ?>
        <div style="background:rgba(239, 68, 68, 0.15); color:#f87171; padding:1rem 1.25rem; border-radius:10px; border:1px solid rgba(239, 68, 68, 0.3); margin-bottom:1.5rem; font-weight:700; display:flex; align-items:center; gap:10px;">
            <span style="font-size:1.25rem;">⚠️</span>
            <span><?= $msg ?></span>
        </div>
    <?php endif; ?>

    <!-- Main Profile & Security Form -->
    <form action="<?= url('admin/profile') ?>" method="POST">
        <?= csrf_field() ?>

        <!-- SECTION 1: Personal Credentials -->
        <div class="admin-card" style="margin-bottom:1.75rem; border:1px solid rgba(0, 210, 255, 0.25); box-shadow:0 10px 30px rgba(0,0,0,0.4);">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center; background:rgba(0, 210, 255, 0.04); border-bottom:1px solid rgba(255,255,255,0.06);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:1.3rem;">👤</span>
                    <h3 style="font-size:1.1rem; font-weight:800; color:#ffffff; margin:0;">البيانات الشخصية ورتبة المشرف</h3>
                </div>
                <span style="font-size:0.75rem; background:rgba(255, 255, 255, 0.08); padding:3px 8px; border-radius:4px; color:#cbd5e1; font-weight:700;">
                    معرّف الحساب: #<?= $admin->id ?>
                </span>
            </div>
            <div class="admin-card-body" style="padding:1.75rem;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;">اسم المشرف الكامل *</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($admin->name) ?>" required style="background:#131c2e; border-color:rgba(255,255,255,0.12); color:#fff; padding:0.75rem 1rem; border-radius:8px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;">
                            البريد الإلكتروني لتسجيل الدخول *
                        </label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin->email) ?>" required style="background:#131c2e; border-color:rgba(0, 210, 255, 0.4); color:#00d2ff; font-weight:700; padding:0.75rem 1rem; border-radius:8px;">
                        <small style="color:#94a3b8; font-size:0.78rem; margin-top:4px; display:block;">
                            ⚠️ هذا هو البريد الذي ستستخدمه للدخول كمسؤول في صفحة <code>/admin/login</code>
                        </small>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-top:1rem;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;">رقم الهاتف / واتساب</label>
                        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($admin->phone ?? '') ?>" placeholder="+213 774 44 44 44" style="background:#131c2e; border-color:rgba(255,255,255,0.12); color:#fff; padding:0.75rem 1rem; border-radius:8px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;">مستوى الصلاحية بالنظام</label>
                        <input type="text" class="form-control" value="🛡️ مدير عام كامل الصلاحيات (Super Admin)" disabled style="background:#090d17; border-color:rgba(255,255,255,0.06); color:#00ff88; font-weight:800; padding:0.75rem 1rem; border-radius:8px; cursor:not-allowed;">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Change Password -->
        <div class="admin-card" style="margin-bottom:1.75rem; border:1px solid rgba(0, 255, 136, 0.25); box-shadow:0 10px 30px rgba(0,0,0,0.4);">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center; background:rgba(0, 255, 136, 0.04); border-bottom:1px solid rgba(255,255,255,0.06);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:1.3rem;">🔑</span>
                    <h3 style="font-size:1.1rem; font-weight:800; color:#ffffff; margin:0;">تغيير كلمة المرور وتأمين البوابة</h3>
                </div>
                <span style="font-size:0.8rem; color:#94a3b8;">اترك الحقول فارغة إذا كنت لا ترغب بتغييرها</span>
            </div>
            <div class="admin-card-body" style="padding:1.75rem;">
                <div class="form-group" style="margin-bottom:1.25rem;">
                    <label class="form-label" style="font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;">كلمة المرور الحالية</label>
                    <div style="position:relative;">
                        <input type="password" id="current_password" name="current_password" class="form-control" placeholder="••••••••" style="background:#131c2e; border-color:rgba(255,255,255,0.12); color:#fff; padding:0.75rem 2.8rem 0.75rem 1rem; border-radius:8px;">
                        <button type="button" onclick="togglePassVisibility('current_password', this)" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#94a3b8; cursor:pointer; font-size:1.1rem;">👁️</button>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;">كلمة المرور الجديدة (6 أحرف على الأقل)</label>
                        <div style="position:relative;">
                            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="••••••••" style="background:#131c2e; border-color:rgba(0, 255, 136, 0.4); color:#fff; padding:0.75rem 2.8rem 0.75rem 1rem; border-radius:8px;">
                            <button type="button" onclick="togglePassVisibility('new_password', this)" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#94a3b8; cursor:pointer; font-size:1.1rem;">👁️</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;">تأكيد كلمة المرور الجديدة</label>
                        <div style="position:relative;">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="••••••••" style="background:#131c2e; border-color:rgba(0, 255, 136, 0.4); color:#fff; padding:0.75rem 2.8rem 0.75rem 1rem; border-radius:8px;">
                            <button type="button" onclick="togglePassVisibility('confirm_password', this)" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#94a3b8; cursor:pointer; font-size:1.1rem;">👁️</button>
                        </div>
                    </div>
                </div>

                <!-- Security Tips Box -->
                <div style="background:rgba(0, 210, 255, 0.06); border:1px dashed rgba(0, 210, 255, 0.3); border-radius:8px; padding:0.85rem 1rem; font-size:0.825rem; color:#cbd5e1; margin-top:1rem; display:flex; align-items:center; gap:8px;">
                    <span style="font-size:1.1rem;">💡</span>
                    <span>نصيحة أمان: اختر كلمة مرور قوية تحتوي على أحرف كبيرة وصغيرة وأرقام ورموز لضمان عدم تمكن أي شخص من تخمينها.</span>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Production Live Shield & Demo Mode Controls -->
        <div class="admin-card" style="margin-bottom:2rem; border:1px solid rgba(255, 170, 0, 0.35); box-shadow:0 10px 30px rgba(0,0,0,0.4);">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center; background:rgba(255, 170, 0, 0.05); border-bottom:1px solid rgba(255,255,255,0.06);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:1.3rem;">🚀</span>
                    <h3 style="font-size:1.1rem; font-weight:800; color:#ffd700; margin:0;">درع الإنتاج والوضع الآمن للنشر المباشر</h3>
                </div>
                <span style="font-size:0.75rem; background:rgba(255, 170, 0, 0.15); color:#ffd700; border:1px solid rgba(255, 170, 0, 0.3); padding:3px 8px; border-radius:4px; font-weight:800;">
                    PRODUCTION SHIELD
                </span>
            </div>
            <div class="admin-card-body" style="padding:1.75rem;">
                <!-- Demo Mode Toggle -->
                <label style="display:flex; align-items:flex-start; gap:14px; cursor:pointer; padding-bottom:1.5rem; border-bottom:1px solid rgba(255,255,255,0.06);">
                    <input type="checkbox" name="enable_demo_mode" value="1" <?= $enableDemoMode === '1' ? 'checked' : '' ?> style="accent-color:var(--color-primary); width:22px; height:22px; margin-top:2px; flex-shrink:0; cursor:pointer;">
                    <div>
                        <div style="font-weight:800; font-size:0.95rem; color:#ffffff;">
                            إظهار أزرار الحسابات التجريبية والتعبئة بنقرة واحدة على شاشات الدخول (Demo Mode)
                        </div>
                        <p style="color:#94a3b8; font-size:0.85rem; line-height:1.6; margin:6px 0 0;">
                            ⚠️ <strong>توصية النشر:</strong> عند <span style="color:#ffaa00; font-weight:700;">إلغاء تحديد هذا الخيار</span>، سيتم فوراً إخفاء أي بيانات تجريبية وأزرار ملء تلقائي من واجهات تسجيل الدخول، وبذلك لن يستطيع أي زائر معرفة أو تخمين أي بيانات وستكون أنت الوحيد الذي يمتلك مفاتيح الدخول!
                        </p>
                    </div>
                </label>

                <!-- 2FA OTP Toggle -->
                <label style="display:flex; align-items:flex-start; gap:14px; cursor:pointer; margin-top:1.5rem;">
                    <input type="checkbox" name="two_factor_enabled" value="1" <?= ($admin->two_factor_enabled ?? 0) == 1 ? 'checked' : '' ?> style="accent-color:#00ff88; width:22px; height:22px; margin-top:2px; flex-shrink:0; cursor:pointer;">
                    <div>
                        <div style="font-weight:800; font-size:0.95rem; color:#00ff88;">
                            🛡️ تفعيل درع التحقق الأمني المزدوج (2FA OTP) لبوابة المشرف
                        </div>
                        <p style="color:#94a3b8; font-size:0.85rem; line-height:1.6; margin:6px 0 0;">
                            عند التفعيل، سيُطلب منك إدخال رمز أمان عشوائي مكوّن من 6 أرقام يتم إرساله إلى بريدك الإلكتروني في كل عملية تسجيل دخول للمشرف، لمنع أي محاولة اختراق حتى في حال تسرب كلمة السر.
                        </p>
                    </div>
                </label>
            </div>
        </div>

        <!-- SECTION 4: Google OAuth 2.0 Integration Keys -->
        <div class="admin-card" style="margin-bottom:2rem; border:1px solid rgba(66, 133, 244, 0.35); box-shadow:0 10px 30px rgba(0,0,0,0.4);">
            <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center; background:rgba(66, 133, 244, 0.05); border-bottom:1px solid rgba(255,255,255,0.06);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:1.3rem;">🌐</span>
                    <h3 style="font-size:1.1rem; font-weight:800; color:#60a5fa; margin:0;">إعدادات ربط تسجيل الدخول مع Google OAuth 2.0</h3>
                </div>
                <span style="font-size:0.75rem; background:rgba(66, 133, 244, 0.15); color:#60a5fa; border:1px solid rgba(66, 133, 244, 0.3); padding:3px 8px; border-radius:4px; font-weight:800;">
                    GOOGLE SIGN-IN
                </span>
            </div>
            <div class="admin-card-body" style="padding:1.75rem;">
                <p style="color:#cbd5e1; font-size:0.875rem; line-height:1.6; margin-bottom:1.25rem;">
                    يتيح للمستخدمين تسجيل الدخول أو إنشاء حسابات فورية بنقرة واحدة بحساباتهم الفعلية على Google. احصل على المفاتيح من <a href="https://console.cloud.google.com/apis/credentials" target="_blank" style="color:#00d2ff; text-decoration:underline;">Google Cloud Console</a>.
                </p>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;">Google Client ID</label>
                        <input type="text" name="google_client_id" class="form-control" value="<?= htmlspecialchars($googleClientId ?? '') ?>" placeholder="xxxxxx.apps.googleusercontent.com" style="background:#131c2e; border-color:rgba(255,255,255,0.12); color:#fff; padding:0.75rem 1rem; border-radius:8px; font-family:monospace; font-size:0.85rem;">
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;">Google Client Secret</label>
                        <input type="password" name="google_client_secret" class="form-control" value="<?= htmlspecialchars($googleClientSecret ?? '') ?>" placeholder="GOCSPX-xxxxxx" style="background:#131c2e; border-color:rgba(255,255,255,0.12); color:#fff; padding:0.75rem 1rem; border-radius:8px; font-family:monospace; font-size:0.85rem;">
                    </div>
                </div>

                <div style="background:rgba(0, 0, 0, 0.4); border:1px solid rgba(0, 210, 255, 0.2); border-radius:10px; padding:1.1rem; margin-top:1.25rem; font-size:0.825rem; color:#94a3b8;">
                    <div style="margin-bottom:0.75rem;">
                        <strong style="color:#e2e8f0; font-size:0.875rem;">الرابط المحسوب للبيئة الحالية (Active Redirect URI):</strong>
                        <div style="margin-top:4px;">
                            <code style="color:#00ff88; font-size:0.85rem; word-break:break-all; background:rgba(0,0,0,0.5); padding:3px 8px; border-radius:4px; font-weight:700;"><?= htmlspecialchars($googleRedirectUri) ?></code>
                        </div>
                    </div>
                    
                    <div style="border-top:1px dashed rgba(255,255,255,0.1); padding-top:0.75rem;">
                        <div style="color:#60a5fa; font-weight:800; margin-bottom:6px;">📋 الروابط الواجب إدخالها في Google Cloud Console لتعمل البيئتان معاً:</div>
                        <div style="font-family:monospace; font-size:0.8rem; direction:ltr; text-align:left; background:rgba(10,15,26,0.8); padding:8px 12px; border-radius:6px; line-height:1.7;">
                            <div style="color:#94a3b8; font-size:0.75rem; font-weight:bold;"># Authorized redirect URIs:</div>
                            <div><span style="color:#00ff88;">https://alpha-efxl.onrender.com/auth/google/callback</span> <span style="color:#64748b;">(Production Render)</span></div>
                            <div><span style="color:#facc15;">http://localhost:8000/auth/google/callback</span> <span style="color:#64748b;">(Local Dev)</span></div>
                            <div style="color:#94a3b8; font-size:0.75rem; font-weight:bold; margin-top:6px;"># Authorized JavaScript origins:</div>
                            <div><span style="color:#00d2ff;">https://alpha-efxl.onrender.com</span></div>
                            <div><span style="color:#38bdf8;">http://localhost:8000</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-block btn-lg" style="background:linear-gradient(135deg, #00d2ff, #0077ff); color:#000; font-size:1.1rem; font-weight:900; border-radius:10px; padding:1rem; box-shadow:0 6px 25px rgba(0, 210, 255, 0.35); border:none; cursor:pointer; width:100%;">
            💾 حفظ التعديلات وتأمين الحساب فوراً 🔒
        </button>
    </form>
</div>

<script>
function togglePassVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerText = '👁️‍🗨️';
    } else {
        input.type = 'password';
        btn.innerText = '👁️';
    }
}
</script>
@endsection
