@extends('layouts.admin')

@section('content')
<div style="margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center;">
    <h1 style="font-size:1.5rem; font-weight:800;">✏️ <?= __('admin.edit_product') ?>: <?= htmlspecialchars($product->name_en) ?></h1>
    <a href="<?= url('admin/products') ?>" class="btn btn-outline btn-sm">&larr; <?= __('messages.back') ?></a>
</div>

<form action="<?= url('admin/products/' . $product->id . '/update') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem; align-items:start;">
        
        <!-- Left: Main Details -->
        <div class="admin-card">
            <div class="admin-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">اسم المنتج بالعربية *</label>
                        <input type="text" name="name_ar" class="form-control" value="<?= htmlspecialchars($product->name_ar) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">اسم المنتج بالإنجليزية *</label>
                        <input type="text" name="name_en" class="form-control" value="<?= htmlspecialchars($product->name_en) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">السعر الأساسي (SAR) *</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="<?= $product->price ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">السعر قبل الخصم</label>
                        <input type="number" step="0.01" name="compare_price" class="form-control" value="<?= $product->compare_price ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">سعر التكلفة</label>
                        <input type="number" step="0.01" name="cost_price" class="form-control" value="<?= $product->cost_price ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">الكمية بالمخزون (Stock) *</label>
                        <input type="number" name="stock_quantity" class="form-control" value="<?= $product->stock_quantity ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">تنبيه انخفاض المخزون</label>
                        <input type="number" name="low_stock_threshold" class="form-control" value="<?= $product->low_stock_threshold ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رمز SKU</label>
                        <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($product->sku) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">وصف قصير بالعربية</label>
                    <textarea name="short_description_ar" rows="2" class="form-control"><?= htmlspecialchars($product->short_description_ar) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">وصف قصير بالإنجليزية</label>
                    <textarea name="short_description_en" rows="2" class="form-control"><?= htmlspecialchars($product->short_description_en) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">الوصف الكامل والمواصفات (عربي)</label>
                    <textarea name="description_ar" rows="4" class="form-control"><?= htmlspecialchars($product->description_ar) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">الوصف الكامل والمواصفات (إنجليزي)</label>
                    <textarea name="description_en" rows="4" class="form-control"><?= htmlspecialchars($product->description_en) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Right: Category, Images, Flags -->
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <!-- Category & Brand -->
            <div class="admin-card">
                <div class="admin-card-body">
                    <div class="form-group">
                        <label class="form-label">القسم الرئيسي</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- بدون قسم --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c->id ?>" <?= $product->category_id == $c->id ? 'selected' : '' ?>><?= $c->getNameAttribute() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">الماركة</label>
                        <select name="brand_id" class="form-control">
                            <option value="">-- بدون ماركة --</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= $b->id ?>" <?= $product->brand_id == $b->id ? 'selected' : '' ?>><?= $b->getNameAttribute() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">حالة المنتج</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $product->status === 'active' ? 'selected' : '' ?>>نشط ومتاح للبيع (Active)</option>
                            <option value="out_of_stock" <?= $product->status === 'out_of_stock' ? 'selected' : '' ?>>نفذ المخزون (Out of Stock)</option>
                            <option value="inactive" <?= $product->status === 'inactive' ? 'selected' : '' ?>>معطل ومخفي (Inactive)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Primary Image -->
            <div class="admin-card">
                <div class="admin-card-body">
                    <div class="form-group">
                        <label class="form-label">الصورة الرئيسية الحالية:</label>
                        <img src="<?= asset('uploads/products/' . ($product->primary_image ?: 'default-product.png')) ?>" style="width:100%; height:140px; object-fit:contain; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:8px;">
                        <input type="file" name="primary_image" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Flags -->
            <div class="admin-card">
                <div class="admin-card-body">
                    <label style="display:flex; align-items:center; gap:8px; margin-bottom:10px; cursor:pointer;">
                        <input type="checkbox" name="is_featured" value="1" <?= $product->is_featured ? 'checked' : '' ?>>
                        <span>منتج مميز (Featured)</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; margin-bottom:10px; cursor:pointer;">
                        <input type="checkbox" name="is_best_seller" value="1" <?= $product->is_best_seller ? 'checked' : '' ?>>
                        <span>الأكثر مبيعاً (Best Seller)</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="is_deal" value="1" <?= $product->is_deal ? 'checked' : '' ?>>
                        <span>إدراج في عروض الفلاش سيلز (Flash Deal)</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                💾 حفظ التعديلات
            </button>
        </div>
    </div>
</form>
@endsection
