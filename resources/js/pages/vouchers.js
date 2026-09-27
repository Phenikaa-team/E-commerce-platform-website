/**
 * Vouchers Page Controller (Category Tabs, Clipboard Copy, Quick Apply)
 */
import { showToast } from '../modules/toast.js';

export function filterVoucherTab(category, btn) {
    const tabs = document.querySelectorAll('#voucher-tab-bar .voucher-tab-btn');
    tabs.forEach(t => {
        if (t === btn) {
            t.classList.add('text-[#ea384c]', 'font-bold', 'border-[#ea384c]');
            t.classList.remove('text-gray-500', 'font-semibold', 'border-transparent');
        } else {
            t.classList.remove('text-[#ea384c]', 'font-bold', 'border-[#ea384c]');
            t.classList.add('text-gray-500', 'font-semibold', 'border-transparent');
        }
    });

    const cards = document.querySelectorAll('.voucher-card');
    cards.forEach(card => {
        const cardCat = card.dataset.category;
        const shouldShow = (category === 'all' || cardCat === category);
        card.style.display = shouldShow ? '' : 'none';
    });
}

export function applyQuickVoucher() {
    const input = document.getElementById('quick-voucher-input');
    const code = (input?.value || '').trim().toUpperCase();
    if (!code) {
        showToast('Vui lòng nhập mã voucher!', 'warning');
        input?.focus();
        return;
    }

    const checkoutUrl = input.getAttribute('data-checkout-url') || '/checkout';
    window.location.href = `${checkoutUrl}?coupon=${encodeURIComponent(code)}`;
}

export function copyVoucherCode(code, btn) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
            showToast(`Đã sao chép mã ${code}! Dùng ngay khi thanh toán nhé.`, 'success');
            if (btn) {
                const originalText = btn.innerHTML;
                btn.innerHTML = `<span class="text-emerald-700">Đã chép</span>`;
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            }
        }).catch(() => {
            fallbackCopy(code);
        });
    } else {
        fallbackCopy(code);
    }
}

function fallbackCopy(code) {
    const temp = document.createElement('input');
    temp.value = code;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand('copy');
    document.body.removeChild(temp);
    showToast(`Đã sao chép mã ${code}!`, 'success');
}

export function initVouchersPage() {
    // 1. Delegated events for tabs and copy buttons
    document.addEventListener('click', (e) => {
        const tabBtn = e.target.closest('.voucher-tab-btn');
        if (tabBtn) {
            e.preventDefault();
            const category = tabBtn.getAttribute('data-category') || 'all';
            filterVoucherTab(category, tabBtn);
            return;
        }

        const copyBtn = e.target.closest('[data-copy-voucher]');
        if (copyBtn) {
            e.preventDefault();
            const code = copyBtn.getAttribute('data-copy-voucher');
            copyVoucherCode(code, copyBtn);
            return;
        }

        const quickApplyBtn = e.target.closest('#btn-apply-quick-voucher');
        if (quickApplyBtn) {
            e.preventDefault();
            applyQuickVoucher();
        }
    });

    // 2. Expose functions to window for backwards compatibility
    window.filterVoucherTab = filterVoucherTab;
    window.applyQuickVoucher = applyQuickVoucher;
    window.copyVoucherCode = copyVoucherCode;
}

document.addEventListener('DOMContentLoaded', () => {
    initVouchersPage();
});
