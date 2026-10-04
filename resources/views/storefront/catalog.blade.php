@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <!-- Breadcrumbs -->
    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="<?= url('/') ?>">الرئيسية</a> &gt; 
        <span style="color: var(--color-primary); font-weight: 700;"><?= $activeCategory ? $activeCategory->getNameAttribute() : ($activeBrand ? $activeBrand->getNameAttribute() : __('messages.shop')) ?></span>
    </div>

    <!-- Mobile Filter Toggle Button -->
    <button type="button" class="mobile-filter-toggle" onclick="toggleCatalogFilters()">
        <span>⚙️ <?= __('messages.filter') ?> والتصنيفات</span>
        <span id="filterToggleIcon">▼</span>
    </button>

    <div class="catalog-layout">
        <!-- Filters Sidebar -->
        <aside id="catalogFiltersSidebar" class="catalog-sidebar" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--card-radius); padding: 1.5rem; box-shadow: var(--shadow-sm);">
            <form action="<?= url('shop') ?>" method="GET">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid var(--border-subtle);">
                    <h3 style="font-size:1.1rem; font-weight:800; color:#fff;">⚙️ <?= __('messages.filter') ?></h3>
                    <a href="<?= url('shop') ?>" style="font-size:0.8rem; color:var(--color-primary); font-weight:700;"><?= __('messages.clear_filter') ?></a>
                </div>

                <!-- Search inside catalog -->
                <?php if (!empty($q)): ?>
                    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
                <?php endif; ?>

                <!-- Categories Filter -->
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="font-size:0.95rem; font-weight:700; margin-bottom:0.75rem; color:#fff;"><?= __('messages.categories') ?></h4>
                    <div style="display:flex; flex-direction:column; gap:8px; max-height:200px; overflow-y:auto;">
                        <?php foreach ($categories as $cat): ?>
                            <label style="display:flex; align-items:center; gap:8px; font-size:0.875rem; cursor:pointer; color:#cbd5e1;">
                                <input type="radio" name="category" value="<?= $cat->slug ?>" <?= ($activeCategory && $activeCategory->slug === $cat->slug) ? 'checked' : '' ?> onchange="this.form.submit()" style="accent-color:var(--color-primary);">
                                <span><?= $cat->getNameAttribute() ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Brands Filter -->
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="font-size:0.95rem; font-weight:700; margin-bottom:0.75rem; color:#fff;"><?= __('messages.brands') ?></h4>
                    <div style="display:flex; flex-direction:column; gap:8px; max-height:180px; overflow-y:auto;">
                        <?php foreach ($brands as $b): ?>
                            <label style="display:flex; align-items:center; gap:8px; font-size:0.875rem; cursor:pointer; color:#cbd5e1;">
                                <input type="radio" name="brand" value="<?= $b->slug ?>" <?= ($activeBrand && $activeBrand->slug === $b->slug) ? 'checked' : '' ?> onchange="this.form.submit()" style="accent-color:var(--color-primary);">
                                <span><?= $b->getNameAttribute() ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="font-size:0.95rem; font-weight:700; margin-bottom:0.75rem; color:#fff;"><?= __('messages.price_range') ?></h4>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                        <input type="number" name="min_price" placeholder="من" value="<?= htmlspecialchars($minPrice ?? '') ?>" style="padding:0.5rem; background:var(--bg-input); border:1px solid rgba(255,255,255,0.1); border-radius:6px; font-size:0.85rem; color:#fff;">
                        <input type="number" name="max_price" placeholder="إلى" value="<?= htmlspecialchars($maxPrice ?? '') ?>" style="padding:0.5rem; background:var(--bg-input); border:1px solid rgba(255,255,255,0.1); border-radius:6px; font-size:0.85rem; color:#fff;">
                    </div>
                </div>

                <!-- In Stock Checkbox -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:0.875rem; cursor:pointer; color:#cbd5e1;">
                        <input type="checkbox" name="in_stock" value="1" <?= ($_GET['in_stock'] ?? '') === '1' ? 'checked' : '' ?> style="accent-color:var(--color-primary);">
                        <span><?= __('messages.in_stock') ?> فقط</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-sm">
                    <?= __('messages.filter') ?>
                </button>
            </form>
        </aside>

        <!-- Products List & Sorting Header -->
        <section>
            <!-- Top Controls Bar -->
            <div class="catalog-toolbar" style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:0.85rem 1rem; display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.75rem; box-shadow: var(--shadow-sm);">
                <div style="font-size:0.85rem; color:var(--text-muted);">
                    عرض <strong style="color:#ffffff;"><?= $products['total'] ?></strong> منتج وبكج ألعاب
                </div>

                <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                    <!-- Sort Dropdown -->
                    <div style="display:flex; align-items:center; gap:6px; font-size:0.85rem;">
                        <span style="color:var(--text-muted);"><?= __('messages.sort_by') ?>:</span>
                        <select onchange="const u = new URL(window.location); u.searchParams.set('sort', this.value); window.location = u.href;" style="padding:0.4rem 0.6rem; border-radius:6px; background:var(--bg-input); color:#fff; border:1px solid rgba(255,255,255,0.1); font-family:inherit; font-size:0.825rem; cursor:pointer;">
                            <option value="newest" <?= ($sort ?? '') === 'newest' ? 'selected' : '' ?> style="background:#0f1626; color:#fff;"><?= __('messages.sort_newest') ?></option>
                            <option value="price_low" <?= ($sort ?? '') === 'price_low' ? 'selected' : '' ?> style="background:#0f1626; color:#fff;"><?= __('messages.sort_price_low') ?></option>
                            <option value="price_high" <?= ($sort ?? '') === 'price_high' ? 'selected' : '' ?> style="background:#0f1626; color:#fff;"><?= __('messages.sort_price_high') ?></option>
                            <option value="best_seller" <?= ($sort ?? '') === 'best_seller' ? 'selected' : '' ?> style="background:#0f1626; color:#fff;"><?= __('messages.sort_popular') ?></option>
                        </select>
                    </div>

                    <!-- View Toggle -->
                    <div style="display:flex; gap:4px;">
                        <a href="?view=grid" class="btn btn-outline btn-sm <?= ($viewMode ?? 'grid') === 'grid' ? 'active' : '' ?>">⊞</a>
                        <a href="?view=list" class="btn btn-outline btn-sm <?= ($viewMode ?? 'grid') === 'list' ? 'active' : '' ?>">☰</a>
                    </div>
                </div>
            </div>

            <!-- Products Grid or List -->
            <?php if (empty($products['data'])): ?>
                <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:4rem 2rem; text-align:center;">
                    <div style="font-size:3rem; margin-bottom:1rem;">🔍</div>
                    <h3 style="font-size:1.25rem; font-weight:700; margin-bottom:0.5rem; color:#fff;"><?= __('messages.no_products_found') ?></h3>
                    <p style="color:var(--text-muted); margin-bottom:1.5rem;">جرب تغيير خيارات الفلترة أو البحث بكلمات أخرى.</p>
                    <a href="<?= url('shop') ?>" class="btn btn-primary"><?= __('messages.clear_filter') ?></a>
                </div>
            <?php else: ?>
                <div class="<?= ($viewMode ?? 'grid') === 'list' ? 'products-list' : 'products-grid' ?>">
                    <?php foreach ($products['data'] as $prod): ?>
                        <div class="product-card">
                            <div class="product-badge-wrap">
                                <?php if ($prod->is_deal): ?>
                                    <span class="badge-deal">🔥 <?= __('messages.deals') ?></span>
                                <?php endif; ?>
                                <?php if ($prod->getDiscountPercentage() > 0): ?>
                                    <span class="badge-discount">-<?= $prod->getDiscountPercentage() ?>%</span>
                                <?php endif; ?>
                            </div>

                            <div class="product-card-actions">
                                <button class="card-action-btn btn-toggle-wishlist" data-product-id="<?= $prod->id ?>" title="<?= __('messages.add_to_wishlist') ?>">❤️</button>
                                <button class="card-action-btn btn-toggle-compare" data-product-id="<?= $prod->id ?>" title="<?= __('messages.add_to_compare') ?>">⚖️</button>
                            </div>

                            <a href="<?= url('product/' . $prod->slug) ?>" class="product-image-box">
                                <img src="<?= asset('uploads/products/' . ($prod->primary_image ?: 'default-product.png')) ?>" alt="<?= $prod->getNameAttribute() ?>" loading="lazy" decoding="async" width="260" height="260">
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

                <!-- Pagination -->
                <?php if ($products['last_page'] > 1): ?>
                    <div style="display:flex; justify-content:center; gap:8px; margin-top:2.5rem;">
                        <?php for ($p = 1; $p <= $products['last_page']; $p++): ?>
                            <a href="?page=<?= $p ?>" class="btn <?= $products['current_page'] == $p ? 'btn-primary' : 'btn-outline' ?> btn-sm">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</div>
@endsection
