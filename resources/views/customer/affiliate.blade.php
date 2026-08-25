@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <div style="display:grid; grid-template-columns: 260px 1fr; gap:2.5rem; align-items:start;">
        
        <!-- Sidebar -->
        <aside style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem;">
            <ul style="list-style:none; display:flex; flex-direction:column; gap:6px;">
                <li><a href="<?= url('account/dashboard') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">📊 <?= __('messages.dashboard') ?></a></li>
                <li><a href="<?= url('account/orders') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🛒 <?= __('messages.my_orders') ?></a></li>
                <li><a href="<?= url('wishlist') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">❤️ <?= __('messages.my_wishlist') ?></a></li>
                <li><a href="<?= url('account/returns') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🔄 <?= __('messages.returns_refunds') ?></a></li>
                <li><a href="<?= url('account/affiliate') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start; background:rgba(0, 210, 255, 0.15); color:var(--color-primary); border-color:var(--color-primary);">🤝 <?= __('messages.affiliate_program') ?></a></li>
                <li><a href="<?= url('account/profile') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">⚙️ <?= __('messages.profile_settings') ?></a></li>
            </ul>
        </aside>

        <div>
            <h1 style="font-size:1.5rem; font-weight:800; margin-bottom:0.5rem; color:#fff;">
                🤝 <?= __('messages.affiliate_program') ?> (نظام التسويق بالعمولة)
            </h1>
            <p style="color:var(--text-muted); margin-bottom:2rem;">
                شارك رابط الإحالة الخاص بك واكسب عمولة بنسبة <strong><?= \App\Services\AffiliateService::getCommissionRate() ?>%</strong> على كل عملية شراء ناجحة للألعاب والبكجات!
            </p>

            <!-- Referral Link Card -->
            <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.75rem; margin-bottom:2rem;">
                <label style="display:block; font-weight:700; font-size:0.95rem; margin-bottom:0.75rem; color:#fff;"><?= __('messages.affiliate_link') ?>:</label>
                <div style="display:flex; gap:8px;">
                    <input type="text" id="affiliate-url-input" class="form-control" value="<?= htmlspecialchars($referralUrl) ?>" style="font-weight:700; font-size:1rem; color:var(--color-primary); background:var(--bg-input);" readonly>
                    <button type="button" class="btn btn-primary btn-copy" data-target="#affiliate-url-input" style="font-weight:800; white-space:nowrap;">
                        📋 <?= __('messages.copy_link') ?>
                    </button>
                </div>
            </div>

            <!-- Earnings Stats -->
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1.25rem; margin-bottom:2rem;">
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; text-align:center;">
                    <div style="font-size:1.75rem; font-weight:900; color:var(--color-primary);"><?= format_price($totalEarned) ?></div>
                    <div style="font-size:0.85rem; color:var(--text-muted);"><?= __('messages.total_earnings') ?></div>
                </div>
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; text-align:center;">
                    <div style="font-size:1.75rem; font-weight:900; color:var(--color-accent);"><?= format_price($pendingPayout) ?></div>
                    <div style="font-size:0.85rem; color:var(--text-muted);"><?= __('messages.pending_payout') ?></div>
                </div>
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; text-align:center;">
                    <div style="font-size:1.75rem; font-weight:900; color:var(--color-secondary);"><?= format_price($paidPayout) ?></div>
                    <div style="font-size:0.85rem; color:var(--text-muted);"><?= __('messages.paid_payout') ?></div>
                </div>
            </div>

            <!-- Commissions History -->
            <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem;">
                <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:1.25rem; color:#fff;">سجل العمولات والمبيعات</h3>
                <?php if (empty($commissions)): ?>
                    <p style="color:var(--text-muted); text-align:center; padding:2rem 0;">لم يتم تسجيل أي مبيعات عبر رابط الإحالة الخاص بك حتى الآن. ابدأ بالمشاركة واكسب أرباحاً فورية!</p>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; text-align:inherit; font-size:0.9rem;">
                            <thead>
                                <tr style="border-bottom:2px solid var(--border-subtle); color:var(--text-muted);">
                                    <th style="padding:0.75rem 0.5rem;">رقم الطلب</th>
                                    <th style="padding:0.75rem 0.5rem;">قيمة الطلب</th>
                                    <th style="padding:0.75rem 0.5rem;">نسبة العمولة</th>
                                    <th style="padding:0.75rem 0.5rem;">مبلغ الربح</th>
                                    <th style="padding:0.75rem 0.5rem;">الحالة</th>
                                    <th style="padding:0.75rem 0.5rem;">تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commissions as $comm): ?>
                                    <tr style="border-bottom:1px solid var(--border-subtle);">
                                        <td style="padding:1rem 0.5rem; font-weight:700; color:var(--color-primary);"><?= $comm->order() ? $comm->order()->order_number : '-' ?></td>
                                        <td style="padding:1rem 0.5rem; color:#fff;"><?= format_price($comm->order_total) ?></td>
                                        <td style="padding:1rem 0.5rem; color:#cbd5e1;"><?= $comm->commission_rate ?>%</td>
                                        <td style="padding:1rem 0.5rem; font-weight:800; color:var(--color-secondary);"><?= format_price($comm->commission_amount) ?></td>
                                        <td style="padding:1rem 0.5rem;">
                                            <span class="status-pill <?= $comm->status === 'paid' ? 'status-pill-paid' : 'status-pill-pending' ?>"><?= strtoupper($comm->status) ?></span>
                                        </td>
                                        <td style="padding:1rem 0.5rem; color:var(--text-muted);"><?= date('Y-m-d', strtotime($comm->created_at)) ?></td>
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
