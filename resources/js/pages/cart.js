/**
 * Cart Page Main Module
 * Combines cart selection, vouchers, checkout stepper, and address management
 */

import { initCartPageInteractions } from '../components/cart-page.js';

export const cartVoucherState = {
    freeshipCode: '',
    platformCode: ''
};

export function openCartVoucherModal() {
    const modal = document.getElementById('cart-voucher-modal');
    if (modal) modal.classList.remove('hidden');
}

export function closeCartVoucherModal() {
    const modal = document.getElementById('cart-voucher-modal');
    if (modal) modal.classList.add('hidden');
}

export function toggleCartMoreFreeship() {
    const extraList = document.getElementById('cart-extra-freeship-list');
    const toggleText = document.getElementById('cart-freeship-toggle-text');
    const toggleIcon = document.getElementById('cart-freeship-toggle-icon');
    if (!extraList) return;

    if (extraList.classList.contains('hidden')) {
        extraList.classList.remove('hidden');
        if (toggleText) toggleText.textContent = 'Thu gọn';
        if (toggleIcon) toggleIcon.classList.add('rotate-180');
    } else {
        extraList.classList.add('hidden');
        if (toggleText) toggleText.textContent = 'Xem thêm mã Miễn phí vận chuyển';
        if (toggleIcon) toggleIcon.classList.remove('rotate-180');
    }
}

export function selectVoucherCard(code, type) {
    if (type === 'freeship') {
        cartVoucherState.freeshipCode = (cartVoucherState.freeshipCode === code) ? '' : code;
    } else {
        cartVoucherState.platformCode = (cartVoucherState.platformCode === code) ? '' : code;
    }
    cartSyncVouchersWithBackend();
}

export async function cartApplyManualCoupon() {
    const input = document.getElementById('cart-modal-voucher-input');
    const msgEl = document.getElementById('cart-modal-coupon-message');
    const applyBtn = document.getElementById('cart-btn-modal-apply');
    const code = input ? input.value.trim().toUpperCase() : '';

    if (!code) {
        if (msgEl) {
            msgEl.textContent = 'Vui lòng nhập mã voucher';
            msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
        }
        return;
    }

    if (applyBtn) {
        applyBtn.disabled = true;
        applyBtn.textContent = '...';
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const rawSubtotal = (document.getElementById('mobile-sticky-total')?.textContent || '').replace(/[^\d]/g, '');
    const subtotal = parseFloat(rawSubtotal) || 0;

    try {
        const res = await fetch('/checkout/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code, subtotal: subtotal })
        });
        const data = await res.json();

        if (data.success) {
            if (data.freeship_code) cartVoucherState.freeshipCode = data.freeship_code;
            if (data.platform_code) cartVoucherState.platformCode = data.platform_code;

            if (msgEl) {
                msgEl.textContent = `${data.message} (${data.applied_codes_string || code})`;
                msgEl.className = 'text-xs mt-1.5 px-1 text-emerald-600 font-bold block';
            }
            cartSyncVouchersWithBackend();
        } else {
            if (msgEl) {
                msgEl.textContent = data.message || 'Mã voucher không hợp lệ';
                msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
            }
        }
    } catch (e) {
        console.error('Manual coupon apply error:', e);
    } finally {
        if (applyBtn) {
            applyBtn.disabled = false;
            applyBtn.textContent = 'Áp dụng';
        }
    }
}

export async function cartSyncVouchersWithBackend() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const rawSubtotal = (document.getElementById('mobile-sticky-total')?.textContent || '').replace(/[^\d]/g, '');
    const subtotal = parseFloat(rawSubtotal) || 0;

    const hasAnyCoupon = !!(cartVoucherState.freeshipCode || cartVoucherState.platformCode);

    if (!hasAnyCoupon) {
        updateCartVoucherDOM(null);
        return;
    }

    try {
        const res = await fetch('/checkout/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                freeship_code: cartVoucherState.freeshipCode,
                platform_code: cartVoucherState.platformCode,
                subtotal: subtotal
            })
        });

        const data = await res.json();
        if (data.success) {
            updateCartVoucherDOM(data);
        } else {
            const msgEl = document.getElementById('cart-modal-coupon-message');
            if (msgEl) {
                msgEl.textContent = data.message || 'Mã không khả dụng';
                msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
            }
        }
    } catch (e) {
        console.error('Cart voucher sync error:', e);
    }
}

