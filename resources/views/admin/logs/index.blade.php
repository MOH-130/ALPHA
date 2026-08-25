@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;">📜 <?= __('admin.activity_logs') ?></h1>

<div class="admin-card">
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>العملية (Action)</th>
                        <th>الوحدة (Module)</th>
                        <th>التفاصيل</th>
                        <th>عنوان IP</th>
                        <th>الوقت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs['data'])): ?>
                        <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا توجد أي سجلات نشاط حتى الآن.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs['data'] as $log): ?>
                            <tr>
                                <td>#<?= $log->id ?></td>
                                <td style="font-weight:700;"><?= htmlspecialchars($log->user_name) ?></td>
                                <td style="color:var(--admin-primary); font-weight:700;"><?= htmlspecialchars($log->action) ?></td>
                                <td><span class="status-pill bg-blue-100 text-blue-800"><?= htmlspecialchars($log->module) ?></span></td>
                                <td style="font-size:0.85rem; color:var(--admin-text-muted);"><?= htmlspecialchars($log->details) ?></td>
                                <td><code><?= $log->ip_address ?></code></td>
                                <td style="font-size:0.85rem; color:var(--admin-text-muted);"><?= date('Y-m-d H:i:s', strtotime($log->created_at)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
