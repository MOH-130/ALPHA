@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <h1 style="font-size:1.85rem; font-weight:800; margin-bottom:1.5rem; color:#fff;">
        ⚖️ <?= __('messages.compare_products') ?>
    </h1>

    <?php if (empty($compareItems)): ?>
        <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:4rem 2rem; text-align:center; box-shadow: var(--shadow-md);">
            <div style="font-size:3.5rem; margin-bottom:1rem;">⚖️</div>
            <h2 style="font-size:1.35rem; font-weight:700; margin-bottom:0.75rem; color:#fff;">قائمة المقارنة فارغة</h2>
            <p style="color:var(--text-muted); margin-bottom:2rem;">أضف عدة ألعاب وبكجات للمقارنة بين أسعارها ومميزاتها جنباً إلى جنب.</p>
            <a href="<?= url('shop') ?>" class="btn btn-primary btn-lg"><?= __('messages.continue_shopping') ?> 🎮</a>
        </div>
    <?php else: ?>
        <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; overflow-x:auto; box-shadow: var(--shadow-md);">
            <table style="width:100%; border-collapse:collapse; text-align:center; font-size:0.95rem; color:#cbd5e1;">
                <tr>
                    <td style="padding:1rem; font-weight:800; text-align:right; width:180px; background:var(--bg-input); color:#fff;">المنتج</td>
                    <?php foreach ($compareItems as $c): ?>
                        <?php $p = $c->product(); if (!$p) continue; ?>
                        <td style="padding:1.5rem; vertical-align:top; border-left:1px solid var(--border-color);">
                            <img src="<?= asset('uploads/products/' . ($p->primary_image ?: 'default-product.png')) ?>" style="width:120px; height:120px; object-fit:contain; margin:0 auto 10px; border-radius:8px; background:#070a12;">
                            <h4 style="font-size:1rem; font-weight:700; margin-bottom:8px; color:#fff;"><?= $p->getNameAttribute() ?></h4>
                            <div style="font-size:1.25rem; font-weight:900; color:var(--color-primary); margin-bottom:10px;"><?= format_price($p->getEffectivePrice()) ?></div>
                            <button class="btn btn-primary btn-sm btn-add-to-cart btn-block" data-product-id="<?= $p->id ?>">🛒 أضف للسلة</button>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <tr style="border-top:1px solid var(--border-color);">
                    <td style="padding:1rem; font-weight:800; text-align:right; background:var(--bg-input); color:#fff;">القسم</td>
                    <?php foreach ($compareItems as $c): ?>
                        <?php $p = $c->product(); if (!$p) continue; ?>
                        <td style="padding:1rem; border-left:1px solid var(--border-color);"><?= $p->category() ? $p->category()->getNameAttribute() : '-' ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr style="border-top:1px solid var(--border-color);">
                    <td style="padding:1rem; font-weight:800; text-align:right; background:var(--bg-input); color:#fff;">المنصة / الماركة</td>
                    <?php foreach ($compareItems as $c): ?>
                        <?php $p = $c->product(); if (!$p) continue; ?>
                        <td style="padding:1rem; border-left:1px solid var(--border-color);"><?= $p->brand() ? $p->brand()->getNameAttribute() : '-' ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr style="border-top:1px solid var(--border-color);">
                    <td style="padding:1rem; font-weight:800; text-align:right; background:var(--bg-input); color:#fff;">التقييم العام</td>
                    <?php foreach ($compareItems as $c): ?>
                        <?php $p = $c->product(); if (!$p) continue; ?>
                        <td style="padding:1rem; color:var(--color-accent); font-weight:700; border-left:1px solid var(--border-color);">⭐ <?= $p->averageRating() ?> / 5 (<?= $p->reviewsCount() ?>)</td>
                    <?php endforeach; ?>
                </tr>
                <tr style="border-top:1px solid var(--border-color);">
                    <td style="padding:1rem; font-weight:800; text-align:right; background:var(--bg-input); color:#fff;">حالة التوفر</td>
                    <?php foreach ($compareItems as $c): ?>
                        <?php $p = $c->product(); if (!$p) continue; ?>
                        <td style="padding:1rem; font-weight:700; color:<?= $p->isInStock() ? 'var(--color-secondary)' : 'var(--color-danger)' ?>; border-left:1px solid var(--border-color);">
                            <?= $p->isInStock() ? 'متوفر للتسليم الفوري' : 'نفذت الكمية' ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </table>
        </div>
    <?php endif; ?>
</div>
@endsection
