@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <!-- Breadcrumbs -->
    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="<?= url('/') ?>">الرئيسية</a> &gt; 
        <?php if ($product->category()): ?>
            <a href="<?= url('category/' . $product->category()->slug) ?>"><?= $product->category()->getNameAttribute() ?></a> &gt;
        <?php endif; ?>
        <span style="color: var(--color-primary); font-weight: 700;"><?= $product->getNameAttribute() ?></span>
    </div>

    <!-- Main Product Details Grid -->
    <div class="product-details-grid">
        
        <!-- Left: Image Gallery -->
        <div>
            <div class="product-gallery-main" style="position:relative; border:1px solid var(--border-color); border-radius:var(--card-radius); overflow:hidden; margin-bottom:1rem; background:#070a12; display:flex; align-items:center; justify-content:center;">
                <?php if ($product->getDiscountPercentage() > 0): ?>
                    <span class="badge-discount" style="position:absolute; top:16px; right:16px; font-size:0.875rem; z-index:2;">-<?= $product->getDiscountPercentage() ?>% خصم</span>
                <?php endif; ?>
                <img src="<?= asset('uploads/products/' . ($product->primary_image ?: 'default-product.png')) ?>" alt="<?= $product->getNameAttribute() ?>" style="width:100%; height:460px; object-fit:contain;">
            </div>

            <!-- Thumbnails -->
            <div style="display:flex; gap:10px; overflow-x:auto;">
                <div class="product-thumb-item active" data-src="<?= asset('uploads/products/' . ($product->primary_image ?: 'default-product.png')) ?>" style="width:80px; height:80px; border:2px solid var(--color-primary); border-radius:8px; overflow:hidden; cursor:pointer; background:#070a12;">
                    <img src="<?= asset('uploads/products/' . ($product->primary_image ?: 'default-product.png')) ?>" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <?php foreach ($images as $img): ?>
                    <div class="product-thumb-item" data-src="<?= asset('uploads/products/' . $img->image_path) ?>" style="width:80px; height:80px; border:1px solid var(--border-color); border-radius:8px; overflow:hidden; cursor:pointer; background:#070a12;">
                        <img src="<?= asset('uploads/products/' . $img->image_path) ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right: Product Information & Purchase Controls -->
        <div>
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
                <span style="font-size:0.85rem; font-weight:700; color:var(--color-primary); background:rgba(0, 210, 255, 0.15); border:1px solid rgba(0, 210, 255, 0.3); padding:4px 10px; border-radius:6px;">
                    🎮 <?= $product->category() ? $product->category()->getNameAttribute() : 'ألعاب أصلية' ?>
                </span>
                <span style="font-size:0.85rem; color:var(--text-muted);">
                    <?= __('messages.sku') ?>: <strong style="color:#fff;"><?= $product->sku ?></strong>
                </span>
            </div>

            <h1 style="font-size:1.85rem; font-weight:800; line-height:1.3; margin-bottom:1rem; color:#ffffff;">
                <?= $product->getNameAttribute() ?>
            </h1>

            <!-- Ratings & Reviews Header -->
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem; font-size:0.9rem;">
                <div style="color:var(--color-accent); font-weight:700;">
                    ⭐ <?= $product->averageRating() ?> / 5.0
                </div>
                <span style="color:var(--text-muted);">(<?= $product->reviewsCount() ?> تقييمات المشترين)</span>
                <span style="color:rgba(255,255,255,0.1);">|</span>
                <span style="color:var(--text-muted); font-size:0.85rem;">المنصة / الماركة: <strong style="color:#fff;"><?= $product->brand() ? $product->brand()->getNameAttribute() : 'Steam' ?></strong></span>
            </div>

            <!-- Price Box -->
            <div style="background:var(--bg-input); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.25rem 1.5rem; margin-bottom:1.75rem; display:flex; align-items:baseline; gap:1rem;">
                <span style="font-size:2.25rem; font-weight:900; color:#ffffff;">
                    <?= format_price($product->getEffectivePrice()) ?>
                </span>
                <?php if ($product->compare_price): ?>
                    <span style="font-size:1.2rem; color:var(--text-light); text-decoration:line-through;">
                        <?= format_price($product->compare_price) ?>
                    </span>
                    <span style="background:var(--color-danger); color:#fff; font-size:0.75rem; font-weight:700; padding:2px 8px; border-radius:4px;">
                        وفرت <?= format_price($product->compare_price - $product->getEffectivePrice()) ?>
                    </span>
                <?php endif; ?>
                <span style="font-size:0.8rem; color:var(--color-secondary); margin-right:auto; font-weight:700;">⚡ تسليم فوري وتلقائي</span>
            </div>

            <!-- Short Description -->
            <p style="color:var(--text-muted); line-height:1.7; margin-bottom:1.5rem; font-size:0.95rem;">
                <?= nl2br($product->getShortDescriptionAttribute()) ?>
            </p>

            <!-- Attributes / Variations (e.g. Size, Color, Edition) -->
            <?php foreach ($attributes as $attr): ?>
                <div style="margin-bottom:1.25rem;">
                    <label style="display:block; font-weight:700; font-size:0.9rem; margin-bottom:0.5rem; color:#fff;"><?= $attr->getNameAttribute() ?>:</label>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <?php foreach ($attr->getValues() as $idx => $val): ?>
                            <button type="button" class="btn btn-outline btn-sm attribute-choice-btn <?= $idx === 0 ? 'active' : '' ?>" style="border-radius:6px; font-weight:600;">
                                <?= $val ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Stock Availability -->
            <div style="margin-bottom:1.5rem; display:flex; align-items:center; gap:8px; font-size:0.9rem;">
                <span style="color:var(--text-muted);">حالة التوفر:</span>
                <?php if ($product->isInStock()): ?>
                    <span style="color:var(--color-secondary); font-weight:700; display:flex; align-items:center; gap:4px;">
                        <span style="width:8px; height:8px; background:var(--color-secondary); border-radius:50%; display:inline-block; box-shadow:0 0 8px var(--color-secondary);"></span>
                        متوفر للتسليم الفوري (<?= $product->stock_quantity ?> حساب / كود)
                    </span>
                <?php else: ?>
                    <span style="color:var(--color-danger); font-weight:700;">نفذت الكمية حالياً</span>
                <?php endif; ?>
            </div>

            <!-- Quantity & Actions -->
            <div style="display:flex; gap:1rem; align-items:center; margin-bottom:2rem; flex-wrap:wrap;">
                <div style="display:flex; align-items:center; border:1px solid var(--border-color); border-radius:var(--btn-radius); background:var(--bg-input);">
                    <button type="button" onclick="const i = document.getElementById('product-qty'); if (parseInt(i.value) > 1) i.value = parseInt(i.value) - 1;" style="border:none; background:transparent; color:#fff; padding:0.75rem 1rem; font-size:1.1rem; cursor:pointer;">-</button>
                    <input type="number" id="product-qty" value="1" min="1" max="<?= $product->stock_quantity ?>" style="width:50px; text-align:center; border:none; background:transparent; color:#fff; font-weight:700; font-size:1rem; outline:none;" readonly>
                    <button type="button" onclick="const i = document.getElementById('product-qty'); if (parseInt(i.value) < <?= $product->stock_quantity ?>) i.value = parseInt(i.value) + 1;" style="border:none; background:transparent; color:#fff; padding:0.75rem 1rem; font-size:1.1rem; cursor:pointer;">+</button>
                </div>

                <button type="button" class="btn btn-primary btn-lg" onclick="const q = document.getElementById('product-qty').value; fetch('/api/cart/add', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({product_id:<?= $product->id ?>, quantity:parseInt(q)})}).then(r=>r.json()).then(d=>{showToast(d.message, d.success?'success':'error'); if(d.success) updateCartBadge(d.cart_count);});" style="flex:1;">
                    🛒 <?= __('messages.add_to_cart') ?>
                </button>

                <button type="button" class="btn btn-outline btn-lg btn-toggle-wishlist" data-product-id="<?= $product->id ?>" title="<?= __('messages.add_to_wishlist') ?>">
                    ❤️
                </button>

                <button type="button" class="btn btn-outline btn-lg btn-toggle-compare" data-product-id="<?= $product->id ?>" title="<?= __('messages.add_to_compare') ?>">
                    ⚖️
                </button>
            </div>

            <!-- Social Share -->
            <div style="border-top:1px solid var(--border-color); padding-top:1.25rem; display:flex; align-items:center; gap:12px;">
                <span style="font-size:0.875rem; font-weight:700; color:var(--text-muted);"><?= __('messages.share_product') ?>:</span>
                <a href="https://api.whatsapp.com/send?text=<?= urlencode($product->getNameAttribute() . ' - ' . url('product/' . $product->slug)) ?>" target="_blank" class="btn btn-outline btn-sm">💬 واتساب</a>
                <a href="https://twitter.com/intent/tweet?text=<?= urlencode($product->getNameAttribute()) ?>&url=<?= urlencode(url('product/' . $product->slug)) ?>" target="_blank" class="btn btn-outline btn-sm">✖️ X</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('product/' . $product->slug)) ?>" target="_blank" class="btn btn-outline btn-sm">🌐 فيسبوك</a>
                <button type="button" onclick="navigator.clipboard.writeText(window.location.href); showToast('تم نسخ رابط المنتج بنجاح! 📋', 'success');" class="btn btn-outline btn-sm">🔗 نسخ الرابط</button>
            </div>
        </div>
    </div>

    <!-- Product Tabs (Description, Specifications, Shipping, Reviews) -->
    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:2rem; margin-bottom:3.5rem; box-shadow: var(--shadow-md);">
        <div style="display:flex; gap:1.5rem; border-bottom:2px solid var(--border-subtle); margin-bottom:1.75rem; overflow-x:auto;">
            <button type="button" class="tab-btn active" onclick="document.querySelectorAll('.tab-content').forEach(c=>c.style.display='none'); document.querySelectorAll('.tab-btn').forEach(b=>b.style.borderBottom='none'); this.style.borderBottom='3px solid var(--color-primary)'; document.getElementById('tab-desc').style.display='block';" style="background:transparent; border:none; padding:0.75rem 1rem; font-size:1.05rem; font-weight:700; cursor:pointer; font-family:inherit; color:#ffffff; border-bottom:3px solid var(--color-primary);">
                📖 <?= __('messages.product_description') ?>
            </button>
            <button type="button" class="tab-btn" onclick="document.querySelectorAll('.tab-content').forEach(c=>c.style.display='none'); document.querySelectorAll('.tab-btn').forEach(b=>b.style.borderBottom='none'); this.style.borderBottom='3px solid var(--color-primary)'; document.getElementById('tab-specs').style.display='block';" style="background:transparent; border:none; padding:0.75rem 1rem; font-size:1.05rem; font-weight:700; cursor:pointer; font-family:inherit; color:#94a3b8;">
                ⚙️ <?= __('messages.product_specifications') ?>
            </button>
            <button type="button" class="tab-btn" id="reviews-tab-btn" onclick="document.querySelectorAll('.tab-content').forEach(c=>c.style.display='none'); document.querySelectorAll('.tab-btn').forEach(b=>b.style.borderBottom='none'); this.style.borderBottom='3px solid var(--color-primary)'; document.getElementById('tab-reviews').style.display='block';" style="background:transparent; border:none; padding:0.75rem 1rem; font-size:1.05rem; font-weight:700; cursor:pointer; font-family:inherit; color:#94a3b8;">
                ⭐ <?= __('messages.reviews') ?> (<?= count($reviews) ?>)
            </button>
        </div>

        <!-- Tab 1: Full Description -->
        <div id="tab-desc" class="tab-content" style="line-height:1.8; color:#cbd5e1; font-size:1rem;">
            <?= nl2br($product->getDescriptionAttribute() ?: $product->getShortDescriptionAttribute()) ?>
        </div>

        <!-- Tab 2: Specifications -->
        <div id="tab-specs" class="tab-content" style="display:none;">
            <table style="width:100%; border-collapse:collapse; font-size:0.95rem; color:#cbd5e1;">
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:0.75rem; font-weight:700; width:220px; background:var(--bg-input); color:#fff;">المنصة / الشركة</td>
                    <td style="padding:0.75rem;"><?= $product->brand() ? $product->brand()->getNameAttribute() : 'Steam' ?></td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:0.75rem; font-weight:700; background:var(--bg-input); color:#fff;">القسم والتصنيف</td>
                    <td style="padding:0.75rem;"><?= $product->category() ? $product->category()->getNameAttribute() : 'ألعاب الكمبيوتر PC' ?></td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:0.75rem; font-weight:700; background:var(--bg-input); color:#fff;">رمز المنتج (SKU)</td>
                    <td style="padding:0.75rem;"><?= $product->sku ?></td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:0.75rem; font-weight:700; background:var(--bg-input); color:#fff;">طريقة التسليم</td>
                    <td style="padding:0.75rem; color:var(--color-secondary); font-weight:700;">تسليم فوري (كود تفعيل / بيانات حساب)</td>
                </tr>
            </table>
        </div>

        <!-- Tab 3: Reviews -->
        <div id="tab-reviews" class="tab-content" style="display:none;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:2.5rem;">
                <!-- Existing Reviews List -->
                <div>
                    <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:1.25rem; color:#fff;">تقييمات المشترين (<?= count($reviews) ?>)</h3>
                    <?php if (empty($reviews)): ?>
                        <p style="color:var(--text-muted);">لا توجد تقييمات لهذا المنتج حتى الآن. كن أول من يكتب مراجعة!</p>
                    <?php else: ?>
                        <div style="display:flex; flex-direction:column; gap:1.25rem;">
                            <?php foreach ($reviews as $rev): ?>
                                <div style="background:var(--bg-input); border:1px solid var(--border-color); border-radius:8px; padding:1.25rem;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                                        <strong style="color:#fff;">👤 <?= htmlspecialchars($rev->author_name) ?></strong>
                                        <span style="color:var(--color-accent); font-weight:700;">⭐ <?= $rev->rating ?> / 5</span>
                                    </div>
                                    <p style="color:#cbd5e1; font-size:0.925rem; line-height:1.6; margin:0;"><?= nl2br(htmlspecialchars($rev->comment)) ?></p>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.5rem;"><?= date('d M Y', strtotime($rev->created_at)) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Submit Review Form -->
                <div style="background:var(--bg-input); border:1px solid var(--border-color); border-radius:var(--card-radius); padding:1.5rem;">
                    <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:1rem; color:#fff;"><?= __('messages.leave_review') ?></h3>
                    <form action="<?= url('product/' . $product->slug . '/review') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label class="form-label"><?= __('messages.rating') ?>:</label>
                            <select name="rating" class="form-control" style="font-size:1rem; font-weight:700;">
                                <option value="5">⭐⭐⭐⭐⭐ 5 نجوم (ممتاز جداً)</option>
                                <option value="4">⭐⭐⭐⭐ 4 نجوم (جيد جداً)</option>
                                <option value="3">⭐⭐⭐ 3 نجوم (متوسط)</option>
                                <option value="2">⭐⭐ 2 نجوم (مقبول)</option>
                                <option value="1">⭐ 1 نجمة (ضعيف)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?= __('messages.your_review') ?>:</label>
                            <textarea name="comment" rows="4" class="form-control" placeholder="اكتب تفاصيل تجربتك مع المنتج..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <?= __('messages.submit_review') ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <section>
        <h2 style="font-size:1.6rem; font-weight:800; margin-bottom:1.5rem; color:#fff;"><?= __('messages.related_products') ?></h2>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $rel): ?>
                <div class="product-card">
                    <a href="<?= url('product/' . $rel->slug) ?>" class="product-image-box">
                        <img src="<?= asset('uploads/products/' . ($rel->primary_image ?: 'default-product.png')) ?>" alt="<?= $rel->getNameAttribute() ?>">
                    </a>
                    <div class="product-card-body">
                        <a href="<?= url('product/' . $rel->slug) ?>" class="product-title"><?= $rel->getNameAttribute() ?></a>
                        <div class="product-price-box">
                            <span class="current-price"><?= format_price($rel->getEffectivePrice()) ?></span>
                            <button class="btn btn-primary btn-sm btn-add-to-cart" data-product-id="<?= $rel->id ?>">🛒</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>
@endsection
