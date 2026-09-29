@extends('layouts.storefront')

@section('content')
<div class="container">
    <!-- Hero Banner Section -->
    <section class="hero-section">
        <div class="hero-banner" style="background: radial-gradient(circle at 75% 30%, rgba(0, 210, 255, 0.15), transparent 50%), linear-gradient(135deg, #0d1527 0%, #060911 100%);">
            <div class="hero-content">
                <span class="hero-badge">⚡ المتجر الجزائري الأول للألعاب الرقمية الفاخرة 🇩🇿</span>
                <h1 class="hero-title">
                    <?= is_rtl() ? 'عالم الألعاب الأصلية، بكجات Steam، واشتراكات الترفيه' : 'Original Games, Steam Packs & Digital Subscriptions' ?>
                </h1>
                <p class="hero-desc">
                    <?= is_rtl() ? 'احصل على أقوى ألعاب PC و PS5 و Xbox وبكجات ستيم الضخمة بأفضل الأسعار بالدينار الجزائري مع تسليم فوري ودفع مريح عبر بريدي موب BaridiMob و CCP!' : 'Get the biggest PC & Console titles and Steam packages with instant delivery in Algeria.' ?>
                </p>

                <!-- Live Trust Metrics Strip -->
                <div style="display:flex; gap:1.25rem; flex-wrap:wrap; margin-bottom:1.75rem; font-size:0.85rem; font-weight:700;">
                    <div style="display:inline-flex; align-items:center; gap:6px; color:#00ff88; background:rgba(0, 255, 136, 0.08); border:1px solid rgba(0, 255, 136, 0.25); border-radius:999px; padding:4px 12px;">
                        <span class="live-pulse-dot" style="width:8px; height:8px;"></span>
                        <span>+15,400 لاعب مفعل 🇩🇿</span>
                    </div>
                    <div style="display:inline-flex; align-items:center; gap:6px; color:#00d2ff; background:rgba(0, 210, 255, 0.08); border:1px solid rgba(0, 210, 255, 0.25); border-radius:999px; padding:4px 12px;">
                        <span>⚡</span>
                        <span>تسليم آلي فوري 24/7</span>
                    </div>
                    <div style="display:inline-flex; align-items:center; gap:6px; color:#ffd700; background:rgba(255, 215, 0, 0.08); border:1px solid rgba(255, 215, 0, 0.25); border-radius:999px; padding:4px 12px;">
                        <span>🛡️</span>
                        <span>ضمان ذهبي معتمد</span>
                    </div>
                </div>

                <div style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
                    <a href="<?= url('shop') ?>" class="btn btn-luxury-shimmer btn-lg" onclick="playCyberSound('access')">
                        <?= __('messages.shop') ?> 🎮
                    </a>
                    <a href="<?= url('shop?deals_only=1') ?>" class="btn btn-accent btn-lg" onclick="playCyberSound('click')">
                        <?= __('messages.deals') ?> 🔥
                    </a>
                    <button type="button" onclick="playCyberSound('click'); openWelcomeModal();" class="btn btn-outline btn-lg" style="border-color:rgba(0, 210, 255, 0.4); color:var(--color-primary);">
                        ✨ بوابة ومزايا VIP
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Value Propositions -->
    <section class="props-grid">
        <div class="prop-card">
            <div class="prop-icon">⚡</div>
            <div>
                <h4 style="font-weight:700; font-size:1rem; color:#fff;"><?= __('messages.fast_shipping') ?></h4>
                <p style="font-size:0.85rem; color:var(--text-muted);">تسليم فوري وتلقائي للحسابات والأكواد على واتساب</p>
            </div>
        </div>
        <div class="prop-card">
            <div class="prop-icon">🇩🇿</div>
            <div>
                <h4 style="font-weight:700; font-size:1rem; color:#fff;"><?= __('messages.secure_payment') ?></h4>
                <p style="font-size:0.85rem; color:var(--text-muted);">دفع محلي آمن عبر بريدي موب BaridiMob و CCP</p>
            </div>
        </div>
        <div class="prop-card">
            <div class="prop-icon">🛡️</div>
            <div>
                <h4 style="font-weight:700; font-size:1rem; color:#fff;"><?= __('messages.guaranteed_quality') ?></h4>
                <p style="font-size:0.85rem; color:var(--text-muted);">حسابات أصلية 100% مع ضمان كامل ودائم</p>
            </div>
        </div>
        <div class="prop-card">
            <div class="prop-icon">💬</div>
            <div>
                <h4 style="font-weight:700; font-size:1rem; color:#fff;"><?= __('messages.support_247') ?></h4>
                <p style="font-size:0.85rem; color:var(--text-muted);">فريق دعم فني متواجد 24/7 لمساعدتك في التفعيل</p>
            </div>
        </div>
    </section>

    <!-- Featured Categories Grid -->
    <section style="margin: 3.5rem 0;">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:1.75rem;">
            <div>
                <h2 style="font-size:1.75rem; font-weight:800; color:#fff;">📁 <?= __('messages.categories') ?></h2>
                <p style="color:var(--text-muted); font-size:0.95rem;">استكشف أقسام الألعاب، الحسابات، والبكجات المتخصصة</p>
            </div>
            <a href="<?= url('shop') ?>" style="color:var(--color-primary); font-weight:700; font-size:0.95rem;">
                <?= __('messages.view_all') ?> &rarr;
            </a>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:1.25rem;">
            <?php foreach ($featuredCategories as $cat): ?>
                <a href="<?= url('category/' . $cat->slug) ?>" style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem; text-align:center; transition:all 0.25s ease; display:flex; flex-direction:column; align-items:center;" onmouseover="this.style.borderColor='var(--color-primary)'; this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0, 210, 255, 0.15)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <div style="width:60px; height:60px; background:rgba(0, 210, 255, 0.1); color:var(--color-primary); border:1px solid rgba(0, 210, 255, 0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin-bottom:1rem;">
                        🎮
                    </div>
                    <h4 style="font-size:1rem; font-weight:700; color:#ffffff; margin-bottom:0.25rem;"><?= $cat->getNameAttribute() ?></h4>
                    <span style="font-size:0.8rem; color:var(--color-secondary); font-weight:600;"><?= $cat->productsCount() ?> منتج</span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Flash Deals Section with Countdown -->
    <?php if (!empty($deals)): ?>
    <section style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(249, 115, 22, 0.08) 100%), #0f1626; border: 1px solid rgba(239, 68, 68, 0.3); border-radius:var(--card-radius); padding:2.5rem; margin: 3.5rem 0; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:2rem;">
            <div>
                <span style="background:linear-gradient(135deg, #ef4444, #f97316); color:#fff; font-size:0.75rem; font-weight:800; padding:4px 10px; border-radius:6px; text-transform:uppercase;">🔥 LIMITED TIME PROMO</span>
                <h2 style="font-size:1.85rem; font-weight:900; margin-top:0.5rem; color:#ffffff;">🔥 <?= __('messages.flash_deals') ?></h2>
            </div>
            
            <!-- Countdown Timer -->
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="background:#172238; border:1px solid rgba(255,255,255,0.1); color:var(--color-accent); padding:8px 14px; border-radius:10px; font-weight:800; font-size:1.1rem; text-align:center;">
                    <span id="deal-days">14</span><div style="font-size:0.65rem; color:var(--text-muted);">يوم</div>
                </div>
                <div style="background:#172238; border:1px solid rgba(255,255,255,0.1); color:var(--color-accent); padding:8px 14px; border-radius:10px; font-weight:800; font-size:1.1rem; text-align:center;">
                    <span id="deal-hours">08</span><div style="font-size:0.65rem; color:var(--text-muted);">ساعة</div>
                </div>
                <div style="background:#172238; border:1px solid rgba(255,255,255,0.1); color:var(--color-accent); padding:8px 14px; border-radius:10px; font-weight:800; font-size:1.1rem; text-align:center;">
                    <span id="deal-mins">32</span><div style="font-size:0.65rem; color:var(--text-muted);">دقيقة</div>
                </div>
                <div style="background:#172238; border:1px solid rgba(255,255,255,0.1); color:var(--color-accent); padding:8px 14px; border-radius:10px; font-weight:800; font-size:1.1rem; text-align:center;">
                    <span id="deal-secs">45</span><div style="font-size:0.65rem; color:var(--text-muted);">ثانية</div>
                </div>
            </div>
        </div>

        <div class="products-grid">
            <?php foreach ($deals as $dealProduct): ?>
                <div class="product-card">
                    <div class="product-badge-wrap">
                        <span class="badge-deal">🔥 <?= __('messages.flash_deals') ?></span>
                        <?php if ($dealProduct->getDiscountPercentage() > 0): ?>
                            <span class="badge-discount">-<?= $dealProduct->getDiscountPercentage() ?>%</span>
                        <?php endif; ?>
                    </div>

                    <div class="product-card-actions">
                        <button class="card-action-btn btn-toggle-wishlist" data-product-id="<?= $dealProduct->id ?>" title="<?= __('messages.add_to_wishlist') ?>">❤️</button>
                        <button class="card-action-btn btn-toggle-compare" data-product-id="<?= $dealProduct->id ?>" title="<?= __('messages.add_to_compare') ?>">⚖️</button>
                    </div>

                    <a href="<?= url('product/' . $dealProduct->slug) ?>" class="product-image-box">
                        <img src="<?= asset('uploads/products/' . ($dealProduct->primary_image ?: 'default-product.png')) ?>" alt="<?= $dealProduct->getNameAttribute() ?>" loading="lazy">
                    </a>

                    <div class="product-card-body">
                        <span class="product-category"><?= $dealProduct->category() ? $dealProduct->category()->getNameAttribute() : '' ?></span>
                        <a href="<?= url('product/' . $dealProduct->slug) ?>" class="product-title"><?= $dealProduct->getNameAttribute() ?></a>
                        
                        <div class="product-rating">
                            <span>⭐ <?= $dealProduct->averageRating() ?></span>
                            <span style="color:var(--text-muted); font-size:0.75rem;">(<?= $dealProduct->reviewsCount() ?>)</span>
                        </div>

                        <div class="product-price-box">
                            <div>
                                <span class="current-price"><?= format_price($dealProduct->getEffectivePrice()) ?></span>
                                <?php if ($dealProduct->compare_price): ?>
                                    <span class="old-price"><?= format_price($dealProduct->compare_price) ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="btn btn-primary btn-sm btn-add-to-cart" data-product-id="<?= $dealProduct->id ?>">
                                🛒
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Best Sellers Section -->
    <section style="margin: 3.5rem 0;">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:1.75rem;">
            <div>
                <h2 style="font-size:1.75rem; font-weight:800; color:#fff;">🏆 <?= __('messages.best_sellers') ?></h2>
                <p style="color:var(--text-muted); font-size:0.95rem;">المنتجات والبكجات الأكثر طلباً ومبيعاً لدى مجتمع اللاعبين</p>
            </div>
            <a href="<?= url('shop?sort=best_seller') ?>" style="color:var(--color-primary); font-weight:700; font-size:0.95rem;">
                <?= __('messages.view_all') ?> &rarr;
            </a>
        </div>

        <div class="products-grid">
            <?php foreach ($bestSellers as $prod): ?>
                <div class="product-card">
                    <div class="product-badge-wrap">
                        <?php if ($prod->getDiscountPercentage() > 0): ?>
                            <span class="badge-discount">-<?= $prod->getDiscountPercentage() ?>%</span>
                        <?php endif; ?>
                    </div>

                    <div class="product-card-actions">
                        <button class="card-action-btn btn-toggle-wishlist" data-product-id="<?= $prod->id ?>" title="<?= __('messages.add_to_wishlist') ?>">❤️</button>
                        <button class="card-action-btn btn-toggle-compare" data-product-id="<?= $prod->id ?>" title="<?= __('messages.add_to_compare') ?>">⚖️</button>
                    </div>

                    <a href="<?= url('product/' . $prod->slug) ?>" class="product-image-box">
                        <img src="<?= asset('uploads/products/' . ($prod->primary_image ?: 'default-product.png')) ?>" alt="<?= $prod->getNameAttribute() ?>" loading="lazy">
                    </a>

                    <div class="product-card-body">
                        <span class="product-category"><?= $prod->category() ? $prod->category()->getNameAttribute() : '' ?></span>
                        <a href="<?= url('product/' . $prod->slug) ?>" class="product-title"><?= $prod->getNameAttribute() ?></a>
                        
                        <div class="product-rating">
                            <span>⭐ <?= $prod->averageRating() ?></span>
                            <span style="color:var(--text-muted); font-size:0.75rem;">(<?= $prod->reviewsCount() ?>)</span>
                        </div>

                        <div class="product-price-box">
                            <div>
                                <span class="current-price"><?= format_price($prod->getEffectivePrice()) ?></span>
                                <?php if ($prod->compare_price): ?>
                                    <span class="old-price"><?= format_price($prod->compare_price) ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="btn btn-primary btn-sm btn-add-to-cart" data-product-id="<?= $prod->id ?>">
                                🛒
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Verified Customer Reviews Showcase (From INSTA Poster) -->
    <section style="margin: 4.5rem 0; background: #0c1322; border: 1px solid var(--border-color); border-radius: var(--card-radius); padding: 3rem 2rem;">
        <div style="text-align: center; max-width: 600px; margin: 0 auto 2.5rem;">
            <span style="background: rgba(0, 255, 136, 0.15); color: var(--color-secondary); border: 1px solid rgba(0, 255, 136, 0.3); padding: 4px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 800;">
                ⭐ آراء وتقييمات زبائننا المعتمدين
            </span>
            <h2 style="font-size: 1.85rem; font-weight: 900; color: #ffffff; margin-top: 0.75rem;">
                ماذا يقول اللاعبون عن تجربة الشراء من Alpha Gaming Shop؟
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">
                أكثر من 500+ تقييم إيجابي مع سرعة تسليم وضمان أصالة دائم.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <div style="background: var(--bg-input); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                    <strong style="color:#ffffff; font-size: 1rem;">يوسف س. (الجزائر العاصمة)</strong>
                    <span style="color:#fbbf24; font-size:0.9rem;">⭐⭐⭐⭐⭐</span>
                </div>
                <p style="color: #cbd5e1; font-size: 0.88rem; line-height: 1.6;">
                    "أفضل متجر تعاملت معه في الجزائر! استلمت حساب ستيم مع أكثر من 17,000 لعبة في أقل من 5 دقائق، الدفع عبر بريدي موب كان سريع جداً."
                </p>
                <div style="margin-top: 1rem; font-size: 0.75rem; color: var(--color-secondary); font-weight: 700;">
                    ✓ مشتري معتمد (حساب ستيم +17,000 لعبة)
                </div>
            </div>

            <div style="background: var(--bg-input); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                    <strong style="color:#ffffff; font-size: 1rem;">أمين ب. (وهران)</strong>
                    <span style="color:#fbbf24; font-size:0.9rem;">⭐⭐⭐⭐⭐</span>
                </div>
                <p style="color: #cbd5e1; font-size: 0.88rem; line-height: 1.6;">
                    "اشتريت تفعيل لعبة Ghost Recon و RUST، الدعم الفني ساعدني في التفعيل خطوة بخطوة. ثقة واحترافية عالية جداً."
                </p>
                <div style="margin-top: 1rem; font-size: 0.75rem; color: var(--color-secondary); font-weight: 700;">
                    ✓ مشتري معتمد (Ghost Recon Breakpoint)
                </div>
            </div>

            <div style="background: var(--bg-input); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                    <strong style="color:#ffffff; font-size: 1rem;">محمد ر. (قسنطينة)</strong>
                    <span style="color:#fbbf24; font-size:0.9rem;">⭐⭐⭐⭐⭐</span>
                </div>
                <p style="color: #cbd5e1; font-size: 0.88rem; line-height: 1.6;">
                    "حجزت لعبة EA FC 27 بسعر ممتاز مقارنة بالبطاقات البنكية الدولية. خدمة ممتازة وأنصح الجميع بالتعامل معهم."
                </p>
                <div style="margin-top: 1rem; font-size: 0.75rem; color: var(--color-secondary); font-weight: 700;">
                    ✓ مشتري معتمد (EA SPORTS FC 27 Pre-order)
                </div>
            </div>
        </div>
    </section>

    <!-- Brands Showcase -->
    <?php if (theme_setting('show_brands_section', '1') === '1' && !empty($brands)): ?>
    <section style="margin: 4rem 0 2rem; border-top:1px solid var(--border-color); padding-top:3rem;">
        <h3 style="text-align:center; font-size:1.4rem; font-weight:800; margin-bottom:2rem; color:var(--text-muted);">
            ✨ منصات وشركات الألعاب المعتمدة
        </h3>
        <div style="display:flex; justify-content:center; align-items:center; gap:1.5rem; flex-wrap:wrap;">
            <?php foreach ($brands as $brand): ?>
                <a href="<?= url('brand/' . $brand->slug) ?>" style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--btn-radius); padding:0.85rem 1.75rem; font-weight:700; color:#e2e8f0; transition:all 0.2s ease;" onmouseover="this.style.borderColor='var(--color-primary)'; this.style.color='var(--color-primary)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='#e2e8f0'; this.style.transform='translateY(0)';">
                    🎮 <?= $brand->getNameAttribute() ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
// Simple countdown ticker
setInterval(() => {
    const s = document.getElementById('deal-secs');
    if (s) {
        let val = parseInt(s.textContent);
        s.textContent = val > 0 ? (val - 1 < 10 ? '0' + (val - 1) : val - 1) : '59';
    }
}, 1000);
</script>
@endsection
