@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;"><?= __('admin.reviews') ?></h1>

<div class="admin-card">
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الكاتب</th>
                        <th>التقييم</th>
                        <th>المراجعة والتعليق</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th style="text-align:center;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews['data'])): ?>
                        <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا توجد أي تقييمات.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reviews['data'] as $rev): ?>
                            <tr>
                                <td style="font-weight:700; color:var(--admin-primary);"><?= $rev->product() ? $rev->product()->getNameAttribute() : '-' ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($rev->author_name) ?></strong>
                                    <div style="font-size:0.75rem; color:var(--admin-text-muted);"><?= htmlspecialchars($rev->author_email) ?></div>
                                </td>
                                <td style="color:var(--color-accent); font-weight:700;">⭐ <?= $rev->rating ?> / 5</td>
                                <td style="max-width:320px; font-size:0.875rem; line-height:1.5;"><?= nl2br(htmlspecialchars($rev->comment)) ?></td>
                                <td>
                                    <span class="status-pill <?= $rev->is_approved ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' ?>">
                                        <?= $rev->is_approved ? 'معتمد ومنشور' : 'مخفي' ?>
                                    </span>
                                </td>
                                <td style="font-size:0.85rem; color:var(--admin-text-muted);"><?= date('Y-m-d', strtotime($rev->created_at)) ?></td>
                                <td style="text-align:center;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <a href="<?= url('admin/reviews/' . $rev->id . '/toggle') ?>" class="btn btn-outline btn-sm">
                                            <?= $rev->is_approved ? 'إخفاء 👁️' : 'اعتماد ونشر ✓' ?>
                                        </a>
                                        <a href="<?= url('admin/reviews/' . $rev->id . '/delete') ?>" onclick="return confirm('حذف هذا التقييم نهائياً؟');" class="btn btn-outline btn-sm" style="color:#ef4444;">حذف</a>
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