export function updateCartVoucherDOM(data) {
    const codes = [cartVoucherState.freeshipCode, cartVoucherState.platformCode].filter(Boolean);

    // 1. Modal selected summary in footer
    const modalSummary = document.getElementById('cart-modal-selected-summary');
    if (modalSummary) {
        if (codes.length > 0) {
            modalSummary.innerHTML = codes.map(c => 
                `<span class="px-2 py-0.5 rounded bg-orange-100 text-orange-700 text-[11px] font-extrabold border border-orange-200">${c}</span>`
            ).join('');
        } else {
            modalSummary.innerHTML = '<span class="text-gray-400 font-normal">Chưa chọn voucher</span>';
        }
    }

    // 2. Highlight selected cards in modal
    document.querySelectorAll('#cart-voucher-modal .voucher-card').forEach(card => {
        const cCode = card.getAttribute('data-code');
        const isSelected = codes.includes(cCode);
        const btn = card.querySelector('.btn-modal-select-coupon');
        if (isSelected) {
            card.classList.add('border-orange-500', 'ring-2', 'ring-orange-400/40');
            if (btn) {
                btn.textContent = 'Bỏ chọn';
                btn.className = 'btn-modal-select-coupon px-3 py-1 bg-primary text-white rounded-md text-xs font-bold transition-colors cursor-pointer';
            }
        } else {
            card.classList.remove('border-orange-500', 'ring-2', 'ring-orange-400/40');
            if (btn) {
                btn.textContent = 'Dùng ngay';
                btn.className = 'btn-modal-select-coupon px-3 py-1 border border-primary text-primary hover:bg-primary hover:text-white rounded-md text-xs font-bold transition-colors cursor-pointer';
            }
        }
    });

    // 3. Sticky Bottom Bar Trigger
    const stickyBadge = document.getElementById('cart-sticky-voucher-badge');
    const stickyText = document.getElementById('cart-sticky-voucher-text');
    if (stickyBadge && stickyText) {
        if (codes.length > 0) {
            stickyBadge.textContent = codes.join(', ');
            stickyBadge.classList.remove('hidden');
            stickyText.textContent = data?.formatted_discount ? `Giảm ${data.formatted_discount}` : 'Đã áp dụng mã';
        } else {
            stickyBadge.classList.add('hidden');
            stickyText.textContent = 'Chọn hoặc nhập mã';
        }
    }

    // 4. Cart Step 2 Row if present
    const statusText = document.getElementById('cart-voucher-status-text');
    const appliedPill = document.getElementById('cart-voucher-applied-pill');
    if (statusText) {
        statusText.textContent = codes.length > 0 
            ? `Đã áp dụng: ${codes.join(', ')} (${data?.formatted_discount || ''})` 
            : 'Chọn hoặc nhập mã khuyến mãi ›';
    }
    if (appliedPill) {
        if (codes.length > 0) {
            appliedPill.textContent = codes.join(', ');
            appliedPill.classList.remove('hidden');
        } else {
            appliedPill.classList.add('hidden');
        }
    }
    const hiddenCoupon = document.getElementById('cart-hidden-coupon-code');
    if (hiddenCoupon) {
        hiddenCoupon.value = codes.join(', ');
    }

    // 5. Billing summary
    if (data) {
        const billingVoucher = document.getElementById('billing-voucher');
        const billingGrandTotal = document.getElementById('billing-grand-total');
        if (billingVoucher && data.formatted_discount) {
            billingVoucher.textContent = '- ' + data.formatted_discount;
        }
        if (billingGrandTotal && data.formatted_new_total) {
            billingGrandTotal.textContent = data.formatted_new_total;
        }
    }

    // 6. Forward voucher codes to checkout button
    const checkoutLink = document.getElementById('btn-mobile-checkout-submit');
    if (checkoutLink && checkoutLink.tagName === 'A') {
        try {
            const url = new URL(checkoutLink.href, window.location.origin);
            if (cartVoucherState.freeshipCode) {
                url.searchParams.set('freeship_code', cartVoucherState.freeshipCode);
            } else {
                url.searchParams.delete('freeship_code');
            }
            if (cartVoucherState.platformCode) {
                url.searchParams.set('platform_code', cartVoucherState.platformCode);
            } else {
                url.searchParams.delete('platform_code');
            }
            checkoutLink.href = url.pathname + url.search;
        } catch (err) {
            // ignore
        }
    }
}

