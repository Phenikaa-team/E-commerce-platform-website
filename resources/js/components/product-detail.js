/**
 * Product Detail Page Components:
 * - Desktop Image Gallery with thumbnails and zoom counters
 * - Mobile Image Swipe Gallery with touch gestures and thumbnail strip
 * - Tab navigation (Overview, Specifications, Reviews)
 * - Quantity selector (+/-)
 * - Variant selector (Color & Storage options)
 */

import { formatVnd } from '../modules/formatters.js';

/**
 * 8. Product Detail Page — Desktop Image Gallery
 */
export function initProductGallery() {
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
                b.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
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
export function initMobileImageSwipe() {
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
export function initProductTabs() {
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
                b.classList.remove('is-active', 'text-[#ea384c]', 'border-[#ea384c]', 'font-bold');
                b.classList.add('text-gray-500', 'border-transparent', 'font-semibold');
            });
            btn.classList.remove('text-gray-500', 'border-transparent', 'font-semibold');
            btn.classList.add('is-active', 'text-[#ea384c]', 'border-[#ea384c]', 'font-bold');

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
export function initQuantitySelector() {
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
export function initVariantSelector() {
    const colorContainer = document.getElementById('pd-color-variants');
    const storageContainer = document.getElementById('pd-storage-variants');

    // Parse variants JSON if present
    const variantsJsonEl = document.getElementById('pd-variants-json');
    let variants = [];
    if (variantsJsonEl) {
        try {
            variants = JSON.parse(variantsJsonEl.textContent || '[]');
        } catch (e) {
            console.error('Failed to parse pd-variants-json:', e);
        }
    }

    const colorButtons = colorContainer ? colorContainer.querySelectorAll('.pd-variant-btn, [data-variant-color]') : [];
    const storageButtons = storageContainer ? storageContainer.querySelectorAll('.pd-variant-btn, [data-variant-storage], [data-variant-option]') : [];

    const getActiveColorName = () => {
        const active = colorContainer?.querySelector('.is-active, .active');
        return active?.getAttribute('data-color-name') || active?.querySelector('span')?.textContent?.trim() || active?.textContent?.trim() || '';
    };

    const getActiveOptionName = () => {
        const active = storageContainer?.querySelector('.is-active, .active');
        return active?.getAttribute('data-option-name') || active?.textContent?.trim() || '';
    };

    const updateVariantUI = () => {
        const colorName = getActiveColorName();
        const optionName = getActiveOptionName();

        // Find matching variant
        let matchedVariant = null;
        if (variants.length > 0) {
            if (colorName && optionName) {
                matchedVariant = variants.find(v => v.color === colorName && v.option === optionName);
            }
            if (!matchedVariant && colorName && !optionName) {
                matchedVariant = variants.find(v => v.color === colorName);
            }
            if (!matchedVariant && !colorName && optionName) {
                matchedVariant = variants.find(v => v.option === optionName);
            }
            if (!matchedVariant && (colorName || optionName)) {
                const expectedName = [colorName, optionName].filter(Boolean).join(' - ');
                matchedVariant = variants.find(v => v.name === expectedName || (colorName && v.color === colorName) || (optionName && v.option === optionName));
            }
        }

        // Target stock and price elements
        const stockLabel = document.getElementById('pd-stock-label');
        const stockDisplay = document.getElementById('pd-stock-display');
        const stockUnit = document.getElementById('pd-stock-unit');
        const qtyInput = document.getElementById('pd-qty-input');
        const qtyMinus = document.getElementById('pd-qty-minus');
        const qtyPlus = document.getElementById('pd-qty-plus');
        const priceDisplay = document.getElementById('pd-price-display');
        const originalPriceDisplay = document.getElementById('pd-original-price-display');
        const discountBadge = document.getElementById('pd-discount-badge');
        const saveContainer = document.getElementById('pd-save-container');
        const saveAmount = document.getElementById('pd-save-amount');

        // Add to cart & Buy now buttons (both desktop and mobile)
        const ctaButtons = document.querySelectorAll('[data-add-to-cart], [data-buy-now]');

        if (matchedVariant) {
            const stock = matchedVariant.stock;

            // Live Update Price
            if (matchedVariant.price && priceDisplay) {
                priceDisplay.textContent = formatVnd(matchedVariant.price);
            }

            // Live Update Original Price & Discount
            if (matchedVariant.original_price && matchedVariant.original_price > matchedVariant.price) {
                if (originalPriceDisplay) {
                    originalPriceDisplay.textContent = formatVnd(matchedVariant.original_price);
                    originalPriceDisplay.classList.remove('hidden');
                }
                const discount = Math.round(((matchedVariant.original_price - matchedVariant.price) / matchedVariant.original_price) * 100);
                if (discountBadge) {
                    discountBadge.textContent = `-${discount}%`;
                    discountBadge.classList.remove('hidden');
                }
                if (saveAmount) {
                    saveAmount.textContent = formatVnd(matchedVariant.original_price - matchedVariant.price);
                }
                if (saveContainer) {
                    saveContainer.classList.remove('hidden');
                }
            } else {
                if (originalPriceDisplay) originalPriceDisplay.classList.add('hidden');
                if (discountBadge) discountBadge.classList.add('hidden');
                if (saveContainer) saveContainer.classList.add('hidden');
            }

            if (stock <= 0) {
                if (stockLabel) stockLabel.textContent = '';
                if (stockDisplay) {
                    stockDisplay.textContent = 'Hết hàng';
                    stockDisplay.className = 'text-red-600 font-bold';
                }
                if (stockUnit) stockUnit.textContent = '';

                // Disable quantity input
                if (qtyInput) {
                    qtyInput.value = 0;
                    qtyInput.max = 0;
                    qtyInput.disabled = true;
                }
                if (qtyMinus) qtyMinus.classList.add('opacity-40', 'pointer-events-none');
                if (qtyPlus) qtyPlus.classList.add('opacity-40', 'pointer-events-none');

                // Disable CTA buttons
                ctaButtons.forEach(btn => {
                    btn.disabled = true;
                    btn.setAttribute('data-disabled', 'true');
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                });
            } else {
                if (stockLabel) stockLabel.textContent = 'Còn ';
                if (stockDisplay) {
                    stockDisplay.textContent = stock;
                    stockDisplay.className = 'text-gray-700';
                }
                if (stockUnit) stockUnit.textContent = ' sản phẩm';

                // Enable quantity input
                if (qtyInput) {
                    qtyInput.disabled = false;
                    qtyInput.min = 1;
                    qtyInput.max = stock;
                    let currentVal = parseInt(qtyInput.value) || 1;
                    if (currentVal < 1) qtyInput.value = 1;
                    if (currentVal > stock) qtyInput.value = stock;
                }
                if (qtyMinus) qtyMinus.classList.remove('opacity-40', 'pointer-events-none');
                if (qtyPlus) qtyPlus.classList.remove('opacity-40', 'pointer-events-none');

                // Enable CTA buttons
                ctaButtons.forEach(btn => {
                    btn.disabled = false;
                    btn.removeAttribute('data-disabled');
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                });
            }

            // Update main image if variant has a distinct image
            if (matchedVariant.image_url) {
                const mainImg = document.getElementById('pd-main-image');
                if (mainImg) {
                    mainImg.src = matchedVariant.image_url;
                }
            }
        }
    };

    // Color click handler
    colorButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            colorButtons.forEach(b => {
                b.classList.remove('is-active', 'active', 'border-primary', 'bg-rose-50/30', 'ring-2', 'ring-rose-400/20');
                b.classList.add('border-gray-200', 'bg-white');
            });
            btn.classList.remove('border-gray-200', 'bg-white');
            btn.classList.add('is-active', 'active', 'border-primary', 'bg-rose-50/30', 'ring-2', 'ring-rose-400/20');

            updateVariantUI();
        });
    });

    // Storage/Option click handler
    storageButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            storageButtons.forEach(b => {
                b.classList.remove('is-active', 'active', 'border-primary', 'bg-rose-50/30', 'text-primary', 'ring-2', 'ring-rose-400/20');
                b.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
            });
            btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');
            btn.classList.add('is-active', 'active', 'border-primary', 'bg-rose-50/30', 'text-primary', 'ring-2', 'ring-rose-400/20');

            updateVariantUI();
        });
    });

    // Run on initial load to calibrate with active variant
    updateVariantUI();
}
