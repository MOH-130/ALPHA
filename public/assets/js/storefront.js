/**
 * ALPHA GAMING SHOP - STOREFRONT CLIENT INTERACTION JAVASCRIPT
 */

document.addEventListener('DOMContentLoaded', function() {
    initInteractiveCursor();
    initLiveSearch();
    initCartHandlers();
    initWishlistAndCompare();
    initProductGallery();
    initCopyButtons();
    initWelcomeModal();
});

// ================= 1. INTERACTIVE GAMER MOUSE CURSOR =================
function initInteractiveCursor() {
    // Only run on desktop pointer devices
    if (window.matchMedia('(pointer: coarse)').matches) return;

    let dot = document.querySelector('.custom-cursor-dot');
    let ring = document.querySelector('.custom-cursor-ring');

    if (!dot) {
        dot = document.createElement('div');
        dot.className = 'custom-cursor-dot';
        document.body.appendChild(dot);
    }

    if (!ring) {
        ring = document.createElement('div');
        ring.className = 'custom-cursor-ring';
        document.body.appendChild(ring);
    }

    let mouseX = -100;
    let mouseY = -100;
    let ringX = -100;
    let ringY = -100;
    let isVisible = false;

    window.addEventListener('mousemove', function(e) {
        mouseX = e.clientX;
        mouseY = e.clientY;

        if (!isVisible) {
            dot.style.opacity = '1';
            ring.style.opacity = '1';
            isVisible = true;
        }

        dot.style.left = `${mouseX}px`;
        dot.style.top = `${mouseY}px`;
    });

    document.addEventListener('mouseleave', function() {
        dot.style.opacity = '0';
        ring.style.opacity = '0';
        isVisible = false;
    });

    document.addEventListener('mouseenter', function() {
        dot.style.opacity = '1';
        ring.style.opacity = '1';
        isVisible = true;
    });

    // Lerp loop for buttery-smooth follower ring
    function renderCursor() {
        if (isVisible) {
            ringX += (mouseX - ringX) * 0.18;
            ringY += (mouseY - ringY) * 0.18;
            ring.style.left = `${ringX}px`;
            ring.style.top = `${ringY}px`;
        }
        requestAnimationFrame(renderCursor);
    }
    requestAnimationFrame(renderCursor);

    // Interactive Hover Elements (links, buttons, inputs, cards)
    const hoverSelectors = 'a, button, .btn, input, select, textarea, .product-card, .prop-card, .card-action-btn, .cat-pill, .product-thumb-item';

    document.addEventListener('mouseover', function(e) {
        const target = e.target.closest(hoverSelectors);
        if (target) {
            ring.classList.add('cursor-hover');
        }
    });

    document.addEventListener('mouseout', function(e) {
        const target = e.target.closest(hoverSelectors);
        if (target) {
            ring.classList.remove('cursor-hover');
        }
    });

    document.addEventListener('mousedown', function() {
        ring.classList.add('cursor-click');
    });

    document.addEventListener('mouseup', function() {
        ring.classList.remove('cursor-click');
    });
}

// ================= 2. WELCOME MODAL LOGIC =================
function initWelcomeModal() {
    const overlay = document.getElementById('welcome-modal-overlay');
    if (!overlay) return;

    // Check if user has dismissed previously
    const hasSeen = localStorage.getItem('alpha_welcome_seen');
    if (!hasSeen) {
        setTimeout(() => {
            openWelcomeModal();
        }, 500);
    }

    // Close on click outside modal
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeWelcomeModal();
        }
    });

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            closeWelcomeModal();
        }
    });
}

