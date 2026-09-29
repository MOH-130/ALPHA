@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <div class="account-layout">
        
        <!-- Sidebar -->
        <aside style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem;">
            <ul style="list-style:none; display:flex; flex-direction:column; gap:6px;">
                <li><a href="<?= url('account/dashboard') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">📊 <?= __('messages.dashboard') ?></a></li>
                <li><a href="<?= url('account/orders') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🛒 <?= __('messages.my_orders') ?></a></li>
                <li><a href="<?= url('wishlist') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">❤️ <?= __('messages.my_wishlist') ?></a></li>
                <li><a href="<?= url('account/returns') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start; background:rgba(0, 210, 255, 0.15); color:var(--color-primary); border-color:var(--color-primary);">🔄 <?= __('messages.returns_refunds') ?></a></li>
                <li><a href="<?= url('account/affiliate') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🤝 <?= __('messages.affiliate_program') ?></a></li>
                <li><a href="<?= url('account/profile') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">⚙️ <?= __('messages.profile_settings') ?></a></li>
            </ul>
        </aside>

        <div>
            <!-- Request Return Form -->
            <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem; margin-bottom:2rem;">
                <h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem; color:#fff;">
                    🔄 <?= __('messages.request_return') ?> والضمان
                </h1>

                <?php if (empty($eligibleOrders)): ?>
                    <p style="color:var(--text-muted);">لا توجد لديك طلبات مؤهلة للإرجاع حالياً (يجب أن يكون الطلب مشحوناً أو مكتملاً).</p>
                <?php else: ?>
                    <form action="<?= url('account/returns') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label class="form-label">اختر الطلب المراد استبداله أو إرجاعه *</label>
                            <select name="order_id" class="form-control" required>
                                <?php foreach ($eligibleOrders as $eo): ?>
                                    <option value="<?= $eo->id ?>">طلب رقم: <?= $eo->order_number ?> (الإجمالي: <?= format_price($eo->total_amount, $eo->currency_code) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?= __('messages.return_reason') ?> *</label>
                            <select name="reason" class="form-control" required>
                                <option value="damaged">مشكلة في كود التفعيل أو الحساب</option>
                                <option value="wrong_item">وصلني حساب أو إصدار مختلف عن طلبي</option>
                                <option value="not_as_described">المنتج لا يطابق المواصفات</option>
                                <option value="changed_mind">طلب استبدال بلعبة أخرى</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?= __('messages.return_details') ?> *</label>
                            <textarea name="details" rows="3" class="form-control" placeholder="اشرح تفاصيل المشكلة بدقة..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">صورة إثبات المشكلة (اختياري)</label>
                            <input type="file" name="images" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg" style="font-weight:800;">
                            إرسال طلب الدعم والضمان 🚀
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Existing Return Requests Table -->
            <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem;">
                <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:1.25rem; color:#fff;">سجل طلبات الضمان والإرجاع السابقة</h3>
                <?php if (empty($returns)): ?>
                    <p style="color:var(--text-muted); text-align:center; padding:1.5rem 0;">لا توجد أي طلبات إرجاع سابقة.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table style="width:100%; border-collapse:collapse; text-align:inherit; font-size:0.9rem;">
                            <thead>
                                <tr style="border-bottom:2px solid var(--border-subtle); color:var(--text-muted);">
                                    <th style="padding:0.75rem 0.5rem;">رقم الطلب</th>
                                    <th style="padding:0.75rem 0.5rem;">السبب</th>
                                    <th style="padding:0.75rem 0.5rem;">الحالة</th>
                                    <th style="padding:0.75rem 0.5rem;">ملاحظات الدعم الفني</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($returns as $ret): ?>
                                    <tr style="border-bottom:1px solid var(--border-subtle);">
                                        <td style="padding:1rem 0.5rem; font-weight:700; color:var(--color-primary);"><?= $ret->order() ? $ret->order()->order_number : '-' ?></td>
                                        <td style="padding:1rem 0.5rem; color:#fff;"><?= htmlspecialchars($ret->reason) ?></td>
                                        <td style="padding:1rem 0.5rem;"><span class="status-pill <?= $ret->getStatusBadgeClass() ?>"><?= strtoupper($ret->status) ?></span></td>
                                        <td style="padding:1rem 0.5rem; color:var(--text-muted);"><?= htmlspecialchars($ret->admin_notes ?: 'قيد المراجعة') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
@endsection
