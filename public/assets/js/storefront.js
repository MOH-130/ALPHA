/**
 * ALPHA GAMING SHOP - STOREFRONT CLIENT INTERACTION JAVASCRIPT
 */

document.addEventListener('DOMContentLoaded', function() {
    initInteractiveCursor();
    initMobileNavigation();
    initLiveSearch();
    initCartHandlers();
    initWishlistAndCompare();
    initProductGallery();
    initCopyButtons();
    initWelcomeModal();
    initAlphaIntroVideo();
});

// ================= MOBILE NAVIGATION DRAWER =================
function toggleMobileNav() {
    const drawer = document.getElementById('mobileNavDrawer');
    const overlay = document.getElementById('mobileNavOverlay');
    const toggleBtn = document.getElementById('mobileMenuToggle');
    if (!drawer || !overlay) return;

    if (drawer.classList.contains('active')) {
        closeMobileNav();
    } else {
        openMobileNav();
    }
}

function openMobileNav() {
    const drawer = document.getElementById('mobileNavDrawer');
    const overlay = document.getElementById('mobileNavOverlay');
    const toggleBtn = document.getElementById('mobileMenuToggle');
    if (!drawer || !overlay) return;

    drawer.classList.add('active');
    overlay.classList.add('active');
    if (toggleBtn) toggleBtn.classList.add('active');
    document.body.classList.add('mobile-nav-open');
}

function closeMobileNav() {
    const drawer = document.getElementById('mobileNavDrawer');
    const overlay = document.getElementById('mobileNavOverlay');
    const toggleBtn = document.getElementById('mobileMenuToggle');
    if (!drawer || !overlay) return;

    drawer.classList.remove('active');
    overlay.classList.remove('active');
    if (toggleBtn) toggleBtn.classList.remove('active');
    document.body.classList.remove('mobile-nav-open');
}

function initMobileNavigation() {
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileNav();
        }
    });

    // Close on window resize if scaled up to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            closeMobileNav();
        }
    });
}

function toggleCatalogFilters() {
    const sidebar = document.getElementById('catalogFiltersSidebar');
    const icon = document.getElementById('filterToggleIcon');
    if (!sidebar) return;

    const isShown = sidebar.classList.contains('active');
    if (isShown) {
        sidebar.classList.remove('active');
        if (icon) icon.textContent = '▼';
    } else {
        sidebar.classList.add('active');
        if (icon) icon.textContent = '▲';
    }
}

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
    let isRafRunning = false;

    function renderCursor() {
        if (!isVisible) {
            isRafRunning = false;
            return;
        }

        const dx = mouseX - ringX;
        const dy = mouseY - ringY;
        ringX += dx * 0.22;
        ringY += dy * 0.22;

        ring.style.transform = `translate3d(${ringX - 18}px, ${ringY - 18}px, 0)`;

        // Keep loop running only while lerp is closing the gap
        if (Math.abs(dx) > 0.2 || Math.abs(dy) > 0.2) {
            requestAnimationFrame(renderCursor);
        } else {
            isRafRunning = false;
        }
    }

    function scheduleRingUpdate() {
        if (!isRafRunning && isVisible) {
            isRafRunning = true;
            requestAnimationFrame(renderCursor);
        }
    }

    window.addEventListener('mousemove', function(e) {
        mouseX = e.clientX;
        mouseY = e.clientY;

        if (!isVisible) {
            dot.style.opacity = '1';
            ring.style.opacity = '1';
            isVisible = true;
            ringX = mouseX;
            ringY = mouseY;
        }

        dot.style.transform = `translate3d(${mouseX - 4}px, ${mouseY - 4}px, 0)`;
        scheduleRingUpdate();
    }, { passive: true });

    document.addEventListener('mouseleave', function() {
        dot.style.opacity = '0';
        ring.style.opacity = '0';
        isVisible = false;
        isRafRunning = false;
    });

    document.addEventListener('mouseenter', function() {
        dot.style.opacity = '1';
        ring.style.opacity = '1';
        isVisible = true;
    });

    // Interactive Hover Elements (links, buttons, inputs, cards)
    const hoverSelectors = 'a, button, .btn, input, select, textarea, .product-card, .prop-card, .card-action-btn, .cat-pill, .product-thumb-item, .featured-cat-card, .brand-chip';

    document.addEventListener('mouseover', function(e) {
        const target = e.target.closest(hoverSelectors);
        if (target) {
            ring.classList.add('cursor-hover');
        }
    }, { passive: true });

    document.addEventListener('mouseout', function(e) {
        const target = e.target.closest(hoverSelectors);
        if (target) {
            ring.classList.remove('cursor-hover');
        }
    }, { passive: true });

    document.addEventListener('mousedown', function() {
        ring.classList.add('cursor-click');
    }, { passive: true });

    document.addEventListener('mouseup', function() {
        ring.classList.remove('cursor-click');
    }, { passive: true });
}

