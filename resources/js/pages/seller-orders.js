/**
 * Seller Orders Page Script
 * Shipping Slip Modal Controller & Print Action
 */

export function openSlipModal(data) {
    const codeEl = document.getElementById('slip-order-code');
    const dateEl = document.getElementById('slip-order-date');
    const storeEl = document.getElementById('slip-store-name');
    const storePhoneEl = document.getElementById('slip-store-phone');
    const storeAddrEl = document.getElementById('slip-store-address');
    const custEl = document.getElementById('slip-customer-name');
    const custPhoneEl = document.getElementById('slip-customer-phone');
    const custAddrEl = document.getElementById('slip-customer-address');
    const payEl = document.getElementById('slip-payment-method');
    const totalEl = document.getElementById('slip-total-amount');

    if (codeEl) codeEl.textContent = data.code || '';
    if (dateEl) dateEl.textContent = data.date || '';
    if (storeEl) storeEl.textContent = data.store || '';
    if (storePhoneEl) storePhoneEl.textContent = data.store_phone || '';
    if (storeAddrEl) storeAddrEl.textContent = data.store_address || '';
    if (custEl) custEl.textContent = data.customer || '';
    if (custPhoneEl) custPhoneEl.textContent = data.phone || '';
    if (custAddrEl) custAddrEl.textContent = data.address || '';
    if (payEl) payEl.textContent = data.payment_method || '';
    if (totalEl) totalEl.textContent = data.total || '';

    const itemsList = document.getElementById('slip-items-list');
    if (itemsList) {
        itemsList.innerHTML = '';
        (data.items || []).forEach((item) => {
            itemsList.innerHTML += `
                <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center text-gray-500">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        <div>
                            <span class="font-bold text-gray-900">${item.qty}x ${item.name}</span>
                            ${item.variant ? `<span class="text-gray-400 text-[10px] block">${item.variant}</span>` : ''}
                        </div>
                    </div>
                    <span class="font-bold text-gray-800">${item.price}</span>
                </div>
            `;
        });
    }

    const modal = document.getElementById('shipping-slip-modal');
    if (modal) modal.classList.remove('hidden');
}

export function closeSlipModal() {
    const modal = document.getElementById('shipping-slip-modal');
    if (modal) modal.classList.add('hidden');
}

export function initSellerOrders() {
    window.openSlipModal = openSlipModal;
    window.closeSlipModal = closeSlipModal;
}

document.addEventListener('DOMContentLoaded', () => {
    initSellerOrders();
});
