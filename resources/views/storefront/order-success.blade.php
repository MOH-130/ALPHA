@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 800px;">
    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:3rem 2.5rem; text-align:center; box-shadow:var(--shadow-lg);">
        
        <div style="width:72px; height:72px; background:rgba(0, 255, 136, 0.15); color:var(--color-secondary); border:1px solid rgba(0, 255, 136, 0.3); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2.5rem; margin:0 auto 1.5rem; box-shadow: 0 0 25px rgba(0, 255, 136, 0.2);">
            ✓
        </div>

        <h1 style="font-size:2rem; font-weight:900; color:#fff; margin-bottom:0.75rem;">
            <?= __('messages.order_placed_successfully') ?> 🎮
        </h1>
        <p style="color:var(--text-muted); font-size:1.05rem; margin-bottom:2rem;">
            تم تسجيل طلبك بنجاح! سيتم إرسال بيانات حسابك وكود التفعيل عبر واتساب والإيميل (<strong><?= htmlspecialchars($order->customer_email) ?></strong>).
        </p>

        <!-- Order Number & Highlights Card -->
        <div style="background:var(--bg-input); border:1px solid var(--border-color); border-radius:var(--btn-radius); padding:1.5rem; margin-bottom:2.5rem; text-align:right;">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:1.25rem;">
                <div>
                    <span style="font-size:0.8rem; color:var(--text-muted); display:block;"><?= __('messages.order_number') ?></span>
                    <strong style="font-size:1.1rem; color:var(--color-primary);"><?= $order->order_number ?></strong>
                </div>
                <div>
                    <span style="font-size:0.8rem; color:var(--text-muted); display:block;"><?= __('messages.order_date') ?></span>
                    <strong style="color:#fff;"><?= date('Y-m-d H:i', strtotime($order->created_at)) ?></strong>
                </div>
                <div>
                    <span style="font-size:0.8rem; color:var(--text-muted); display:block;"><?= __('messages.total') ?></span>
                    <strong style="font-size:1.1rem; color:var(--color-primary);"><?= format_price($order->total_amount, $order->currency_code) ?></strong>
                </div>
                <div>
                    <span style="font-size:0.8rem; color:var(--text-muted); display:block;"><?= __('messages.order_status') ?></span>
                    <span class="status-pill <?= $order->getStatusBadgeClass() ?>"><?= strtoupper($order->status) ?></span>
                </div>
            </div>
        </div>

        <!-- Ordered Items Summary -->
        <div style="text-align:right; margin-bottom:2.5rem;">
            <h3 style="font-size:1.15rem; font-weight:800; margin-bottom:1rem; border-bottom:1px solid var(--border-subtle); padding-bottom:0.5rem; color:#fff;">
                الألعاب والمنتجات المطلوبة
            </h3>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <?php foreach ($order->items() as $item): ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-subtle); padding-bottom:10px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <img src="<?= asset('uploads/products/' . ($item->product_image ?: 'default-product.png')) ?>" style="width:48px; height:48px; object-fit:cover; border-radius:6px; background:#070a12;">
                            <div>
                                <div style="font-weight:700; font-size:0.95rem; color:#fff;"><?= htmlspecialchars($item->product_name) ?></div>
                                <div style="font-size:0.8rem; color:var(--text-muted);">الكمية: <?= $item->quantity ?> × <?= format_price($item->unit_price) ?></div>
                            </div>
                        </div>
                        <span style="font-weight:800; color:#fff;"><?= format_price($item->subtotal) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Direct WhatsApp confirmation banner -->
        <div style="background:rgba(0, 210, 255, 0.1); border:1px dashed var(--color-primary); border-radius:10px; padding:1.25rem; margin-bottom:2rem; text-align:center;">
            <div style="font-weight:700; color:#fff; margin-bottom:6px;">لتسريع استلام طلبك، أرسل رقم الطلب عبر واتساب:</div>
            <a href="https://wa.me/213774444444?text=<?= urlencode('مرحباً، قمت بطلب رقم: ' . $order->order_number) ?>" target="_blank" class="btn btn-primary" style="background:#25d366; border-color:#25d366; color:#fff; font-weight:800; display:inline-flex; align-items:center; gap:6px;">
                <span>💬 إرسال عبر واتساب (+213 774 44 44 44)</span>
            </a>
        </div>

        <!-- Action Buttons -->
        <div style="display:flex; justify-content:center; gap:1rem; flex-wrap:wrap;">
            <a href="<?= url('track-order?order_number=' . $order->order_number . '&email=' . urlencode($order->customer_email)) ?>" class="btn btn-primary">
                📍 <?= __('messages.track_order_btn') ?>
            </a>
            <a href="<?= url('shop') ?>" class="btn btn-outline">
                🛍️ <?= __('messages.continue_shopping') ?>
            </a>
        </div>
    </div>
</div>
@endsection
