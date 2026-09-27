/**
 * Seller Finances Page Script
 * Withdraw Modal Controller
 */

export function openWithdrawModal() {
    document.getElementById('withdraw-modal')?.classList.remove('hidden');
}

export function closeWithdrawModal() {
    document.getElementById('withdraw-modal')?.classList.add('hidden');
}

export function initSellerFinances() {
    window.openWithdrawModal = openWithdrawModal;
    window.closeWithdrawModal = closeWithdrawModal;
}

document.addEventListener('DOMContentLoaded', () => {
    initSellerFinances();
});
