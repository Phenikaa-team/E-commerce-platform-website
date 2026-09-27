/**
 * Seller Coupons Page Script
 * Sync coupon type (% vs fixed) with input boundaries & helper UI
 */

export function initSellerCoupons() {
    const typeSelect = document.getElementById('coupon_type');
    const valueInput = document.getElementById('coupon_value');
    const maxDiscountWrapper = document.getElementById('max_discount_wrapper');
    const usageLimitWrapper = document.getElementById('usage_limit_wrapper');

    function syncTypeUI() {
        if (!typeSelect || !valueInput) return;
        const isPercent = typeSelect.value === 'percent';
        if (isPercent) {
            valueInput.min = '1';
            valueInput.max = '99';
            valueInput.step = '1';
            if (parseFloat(valueInput.value) > 100) valueInput.value = '10';
            maxDiscountWrapper?.classList.remove('hidden');
            usageLimitWrapper?.classList.add('col-span-2');
        } else {
            valueInput.min = '1000';
            valueInput.removeAttribute('max');
            valueInput.step = '1000';
            if (parseFloat(valueInput.value) < 1000) valueInput.value = '20000';
            maxDiscountWrapper?.classList.add('hidden');
            usageLimitWrapper?.classList.remove('col-span-2');
        }
    }

    typeSelect?.addEventListener('change', syncTypeUI);
    syncTypeUI();
}

document.addEventListener('DOMContentLoaded', () => {
    initSellerCoupons();
});
