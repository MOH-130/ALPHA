@extends('layouts.admin')

@section('content')
<h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem;">🌐 <?= __('admin.languages') ?></h1>

<div class="admin-card">
    <div class="admin-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>الكود</th>
                        <th>اسم اللغة</th>
                        <th>الاتجاه</th>
                        <th>الافتراضية</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($languages as $lang): ?>
                        <tr>
                            <td style="font-weight:700; color:var(--admin-primary);"><code><?= $lang->code ?></code></td>
                            <td style="font-weight:700;"><?= $lang->name ?></td>
                            <td><?= strtoupper($lang->direction) ?></td>
                            <td>
                                <span class="status-pill <?= $lang->is_default ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= $lang->is_default ? 'نعم (افتراضية)' : 'لا' ?>
                                </span>
                            </td>
                            <td><span class="status-pill bg-green-100 text-green-800"><?= strtoupper($lang->status) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
