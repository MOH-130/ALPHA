<!DOCTYPE html>
<html lang="<?= current_locale() ?>" dir="<?= is_rtl() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('auth.admin_login') ?> | <?= app_setting('site_name', 'Alpha Gaming Shop') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
    <?php if (is_rtl()): ?>
    <link rel="stylesheet" href="<?= asset('assets/css/rtl.css') ?>">
    <?php endif; ?>
</head>
<body style="background:#080b13; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1.5rem; font-family:'Cairo', sans-serif;">
    <div style="background:#0f1626; border:1px solid rgba(0, 210, 255, 0.25); border-radius:1.25rem; padding:3rem 2.5rem; max-width:440px; width:100%; box-shadow:0 25px 50px rgba(0,0,0,0.8), 0 0 30px rgba(0, 210, 255, 0.15);">
        <div style="text-align:center; margin-bottom:2rem;">
            <div style="display:inline-flex; align-items:center; justify-content:center; width:60px; height:60px; background:rgba(0, 210, 255, 0.15); border:1px solid rgba(0, 210, 255, 0.3); color:#00d2ff; font-size:2rem; border-radius:14px; margin-bottom:1rem; box-shadow: 0 0 20px rgba(0, 210, 255, 0.2);">🎮</div>
            <h1 style="font-size:1.6rem; font-weight:900; color:#ffffff; margin:0 0 0.5rem;"><?= __('auth.admin_login') ?></h1>
            <p style="color:#94a3b8; font-size:0.875rem; margin:0;">بوابة إدارة متجر Alpha Gaming Shop 🇩🇿</p>
        </div>

        <?php if ($msg = session()->flash('error')): ?>
            <div style="background:rgba(239, 68, 68, 0.15); color:#f87171; padding:0.75rem 1rem; border-radius:8px; font-size:0.875rem; margin-bottom:1.5rem; border:1px solid rgba(239, 68, 68, 0.3);">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <div style="background:#090e1a; border:1px solid rgba(255,255,255,0.08); border-radius:8px; padding:0.85rem 1rem; font-size:0.825rem; margin-bottom:1.5rem; color:#cbd5e1;">
            <strong style="color:#00d2ff;">بيانات دخول الأدمن:</strong><br>
            البريد: <code style="color:#fff;">admin@store.com</code><br>
            كلمة المرور: <code style="color:#00ff88;">password123</code>
        </div>

        <form action="<?= url('admin/login') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label class="form-label" style="display:block; font-size:0.875rem; font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;"><?= __('auth.email') ?> *</label>
                <input type="email" name="email" value="admin@store.com" required style="width:100%; padding:0.75rem 1rem; background:#131c2e; border:1px solid rgba(255,255,255,0.1); border-radius:8px; color:#fff; font-family:inherit; outline:none;">
            </div>

            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label" style="display:block; font-size:0.875rem; font-weight:700; color:#e2e8f0; margin-bottom:0.4rem;"><?= __('auth.password') ?> *</label>
                <input type="password" name="password" value="password123" required style="width:100%; padding:0.75rem 1rem; background:#131c2e; border:1px solid rgba(255,255,255,0.1); border-radius:8px; color:#fff; font-family:inherit; outline:none;">
            </div>

            <button type="submit" style="background:linear-gradient(135deg, #00d2ff, #0077ff); color:#000; border:none; padding:0.85rem; border-radius:8px; font-weight:900; width:100%; cursor:pointer; font-family:inherit; font-size:1rem; box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);">
                دخول لوحة التحكم 🚀
            </button>
        </form>

        <div style="text-align:center; margin-top:1.5rem;">
            <a href="<?= url('/') ?>" style="color:#94a3b8; font-size:0.85rem; text-decoration:none;">&larr; العودة لواجهة المتجر</a>
        </div>
    </div>
</body>
</html>
