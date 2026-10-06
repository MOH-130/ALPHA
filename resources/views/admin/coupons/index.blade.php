@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;"><?= __('admin.coupons') ?></h1>

<div style="display:grid; grid-template-columns: 1fr 340px; gap:2rem; align-items:start;">
    <!-- Coupons List -->
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>الكود</th>
                            <th>النوع</th>
                            <th>القيمة</th>
                            <th>الحد الأدنى</th>
                            <th>مرات الاستخدام</th>
                            <th>الحالة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td style="font-weight:800; color:var(--admin-primary); font-size:1.05rem;"><?= $coupon->code ?></td>
                                <td><?= $coupon->type === 'percentage' ? 'نسبة مئوية (%)' : 'مبلغ ثابت' ?></td>
                                <td style="font-weight:700;"><?= $coupon->type === 'percentage' ? ($coupon->value . '%') : format_price($coupon->value) ?></td>
                                <td><?= $coupon->min_order_amount ? format_price($coupon->min_order_amount) : '-' ?></td>
                                <td><?= $coupon->used_count ?> / <?= $coupon->usage_limit ?: '∞' ?></td>
                                <td><span class="status-pill bg-green-100 text-green-800"><?= strtoupper($coupon->status) ?></span></td>
                                <td>
                                    <a href="<?= url('admin/coupons/' . $coupon->id . '/delete', ['_token' => csrf_token()]) ?>" onclick="return confirm('هل تريد حذف هذا الكوبون؟');" class="btn btn-outline btn-sm" style="color:#ef4444;">حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Coupon Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">➕ إنشاء كود خصم جديد</h3>
        </div>
        <div class="admin-card-body">
            <form action="<?= url('admin/coupons/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">كود الخصم (الكوبون) *</label>
                    <input type="text" name="code" class="form-control" placeholder="SALE20" style="text-transform:uppercase; font-weight:700;" required>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الخصم *</label>
                    <select name="type" class="form-control" required>
                        <option value="percentage">نسبة مئوية (Percentage %)</option>
                        <option value="fixed">مبلغ ثابت (Fixed Amount)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">قيمة الخصم *</label>
                    <input type="number" step="0.01" name="value" class="form-control" placeholder="مثال: 15 للنسبة أو 50 للمبلغ" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الحد الأدنى لقيمة الطلب (د.ج - DZD)</label>
                    <input type="number" step="0.01" name="min_order_amount" class="form-control" placeholder="1000">
                </div>

                <div class="form-group">
                    <label class="form-label">الحد الأقصى لمرات الاستخدام</label>
                    <input type="number" name="usage_limit" class="form-control" placeholder="100">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    💾 حفظ الكوبون
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
