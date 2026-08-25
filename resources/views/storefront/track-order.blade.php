@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem; max-width: 750px;">
    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2.5rem; box-shadow:var(--shadow-lg);">
        <h1 style="font-size:1.75rem; font-weight:800; text-align:center; margin-bottom:0.5rem; color:#fff;">
            📍 <?= __('messages.track_order') ?>
        </h1>
        <p style="text-align:center; color:var(--text-muted); margin-bottom:2rem;">
            أدخل رقم الطلب والبريد الإلكتروني لمتابعة حالة استلام بيانات حسابك والتفعيل مباشرة.
        </p>

        <form action="<?= url('track-order') ?>" method="GET" style="margin-bottom:2.5rem;">
            <div class="form-group">
                <label class="form-label"><?= __('messages.order_number') ?> *</label>
                <input type="text" name="order_number" class="form-control" placeholder="ORD-20260818-XXXXX" value="<?= htmlspecialchars($orderNumber ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('messages.email_address') ?></label>
                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" style="font-weight:800;">
                🔍 تتبع حالة الطلب
            </button>
        </form>

        <?php if ($order): ?>
            <div style="background:var(--bg-input); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; border-bottom:1px solid var(--border-subtle); padding-bottom:1rem;">
                    <div>
                        <span style="font-size:0.85rem; color:var(--text-muted);">رقم الطلب:</span>
                        <h3 style="font-size:1.25rem; font-weight:800; color:var(--color-primary);"><?= $order->order_number ?></h3>
                    </div>
                    <span class="status-pill <?= $order->getStatusBadgeClass() ?>"><?= strtoupper($order->status) ?></span>
                </div>

                <!-- Timeline Stages -->
                <div style="display:flex; justify-content:space-between; position:relative; margin:2.5rem 0 2rem;">
                    <div style="text-align:center; flex:1;">
                        <div style="width:36px; height:36px; border-radius:50%; background:var(--color-primary); color:#000; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-weight:800;">1</div>
                        <div style="font-size:0.85rem; font-weight:700; color:#fff;">تم تسجيل الطلب</div>
                    </div>
                    <div style="text-align:center; flex:1;">
                        <div style="width:36px; height:36px; border-radius:50%; background:<?= in_array($order->status, ['processing', 'paid', 'shipped', 'completed']) ? 'var(--color-primary)' : 'rgba(255,255,255,0.1)' ?>; color:<?= in_array($order->status, ['processing', 'paid', 'shipped', 'completed']) ? '#000' : '#64748b' ?>; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-weight:800;">2</div>
                        <div style="font-size:0.85rem; font-weight:700; color:#cbd5e1;">تأكيد الدفع</div>
                    </div>
                    <div style="text-align:center; flex:1;">
                        <div style="width:36px; height:36px; border-radius:50%; background:<?= in_array($order->status, ['shipped', 'completed']) ? 'var(--color-primary)' : 'rgba(255,255,255,0.1)' ?>; color:<?= in_array($order->status, ['shipped', 'completed']) ? '#000' : '#64748b' ?>; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-weight:800;">3</div>
                        <div style="font-size:0.85rem; font-weight:700; color:#cbd5e1;">تجهيز الكود / الحساب</div>
                    </div>
                    <div style="text-align:center; flex:1;">
                        <div style="width:36px; height:36px; border-radius:50%; background:<?= $order->status === 'completed' ? 'var(--color-secondary)' : 'rgba(255,255,255,0.1)' ?>; color:<?= $order->status === 'completed' ? '#000' : '#64748b' ?>; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-weight:800;">4</div>
                        <div style="font-size:0.85rem; font-weight:700; color:#cbd5e1;">تم التسليم بنجاح</div>
                    </div>
                </div>

                <div style="font-size:0.9rem; color:var(--text-muted); border-top:1px solid var(--border-subtle); padding-top:1rem;">
                    <div>طريقة التسليم: <strong style="color:#fff;"><?= $order->shipping_method ?></strong></div>
                    <?php if ($order->tracking_number): ?>
                        <div>كود / بوليصة المتابعة: <strong style="color:var(--color-primary);"><?= $order->tracking_number ?></strong></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
@endsection
