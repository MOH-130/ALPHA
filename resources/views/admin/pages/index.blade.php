@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;">📄 <?= __('admin.pages') ?></h1>

<div style="display:grid; grid-template-columns: 1fr 380px; gap:2rem; align-items:start;">
    <!-- Pages List -->
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>عنوان الصفحة</th>
                            <th>الرابط (Slug)</th>
                            <th>الحالة</th>
                            <th>معاينة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pages as $p): ?>
                            <tr>
                                <td style="font-weight:700; color:var(--admin-primary);"><?= $p->title_ar ?></td>
                                <td><code>page/<?= $p->slug ?></code></td>
                                <td><span class="status-pill bg-green-100 text-green-800"><?= strtoupper($p->status) ?></span></td>
                                <td><a href="<?= url('page/' . $p->slug) ?>" target="_blank" class="btn btn-outline btn-sm">عرض 👁️</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Page Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">➕ إنشاء صفحة جديدة</h3>
        </div>
        <div class="admin-card-body">
            <form action="<?= url('admin/pages/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">العنوان بالعربية *</label>
                    <input type="text" name="title_ar" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">العنوان بالإنجليزية *</label>
                    <input type="text" name="title_en" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الرابط الفرعي (Slug) *</label>
                    <input type="text" name="slug" placeholder="custom-page" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">محتوى الصفحة (عربي)</label>
                    <textarea name="content_ar" rows="4" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">محتوى الصفحة (إنجليزي)</label>
                    <textarea name="content_en" rows="4" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    💾 نشر الصفحة
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
