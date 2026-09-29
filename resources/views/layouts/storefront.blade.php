<!DOCTYPE html>
<html lang="<?= current_locale() ?>" dir="<?= is_rtl() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? (app_setting('site_name', 'Alpha Gaming Shop') . ' | ' . __('messages.site_tagline')) ?></title>
    
    <!-- Meta & SEO Tags -->
    <meta name="description" content="<?= $metaDescription ?? app_setting('site_description', __('messages.site_tagline')) ?>">
    <meta name="keywords" content="<?= $metaKeywords ?? 'Alpha Gaming Shop, Steam Games Algeria, EA FC 27, GTA 6, BaridiMob, PC Games' ?>">
    <link rel="canonical" href="<?= url($_SERVER['REQUEST_URI'] ?? '') ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Assets -->
    <link rel="stylesheet" href="<?= asset('assets/css/storefront.css') ?>">
    <?php if (is_rtl()): ?>
    <link rel="stylesheet" href="<?= asset('assets/css/rtl.css') ?>">
    <?php endif; ?>

    <!-- Dynamic Theme Customizer Styles & Sleek Dark Scrollbars -->
    <style>
        :root {
            --color-primary: <?= theme_setting('primary_color', '#00d2ff') ?>;
            --color-secondary: <?= theme_setting('secondary_color', '#00ff88') ?>;
            --color-accent: <?= theme_setting('accent_color', '#ffaa00') ?>;
            --bg-main: <?= theme_setting('bg_main_color', '#080b13') ?>;
            --bg-card: <?= theme_setting('bg_card_color', '#0f1626') ?>;
            --cursor-glow: <?= theme_setting('cursor_glow_color', '#00d2ff') ?>;
            --btn-radius: <?= theme_setting('button_radius', '0.75rem') ?>;
            --font-family: '<?= theme_setting('font_family', 'Cairo') ?>', system-ui, sans-serif;
        }

        /* Dark Gamer Scrollbar System */
        ::-webkit-scrollbar {
            width: 8px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #080b13;
        }
        ::-webkit-scrollbar-thumb {
            background: #172238;
            border-radius: 4px;
            border: 1px solid rgba(0, 210, 255, 0.2);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-primary);
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: #172238 #080b13;
        }
        .category-strip-inner {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .category-strip-inner::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
    </style>

    <?= $schemaJson ?? '' ?>
</head>
<body>

    <!-- Interactive Gamer Mouse Cursor Elements -->
    <?php if (theme_setting('enable_custom_cursor', '1') === '1'): ?>
        <div class="custom-cursor-dot"></div>
        <div class="custom-cursor-ring"></div>
    <?php endif; ?>

    <!-- Top Info Bar -->
    <div class="top-bar">
        <div class="container top-bar-inner">
            <div class="top-bar-left">
                <span>🎮 <?= is_rtl() ? 'متجر ألعاب الكمبيوتر والكونسول الأول في الجزائر 🇩🇿' : 'Algeria\'s #1 Digital Gaming Store 🇩🇿' ?></span>
                <button type="button" onclick="openWelcomeModal()" style="background:rgba(0, 210, 255, 0.15); color:var(--color-primary); border:1px solid rgba(0, 210, 255, 0.3); border-radius:6px; padding:2px 8px; font-size:0.75rem; font-weight:700; cursor:pointer; font-family:inherit;">
                    ✨ <?= is_rtl() ? 'دليل ومزايا المتجر' : 'Store Guide' ?>
                </button>
            </div>
            <div class="top-bar-right">
                <a href="<?= url('track-order') ?>">📍 <?= __('messages.track_order') ?></a>
                
                <!-- Currency Selector -->
                <div style="display:inline-flex; align-items:center; gap:4px;">
                    <span>💱</span>
                    <select onchange="location.href='<?= url('currency') ?>/' + this.value" style="background:#131c2e; color:#fff; border:1px solid rgba(255,255,255,0.1); border-radius:4px; padding:2px 6px; font-size:0.825rem; cursor:pointer;">
                        <?php foreach (\App\Services\CurrencyService::getAllActive() as $curr): ?>
                            <option value="<?= $curr->code ?>" <?= current_currency()->code === $curr->code ? 'selected' : '' ?> style="background:#0f1626; color:#fff;">
                                <?= $curr->code ?> (<?= is_rtl() ? $curr->symbol_ar : $curr->symbol_en ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Language Selector -->
                <div style="display:inline-flex; align-items:center; gap:4px;">
                    <span>🌐</span>
                    <a href="<?= url('lang/' . (current_locale() === 'ar' ? 'en' : 'ar')) ?>" style="color:#cbd5e1; font-weight:700;">
                        <?= current_locale() === 'ar' ? 'English (LTR)' : 'العربية (RTL)' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- VIP Luxury Announcement Ticker -->
    <div class="vip-ticker-bar">
        <div class="vip-ticker-track">
            <div class="vip-ticker-item">
                <span class="vip-badge-mini">⚡ تسليم فوري</span>
                <span>استلام آلي فوري لبيانات الحسابات وأكواد التفعيل 24/7 عبر واتساب والإيميل</span>
            </div>
            <div class="vip-ticker-item">
                <span class="vip-badge-mini">🇩🇿 دفع محلي</span>
                <span>دعم كامل ومباشر لتطبيق بريدي موب BaridiMob والحساب البريدي CCP</span>
            </div>
            <div class="vip-ticker-item">
                <span class="vip-badge-mini">🛡️ ضمان رسمي</span>
                <span>جميع الألعاب والحسابات مرخصة وأصلية 100% مع ضمان استبدال كامل</span>
            </div>
            <div class="vip-ticker-item">
                <span class="vip-badge-mini">💎 عروض حصرية</span>
                <span>أكثر من +45,000 لعبة Steam وبكجات خارقة تبدأ من 500 دينار فقط!</span>
            </div>
            <!-- Seamless loop duplicate -->
            <div class="vip-ticker-item">
                <span class="vip-badge-mini">⚡ تسليم فوري</span>
                <span>استلام آلي فوري لبيانات الحسابات وأكواد التفعيل 24/7 عبر واتساب والإيميل</span>
            </div>
            <div class="vip-ticker-item">
                <span class="vip-badge-mini">🇩🇿 دفع محلي</span>
                <span>دعم كامل ومباشر لتطبيق بريدي موب BaridiMob والحساب البريدي CCP</span>
            </div>
            <div class="vip-ticker-item">
                <span class="vip-badge-mini">🛡️ ضمان رسمي</span>
                <span>جميع الألعاب والحسابات مرخصة وأصلية 100% مع ضمان استبدال كامل</span>
            </div>
            <div class="vip-ticker-item">
                <span class="vip-badge-mini">💎 عروض حصرية</span>
                <span>أكثر من +45,000 لعبة Steam وبكجات خارقة تبدأ من 500 دينار فقط!</span>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container header-inner">
            <!-- Brand Logo -->
            <a href="<?= url('/') ?>" class="store-logo">
                <div class="logo-badge">🎮</div>
                <div style="display:flex; flex-direction:column;">
                    <span style="font-weight:900; font-size:1.35rem; line-height:1.1; letter-spacing:-0.5px; background: linear-gradient(135deg, #ffffff, #00d2ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?= app_setting('site_name', 'Alpha Gaming Shop') ?></span>
                    <span style="font-size:0.7rem; font-weight:700; color:var(--color-secondary); letter-spacing:0.5px; text-transform:uppercase;">Official Gaming Store 🇩🇿</span>
                </div>
            </a>

            <!-- Live Search Bar -->
            <div class="header-search">
                <form action="<?= url('shop') ?>" method="GET" class="search-form">
                    <input type="text" name="q" class="search-input" placeholder="<?= __('messages.search_placeholder') ?>" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" autocomplete="off">
                    <button type="submit" class="search-btn">🔍</button>
                </form>
                <div class="search-results-dropdown"></div>
            </div>

            <!-- Header Actions -->
            <div class="header-actions">
                <a href="<?= url('wishlist') ?>" class="header-action-btn" title="<?= __('messages.my_wishlist') ?>">
                    <span>❤️</span>
                    <span class="action-label" style="display:none;"><?= __('messages.my_wishlist') ?></span>
                </a>

                <a href="<?= url('compare') ?>" class="header-action-btn" title="<?= __('messages.compare_products') ?>">
                    <span>⚖️</span>
                </a>

                <a href="<?= url('cart') ?>" class="header-action-btn" style="background: linear-gradient(135deg, rgba(0, 210, 255, 0.2), rgba(0, 119, 255, 0.2)); border-color: rgba(0, 210, 255, 0.4); color:#ffffff;">
                    <span>🛒</span>
                    <span style="font-weight:700;"><?= __('messages.cart') ?></span>
                    <span class="badge-count header-cart-badge" style="<?= \App\Services\CartService::count() > 0 ? '' : 'display:none;' ?>"><?= \App\Services\CartService::count() ?></span>
                </a>

                <?php if (auth()->check()): ?>
                    <a href="<?= auth()->isAdmin() ? url('admin/dashboard') : url('account/dashboard') ?>" class="btn btn-outline btn-sm">
                        <span>👤 <?= auth()->user()->name ?></span>
                    </a>
                    <a href="<?= url('logout') ?>" class="btn btn-outline btn-sm" style="color:var(--color-danger); border-color:rgba(239,68,68,0.3);">
                        🚪
                    </a>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="btn btn-outline btn-sm">
                        <?= __('messages.login') ?>
                    </a>
                    <a href="<?= url('register') ?>" class="btn btn-primary btn-sm">
                        <?= __('messages.register') ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Navigation Menu Bar -->
    <nav class="main-nav">
        <div class="container">
            <ul class="nav-list">
                <li><a href="<?= url('/') ?>" class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '') ? 'active' : '' ?>">🏠 <?= __('messages.home') ?></a></li>
                <li><a href="<?= url('shop') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'shop') !== false ? 'active' : '' ?>">🛍️ <?= __('messages.shop') ?></a></li>
                
                <?php 
                $navCategories = \App\Models\Category::where('status', 'active')->where('parent_id', null)->orderBy('sort_order', 'ASC')->limit(6)->get();
                foreach ($navCategories as $navCat): 
                ?>
                    <li><a href="<?= url('category/' . $navCat->slug) ?>" class="nav-link"><?= $navCat->getNameAttribute() ?></a></li>
                <?php endforeach; ?>

                <li><a href="<?= url('shop?deals_only=1') ?>" class="nav-link" style="color:var(--color-accent); font-weight:700;">🔥 <?= __('messages.deals') ?></a></li>
                <li><a href="<?= url('contact') ?>" class="nav-link">✉️ <?= __('messages.contact') ?></a></li>
            </ul>
        </div>
    </nav>

    <!-- Fast Modern Category Ribbon Strip -->
    <div class="category-strip">
        <div class="container">
            <div class="category-strip-inner">
                <a href="<?= url('shop') ?>" class="cat-pill <?= ($_SERVER['REQUEST_URI'] == '/shop') ? 'active' : '' ?>">
                    <span>🔥 جميع الألعاب</span>
                    <span class="cat-pill-count"><?= \App\Models\Product::where('status', 'active')->count() ?></span>
                </a>
                <?php 
                $iconsMap = [
                    'pc' => '💻',
                    'steam' => '🎁',
                    'console' => '⚽',
                    'playstation' => '🎮',
                    'minecraft' => '⛏️',
                    'software' => '⚡',
                    'cards' => '💳'
                ];
                foreach ($navCategories as $cat): 
                    $catIcon = '🎮';
                    foreach ($iconsMap as $k => $ic) {
                        if (stripos($cat->slug, $k) !== false || stripos($cat->name_en, $k) !== false) {
                            $catIcon = $ic;
                            break;
                        }
                    }
                    $isCatActive = strpos($_SERVER['REQUEST_URI'] ?? '', 'category/' . $cat->slug) !== false;
                ?>
                    <a href="<?= url('category/' . $cat->slug) ?>" class="cat-pill <?= $isCatActive ? 'active' : '' ?>">
                        <span><?= $catIcon ?> <?= $cat->getNameAttribute() ?></span>
                        <span class="cat-pill-count"><?= $cat->productsCount() ?></span>
                    </a>
                <?php endforeach; ?>

                <a href="<?= url('shop?deals_only=1') ?>" class="cat-pill" style="border-color:rgba(255, 170, 0, 0.4); color:var(--color-accent);">
                    <span>⚡ تخفيضات اليوم</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="container" style="margin-top: 1.5rem;">
        <?php if ($msg = session()->flash('success')): ?>
            <div style="background:rgba(16, 185, 129, 0.15); color:#34d399; padding:1rem 1.5rem; border-radius:var(--btn-radius); border:1px solid rgba(16, 185, 129, 0.3); margin-bottom:1rem; font-weight:600; display:flex; align-items:center; gap:8px;">
                <span>✅</span> <span><?= $msg ?></span>
            </div>
        <?php endif; ?>

        <?php if ($msg = session()->flash('error')): ?>
            <div style="background:rgba(239, 68, 68, 0.15); color:#f87171; padding:1rem 1.5rem; border-radius:var(--btn-radius); border:1px solid rgba(239, 68, 68, 0.3); margin-bottom:1rem; font-weight:600; display:flex; align-items:center; gap:8px;">
                <span>❌</span> <span><?= $msg ?></span>
            </div>
        <?php endif; ?>

        <?php if ($msg = session()->flash('info')): ?>
            <div style="background:rgba(0, 210, 255, 0.15); color:#38bdf8; padding:1rem 1.5rem; border-radius:var(--btn-radius); border:1px solid rgba(0, 210, 255, 0.3); margin-bottom:1rem; font-weight:600; display:flex; align-items:center; gap:8px;">
                <span>ℹ️</span> <span><?= $msg ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Dynamic Content Slot -->
    <main>
        <?= $__sections['content'] ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="store-logo" style="color:#ffffff; margin-bottom:1rem;">
                        <div class="logo-badge">🎮</div>
                        <span style="font-weight:900; font-size:1.3rem;"><?= app_setting('site_name', 'Alpha Gaming Shop') ?></span>
                    </div>
                    <p style="font-size:0.925rem; line-height:1.7; margin-bottom:1.5rem; color:#94a3b8;">
                        <?= __('messages.site_tagline') ?>
                    </p>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <a href="https://wa.me/213774444444" target="_blank" class="card-action-btn" title="WhatsApp: +213 774 44 44 44">💬</a>
                        <a href="https://www.instagram.com/alpha_gaming_shop/" target="_blank" class="card-action-btn" title="Instagram: @alpha_gaming_shop">📸</a>
                        <a href="https://www.facebook.com/AlphaGamingShop1/" target="_blank" class="card-action-btn" title="Facebook: AlphaGamingShop1">🌐</a>
                        <a href="tel:+213774444444" class="card-action-btn" title="Phone: +213 774 44 44 44">📞</a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-title"><?= __('messages.categories') ?></h4>
                    <ul class="footer-links">
                        <?php foreach ($navCategories as $cat): ?>
                            <li><a href="<?= url('category/' . $cat->slug) ?>"><?= $cat->getNameAttribute() ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-title"><?= __('messages.about_us') ?></h4>
                    <ul class="footer-links">
                        <li><a href="<?= url('page/about-us') ?>"><?= __('messages.about_us') ?></a></li>
                        <li><a href="<?= url('page/privacy-policy') ?>"><?= __('messages.privacy_policy') ?></a></li>
                        <li><a href="<?= url('page/terms-and-conditions') ?>"><?= __('messages.terms_conditions') ?></a></li>
                        <li><a href="<?= url('contact') ?>"><?= __('messages.contact') ?></a></li>
                        <li><a href="<?= url('track-order') ?>"><?= __('messages.track_order') ?></a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-title">طرق الدفع والتسليم بالجزائر 🇩🇿</h4>
                    <p style="font-size:0.875rem; margin-bottom:1rem; color:#94a3b8;">
                        نقبل الدفع عبر تطبيق <strong>بريدي موب BaridiMob</strong>، الحساب البريدي <strong>CCP</strong>، و <strong>PayPal</strong> مع تسليم فوري للأكواد والحسابات 24/7.
                    </p>
                    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;">
                        <span style="background:#131c2e; border:1px solid rgba(255,255,255,0.1); border-radius:6px; padding:4px 10px; font-size:0.8rem; font-weight:700; color:#fff;">💳 BaridiMob</span>
                        <span style="background:#131c2e; border:1px solid rgba(255,255,255,0.1); border-radius:6px; padding:4px 10px; font-size:0.8rem; font-weight:700; color:#fff;">📮 CCP Algérie</span>
                        <span style="background:#131c2e; border:1px solid rgba(255,255,255,0.1); border-radius:6px; padding:4px 10px; font-size:0.8rem; font-weight:700; color:#fff;">🅿️ PayPal</span>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p><?= is_rtl() ? theme_setting('footer_text_ar', 'جميع الحقوق محفوظة © Alpha Gaming Shop 2026') : theme_setting('footer_text_en', 'All Rights Reserved © Alpha Gaming Shop 2026') ?></p>
            </div>
        </div>
    </footer>

    <!-- Luxury VIP Entrance Gateway Modal -->
    <?php if (theme_setting('enable_welcome_modal', '1') === '1'): ?>
    <div id="welcome-modal-overlay" class="welcome-overlay">
        <div class="welcome-modal vip-entrance-modal">
            <button type="button" class="welcome-close-btn" onclick="closeWelcomeModal()" title="إغلاق">&times;</button>
            
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div class="vip-entrance-header-emblem">
                    🎮
                </div>
                <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(0, 210, 255, 0.12); border:1px solid rgba(0, 210, 255, 0.3); border-radius:999px; padding:4px 14px; font-size:0.75rem; font-weight:800; color:var(--color-primary); margin-bottom:0.75rem;">
                    <span>💎 ALPHA LUXURY GAMING GATEWAY</span>
                    <span>•</span>
                    <span style="color:#00ff88;">الجزائر 🇩🇿</span>
                </div>
                <h2 style="font-size: 1.95rem; font-weight: 900; color: #ffffff; letter-spacing: -0.5px; margin-bottom: 0.5rem; background: linear-gradient(135deg, #ffffff 30%, #00d2ff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    مرحباً بك في البوابة الرقمية الفاخرة
                </h2>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; max-width: 540px; margin: 0 auto;">
                    المتجر الجزائري الأول المخصص لأقوى ألعاب PC و PlayStation و Xbox، بكجات Steam الضخمة، واشتراكات الترفيه بالدينار الجزائري مع تسليم فوري مشفر!
                </p>
            </div>

            <!-- 4 Luxury Pillars Grid -->
            <div class="welcome-features-list">
                <div class="vip-portal-card">
                    <span class="vip-portal-icon">⚡</span>
                    <div>
                        <div class="welcome-feat-title" style="color:#00d2ff;">تسليم فوري ذكي</div>
                        <div class="welcome-feat-desc">كود التفعيل أو بيانات الحساب تصلك فوراً على الإيميل والواتساب 24/7.</div>
                    </div>
                </div>

                <div class="vip-portal-card">
                    <span class="vip-portal-icon">🇩🇿</span>
                    <div>
                        <div class="welcome-feat-title" style="color:#00ff88;">دفع محلي فوري وآمن</div>
                        <div class="welcome-feat-desc">دعم كامل لتطبيق بريدي موب BaridiMob، الحساب البريدي CCP، و PayPal.</div>
                    </div>
                </div>

                <div class="vip-portal-card">
                    <span class="vip-portal-icon">🛡️</span>
                    <div>
                        <div class="welcome-feat-title" style="color:#ffd700;">ضمان ذهبي شامل 100%</div>
                        <div class="welcome-feat-desc">حسابات وألعاب أصلية ورسمية مع إمكانية تغيير البيانات وضمان استبدال دائم.</div>
                    </div>
                </div>

                <div class="vip-portal-card">
                    <span class="vip-portal-icon">💎</span>
                    <div>
                        <div class="welcome-feat-title" style="color:#ff77e9;">نادي النخبة VIP</div>
                        <div class="welcome-feat-desc">أكثر من +45,000 لعبة Steam، وبكجات أسطورية تبدأ من 500 DA فقط!</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Group -->
            <div style="margin-top: 1.75rem; display: flex; flex-direction: column; gap: 0.85rem;">
                <div style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:0.75rem;">
                    <button type="button" class="btn btn-luxury-shimmer btn-lg" onclick="playCyberSound('access'); closeWelcomeModal();" style="font-size: 1.05rem; font-weight: 800;">
                        دخول واستكشاف العروض الفاخرة 🔥
                    </button>
                    <?php if (!auth()->check()): ?>
                    <a href="<?= url('login') ?>" class="btn btn-outline btn-lg" onclick="playCyberSound('click');" style="border-color:rgba(0, 210, 255, 0.4); color:#00d2ff; font-weight:800; text-align:center; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <span>👤</span> <span>دخول VIP</span>
                    </a>
                    <?php else: ?>
                    <a href="<?= url('shop') ?>" class="btn btn-outline btn-lg" style="border-color:rgba(0, 255, 136, 0.4); color:#00ff88; font-weight:800; text-align:center; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <span>🛍️</span> <span>كل الألعاب</span>
                    </a>
                    <?php endif; ?>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.825rem; color: var(--text-light); padding: 4px 6px;">
                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                        <input type="checkbox" id="dont-show-welcome-again" onchange="toggleWelcomeDontShow(this.checked)" style="accent-color: var(--color-primary); width:16px; height:16px;">
                        <span>عدم إظهار هذه البوابة مجدداً</span>
                    </label>
                    <a href="https://wa.me/213774444444" target="_blank" style="color: var(--color-secondary); font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration:none;">
                        <span>💬 تواصل مع مستشار VIP (+213 774 44 44 44)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Floating VIP Concierge WhatsApp Button -->
    <a href="https://wa.me/213774444444" target="_blank" class="vip-float-support" title="مستشار كبار الشخصيات VIP عبر واتساب">
        <span class="vip-float-icon">💬</span>
        <span>مستشار VIP مباشر</span>
    </a>

    <!-- Toast Notifications Container -->
    <div class="toast-container"></div>

    <!-- JavaScript Assets -->
    <script src="<?= asset('assets/js/storefront.js') ?>"></script>
</body>
</html>
