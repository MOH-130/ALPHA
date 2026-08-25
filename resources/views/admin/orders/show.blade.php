@extends('layouts.admin')

@section('content')
<div style="margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center;">
    <h1 style="font-size:1.5rem; font-weight:800;">📋 تفاصيل الطلب: <?= $order->order_number ?></h1>
    <div style="display:flex; gap:8px;">
        <a href="<?= url('admin/orders/' . $order->id . '/invoice') ?>" target="_blank" class="btn btn-secondary btn-sm">🖨️ طباعة الفاتورة</a>
        <a href="<?= url('admin/orders') ?>" class="btn btn-outline btn-sm">&larr; العودة للطلبات</a>
    </div>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem; align-items:start;">
    
    <!-- Left Column: Items & Timeline -->
    <div style="display:flex; flex-direction:column; gap:1.5rem;">
        <!-- Order Items -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">المنتجات المطلوبة (<?= count($items) ?>)</h3>
            </div>
            <div class="admin-card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>سعر الوحدة</th>
                                <th>الكمية</th>
                                <th>المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <img src="<?= asset('uploads/products/' . ($item->product_image ?: 'default-product.png')) ?>" style="width:40px; height:40px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0;">
                                            <div>
                                                <strong><?= htmlspecialchars($item->product_name) ?></strong>
                                                <div style="font-size:0.75rem; color:var(--admin-text-muted);">SKU: <?= $item->sku ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= format_price($item->unit_price, $order->currency_code) ?></td>
                                    <td style="font-weight:700;"><?= $item->quantity ?></td>
                                    <td style="font-weight:800;"><?= format_price($item->subtotal, $order->currency_code) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div style="padding:1.25rem 1.5rem; background:var(--bg-input, #090e1a); border-top:1px solid var(--admin-border, rgba(255,255,255,0.08)); display:flex; flex-direction:column; gap:6px; max-width:300px; margin-right:auto; font-size:0.9rem;">
                    <div style="display:flex; justify-content:space-between;">
                        <span>المجموع الفرعي:</span>
                        <span><?= format_price($order->subtotal, $order->currency_code) ?></span>
                    </div>
                    <?php if ($order->discount_amount > 0): ?>
                        <div style="display:flex; justify-content:space-between; color:var(--color-secondary, #00ff88);">
                            <span>الخصم (<?= $order->coupon_code ?>):</span>
                            <span>-<?= format_price($order->discount_amount, $order->currency_code) ?></span>
                        </div>
                    <?php endif; ?>
                    <div style="display:flex; justify-content:space-between;">
                        <span>الضريبة:</span>
                        <span><?= format_price($order->tax_amount, $order->currency_code) ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span>الشحن:</span>
                        <span><?= format_price($order->shipping_fee, $order->currency_code) ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:800; font-size:1.15rem; color:var(--admin-primary, #00d2ff); border-top:1px solid rgba(255,255,255,0.1); padding-top:6px;">
                        <span>الإجمالي:</span>
                        <span><?= format_price($order->total_amount, $order->currency_code) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Timeline -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">📜 سجل تتبع وتحديثات الطلب</h3>
            </div>
            <div class="admin-card-body">
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($history as $h): ?>
                        <div style="display:flex; justify-content:space-between; background:var(--bg-input, #090e1a); padding:10px 14px; border-radius:6px; border:1px solid rgba(255,255,255,0.08); font-size:0.875rem;">
                            <div>
                                <strong style="color:var(--admin-primary, #00d2ff);"><?= strtoupper($h->status) ?>:</strong>
                                <span><?= htmlspecialchars($h->comment) ?></span>
                                <span style="font-size:0.75rem; color:var(--admin-text-muted); display:block;">بواسطة: <?= htmlspecialchars($h->created_by) ?></span>
                            </div>
                            <span style="font-size:0.75rem; color:var(--admin-text-muted);"><?= date('Y-m-d H:i', strtotime($h->created_at)) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Status Updater & Customer Details -->
    <div style="display:flex; flex-direction:column; gap:1.5rem;">
        <!-- Status Updater -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">⚙️ تحديث حالة الطلب</h3>
            </div>
            <div class="admin-card-body">
                <form action="<?= url('admin/orders/' . $order->id . '/status') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="form-label">حالة الطلب *</label>
                        <select name="status" class="form-control">
                            <option value="pending" <?= $order->status === 'pending' ? 'selected' : '' ?>>Pending (قيد الانتظار)</option>
                            <option value="processing" <?= $order->status === 'processing' ? 'selected' : '' ?>>Processing (قيد التجهيز)</option>
                            <option value="paid" <?= $order->status === 'paid' ? 'selected' : '' ?>>Paid (مدفوع)</option>
                            <option value="shipped" <?= $order->status === 'shipped' ? 'selected' : '' ?>>Shipped (تم الشحن)</option>
                            <option value="completed" <?= $order->status === 'completed' ? 'selected' : '' ?>>Completed (مكتمل)</option>
                            <option value="cancelled" <?= $order->status === 'cancelled' ? 'selected' : '' ?>>Cancelled (ملغي)</option>
                            <option value="refunded" <?= $order->status === 'refunded' ? 'selected' : '' ?>>Refunded (مسترجع)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">حالة الدفع *</label>
                        <select name="payment_status" class="form-control">
                            <option value="pending" <?= $order->payment_status === 'pending' ? 'selected' : '' ?>>Pending (معلق)</option>
                            <option value="paid" <?= $order->payment_status === 'paid' ? 'selected' : '' ?>>Paid (مدفوع)</option>
                            <option value="failed" <?= $order->payment_status === 'failed' ? 'selected' : '' ?>>Failed (فشل)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">رقم بوليصة التتبع (Tracking Number)</label>
                        <input type="text" name="tracking_number" value="<?= htmlspecialchars($order->tracking_number ?? '') ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">ملاحظات التحديث / رسالة للعميل</label>
                        <textarea name="comment" rows="2" class="form-control" placeholder="تم تسليم الشحنة لشركة الشحن..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        💾 حفظ التحديث
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer & Shipping Card -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">📍 العميل والشحن</h3>
            </div>
            <div class="admin-card-body" style="font-size:0.9rem; line-height:1.7;">
                <strong><?= htmlspecialchars($order->customer_name) ?></strong><br>
                ✉️ <?= htmlspecialchars($order->customer_email) ?><br>
                📞 <?= htmlspecialchars($order->customer_phone) ?><br>
                <div style="margin-top:10px; border-top:1px solid #e2e8f0; padding-top:10px;">
                    <strong>عنوان التوصيل:</strong><br>
                    <?= htmlspecialchars($order->shipping_address) ?><br>
                    <?= htmlspecialchars($order->shipping_city) ?>, <?= htmlspecialchars($order->shipping_country) ?><br>
                    <strong>الشحن:</strong> <?= htmlspecialchars($order->shipping_method) ?>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