function openWelcomeModal() {
    const overlay = document.getElementById('welcome-modal-overlay');
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeWelcomeModal() {
    const overlay = document.getElementById('welcome-modal-overlay');
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function toggleWelcomeDontShow(checked) {
    if (checked) {
        localStorage.setItem('alpha_welcome_seen', 'true');
    } else {
        localStorage.removeItem('alpha_welcome_seen');
    }
}

// ================= 3. LIVE AUTO-COMPLETE SEARCH =================
function initLiveSearch() {
    const searchInput = document.querySelector('.search-input');
    const dropdown = document.querySelector('.search-results-dropdown');
    let debounceTimer = null;

    if (!searchInput || !dropdown) return;

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/api/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        let html = '';
                        data.results.forEach(item => {
                            html += `
                                <a href="${item.url}" class="search-result-item">
                                    <img src="${item.image}" class="search-result-thumb" alt="${item.title}">
                                    <div>
                                        <div style="font-weight:600; font-size:0.9rem; color:#fff;">${item.title}</div>
                                        <div style="color:var(--color-primary); font-weight:700; font-size:0.85rem;">${item.price_formatted}</div>
                                    </div>
                                </a>
                            `;
                        });
                        dropdown.innerHTML = html;
                        dropdown.style.display = 'block';
                    } else {
                        dropdown.innerHTML = '<div style="padding:1rem; color:var(--text-muted); text-align:center;">لا توجد نتائج مطابقة</div>';
                        dropdown.style.display = 'block';
                    }
                })
                .catch(err => console.error('Search error:', err));
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

// ================= 4. CART AJAX HANDLERS =================
function initCartHandlers() {
    // Quick Add to Cart
    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const qtyInput = document.querySelector(`#qty-${productId}`) || { value: 1 };
            const quantity = parseInt(qtyInput.value) || 1;

            fetch('/api/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    updateCartBadge(data.cart_count);
                } else {
                    showToast(data.message, 'error');
                }
            });
        });
    });

    // Cart Quantity Stepper in Cart Page
    document.querySelectorAll('.cart-qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemKey = this.getAttribute('data-key');
            const action = this.getAttribute('data-action');
            const input = document.querySelector(`.cart-qty-input[data-key="${itemKey}"]`);
            if (!input) return;

            let currentVal = parseInt(input.value) || 1;
            let newVal = action === 'plus' ? currentVal + 1 : Math.max(1, currentVal - 1);
            input.value = newVal;

            updateCartItem(itemKey, newVal);
        });
    });

    // Remove item from Cart
    document.querySelectorAll('.btn-remove-cart-item').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemKey = this.getAttribute('data-key');
            fetch('/api/cart/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ item_key: itemKey })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    });
}

function updateCartItem(itemKey, quantity) {
    fetch('/api/cart/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ item_key: itemKey, quantity: quantity })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast(data.message, 'error');
        }
    });
}

function updateCartBadge(count) {
    const badge = document.querySelector('.header-cart-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

// ================= 5. WISHLIST & COMPARE =================
function initWishlistAndCompare() {
    document.querySelectorAll('.btn-toggle-wishlist').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            fetch('/api/wishlist/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message, data.added ? 'success' : 'info');
                if (data.added) {
                    this.classList.add('active');
                } else {
                    this.classList.remove('active');
                }
            });
        });
    });

    document.querySelectorAll('.btn-toggle-compare').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            fetch('/api/compare/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message, data.added ? 'success' : 'info');
            });
        });
    });
}

// ================= 6. PRODUCT GALLERY =================
function initProductGallery() {
    const mainImg = document.querySelector('.product-gallery-main img');
    const thumbs = document.querySelectorAll('.product-thumb-item');

    thumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            thumbs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const targetSrc = this.getAttribute('data-src');
            if (mainImg && targetSrc) {
                mainImg.src = targetSrc;
            }
        });
    });
}

// ================= 7. COPY BUTTONS =================
function initCopyButtons() {
    document.querySelectorAll('.btn-copy').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetInput = document.querySelector(this.getAttribute('data-target'));
            if (targetInput) {
                targetInput.select();
                document.execCommand('copy');
                showToast('تم النسخ بنجاح! 📋', 'success');
            }
        });
    });
}

// ================= 8. TOAST NOTIFICATIONS =================
function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let icon = 'ℹ️';
    if (type === 'success') icon = '✅';
    if (type === 'error') icon = '❌';

    toast.innerHTML = `<span>${icon}</span> <span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}
