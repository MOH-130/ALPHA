@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;">💱 <?= __('admin.currencies') ?></h1>

<div style="display:grid; grid-template-columns: 1fr 340px; gap:2rem; align-items:start;">
    <!-- Currencies List -->
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>الرمز</th>
                            <th>الاسم بالعربية</th>
                            <th>الاسم بالإنجليزية</th>
                            <th>الرمز العربي</th>
                            <th>سعر الصرف (نسبة للأساس)</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($currencies as $curr): ?>
                            <tr>
                                <td style="font-weight:800; color:var(--admin-primary);"><?= $curr->code ?></td>
                                <td><?= $curr->name_ar ?></td>
                                <td><?= $curr->name_en ?></td>
                                <td style="font-weight:700;"><?= $curr->symbol_ar ?></td>
                                <td style="font-weight:700;"><?= $curr->exchange_rate ?></td>
                                <td>
                                    <span class="status-pill <?= $curr->is_default ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= $curr->is_default ? 'الافتراضية' : 'نشط' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Currency Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">➕ إضافة عملة جديدة</h3>
        </div>
        <div class="admin-card-body">
            <form action="<?= url('admin/currencies/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">رمز العملة (ISO Code) *</label>
                    <input type="text" name="code" placeholder="KWD" class="form-control" style="text-transform:uppercase; font-weight:700;" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالعربية *</label>
                    <input type="text" name="name_ar" placeholder="دينار كويتي" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزية *</label>
                    <input type="text" name="name_en" placeholder="Kuwaiti Dinar" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">سعر الصرف (مقابل الريال) *</label>
                    <input type="number" step="0.0001" name="exchange_rate" placeholder="0.0820" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    💾 حفظ العملة
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
