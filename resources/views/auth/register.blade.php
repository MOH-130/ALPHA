@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 500px;">
    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2.5rem; box-shadow:var(--shadow-lg);">
        <h1 style="font-size:1.75rem; font-weight:900; text-align:center; margin-bottom:0.5rem; color:#fff;">
            <?= __('auth.register') ?>
        </h1>
        <p style="text-align:center; color:var(--text-muted); font-size:0.925rem; margin-bottom:2rem;">
            انضم إلى مجتمع اللاعبين في متجر Alpha Gaming Shop واستلم ألعابك فوراً.
        </p>

        <form action="<?= url('register') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label"><?= __('messages.full_name') ?> *</label>
                <input type="text" name="name" class="form-control" placeholder="الاسم واللقب الكامل" value="<?= htmlspecialchars(old('name')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label"><?= __('auth.email') ?> *</label>
                <input type="email" name="email" class="form-control" placeholder="user@gmail.com" value="<?= htmlspecialchars(old('email')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label"><?= __('messages.phone_number') ?> (واتساب) *</label>
                <input type="tel" name="phone" class="form-control" placeholder="+213 774 44 44 44" value="<?= htmlspecialchars(old('phone')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label"><?= __('auth.password') ?> *</label>
                <input type="password" name="password" class="form-control" placeholder="•••••••• (6 أحرف على الأقل)" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:1.5rem; font-weight:800;">
                <?= __('auth.register') ?> 🚀
            </button>
        </form>

        <div style="text-align:center; margin-top:2rem; font-size:0.9rem; color:var(--text-muted); border-top:1px solid var(--border-subtle); padding-top:1.5rem;">
            <?= __('auth.already_have_account') ?> 
            <a href="<?= url('login') ?>" style="color:var(--color-primary); font-weight:700;"><?= __('auth.login') ?></a>
        </div>
    </div>
</div>
@endsection
