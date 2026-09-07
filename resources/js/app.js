// ShopMart Interactive Features
document.addEventListener('DOMContentLoaded', () => {
    initCountdown();
    initHeroCarousel();
    initRecommendedTabs();
    initCartAndWishlist();
    initMobileNav();
    initTopMegaMenu();
    initSidebarFlyout();
    initCartPageInteractions();
});

/**
 * 1. Flash Sale Live Countdown Timer
 */
function initCountdown() {
    let totalSeconds = 4 * 3600 + 18 * 60 + 27; // 04:18:27

    const updateDisplay = () => {
        if (totalSeconds <= 0) {
            totalSeconds = 24 * 3600; // Reset to 24h
        }

        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        const pad = (num) => String(num).padStart(2, '0');

        document.querySelectorAll('.timer-hours').forEach(el => el.textContent = pad(hours));
        document.querySelectorAll('.timer-minutes').forEach(el => el.textContent = pad(minutes));
        document.querySelectorAll('.timer-seconds').forEach(el => el.textContent = pad(seconds));

        totalSeconds--;
    };

    updateDisplay();
    setInterval(updateDisplay, 1000);
}

/**
 * 2. Hero Banner Carousel
 */
function initHeroCarousel() {
    const carousels = document.querySelectorAll('[data-carousel]');
    
    carousels.forEach(carousel => {
        const slides = carousel.querySelectorAll('[data-carousel-slide]');
        const dots = carousel.querySelectorAll('[data-carousel-dot]');
        const prevBtn = carousel.querySelector('[data-carousel-prev]');
        const nextBtn = carousel.querySelector('[data-carousel-next]');
        let currentIndex = 0;
        let timer = null;

        const showSlide = (index) => {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;
            currentIndex = index;

            slides.forEach((slide, i) => {
                if (i === currentIndex) {
                    slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                    slide.classList.add('opacity-100', 'z-10');
                } else {
                    slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                    slide.classList.remove('opacity-100', 'z-10');
                }
            });

            dots.forEach((dot, i) => {
                if (i === currentIndex) {
                    dot.classList.add('bg-white', 'w-6');
                    dot.classList.remove('bg-white/40', 'w-2');
                } else {
                    dot.classList.remove('bg-white', 'w-6');
                    dot.classList.add('bg-white/40', 'w-2');
                }
            });
        };

        const nextSlide = () => showSlide(currentIndex + 1);
        const prevSlide = () => showSlide(currentIndex - 1);

        if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); resetTimer(); });
        if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); resetTimer(); });

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                showSlide(idx);
                resetTimer();
            });
        });

        const startTimer = () => {
            timer = setInterval(nextSlide, 5000);
        };

        const resetTimer = () => {
            clearInterval(timer);
            startTimer();
        };

        showSlide(0);
        startTimer();
    });
}

/**
 * 3. Recommended Products Category Tabs WITH SLIDING PILL INDICATOR & SMOOTH HEIGHT
 */
function initRecommendedTabs() {
    const container = document.getElementById('tab-nav-container');
    const indicator = document.getElementById('tab-indicator');
    const tabButtons = document.querySelectorAll('[data-filter-tab]');
    const wrapper = document.getElementById('recommended-products-wrapper');
    const grid = document.getElementById('recommended-products-grid');
    const productCards = document.querySelectorAll('[data-product-category]');

    let isAnimating = false;

    // Function to calculate and slide indicator pill
    const moveIndicator = (activeBtn) => {
        if (!indicator || !activeBtn || !container) return;
        indicator.style.left = `${activeBtn.offsetLeft}px`;
        indicator.style.width = `${activeBtn.offsetWidth}px`;
    };

    // Position indicator on load
    const initialActive = document.querySelector('[data-filter-tab].active-tab') || tabButtons[0];
    if (initialActive) {
        setTimeout(() => moveIndicator(initialActive), 60);
    }

    // Keep indicator position accurate on window resize
    window.addEventListener('resize', () => {
        const active = document.querySelector('[data-filter-tab].active-tab');
        if (active) moveIndicator(active);
    });

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.classList.contains('active-tab') || isAnimating) return;

            // Highlight Tab Buttons
            tabButtons.forEach(b => {
                b.classList.remove('active-tab', 'text-[#ea384c]', 'font-bold');
                b.classList.add('text-gray-600', 'font-medium');
            });
            btn.classList.add('active-tab', 'text-[#ea384c]', 'font-bold');
            btn.classList.remove('text-gray-600', 'font-medium');

            // Slide indicator smoothly
            moveIndicator(btn);

            const category = btn.getAttribute('data-filter-tab');

            if (!wrapper || !grid) return;

            isAnimating = true;

            // Step 1: Capture current height
            const currentHeight = wrapper.offsetHeight;
            wrapper.style.height = `${currentHeight}px`;

            // Step 2: Smoothly fade out existing cards
            grid.style.transition = 'opacity 0.15s ease';
            grid.style.opacity = '0.2';

            setTimeout(() => {
                // Step 3: Toggle cards visibility with staggered cascade animation
                let visibleCount = 0;
                productCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-product-category');
                    if (category === 'all' || cardCategory === category) {
                        card.style.display = '';
                        card.classList.remove('animate-card-appear');
                        void card.offsetWidth; // force reflow for animation restart
                        card.style.animationDelay = `${visibleCount * 35}ms`;
                        card.classList.add('animate-card-appear');
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                        card.classList.remove('animate-card-appear');
                        card.style.animationDelay = '0ms';
                    }
                });

                // Step 4: Measure target natural height
                wrapper.style.transition = 'none';
                wrapper.style.height = 'auto';
                const targetHeight = wrapper.offsetHeight;

                // Step 5: Restore current height and animate to target height smoothly
                wrapper.style.height = `${currentHeight}px`;
                void wrapper.offsetHeight; // force reflow

                wrapper.style.transition = 'height 0.38s cubic-bezier(0.25, 1, 0.5, 1)';
                wrapper.style.height = `${targetHeight}px`;

                // Fade grid back in
                grid.style.opacity = '1';

                // Step 6: Cleanup after animation completes
                setTimeout(() => {
                    wrapper.style.height = 'auto';
                    wrapper.style.transition = '';
                    isAnimating = false;
                }, 400);
            }, 150);
        });
    });
}

/**
 * 4. Cart Interactions + Toast System
 */
let cartCount = 3;

