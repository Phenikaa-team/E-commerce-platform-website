/**
 * Checkout Page Module
 * Manages Multi-Vendor Vouchers, ShopMart Vouchers, Shopee Xu, Payment Selection & Address Validation
 */

export const checkoutState = {
    freeshipCode: '',
    shopCodes: {},
    platformCode: '',
    usePoints: false,
    currentSubtotal: 0,
    baseShippingFee: 0,
    storeSubtotals: {},
    activeStoreId: null,
    recommendedVouchers: {}
};

export function openShopVoucherModal(storeId, storeName) {
    const modal = document.getElementById('shop-voucher-modal');
    if (!modal) return;
    checkoutState.activeStoreId = storeId;
    if (storeName) {
        const nameEl = document.getElementById('shop-modal-store-name');
        if (nameEl) nameEl.textContent = `Ưu đãi độc quyền từ ${storeName}`;
    }

    const container = document.getElementById('shop-modal-vouchers-list');
    let matchCount = 0;
    const cards = modal.querySelectorAll('.voucher-card');
    const bestShopCode = checkoutState.recommendedVouchers?.shops?.[storeId]?.coupon?.code;

    cards.forEach(card => {
        const sId = card.getAttribute('data-store-id');
        if (sId && String(sId) === String(storeId)) {
            card.classList.remove('hidden');
            matchCount++;
            if (bestShopCode && card.getAttribute('data-code') === bestShopCode && container) {
                container.prepend(card);
            }
        } else {
            card.classList.add('hidden');
        }
    });

    const emptyEl = document.getElementById('shop-modal-empty');
    if (emptyEl) {
        if (matchCount === 0) emptyEl.classList.remove('hidden');
        else emptyEl.classList.add('hidden');
    }

    const currentShopCode = checkoutState.shopCodes[storeId];
    const shopModalSelected = document.getElementById('shop-modal-selected');
    if (shopModalSelected) {
        shopModalSelected.innerHTML = currentShopCode 
            ? `<span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 text-[11px] font-extrabold border border-rose-200">${currentShopCode}</span>`
            : '<span class="text-gray-400 font-normal">Chưa chọn mã</span>';
    }

    modal.classList.remove('hidden');
}

export function closeShopVoucherModal() {
    const modal = document.getElementById('shop-voucher-modal');
    if (modal) modal.classList.add('hidden');
}

export function openShopeeModal() {
    const modal = document.getElementById('shopee-voucher-modal');
    if (modal) modal.classList.remove('hidden');
}

export function closeShopeeModal() {
    const modal = document.getElementById('shopee-voucher-modal');
    if (modal) modal.classList.add('hidden');
}

export function toggleMoreFreeship() {
    const extraList = document.getElementById('extra-freeship-list');
    const toggleText = document.getElementById('freeship-toggle-text');
    const toggleIcon = document.getElementById('freeship-toggle-icon');
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

export function applyOptimalVouchers() {
    const rec = checkoutState.recommendedVouchers;
    if (!rec) return;

    if (rec.freeship?.coupon) {
        checkoutState.freeshipCode = rec.freeship.coupon.code;
    }
    if (rec.platform?.coupon) {
        checkoutState.platformCode = rec.platform.coupon.code;
    }
    if (rec.shops) {
        Object.entries(rec.shops).forEach(([sId, item]) => {
            if (item?.coupon) {
                checkoutState.shopCodes[sId] = item.coupon.code;
            }
        });
    }

    syncVouchersWithBackend();
}

export function selectVoucherCard(code, type, storeId) {
    if (type === 'freeship') {
        checkoutState.freeshipCode = (checkoutState.freeshipCode === code) ? '' : code;
    } else if (type === 'shop') {
        const targetStoreId = storeId || checkoutState.activeStoreId || 0;
        if (checkoutState.shopCodes[targetStoreId] === code) {
            delete checkoutState.shopCodes[targetStoreId];
        } else {
            checkoutState.shopCodes[targetStoreId] = code;
        }
        const shopModalSelected = document.getElementById('shop-modal-selected');
        if (shopModalSelected) {
            const currentSel = checkoutState.shopCodes[targetStoreId];
            shopModalSelected.innerHTML = currentSel 
                ? `<span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 text-[11px] font-extrabold border border-rose-200">${currentSel}</span>`
                : '<span class="text-gray-400 font-normal">Chưa chọn mã</span>';
        }
    } else {
        checkoutState.platformCode = (checkoutState.platformCode === code) ? '' : code;
    }
    syncVouchersWithBackend();
}

export async function applyManualShopCoupon() {
    const input = document.getElementById('shop-modal-input');
    const msgEl = document.getElementById('shop-modal-message');
    const code = input ? input.value.trim().toUpperCase() : '';
    const targetStoreId = checkoutState.activeStoreId || 0;

    if (!code) {
        if (msgEl) {
            msgEl.textContent = 'Vui lòng nhập mã của shop';
            msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
        }
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const res = await fetch('/checkout/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                shop_code: code,
                store_id: targetStoreId,
                subtotal: checkoutState.currentSubtotal,
                store_subtotals: checkoutState.storeSubtotals
            })
        });

        const data = await res.json();
        if (data.success) {
            checkoutState.shopCodes[targetStoreId] = code;
            if (msgEl) {
                msgEl.textContent = `Áp dụng thành công mã: ${code}`;
                msgEl.className = 'text-xs mt-1.5 px-1 text-emerald-600 font-bold block';
            }
            syncVouchersWithBackend();
        } else {
            if (msgEl) {
                msgEl.textContent = data.message || 'Mã shop không hợp lệ';
                msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
            }
        }
    } catch (e) {
        console.error('Manual shop coupon error:', e);
    }
}

