@extends('layouts.admin')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem; font-weight:800;">🟢 <?= __('admin.live_visitors') ?></h1>
    <div style="background:#f0fdf4; color:#166534; padding:0.4rem 1rem; border-radius:9999px; font-weight:800; font-size:0.95rem; border:1px solid #bbf7d0;">
        🟢 <?= $onlineCount ?> زائر متواجد في المتجر الآن
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>عنوان IP (مشفر/محمي)</th>
                        <th>الصفحة الحالية</th>
                        <th>الجهاز</th>
                        <th>المتصفح</th>
                        <th>المصدر (Referrer)</th>
                        <th>آخر نشاط</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($visitors)): ?>
                        <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا يوجد زوار مسجلون حالياً.</td></tr>
                    <?php else: ?>
                        <?php foreach ($visitors as $v): ?>
                            <tr>
                                <td><code><?= $v->ip_address ?></code></td>
                                <td style="font-weight:700; color:var(--admin-primary);"><?= htmlspecialchars($v->current_url) ?></td>
                                <td><?= $v->device ?></td>
                                <td><?= $v->browser ?></td>
                                <td style="font-size:0.8rem; color:var(--admin-text-muted);"><?= htmlspecialchars($v->referrer ?: 'مباشر (Direct)') ?></td>
                                <td style="font-size:0.85rem; color:var(--admin-text-muted);"><?= date('H:i:s', strtotime($v->last_activity)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
