/**
 * Admin Orders Page Script
 * Order Detail Modal Controller
 */

export function viewOrderDetail(order) {
    const codeEl = document.getElementById('modal-order-code');
    const dateEl = document.getElementById('modal-order-date');
    const statusBadge = document.getElementById('modal-order-status');

    if (codeEl) codeEl.textContent = '#' + (order.order_code || ('SHM' + String(order.id).padStart(5, '0')));
    if (dateEl) dateEl.textContent = 'Thời gian đặt: ' + (order.created_at ? new Date(order.created_at).toLocaleString('vi-VN') : 'N/A');

    if (statusBadge) {
        statusBadge.textContent = order.status_label || order.status;
        statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold border ' + (order.status_badge || 'bg-gray-100 text-gray-700');
    }

    // Recipient
    const addr = order.shipping_address || {};
    const nameEl = document.getElementById('modal-recipient-name');
    const phoneEl = document.getElementById('modal-recipient-phone');
    const addrEl = document.getElementById('modal-recipient-address');

    if (nameEl) nameEl.textContent = addr.recipient_name || (order.user ? order.user.name : 'Khách Mua');
    if (phoneEl) phoneEl.textContent = addr.phone || (order.user ? order.user.phone : 'Chưa có SĐT');
    if (addrEl) addrEl.textContent = [addr.address_line, addr.ward, addr.district, addr.city].filter(Boolean).join(', ') || 'Chưa cung cấp địa chỉ';

    // Payment
    const payMethodEl = document.getElementById('modal-payment-method');
    const payStatusEl = document.getElementById('modal-payment-status');
    const notesEl = document.getElementById('modal-order-notes');

    if (payMethodEl) payMethodEl.textContent = (order.payment_method || 'cod').toUpperCase();
    if (payStatusEl) payStatusEl.textContent = order.payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán (Thu khi giao)';
    if (notesEl) notesEl.textContent = order.notes || 'Không có ghi chú';

    // Totals
    const fmt = (n) => new Intl.NumberFormat('vi-VN').format(Math.round(n || 0)) + '₫';
    const subtotalEl = document.getElementById('modal-subtotal');
    const shippingEl = document.getElementById('modal-shipping-fee');
    const discountEl = document.getElementById('modal-discount');
    const totalEl = document.getElementById('modal-total');

    if (subtotalEl) subtotalEl.textContent = fmt(order.subtotal || order.total);
    if (shippingEl) shippingEl.textContent = fmt(order.shipping_fee || 0);
    if (discountEl) discountEl.textContent = '-' + fmt(order.discount_amount || 0);
    if (totalEl) totalEl.textContent = fmt(order.total || 0);

    // Items list
    const itemsBox = document.getElementById('modal-order-items');
    if (itemsBox) {
        itemsBox.innerHTML = '';
        if (order.items && order.items.length) {
            order.items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between py-2 border-b border-gray-50 last:border-0';
                div.innerHTML = `
                    <div class="min-w-0 pr-3">
                        <p class="font-bold text-gray-800 truncate">${item.product_name}</p>
                        <p class="text-[11px] text-gray-400">${item.selected_variant ? 'Phân loại: ' + item.selected_variant : ''} Số lượng: ${item.quantity}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="font-bold text-gray-900">${fmt(item.subtotal || (item.unit_price * item.quantity))}</p>
                        <p class="text-[10px] text-gray-400">Đơn giá: ${fmt(item.unit_price)}</p>
                    </div>
                `;
                itemsBox.appendChild(div);
            });
        } else {
            itemsBox.innerHTML = '<p class="text-gray-400 py-2">Không có chi tiết sản phẩm</p>';
        }
    }

    const modal = document.getElementById('order-detail-modal');
    if (modal) modal.classList.remove('hidden');
}

export function closeOrderDetailModal() {
    const modal = document.getElementById('order-detail-modal');
    if (modal) modal.classList.add('hidden');
}

export function initAdminOrders() {
    window.viewOrderDetail = viewOrderDetail;
    window.closeOrderDetailModal = closeOrderDetailModal;
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminOrders();
});
