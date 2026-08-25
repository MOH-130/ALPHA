@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;"><?= __('admin.returns') ?></h1>

<div class="admin-card">
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>السبب</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th style="text-align:center;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($returns['data'])): ?>
                        <tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا توجد أي طلبات استرجاع.</td></tr>
                    <?php else: ?>
                        <?php foreach ($returns['data'] as $ret): ?>
                            <tr>
                                <td>#<?= $ret->id ?></td>
                                <td style="font-weight:700; color:var(--admin-primary);"><?= $ret->order() ? $ret->order()->order_number : '-' ?></td>
                                <td><?= $ret->user() ? htmlspecialchars($ret->user()->name) : 'عميل' ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($ret->reason) ?></strong>
                                    <div style="font-size:0.8rem; color:var(--admin-text-muted);"><?= htmlspecialchars($ret->details) ?></div>
                                </td>
                                <td style="font-weight:800;"><?= format_price($ret->refund_amount ?: 0) ?></td>
                                <td><span class="status-pill <?= $ret->getStatusBadgeClass() ?>"><?= strtoupper($ret->status) ?></span></td>
                                <td style="font-size:0.85rem; color:var(--admin-text-muted);"><?= date('Y-m-d', strtotime($ret->created_at)) ?></td>
                                <td style="text-align:center;">
                                    <form action="<?= url('admin/returns/' . $ret->id . '/status') ?>" method="POST" style="display:inline-flex; gap:4px;">
                                        <?= csrf_field() ?>
                                        <select name="status" class="form-control" style="font-size:0.8rem; padding:0.3rem;" onchange="this.form.submit()">
                                            <option value="pending" <?= $ret->status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="approved" <?= $ret->status === 'approved' ? 'selected' : '' ?>>Approve</option>
                                            <option value="refunded" <?= $ret->status === 'refunded' ? 'selected' : '' ?>>Refund</option>
                                            <option value="rejected" <?= $ret->status === 'rejected' ? 'selected' : '' ?>>Reject</option>
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
