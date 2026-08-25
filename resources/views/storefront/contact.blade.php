@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <div style="max-width:950px; margin:0 auto; display:grid; grid-template-columns: 1fr 1.1fr; gap:3rem; background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:3rem; box-shadow:var(--shadow-lg);">
        
        <!-- Store Contact Details -->
        <div>
            <h1 style="font-size:1.85rem; font-weight:900; margin-bottom:1rem; color:#fff;">
                💬 <?= __('messages.contact') ?>
            </h1>
            <p style="color:var(--text-muted); line-height:1.7; margin-bottom:2rem;">
                يسعدنا تلقي استفساراتكم وطلباتكم، وفريق الدعم الفني لمتجر Alpha Gaming Shop متواجد 24/7 لخدمتكم ومساعدتكم في تفعيل الألعاب والحسابات.
            </p>

            <div style="display:flex; flex-direction:column; gap:1.5rem; font-size:0.95rem;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:44px; height:44px; background:var(--bg-input); border:1px solid var(--border-color); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">🇩🇿</div>
                    <div>
                        <strong style="display:block; color:#fff;">الموقع والبلد:</strong>
                        <span style="color:var(--color-primary); font-weight:700;">الجزائر العاصمة، الجزائر (Algeria)</span>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:44px; height:44px; background:var(--bg-input); border:1px solid var(--border-color); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">📞</div>
                    <div>
                        <strong style="display:block; color:#fff;">الهاتف وخدمة العملاء:</strong>
                        <a href="tel:+213774444444" style="color:#fff; font-weight:700; direction:ltr; display:inline-block;">+213 774 44 44 44</a>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:44px; height:44px; background:var(--bg-input); border:1px solid var(--border-color); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">💬</div>
                    <div>
                        <strong style="display:block; color:#fff;">واتساب المباشر:</strong>
                        <a href="https://wa.me/213774444444" target="_blank" style="color:var(--color-secondary); font-weight:800; direction:ltr; display:inline-block;">+213 774 44 44 44 ⚡</a>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:44px; height:44px; background:var(--bg-input); border:1px solid var(--border-color); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">📸</div>
                    <div>
                        <strong style="display:block; color:#fff;">انستغرام الرسمي:</strong>
                        <a href="https://www.instagram.com/alpha_gaming_shop/" target="_blank" style="color:var(--color-primary); font-weight:700;">@alpha_gaming_shop</a>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:44px; height:44px; background:var(--bg-input); border:1px solid var(--border-color); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">🌐</div>
                    <div>
                        <strong style="display:block; color:#fff;">صفحة الفيسبوك:</strong>
                        <a href="https://www.facebook.com/AlphaGamingShop1/" target="_blank" style="color:var(--color-primary); font-weight:700;">AlphaGamingShop1</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div style="background:var(--bg-input); border:1px solid var(--border-color); border-radius:12px; padding:2rem;">
            <h3 style="font-size:1.2rem; font-weight:800; color:#fff; margin-bottom:1.25rem;">أرسل رسالة مباشرة</h3>
            <form action="<?= url('contact') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label"><?= __('messages.full_name') ?> *</label>
                    <input type="text" name="name" class="form-control" required placeholder="الاسم الكامل">
                </div>
                <div class="form-group">
                    <label class="form-label"><?= __('messages.email_address') ?> *</label>
                    <input type="email" name="email" class="form-control" required placeholder="your-email@gmail.com">
                </div>
                <div class="form-group">
                    <label class="form-label">موضوع الرسالة *</label>
                    <input type="text" name="subject" class="form-control" placeholder="استفسار عن لعبة، طلب بكج، مساعدة بالتفعيل..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">تفاصيل الرسالة *</label>
                    <textarea name="message" rows="4" class="form-control" placeholder="اكتب استفسارك هنا..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg" style="font-weight:800;">
                    ✉️ <?= __('messages.submit') ?>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