// ================= 2. WELCOME MODAL LOGIC =================
function initWelcomeModal() {
    const overlay = document.getElementById('welcome-modal-overlay');
    if (!overlay) return;

    // Check if user has dismissed previously
    const hasSeen = localStorage.getItem('alpha_welcome_seen');
    const videoOverlay = document.getElementById('alpha-video-modal-overlay');
    const isVideoDismissed = localStorage.getItem('alpha_video_dismissed') === 'true' || sessionStorage.getItem('alpha_video_session_played') === 'true';

    // Only auto-open welcome modal if video modal won't be shown
    if (!hasSeen && isVideoDismissed) {
        setTimeout(() => {
            if (!videoOverlay || !videoOverlay.classList.contains('active')) {
                openWelcomeModal();
            }
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

// ================= 9. LUXURY CYBER SOUND SYNTHESIZER (Web Audio API) =================
let audioCtx = null;
function getAudioContext() {
    if (!audioCtx && (window.AudioContext || window.webkitAudioContext)) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

function isSfxEnabled() {
    return localStorage.getItem('alpha_sfx_enabled') !== 'false';
}

function toggleSfx(btn) {
    const current = isSfxEnabled();
    const next = !current;
    localStorage.setItem('alpha_sfx_enabled', next ? 'true' : 'false');
    if (btn) {
        btn.innerHTML = next ? '🔊 <span style="font-size:0.75rem;">المؤثرات: مفعلة</span>' : '🔇 <span style="font-size:0.75rem;">المؤثرات: معطلة</span>';
    }
    if (next) {
        playCyberSound('success');
        showToast('تم تفعيل المؤثرات الصوتية الفاخرة! 🔊', 'success');
    } else {
        showToast('تم كتم المؤثرات الصوتية 🔇', 'info');
    }
}

function playCyberSound(type = 'click') {
    if (!isSfxEnabled()) return;
    try {
        const ctx = getAudioContext();
        if (!ctx) return;

        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        const now = ctx.currentTime;

        osc.connect(gain);
        gain.connect(ctx.destination);

        if (type === 'click') {
            osc.type = 'sine';
            osc.frequency.setValueAtTime(800, now);
            osc.frequency.exponentialRampToValueAtTime(1400, now + 0.06);
            gain.gain.setValueAtTime(0.08, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.06);
            osc.start(now);
            osc.stop(now + 0.06);
        } else if (type === 'hover') {
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(440, now);
            gain.gain.setValueAtTime(0.02, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.03);
            osc.start(now);
            osc.stop(now + 0.03);
        } else if (type === 'success') {
            osc.type = 'sine';
            osc.frequency.setValueAtTime(523.25, now); // C5
            osc.frequency.setValueAtTime(659.25, now + 0.08); // E5
            osc.frequency.setValueAtTime(783.99, now + 0.16); // G5
            gain.gain.setValueAtTime(0.1, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc.start(now);
            osc.stop(now + 0.35);
        } else if (type === 'access') {
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(320, now);
            osc.frequency.exponentialRampToValueAtTime(960, now + 0.15);
            gain.gain.setValueAtTime(0.07, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.18);
            osc.start(now);
            osc.stop(now + 0.18);
        }
    } catch (e) {
        // AudioContext not allowed or not supported
    }
}

// ================= 10. DEMO AUTO-FILL CREDENTIALS (REMOVED FOR PRODUCTION SECURITY) =================
function fillDemoCredentials(role) {
    // Hardcoded credentials removed in production to prevent sensitive data exposure
    return false;
}

// ================= 11. PASSWORD VISIBILITY TOGGLE =================
function togglePasswordVisibility(inputId, btn) {
    playCyberSound('click');
    const input = document.getElementById(inputId) || document.querySelector(`input[name="${inputId}"]`);
    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '👁️‍🗨️';
        btn.setAttribute('title', 'إخفاء كلمة المرور');
    } else {
        input.type = 'password';
        btn.innerHTML = '👁️';
        btn.setAttribute('title', 'إظهار كلمة المرور');
    }
}

// ================= 12. ALPHA CINEMA ENTRANCE VIDEO PLAYER & SOUND CONTROLS =================
function formatVideoTime(seconds) {
    if (isNaN(seconds) || seconds < 0) return '00:00';
    const m = Math.floor(seconds / 60);
    const s = Math.floor(seconds % 60);
    return `${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
}

function initAlphaIntroVideo() {
    const overlay = document.getElementById('alpha-video-modal-overlay');
    const video = document.getElementById('alphaStoreVideo');
    if (!overlay || !video) return;

    const scrubberTrack = document.getElementById('alphaScrubberTrack');
    const scrubberFill = document.getElementById('alphaScrubberFill');
    const currentTimeEl = document.getElementById('alphaCurrentTime');
    const durationTimeEl = document.getElementById('alphaDurationTime');
    const endScreen = document.getElementById('alphaVideoEndScreen');
    const dontShowCheck = document.getElementById('alphaDontShowVideoAgain');

    // Scrubber click seek
    if (scrubberTrack) {
        scrubberTrack.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!video.duration) return;
            const rect = scrubberTrack.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const fraction = Math.max(0, Math.min(1, clickX / rect.width));
            video.currentTime = fraction * video.duration;
            if (endScreen) endScreen.classList.remove('visible');
        });
    }

    // Video timeupdate
    video.addEventListener('timeupdate', function() {
        if (!video.duration) return;
        const pct = (video.currentTime / video.duration) * 100;
        if (scrubberFill) scrubberFill.style.width = `${pct}%`;
        if (currentTimeEl) currentTimeEl.textContent = formatVideoTime(video.currentTime);
    });

    // Loaded metadata
    video.addEventListener('loadedmetadata', function() {
        if (durationTimeEl) durationTimeEl.textContent = formatVideoTime(video.duration);
    });

    // Ended event
    video.addEventListener('ended', function() {
        if (endScreen) endScreen.classList.add('visible');
        const playIcon = document.getElementById('alphaPlayIcon');
        if (playIcon) playIcon.textContent = '▶';
        hideAlphaUnmuteBadge();
    });

    // Play event
    video.addEventListener('play', function() {
        const playIcon = document.getElementById('alphaPlayIcon');
        if (playIcon) playIcon.textContent = '⏸️';
        if (endScreen) endScreen.classList.remove('visible');
        hideAlphaPlayOverlay();
    });

    // Pause event
    video.addEventListener('pause', function() {
        const playIcon = document.getElementById('alphaPlayIcon');
        if (playIcon) playIcon.textContent = '▶';
        if (!video.ended) {
            showAlphaPlayOverlay();
        }
    });

    // Volume change event
    video.addEventListener('volumechange', function() {
        updateAlphaSoundUI();
    });

    // Close on click outside modal
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeAlphaVideoModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            closeAlphaVideoModal();
        }
    });

    // Check auto-launch on site entrance
    const isDismissed = localStorage.getItem('alpha_video_dismissed') === 'true';
    const isSessionPlayed = sessionStorage.getItem('alpha_video_session_played') === 'true';

    // Initialize "don't show again" checkbox state
    if (dontShowCheck) {
        dontShowCheck.checked = isDismissed;
        dontShowCheck.addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('alpha_video_dismissed', 'true');
            } else {
                localStorage.removeItem('alpha_video_dismissed');
            }
        });
    }

    if (!isDismissed && !isSessionPlayed) {
        // Auto-launch entrance video
        setTimeout(() => {
            openAlphaVideoModal(false);
        }, 350);
    }
}

function openAlphaVideoModal(force = false) {
    const overlay = document.getElementById('alpha-video-modal-overlay');
    const video = document.getElementById('alphaStoreVideo');
    if (!overlay || !video) return;

    // Record session played
    sessionStorage.setItem('alpha_video_session_played', 'true');

    // Close welcome modal if open
    closeWelcomeModal();

    // Show modal
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    const endScreen = document.getElementById('alphaVideoEndScreen');
    if (endScreen) endScreen.classList.remove('visible');

    if (video.currentTime >= video.duration || video.ended) {
        video.currentTime = 0;
    }

    if (force) {
        // User explicitly clicked "Watch Trailer" or Floating Launcher
        video.muted = false;
        video.volume = 1;
        hideAlphaUnmuteBadge();
        const playPromise = video.play();
        if (playPromise !== undefined) {
            playPromise.catch(err => {
                video.muted = true;
                video.play().then(() => showAlphaUnmuteBadge()).catch(() => showAlphaPlayOverlay());
            });
        }
    } else {
        // Entrance on site load: try sound first, fallback to muted autoplay seamlessly
        video.muted = false;
        video.volume = 1;
        const playPromise = video.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                hideAlphaUnmuteBadge();
            }).catch(err => {
                // Autoplay with sound restricted by browser policy
                video.muted = true;
                video.play().then(() => {
                    showAlphaUnmuteBadge();
                }).catch(e => {
                    showAlphaPlayOverlay();
                });
            });
        }
    }

    updateAlphaSoundUI();
}

function closeAlphaVideoModal() {
    const overlay = document.getElementById('alpha-video-modal-overlay');
    const video = document.getElementById('alphaStoreVideo');
    if (!overlay) return;

    if (video) {
        video.pause();
    }

    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

function toggleAlphaVideoPlay() {
    const video = document.getElementById('alphaStoreVideo');
    if (!video) return;

    const endScreen = document.getElementById('alphaVideoEndScreen');
    if (endScreen && endScreen.classList.contains('visible')) {
        replayAlphaVideo();
        return;
    }

    if (video.paused) {
        video.play();
        hideAlphaPlayOverlay();
    } else {
        video.pause();
        showAlphaPlayOverlay();
    }
}

function showAlphaPlayOverlay() {
    const overlay = document.getElementById('alphaPlayOverlay');
    if (overlay) {
        overlay.classList.add('visible');
    }
}

function hideAlphaPlayOverlay() {
    const overlay = document.getElementById('alphaPlayOverlay');
    if (overlay) {
        overlay.classList.remove('visible');
    }
}

function showAlphaUnmuteBadge() {
    const badge = document.getElementById('alphaVideoUnmuteBadge');
    if (badge) {
        badge.style.display = 'inline-flex';
    }
}

function hideAlphaUnmuteBadge() {
    const badge = document.getElementById('alphaVideoUnmuteBadge');
    if (badge) {
        badge.style.display = 'none';
    }
}

function unmuteAlphaVideo(e) {
    if (e) {
        e.stopPropagation();
    }
    const video = document.getElementById('alphaStoreVideo');
    if (!video) return;

    video.muted = false;
    video.volume = 1;
    hideAlphaUnmuteBadge();
    updateAlphaSoundUI();
    showToast('تم تشغيل الصوت الفاخر! 🔊', 'success');
}

function toggleAlphaVideoSound() {
    const video = document.getElementById('alphaStoreVideo');
    if (!video) return;

    if (video.muted || video.volume === 0) {
        video.muted = false;
        if (video.volume === 0) video.volume = 0.8;
        hideAlphaUnmuteBadge();
    } else {
        video.muted = true;
    }

    updateAlphaSoundUI();
}

function setAlphaVideoVolume(val) {
    const video = document.getElementById('alphaStoreVideo');
    if (!video) return;

    const v = parseFloat(val);
    video.volume = v;
    video.muted = (v === 0);
    if (v > 0) {
        hideAlphaUnmuteBadge();
    }
    updateAlphaSoundUI();
}

function updateAlphaSoundUI() {
    const video = document.getElementById('alphaStoreVideo');
    const soundIcon = document.getElementById('alphaSoundIcon');
    const volSlider = document.getElementById('alphaVolumeSlider');
    if (!video) return;

    const isMuted = video.muted || video.volume === 0;

    if (soundIcon) {
        if (isMuted) {
            soundIcon.textContent = '🔇';
        } else if (video.volume < 0.5) {
            soundIcon.textContent = '🔉';
        } else {
            soundIcon.textContent = '🔊';
        }
    }

    if (volSlider) {
        volSlider.value = isMuted ? 0 : video.volume;
    }
}

function replayAlphaVideo() {
    const video = document.getElementById('alphaStoreVideo');
    const endScreen = document.getElementById('alphaVideoEndScreen');
    if (!video) return;

    if (endScreen) endScreen.classList.remove('visible');
    video.currentTime = 0;
    video.play();
}

function toggleAlphaVideoFullscreen() {
    const stage = document.getElementById('alphaVideoStage');
    if (!stage) return;

    if (!document.fullscreenElement && !document.webkitFullscreenElement) {
        if (stage.requestFullscreen) {
            stage.requestFullscreen();
        } else if (stage.webkitRequestFullscreen) {
            stage.webkitRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
    }
}


