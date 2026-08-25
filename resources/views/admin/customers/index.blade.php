@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;"><?= __('admin.customers') ?></h1>

<!-- Filters Bar -->
<div class="admin-card" style="margin-bottom:1.5rem;">
    <div class="admin-card-body" style="padding:1rem 1.25rem;">
        <form action="<?= url('admin/customers') ?>" method="GET" style="display:flex; gap:1rem; align-items:center;">
            <input type="text" name="q" placeholder="ابحث بالاسم أو البريد أو الهاتف..." value="<?= htmlspecialchars($q ?? '') ?>" class="form-control" style="max-width:320px;">
            <button type="submit" class="btn btn-secondary btn-sm">بحث</button>
            <a href="<?= url('admin/customers') ?>" class="btn btn-outline btn-sm">إلغاء</a>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>العميل</th>
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th>المدينة</th>
                        <th>الموافقة</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th style="text-align:center;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers['data'])): ?>
                        <tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا يوجد عملاء مسجلون.</td></tr>
                    <?php else: ?>
                        <?php foreach ($customers['data'] as $c): ?>
                            <tr>
                                <td style="font-weight:700; color:var(--admin-primary);"><?= htmlspecialchars($c->name) ?></td>
                                <td><?= htmlspecialchars($c->email) ?></td>
                                <td><?= htmlspecialchars($c->phone) ?></td>
                                <td><?= htmlspecialchars($c->city ?: '-') ?></td>
                                <td>
                                    <span class="status-pill <?= $c->is_approved ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' ?>">
                                        <?= $c->is_approved ? 'مقبول' : 'معلق' ?>
                                    </span>
                                </td>
                                <td><span class="status-pill <?= $c->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-rose-100 text-rose-800' ?>"><?= strtoupper($c->status) ?></span></td>
                                <td style="color:var(--admin-text-muted); font-size:0.85rem;"><?= date('Y-m-d', strtotime($c->created_at)) ?></td>
                                <td style="text-align:center;">
                                    <form action="<?= url('admin/customers/' . $c->id . '/status') ?>" method="POST" style="display:inline-flex; gap:4px;">
                                        <?= csrf_field() ?>
                                        <?php if (!$c->is_approved): ?>
                                            <input type="hidden" name="is_approved" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">موافقة ✓</button>
                                        <?php endif; ?>
                                        <select name="status" class="form-control" style="font-size:0.8rem; padding:0.3rem;" onchange="this.form.submit()">
                                            <option value="active" <?= $c->status === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= $c->status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            <option value="banned" <?= $c->status === 'banned' ? 'selected' : '' ?>>Banned</option>
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
