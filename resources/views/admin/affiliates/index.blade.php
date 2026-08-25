@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;"><?= __('admin.affiliates') ?></h1>

<!-- Overview KPIs -->
<div class="kpi-grid" style="margin-bottom:2rem;">
    <div class="kpi-card">
        <div>
            <div class="kpi-title">إجمالي العمولات المسجلة</div>
            <div class="kpi-value"><?= format_price($totalCommissions) ?></div>
        </div>
        <div class="kpi-icon blue">🤝</div>
    </div>
    <div class="kpi-card">
        <div>
            <div class="kpi-title">عمولات معلقة (Pending)</div>
            <div class="kpi-value"><?= format_price($pendingCommissions) ?></div>
        </div>
        <div class="kpi-icon amber">⏳</div>
    </div>
    <div class="kpi-card">
        <div>
            <div class="kpi-title">عمولات مدفوعة للمسوقين</div>
            <div class="kpi-value"><?= format_price($paidCommissions) ?></div>
        </div>
        <div class="kpi-icon green">✅</div>
    </div>
</div>

<!-- Commissions Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">سجل عمليات ومبيعات التسويق بالعمولة</h3>
    </div>
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المسوق (Affiliate)</th>
                        <th>رقم الطلب</th>
                        <th>إجمالي الطلب</th>
                        <th>نسبة العمولة</th>
                        <th>مبلغ العمولة</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th style="text-align:center;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commissions['data'])): ?>
                        <tr><td colspan="9" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا توجد أي عمليات تسويق بالعمولة حتى الآن.</td></tr>
                    <?php else: ?>
                        <?php foreach ($commissions['data'] as $comm): ?>
                            <tr>
                                <td>#<?= $comm->id ?></td>
                                <td>
                                    <strong><?= $comm->affiliate() ? htmlspecialchars($comm->affiliate()->name) : '-' ?></strong>
                                    <div style="font-size:0.75rem; color:var(--admin-text-muted);"><?= $comm->affiliate() ? $comm->affiliate()->email : '' ?></div>
                                </td>
                                <td style="font-weight:700; color:var(--admin-primary);"><?= $comm->order() ? $comm->order()->order_number : '-' ?></td>
                                <td><?= format_price($comm->order_total) ?></td>
                                <td><?= $comm->commission_rate ?>%</td>
                                <td style="font-weight:800; color:var(--admin-primary);"><?= format_price($comm->commission_amount) ?></td>
                                <td>
                                    <span class="status-pill <?= $comm->status === 'paid' ? 'bg-green-100 text-green-800' : ($comm->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') ?>">
                                        <?= strtoupper($comm->status) ?>
                                    </span>
                                </td>
                                <td style="font-size:0.85rem; color:var(--admin-text-muted);"><?= date('Y-m-d', strtotime($comm->created_at)) ?></td>
                                <td style="text-align:center;">
                                    <form action="<?= url('admin/affiliates/' . $comm->id . '/status') ?>" method="POST" style="display:inline-flex; gap:4px;">
                                        <?= csrf_field() ?>
                                        <select name="status" class="form-control" style="font-size:0.8rem; padding:0.3rem;" onchange="this.form.submit()">
                                            <option value="pending" <?= $comm->status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="approved" <?= $comm->status === 'approved' ? 'selected' : '' ?>>Approve</option>
                                            <option value="paid" <?= $comm->status === 'paid' ? 'selected' : '' ?>>Mark as Paid</option>
                                            <option value="rejected" <?= $comm->status === 'rejected' ? 'selected' : '' ?>>Reject</option>
                                        </select>
                                    </form>
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
