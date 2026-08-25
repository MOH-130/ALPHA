@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;"><?= __('admin.orders') ?></h1>

<!-- Filters Bar -->
<div class="admin-card" style="margin-bottom:1.5rem;">
    <div class="admin-card-body" style="padding:1rem 1.25rem;">
        <form action="<?= url('admin/orders') ?>" method="GET" style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
            <input type="text" name="q" placeholder="رقم الطلب أو اسم العميل أو الهاتف..." value="<?= htmlspecialchars($q ?? '') ?>" class="form-control" style="max-width:280px;">
            
            <select name="status" class="form-control" style="max-width:180px;">
                <option value="">جميع حالات الطلب</option>
                <option value="pending" <?= ($status === 'pending') ? 'selected' : '' ?>>Pending (قيد الانتظار)</option>
                <option value="processing" <?= ($status === 'processing') ? 'selected' : '' ?>>Processing (قيد التجهيز)</option>
                <option value="shipped" <?= ($status === 'shipped') ? 'selected' : '' ?>>Shipped (تم الشحن)</option>
                <option value="completed" <?= ($status === 'completed') ? 'selected' : '' ?>>Completed (مكتمل)</option>
                <option value="cancelled" <?= ($status === 'cancelled') ? 'selected' : '' ?>>Cancelled (ملغي)</option>
            </select>

            <select name="payment_status" class="form-control" style="max-width:180px;">
                <option value="">جميع حالات الدفع</option>
                <option value="pending" <?= ($paymentStatus === 'pending') ? 'selected' : '' ?>>Pending (معلق)</option>
                <option value="paid" <?= ($paymentStatus === 'paid') ? 'selected' : '' ?>>Paid (مدفوع)</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm">تطبيق الفلترة</button>
            <a href="<?= url('admin/orders') ?>" class="btn btn-outline btn-sm">إلغاء</a>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="admin-card">
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>الهاتف</th>
                        <th>الإجمالي</th>
                        <th>طريقة الدفع</th>
                        <th>حالة الطلب</th>
                        <th>التاريخ</th>
                        <th style="text-align:center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders['data'])): ?>
                        <tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا توجد أي طلبات مطابقة.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders['data'] as $order): ?>
                            <tr>
                                <td style="font-weight:700; color:var(--admin-primary);"><?= $order->order_number ?></td>
                                <td><?= htmlspecialchars($order->customer_name) ?></td>
                                <td><?= htmlspecialchars($order->customer_phone) ?></td>
                                <td style="font-weight:800;"><?= format_price($order->total_amount, $order->currency_code) ?></td>
                                <td><?= strtoupper($order->payment_method) ?></td>
                                <td><span class="status-pill <?= $order->getStatusBadgeClass() ?>"><?= strtoupper($order->status) ?></span></td>
                                <td style="color:var(--admin-text-muted); font-size:0.85rem;"><?= date('Y-m-d H:i', strtotime($order->created_at)) ?></td>
                                <td style="text-align:center;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <a href="<?= url('admin/orders/' . $order->id) ?>" class="btn btn-outline btn-sm">معاينة 📋</a>
                                        <a href="<?= url('admin/orders/' . $order->id . '/invoice') ?>" target="_blank" class="btn btn-outline btn-sm">طباعة 🖨️</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
