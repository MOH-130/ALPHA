@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <h1 style="font-size:1.85rem; font-weight:800; margin-bottom:1.5rem; color:#fff;">
        ❤️ <?= __('messages.my_wishlist') ?>
    </h1>

    <?php if (empty($wishlistItems)): ?>
        <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:4rem 2rem; text-align:center; box-shadow: var(--shadow-md);">
            <div style="font-size:3.5rem; margin-bottom:1rem;">❤️</div>
            <h2 style="font-size:1.35rem; font-weight:700; margin-bottom:0.75rem; color:#fff;">قائمة المفضلة فارغة</h2>
            <p style="color:var(--text-muted); margin-bottom:2rem;">احفظ منتجاتك وألعابك المفضلة لتتمكن من الرجوع إليها وشرائها في أي وقت لاحق.</p>
            <a href="<?= url('shop') ?>" class="btn btn-primary btn-lg"><?= __('messages.continue_shopping') ?> 🎮</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($wishlistItems as $item): ?>
                <?php $prod = $item->product(); if (!$prod) continue; ?>
                <div class="product-card">
                    <a href="<?= url('product/' . $prod->slug) ?>" class="product-image-box">
                        <img src="<?= asset('uploads/products/' . ($prod->primary_image ?: 'default-product.png')) ?>" alt="<?= $prod->getNameAttribute() ?>">
                    </a>
                    <div class="product-card-body">
                        <a href="<?= url('product/' . $prod->slug) ?>" class="product-title"><?= $prod->getNameAttribute() ?></a>
                        <div class="product-price-box">
                            <span class="current-price"><?= format_price($prod->getEffectivePrice()) ?></span>
                            <div style="display:flex; gap:6px;">
                                <button class="btn btn-primary btn-sm btn-add-to-cart" data-product-id="<?= $prod->id ?>">🛒</button>
                                <button class="btn btn-outline btn-sm btn-toggle-wishlist" data-product-id="<?= $prod->id ?>" onclick="location.reload();" title="حذف">🗑️</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
@endsection
