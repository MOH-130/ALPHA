@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;"><?= __('admin.brands') ?></h1>

<div style="display:grid; grid-template-columns: 1fr 340px; gap:2rem; align-items:start;">
    <!-- Brands List -->
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>الماركة</th>
                            <th>الاسم بالإنجليزية</th>
                            <th>عدد المنتجات</th>
                            <th>الحالة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td style="font-weight:700; color:var(--admin-primary);"><?= $brand->name_ar ?></td>
                                <td><?= $brand->name_en ?></td>
                                <td><?= $brand->productsCount() ?></td>
                                <td><span class="status-pill bg-green-100 text-green-800"><?= strtoupper($brand->status) ?></span></td>
                                <td>
                                    <a href="<?= url('admin/brands/' . $brand->id . '/delete') ?>" onclick="return confirm('هل تريد حذف هذه الماركة؟');" class="btn btn-outline btn-sm" style="color:#ef4444;">حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Brand Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">➕ إضافة ماركة جديدة</h3>
        </div>
        <div class="admin-card-body">
            <form action="<?= url('admin/brands/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">اسم الماركة بالعربية *</label>
                    <input type="text" name="name_ar" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">اسم الماركة بالإنجليزية *</label>
                    <input type="text" name="name_en" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    💾 حفظ الماركة
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
