/**
 * Product Detail Page Components:
 * - Desktop Image Gallery with thumbnails and zoom counters
 * - Mobile Image Swipe Gallery with touch gestures and thumbnail strip
 * - Tab navigation (Overview, Specifications, Reviews)
 * - Quantity selector (+/-)
 * - Variant selector (Color & Storage options)
 */

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