export async function applyManualCoupon() {
    const input = document.getElementById('modal-voucher-input');
    const msgEl = document.getElementById('modal-coupon-message');
    const code = input ? input.value.trim().toUpperCase() : '';

    if (!code) {
        if (msgEl) {
            msgEl.textContent = 'Vui lòng nhập mã voucher';
            msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
        }
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const res = await fetch('/checkout/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                code: code,
                subtotal: checkoutState.currentSubtotal,
                store_subtotals: checkoutState.storeSubtotals
            })
        });

        const data = await res.json();
        if (data.success) {
            if (data.freeship_code) checkoutState.freeshipCode = data.freeship_code;
            if (data.platform_code) checkoutState.platformCode = data.platform_code;

            if (msgEl) {
                msgEl.textContent = `${data.message} (${data.applied_codes_string || code})`;
                msgEl.className = 'text-xs mt-1.5 px-1 text-emerald-600 font-bold block';
            }
            syncVouchersWithBackend();
        } else {
            if (msgEl) {
                msgEl.textContent = data.message || 'Mã không hợp lệ hoặc không đủ điều kiện';
                msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
            }
        }
    } catch (e) {
        console.error('Manual platform coupon error:', e);
    }
}

export async function syncVouchersWithBackend() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const res = await fetch('/checkout/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                freeship_code: checkoutState.freeshipCode,
                shop_codes: checkoutState.shopCodes,
                platform_code: checkoutState.platformCode,
                use_points: checkoutState.usePoints,
                subtotal: checkoutState.currentSubtotal,
                store_subtotals: checkoutState.storeSubtotals
            })
        });

        const data = await res.json();

        // Update hidden inputs for checkout submission
        const fsHidden = document.getElementById('hidden-freeship-code');
        if (fsHidden) fsHidden.value = checkoutState.freeshipCode;
        const plHidden = document.getElementById('hidden-platform-code');
        if (plHidden) plHidden.value = checkoutState.platformCode;
        const cpHidden = document.getElementById('hidden-coupon-code');
        if (cpHidden) cpHidden.value = data.applied_codes_string || '';

        const shopHidden = document.getElementById('hidden-shop-code');
        const shopCodesArray = Object.values(checkoutState.shopCodes).filter(Boolean);
        if (shopHidden) shopHidden.value = shopCodesArray[0] || '';

        const storeCouponsContainer = document.getElementById('hidden-store-coupons-container');
        if (storeCouponsContainer) {
            storeCouponsContainer.innerHTML = '';
            Object.entries(checkoutState.shopCodes).forEach(([sId, sCode]) => {
                if (sCode) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `shop_voucher_codes[${sId}]`;
                    input.value = sCode;
                    storeCouponsContainer.appendChild(input);
                }
            });
        }

        // Update UI tags on store cards
        document.querySelectorAll('.shop-store-badge').forEach(b => b.classList.add('hidden'));
        Object.entries(checkoutState.shopCodes).forEach(([sId, sCode]) => {
            const sBadge = document.getElementById(`shop-voucher-badge-${sId}`);
            if (sBadge && sCode) {
                sBadge.textContent = sCode;
                sBadge.classList.remove('hidden');
            }
        });

        const platformBadge = document.getElementById('platform-voucher-badge');
        if (platformBadge) {
            if (checkoutState.platformCode) {
                platformBadge.textContent = checkoutState.platformCode;
                platformBadge.classList.remove('hidden');
            } else {
                platformBadge.classList.add('hidden');
            }
        }

        const freeshipBadge = document.getElementById('freeship-voucher-badge');
        if (freeshipBadge) {
            if (checkoutState.freeshipCode) {
                freeshipBadge.textContent = checkoutState.freeshipCode;
                freeshipBadge.classList.remove('hidden');
            } else {
                freeshipBadge.classList.add('hidden');
            }
        }

        // Update breakdown rows in right sticky summary
        const freeshipRow = document.getElementById('freeship-discount-row');
        const summaryFreeship = document.getElementById('summary-freeship-discount');
        if (data.freeship_discount > 0) {
            if (freeshipRow) freeshipRow.classList.remove('hidden');
            if (summaryFreeship) summaryFreeship.textContent = data.formatted_freeship_discount;
        } else {
            if (freeshipRow) freeshipRow.classList.add('hidden');
        }

        const shopRow = document.getElementById('shop-discount-row');
        const summaryShop = document.getElementById('summary-shop-discount');
        if (data.shop_discount > 0) {
            if (shopRow) shopRow.classList.remove('hidden');
            if (summaryShop) summaryShop.textContent = data.formatted_shop_discount || ('-' + Number(data.shop_discount).toLocaleString('vi-VN') + '₫');
        } else {
            if (shopRow) shopRow.classList.add('hidden');
        }

        const platformRow = document.getElementById('platform-discount-row');
        const summaryPlatform = document.getElementById('summary-platform-discount');
        if (data.platform_discount > 0) {
            if (platformRow) platformRow.classList.remove('hidden');
            if (summaryPlatform) summaryPlatform.textContent = data.formatted_platform_discount || ('-' + Number(data.platform_discount).toLocaleString('vi-VN') + '₫');
        } else {
            if (platformRow) platformRow.classList.add('hidden');
        }

        const xuRow = document.getElementById('xu-discount-row');
        const summaryXu = document.getElementById('summary-xu-discount');
        if (data.points_discount > 0) {
            if (xuRow) xuRow.classList.remove('hidden');
            if (summaryXu) summaryXu.textContent = data.formatted_points_discount;
        } else {
            if (xuRow) xuRow.classList.add('hidden');
        }

        // Shipping fee & grand total
        const summaryShipping = document.getElementById('summary-shipping');
        if (summaryShipping && data.formatted_shipping) {
            summaryShipping.textContent = data.formatted_shipping;
        }

        const summaryGrandTotal = document.getElementById('summary-grand-total');
        if (summaryGrandTotal && data.formatted_new_total) {
            summaryGrandTotal.textContent = data.formatted_new_total;
        }

        // Update shop modal selected display
        const targetStoreId = checkoutState.activeStoreId;
        const currentSelectedShopCode = targetStoreId ? checkoutState.shopCodes[targetStoreId] : null;
        const shopModalSelected = document.getElementById('shop-modal-selected');
        if (shopModalSelected) {
            shopModalSelected.innerHTML = currentSelectedShopCode 
                ? `<span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 text-[11px] font-extrabold border border-rose-200">${currentSelectedShopCode}</span>`
                : '<span class="text-gray-400 font-normal">Chưa chọn mã</span>';
        }

        // Update platform modal selected status bar
        const modalSelectedSummary = document.getElementById('modal-selected-summary');
        if (modalSelectedSummary) {
            const platCodes = [checkoutState.freeshipCode, checkoutState.platformCode].filter(Boolean);
            if (platCodes.length > 0) {
                modalSelectedSummary.innerHTML = platCodes.map(c => 
                    `<span class="px-2 py-0.5 rounded bg-orange-100 text-orange-700 text-[11px] font-extrabold border border-orange-200">${c}</span>`
                ).join('');
            } else {
                modalSelectedSummary.innerHTML = '<span class="text-gray-400 font-normal">Chưa chọn voucher</span>';
            }
        }

        // Highlight selected cards in both modals
        const allSelectedCodes = [
            checkoutState.freeshipCode,
            checkoutState.platformCode,
            ...Object.values(checkoutState.shopCodes)
        ].filter(Boolean);

        document.querySelectorAll('.voucher-card').forEach(card => {
            const cCode = card.getAttribute('data-code');
            const isSelected = allSelectedCodes.includes(cCode);
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

    } catch (e) {
        console.error('Failed to sync vouchers:', e);
    }
}

