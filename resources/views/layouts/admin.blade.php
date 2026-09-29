<!DOCTYPE html>
<html lang="<?= current_locale() ?>" dir="<?= is_rtl() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? __('admin.dashboard') ?> | <?= app_setting('site_name', 'NovaStore Admin') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
    <?php if (is_rtl()): ?>
    <link rel="stylesheet" href="<?= asset('assets/css/rtl.css') ?>">
    <?php endif; ?>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="admin-body">

    <!-- Admin Sidebar Navigation -->
    <aside class="admin-sidebar">
        <div class="admin-brand" style="display:flex; align-items:center; gap:10px; padding:1.25rem 1.5rem;">
            <img src="<?= asset('assets/images/logo.png') ?>" alt="Logo" style="height:36px; width:auto; border-radius:6px; object-fit:contain;">
            <span style="font-weight:900; font-size:1.15rem; color:#fff;"><?= app_setting('site_name', 'Alpha Gaming Shop') ?></span>
            <span style="font-size:0.7rem; background:#00d2ff; color:#000; font-weight:800; padding:2px 6px; border-radius:4px;">ADMIN</span>
        </div>

        <ul class="admin-nav">
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/dashboard') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/dashboard') ?>">📊 <?= __('admin.dashboard') ?></a>
            </li>

            <li class="admin-nav-heading">إدارة المتجر والكتالوج</li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/products') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/products') ?>">📦 <?= __('admin.products') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/categories') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/categories') ?>">🏷️ <?= __('admin.categories') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/brands') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/brands') ?>">🏢 <?= __('admin.brands') ?></a>
            </li>

            <li class="admin-nav-heading">المبيعات والعملاء</li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/orders') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/orders') ?>">🛒 <?= __('admin.orders') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/returns') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/returns') ?>">🔄 <?= __('admin.returns') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/customers') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/customers') ?>">👥 <?= __('admin.customers') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/reviews') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/reviews') ?>">⭐ <?= __('admin.reviews') ?></a>
            </li>

            <li class="admin-nav-heading">التسويق والتخصيص</li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/coupons') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/coupons') ?>">🎟️ <?= __('admin.coupons') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/affiliates') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/affiliates') ?>">🤝 <?= __('admin.affiliates') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/theme') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/theme') ?>">🎨 <?= __('admin.theme_customizer') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/pages') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/pages') ?>">📄 <?= __('admin.pages') ?></a>
            </li>

            <li class="admin-nav-heading">النظام والإعدادات</li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/shipping') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/shipping') ?>">🚚 <?= __('admin.shipping') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/payments') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/payments') ?>">💳 <?= __('admin.payments') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/currencies') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/currencies') ?>">💱 <?= __('admin.currencies') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/settings') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/settings') ?>">⚙️ <?= __('admin.settings') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/visitors') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/visitors') ?>">🟢 <?= __('admin.live_visitors') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/logs') !== false ? 'active' : '' ?>">
                <a href="<?= url('admin/logs') ?>">📜 <?= __('admin.activity_logs') ?></a>
            </li>
            <li class="admin-nav-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/profile') !== false ? 'active' : '' ?>" style="margin-top:4px;">
                <a href="<?= url('admin/profile') ?>" style="color:#00d2ff; font-weight:700;">🔐 أمان حساب المشرف</a>
            </li>
        </ul>

        <div style="padding:1rem; border-top:1px solid rgba(255,255,255,0.08);">
            <a href="<?= url('/') ?>" target="_blank" style="display:flex; align-items:center; gap:8px; color:#94a3b8; font-size:0.875rem; font-weight:600;">
                <span>🌐</span> <span>زيارة واجهة المتجر</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Admin Header -->
        <header class="admin-header">
            <div>
                <h3 style="margin:0; font-size:1.15rem; font-weight:700;"><?= $title ?? __('admin.dashboard') ?></h3>
            </div>

            <div style="display:flex; align-items:center; gap:1.25rem;">
                <div style="display:flex; align-items:center; gap:6px; font-size:0.875rem; background:#f0fdf4; color:#166534; padding:0.35rem 0.75rem; border-radius:9999px; font-weight:700;">
                    <span style="width:8px; height:8px; background:#16a34a; border-radius:50%; display:inline-block;"></span>
                    <span><?= \App\Services\VisitorTrackerService::getActiveOnlineCount() ?> زائر نشط الآن</span>
                </div>

                <div style="display:flex; align-items:center; gap:8px;">
                    <a href="<?= url('admin/profile') ?>" title="إدارة حساب المشرف وكلمة المرور" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; font-size:0.9rem; color:#fff; text-decoration:none; padding:0.4rem 0.85rem; background:#131c2e; border:1px solid rgba(0, 210, 255, 0.4); border-radius:8px; transition:all 0.2s ease;">
                        <span>👤</span>
                        <span><?= auth()->user() ? auth()->user()->name : 'Admin' ?></span>
                        <span style="font-size:0.68rem; background:linear-gradient(135deg, #00d2ff, #0077ff); color:#000; font-weight:900; padding:1px 6px; border-radius:4px;">PRO</span>
                    </a>
                    <a href="<?= url('logout') ?>" style="color:#ef4444; font-size:0.9rem; font-weight:600; padding:0.4rem 0.8rem; border:1px solid #fecaca; border-radius:6px; background:#fef2f2; text-decoration:none;">
                        خروج 🚪
                    </a>
                </div>
            </div>
        </header>

        <!-- Admin Content -->
        <div class="admin-content">
            <!-- Flash Notification Alerts -->
            <?php if ($msg = session()->flash('success')): ?>
                <div style="background:#f0fdf4; color:#166534; padding:1rem 1.5rem; border-radius:0.5rem; border:1px solid #bbf7d0; margin-bottom:1.5rem; font-weight:600; display:flex; align-items:center; gap:8px;">
                    <span>✅</span> <span><?= $msg ?></span>
                </div>
            <?php endif; ?>

            <?php if ($msg = session()->flash('error')): ?>
                <div style="background:#fef2f2; color:#991b1b; padding:1rem 1.5rem; border-radius:0.5rem; border:1px solid #fecaca; margin-bottom:1.5rem; font-weight:600; display:flex; align-items:center; gap:8px;">
                    <span>❌</span> <span><?= $msg ?></span>
                </div>
            <?php endif; ?>

            <?= $__sections['content'] ?? '' ?>
        </div>
    </div>

    <!-- Admin JavaScript Assets -->
    <script src="<?= asset('assets/js/admin.js') ?>"></script>
</body>
</html>
