@extends('layouts.admin')

@section('content')
<div style="margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center;">
    <h1 style="font-size:1.5rem; font-weight:800;">➕ <?= __('admin.add_product') ?></h1>
    <a href="<?= url('admin/products') ?>" class="btn btn-outline btn-sm">&larr; <?= __('messages.back') ?></a>
</div>

<form action="<?= url('admin/products/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem; align-items:start;">
        
        <!-- Left: Main Details -->
        <div class="admin-card">
            <div class="admin-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">اسم المنتج بالعربية *</label>
                        <input type="text" name="name_ar" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">اسم المنتج بالإنجليزية *</label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">السعر الأساسي (SAR) *</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">السعر قبل الخصم (اختياري)</label>
                        <input type="number" step="0.01" name="compare_price" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">سعر التكلفة (Cost Price)</label>
                        <input type="number" step="0.01" name="cost_price" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">الكمية بالمخزون (Stock) *</label>
                        <input type="number" name="stock_quantity" value="10" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">تنبيه انخفاض المخزون عند</label>
                        <input type="number" name="low_stock_threshold" value="5" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رمز SKU</label>
                        <input type="text" name="sku" class="form-control" placeholder="اتركه فارغاً للتوليد التلقائي">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">وصف قصير بالعربية</label>
                    <textarea name="short_description_ar" rows="2" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">وصف قصير بالإنجليزية</label>
                    <textarea name="short_description_en" rows="2" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">الوصف الكامل والمواصفات (عربي)</label>
                    <textarea name="description_ar" rows="4" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">الوصف الكامل والمواصفات (إنجليزي)</label>
                    <textarea name="description_en" rows="4" class="form-control"></textarea>
                </div>
            </div>
        </div>

        <!-- Right: Images, Category, Brand, Badges -->
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <!-- Category & Brand -->
            <div class="admin-card">
                <div class="admin-card-body">
                    <div class="form-group">
                        <label class="form-label">القسم الرئيسي *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- اختر القسم --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c->id ?>"><?= $c->getNameAttribute() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">الماركة التجارية</label>
                        <select name="brand_id" class="form-control">
                            <option value="">-- بدون ماركة --</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= $b->id ?>"><?= $b->getNameAttribute() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">حالة المنتج</label>
                        <select name="status" class="form-control">
                            <option value="active">نشط ومتاح للبيع (Active)</option>
                            <option value="inactive">معطل ومخفي (Inactive)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Images Upload -->
            <div class="admin-card">
                <div class="admin-card-body">
                    <div class="form-group">
                        <label class="form-label">الصورة الرئيسية للمنتج *</label>
                        <input type="file" name="primary_image" class="form-control image-file-input" data-preview="#primary-img-preview" required>
                        <img id="primary-img-preview" style="width:100%; height:160px; object-fit:contain; border:1px dashed #cbd5e1; border-radius:8px; margin-top:8px; display:none;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">صور المعرض الإضافية (Gallery)</label>
                        <input type="file" name="gallery_images[]" multiple class="form-control">
                    </div>
                </div>
            </div>

            <!-- Flags -->
            <div class="admin-card">
                <div class="admin-card-body">
                    <label style="display:flex; align-items:center; gap:8px; margin-bottom:10px; cursor:pointer;">
                        <input type="checkbox" name="is_featured" value="1">
                        <span>منتج مميز (Featured)</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; margin-bottom:10px; cursor:pointer;">
                        <input type="checkbox" name="is_best_seller" value="1">
                        <span>الأكثر مبيعاً (Best Seller)</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="is_deal" value="1">
                        <span>إدراج في عروض الفلاش سيلز (Flash Deal)</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                💾 حفظ ونشر المنتج
            </button>
        </div>
    </div>
</form>
@endsection