export function initCheckoutPage() {
    // Read initial config
    const dataEl = document.getElementById('checkout-page-data');
    if (dataEl) {
        try {
            const config = JSON.parse(dataEl.textContent);
            checkoutState.currentSubtotal = Number(config.currentSubtotal || 0);
            checkoutState.baseShippingFee = Number(config.baseShippingFee || 0);
            checkoutState.storeSubtotals = config.storeSubtotals || {};
            checkoutState.recommendedVouchers = config.recommendedVouchers || {};
        } catch (e) {
            console.error('Failed to parse checkout data:', e);
        }
    }

    // Expose functions globally for onclick compatibility in blade templates
    window.checkoutState = checkoutState;
    window.openShopVoucherModal = openShopVoucherModal;
    window.closeShopVoucherModal = closeShopVoucherModal;
    window.openShopeeModal = openShopeeModal;
    window.closeShopeeModal = closeShopeeModal;
    window.toggleMoreFreeship = toggleMoreFreeship;
    window.applyOptimalVouchers = applyOptimalVouchers;
    window.selectVoucherCard = selectVoucherCard;
    window.applyManualShopCoupon = applyManualShopCoupon;
    window.applyManualCoupon = applyManualCoupon;
    window.syncVouchersWithBackend = syncVouchersWithBackend;

    // Payment Method Switcher
    const paymentOptions = document.querySelectorAll('.payment-list-option');
    paymentOptions.forEach(opt => {
        opt.addEventListener('click', () => {
            paymentOptions.forEach(o => {
                o.classList.remove('is-selected');
                const dot = o.querySelector('.payment-list-dot');
                if (dot) {
                    dot.classList.remove('border-primary', 'bg-primary');
                    dot.classList.add('border-gray-300');
                    const inner = dot.querySelector('span');
                    if (inner) inner.classList.add('hidden');
                }
            });
            opt.classList.add('is-selected');
            const radio = opt.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            const dot = opt.querySelector('.payment-list-dot');
            if (dot) {
                dot.classList.add('border-primary', 'bg-primary');
                dot.classList.remove('border-gray-300');
                const inner = dot.querySelector('span');
                if (inner) inner.classList.remove('hidden');
            }
        });
    });

    // Shopee Xu Points Toggle
    const pointsToggle = document.getElementById('checkout-points-toggle');
    const xuDiscountText = document.getElementById('xu-discount-text');
    if (pointsToggle) {
        pointsToggle.addEventListener('change', () => {
            checkoutState.usePoints = pointsToggle.checked;
            if (xuDiscountText) {
                xuDiscountText.textContent = pointsToggle.checked ? '[-50.000₫]' : '[-0₫]';
                xuDiscountText.className = pointsToggle.checked ? 'text-xs font-bold text-orange-600' : 'text-xs font-bold text-gray-400';
            }
            syncVouchersWithBackend();
        });
    }

    // Address Modal Manager
    if (window.AddressModalManager) {
        window.AddressModalManager.init('checkout');
    }

    // Mandatory address validation before placing order
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const name = document.getElementById('input-recipient-name')?.value.trim();
            const phone = document.getElementById('input-recipient-phone')?.value.trim();
            const addr = document.getElementById('input-recipient-address')?.value.trim();

            if (!name || !phone || !addr) {
                e.preventDefault();
                alert('Bắt buộc phải cài đặt địa chỉ nhận hàng trước khi tiến hành thanh toán!');
                window.AddressModalManager?.openSelectorModal();
                return false;
            }
        });
    }

    // Auto-apply coupon from URL param if present
    const urlParams = new URLSearchParams(window.location.search);
    const fsFromUrl = urlParams.get('freeship_code');
    const platFromUrl = urlParams.get('platform_code') || urlParams.get('coupon');
    if (fsFromUrl) checkoutState.freeshipCode = fsFromUrl;
    if (platFromUrl) checkoutState.platformCode = platFromUrl;
    if (fsFromUrl || platFromUrl) {
        syncVouchersWithBackend();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initCheckoutPage();
});
