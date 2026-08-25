@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <div style="display:grid; grid-template-columns: 260px 1fr; gap:2.5rem; align-items:start;">
        
        <!-- Sidebar -->
        <aside style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem;">
            <ul style="list-style:none; display:flex; flex-direction:column; gap:6px;">
                <li><a href="<?= url('account/dashboard') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">📊 <?= __('messages.dashboard') ?></a></li>
                <li><a href="<?= url('account/orders') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🛒 <?= __('messages.my_orders') ?></a></li>
                <li><a href="<?= url('wishlist') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">❤️ <?= __('messages.my_wishlist') ?></a></li>
                <li><a href="<?= url('account/returns') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🔄 <?= __('messages.returns_refunds') ?></a></li>
                <li><a href="<?= url('account/affiliate') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🤝 <?= __('messages.affiliate_program') ?></a></li>
                <li><a href="<?= url('account/profile') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start; background:rgba(0, 210, 255, 0.15); color:var(--color-primary); border-color:var(--color-primary);">⚙️ <?= __('messages.profile_settings') ?></a></li>
            </ul>
        </aside>

        <!-- Profile Form -->
        <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem;">
            <h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem; color:#fff;">
                ⚙️ <?= __('messages.profile_settings') ?>
            </h1>

            <form action="<?= url('account/profile') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><?= __('messages.full_name') ?> *</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user->name) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('messages.email_address') ?> *</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><?= __('messages.phone_number') ?> (واتساب) *</label>
                        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user->phone) ?>" placeholder="+213 774 44 44 44" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">المدينة / الولاية (الجزائر 🇩🇿)</label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user->city ?: 'الجزائر العاصمة') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= __('messages.delivery_address') ?></label>
                    <textarea name="address" rows="2" class="form-control" placeholder="اسم المستخدم في ستيم أو ملاحظات التسليم..."><?= htmlspecialchars($user->address) ?></textarea>
                </div>

                <div style="border-top:1px solid var(--border-subtle); padding-top:1.5rem; margin-top:1.5rem;">
                    <h3 style="font-size:1.1rem; font-weight:800; margin-bottom:1rem; color:#fff;"><?= __('messages.change_password') ?></h3>
                    <div class="form-group">
                        <label class="form-label">كلمة المرور الجديدة (اتركها فارغة إذا كنت لا ترغب بالتغيير)</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="margin-top:1rem; font-weight:800;">
                    💾 <?= __('messages.save') ?> التغييرات
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
