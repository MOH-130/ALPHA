@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <h1 style="font-size:1.85rem; font-weight:800; margin-bottom:1.5rem; color:#fff;">
        🛒 <?= __('messages.shopping_cart') ?>
    </h1>

    <?php if (empty($cart)): ?>
        <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:4rem 2rem; text-align:center; box-shadow: var(--shadow-sm);">
            <div style="font-size:3.5rem; margin-bottom:1rem;">🛒</div>
            <h2 style="font-size:1.35rem; font-weight:700; margin-bottom:0.75rem; color:#fff;"><?= __('messages.cart_is_empty') ?></h2>
            <p style="color:var(--text-muted); margin-bottom:2rem;">تصفح تشكيلتنا الواسعة من أحدث ألعاب ستيم وبكجات الألعاب الحصرية.</p>
            <a href="<?= url('shop') ?>" class="btn btn-primary btn-lg"><?= __('messages.continue_shopping') ?> 🎮</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <!-- Cart Items Table -->
            <div class="table-responsive" style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; box-shadow: var(--shadow-sm);">
                <table style="width:100%; border-collapse:collapse; text-align:inherit;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border-subtle); color:var(--text-muted); font-size:0.85rem; text-transform:uppercase;">
                            <th style="padding:0.75rem 0.5rem;"><?= __('messages.product') ?></th>
                            <th style="padding:0.75rem 0.5rem; text-align:center;"><?= __('messages.price') ?></th>
                            <th style="padding:0.75rem 0.5rem; text-align:center;"><?= __('messages.quantity') ?></th>
                            <th style="padding:0.75rem 0.5rem; text-align:center;"><?= __('messages.subtotal') ?></th>
                            <th style="padding:0.75rem 0.5rem; text-align:center;"><?= __('messages.action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $key => $item): ?>
                            <tr style="border-bottom:1px solid var(--border-subtle);">
                                <td style="padding:1.25rem 0.5rem;">
                                    <div style="display:flex; align-items:center; gap:1rem;">
                                        <img src="<?= asset('uploads/products/' . ($item['image'] ?: 'default-product.png')) ?>" style="width:64px; height:64px; object-fit:cover; border-radius:8px; border:1px solid var(--border-color); background:#070a12;">
                                        <div>
                                            <a href="<?= url('product/' . $item['slug']) ?>" style="font-weight:700; color:#ffffff; font-size:0.95rem;"><?= is_rtl() ? $item['name_ar'] : $item['name_en'] ?></a>
                                            <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">SKU: <?= $item['sku'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:1.25rem 0.5rem; text-align:center; font-weight:700; color:#fff;">
                                    <?= format_price($item['price']) ?>
                                </td>
                                <td style="padding:1.25rem 0.5rem; text-align:center;">
                                    <div style="display:inline-flex; align-items:center; border:1px solid var(--border-color); border-radius:6px; background:var(--bg-input);">
                                        <button type="button" class="cart-qty-btn" data-key="<?= $key ?>" data-action="minus" style="border:none; background:transparent; color:#fff; padding:4px 10px; cursor:pointer; font-weight:700;">-</button>
                                        <input type="text" class="cart-qty-input" data-key="<?= $key ?>" value="<?= $item['quantity'] ?>" style="width:36px; text-align:center; border:none; background:transparent; color:#fff; font-weight:700;" readonly>
                                        <button type="button" class="cart-qty-btn" data-key="<?= $key ?>" data-action="plus" style="border:none; background:transparent; color:#fff; padding:4px 10px; cursor:pointer; font-weight:700;">+</button>
                                    </div>
                                </td>
                                <td style="padding:1.25rem 0.5rem; text-align:center; font-weight:800; color:var(--color-primary);">
                                    <?= format_price($item['price'] * $item['quantity']) ?>
                                </td>
                                <td style="padding:1.25rem 0.5rem; text-align:center;">
                                    <button type="button" class="btn-remove-cart-item" data-key="<?= $key ?>" style="background:transparent; border:none; color:var(--color-danger); cursor:pointer; font-size:1.25rem;" title="<?= __('messages.delete') ?>">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:1.5rem;">
                    <a href="<?= url('shop') ?>" class="btn btn-outline btn-sm">
                        &larr; <?= __('messages.continue_shopping') ?>
                    </a>
                </div>
            </div>

            <!-- Order Summary & Coupon Card -->
            <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.75rem; box-shadow: var(--shadow-sm);">
                <h3 style="font-size:1.25rem; font-weight:800; margin-bottom:1.5rem; border-bottom:1px solid var(--border-subtle); padding-bottom:0.75rem; color:#fff;">
                    <?= __('messages.order_summary') ?>
                </h3>

                <!-- Apply Coupon Box -->
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; font-weight:700; font-size:0.875rem; margin-bottom:0.5rem; color:#cbd5e1;"><?= __('messages.have_coupon') ?></label>
                    <div style="display:flex; gap:6px;">
                        <input type="text" id="coupon-code-input" placeholder="<?= __('messages.coupon_code') ?>" value="<?= $coupon['code'] ?? '' ?>" style="flex:1; padding:0.6rem 0.8rem; background:var(--bg-input); color:#fff; border:1px solid rgba(255,255,255,0.1); border-radius:var(--btn-radius); font-family:inherit; font-weight:700; text-transform:uppercase;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="const c = document.getElementById('coupon-code-input').value; fetch('/api/coupon/apply', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({coupon_code:c})}).then(r=>r.json()).then(d=>{showToast(d.message, d.success?'success':'error'); if(d.success) location.reload();});">
                            تطبيق
                        </button>
                    </div>
                    <?php if ($coupon): ?>
                        <div style="margin-top:6px; font-size:0.8rem; color:var(--color-secondary); font-weight:700;">
                            ✓ تم تطبيق الكوبون (<?= $coupon['code'] ?>)
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Financial Breakdown -->
                <div style="display:flex; flex-direction:column; gap:0.75rem; font-size:0.95rem; border-bottom:1px solid var(--border-subtle); padding-bottom:1.25rem; margin-bottom:1.25rem;">
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);"><?= __('messages.subtotal') ?>:</span>
                        <span style="font-weight:700; color:#fff;"><?= format_price($subtotal) ?></span>
                    </div>

                    <?php if ($discount > 0): ?>
                        <div style="display:flex; justify-content:space-between; color:var(--color-secondary);">
                            <span><?= __('messages.discount') ?>:</span>
                            <span style="font-weight:700;">-<?= format_price($discount) ?></span>
                        </div>
                    <?php endif; ?>

                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);"><?= __('messages.tax') ?>:</span>
                        <span style="font-weight:700; color:#fff;"><?= format_price($tax) ?></span>
                    </div>
                </div>

                <!-- Total Amount -->
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:1.75rem;">
                    <span style="font-size:1.15rem; font-weight:800; color:#fff;"><?= __('messages.total') ?>:</span>
                    <span style="font-size:1.75rem; font-weight:900; color:var(--color-primary);">
                        <?= format_price($total) ?>
                    </span>
                </div>

                <a href="<?= url('checkout') ?>" class="btn btn-primary btn-block btn-lg">
                    <?= __('messages.proceed_to_checkout') ?> 🚀
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
@endsection
