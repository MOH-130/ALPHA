@extends('layouts.storefront')

@section('content')
<div class="container">
    <div class="auth-split-wrapper">
        <!-- 1. LEFT SHOWCASE PANEL: REGISTRATION PERKS -->
        <div class="auth-showcase-panel">
            <div>
                <!-- Brand Badge -->
                <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(0, 255, 136, 0.12); border:1px solid rgba(0, 255, 136, 0.3); border-radius:999px; padding:6px 14px; font-size:0.8rem; font-weight:800; color:#00ff88; margin-bottom:1.5rem;">
                    <span>💎 انضم إلى نخبة اللاعبين</span>
                    <span style="opacity:0.6;">•</span>
                    <span style="color:#ffffff;">عضوية فورية</span>
                </div>

                <h2 style="font-size:2rem; font-weight:900; line-height:1.2; margin-bottom:0.75rem; color:#ffffff;">
                    امتلك أقوى الألعاب <span style="background:linear-gradient(135deg, #00ff88, #00d2ff); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">بأفضل الأسعار الرسمية</span>
                </h2>
                <p style="color:var(--text-muted); font-size:0.95rem; line-height:1.6; margin-bottom:2rem;">
                    أنشئ حسابك المجاني في أقل من 30 ثانية واستفد من عروض الألعاب اليومية، بكجات Steam الخارقة، وتسليم الأكواد فورا عبر واتساب.
                </p>

                <!-- Membership Perks Grid -->
                <div style="display:grid; grid-template-columns:1fr; gap:1rem; margin-bottom:2rem;">
                    <div style="background:rgba(15, 23, 42, 0.7); border:1px solid rgba(0, 255, 136, 0.2); border-radius:12px; padding:1rem 1.25rem; display:flex; align-items:center; gap:14px;">
                        <span style="font-size:1.75rem;">⚡</span>
                        <div>
                            <div style="font-weight:800; font-size:0.95rem; color:#ffffff;">تسليم مباشر على الواتساب والإيميل</div>
                            <div style="font-size:0.8rem; color:var(--text-muted);">بيانات حسابك وكود اللعبة يصلانك في ثوانٍ معدودة.</div>
                        </div>
                    </div>

                    <div style="background:rgba(15, 23, 42, 0.7); border:1px solid rgba(0, 210, 255, 0.2); border-radius:12px; padding:1rem 1.25rem; display:flex; align-items:center; gap:14px;">
                        <span style="font-size:1.75rem;">🇩🇿</span>
                        <div>
                            <div style="font-weight:800; font-size:0.95rem; color:#ffffff;">دفع محلي فوري وآمن 100%</div>
                            <div style="font-size:0.8rem; color:var(--text-muted);">ادفع براحتك عبر بريدي موب BaridiMob و CCP و PayPal.</div>
                        </div>
                    </div>

                    <div style="background:rgba(15, 23, 42, 0.7); border:1px solid rgba(255, 170, 0, 0.2); border-radius:12px; padding:1rem 1.25rem; display:flex; align-items:center; gap:14px;">
                        <span style="font-size:1.75rem;">🛡️</span>
                        <div>
                            <div style="font-weight:800; font-size:0.95rem; color:#ffffff;">ألعاب أصلية بضمان شامل</div>
                            <div style="font-size:0.8rem; color:var(--text-muted);">جميع الحسابات والاشتراكات مرخصة مع دعم فني 24/7.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Trust Indicators -->
            <div>
                <div class="showcase-stats-grid" style="margin-top:0;">
                    <div class="showcase-stat-box">
                        <div class="showcase-stat-num">+45,000</div>
                        <div class="showcase-stat-label">لعبة وكود ستيم</div>
                    </div>
                    <div class="showcase-stat-box">
                        <div class="showcase-stat-num">100%</div>
                        <div class="showcase-stat-label">أصلي ومضمون</div>
                    </div>
                    <div class="showcase-stat-box">
                        <div class="showcase-stat-num">500 DA</div>
                        <div class="showcase-stat-label">تبدأ العروض من</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. RIGHT AUTH HUB PANEL: REGISTER FORM -->
        <div class="auth-form-panel">
            <div class="auth-glass-card">
                <!-- Header -->
                <div style="margin-bottom:1.75rem;">
                    <span style="color:var(--color-secondary); font-size:0.825rem; font-weight:800; letter-spacing:0.5px; text-transform:uppercase;">
                        <?= app_setting('site_name', 'Alpha Gaming Shop') ?>
                    </span>
                    <h1 style="font-size:1.85rem; font-weight:900; color:#ffffff; margin-top:2px;">
                        <?= __('auth.register') ?> 🎮
                    </h1>
                    <p style="color:var(--text-muted); font-size:0.875rem; margin-top:4px;">
                        أنشئ حسابك الشخصي وكن أول من يستفيد من العروض الحصرية
                    </p>
                </div>

                <!-- Google OAuth 2.0 Fast Sign-Up Button -->
                <div style="margin-bottom: 1.5rem;">
                    <a href="<?= url('auth/google') ?>" class="btn-google-login" style="display:flex; align-items:center; justify-content:center; gap:12px; width:100%; padding:0.85rem 1.25rem; background:#ffffff; color:#1f2937; border-radius:12px; font-weight:800; font-size:1rem; text-decoration:none; box-shadow:0 4px 15px rgba(0,0,0,0.3); transition:all 0.25s ease; border:1px solid #e5e7eb;">
                        <svg width="22" height="22" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.27-2.09 3.66-5.17 3.66-9.12z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.13C3.26 21.36 7.36 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.57H1.24C.45 8.14 0 9.99 0 12s.45 3.86 1.24 5.43l4.04-3.14z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.26 2.64 1.24 6.57l4.04 3.14c.95-2.83 3.6-4.96 6.72-4.96z"/>
                        </svg>
                        <span>التسجيل الفوري بنقرة واحدة عبر Google</span>
                    </a>
                    
                    <div style="display:flex; align-items:center; gap:12px; margin: 1.25rem 0 0.75rem;">
                        <div style="flex:1; height:1px; background:rgba(255,255,255,0.12);"></div>
                        <span style="color:#94a3b8; font-size:0.8rem; font-weight:700;">أو إنشاء الحساب بالبيانات الشخصية</span>
                        <div style="flex:1; height:1px; background:rgba(255,255,255,0.12);"></div>
                    </div>
                </div>

                <!-- Registration Form -->
                <form action="<?= url('register') ?>" method="POST" onsubmit="playCyberSound('access')">
                    <?= csrf_field() ?>

                    <!-- Name Field -->
                    <div class="form-group">
                        <label class="form-label"><?= __('messages.full_name') ?> *</label>
                        <div class="cyber-input-wrap">
                            <span class="input-icon-prefix">👤</span>
                            <input type="text" name="name" class="form-control" placeholder="الاسم واللقب الكامل" value="<?= htmlspecialchars(old('name')) ?>" required autofocus onfocus="playCyberSound('hover')">
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label class="form-label"><?= __('auth.email') ?> *</label>
                        <div class="cyber-input-wrap">
                            <span class="input-icon-prefix">✉️</span>
                            <input type="email" name="email" class="form-control" placeholder="user@gmail.com" value="<?= htmlspecialchars(old('email')) ?>" required onfocus="playCyberSound('hover')">
                        </div>
                    </div>

                    <!-- Phone (WhatsApp) Field -->
                    <div class="form-group">
                        <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                            <span><?= __('messages.phone_number') ?> (واتساب) *</span>
                            <span style="font-size:0.75rem; color:var(--color-secondary); font-weight:700;">لاستلام الأكواد فوراً</span>
                        </label>
                        <div class="cyber-input-wrap">
                            <span class="input-icon-prefix">📱</span>
                            <input type="tel" name="phone" class="form-control" placeholder="+213 774 44 44 44" value="<?= htmlspecialchars(old('phone')) ?>" required onfocus="playCyberSound('hover')">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label class="form-label"><?= __('auth.password') ?> * (6 أحرف على الأقل)</label>
                        <div class="cyber-input-wrap">
                            <span class="input-icon-prefix">🔒</span>
                            <input type="password" name="password" id="register-password" class="form-control" placeholder="••••••••" required onfocus="playCyberSound('hover')">
                            <button type="button" class="btn-eye-toggle" onclick="togglePasswordVisibility('register-password', this)" title="إظهار / إخفاء كلمة المرور">
                                👁️
                            </button>
                        </div>
                    </div>

                    <!-- Anti-Bot Math CAPTCHA -->
                    <?= \App\Services\CaptchaService::renderHtml() ?>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-luxury-shimmer btn-block btn-lg" style="width:100%; margin-top:1.5rem;">
                        إنشاء الحساب وبدء التسوق 🚀
                    </button>
                </form>

                <!-- Security Guarantee Pill -->
                <div style="text-align:center;">
                    <div class="security-vault-pill">
                        <span>🛡️</span> <span>بياناتك الشخصية مشفرة ومحمية بالكامل 100%</span>
                    </div>
                </div>

                <!-- Login Footer Switcher -->
                <div style="text-align:center; margin-top:2rem; font-size:0.925rem; color:var(--text-muted); border-top:1px solid rgba(255,255,255,0.08); padding-top:1.5rem;">
                    <?= __('auth.already_have_account') ?> 
                    <a href="<?= url('login') ?>" style="color:var(--color-primary); font-weight:800; text-decoration:none; margin-right:4px;">
                        <?= __('auth.login') ?> ⚡
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
