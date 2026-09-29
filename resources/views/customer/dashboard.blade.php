@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <div class="account-layout">
        
        <!-- Customer Navigation Sidebar -->
        <aside style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; box-shadow:var(--shadow-sm);">
            <div style="text-align:center; padding-bottom:1.5rem; border-bottom:1px solid var(--border-subtle); margin-bottom:1.25rem;">
                <div style="width:64px; height:64px; border-radius:50%; background:rgba(0, 210, 255, 0.15); color:var(--color-primary); display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin:0 auto 0.75rem; font-weight:800; border:1px solid rgba(0, 210, 255, 0.3);">
                    👤
                </div>
                <h3 style="font-size:1.05rem; font-weight:800; color:#fff;"><?= htmlspecialchars($user->name) ?></h3>
                <span style="font-size:0.8rem; color:var(--text-muted);"><?= htmlspecialchars($user->email) ?></span>
            </div>

            <ul style="list-style:none; display:flex; flex-direction:column; gap:6px;">
                <li><a href="<?= url('account/dashboard') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start; background:rgba(0, 210, 255, 0.15); color:var(--color-primary); border-color:var(--color-primary);">📊 <?= __('messages.dashboard') ?></a></li>
                <li><a href="<?= url('account/orders') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🛒 <?= __('messages.my_orders') ?></a></li>
                <li><a href="<?= url('wishlist') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">❤️ <?= __('messages.my_wishlist') ?></a></li>
                <li><a href="<?= url('account/returns') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🔄 <?= __('messages.returns_refunds') ?></a></li>
                <li><a href="<?= url('account/affiliate') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">🤝 <?= __('messages.affiliate_program') ?></a></li>
                <li><a href="<?= url('account/profile') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start;">⚙️ <?= __('messages.profile_settings') ?></a></li>
                <li style="border-top:1px solid var(--border-subtle); padding-top:6px; margin-top:6px;"><a href="<?= url('logout') ?>" class="btn btn-outline btn-sm btn-block" style="justify-content:flex-start; color:var(--color-danger); border-color:rgba(239,68,68,0.3);">🚪 <?= __('auth.logout') ?></a></li>
            </ul>
        </aside>

        <!-- Customer Main Section -->
        <div>
            <h1 style="font-size:1.75rem; font-weight:800; margin-bottom:1.5rem; color:#fff;">
                👋 <?= __('messages.welcome_back', ['name' => $user->name]) ?>
            </h1>

            <!-- Quick Stats -->
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1.25rem; margin-bottom:2rem;">
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; text-align:center;">
                    <div style="font-size:2rem; font-weight:900; color:var(--color-primary); margin-bottom:4px;"><?= $totalOrders ?></div>
                    <div style="font-size:0.85rem; color:var(--text-muted);"><?= __('messages.total_orders') ?></div>
                </div>
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; text-align:center;">
                    <div style="font-size:2rem; font-weight:900; color:var(--color-accent); margin-bottom:4px;"><?= $pendingOrders ?></div>
                    <div style="font-size:0.85rem; color:var(--text-muted);"><?= __('messages.pending_orders') ?></div>
                </div>
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; text-align:center;">
                    <div style="font-size:2rem; font-weight:900; color:var(--color-secondary); margin-bottom:4px;"><?= $completedOrders ?></div>
                    <div style="font-size:0.85rem; color:var(--text-muted);">طلبات مكتملة</div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
                    <h3 style="font-size:1.15rem; font-weight:800; color:#fff;"><?= __('messages.my_orders') ?></h3>
                    <a href="<?= url('account/orders') ?>" style="color:var(--color-primary); font-size:0.875rem; font-weight:700;"><?= __('messages.view_all') ?></a>
                </div>

                <?php if (empty($recentOrders)): ?>
                    <p style="color:var(--text-muted); padding:2rem 0; text-align:center;">لم تقم بإنشاء أي طلبات حتى الآن.</p>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; text-align:inherit; font-size:0.9rem;">
                            <thead>
                                <tr style="border-bottom:2px solid var(--border-subtle); color:var(--text-muted); font-size:0.8rem;">
                                    <th style="padding:0.75rem 0.5rem;"><?= __('messages.order_number') ?></th>
                                    <th style="padding:0.75rem 0.5rem;"><?= __('messages.order_date') ?></th>
                                    <th style="padding:0.75rem 0.5rem;"><?= __('messages.total') ?></th>
                                    <th style="padding:0.75rem 0.5rem;"><?= __('messages.status') ?></th>
                                    <th style="padding:0.75rem 0.5rem;"><?= __('messages.action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr style="border-bottom:1px solid var(--border-subtle);">
                                        <td style="padding:1rem 0.5rem; font-weight:700; color:var(--color-primary);"><?= $order->order_number ?></td>
                                        <td style="padding:1rem 0.5rem; color:var(--text-muted);"><?= date('Y-m-d', strtotime($order->created_at)) ?></td>
                                        <td style="padding:1rem 0.5rem; font-weight:800; color:#fff;"><?= format_price($order->total_amount, $order->currency_code) ?></td>
                                        <td style="padding:1rem 0.5rem;"><span class="status-pill <?= $order->getStatusBadgeClass() ?>"><?= strtoupper($order->status) ?></span></td>
                                        <td style="padding:1rem 0.5rem;">
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
</div>
@endsection
