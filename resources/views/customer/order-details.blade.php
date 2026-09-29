@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem; max-width: 900px;">
    <div style="margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center;">
        <a href="<?= url('account/orders') ?>" class="btn btn-outline btn-sm">&larr; <?= __('messages.back') ?> لقائمة الطلبات</a>
        <button onclick="window.print();" class="btn btn-primary btn-sm">🖨️ <?= __('messages.print_invoice') ?></button>
    </div>

    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2.5rem; box-shadow:var(--shadow-lg);">
        <!-- Invoice Header -->
        <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid var(--border-subtle); padding-bottom:1.5rem; margin-bottom:2rem;">
            <div>
                <h1 style="font-size:1.6rem; font-weight:900; color:var(--color-primary); margin-bottom:0.25rem;">
                    فاتورة طلب رقم: <?= $order->order_number ?>
                </h1>
                <div style="font-size:0.875rem; color:var(--text-muted);">
                    تاريخ الإنشاء: <?= date('Y-m-d H:i', strtotime($order->created_at)) ?>
                </div>
            </div>
            <div style="text-align:left;">
                <span class="status-pill <?= $order->getStatusBadgeClass() ?>"><?= strtoupper($order->status) ?></span>
                <div style="margin-top:4px; font-size:0.85rem; color:var(--text-muted);">الدفع: <?= strtoupper($order->payment_status) ?></div>
            </div>
        </div>

        <!-- Customer & Delivery Details -->
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem; margin-bottom:2rem; background:var(--bg-input); padding:1.25rem; border-radius:var(--btn-radius); border:1px solid var(--border-color);">
            <div>
                <strong style="display:block; font-size:0.95rem; margin-bottom:6px; color:#fff;">بيانات العميل:</strong>
                <div style="font-size:0.875rem; color:#cbd5e1; line-height:1.6;">
                    الاسم: <?= htmlspecialchars($order->customer_name) ?><br>
                    البريد: <?= htmlspecialchars($order->customer_email) ?><br>
                    الجوال: <?= htmlspecialchars($order->customer_phone) ?>
                </div>
            </div>
            <div>
                <strong style="display:block; font-size:0.95rem; margin-bottom:6px; color:#fff;">طريقة الدفع والتسليم:</strong>
                <div style="font-size:0.875rem; color:#cbd5e1; line-height:1.6;">
                    المدينة: <?= htmlspecialchars($order->shipping_city) ?><br>
                    طريقة التسليم: <?= htmlspecialchars($order->shipping_method) ?><br>
                    طريقة الدفع: <?= strtoupper($order->payment_method) ?> (Algeria 🇩🇿)
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive">
            <table style="width:100%; border-collapse:collapse; text-align:inherit; font-size:0.925rem; margin-bottom:1rem;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border-subtle); color:var(--text-muted); font-size:0.85rem;">
                        <th style="padding:0.75rem 0.5rem;"><?= __('messages.product') ?></th>
                        <th style="padding:0.75rem 0.5rem; text-align:center;"><?= __('messages.price') ?></th>
                        <th style="padding:0.75rem 0.5rem; text-align:center;"><?= __('messages.quantity') ?></th>
                        <th style="padding:0.75rem 0.5rem; text-align:center;"><?= __('messages.subtotal') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr style="border-bottom:1px solid var(--border-subtle);">
                            <td style="padding:1rem 0.5rem;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="<?= asset('uploads/products/' . ($item->product_image ?: 'default-product.png')) ?>" style="width:44px; height:44px; object-fit:cover; border-radius:6px; background:#070a12;">
                                    <div>
                                        <strong style="display:block; color:#fff;"><?= htmlspecialchars($item->product_name) ?></strong>
                                        <span style="font-size:0.75rem; color:var(--text-muted);">SKU: <?= $item->sku ?></span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:1rem 0.5rem; text-align:center; color:#fff;"><?= format_price($item->unit_price, $order->currency_code) ?></td>
                            <td style="padding:1rem 0.5rem; text-align:center; font-weight:700; color:#fff;"><?= $item->quantity ?></td>
                            <td style="padding:1rem 0.5rem; text-align:center; font-weight:800; color:var(--color-primary);"><?= format_price($item->subtotal, $order->currency_code) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals Summary -->
        <div style="max-width:320px; margin-right:auto; display:flex; flex-direction:column; gap:8px; font-size:0.95rem; border-top:1px solid var(--border-color); padding-top:1rem;">
            <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-muted);"><?= __('messages.subtotal') ?>:</span>
                <span style="color:#fff; font-weight:700;"><?= format_price($order->subtotal, $order->currency_code) ?></span>
            </div>
            <?php if ($order->discount_amount > 0): ?>
                <div style="display:flex; justify-content:space-between; color:var(--color-secondary);">
                    <span><?= __('messages.discount') ?>:</span>
                    <span>-<?= format_price($order->discount_amount, $order->currency_code) ?></span>
                </div>
            <?php endif; ?>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-muted);"><?= __('messages.shipping_fee') ?>:</span>
                <span style="color:var(--color-secondary); font-weight:700;">مجاناً ⚡</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-weight:900; font-size:1.35rem; color:var(--color-primary); border-top:2px solid var(--border-subtle); padding-top:8px;">
                <span><?= __('messages.total') ?>:</span>
                <span><?= format_price($order->total_amount, $order->currency_code) ?></span>
            </div>
        </div>
    </div>
</div>
@endsection
