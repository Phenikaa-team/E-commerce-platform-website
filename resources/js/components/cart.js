/**
 * Cart, Wishlist & Toast Notification Components
 */

let cartCount = 3;

export function showToast(message, type = 'success') {
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
export function updateAllCartBadges(displayCount) {
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

export function fetchCartCount() {
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

export function initCartAndWishlist() {
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
 * Global Add to Cart Toast Notification
 */
export function initAddToCartToast() {
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
