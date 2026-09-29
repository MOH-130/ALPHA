@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">
    <div style="max-width: 620px; margin: 0 auto;">
        <div class="auth-glass-card" style="background: rgba(13, 20, 36, 0.95); backdrop-filter: blur(25px); border: 1px solid rgba(0, 210, 255, 0.4); border-radius: 1.5rem; padding: 2.75rem 2.25rem; box-shadow: 0 25px 60px rgba(0,0,0,0.85), 0 0 35px rgba(0, 210, 255, 0.2); position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #4285F4, #34A853, #FBBC05, #EA4335);"></div>

            <!-- Google Header -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 68px; height: 68px; background: #ffffff; border-radius: 18px; margin-bottom: 1rem; box-shadow: 0 0 25px rgba(66, 133, 244, 0.3);">
                    <svg width="36" height="36" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.27-2.09 3.66-5.17 3.66-9.12z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.13C3.26 21.36 7.36 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.57H1.24C.45 8.14 0 9.99 0 12s.45 3.86 1.24 5.43l4.04-3.14z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.26 2.64 1.24 6.57l4.04 3.14c.95-2.83 3.6-4.96 6.72-4.96z"/>
                    </svg>
                </div>
                <h1 style="font-size: 1.75rem; font-weight: 900; color: #ffffff; margin: 0 0 0.5rem;">
                    بوابة ربط الحساب الفعلي مع Google
                </h1>
                <p style="color: #94a3b8; font-size: 0.92rem; line-height: 1.6; margin: 0;">
                    نظام تسجيل الدخول بـ Google مبرمج ومجهز بالكامل وفق بروتوكول <strong style="color: #00d2ff;">OAuth 2.0</strong> الرسمي.
                </p>
            </div>

            <!-- Setup Instructions Box -->
            <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(0, 210, 255, 0.25); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 2rem;">
                <h3 style="font-size: 0.95rem; font-weight: 800; color: #00d2ff; margin: 0 0 0.75rem; display: flex; align-items: center; gap: 8px;">
                    <span>⚙️</span> <span>تفعيل الربط المباشر مع حسابات Google الحية:</span>
                </h3>
                <p style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.6; margin-bottom: 0.75rem;">
                    لتوصيل متجرك بخوادم Google الرسمية، قم بإضافة مفاتيحك الخاصة من <a href="https://console.cloud.google.com/apis/credentials" target="_blank" style="color: #00ff88; text-decoration: underline;">Google Cloud Console</a> إلى ملف <code style="color: #ffaa00; background: rgba(0,0,0,0.4); padding: 2px 6px; border-radius: 4px;">.env</code>:
                </p>
                <div style="background: #090e1a; border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 0.75rem 1rem; font-family: monospace; font-size: 0.825rem; color: #a5f3fc; direction: ltr; text-align: left; margin-bottom: 0.75rem; overflow-x: auto;">
                    GOOGLE_CLIENT_ID="your_google_client_id"<br>
                    GOOGLE_CLIENT_SECRET="your_google_client_secret"
                </div>
                <div style="font-size: 0.8rem; color: #94a3b8;">
                    <strong>Authorized Redirect URI:</strong>
                    <br>
                    <code style="color: #00ff88; background: rgba(0,0,0,0.4); padding: 2px 6px; border-radius: 4px; word-break: break-all;"><?= htmlspecialchars($redirectUri) ?></code>
                </div>
            </div>

            <!-- Sandbox Instant Test Form -->
            <div style="background: rgba(0, 255, 136, 0.05); border: 1px dashed rgba(0, 255, 136, 0.35); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1rem;">
                    <span style="font-size: 1.3rem;">🧪</span>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 800; color: #00ff88; margin: 0;">
                            تجربة الدخول الفوري بحساب Google (Sandbox Mode)
                        </h4>
                        <span style="font-size: 0.78rem; color: #94a3b8;">اختبر عملية الربط وإنشاء الحساب تلقائياً الآن:</span>
                    </div>
                </div>

                <form action="<?= url('auth/google/simulate') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="font-size: 0.85rem; color: #cbd5e1;">اسم صاحب حساب Google:</label>
                        <input type="text" name="name" class="form-control" value="مستخدم Google الموثق" required style="background: #090e1c; border-color: rgba(255,255,255,0.15); color: #ffffff;">
                    </div>
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="font-size: 0.85rem; color: #cbd5e1;">إيميل Google الفعلي لتجربة الربط:</label>
                        <input type="email" name="email" class="form-control" value="elnou.gamer@gmail.com" required style="background: #090e1c; border-color: rgba(255,255,255,0.15); color: #00d2ff; font-weight: 700;">
                    </div>
                    <button type="submit" class="btn btn-luxury-shimmer btn-block btn-lg" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <span>🚀</span> <span>المتابعة وتسجيل الدخول عبر Google</span>
                    </button>
                </form>
            </div>

            <!-- Return Link -->
            <div style="text-align: center;">
                <a href="<?= url('login') ?>" style="color: #94a3b8; font-size: 0.875rem; text-decoration: none; font-weight: 700;">
                    &larr; <?= __('auth.login') ?>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
