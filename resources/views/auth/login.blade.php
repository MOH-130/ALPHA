@extends('layouts.storefront')

@section('content')
<div class="container">
    <div class="auth-split-wrapper">
        <!-- 1. LEFT SHOWCASE PANEL: VIP PASS & GAMING HIGHLIGHTS -->
        <div class="auth-showcase-panel">
            <div>
                <!-- Brand Badge -->
                <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(0, 210, 255, 0.12); border:1px solid rgba(0, 210, 255, 0.3); border-radius:999px; padding:6px 14px; font-size:0.8rem; font-weight:800; color:var(--color-primary); margin-bottom:1.5rem;">
                    <span>⚡ ALPHA GAMING VAULT</span>
                    <span style="opacity:0.6;">•</span>
                    <span style="color:#00ff88;">بوابة الولوج المشفرة</span>
                </div>

                <h2 style="font-size:2rem; font-weight:900; line-height:1.2; margin-bottom:0.75rem; color:#ffffff;">
                    مرحباً بك في عالم <span style="background:linear-gradient(135deg, #00d2ff, #00ff88); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">الألعاب الرقمية الفاخرة</span>
                </h2>
                <p style="color:var(--text-muted); font-size:0.95rem; line-height:1.6; margin-bottom:2rem;">
                    سجل دخولك الآن للوصول الفوري لمكتبة ألعابك، أكواد التفعيل الأصلية، وعروض البكجات الحصرية بالدينار الجزائري.
                </p>

                <!-- 3D Holographic VIP Pass Card -->
                <div class="vip-holo-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:42px; height:42px; background:linear-gradient(135deg, #00d2ff, #0077ff); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; box-shadow:0 0 15px rgba(0,210,255,0.4);">
                                🎮
                            </div>
                            <div>
                                <div style="font-weight:900; font-size:1.05rem; color:#ffffff; letter-spacing:0.5px;">ALPHA VIP PASS</div>
                                <div style="font-size:0.75rem; color:var(--color-secondary); font-weight:700;">DIGITAL ELITE MEMBER</div>
                            </div>
                        </div>
                        <span class="vip-badge-mini">💎 VIP TIER</span>
                    </div>

                    <!-- Chip & Serial -->
                    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-top:1.5rem; padding-top:1rem; border-top:1px solid rgba(255,255,255,0.08);">
                        <div>
                            <div style="font-size:0.7rem; color:var(--text-light); text-transform:uppercase; letter-spacing:1px;">SECURITY CLEARANCE</div>
                            <div style="font-family:monospace; font-size:0.95rem; font-weight:700; color:#e2e8f0; letter-spacing:1px; margin-top:2px;">
                                256-BIT ENCRYPTED // DZ-980
                            </div>
                        </div>
                        <div style="text-align:<?= is_rtl() ? 'left' : 'right' ?>;">
                            <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.75rem; font-weight:700; color:#00ff88; background:rgba(0,255,136,0.1); border:1px solid rgba(0,255,136,0.25); border-radius:4px; padding:2px 8px;">
                                🛡️ ضمان دائم 100%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Gaming Statistics Grid -->
            <div>
                <div class="showcase-stats-grid">
                    <div class="showcase-stat-box">
                        <div class="showcase-stat-num">+15,400</div>
                        <div class="showcase-stat-label">لاعب نشط 🇩🇿</div>
                    </div>
                    <div class="showcase-stat-box">
                        <div class="showcase-stat-num">99.8%</div>
                        <div class="showcase-stat-label">تسليم فوري آلي</div>
                    </div>
                    <div class="showcase-stat-box">
                        <div class="showcase-stat-num">24/7</div>
                        <div class="showcase-stat-label">دعم فني مباشر</div>
                    </div>
                </div>

                <!-- Live Pulse Activity Box -->
                <div class="live-activity-box">
                    <div class="live-pulse-dot"></div>
                    <div style="color:#cbd5e1; font-size:0.85rem; line-height:1.4;">
                        <strong style="color:#ffffff;">نشاط حي:</strong> تم تسليم كود تفعيل لعبة <strong>GTA 6 PS5</strong> للتو بنجاح عبر بريدي موب! ⚡
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. RIGHT AUTH HUB PANEL: LOGIN FORM -->
        <div class="auth-form-panel">
            <div class="auth-glass-card">
                <!-- Audio SFX Toggle & Header -->
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <div>
                        <span style="color:var(--color-primary); font-size:0.825rem; font-weight:800; letter-spacing:0.5px; text-transform:uppercase;">
                            <?= app_setting('site_name', 'Alpha Gaming Shop') ?>
                        </span>
                        <h1 style="font-size:1.85rem; font-weight:900; color:#ffffff; margin-top:2px;">
                            <?= __('auth.login') ?> 🚀
                        </h1>
                    </div>
                    <button type="button" onclick="toggleSfx(this)" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); color:#cbd5e1; border-radius:20px; padding:4px 10px; font-size:0.8rem; cursor:pointer; font-family:inherit; transition:all 0.2s ease;">
                        🔊 <span style="font-size:0.75rem;">المؤثرات: مفعلة</span>
                    </button>
                </div>

                <!-- 1-Click Fast Demo Credentials Buttons (Protected by Production Shield) -->
                <?php if (app_setting('enable_demo_mode', '0') === '1'): ?>
                <div class="demo-quick-bar">
                    <div class="demo-quick-title">
                        <span>⚡ تجربة الحسابات بنقرة واحدة (1-Click Fill)</span>
                        <span style="color:var(--color-primary); font-size:0.75rem;">اختر حسابك:</span>
                    </div>
                    <div class="demo-chips-grid">
                        <button type="button" class="btn-demo-quick" onclick="fillDemoCredentials('customer')">
                            <span>🎮</span> <span>عميل VIP تجريبي</span>
                        </button>
                        <a href="<?= url('admin/login') ?>" class="btn-demo-quick admin" style="text-decoration:none;">
                            <span>👑</span> <span>بوابة المشرف (Admin)</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Google OAuth 2.0 Sign-In Button -->
                <div style="margin-bottom: 1.5rem;">
                    <a href="<?= url('auth/google') ?>" class="btn-google-login" style="display:flex; align-items:center; justify-content:center; gap:12px; width:100%; padding:0.85rem 1.25rem; background:#ffffff; color:#1f2937; border-radius:12px; font-weight:800; font-size:1rem; text-decoration:none; box-shadow:0 4px 15px rgba(0,0,0,0.3); transition:all 0.25s ease; border:1px solid #e5e7eb;">
                        <svg width="22" height="22" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.27-2.09 3.66-5.17 3.66-9.12z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.13C3.26 21.36 7.36 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.57H1.24C.45 8.14 0 9.99 0 12s.45 3.86 1.24 5.43l4.04-3.14z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.26 2.64 1.24 6.57l4.04 3.14c.95-2.83 3.6-4.96 6.72-4.96z"/>
                        </svg>
                        <span>تسجيل الدخول الفوري بحساب Google</span>
                    </a>
                    
                    <div style="display:flex; align-items:center; gap:12px; margin: 1.25rem 0 0.75rem;">
                        <div style="flex:1; height:1px; background:rgba(255,255,255,0.12);"></div>
                        <span style="color:#94a3b8; font-size:0.8rem; font-weight:700;">أو تسجيل الدخول بالإيميل وكلمة السر</span>
                        <div style="flex:1; height:1px; background:rgba(255,255,255,0.12);"></div>
                    </div>
                </div>

                <!-- Login Form -->
                <form action="<?= url('login') ?>" method="POST" onsubmit="playCyberSound('access')">
                    <?= csrf_field() ?>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                            <span><?= __('auth.email') ?> *</span>
                            <span style="font-size:0.75rem; color:var(--text-light); font-weight:400;">الإيميل المسجل لدينا</span>
                        </label>
                        <div class="cyber-input-wrap">
                            <span class="input-icon-prefix">✉️</span>
                            <input type="email" name="email" id="login-email" class="form-control" placeholder="user@store.com" value="<?= htmlspecialchars(old('email')) ?>" required autofocus onfocus="playCyberSound('hover')">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                            <label class="form-label" style="margin-bottom:0;"><?= __('auth.password') ?> *</label>
                            <a href="<?= url('forgot-password') ?>" style="font-size:0.825rem; color:var(--color-primary); font-weight:700;">
                                <?= __('auth.forgot_password') ?>
                            </a>
                        </div>
                        <div class="cyber-input-wrap">
                            <span class="input-icon-prefix">🔒</span>
                            <input type="password" name="password" id="login-password" class="form-control" placeholder="••••••••" required onfocus="playCyberSound('hover')">
                            <button type="button" class="btn-eye-toggle" onclick="togglePasswordVisibility('login-password', this)" title="إظهار / إخفاء كلمة المرور">
                                👁️
                            </button>
                        </div>
                    </div>

                    <!-- Anti-Bot Math CAPTCHA -->
                    <?= \App\Services\CaptchaService::renderHtml() ?>

                    <!-- Remember Me Option -->
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label style="display:flex; align-items:center; gap:10px; font-size:0.875rem; cursor:pointer; color:#cbd5e1; user-select:none;">
                            <input type="checkbox" name="remember" value="1" style="accent-color:var(--color-primary); width:18px; height:18px; cursor:pointer;">
                            <span><?= __('auth.remember_me') ?></span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-luxury-shimmer btn-block btn-lg" style="width:100%;">
                        <?= __('auth.login') ?> 🚀
                    </button>
                </form>

                <!-- Security Guarantee Pill -->
                <div style="text-align:center;">
                    <div class="security-vault-pill">
                        <span>🔒</span> <span>اتصال محمي ومشفر 256-Bit SSL Gaming Vault</span>
                    </div>
                </div>

                <!-- Register Footer Switcher -->
                <div style="text-align:center; margin-top:2rem; font-size:0.925rem; color:var(--text-muted); border-top:1px solid rgba(255,255,255,0.08); padding-top:1.5rem;">
                    <?= __('auth.dont_have_account') ?> 
                    <a href="<?= url('register') ?>" style="color:var(--color-secondary); font-weight:800; text-decoration:none; margin-right:4px;">
                        <?= __('auth.register') ?> 💎
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
