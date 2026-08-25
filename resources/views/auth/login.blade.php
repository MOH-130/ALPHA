@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 480px;">
    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2.5rem; box-shadow:var(--shadow-lg);">
        <h1 style="font-size:1.75rem; font-weight:900; text-align:center; margin-bottom:0.5rem; color:#fff;">
            <?= __('auth.login') ?>
        </h1>
        <p style="text-align:center; color:var(--text-muted); font-size:0.925rem; margin-bottom:2rem;">
            <?= __('messages.site_tagline') ?>
        </p>

        <!-- Demo Credentials Helper -->
        <div style="background:rgba(0, 210, 255, 0.1); border:1px solid rgba(0, 210, 255, 0.25); border-radius:8px; padding:0.75rem 1rem; font-size:0.825rem; margin-bottom:1.5rem; color:#cbd5e1;">
            <div><strong>حساب عميل تجريبي:</strong> <code style="color:var(--color-primary);">customer@store.com</code> / <code style="color:var(--color-secondary);">password123</code></div>
            <div style="margin-top:4px;"><strong>حساب إدارة تجريبي:</strong> <code style="color:var(--color-primary);">admin@store.com</code> / <code style="color:var(--color-secondary);">password123</code></div>
        </div>

        <form action="<?= url('login') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label"><?= __('auth.email') ?> *</label>
                <input type="email" name="email" class="form-control" placeholder="user@gmail.com" value="<?= htmlspecialchars(old('email')) ?>" required>
            </div>

            <div class="form-group">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.4rem;">
                    <label class="form-label" style="margin-bottom:0;"><?= __('auth.password') ?> *</label>
                    <a href="<?= url('forgot-password') ?>" style="font-size:0.8rem; color:var(--color-primary);"><?= __('auth.forgot_password') ?></a>
                </div>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; font-size:0.875rem; cursor:pointer; color:#cbd5e1;">
                    <input type="checkbox" name="remember" value="1" style="accent-color:var(--color-primary);">
                    <span><?= __('auth.remember_me') ?></span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:1.5rem; font-weight:800;">
                <?= __('auth.login') ?> 🚀
            </button>
        </form>

        <div style="text-align:center; margin-top:2rem; font-size:0.9rem; color:var(--text-muted); border-top:1px solid var(--border-subtle); padding-top:1.5rem;">
            <?= __('auth.dont_have_account') ?> 
            <a href="<?= url('register') ?>" style="color:var(--color-primary); font-weight:700;"><?= __('auth.register') ?></a>
        </div>
    </div>
</div>
@endsection
