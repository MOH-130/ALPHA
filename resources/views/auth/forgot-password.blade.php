@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 480px;">
    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2.5rem; box-shadow:var(--shadow-lg);">
        <h1 style="font-size:1.75rem; font-weight:900; text-align:center; margin-bottom:0.5rem; color:#fff;">
            <?= __('auth.reset_password') ?>
        </h1>
        <p style="text-align:center; color:var(--text-muted); font-size:0.925rem; margin-bottom:2rem;">
            أدخل بريدك الإلكتروني وسنرسل لك تعليمات استعادة حسابك.
        </p>

        <form action="<?= url('forgot-password') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label"><?= __('auth.email') ?> *</label>
                <input type="email" name="email" class="form-control" placeholder="user@gmail.com" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:1.5rem; font-weight:800;">
                إرسال الرابط ✉️
            </button>
        </form>

        <div style="text-align:center; margin-top:2rem; font-size:0.9rem; color:var(--text-muted);">
            <a href="<?= url('login') ?>" style="color:var(--color-primary); font-weight:700;">&larr; العودة لتسجيل الدخول</a>
        </div>
    </div>
</div>
@endsection
