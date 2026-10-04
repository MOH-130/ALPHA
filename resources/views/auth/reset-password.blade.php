@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 480px;">
    <div class="auth-glass-card" style="background: rgba(13, 20, 36, 0.95); backdrop-filter: blur(25px); border: 1px solid rgba(0, 210, 255, 0.4); border-radius: 1.5rem; padding: 2.75rem 2.25rem; box-shadow: 0 25px 60px rgba(0,0,0,0.85), 0 0 35px rgba(0, 210, 255, 0.2); position: relative; overflow: hidden;">
        <!-- Top Cyber Glow Line -->
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #00d2ff, #00ff88, #ffaa00);"></div>

        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 68px; height: 68px; background: linear-gradient(135deg, rgba(0, 210, 255, 0.15), rgba(0, 255, 136, 0.15)); border: 1px solid rgba(0, 255, 136, 0.4); border-radius: 18px; font-size: 2.2rem; margin-bottom: 1rem;">
                🔐
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 900; color: #ffffff; margin: 0 0 0.5rem;">
                تعيين كلمة المرور الجديدة
            </h1>
            <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">
                تم التحقق من هويتك بنجاح عبر رمز OTP. يرجى إدخال كلمة المرور الجديدة لحسابك:
            </p>
        </div>

        <form action="<?= url('reset-password') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="color: #cbd5e1; font-weight: 700; margin-bottom: 0.5rem; display: block;">
                    كلمة المرور الجديدة *
                </label>
                <div class="cyber-input-wrap" style="position: relative;">
                    <input type="password" name="password" id="new-password" class="form-control" placeholder="•••••••• (6 أحرف أو أرقام على الأقل)" required minlength="6" style="width: 100%; height: 46px; background: #090e1c; border: 1px solid rgba(0, 210, 255, 0.3); border-radius: 10px; color: #fff; padding: 0 14px; font-family: inherit;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.75rem;">
                <label class="form-label" style="color: #cbd5e1; font-weight: 700; margin-bottom: 0.5rem; display: block;">
                    تأكيد كلمة المرور الجديدة *
                </label>
                <div class="cyber-input-wrap" style="position: relative;">
                    <input type="password" name="password_confirmation" id="confirm-password" class="form-control" placeholder="••••••••" required minlength="6" style="width: 100%; height: 46px; background: #090e1c; border: 1px solid rgba(0, 210, 255, 0.3); border-radius: 10px; color: #fff; padding: 0 14px; font-family: inherit;">
                </div>
            </div>

            <button type="submit" class="btn btn-luxury-shimmer btn-block btn-lg" style="width: 100%; height: 48px; font-weight: 800; font-size: 1rem; cursor: pointer;">
                💾 حفظ كلمة المرور والدخول
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.75rem;">
            <a href="<?= url('login') ?>" style="color: #64748b; font-size: 0.85rem; text-decoration: none;">
                &larr; <?= __('auth.back_to_login') ?? 'العودة لتسجيل الدخول' ?>
            </a>
        </div>
    </div>
</div>
@endsection