function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'toast-enter pointer-events-auto bg-white dark:bg-gray-900 border border-gray-100 shadow-xl rounded-2xl p-4 flex items-center gap-3 min-w-[280px] max-w-sm text-sm font-medium';
    
    const icon = type === 'success' 
        ? `<div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
           </div>`
        : `<div class="w-8 h-8 rounded-full bg-rose-100 text-[#ea384c] flex items-center justify-center shrink-0">
             <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
           </div>`;

    toast.innerHTML = `
        ${icon}
        <div class="flex-1 text-gray-800 dark:text-gray-100">${message}</div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Update all cart badge counters in the DOM
 * Rules: Cap display at '99+' if total items > 99
 */
function updateAllCartBadges(displayCount) {
    let text = String(displayCount);
    const num = parseInt(displayCount);
    if (!isNaN(num) && num > 99) {
        text = '99+';
    }
    const badges = document.querySelectorAll('.cart-badge-count, #header-cart-badge, [data-cart-badge]');
    badges.forEach(badge => {
        badge.textContent = text;
        badge.classList.remove('scale-125');
        void badge.offsetWidth; // trigger reflow
        badge.classList.add('scale-125', 'transition-transform');
        setTimeout(() => badge.classList.remove('scale-125'), 250);
    });
}

function fetchCartCount() {
    fetch('/cart/count', {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.display_count !== undefined) {
            updateAllCartBadges(data.display_count);
        }
    })
    .catch(() => {});
}

function initCartAndWishlist() {
    // Initial fetch of real count from backend
    fetchCartCount();

    // Global Add To Cart Listener for all current and dynamically added cards
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-add-to-cart]');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const productId = btn.getAttribute('data-product-id');
        const productName = btn.getAttribute('data-product-name') || 'Sản phẩm';

        // Check if quantity selector is present on product detail page
        let quantity = 1;
        const qtyInput = document.getElementById('pd-qty-input');
        if (qtyInput) {
            const val = parseInt(qtyInput.value);
            if (!isNaN(val) && val > 0) quantity = val;
        }

        // Check for active variant labels on product detail page
        let variant = null;
        const activeColor = document.querySelector('#pd-color-variants button.border-\\[\\#ea384c\\] span');
        const activeOption = document.querySelector('#pd-storage-variants button.border-\\[\\#ea384c\\]');
        if (activeColor || activeOption) {
            const parts = [];
            if (activeColor) parts.push(activeColor.textContent.trim());
            if (activeOption) parts.push(activeOption.textContent.trim());
            variant = parts.join(' - ');
        }

        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // If product ID exists, send to backend API
        if (productId) {
            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity,
                        variant: variant
                    })
                });

                const data = await response.json();
                if (data && data.success) {
                    updateAllCartBadges(data.display_count);
                    showToast(`Đã thêm <b>${productName}</b> vào giỏ hàng!`, 'success');
                } else {
                    showToast(data.message || 'Không thể thêm vào giỏ hàng', 'error');
                }
            } catch (err) {
                console.error('Add to cart error:', err);
                showToast(`Đã thêm <b>${productName}</b> vào giỏ hàng!`, 'success');
            }
        } else {
            showToast(`Đã thêm <b>${productName}</b> vào giỏ hàng!`, 'success');
        }
    });
}

/**
 * 4b. Cart & Checkout Interactive Page Logic
 */
function initCartPageInteractions() {
    const step1View = document.getElementById('cart-step-1-view');
    const step2View = document.getElementById('cart-step-2-view');
    const step3View = document.getElementById('cart-step-3-view');
    const mobileBottomBar = document.getElementById('mobile-sticky-checkout-bar');
    const mobileAppNav = document.getElementById('mobile-bottom-app-nav');
    const shopeeBottomWrapper = document.getElementById('shopee-bottom-wrapper');

    if (!step1View) return; // Not on cart page

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Switch: Step 1 -> Step 2 (Checkout)
    const proceedButtons = document.querySelectorAll(
        '#btn-proceed-checkout-desktop, #btn-mobile-checkout-submit, [data-btn-proceed-checkout]'
    );

    proceedButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            step1View.classList.add('hidden');
            if (shopeeBottomWrapper) shopeeBottomWrapper.classList.add('hidden');
            if (mobileBottomBar) mobileBottomBar.classList.add('hidden');
            if (mobileAppNav) mobileAppNav.classList.add('hidden');
            if (step2View) step2View.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Switch: Step 2 -> Step 1 (Back to Cart)
    const backButtons = document.querySelectorAll(
        '#btn-back-to-cart, #btn-edit-cart-items, [data-btn-back-to-cart]'
    );

    backButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (step2View) step2View.classList.add('hidden');
            step1View.classList.remove('hidden');
            if (shopeeBottomWrapper) shopeeBottomWrapper.classList.remove('hidden');
            if (mobileBottomBar) mobileBottomBar.classList.remove('hidden');
            if (mobileAppNav) mobileAppNav.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // ShopMart Points Toggle (-500.000đ)
    const pointsToggle = document.getElementById('toggle-reward-points');
    const pointsRow = document.getElementById('points-discount-row');
    const grandTotalEl = document.getElementById('billing-grand-total');
    const totalSavingsEl = document.getElementById('billing-total-savings');

    let baseTotal = 0;
    if (grandTotalEl) {
        baseTotal = parseInt(grandTotalEl.textContent.replace(/[^\d]/g, '')) || 0;
    }

    if (pointsToggle && pointsRow) {
        pointsToggle.addEventListener('change', () => {
            if (pointsToggle.checked) {
                pointsRow.classList.remove('hidden');
                if (grandTotalEl) {
                    const newTotal = Math.max(0, baseTotal - 500000);
                    grandTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(newTotal) + '₫';
                }
                if (totalSavingsEl) {
                    totalSavingsEl.textContent = '1.150.000₫';
                }
                showToast('Đã áp dụng 500.000 điểm ShopMart (-500.000₫)', 'success');
            } else {
                pointsRow.classList.add('hidden');
                if (grandTotalEl) {
                    grandTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(baseTotal) + '₫';
                }
                if (totalSavingsEl) {
                    totalSavingsEl.textContent = '650.000₫';
                }
            }
        });
    }

    // Place Order Button (Step 2 -> Step 3)
    const placeOrderBtn = document.getElementById('btn-place-order');
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            placeOrderBtn.disabled = true;
            placeOrderBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Đang xử lý đặt hàng...</span>
            `;

            const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value || 'wallet';
            const pointsUsed = pointsToggle?.checked ? 500000 : 0;

            try {
                const response = await fetch('/checkout/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        payment_method: selectedPayment,
                        points_used: pointsUsed
                    })
                });

                const result = await response.json();
                if (result && result.success) {
                    if (step2View) step2View.classList.add('hidden');
                    if (step3View) {
                        step3View.classList.remove('hidden');
                        const orderCodeEl = document.getElementById('success-order-code');
                        if (orderCodeEl) orderCodeEl.textContent = result.order_code;
                    }
                    updateAllCartBadges('0');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    showToast('🎉 Đặt hàng thành công!', 'success');
                } else {
                    showToast(result.message || 'Không thể đặt hàng, vui lòng thử lại', 'error');
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.innerHTML = `<span>Đặt hàng</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
                }
            } catch (err) {
                console.error(err);
                showToast('Có lỗi xảy ra khi xử lý đơn hàng', 'error');
                placeOrderBtn.disabled = false;
                placeOrderBtn.innerHTML = `<span>Đặt hàng</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
            }
        });
    }

    // Quantity Stepper +/-
    document.querySelectorAll('[data-qty-btn]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const itemId = btn.getAttribute('data-item-id');
            const action = btn.getAttribute('data-action');
            if (!itemId) return;

            btn.disabled = true;
            try {
                const response = await fetch(`/cart/item/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ action })
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
                btn.disabled = false;
            }
        });
    });

    // Remove single item (Trash icon)
    document.querySelectorAll('[data-remove-item]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const itemId = btn.getAttribute('data-item-id');
            if (!itemId) return;

            if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) return;

            btn.disabled = true;
            try {
                const response = await fetch(`/cart/item/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
                btn.disabled = false;
            }
        });
    });

    // =========================================================================
    // Rich Variant Selection Popup / Modal with Live Color-Image Preview
    // =========================================================================
    const variantModal = document.getElementById('variant-selection-modal');
    const modalBackdrop = document.getElementById('variant-modal-backdrop');
    const modalCard = document.getElementById('variant-modal-card');
    const modalCloseBtn = document.getElementById('close-variant-modal-btn');
    const modalCancelBtn = document.getElementById('btn-cancel-variant-modal');
    const modalConfirmBtn = document.getElementById('btn-confirm-variant-modal');
    const modalImg = document.getElementById('variant-modal-img');
    const modalTitle = document.getElementById('variant-modal-title');
    const modalPrice = document.getElementById('variant-modal-price');
    const modalOriginalPrice = document.getElementById('variant-modal-original-price');
    const modalSelectedText = document.getElementById('variant-modal-selected-text');
    const colorsSection = document.getElementById('variant-modal-colors-section');
    const colorsList = document.getElementById('variant-modal-colors-list');
    const activeColorLabel = document.getElementById('variant-modal-active-color-label');
    const optionsSection = document.getElementById('variant-modal-options-section');
    const optionsList = document.getElementById('variant-modal-options-list');
    const activeOptionLabel = document.getElementById('variant-modal-active-option-label');

    let currentVariantItemId = null;
    let currentTriggerBtn = null;
    let modalColors = [];
    let modalOptions = [];
    let activeSelectedColor = null;
    let activeSelectedColorImg = null;
    let activeSelectedOption = null;

    function updateModalSummaryText() {
        if (!modalSelectedText) return;
        const parts = [];
        if (activeSelectedColor) parts.push(activeSelectedColor);
        if (activeSelectedOption) parts.push(activeSelectedOption);
        modalSelectedText.textContent = parts.length > 0 ? parts.join(' | ') : 'Mặc định';
        if (activeColorLabel) activeColorLabel.textContent = activeSelectedColor || 'Chưa chọn';
        if (activeOptionLabel) activeOptionLabel.textContent = activeSelectedOption || 'Chưa chọn';
    }

    function openVariantModal(btn) {
        if (!variantModal || !modalCard) return;

        currentTriggerBtn = btn;
        currentVariantItemId = btn.getAttribute('data-item-id');
        const productName = btn.getAttribute('data-product-name') || 'Sản phẩm';
        const price = btn.getAttribute('data-product-price') || '';
        const originalPrice = btn.getAttribute('data-product-original-price') || '';
        const defaultImg = btn.getAttribute('data-default-img') || '';
        const currentVariant = btn.getAttribute('data-selected-variant') || '';

        try {
            modalColors = JSON.parse(btn.getAttribute('data-colors') || '[]');
        } catch (e) {
            modalColors = [];
        }

        try {
            modalOptions = JSON.parse(btn.getAttribute('data-options') || '[]');
        } catch (e) {
            modalOptions = [];
        }

        if (modalTitle) modalTitle.textContent = productName;
        if (modalPrice) modalPrice.textContent = price;

        if (modalOriginalPrice) {
            if (originalPrice) {
                modalOriginalPrice.textContent = originalPrice;
                modalOriginalPrice.classList.remove('hidden');
            } else {
                modalOriginalPrice.classList.add('hidden');
            }
        }

        // Identify currently selected color and option from saved variant string
        activeSelectedColor = null;
        activeSelectedColorImg = defaultImg;
        activeSelectedOption = null;

        if (modalColors.length > 0) {
            for (const c of modalColors) {
                if (currentVariant && currentVariant.includes(c.label)) {
                    activeSelectedColor = c.label;
                    activeSelectedColorImg = c.image || defaultImg;
                    break;
                }
            }
            if (!activeSelectedColor) {
                activeSelectedColor = modalColors[0].label;
                activeSelectedColorImg = modalColors[0].image || defaultImg;
            }
        }

        if (modalOptions.length > 0) {
            for (const opt of modalOptions) {
                if (currentVariant && currentVariant.includes(opt)) {
                    activeSelectedOption = opt;
                    break;
                }
            }
            if (!activeSelectedOption) {
                activeSelectedOption = modalOptions[0];
            }
        }

        // Set preview image
        if (modalImg) {
            modalImg.src = activeSelectedColorImg || defaultImg;
            modalImg.alt = productName;
        }

        // Render Colors
        if (colorsSection && colorsList) {
            if (modalColors.length === 0) {
                colorsSection.classList.add('hidden');
                colorsList.innerHTML = '';
            } else {
                colorsSection.classList.remove('hidden');
                colorsList.innerHTML = '';

                modalColors.forEach(colorItem => {
                    const isSelected = (colorItem.label === activeSelectedColor);
                    const colorBtn = document.createElement('button');
                    colorBtn.type = 'button';
                    colorBtn.className = `p-1.5 sm:p-2 rounded-xl border flex items-center gap-2 text-left transition-all cursor-pointer group ${
                        isSelected 
                            ? 'border-2 border-[#ea384c] bg-rose-50/60 shadow-3xs' 
                            : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50'
                    }`;

                    const imgThumb = colorItem.image || defaultImg;
                    colorBtn.innerHTML = `
                        <div class="w-8 h-8 rounded-lg overflow-hidden shrink-0 border border-gray-100 bg-gray-50">
                            <img src="${imgThumb}" alt="${colorItem.label}" class="w-full h-full object-cover">
                        </div>
                        <span class="text-[11px] sm:text-xs font-bold leading-tight truncate flex-1 ${
                            isSelected ? 'text-[#ea384c]' : 'text-gray-800'
                        }">
                            ${colorItem.label}
                        </span>
                        ${isSelected ? '<span class="w-2 h-2 rounded-full bg-[#ea384c] shrink-0 mr-1"></span>' : ''}
                    `;

                    colorBtn.addEventListener('click', () => {
                        activeSelectedColor = colorItem.label;
                        activeSelectedColorImg = colorItem.image || defaultImg;

                        // Visual flash/zoom on modal image to delight the user
                        if (modalImg) {
                            modalImg.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                modalImg.src = activeSelectedColorImg;
                                modalImg.style.transform = 'scale(1)';
                            }, 120);
                        }

                        // Re-render color buttons active state
                        colorsList.querySelectorAll('button').forEach(btnEl => {
                            btnEl.className = 'p-1.5 sm:p-2 rounded-xl border border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50 flex items-center gap-2 text-left transition-all cursor-pointer group';
                            const labelSpan = btnEl.querySelector('span');
                            if (labelSpan) {
                                labelSpan.className = 'text-[11px] sm:text-xs font-bold leading-tight truncate flex-1 text-gray-800';
                            }
                            const dot = btnEl.querySelector('.bg-\\[\\#ea384c\\]');
                            if (dot) dot.remove();
                        });

                        colorBtn.className = 'p-1.5 sm:p-2 rounded-xl border-2 border-[#ea384c] bg-rose-50/60 shadow-3xs flex items-center gap-2 text-left transition-all cursor-pointer group';
                        const labelSpan = colorBtn.querySelector('span');
                        if (labelSpan) {
                            labelSpan.className = 'text-[11px] sm:text-xs font-bold leading-tight truncate flex-1 text-[#ea384c]';
                        }
                        const dot = document.createElement('span');
                        dot.className = 'w-2 h-2 rounded-full bg-[#ea384c] shrink-0 mr-1';
                        colorBtn.appendChild(dot);

                        updateModalSummaryText();
                    });

                    colorsList.appendChild(colorBtn);
                });
            }
        }

        // Render Options / Storage / Sizes
        if (optionsSection && optionsList) {
            if (modalOptions.length === 0) {
                optionsSection.classList.add('hidden');
                optionsList.innerHTML = '';
            } else {
                optionsSection.classList.remove('hidden');
                optionsList.innerHTML = '';

                modalOptions.forEach(opt => {
                    const isSelected = (opt === activeSelectedOption);
                    const optBtn = document.createElement('button');
                    optBtn.type = 'button';
                    optBtn.className = `px-3.5 py-2 rounded-xl text-xs font-bold border transition-all cursor-pointer ${
                        isSelected 
                            ? 'border-2 border-blue-600 bg-blue-50/70 text-blue-600 shadow-3xs' 
                            : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50 text-gray-700'
                    }`;
                    optBtn.textContent = opt;

                    optBtn.addEventListener('click', () => {
                        activeSelectedOption = opt;

                        optionsList.querySelectorAll('button').forEach(b => {
                            b.className = 'px-3.5 py-2 rounded-xl text-xs font-bold border border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50 text-gray-700 transition-all cursor-pointer';
                        });

                        optBtn.className = 'px-3.5 py-2 rounded-xl text-xs font-bold border-2 border-blue-600 bg-blue-50/70 text-blue-600 shadow-3xs transition-all cursor-pointer';
                        updateModalSummaryText();
                    });

                    optionsList.appendChild(optBtn);
                });
            }
        }

        updateModalSummaryText();

        // Show Modal
        variantModal.classList.remove('opacity-0', 'pointer-events-none');
        variantModal.classList.add('opacity-100', 'pointer-events-auto');
        modalCard.classList.remove('translate-y-full', 'sm:scale-95');
        modalCard.classList.add('translate-y-0', 'sm:scale-100');
        document.body.classList.add('overflow-hidden');
    }

    function closeVariantModal() {
        if (!variantModal || !modalCard) return;
        variantModal.classList.remove('opacity-100', 'pointer-events-auto');
        variantModal.classList.add('opacity-0', 'pointer-events-none');
        modalCard.classList.remove('translate-y-0', 'sm:scale-100');
        modalCard.classList.add('translate-y-full', 'sm:scale-95');
        document.body.classList.remove('overflow-hidden');
    }

    // Attach listeners to all open-variant buttons
    document.querySelectorAll('[data-open-variant-popup]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            openVariantModal(btn);
        });
    });

    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeVariantModal);
    if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeVariantModal);
    if (modalBackdrop) modalBackdrop.addEventListener('click', closeVariantModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && variantModal && !variantModal.classList.contains('pointer-events-none')) {
            closeVariantModal();
        }
    });

    // Confirm Variant Selection
    if (modalConfirmBtn) {
        modalConfirmBtn.addEventListener('click', async () => {
            if (!currentVariantItemId) return;

            const parts = [];
            if (activeSelectedColor) parts.push(activeSelectedColor);
            if (activeSelectedOption) parts.push(activeSelectedOption);
            const combinedVariant = parts.length > 0 ? parts.join(' | ') : 'Mặc định';

            modalConfirmBtn.disabled = true;
            modalConfirmBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Đang cập nhật...</span>
            `;

            try {
                const response = await fetch(`/cart/item/${currentVariantItemId}/variant`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ variant: combinedVariant })
                });

                const data = await response.json();
                if (data && data.success) {
                    // Update trigger button display label
                    const displayEl = document.querySelector(`[data-variant-display="${currentVariantItemId}"]`);
                    if (displayEl) displayEl.textContent = data.variant;

                    if (currentTriggerBtn) {
                        currentTriggerBtn.setAttribute('data-selected-variant', data.variant);
                    }

                    // Update row thumbnail image if a color image is returned or picked
                    const rowImg = document.querySelector(`[data-cart-item-img="${currentVariantItemId}"]`);
                    const newImgUrl = data.image_url || activeSelectedColorImg;
                    if (rowImg && newImgUrl) {
                        rowImg.style.opacity = '0.5';
                        setTimeout(() => {
                            rowImg.src = newImgUrl;
                            rowImg.style.opacity = '1';
                        }, 150);
                    }

                    closeVariantModal();
                    showToast(`Đã đổi phân loại thành: <b>${data.variant}</b>`, 'success');
                } else {
                    showToast(data.message || 'Không thể đổi phân loại', 'error');
                }
            } catch (err) {
                console.error('Error updating variant:', err);
                showToast('Lỗi kết nối khi cập nhật phân loại', 'error');
            } finally {
                modalConfirmBtn.disabled = false;
                modalConfirmBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Xác nhận thay đổi</span>
                `;
            }
        });
    }

    // Remove selected items (desktop, mobile top, and sticky bottom)
    const removeSelectedButtons = document.querySelectorAll(
        '#btn-remove-selected, #btn-remove-selected-desktop, #btn-remove-selected-top, #btn-remove-selected-mobile, #btn-remove-selected-sticky'
    );
    removeSelectedButtons.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm đã chọn?')) return;

            try {
                const response = await fetch('/cart/remove-selected', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    // Select All Checkboxes (Desktop header, Mobile header, and Sticky Bottom bar)
    const selectAllCheckboxes = document.querySelectorAll(
        '[data-select-all-checkbox], #select-all-desktop-top, #select-all-mobile-top, #mobile-select-all-bottom, #select-all-top-checkbox, #select-all-mobile-checkbox'
    );

    selectAllCheckboxes.forEach(cb => {
        cb.addEventListener('change', async () => {
            const isSelected = cb.checked;
            try {
                const response = await fetch('/cart/toggle-select', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'all',
                        is_selected: isSelected
                    })
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    // Store Group Checkbox
    document.querySelectorAll('[data-store-checkbox]').forEach(cb => {
        cb.addEventListener('change', async () => {
            const storeId = cb.getAttribute('data-store-id');
            const isSelected = cb.checked;
            try {
                const response = await fetch('/cart/toggle-select', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'store',
                        store_id: storeId,
                        is_selected: isSelected
                    })
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    // Single Item Checkbox
    document.querySelectorAll('[data-item-checkbox]').forEach(cb => {
        cb.addEventListener('change', async () => {
            const itemId = cb.getAttribute('data-item-id');
            const isSelected = cb.checked;
            try {
                const response = await fetch('/cart/toggle-select', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'item',
                        item_id: itemId,
                        is_selected: isSelected
                    })
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });
}

/**
 * 5. Mobile Bottom Navigation active item switch
 */
function initMobileNav() {
    const navItems = document.querySelectorAll('[data-mobile-nav]');
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            navItems.forEach(i => {
                i.classList.remove('text-[#ea384c]');
                i.classList.add('text-gray-500');
            });
            item.classList.remove('text-gray-500');
            item.classList.add('text-[#ea384c]');
        });
    });
}

/**
 * 6. Top Navbar "Danh mục sản phẩm" Detailed Mega Dropdown Menu
 */
function initTopMegaMenu() {
    const btn = document.getElementById('top-mega-menu-btn');
    const dropdown = document.getElementById('top-mega-dropdown');
    const chevron = document.getElementById('top-mega-menu-chevron');

    if (!btn || !dropdown) return;

    let closeTimeout = null;

    const openMenu = () => {
        clearTimeout(closeTimeout);
        dropdown.classList.remove('hidden');
        if (chevron) chevron.classList.add('rotate-180');
    };

    const closeMenu = () => {
        closeTimeout = setTimeout(() => {
            dropdown.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }, 150);
    };

    btn.addEventListener('mouseenter', openMenu);
    btn.addEventListener('mouseleave', closeMenu);

    dropdown.addEventListener('mouseenter', openMenu);
    dropdown.addEventListener('mouseleave', closeMenu);

    btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (dropdown.classList.contains('hidden')) {
            openMenu();
        } else {
            dropdown.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });

    document.addEventListener('click', (e) => {
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });
}

/**
 * 7. Sidebar Flyout Sub-menu next to Hero Banner
 */
const categoryFlyoutData = {
    phone: {
        title: 'Điện thoại & Phụ kiện',
        subtitle: 'Khám phá thế giới công nghệ, kết nối mọi khoảnh khắc',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>`,
        topCards: [
            { title: 'iPhone', image: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=300&q=80' },
            { title: 'Tai nghe TWS', image: 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=300&q=80' },
            { title: 'Ốp lưng & Bao da', image: 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=300&q=80' },
            { title: 'Sạc nhanh GaN', image: 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=300&q=80' },
            { title: 'Pin dự phòng', image: 'https://images.unsplash.com/photo-1609592424367-e9ee1e765be1?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Apple', iconHtml: `<img src="/icons/brands/apple_light.svg" alt="Apple" class="w-3.5 h-3.5 object-contain shrink-0">` },
                    { name: 'Samsung', iconHtml: `<img src="/icons/brands/samsung_default.svg" alt="Samsung" class="w-5 h-auto object-contain shrink-0">` },
                    { name: 'Xiaomi', iconHtml: `<img src="/icons/brands/xiaomi_default.svg" alt="Xiaomi" class="w-3.5 h-3.5 rounded object-contain shrink-0">` },
                    { name: 'OPPO', iconHtml: `<img src="/icons/brands/oppo_default.svg" alt="OPPO" class="w-5 h-auto object-contain shrink-0">` },
                    { name: 'vivo', iconHtml: `<img src="/icons/brands/vivo_default.svg" alt="vivo" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Sony', iconHtml: `<img src="/icons/brands/sony_mono.svg" alt="Sony" class="w-4.5 h-auto object-contain shrink-0">` }
                ]
            },
            {
                heading: 'DÒNG SẢN PHẨM',
                items: [
                    { name: 'iPhone 15 / 15 Pro Max' },
                    { name: 'Samsung Galaxy S24 Ultra' },
                    { name: 'Xiaomi 14 / Redmi Note' },
                    { name: 'OPPO Find N3 & Reno11' },
                    { name: 'vivo X100 & V30 Pro' },
                    { name: 'Smartphone gập Flip / Fold' }
                ]
            },
            {
                heading: 'PHỤ KIỆN HOT',
                items: [
                    { name: 'Củ sạc nhanh 65W GaN' },
                    { name: 'Cáp sạc Type-C & Lightning' },
                    { name: 'Tai nghe chống ồn ANC' },
                    { name: 'Ốp lưng từ tính MagSafe' },
                    { name: 'Kính cường lực 9D' },
                    { name: 'Gimbal chống rung quay phim' }
                ]
            }
        ],
        deal: {
            badge: 'HOT DEAL',
            title: 'iPhone 15 Pro Max 256GB',
            desc: 'Hiệu năng vượt trội. Giảm thêm 3.500.000₫ hôm nay.',
            btnText: 'Mua ngay',
            image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=400&q=80'
        }
    },
    laptop: {
        title: 'Laptop & Thiết bị số',
        subtitle: 'Công cụ đắc lực cho công việc, học tập và sáng tạo không giới hạn',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>`,
        topCards: [
            { title: 'MacBook M3', image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=300&q=80' },
            { title: 'Laptop Gaming', image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=300&q=80' },
            { title: 'Màn hình 4K', image: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bàn phím cơ', image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=300&q=80' },
            { title: 'Chuột không dây', image: 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Apple MacBook', iconHtml: `<img src="/icons/brands/apple_light.svg" alt="Apple" class="w-3.5 h-3.5 object-contain shrink-0">` },
                    { name: 'ASUS ROG', iconHtml: `<img src="/icons/brands/asus_default.svg" alt="ASUS" class="w-5 h-auto object-contain shrink-0">` },
                    { name: 'Dell XPS', iconHtml: `<img src="/icons/brands/dell_default.svg" alt="Dell" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Lenovo Legion', iconHtml: `<img src="/icons/brands/lenovo_default.svg" alt="Lenovo" class="w-5 h-auto object-contain shrink-0">` },
                    { name: 'HP Pavilion', iconHtml: `<img src="/icons/brands/hp_default.svg" alt="HP" class="w-4 h-auto object-contain shrink-0">` }
                ]
            },
            {
                heading: 'DÒNG MÁY PHỔ BIẾN',
                items: [
                    { name: 'MacBook Pro / Air M3' },
                    { name: 'Laptop Gaming RTX 40 Series' },
                    { name: 'Laptop mỏng nhẹ văn phòng' },
                    { name: 'Laptop đồ họa & Kỹ thuật' },
                    { name: 'Laptop 2-in-1 cảm ứng' },
                    { name: 'Máy trạm đồ họa chuyên nghiệp' }
                ]
            },
            {
                heading: 'PHỤ KIỆN MÁY TÍNH',
                items: [
                    { name: 'Màn hình 2K/4K 144Hz' },
                    { name: 'Bàn phím cơ Custom' },
                    { name: 'Chuột công thái học' },
                    { name: 'Hub Type-C đa năng' },
                    { name: 'Balo laptop chống sốc' },
                    { name: 'Ổ cứng SSD di động' }
                ]
            }
        ],
        deal: {
            badge: 'APPLE OFFICIAL',
            title: 'MacBook Pro 14 M3 Chip',
            desc: 'Hiệu năng đỉnh cao cho sáng tạo nội dung & lập trình.',
            btnText: 'Khám phá ngay',
            image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=400&q=80'
        }
    },
    electronics: {
        title: 'Điện tử & Điện lạnh',
        subtitle: 'Tiện nghi đẳng cấp, nâng tầm cuộc sống hiện đại cho gia đình',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>`,
        topCards: [
            { title: 'Smart Tivi 4K', image: 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=300&q=80' },
            { title: 'Tủ lạnh Inverter', image: 'https://images.unsplash.com/photo-1571175443880-49e1d25b2bc5?auto=format&fit=crop&w=300&q=80' },
            { title: 'Máy giặt sấy', image: 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?auto=format&fit=crop&w=300&q=80' },
            { title: 'Điều hòa Inverter', image: 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=300&q=80' },
            { title: 'Loa Soundbar', image: 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Sony Bravia', iconHtml: `<img src="/icons/brands/sony_mono.svg" alt="Sony" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'LG Electronics', iconHtml: `<img src="/icons/brands/lg_default.svg" alt="LG" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Samsung Digital', iconHtml: `<img src="/icons/brands/samsung_default.svg" alt="Samsung" class="w-5 h-auto object-contain shrink-0">` }
                ]
            },
            {
                heading: 'THIẾT BỊ GIẢI TRÍ',
                items: [
                    { name: 'Tivi OLED / QLED 4K' },
                    { name: 'Loa Soundbar rạp hát' },
                    { name: 'Android TV Box 4K' },
                    { name: 'Máy chiếu mini thông minh' },
                    { name: 'Dàn âm thanh Karaoke' },
                    { name: 'Giá treo tivi di động' }
                ]
            },
            {
                heading: 'ĐIỆN LẠNH GIA ĐÌNH',
                items: [
                    { name: 'Tủ lạnh Side-by-side' },
                    { name: 'Máy giặt cửa trước Inverter' },
                    { name: 'Điều hòa tiết kiệm điện' },
                    { name: 'Máy sấy quần áo thông minh' },
                    { name: 'Máy lọc không khí HEPA' },
                    { name: 'Bình nước nóng trực tiếp' }
                ]
            }
        ],
        deal: {
            badge: 'GIẢM ĐẾN 45%',
            title: 'Smart TV QLED 65 inch 4K',
            desc: 'Đắm chìm không gian rạp chiếu phim đỉnh cao tại gia.',
            btnText: 'Xem ngay',
            image: 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=400&q=80'
        }
    },
    fashion: {
        title: 'Thời trang & Phụ kiện',
        subtitle: 'Khẳng định phong cách riêng với hàng ngàn mẫu mã dẫn đầu xu hướng',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`,
        topCards: [
            { title: 'Áo thun nam', image: 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=300&q=80' },
            { title: 'Váy đầm nữ', image: 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?auto=format&fit=crop&w=300&q=80' },
            { title: 'Giày Sneaker', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=300&q=80' },
            { title: 'Túi xách cao cấp', image: 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đồng hồ nam nữ', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU ĐÌNH ĐÁM',
                items: [
                    { name: 'Nike Sportswear', iconHtml: `<img src="/icons/brands/nike_mono.svg" alt="Nike" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Adidas Originals', iconHtml: `<img src="/icons/brands/adidas_mono.svg" alt="Adidas" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Zara Man & Woman', iconHtml: `<img src="/icons/brands/zara_default.svg" alt="Zara" class="w-5 h-auto object-contain shrink-0">` }
                ]
            },
            {
                heading: 'THỜI TRANG NAM & NỮ',
                items: [
                    { name: 'Áo thun basic & Graphic' },
                    { name: 'Váy đầm dự tiệc thanh lịch' },
                    { name: 'Áo sơ mi công sở cao cấp' },
                    { name: 'Quần Jean & Quần Kaki' },
                    { name: 'Áo khoác Blazer thời thượng' },
                    { name: 'Đồ mặc nhà & Pijama' }
                ]
            },
            {
                heading: 'GIÀY DÉP & PHỤ KIỆN',
                items: [
                    { name: 'Giày Sneaker hot trend' },
                    { name: 'Túi xách & Ví da cao cấp' },
                    { name: 'Đồng hồ cơ & Smartwatch' },
                    { name: 'Mắt kính chống tia UV' },
                    { name: 'Thắt lưng da thật' },
                    { name: 'Trang sức bạc & Vàng tây' }
                ]
            }
        ],
        deal: {
            badge: 'BỘ SƯU TẬP MỚI',
            title: 'Thu Đông Capsule 2026',
            desc: 'Giảm 30% toàn bộ bộ sưu tập mới phong cách Parisian.',
            btnText: 'Khám phá ngay',
            image: 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=400&q=80'
        }
    },
    home: {
        title: 'Nhà cửa & Đời sống',
        subtitle: 'Không gian sống ấm cúng, thiết bị gia dụng tiện nghi thông minh',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>`,
        topCards: [
            { title: 'Robot hút bụi', image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=300&q=80' },
            { title: 'Nồi chiên không dầu', image: 'https://images.unsplash.com/photo-1585515320310-259814833e62?auto=format&fit=crop&w=300&q=80' },
            { title: 'Chăn ga gối nệm', image: 'https://images.unsplash.com/photo-1584100936595-c0654b55a2e2?auto=format&fit=crop&w=300&q=80' },
            { title: 'Nồi chảo nhà bếp', image: 'https://images.unsplash.com/photo-1584990347449-399eb2e0b503?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đèn decor phòng', image: 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU GIA DỤNG',
                items: [
                    { name: 'Philips Domestic' },
                    { name: 'Lock&Lock Official' },
                    { name: 'Tefal France' },
                    { name: 'Sunhouse Vietnam' },
                    { name: 'Dreame Technology' },
                    { name: 'Xiaomi SmartHome' }
                ]
            },
            {
                heading: 'THIẾT BỊ NHÀ BẾP',
                items: [
                    { name: 'Nồi chiên không dầu điện tử' },
                    { name: 'Nồi cơm điện cao tần IH' },
                    { name: 'Máy xay sinh tố đa năng' },
                    { name: 'Máy ép chậm rau củ quả' },
                    { name: 'Bếp từ đôi cảm ứng Inverter' },
                    { name: 'Bộ nồi chảo inox nguyên khối' }
                ]
            },
            {
                heading: 'NỘI THẤT & PHÒNG NGỦ',
                items: [
                    { name: 'Robot hút bụi lau nhà tự động' },
                    { name: 'Bộ drap chăn ga Tencel 60s' },
                    { name: 'Máy lọc nước RO tạo kiềm' },
                    { name: 'Máy khuếch tán tinh dầu thơm' },
                    { name: 'Đèn cây trang trí phong cách Bắc Âu' },
                    { name: 'Kệ giày thông minh đa tầng' }
                ]
            }
        ],
        deal: {
            badge: 'DEAL HỜI GIA ĐÌNH',
            title: 'Robot hút bụi lau nhà Dreame',
            desc: 'Tự động giặt sấy giẻ lau và đổ rác. Giảm sốc 2.500.000₫.',
            btnText: 'Sắm ngay',
            image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=400&q=80'
        }
    },
    beauty: {
        title: 'Làm đẹp & Sức khỏe',
        subtitle: 'Chăm sóc sắc đẹp toàn diện, mỹ phẩm chính hãng cam kết 100%',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>`,
        topCards: [
            { title: 'Serum phục hồi', image: 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=300&q=80' },
            { title: 'Son môi lì', image: 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=300&q=80' },
            { title: 'Nước hoa chính hãng', image: 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=300&q=80' },
            { title: 'Kem chống nắng', image: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=300&q=80' },
            { title: 'Máy rửa mặt', image: 'https://images.unsplash.com/photo-1512290900672-1f0284f18d79?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU UY TÍN',
                items: [
                    { name: 'Estée Lauder Paris' },
                    { name: "L'Oréal Paris" },
                    { name: 'La Roche-Posay' },
                    { name: 'MAC Cosmetics' },
                    { name: 'Laneige Korea' },
                    { name: 'Innisfree Official' }
                ]
            },
            {
                heading: 'CHĂM SÓC DA (SKINCARE)',
                items: [
                    { name: 'Serum phục hồi B5 & HA' },
                    { name: 'Kem chống nắng quang phổ rộng' },
                    { name: 'Nước tẩy trang dịu nhẹ Micellar' },
                    { name: 'Kem dưỡng ẩm chuyên sâu' },
                    { name: 'Toner cân bằng độ pH' },
                    { name: 'Mặt nạ dưỡng trắng & Cấp ẩm' }
                ]
            },
            {
                heading: 'TRANG ĐIỂM & NƯỚC HOA',
                items: [
                    { name: 'Son kem lì chuẩn màu' },
                    { name: 'Cushion mỏng nhẹ tự nhiên' },
                    { name: 'Nước hoa nam / nữ EDP' },
                    { name: 'Bảng phấn mắt đa sắc' },
                    { name: 'Chì kẻ mày & Mascara kháng nước' },
                    { name: 'Bộ cọ trang điểm chuyên nghiệp' }
                ]
            }
        ],
        deal: {
            badge: 'MALL CHÍNH HÃNG',
            title: 'Serum Estée Lauder 50ml',
            desc: 'Tái sinh làn da ban đêm số 1 thế giới. Tặng quà trị giá 600K.',
            btnText: 'Mua ngay',
            image: 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=400&q=80'
        }
    },
    mom: {
        title: 'Mẹ & Bé',
        subtitle: 'Đồng hành cùng sự phát triển khỏe mạnh và thông minh của bé yêu',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        topCards: [
            { title: 'Tã bỉm cho bé', image: 'https://images.unsplash.com/photo-1519689680058-324335c77eba?auto=format&fit=crop&w=300&q=80' },
            { title: 'Sữa bột dinh dưỡng', image: 'https://images.unsplash.com/photo-1527661591475-527312dd65f5?auto=format&fit=crop&w=300&q=80' },
            { title: 'Xe đẩy em bé', image: 'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bình sữa tiệt trùng', image: 'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đồ chơi giáo dục', image: 'https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Merries Japan' },
                    { name: 'Moony Natural' },
                    { name: 'Meiji Official' },
                    { name: 'Aptamil Profutura' },
                    { name: 'Pigeon Japan' },
                    { name: 'Chicco Italy' }
                ]
            },
            {
                heading: 'TÃ BỈM & DINH DƯỠNG',
                items: [
                    { name: 'Tã dán & Tã quần Merries' },
                    { name: 'Sữa bột Meiji / Aptamil Úc' },
                    { name: 'Bột & Bánh ăn dặm hữu cơ' },
                    { name: 'Bình sữa cổ rộng silicone' },
                    { name: 'Máy hâm sữa & Tiệt trùng UV' },
                    { name: 'Men vi sinh BioGaia cho bé' }
                ]
            },
            {
                heading: 'ĐỒ DÙNG & ĐỒ CHƠI',
                items: [
                    { name: 'Xe đẩy em bé siêu nhẹ du lịch' },
                    { name: 'Địu em bé trợ lực thoáng khí' },
                    { name: 'Bộ quần áo sơ sinh sợi tre' },
                    { name: 'Đồ chơi xếp hình trí tuệ Lego' },
                    { name: 'Ghế ngồi ăn dặm nâng hạ' },
                    { name: 'Thảm nằm chơi chống trượt' }
                ]
            }
        ],
        deal: {
            badge: 'FESTIVAL MẸ & BÉ',
            title: 'Tã bỉm & Sữa bột chính hãng',
            desc: 'Mua 2 tặng 1 kèm quà tặng đồ chơi trị giá 300K.',
            btnText: 'Săn quà ngay',
            image: 'https://images.unsplash.com/photo-1519689680058-324335c77eba?auto=format&fit=crop&w=400&q=80'
        }
    },
    sports: {
        title: 'Thể thao & Du lịch',
        subtitle: 'Rèn luyện sức khỏe, bứt phá giới hạn và chinh phục mọi cung đường',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`,
        topCards: [
            { title: 'Giày chạy bộ', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đồ tập Gym Yoga', image: 'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=300&q=80' },
            { title: 'Vợt cầu lông', image: 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=300&q=80' },
            { title: 'Balo dã ngoại', image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bình giữ nhiệt', image: 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU THỂ THAO',
                items: [
                    { name: 'Nike Running' },
                    { name: 'Adidas Training' },
                    { name: 'Yonex Japan' },
                    { name: 'Under Armour' },
                    { name: 'The North Face' },
                    { name: 'Decathlon Sport' }
                ]
            },
            {
                heading: 'TRANG PHỤC & TẬP LUYỆN',
                items: [
                    { name: 'Giày chạy bộ êm ái đàn hồi' },
                    { name: 'Bộ quần áo tập Gym thấm mồ hôi' },
                    { name: 'Thảm tập Yoga định tuyến' },
                    { name: 'Vợt cầu lông & Tennis chuyên nghiệp' },
                    { name: 'Quả bóng đá thi đấu FIFA' },
                    { name: 'Dây kháng lực & Con lăn tập bụng' }
                ]
            },
            {
                heading: 'DÃ NGOẠI & DU LỊCH',
                items: [
                    { name: 'Lều cắm trại tự bung 4 người' },
                    { name: 'Balo phượt chống nước chuyên dụng' },
                    { name: 'Bàn ghế dã ngoại gấp gọn nhôm' },
                    { name: 'Bình nước giữ nhiệt 24h inox 316' },
                    { name: 'Đèn pin cắm trại đa chế độ' },
                    { name: 'Túi ngủ giữ ấm ngoài trời' }
                ]
            }
        ],
        deal: {
            badge: 'SALE THỂ THAO',
            title: 'Giày chạy bộ Nike Air Zoom',
            desc: 'Êm ái trợ lực từng bước chạy. Giảm 35% dịp cuối tuần.',
            btnText: 'Xem chi tiết',
            image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=400&q=80'
        }
    },
    books: {
        title: 'Sách & Văn phòng phẩm',
        subtitle: 'Mở rộng tri thức, thăng hoa cảm xúc và hỗ trợ công việc hiệu quả',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>`,
        topCards: [
            { title: 'Sách kinh tế', image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=300&q=80' },
            { title: 'Tiểu thuyết văn học', image: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=300&q=80' },
            { title: 'Sổ tay bìa da', image: 'https://images.unsplash.com/photo-1586075010923-2dd4570fb338?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bút ký cao cấp', image: 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?auto=format&fit=crop&w=300&q=80' },
            { title: 'Dụng cụ vẽ tranh', image: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'NHÀ XUẤT BẢN & ĐỐI TÁC',
                items: [
                    { name: 'Nhã Nam Official' },
                    { name: 'NXB Trẻ' },
                    { name: 'NXB Kim Đồng' },
                    { name: 'Alpha Books' },
                    { name: 'First News Trí Việt' },
                    { name: 'NXB Phụ Nữ Việt Nam' }
                ]
            },
            {
                heading: 'TỦ SÁCH TINH HOA',
                items: [
                    { name: 'Sách kinh tế & Quản trị kinh doanh' },
                    { name: 'Tiểu thuyết văn học kinh điển' },
                    { name: 'Sách tâm lý học & Phát triển bản thân' },
                    { name: 'Truyện tranh Manga & Comic' },
                    { name: 'Sách học ngoại ngữ IELTS / TOEIC' },
                    { name: 'Sách thiếu nhi & Nuôi dạy con' }
                ]
            },
            {
                heading: 'VĂN PHÒNG PHẨM & SỔ TAY',
                items: [
                    { name: 'Sổ tay còng Planner bìa da' },
                    { name: 'Bút máy & Bút ký kim loại cao cấp' },
                    { name: 'Giấy note & Bút dạ quang pastel' },
                    { name: 'Bút chì màu & Màu nước vẽ tranh' },
                    { name: 'Cặp tài liệu & Bìa lá lưu hồ sơ' },
                    { name: 'Máy tính bỏ túi khoa học Casio' }
                ]
            }
        ],
        deal: {
            badge: 'HỘI SÁCH ONLINE',
            title: 'Tủ sách Best-seller Nhã Nam',
            desc: 'Giảm đồng loạt đến 40% tặng kèm Bookmark kỷ niệm.',
            btnText: 'Xem sách ngay',
            image: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=400&q=80'
        }
    },
    auto: {
        title: 'Ô tô, Xe máy & Phụ kiện',
        subtitle: 'Chăm sóc và nâng cấp xế yêu an toàn trên mọi hành trình',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 14l-2 4m0 0l-2-4m2 4V6a2 2 0 00-2-2H9a2 2 0 00-2 2v12m0 0l-2-4m2 4l2-4"/></svg>`,
        topCards: [
            { title: 'Camera hành trình', image: 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=300&q=80' },
            { title: 'Mũ bảo hiểm', image: 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=300&q=80' },
            { title: 'Dầu nhớt động cơ', image: 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bơm lốp điện tử', image: 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=300&q=80' },
            { title: 'Khử mùi ô tô', image: 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Vietmap Official' },
                    { name: '70mai Xiaomi' },
                    { name: 'Castrol Vietnam' },
                    { name: 'Motul High Performance' },
                    { name: 'Royal Helmet' },
                    { name: 'Michelin Automotive' }
                ]
            },
            {
                heading: 'PHỤ KIỆN Ô TÔ HIỆN ĐẠI',
                items: [
                    { name: 'Camera hành trình 4K trước sau' },
                    { name: 'Bơm lốp điện tử tự ngắt thông minh' },
                    { name: 'Tẩu sạc nhanh & Giá đỡ sạc không dây' },
                    { name: 'Thảm lót sàn ô tô 6D cao cấp' },
                    { name: 'Bọc vô lăng da Napa êm ái' },
                    { name: 'Nước hoa & Tinh dầu kẹp cửa gió' }
                ]
            },
            {
                heading: 'XE MÁY & PHỤ KIỆN PHƯỢT',
                items: [
                    { name: 'Mũ bảo hiểm 3/4 & Fullface chuẩn DOT' },
                    { name: 'Dầu nhớt xe tay ga và xe số cao cấp' },
                    { name: 'Găng tay đi phượt chống nước' },
                    { name: 'Bộ dụng cụ sửa xe lưu động' },
                    { name: 'Khóa chống trộm xe máy báo động' },
                    { name: 'Đèn LED trợ sáng bi cầu mini' }
                ]
            }
        ],
        deal: {
            badge: 'PHỤ KIỆN XẾ HỘP',
            title: 'Camera hành trình 70mai 4K',
            desc: 'Ghi hình ban đêm sắc nét. Tặng thẻ nhớ tốc độ cao 64GB.',
            btnText: 'Lắp đặt ngay',
            image: 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=400&q=80'
        }
    },
    pets: {
        title: 'Thú cưng',
        subtitle: 'Dinh dưỡng trọn vẹn và đồ dùng êm ái yêu thương Boss cưng',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>`,
        topCards: [
            { title: 'Hạt cho mèo', image: 'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?auto=format&fit=crop&w=300&q=80' },
            { title: 'Thức ăn cho chó', image: 'https://images.unsplash.com/photo-1548767797-d8c844163c4c?auto=format&fit=crop&w=300&q=80' },
            { title: 'Nệm ngủ thú cưng', image: 'https://images.unsplash.com/photo-1541599540903-216a46ca1dc0?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đồ chơi cào móng', image: 'https://images.unsplash.com/photo-1545249390-6bdfa286032f?auto=format&fit=crop&w=300&q=80' },
            { title: 'Balo phi hành gia', image: 'https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU PET CARE',
                items: [
                    { name: 'Royal Canin France' },
                    { name: 'Whiskas Delicious' },
                    { name: 'Pedigree Pet' },
                    { name: 'Ciao Churu Japan' },
                    { name: 'Me-O Cat Food' },
                    { name: "Cat's Best Organic" }
                ]
            },
            {
                heading: 'DINH DƯỠNG THÚ CƯNG',
                items: [
                    { name: 'Hạt khô cho mèo con & Mèo lớn' },
                    { name: 'Hạt khô dinh dưỡng cho cún cưng' },
                    { name: 'Pate & Súp thưởng dinh dưỡng Ciao' },
                    { name: 'Gel dinh dưỡng & Vitamin tăng đề kháng' },
                    { name: 'Cỏ mèo tươi & Bánh thưởng sạch răng' },
                    { name: 'Sữa bột chuyên dụng cho thú cưng' }
                ]
            },
            {
                heading: 'ĐỒ DÙNG & VỆ SINH',
                items: [
                    { name: 'Cát vệ sinh đậu nành xả bồn cầu' },
                    { name: 'Bát ăn & Máy cấp nước tự động' },
                    { name: 'Balo phi hành gia thoáng khí' },
                    { name: 'Nệm ngủ bông êm ái mùa đông' },
                    { name: 'Sữa tắm mượt lông khử khuẩn' },
                    { name: 'Trụ cào móng & Cây leo Cat Tree' }
                ]
            }
        ],
        deal: {
            badge: 'FESTIVAL BOSS & SEN',
            title: 'Hạt & Pate Royal Canin',
            desc: 'Dinh dưỡng chuẩn chuyên gia. Mua 2 tặng kèm súp thưởng.',
            btnText: 'Mua cho Boss',
            image: 'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?auto=format&fit=crop&w=400&q=80'
        }
    },
    global: {
        title: 'Hàng quốc tế',
        subtitle: 'Mua sắm xuyên biên giới, hàng hiệu chính ngạch giao tận cửa',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        topCards: [
            { title: 'Mỹ phẩm Hàn Quốc', image: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=300&q=80' },
            { title: 'Gia dụng Nhật Bản', image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=300&q=80' },
            { title: 'Thực phẩm chức năng', image: 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bánh kẹo xách tay', image: 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?auto=format&fit=crop&w=300&q=80' },
            { title: 'Thời trang Ulzzang', image: 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'NGUỒN HÀNG NỔI BẬT',
                items: [
                    { name: 'Hàng nội địa Nhật Bản' },
                    { name: 'Mỹ phẩm chính hãng Hàn Quốc' },
                    { name: 'Thực phẩm chức năng Mỹ & Úc' },
                    { name: 'Thời trang thiết kế Quảng Châu' },
                    { name: 'Đồ điện tử mini Singapore' },
                    { name: 'Bánh kẹo Đài Loan & Thái Lan' }
                ]
            },
            {
                heading: 'MẶT HÀNG BÁN CHẠY',
                items: [
                    { name: 'Viên uống Collagen & Vitamin D3' },
                    { name: 'Son & Kem nền chuẩn Hàn' },
                    { name: 'Socola Bỉ & Bánh quy nhập khẩu' },
                    { name: 'Đồ gia dụng nội địa Nhật bền bỉ' },
                    { name: 'Quần áo & Váy hot trend Ulzzang' },
                    { name: 'Mô hình Anime & Blindbox chính hãng' }
                ]
            },
            {
                heading: 'CHÍNH SÁCH QUỐC TẾ',
                items: [
                    { name: 'Vận chuyển nhanh từ 5 - 7 ngày' },
                    { name: 'Miễn phí thủ tục thông quan' },
                    { name: 'Cam kết 100% hàng chuẩn ngoại' },
                    { name: 'Bảo hiểm đền bù thất lạc hàng' },
                    { name: 'Hỗ trợ kiểm tra hành trình đơn' },
                    { name: 'Đổi trả nếu hàng bị hư vỡ' }
                ]
            }
        ],
        deal: {
            badge: 'SIÊU HỘI XUYÊN BIÊN GIỚI',
            title: 'Hàng xách tay Mỹ & Nhật Bản',
            desc: 'Miễn phí vận chuyển quốc tế cho đơn hàng từ 250K.',
            btnText: 'Săn deal ngay',
            image: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=400&q=80'
        }
    },
    services: {
        title: 'Dịch vụ & Thẻ cào',
        subtitle: 'Nạp tiền điện thoại, thẻ game và thanh toán hóa đơn siêu tốc 24/7',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>`,
        topCards: [
            { title: 'Nạp thẻ điện thoại', image: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=300&q=80' },
            { title: 'Thẻ Game Online', image: 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?auto=format&fit=crop&w=300&q=80' },
            { title: 'Gói cước 4G/5G', image: 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=300&q=80' },
            { title: 'Vé máy bay', image: 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=300&q=80' },
            { title: 'Voucher ăn uống', image: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'NẠP ĐIỆN THOẠI & DATA',
                items: [
                    { name: 'Nạp tiền Viettel chiết khấu 5%' },
                    { name: 'Nạp tiền Mobifone chiết khấu 5%' },
                    { name: 'Nạp tiền Vinaphone chiết khấu 5%' },
                    { name: 'Gói Data 4G theo ngày ST15K' },
                    { name: 'Gói Data 4G tháng không giới hạn' },
                    { name: 'Nạp thẻ Vietnamobile & Wintel' }
                ]
            },
            {
                heading: 'THẺ GAME CHIẾT KHẤU CAO',
                items: [
                    { name: 'Thẻ Garena (Liên Quân, Free Fire)' },
                    { name: 'Thẻ Zing Xu (VNG Games)' },
                    { name: 'Thẻ Vcoin & VTC Pay' },
                    { name: 'Thẻ Gate FPT' },
                    { name: 'Thẻ Steam Wallet USD / VND' },
                    { name: 'Mã thẻ nạp App Store & Google Play' }
                ]
            },
            {
                heading: 'TIỆN ÍCH HÓA ĐƠN & VÉ',
                items: [
                    { name: 'Thanh toán tiền Điện toàn quốc' },
                    { name: 'Thanh toán tiền Nước sinh hoạt' },
                    { name: 'Nạp cước Internet & Truyền hình' },
                    { name: 'Đặt vé máy bay & Phòng khách sạn' },
                    { name: 'Vé xem phim CGV, Lotte, Beta' },
                    { name: 'Voucher ẩm thực lẩu nướng giảm 50%' }
                ]
            }
        ],
        deal: {
            badge: 'CHIẾT KHẤU ĐẾN 5.5%',
            title: 'Nạp tiền điện thoại & Thẻ Game',
            desc: 'Nhận mã thẻ liền tay trong 3 giây, bảo mật tuyệt đối 100%.',
            btnText: 'Nạp ngay',
            image: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=400&q=80'
        }
    }
};

// Aliases
categoryFlyoutData.pet = categoryFlyoutData.pets;
categoryFlyoutData.service = categoryFlyoutData.services;

// Color extraction cache to avoid recomputing on repeated hovers
const cardColorCache = new Map();

/**
 * Extracts the dominant/background edge color of an image via an offscreen canvas.
 * Samples perimeter pixels (top, bottom, left, right edges) where the image's background is situated.
 */
function extractImageDominantEdgeColor(img) {
    if (!img.naturalWidth || !img.naturalHeight) return null;

    const imgSrc = img.currentSrc || img.src;
    if (cardColorCache.has(imgSrc)) {
        return cardColorCache.get(imgSrc);
    }

    try {
        const canvas = document.createElement('canvas');
        const w = 32;
        const h = 32;
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        if (!ctx) return null;

        ctx.drawImage(img, 0, 0, w, h);
        const data = ctx.getImageData(0, 0, w, h).data;

        let rSum = 0;
        let gSum = 0;
        let bSum = 0;
        let count = 0;

        const sample = (x, y) => {
            const i = (y * w + x) * 4;
            const a = data[i + 3];
            if (a > 40) { // ignore transparent pixels
                rSum += data[i];
                gSum += data[i + 1];
                bSum += data[i + 2];
                count++;
            }
        };

        // Sample perimeter rows and columns (corners and outer edges)
        for (let x = 0; x < w; x++) {
            sample(x, 0);
            sample(x, 1);
            sample(x, h - 2);
            sample(x, h - 1);
        }
        for (let y = 2; y < h - 2; y++) {
            sample(0, y);
            sample(1, y);
            sample(w - 2, y);
            sample(w - 1, y);
        }

        if (count === 0) return null;

        const r = Math.round(rSum / count);
        const g = Math.round(gSum / count);
        const b = Math.round(bSum / count);
        const lum = (0.299 * r + 0.587 * g + 0.114 * b);

        const result = {
            r, g, b, lum,
            css: `rgb(${r}, ${g}, ${b})`
        };

        cardColorCache.set(imgSrc, result);
        return result;
    } catch (err) {
        // Safe fallback if cross-origin canvas security restriction occurs
        return null;
    }
}

/**
 * Dynamically applies smart dominant color sampling to top card containers,
 * ensuring image edge feathering blends seamlessly into the top half background.
 */
function initSmartCardBlend(container) {
    if (!container) return;
    const boxes = container.querySelectorAll('[data-smart-card-box]');

    boxes.forEach(box => {
        const img = box.querySelector('[data-smart-card-img]');
        const underlay = box.querySelector('[data-smart-card-underlay]');
        if (!img) return;

        const applyColor = () => {
            const color = extractImageDominantEdgeColor(img);
            if (color) {
                // Determine if photo has a white / light neutral background
                const isLightBackground = color.lum > 215 || (color.r > 220 && color.g > 220 && color.b > 220);

                if (isLightBackground) {
                    // White or clean neutral background: blend multiply seamlessly dissolves any white boundary
                    box.style.backgroundColor = '#ffffff';
                    img.style.mixBlendMode = 'multiply';
                    if (underlay) {
                        underlay.style.opacity = '0';
                    }
                } else {
                    // Rich colored background (e.g. Nike red, headphone yellow, bed slate)
                    box.style.backgroundColor = color.css;
                    img.style.mixBlendMode = 'normal';
                    if (underlay) {
                        underlay.style.opacity = '0.35';
                    }

                    // Adjust inner highlight ring if the background is dark
                    if (color.lum < 95) {
                        const ring = box.querySelector('[data-smart-card-ring]');
                        if (ring) {
                            ring.classList.remove('ring-black/[0.05]');
                            ring.classList.add('ring-white/15');
                        }
                    }
                }
            }
        };

        if (img.complete && img.naturalWidth > 0) {
            applyColor();
        } else {
            img.addEventListener('load', applyColor, { once: true });
        }
    });
}

/**
 * 7. Sidebar Flyout Sub-menu next to Hero Banner
 */
function initSidebarFlyout() {
    const sidebar = document.getElementById('sidebar-categories');
    const flyout = document.getElementById('sidebar-flyout-panel');
    const flyoutContent = document.getElementById('sidebar-flyout-content');
    const items = document.querySelectorAll('[data-sidebar-item]');

    if (!sidebar || !flyout || !flyoutContent) return;

    let activeSlug = null;
    let hideTimer = null;

    const renderFlyout = (slug) => {
        const data = categoryFlyoutData[slug] || {
            title: 'Danh mục chi tiết',
            subtitle: 'Khám phá hàng ngàn sản phẩm chất lượng cao với ưu đãi hấp dẫn',
            iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`,
            topCards: [
                { title: 'Nổi bật 1', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=300&q=80' },
                { title: 'Nổi bật 2', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=300&q=80' },
                { title: 'Nổi bật 3', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=300&q=80' },
                { title: 'Nổi bật 4', image: 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=300&q=80' },
                { title: 'Nổi bật 5', image: 'https://images.unsplash.com/photo-1586105251261-72a756497a11?auto=format&fit=crop&w=300&q=80' }
            ],
            columns: [
                {
                    heading: 'SẢN PHẨM PHỔ BIẾN',
                    items: [
                        { name: 'Hàng bán chạy nhất' },
                        { name: 'Hàng mới cập bến' },
                        { name: 'Top đánh giá 5 sao' },
                        { name: 'Hàng chính hãng 100%' },
                        { name: 'Combo siêu tiết kiệm' },
                        { name: 'Sản phẩm độc quyền' }
                    ]
                },
                {
                    heading: 'PHÂN LOẠI CHI TIẾT',
                    items: [
                        { name: 'Dòng sản phẩm cao cấp' },
                        { name: 'Dòng sản phẩm phổ thông' },
                        { name: 'Bộ quà tặng tuyển chọn' },
                        { name: 'Phụ kiện đi kèm' },
                        { name: 'Gói bảo hành vàng' },
                        { name: 'Hàng nhập khẩu chính ngạch' }
                    ]
                },
                {
                    heading: 'ƯU ĐÃI NỔI BẬT',
                    items: [
                        { name: 'Giảm sốc cuối tuần' },
                        { name: 'Voucher giảm 50K' },
                        { name: 'Miễn phí vận chuyển' },
                        { name: 'Tặng quà tri ân' },
                        { name: 'Đổi trả 30 ngày' },
                        { name: 'Flash sale khung giờ vàng' }
                    ]
                }
            ],
            deal: {
                badge: 'HOT DEAL',
                title: 'Ưu đãi thành viên ShopMart',
                desc: 'Tích điểm đổi quà không giới hạn, giảm thêm 10% mỗi hóa đơn.',
                btnText: 'Xem ngay',
                image: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=400&q=80'
            }
        };

        const topCardsHtml = (data.topCards || []).map(card => `
            <a href="#cat-${slug}-${encodeURIComponent(card.title)}" class="bg-gray-50/70 hover:bg-white rounded-xl border border-gray-100 hover:border-rose-200 hover:shadow-xs p-2.5 flex flex-col items-center text-center group transition-all duration-200">
                <div class="w-14 h-14 rounded-lg bg-white border border-gray-100/80 flex items-center justify-center mb-1.5 p-1 overflow-hidden shadow-2xs group-hover:scale-108 transition-transform duration-200">
                    <img src="${card.image}" alt="${card.title}" class="w-full h-full object-contain" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=200&q=80';">
                </div>
                <span class="text-xs font-semibold text-gray-800 group-hover:text-[#ea384c] transition-colors truncate max-w-full block leading-tight px-0.5">${card.title}</span>
            </a>
        `).join('');

        const columnsHtml = (data.columns || []).map(col => `
            <div>
                <h4 class="font-extrabold text-gray-900 text-xs uppercase tracking-wider mb-2.5">
                    ${col.heading}
                </h4>
                <ul class="space-y-1.5">
                    ${(col.items || []).map(item => `
                        <li>
                            <a href="#search?q=${encodeURIComponent(item.name)}" class="flex items-center justify-between py-0.5 text-xs text-gray-600 hover:text-[#ea384c] group transition-colors">
                                <div class="flex items-center gap-2 truncate pr-1">
                                    ${item.iconHtml ? item.iconHtml : ''}
                                    <span class="group-hover:translate-x-0.5 transition-transform truncate">${item.name}</span>
                                </div>
                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `).join('');

        flyoutContent.innerHTML = `
            <div class="flex flex-col h-full justify-between gap-2.5">
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#ea384c] shrink-0">
                                ${data.iconSvg || '<svg class="w-4 h-4 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>'}
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-gray-900 tracking-tight leading-tight">${data.title}</h3>
                                <p class="text-[11px] text-gray-400 mt-0.5 leading-none">${data.subtitle || 'Khám phá thế giới công nghệ, kết nối mọi khoảnh khắc'}</p>
                            </div>
                        </div>
                        <a href="#category-${slug}" class="text-[11.5px] font-semibold text-[#ea384c] hover:underline flex items-center gap-1 group">
                            <span>Xem tất cả</span>
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>

                    <!-- Top 5 Visual Category Cards -->
                    <div class="grid grid-cols-5 gap-2 mt-2.5">
                        ${topCardsHtml}
                    </div>

                    <!-- Middle 3 Columns -->
                    <div class="grid grid-cols-3 gap-5 mt-3">
                        ${columnsHtml}
                    </div>
                </div>

                <!-- Bottom Promo Banner -->
                <div class="bg-gradient-to-r from-rose-50/90 via-pink-50/60 to-rose-100/40 rounded-xl p-3 border border-rose-100/80 relative overflow-hidden flex items-center justify-between mt-2">
                    <div class="relative z-10 max-w-[62%]">
                        <span class="inline-block px-2.5 py-0.5 bg-[#ea384c] text-white text-[9px] font-black rounded-full uppercase tracking-wider">
                            ${data.deal.badge || 'HOT DEAL'}
                        </span>
                        <h4 class="text-sm font-extrabold text-gray-900 mt-1 leading-snug">
                            ${data.deal.title}
                        </h4>
                        <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">
                            ${data.deal.desc}
                        </p>
                        <a href="#deal" class="inline-flex items-center gap-1 px-3.5 py-1 bg-[#ea384c] hover:bg-[#d3273b] text-white text-[11px] font-bold rounded-full mt-2 shadow-xs transition-transform active:scale-95">
                            <span>${data.deal.btnText || 'Mua ngay'}</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                    
                    <div class="w-44 h-24 relative flex items-center justify-end overflow-hidden shrink-0">
                        <img src="${data.deal.image}" alt="${data.deal.title}" class="h-full object-contain object-right drop-shadow-xl" loading="lazy">
                    </div>
                </div>
            </div>
        `;
    };

    const clearActiveSidebarItems = () => {
        items.forEach(i => {
            i.classList.remove('bg-white', 'text-[#ea384c]', 'font-bold', 'border', 'border-rose-200', 'shadow-xs');
            const svgs = i.querySelectorAll('svg');
            if (svgs[0]) {
                svgs[0].classList.remove('text-[#ea384c]');
                svgs[0].classList.add('text-gray-400');
            }
            if (svgs[1]) {
                svgs[1].classList.remove('text-[#ea384c]');
                svgs[1].classList.add('text-gray-400');
            }
        });
    };

    items.forEach(item => {
        item.addEventListener('mouseenter', () => {
            clearTimeout(hideTimer);
            const slug = item.getAttribute('data-sidebar-item');
            activeSlug = slug;

            clearActiveSidebarItems();

            // Highlight active sidebar item like in screenshot
            item.classList.add('bg-white', 'text-[#ea384c]', 'font-bold', 'border', 'border-rose-200', 'shadow-xs');
            const activeSvgs = item.querySelectorAll('svg');
            if (activeSvgs[0]) {
                activeSvgs[0].classList.remove('text-gray-400');
                activeSvgs[0].classList.add('text-[#ea384c]');
            }
            if (activeSvgs[1]) {
                activeSvgs[1].classList.remove('text-gray-400');
                activeSvgs[1].classList.add('text-[#ea384c]');
            }

            renderFlyout(slug);
            flyout.classList.remove('hidden');
        });
    });

    const hideFlyout = () => {
        hideTimer = setTimeout(() => {
            flyout.classList.add('hidden');
            clearActiveSidebarItems();
            activeSlug = null;
        }, 150);
    };

    sidebar.addEventListener('mouseleave', hideFlyout);
    flyout.addEventListener('mouseenter', () => clearTimeout(hideTimer));
    flyout.addEventListener('mouseleave', hideFlyout);
}

/**
 * 8. Product Detail Page — Desktop Image Gallery
 */
function initProductGallery() {
    const mainImg = document.getElementById('pd-main-image');
    const thumbsContainer = document.getElementById('pd-thumbs-desktop');
    const prevBtn = document.getElementById('pd-desktop-prev');
    const nextBtn = document.getElementById('pd-desktop-next');
    if (!mainImg || !thumbsContainer) return;

    const thumbButtons = thumbsContainer.querySelectorAll('[data-pd-thumb]');
    const imageUrls = [];

    thumbButtons.forEach(btn => {
        const fullUrl = btn.getAttribute('data-full-img');
        const img = btn.querySelector('img');
        if (fullUrl) {
            imageUrls.push(fullUrl);
        } else if (img) {
            imageUrls.push(img.src.replace(/w=\d+/, 'w=800'));
        }
    });

    if (imageUrls.length === 0) return;

    let currentDesktopIndex = 0;

    const setDesktopImage = (index) => {
        if (index < 0) {
            currentDesktopIndex = imageUrls.length - 1;
        } else if (index >= imageUrls.length) {
            currentDesktopIndex = 0;
        } else {
            currentDesktopIndex = index;
        }

        if (imageUrls[currentDesktopIndex]) {
            mainImg.src = imageUrls[currentDesktopIndex];
        }

        // Update active thumbnail border and ring
        thumbButtons.forEach((b, i) => {
            if (i === currentDesktopIndex) {
                b.classList.remove('border-gray-200');
                b.classList.add('border-[#ea384c]', 'ring-2', 'ring-rose-400/30');
                b.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                b.classList.remove('border-[#ea384c]', 'ring-2', 'ring-rose-400/30');
                b.classList.add('border-gray-200');
            }
        });

        // Update counter
        const counter = document.getElementById('pd-main-counter') || document.querySelector('#pd-main-image-container .bg-black\\/50') || document.querySelector('#pd-main-image-container .bg-black\\/60');
        if (counter) {
            counter.textContent = `${currentDesktopIndex + 1} / ${imageUrls.length}`;
        }
    };

    thumbButtons.forEach((btn, idx) => {
        btn.addEventListener('click', () => {
            setDesktopImage(idx);
        });
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            setDesktopImage(currentDesktopIndex - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            setDesktopImage(currentDesktopIndex + 1);
        });
    }
}

/**
 * 9. Product Detail Page — Mobile Image Swipe Gallery
 */
function initMobileImageSwipe() {
    const gallery = document.getElementById('pd-mobile-gallery');
    const slides = document.getElementById('pd-mobile-slides');
    const prevBtn = document.getElementById('pd-mobile-prev');
    const nextBtn = document.getElementById('pd-mobile-next');
    const counter = document.getElementById('pd-mobile-counter');
    const thumbstrip = document.getElementById('pd-mobile-thumbstrip');
    if (!gallery || !slides) return;

    const slideElements = slides.children;
    const totalSlides = slideElements.length;
    let currentSlide = 0;

    const goToSlide = (index) => {
        if (index < 0) { index = totalSlides - 1; }
        if (index >= totalSlides) { index = 0; }
        currentSlide = index;
        slides.style.transform = `translateX(-${currentSlide * 100}%)`;
        if (counter) { counter.textContent = `${currentSlide + 1} / ${totalSlides}`; }

        // Update mobile thumbnail strip
        if (thumbstrip) {
            const thumbBtns = thumbstrip.querySelectorAll('[data-pd-mobile-thumb]');
            thumbBtns.forEach(btn => {
                btn.classList.remove('border-[#ea384c]', 'ring-1', 'ring-rose-400/30');
                btn.classList.add('border-gray-200');
            });
            const activeThumb = thumbstrip.querySelector(`[data-pd-mobile-thumb="${currentSlide}"]`);
            if (activeThumb) {
                activeThumb.classList.remove('border-gray-200');
                activeThumb.classList.add('border-[#ea384c]', 'ring-1', 'ring-rose-400/30');
                activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        }
    };

    if (prevBtn) { prevBtn.addEventListener('click', () => goToSlide(currentSlide - 1)); }
    if (nextBtn) { nextBtn.addEventListener('click', () => goToSlide(currentSlide + 1)); }

    // Mobile thumbnail click
    if (thumbstrip) {
        thumbstrip.querySelectorAll('[data-pd-mobile-thumb]').forEach(btn => {
            btn.addEventListener('click', () => {
                goToSlide(parseInt(btn.getAttribute('data-pd-mobile-thumb')));
            });
        });
    }

    // Touch swipe support
    let touchStartX = 0;
    let touchEndX = 0;

    gallery.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    gallery.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) { goToSlide(currentSlide + 1); }
            else { goToSlide(currentSlide - 1); }
        }
    }, { passive: true });
}

/**
 * 10. Product Detail Page — Tab Navigation
 */
function initProductTabs() {
    const tabNav = document.getElementById('pd-tab-nav');
    const panelsContainer = document.getElementById('pd-tab-panels');
    if (!tabNav || !panelsContainer) return;

    const tabButtons = tabNav.querySelectorAll('[data-pd-tab]');
    const panels = panelsContainer.querySelectorAll('[data-pd-panel]');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTab = btn.getAttribute('data-pd-tab');

            // Update tab button styles
            tabButtons.forEach(b => {
                b.classList.remove('text-[#ea384c]', 'border-[#ea384c]', 'font-bold');
                b.classList.add('text-gray-500', 'border-transparent', 'font-semibold');
            });
            btn.classList.remove('text-gray-500', 'border-transparent', 'font-semibold');
            btn.classList.add('text-[#ea384c]', 'border-[#ea384c]', 'font-bold');

            // Show/hide panels with smooth transition
            panels.forEach(panel => {
                if (panel.getAttribute('data-pd-panel') === targetTab) {
                    panel.classList.remove('hidden');
                    panel.style.animation = 'fadeIn 0.3s ease-out';
                } else {
                    panel.classList.add('hidden');
                }
            });
        });
    });
}

/**
 * 11. Product Detail Page — Quantity Selector
 */
function initQuantitySelector() {
    const minusBtn = document.getElementById('pd-qty-minus');
    const plusBtn = document.getElementById('pd-qty-plus');
    const input = document.getElementById('pd-qty-input');
    if (!minusBtn || !plusBtn || !input) return;

    const updateQty = (delta) => {
        let val = parseInt(input.value) || 1;
        val += delta;
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 99;
        if (val < min) { val = min; }
        if (val > max) { val = max; }
        input.value = val;

        // Visual feedback
        minusBtn.classList.toggle('opacity-40', val <= min);
        plusBtn.classList.toggle('opacity-40', val >= max);
    };

    minusBtn.addEventListener('click', () => updateQty(-1));
    plusBtn.addEventListener('click', () => updateQty(1));
    input.addEventListener('change', () => updateQty(0));
}

/**
 * 12. Product Detail Page — Variant Selector (Color & Storage)
 */
function initVariantSelector() {
    const colorContainer = document.getElementById('pd-color-variants');
    const storageContainer = document.getElementById('pd-storage-variants');

    const setupGroup = (container, prefix) => {
        if (!container) return;
        const buttons = container.querySelectorAll(`[data-variant-${prefix}]`);

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => {
                    b.classList.remove('border-[#ea384c]', 'bg-rose-50/30');
                    b.classList.add('border-gray-200', 'bg-white');
                    if (prefix === 'storage') {
                        b.classList.remove('text-[#ea384c]');
                        b.classList.add('text-gray-700');
                    }
                });
                btn.classList.remove('border-gray-200', 'bg-white');
                btn.classList.add('border-[#ea384c]', 'bg-rose-50/30');
                if (prefix === 'storage') {
                    btn.classList.remove('text-gray-700');
                    btn.classList.add('text-[#ea384c]');
                }
            });
        });
    };

    setupGroup(colorContainer, 'color');
    setupGroup(storageContainer, 'storage');
}

/**
 * 13. Global Add to Cart Toast Notification
 */
function initAddToCartToast() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-add-to-cart]');
        if (!btn) return;

        const productName = btn.getAttribute('data-product-name') || 'Sản phẩm';

        // Update cart badge
        document.querySelectorAll('.header-cart-badge, header .bg-\\[\\#ea384c\\].rounded-full').forEach(badge => {
            const count = parseInt(badge.textContent) || 0;
            badge.textContent = count + 1;
            badge.classList.add('scale-125', 'transition-transform');
            setTimeout(() => badge.classList.remove('scale-125'), 300);
        });

        // Show toast
        let toast = document.getElementById('global-cart-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'global-cart-toast';
            toast.className = 'fixed bottom-6 right-6 z-[9999] bg-gray-900/95 text-white px-5 py-3.5 rounded-2xl shadow-2xl backdrop-blur-md border border-white/10 flex items-center gap-3 transition-all duration-300 translate-y-20 opacity-0 pointer-events-none';
            toast.innerHTML = `
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Đã thêm vào giỏ hàng</div>
                    <div class="text-xs text-gray-200 truncate max-w-[240px] font-semibold mt-0.5" id="global-cart-toast-name"></div>
                </div>
            `;
            document.body.appendChild(toast);
        }

        const nameEl = document.getElementById('global-cart-toast-name');
        if (nameEl) nameEl.textContent = productName;

        toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }, 2800);
    });
}

