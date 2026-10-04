@extends('layouts.admin')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:1.6rem; font-weight:800; color:#fff; display:flex; align-items:center; gap:8px;">
            <span>🎨</span> تخصيص المظهر وتصميم المتجر (Theme Customizer)
        </h1>
        <p style="color:var(--text-muted); font-size:0.9rem; margin-top:4px;">
            تحكم كامل في ألوان المتجر، قوالب الخلفية الفخمة، مؤشر الماوس التفاعلي، وشاشات الترحيب مع معاينة مباشرة.
        </p>
    </div>
    <a href="<?= url('/') ?>" target="_blank" class="btn btn-outline btn-sm" style="display:flex; align-items:center; gap:6px;">
        <span>🌐</span> معاينة واجهة المتجر
    </a>
</div>

<form action="<?= url('admin/theme') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div style="display:grid; grid-template-columns: 1fr 400px; gap:2rem; align-items:start;">
        
        <!-- Left: Settings Panels -->
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            
            <!-- 1. Background Style Presets -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">🌌 قوالب وأنماط الخلفية الفخمة (Background Presets)</h3>
                </div>
                <div class="admin-card-body">
                    <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1rem;">
                        اختر أحد الأنماط الجاهزة فائقة السرعة مع تأثيرات الألوان والتوهج السيبراني:
                    </p>

                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:1rem;">
                        <!-- Preset 1 -->
                        <label style="border:2px solid <?= theme_setting('bg_preset', 'dark_obsidian') === 'dark_obsidian' ? 'var(--color-primary)' : 'rgba(255,255,255,0.1)' ?>; border-radius:12px; padding:12px; cursor:pointer; background:#080b13; transition:all 0.2s ease; display:flex; flex-direction:column; gap:6px;" class="preset-card">
                            <input type="radio" name="bg_preset" value="dark_obsidian" <?= theme_setting('bg_preset', 'dark_obsidian') === 'dark_obsidian' ? 'checked' : '' ?> onchange="applyPreset('dark_obsidian', '#00d2ff', '#00ff88', '#ffaa00', '#080b13', '#0f1626')" style="display:none;">
                            <div style="height:44px; border-radius:6px; background:radial-gradient(circle at 80% 20%, rgba(0, 210, 255, 0.4), transparent 60%), #080b13; border:1px solid rgba(0,210,255,0.3);"></div>
                            <div style="font-weight:700; font-size:0.9rem; color:#fff;">Dark Obsidian</div>
                            <div style="font-size:0.75rem; color:#94a3b8;">سيان متوهج + أخضر زمردي</div>
                        </label>

                        <!-- Preset 2 -->
                        <label style="border:2px solid <?= theme_setting('bg_preset') === 'deep_nebula' ? 'var(--color-primary)' : 'rgba(255,255,255,0.1)' ?>; border-radius:12px; padding:12px; cursor:pointer; background:#070c1b; transition:all 0.2s ease; display:flex; flex-direction:column; gap:6px;" class="preset-card">
                            <input type="radio" name="bg_preset" value="deep_nebula" <?= theme_setting('bg_preset') === 'deep_nebula' ? 'checked' : '' ?> onchange="applyPreset('deep_nebula', '#38bdf8', '#a855f7', '#f43f5e', '#070c1b', '#0f172a')" style="display:none;">
                            <div style="height:44px; border-radius:6px; background:radial-gradient(circle at 80% 20%, rgba(168, 85, 247, 0.4), transparent 60%), #070c1b; border:1px solid rgba(168,85,247,0.3);"></div>
                            <div style="font-weight:700; font-size:0.9rem; color:#fff;">Deep Nebula</div>
                            <div style="font-size:0.75rem; color:#94a3b8;">أزرق فضائي + بنفسجي نيون</div>
                        </label>

                        <!-- Preset 3 -->
                        <label style="border:2px solid <?= theme_setting('bg_preset') === 'midnight_cyber' ? 'var(--color-primary)' : 'rgba(255,255,255,0.1)' ?>; border-radius:12px; padding:12px; cursor:pointer; background:#0e0a18; transition:all 0.2s ease; display:flex; flex-direction:column; gap:6px;" class="preset-card">
                            <input type="radio" name="bg_preset" value="midnight_cyber" <?= theme_setting('bg_preset') === 'midnight_cyber' ? 'checked' : '' ?> onchange="applyPreset('midnight_cyber', '#f43f5e', '#ec4899', '#facc15', '#0e0a18', '#181228')" style="display:none;">
                            <div style="height:44px; border-radius:6px; background:radial-gradient(circle at 80% 20%, rgba(244, 63, 94, 0.4), transparent 60%), #0e0a18; border:1px solid rgba(244,63,94,0.3);"></div>
                            <div style="font-weight:700; font-size:0.9rem; color:#fff;">Midnight Cyber</div>
                            <div style="font-size:0.75rem; color:#94a3b8;">وردي نيون + أصفر ذهبي</div>
                        </label>

                        <!-- Preset 4 -->
                        <label style="border:2px solid <?= theme_setting('bg_preset') === 'matrix_emerald' ? 'var(--color-primary)' : 'rgba(255,255,255,0.1)' ?>; border-radius:12px; padding:12px; cursor:pointer; background:#05120d; transition:all 0.2s ease; display:flex; flex-direction:column; gap:6px;" class="preset-card">
                            <input type="radio" name="bg_preset" value="matrix_emerald" <?= theme_setting('bg_preset') === 'matrix_emerald' ? 'checked' : '' ?> onchange="applyPreset('matrix_emerald', '#10b981', '#00ff88', '#84cc16', '#05120d', '#0a1e16')" style="display:none;">
                            <div style="height:44px; border-radius:6px; background:radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.4), transparent 60%), #05120d; border:1px solid rgba(16,185,129,0.3);"></div>
                            <div style="font-weight:700; font-size:0.9rem; color:#fff;">Matrix Emerald</div>
                            <div style="font-size:0.75rem; color:#94a3b8;">أخضر تقني زمردي</div>
                        </label>

                        <!-- Preset 5 -->
                        <label style="border:2px solid <?= theme_setting('bg_preset') === 'carbon' ? 'var(--color-primary)' : 'rgba(255,255,255,0.1)' ?>; border-radius:12px; padding:12px; cursor:pointer; background:#111114; transition:all 0.2s ease; display:flex; flex-direction:column; gap:6px;" class="preset-card">
                            <input type="radio" name="bg_preset" value="carbon" <?= theme_setting('bg_preset') === 'carbon' ? 'checked' : '' ?> onchange="applyPreset('carbon', '#f97316', '#ef4444', '#eab308', '#111114', '#1a1a1f')" style="display:none;">
                            <div style="height:44px; border-radius:6px; background:radial-gradient(circle at 80% 20%, rgba(249, 115, 22, 0.4), transparent 60%), #111114; border:1px solid rgba(249,115,22,0.3);"></div>
                            <div style="font-weight:700; font-size:0.9rem; color:#fff;">Carbon Gaming</div>
                            <div style="font-size:0.75rem; color:#94a3b8;">برتقالي ناري + كاربون</div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 2. Interactive Gamer Cursor Options -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">🖱️ مؤشر الماوس التفاعلي والمتوهج (Interactive Gamer Cursor)</h3>
                </div>
                <div class="admin-card-body">
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem; background:var(--bg-input); border-radius:10px; margin-bottom:1.25rem;">
                        <div>
                            <div style="font-weight:700; color:#fff; font-size:0.95rem;">تفعيل مؤشر الماوس التفاعلي المتوهج</div>
                            <div style="font-size:0.8rem; color:var(--text-muted);">حلقة نيون متوهجة تتبع الماوس وتتفاعل مع الروابط والأزرار بسلاسة 60fps</div>
                        </div>
                        <label style="position:relative; display:inline-block; width:48px; height:26px;">
                            <input type="hidden" name="enable_custom_cursor" value="0">
                            <input type="checkbox" name="enable_custom_cursor" value="1" <?= theme_setting('enable_custom_cursor', '1') === '1' ? 'checked' : '' ?> style="accent-color:var(--color-primary); width:20px; height:20px; cursor:pointer;">
                        </label>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">لون وهج مؤشر الماوس (Cursor Glow Color)</label>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <input type="color" id="cursor_glow_color_picker" name="cursor_glow_color" value="<?= theme_setting('cursor_glow_color', '#00d2ff') ?>" style="width:48px; height:40px; border:none; border-radius:6px; cursor:pointer;" oninput="document.getElementById('cursor_glow_hex').value = this.value; updateLivePreview();">
                                <input type="text" id="cursor_glow_hex" class="form-control" value="<?= theme_setting('cursor_glow_color', '#00d2ff') ?>" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">تفعيل شاشة الترحيب المنبثقة (Welcome Modal)</label>
                            <select name="enable_welcome_modal" class="form-control">
                                <option value="1" <?= theme_setting('enable_welcome_modal', '1') === '1' ? 'selected' : '' ?>>مفعلة (تظهر للمستخدمين عند الدخول)</option>
                                <option value="0" <?= theme_setting('enable_welcome_modal') === '0' ? 'selected' : '' ?>>معطلة</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Colors & Branding Customizer -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">🎨 تفاصيل الألوان المخصصة (Custom Colors)</h3>
                </div>
                <div class="admin-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">اللون الرئيسي (Primary Color)</label>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <input type="color" id="theme_primary_color" name="primary_color" value="<?= theme_setting('primary_color', '#00d2ff') ?>" style="width:48px; height:40px; border:none; border-radius:6px; cursor:pointer;" oninput="document.getElementById('primary_hex').value = this.value; updateLivePreview();">
                                <input type="text" id="primary_hex" class="form-control" value="<?= theme_setting('primary_color', '#00d2ff') ?>" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">اللون الثانوي (Secondary Glow)</label>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <input type="color" id="theme_secondary_color" name="secondary_color" value="<?= theme_setting('secondary_color', '#00ff88') ?>" style="width:48px; height:40px; border:none; border-radius:6px; cursor:pointer;" oninput="document.getElementById('secondary_hex').value = this.value; updateLivePreview();">
                                <input type="text" id="secondary_hex" class="form-control" value="<?= theme_setting('secondary_color', '#00ff88') ?>" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">لون الخصومات والتمييز (Accent)</label>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <input type="color" id="theme_accent_color" name="accent_color" value="<?= theme_setting('accent_color', '#ffaa00') ?>" style="width:48px; height:40px; border:none; border-radius:6px; cursor:pointer;" oninput="document.getElementById('accent_hex').value = this.value; updateLivePreview();">
                                <input type="text" id="accent_hex" class="form-control" value="<?= theme_setting('accent_color', '#ffaa00') ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-row" style="margin-top:1rem;">
                        <div class="form-group">
                            <label class="form-label">لون خلفية الصفحة (Background Main)</label>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <input type="color" id="theme_bg_main" name="bg_main_color" value="<?= theme_setting('bg_main_color', '#080b13') ?>" style="width:48px; height:40px; border:none; border-radius:6px; cursor:pointer;" oninput="document.getElementById('bg_main_hex').value = this.value; updateLivePreview();">
                                <input type="text" id="bg_main_hex" class="form-control" value="<?= theme_setting('bg_main_color', '#080b13') ?>" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">لون خلفية البطاقات (Card Background)</label>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <input type="color" id="theme_bg_card" name="bg_card_color" value="<?= theme_setting('bg_card_color', '#0f1626') ?>" style="width:48px; height:40px; border:none; border-radius:6px; cursor:pointer;" oninput="document.getElementById('bg_card_hex').value = this.value; updateLivePreview();">
                                <input type="text" id="bg_card_hex" class="form-control" value="<?= theme_setting('bg_card_color', '#0f1626') ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-row" style="margin-top:1rem;">
                        <div class="form-group">
                            <label class="form-label">نوع الخط العربي (Font Family)</label>
                            <select name="font_family" class="form-control" onchange="updateLivePreview();">
                                <option value="Cairo" <?= theme_setting('font_family', 'Cairo') === 'Cairo' ? 'selected' : '' ?>>خط كايرو (Cairo - احترافي وعصري)</option>
                                <option value="Tajawal" <?= theme_setting('font_family') === 'Tajawal' ? 'selected' : '' ?>>خط تجوال (Tajawal - ناعم ومريح)</option>
                                <option value="Plus Jakarta Sans" <?= theme_setting('font_family') === 'Plus Jakarta Sans' ? 'selected' : '' ?>>Plus Jakarta Sans (عالمي)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">شكل وانحناء الأزرار (Border Radius)</label>
                            <select name="button_radius" class="form-control" onchange="updateLivePreview();">
                                <option value="0.75rem" <?= theme_setting('button_radius', '0.75rem') === '0.75rem' ? 'selected' : '' ?>>مستدير متوسط (0.75rem - قياسي)</option>
                                <option value="0.375rem" <?= theme_setting('button_radius') === '0.375rem' ? 'selected' : '' ?>>مستطيل بحواف ناعمة (0.375rem)</option>
                                <option value="9999px" <?= theme_setting('button_radius') === '9999px' ? 'selected' : '' ?>>دائري بالكامل (Pill Shape)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Homepage Banners & Texts -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">🖼️ نصوص الصفحة الرئيسية والبانر الترويجي</h3>
                </div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label class="form-label">عنوان البانر الرئيسي بالعربية</label>
                        <input type="text" name="hero_headline_ar" class="form-control" value="<?= htmlspecialchars(theme_setting('hero_headline_ar', 'عالم الألعاب الأصلية، بكجات Steam، واشتراكات الترفيه')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">عنوان البانر الرئيسي بالإنجليزية</label>
                        <input type="text" name="hero_headline_en" class="form-control" value="<?= htmlspecialchars(theme_setting('hero_headline_en', 'Original Games, Steam Packs & Digital Subscriptions')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">الوصف الترويجي بالعربية</label>
                        <textarea name="hero_subheadline_ar" rows="2" class="form-control"><?= htmlspecialchars(theme_setting('hero_subheadline_ar', 'احصل على أقوى ألعاب PC و PS5 و Xbox وبكجات ستيم الضخمة بأفضل الأسعار بالدينار الجزائري مع تسليم فوري ودفع مريح عبر بريدي موب BaridiMob و CCP!')) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">نص الحقوق أسفل الموقع (Footer)</label>
                        <input type="text" name="footer_text_ar" class="form-control" value="<?= htmlspecialchars(theme_setting('footer_text_ar', 'جميع الحقوق محفوظة © Alpha Gaming Shop 2026')) ?>">
                    </div>
                </div>
            </div>

            <!-- Platforms Showcase Settings -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">🎮 قسم تصفح حسب المنصة (Platforms Showcase)</h3>
                </div>
                <div class="admin-card-body">
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem; background:var(--bg-input); border-radius:10px; margin-bottom:1.25rem;">
                        <div>
                            <div style="font-weight:700; color:#fff; font-size:0.95rem;">إظهار قسم "تصفح حسب المنصة" في الصفحة الرئيسية</div>
                            <div style="font-size:0.8rem; color:var(--text-muted);">عرض بطاقات بصرية كبرى وفخمة للمنصات (Xbox, PlayStation, Steam, EA SPORTS FC, Minecraft, etc.)</div>
                        </div>
                        <label style="position:relative; display:inline-block; width:48px; height:26px;">
                            <input type="hidden" name="show_platforms_section" value="0">
                            <input type="checkbox" name="show_platforms_section" value="1" <?= theme_setting('show_platforms_section', '1') === '1' ? 'checked' : '' ?> style="accent-color:var(--color-primary); width:20px; height:20px; cursor:pointer;">
                        </label>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                            <span>تخصيص المنصات المتقدم عبر JSON (اختياري)</span>
                            <span style="font-size:0.75rem; color:var(--text-muted);">اتركه فارغاً للاعتماد على الـ 12 منصة التلقائية</span>
                        </label>
                        <textarea name="home_platforms_json" rows="4" class="form-control" placeholder='[{"name_ar": "Xbox", "name_en": "Xbox", "url": "brand/xbox", "image": "assets/images/platforms/platform_xbox.webp", "accent_color": "#107c10"}]' style="font-family:monospace; font-size:0.82rem; direction:ltr; text-align:left;"><?= htmlspecialchars(theme_setting('home_platforms_json', '')) ?></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block" style="font-size:1.1rem; font-weight:800;">
                💾 حفظ وتطبيق تصميم المتجر مباشرة
            </button>
        </div>

        <!-- Right: Real-time Live Preview Card -->
        <div style="position:sticky; top:90px;">
            <div class="admin-card" style="box-shadow: 0 10px 30px rgba(0,0,0,0.6); border-color: rgba(0, 210, 255, 0.3);">
                <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3 class="admin-card-title">👁️ معاينة فورية حية</h3>
                    <span style="font-size:0.75rem; color:var(--color-secondary); font-weight:700;">● Live Preview</span>
                </div>
                <div id="theme_preview_box" class="admin-card-body" style="padding:1.25rem; background:<?= theme_setting('bg_main_color', '#080b13') ?>; border-radius:12px; border:1px solid rgba(255,255,255,0.08);">
                    <!-- Mini Header -->
                    <div id="preview_header" style="background:#090e18; border:1px solid rgba(255,255,255,0.08); color:#fff; padding:10px 12px; border-radius:8px; font-size:0.8rem; display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span id="preview_logo_icon" style="color:<?= theme_setting('primary_color', '#00d2ff') ?>; font-size:1.1rem;">🎮</span>
                            <strong style="color:#fff;">ALPHA SHOP</strong>
                        </div>
                        <div id="preview_header_cart" style="background:<?= theme_setting('primary_color', '#00d2ff') ?>; color:#000; font-size:0.7rem; font-weight:800; padding:2px 8px; border-radius:6px;">
                            🛒 3
                        </div>
                    </div>

                    <!-- Mini Hero -->
                    <div id="preview_hero" style="background:linear-gradient(135deg, <?= theme_setting('bg_card_color', '#0f1626') ?>, #070c1b); border:1px solid rgba(0, 210, 255, 0.25); color:#fff; padding:18px 14px; border-radius:10px; text-align:center; font-size:0.8rem; margin-bottom:12px; position:relative; overflow:hidden;">
                        <span id="preview_badge" style="background:rgba(0, 210, 255, 0.15); color:<?= theme_setting('primary_color', '#00d2ff') ?>; border:1px solid rgba(0, 210, 255, 0.3); font-size:0.65rem; font-weight:800; padding:2px 6px; border-radius:999px;">
                            ⚡ ألعاب أصلية 🇩🇿
                        </span>
                        <div style="font-weight:900; font-size:0.95rem; margin:8px 0 4px; color:#fff;">Alpha Gaming Store</div>
                        <div style="font-size:0.7rem; color:#94a3b8; margin-bottom:10px;">تسليم فوري عبر بريدي موب</div>
                        <button type="button" id="preview_btn" style="background:linear-gradient(135deg, <?= theme_setting('primary_color', '#00d2ff') ?>, #0077ff); color:#fff; border:none; padding:6px 14px; border-radius:<?= theme_setting('button_radius', '0.75rem') ?>; font-size:0.75rem; font-weight:800; cursor:pointer;">
                            تصفح الألعاب 🔥
                        </button>
                    </div>

                    <!-- Mini Product Card -->
                    <div id="preview_card" style="background:<?= theme_setting('bg_card_color', '#0f1626') ?>; border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:10px; font-size:0.75rem;">
                        <div style="height:70px; background:#070a12; border-radius:6px; margin-bottom:8px; display:flex; align-items:center; justify-content:center; font-size:1.5rem;">🎮</div>
                        <div style="font-weight:bold; color:#fff;">حساب ستيم +17,000 لعبة PC</div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                            <span id="preview_price" style="color:<?= theme_setting('primary_color', '#00d2ff') ?>; font-weight:900; font-size:0.85rem;">6,000 DA</span>
                            <span style="background:<?= theme_setting('accent_color', '#ffaa00') ?>; color:#000; font-size:0.65rem; font-weight:800; padding:1px 5px; border-radius:4px;">خصم 50%</span>
                        </div>
                    </div>

                    <!-- Interactive Cursor Demo inside preview -->
                    <div style="margin-top:14px; padding:10px; background:rgba(0,0,0,0.4); border:1px dashed rgba(255,255,255,0.1); border-radius:8px; text-align:center;">
                        <div style="font-size:0.75rem; color:#94a3b8; margin-bottom:4px;">تجربة مؤشر الماوس المتوهج:</div>
                        <div id="cursor_preview_target" style="display:inline-block; padding:6px 12px; background:rgba(0, 210, 255, 0.15); border:1px solid rgba(0, 210, 255, 0.3); border-radius:6px; color:#fff; font-size:0.75rem; font-weight:700; cursor:pointer;">
                            حرك الماوس فوقي 🖱️
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function applyPreset(presetName, primary, secondary, accent, bgMain, bgCard) {
    document.querySelectorAll('.preset-card').forEach(c => c.style.borderColor = 'rgba(255,255,255,0.1)');
    event.currentTarget.style.borderColor = primary;

    document.getElementById('theme_primary_color').value = primary;
    document.getElementById('primary_hex').value = primary;

    document.getElementById('theme_secondary_color').value = secondary;
    document.getElementById('secondary_hex').value = secondary;

    document.getElementById('theme_accent_color').value = accent;
    document.getElementById('accent_hex').value = accent;

    document.getElementById('theme_bg_main').value = bgMain;
    document.getElementById('bg_main_hex').value = bgMain;

    document.getElementById('theme_bg_card').value = bgCard;
    document.getElementById('bg_card_hex').value = bgCard;

    document.getElementById('cursor_glow_color_picker').value = primary;
    document.getElementById('cursor_glow_hex').value = primary;

    updateLivePreview();
}

function updateLivePreview() {
    const primary = document.getElementById('theme_primary_color').value;
    const secondary = document.getElementById('theme_secondary_color').value;
    const accent = document.getElementById('theme_accent_color').value;
    const bgMain = document.getElementById('theme_bg_main').value;
    const bgCard = document.getElementById('theme_bg_card').value;

    const box = document.getElementById('theme_preview_box');
    if (box) box.style.background = bgMain;

    const logo = document.getElementById('preview_logo_icon');
    if (logo) logo.style.color = primary;

    const cart = document.getElementById('preview_header_cart');
    if (cart) cart.style.background = primary;

    const btn = document.getElementById('preview_btn');
    if (btn) btn.style.background = `linear-gradient(135deg, ${primary}, #0077ff)`;

    const badge = document.getElementById('preview_badge');
    if (badge) {
        badge.style.color = primary;
        badge.style.borderColor = primary;
    }

    const card = document.getElementById('preview_card');
    if (card) card.style.background = bgCard;

    const price = document.getElementById('preview_price');
    if (price) price.style.color = primary;

    const hero = document.getElementById('preview_hero');
    if (hero) hero.style.background = `linear-gradient(135deg, ${bgCard}, #070c1b)`;
}
</script>
@endsection
