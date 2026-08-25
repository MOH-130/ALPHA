@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;"><?= __('admin.categories') ?></h1>

<div style="display:grid; grid-template-columns: 1fr 340px; gap:2rem; align-items:start;">
    <!-- Categories List -->
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>القسم</th>
                            <th>القسم الأب</th>
                            <th>عدد المنتجات</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td style="font-weight:700; color:var(--admin-primary);">
                                    <?= $cat->parent_id ? '— ' : '' ?><?= $cat->getNameAttribute() ?>
                                </td>
                                <td><?= $cat->parent() ? $cat->parent()->getNameAttribute() : 'رئيسي' ?></td>
                                <td><?= $cat->productsCount() ?></td>
                                <td><?= $cat->sort_order ?></td>
                                <td><span class="status-pill bg-green-100 text-green-800"><?= strtoupper($cat->status) ?></span></td>
                                <td>
                                    <a href="<?= url('admin/categories/' . $cat->id . '/delete') ?>" onclick="return confirm('هل تريد حذف هذا القسم؟');" class="btn btn-outline btn-sm" style="color:#ef4444;">حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Category Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">➕ إضافة قسم جديد</h3>
        </div>
        <div class="admin-card-body">
            <form action="<?= url('admin/categories/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">الاسم بالعربية *</label>
                    <input type="text" name="name_ar" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزية *</label>
                    <input type="text" name="name_en" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">القسم الأب (اختياري)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- بدون أب (قسم رئيسي) --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c->id ?>"><?= $c->getNameAttribute() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">ترتيب العرض</label>
                    <input type="number" name="sort_order" value="0" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    💾 حفظ القسم
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
