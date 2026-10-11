/**
 * Seller Finances Page Script
 * Withdraw Modal Controller
 */

export function openWithdrawModal() {
    const modal = document.getElementById('withdraw-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

export function closeWithdrawModal() {
    const modal = document.getElementById('withdraw-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

export function initSellerFinances() {
    window.openWithdrawModal = openWithdrawModal;
    window.closeWithdrawModal = closeWithdrawModal;

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeWithdrawModal();
        }
    });
}

// Immediate binding in case script runs after DOM ready
initSellerFinances();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSellerFinances);
}
