@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;">🚚 <?= __('admin.shipping') ?></h1>

<div class="admin-card">
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>طريقة الشحن</th>
                        <th>الكود</th>
                        <th>التكلفة الأساسية (د.ج - DZD)</th>
                        <th>حد الشحن المجاني</th>
                        <th>الحالة</th>
                        <th style="text-align:center;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($methods as $m): ?>
                        <tr>
                            <td>
                                <strong><?= $m->name_ar ?></strong>
                                <div style="font-size:0.8rem; color:var(--admin-text-muted);"><?= $m->description_ar ?></div>
                            </td>
                            <td><code><?= $m->code ?></code></td>
                            <td style="font-weight:700;"><?= format_price($m->cost) ?></td>
                            <td><?= $m->free_threshold ? format_price($m->free_threshold) : 'غير مفعل' ?></td>
                            <td><span class="status-pill <?= $m->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>"><?= strtoupper($m->status) ?></span></td>
                            <td style="text-align:center;">
                                <form action="<?= url('admin/shipping/' . $m->id) ?>" method="POST" style="display:inline-flex; gap:6px; align-items:center;">
                                    <?= csrf_field() ?>
                                    <input type="number" step="0.01" name="cost" value="<?= $m->cost ?>" class="form-control" style="width:80px; padding:4px;" title="التكلفة">
                                    <select name="status" class="form-control" style="width:100px; padding:4px;">
                                        <option value="active" <?= $m->status === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= $m->status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">حفظ</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
