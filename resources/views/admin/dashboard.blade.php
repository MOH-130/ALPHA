@extends('layouts.admin')

@section('content')
<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div>
            <div class="kpi-title"><?= __('admin.total_revenue') ?></div>
            <div class="kpi-value"><?= format_price($totalSales) ?></div>
        </div>
        <div class="kpi-icon green">💰</div>
    </div>

    <div class="kpi-card">
        <div>
            <div class="kpi-title"><?= __('admin.total_orders') ?></div>
            <div class="kpi-value"><?= $totalOrders ?></div>
        </div>
        <div class="kpi-icon blue">🛒</div>
    </div>

    <div class="kpi-card">
        <div>
            <div class="kpi-title"><?= __('admin.pending_orders') ?></div>
            <div class="kpi-value"><?= $pendingOrders ?></div>
        </div>
        <div class="kpi-icon amber">⏳</div>
    </div>

    <div class="kpi-card">
        <div>
            <div class="kpi-title"><?= __('admin.total_customers') ?></div>
            <div class="kpi-value"><?= $totalCustomers ?></div>
        </div>
        <div class="kpi-icon purple">👥</div>
    </div>

    <div class="kpi-card">
        <div>
            <div class="kpi-title"><?= __('admin.total_products') ?></div>
            <div class="kpi-value"><?= $totalProducts ?></div>
        </div>
        <div class="kpi-icon blue">📦</div>
    </div>

    <div class="kpi-card">
        <div>
            <div class="kpi-title"><?= __('admin.net_profit') ?></div>
            <div class="kpi-value"><?= format_price($estimatedProfit) ?></div>
        </div>
        <div class="kpi-icon green">📈</div>
    </div>
</div>

<!-- Sales Trends Chart -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">📈 إحصائيات المبيعات والطلبات (آخر 7 أيام)</h3>
    </div>
    <div class="admin-card-body" style="height: 320px; position:relative;">
        <canvas id="salesChart" 
            data-labels='<?= json_encode($chartLabels) ?>' 
            data-sales='<?= json_encode($chartSales) ?>' 
            data-orders='<?= json_encode($chartOrders) ?>'>
        </canvas>
    </div>
</div>

<!-- Bottom Row: Recent Orders & Top Products -->
<div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.5rem;">
    <!-- Recent Orders Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">🛒 <?= __('admin.recent_orders') ?></h3>
            <a href="<?= url('admin/orders') ?>" style="font-size:0.85rem; color:var(--admin-primary); font-weight:700;">عرض الكل &rarr;</a>
        </div>
        <div class="admin-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>العميل</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $ro): ?>
                            <tr>
                                <td style="font-weight:700; color:var(--admin-primary);"><?= $ro->order_number ?></td>
                                <td><?= htmlspecialchars($ro->customer_name) ?></td>
                                <td style="font-weight:800;"><?= format_price($ro->total_amount, $ro->currency_code) ?></td>
                                <td><span class="status-pill <?= $ro->getStatusBadgeClass() ?>"><?= strtoupper($ro->status) ?></span></td>
                                <td><a href="<?= url('admin/orders/' . $ro->id) ?>" class="btn btn-outline btn-sm">تفاصيل</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Products & Low Stock -->
    <div style="display:flex; flex-direction:column; gap:1.5rem;">
        <!-- Top Selling -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">🏆 الأكثر مبيعاً</h3>
            </div>
            <div class="admin-card-body" style="padding:1rem;">
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($topProducts as $tp): ?>
                        <div style="display:flex; align-items:center; justify-content:space-between; font-size:0.875rem;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <img src="<?= asset('uploads/products/' . ($tp->primary_image ?: 'default-product.png')) ?>" style="width:36px; height:36px; object-fit:cover; border-radius:6px;">
                                <span style="font-weight:700;"><?= is_rtl() ? $tp->name_ar : $tp->name_en ?></span>
                            </div>
                            <span style="font-weight:800; color:var(--admin-primary);"><?= $tp->sales_count ?> مبيعة</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">⚠️ تنبيهات نقص المخزون</h3>
            </div>
            <div class="admin-card-body" style="padding:1rem;">
                <?php if (empty($lowStockProducts)): ?>
                    <p style="font-size:0.85rem; color:var(--admin-text-muted); text-align:center;">المخزون بوضع ممتاز لجميع المنتجات.</p>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <?php foreach ($lowStockProducts as $lsp): ?>
                            <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.85rem; background:#fff1f2; padding:6px 10px; border-radius:6px; color:#9f1239;">
                                <span><?= is_rtl() ? $lsp->name_ar : $lsp->name_en ?></span>
                                <strong>متبقي: <?= $lsp->stock_quantity ?> فقط</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
@endsection