export function cartApplyOptimalVouchers() {
    const dataEl = document.getElementById('cart-recommended-vouchers');
    if (!dataEl) return;
    try {
        const rec = JSON.parse(dataEl.textContent);
        if (rec?.freeship?.coupon) {
            cartVoucherState.freeshipCode = rec.freeship.coupon.code;
        }
        if (rec?.platform?.coupon) {
            cartVoucherState.platformCode = rec.platform.coupon.code;
        }
        cartSyncVouchersWithBackend();
    } catch (e) {
        console.error('Failed to parse recommended vouchers:', e);
    }
}

export function initCartPage() {
    initCartPageInteractions();

    // Bind window globals for onclick compatibility
    window.cartVoucherState = cartVoucherState;
    window.cartApplyOptimalVouchers = cartApplyOptimalVouchers;
    window.openCartVoucherModal = openCartVoucherModal;
    window.closeCartVoucherModal = closeCartVoucherModal;
    window.toggleCartMoreFreeship = toggleCartMoreFreeship;
    window.selectVoucherCard = selectVoucherCard;
    window.cartApplyManualCoupon = cartApplyManualCoupon;

    // Cart voucher open button
    document.getElementById('btn-open-cart-voucher-row')?.addEventListener('click', (e) => {
        e.preventDefault();
        openCartVoucherModal();
    });

    // Close on backdrop click
    document.getElementById('cart-voucher-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCartVoucherModal();
    });

    // Cart Step 2 payment method switcher
    document.querySelectorAll('.cart-payment-option').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.cart-payment-option').forEach(o => {
                o.classList.remove('is-selected', 'border-primary', 'bg-rose-50/20', 'border-2');
                o.classList.add('border', 'border-gray-200');
                const dot = o.querySelector('.cart-pay-check span');
                const circle = o.querySelector('.cart-pay-check');
                if (dot) dot.classList.add('hidden');
                if (circle) { circle.classList.remove('border-primary', 'bg-primary'); circle.classList.add('border-gray-300'); }
            });
            opt.classList.add('is-selected', 'border-primary', 'bg-rose-50/20', 'border-2');
            opt.classList.remove('border', 'border-gray-200');
            const radio = opt.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            const dot = opt.querySelector('.cart-pay-check span');
            const circle = opt.querySelector('.cart-pay-check');
            if (dot) dot.classList.remove('hidden');
            if (circle) { circle.classList.add('border-primary', 'bg-primary'); circle.classList.remove('border-gray-300'); }
        });
    });

    // Address modal
    document.getElementById('cart-btn-open-address-modal')?.addEventListener('click', () => {
        document.getElementById('cart-address-modal')?.classList.remove('hidden');
    });
    document.getElementById('cart-btn-close-address-modal')?.addEventListener('click', () => {
        document.getElementById('cart-address-modal')?.classList.add('hidden');
    });

    // Pick existing address
    document.querySelectorAll('.cart-address-option').forEach(opt => {
        opt.addEventListener('click', () => {
            const name = opt.getAttribute('data-name');
            const phone = opt.getAttribute('data-phone');
            const address = opt.getAttribute('data-address');
            const inName = document.getElementById('cart-input-name');
            const inPhone = document.getElementById('cart-input-phone');
            const inAddress = document.getElementById('cart-input-address');
            const dispNamePhone = document.getElementById('cart-display-name-phone');
            const dispAddr = document.getElementById('cart-display-address');

            if (inName) inName.value = name;
            if (inPhone) inPhone.value = phone;
            if (inAddress) inAddress.value = address;
            if (dispNamePhone) dispNamePhone.textContent = `${name} | ${phone}`;
            if (dispAddr) dispAddr.textContent = address;

            document.querySelectorAll('.cart-address-option').forEach(o => {
                o.classList.remove('border-primary', 'ring-2', 'ring-primary/20');
                o.classList.add('border-gray-200');
                const dot = o.querySelector('.cart-addr-dot');
                const inner = dot?.querySelector('span');
                if (dot) { dot.classList.remove('border-primary', 'bg-primary'); dot.classList.add('border-gray-300'); }
                if (inner) inner.classList.add('hidden');
            });
            opt.classList.add('border-primary', 'ring-2', 'ring-primary/20');
            opt.classList.remove('border-gray-200');
            const activeDot = opt.querySelector('.cart-addr-dot');
            const activeInner = activeDot?.querySelector('span');
            if (activeDot) { activeDot.classList.add('border-primary', 'bg-primary'); activeDot.classList.remove('border-gray-300'); }
            if (activeInner) activeInner.classList.remove('hidden');

            document.getElementById('cart-address-modal')?.classList.add('hidden');
        });
    });

    // Save new address via AJAX
    document.getElementById('cart-btn-save-address')?.addEventListener('click', async () => {
        const name = document.getElementById('cart-modal-name-input')?.value.trim();
        const phone = document.getElementById('cart-modal-phone-input')?.value.trim();
        const city = document.getElementById('cart-modal-city-input')?.value.trim() || '';
        const address = document.getElementById('cart-modal-address-input')?.value.trim();
        const isDefault = document.getElementById('cart-modal-default-check')?.checked ?? true;

        if (!name || !phone || !address) { 
            alert('Vui lòng điền đầy đủ họ và tên, số điện thoại và địa chỉ chi tiết.'); 
            return; 
        }

        const saveBtn = document.getElementById('cart-btn-save-address');
        if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Đang lưu địa chỉ...'; }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch('/checkout/quick-address', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    recipient_name: name,
                    phone: phone,
                    address_line: address,
                    city_district: city,
                    is_default: isDefault
                })
            });
            const data = await res.json();
            const finalAddr = (data.success && data.address?.address_line) ? data.address.address_line : (city ? `${address}, ${city}` : address);
            
            const inName = document.getElementById('cart-input-name');
            const inPhone = document.getElementById('cart-input-phone');
            const inAddress = document.getElementById('cart-input-address');
            const dispNamePhone = document.getElementById('cart-display-name-phone');
            const dispAddr = document.getElementById('cart-display-address');

            if (inName) inName.value = name;
            if (inPhone) inPhone.value = phone;
            if (inAddress) inAddress.value = finalAddr;
            if (dispNamePhone) dispNamePhone.textContent = `${name} | ${phone}`;
            if (dispAddr) dispAddr.textContent = finalAddr;
            document.getElementById('cart-address-modal')?.classList.add('hidden');
        } catch (e) {
            console.error(e);
            const finalAddr = city ? `${address}, ${city}` : address;
            const inName = document.getElementById('cart-input-name');
            const inPhone = document.getElementById('cart-input-phone');
            const inAddress = document.getElementById('cart-input-address');
            const dispNamePhone = document.getElementById('cart-display-name-phone');
            const dispAddr = document.getElementById('cart-display-address');

            if (inName) inName.value = name;
            if (inPhone) inPhone.value = phone;
            if (inAddress) inAddress.value = finalAddr;
            if (dispNamePhone) dispNamePhone.textContent = `${name} | ${phone}`;
            if (dispAddr) dispAddr.textContent = finalAddr;
            document.getElementById('cart-address-modal')?.classList.add('hidden');
        } finally {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Lưu &amp; Sử dụng địa chỉ này</span>';
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initCartPage();
});
