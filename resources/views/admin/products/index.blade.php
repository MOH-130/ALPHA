@extends('layouts.admin')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem; font-weight:800;"><?= __('admin.products') ?></h1>
    <a href="<?= url('admin/products/create') ?>" class="btn btn-primary">
        ➕ <?= __('admin.add_product') ?>
    </a>
</div>

<!-- Filters Bar -->
<div class="admin-card" style="margin-bottom:1.5rem;">
    <div class="admin-card-body" style="padding:1rem 1.25rem;">
        <form action="<?= url('admin/products') ?>" method="GET" style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
            <input type="text" name="q" placeholder="ابحث بالاسم أو SKU..." value="<?= htmlspecialchars($q ?? '') ?>" class="form-control" style="max-width:260px;">
            
            <select name="category_id" class="form-control" style="max-width:200px;">
                <option value="">جميع الأقسام</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= ($catId == $cat->id) ? 'selected' : '' ?>><?= $cat->getNameAttribute() ?></option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="form-control" style="max-width:160px;">
                <option value="">جميع الحالات</option>
                <option value="active" <?= ($status === 'active') ? 'selected' : '' ?>>نشط (Active)</option>
                <option value="out_of_stock" <?= ($status === 'out_of_stock') ? 'selected' : '' ?>>نفذ المخزون</option>
                <option value="inactive" <?= ($status === 'inactive') ? 'selected' : '' ?>>معطل (Inactive)</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm">تطبيق الفلترة</button>
            <a href="<?= url('admin/products') ?>" class="btn btn-outline btn-sm">إلغاء</a>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="admin-card">
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>القسم</th>
                        <th>السعر</th>
                        <th>المخزون</th>
                        <th>المبيعات</th>
                        <th>الحالة</th>
                        <th style="text-align:center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products['data'])): ?>
                        <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا توجد منتجات مسجلة.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products['data'] as $p): ?>
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <img src="<?= asset('uploads/products/' . ($p->primary_image ?: 'default-product.png')) ?>" style="width:48px; height:48px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0;">
                                        <div>
                                            <strong style="display:block; font-size:0.95rem;"><?= is_rtl() ? $p->name_ar : $p->name_en ?></strong>
                                            <span style="font-size:0.75rem; color:var(--admin-text-muted);">SKU: <?= $p->sku ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $p->category() ? $p->category()->getNameAttribute() : '-' ?></td>
                                <td style="font-weight:800;"><?= format_price($p->price) ?></td>
                                <td>
                                    <span style="font-weight:700; color:<?= $p->stock_quantity <= 5 ? 'var(--color-danger)' : 'inherit' ?>;">
                                        <?= $p->stock_quantity ?> قطعة
                                    </span>
                                </td>
                                <td><?= $p->sales_count ?></td>
                                <td>
                                    <span class="status-pill <?= $p->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>"><?= strtoupper($p->status) ?></span>
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <a href="<?= url('product/' . $p->slug) ?>" target="_blank" class="btn btn-outline btn-sm" title="معاينة">👁️</a>
                                        <a href="<?= url('admin/products/' . $p->id . '/edit') ?>" class="btn btn-outline btn-sm" title="تعديل">✏️</a>
                                        <a href="<?= url('admin/products/' . $p->id . '/delete', ['_token' => csrf_token()]) ?>" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا المنتج نهائياً؟');" class="btn btn-outline btn-sm" style="color:#ef4444;" title="حذف">🗑️</a>
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