// Global User Account Dropdown (Click Toggle + Click Outside Close)
function initUserDropdownMenus() {
    const triggers = document.querySelectorAll('#user-menu-toggle, #user-menu-dropdown-toggle, #user-menu-wrapper > button, #user-menu-dropdown > button');
    
    triggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const wrapper = trigger.closest('#user-menu-wrapper, #user-menu-dropdown, .group');
            if (!wrapper) return;
            const panel = wrapper.querySelector('#user-dropdown-panel, #profile-user-dropdown-panel');
            if (!panel) return;
            
            const isCurrentlyShown = (panel.style.display === 'block' || panel.classList.contains('active-open'));
            // Close other panels
            document.querySelectorAll('#user-dropdown-panel, #profile-user-dropdown-panel').forEach(p => {
                p.style.display = '';
                p.classList.remove('active-open');
            });
            
            if (!isCurrentlyShown) {
                panel.style.display = 'block';
                panel.classList.add('active-open');
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#user-menu-wrapper, #user-menu-dropdown, .group')) {
            document.querySelectorAll('#user-dropdown-panel, #profile-user-dropdown-panel').forEach(p => {
                p.style.display = '';
                p.classList.remove('active-open');
            });
        }
    });
}

// Initialize product detail and global functions
document.addEventListener('DOMContentLoaded', () => {
    initProductGallery();
    initMobileImageSwipe();
    initProductTabs();
    initQuantitySelector();
    initVariantSelector();
    initAddToCartToast();
    initUserDropdownMenus();
});


