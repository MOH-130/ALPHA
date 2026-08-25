@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <div style="display:grid; grid-template-columns: 260px 1fr; gap:2.5rem; align-items:start;">
        
        <!-- Sidebar -->
        <aside style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem;">
            <ul style="list-style:none; display:flex; flex-direction:column; gap:6px;">
                <li><a href="<?= url('account/dashboard') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">📊 <?= __('messages.dashboard') ?></a></li>
                <li><a href="<?= url('account/orders') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start; background:rgba(0, 210, 255, 0.15); color:var(--color-primary); border-color:var(--color-primary);">🛒 <?= __('messages.my_orders') ?></a></li>
                <li><a href="<?= url('wishlist') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">❤️ <?= __('messages.my_wishlist') ?></a></li>
                <li><a href="<?= url('account/returns') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🔄 <?= __('messages.returns_refunds') ?></a></li>
                <li><a href="<?= url('account/affiliate') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🤝 <?= __('messages.affiliate_program') ?></a></li>
                <li><a href="<?= url('account/profile') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">⚙️ <?= __('messages.profile_settings') ?></a></li>
            </ul>
        </aside>

        <!-- Orders List -->
        <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem;">
            <h1 style="font-size:1.5rem; font-weight:800; margin-bottom:1.5rem; color:#fff;">
                🛒 <?= __('messages.my_orders') ?>
            </h1>

            <?php if (empty($orders['data'])): ?>
                <p style="color:var(--text-muted); text-align:center; padding:3rem 0;">لا توجد أي طلبات سابقة مسجلة في حسابك.</p>
            <?php else: ?>
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; text-align:inherit; font-size:0.925rem;">
                        <thead>
                            <tr style="border-bottom:2px solid var(--border-subtle); color:var(--text-muted); font-size:0.825rem;">
                                <th style="padding:0.75rem 0.5rem;"><?= __('messages.order_number') ?></th>
                                <th style="padding:0.75rem 0.5rem;"><?= __('messages.order_date') ?></th>
                                <th style="padding:0.75rem 0.5rem;"><?= __('messages.total') ?></th>
                                <th style="padding:0.75rem 0.5rem;"><?= __('messages.order_status') ?></th>
                                <th style="padding:0.75rem 0.5rem;"><?= __('messages.payment_status') ?></th>
                                <th style="padding:0.75rem 0.5rem; text-align:center;"><?= __('messages.action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders['data'] as $order): ?>
                                <tr style="border-bottom:1px solid var(--border-subtle);">
                                    <td style="padding:1rem 0.5rem; font-weight:700; color:var(--color-primary);"><?= $order->order_number ?></td>
                                    <td style="padding:1rem 0.5rem; color:var(--text-muted);"><?= date('Y-m-d H:i', strtotime($order->created_at)) ?></td>
                                    <td style="padding:1rem 0.5rem; font-weight:800; color:#fff;"><?= format_price($order->total_amount, $order->currency_code) ?></td>
                                    <td style="padding:1rem 0.5rem;"><span class="status-pill <?= $order->getStatusBadgeClass() ?>"><?= strtoupper($order->status) ?></span></td>
                                    <td style="padding:1rem 0.5rem;"><span class="status-pill <?= $order->getPaymentStatusBadgeClass() ?>"><?= strtoupper($order->payment_status) ?></span></td>
                                    <td style="padding:1rem 0.5rem; text-align:center;">
                                        <a href="<?= url('account/orders/' . $order->id) ?>" class="btn btn-outline btn-sm">عرض الفاتورة 📄</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
@endsection
