/**
 * Admin User Detail Modal & Status Controller
 */

export async function openUserDetailModal(userId) {
    const modal = document.getElementById('admin-user-detail-modal');
    const loading = document.getElementById('modal-loading');
    const content = document.getElementById('modal-content');
    if (!modal || !loading || !content) return;
    
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    content.classList.add('hidden');

    // Reset to first tab
    switchModalTab('profile', document.querySelector('.modal-tab-btn'));

    try {
        const res = await fetch(`/admin/users/${userId}`);
        const data = await res.json();

        if (data.success) {
            const u = data.user;

            // ID & Avatar
            const idEl = document.getElementById('modal-user-id');
            const avatarEl = document.getElementById('modal-user-avatar');
            const nameEl = document.getElementById('modal-user-name');
            const usernameEl = document.getElementById('modal-user-username');
            const roleEl = document.getElementById('modal-user-role-label');

            if (idEl) idEl.textContent = u.id;
            if (avatarEl) avatarEl.src = u.avatar_url;
            if (nameEl) nameEl.textContent = u.name;
            if (usernameEl) usernameEl.textContent = u.username;
            if (roleEl) roleEl.textContent = u.role_label;
            
            // Contacts
            const emailEl = document.getElementById('modal-user-email');
            const phoneEl = document.getElementById('modal-user-phone');
            if (emailEl) emailEl.querySelector('span').textContent = u.email;
            if (phoneEl) phoneEl.querySelector('span').textContent = u.phone;
            
            // Metadata
            const joinedEl = document.getElementById('modal-user-joined');
            const lastLoginEl = document.getElementById('modal-user-last-login');
            if (joinedEl) joinedEl.textContent = u.created_at;
            if (lastLoginEl) lastLoginEl.textContent = u.last_login_at;

            // Status Badge & Controls
            const statusEl = document.getElementById('modal-user-status');
            const onlineDot = document.getElementById('modal-online-dot');
            const statusCard = document.getElementById('modal-status-card');
            const statusTitle = document.getElementById('modal-status-title');
            const statusDesc = document.getElementById('modal-status-desc');
            const statusBtn = document.getElementById('modal-status-btn');
            const statusIcon = document.getElementById('modal-status-icon');
            const toggleForm = document.getElementById('modal-toggle-status-form');

            if (toggleForm) toggleForm.action = `/admin/users/${u.id}/toggle-status`;

            if (u.status === 'banned') {
                if (statusEl) {
                    statusEl.textContent = 'Đã khóa';
                    statusEl.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-200/60';
                }
                if (onlineDot) onlineDot.className = 'w-3.5 h-3.5 rounded-full bg-rose-500 border-2 border-white absolute bottom-0 right-0';
                
                if (statusCard) statusCard.className = 'p-4 rounded-2xl border border-rose-100 bg-rose-50/40 flex items-center justify-between gap-4 transition-colors';
                if (statusIcon) {
                    statusIcon.className = 'w-9 h-9 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0';
                    statusIcon.innerHTML = '<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                }
                if (statusTitle) statusTitle.textContent = 'Tài khoản đang bị tạm khóa';
                if (statusDesc) statusDesc.textContent = 'Người dùng bị chặn đăng nhập và thực hiện giao dịch trên hệ thống.';
                if (statusBtn) {
                    statusBtn.className = 'px-4 py-2 rounded-xl text-xs font-bold border border-emerald-200 bg-white hover:bg-emerald-50 text-emerald-600 transition-colors shadow-2xs cursor-pointer flex items-center gap-1.5';
                    statusBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg><span>Mở khóa tài khoản</span>';
                }
            } else {
                if (statusEl) {
                    statusEl.textContent = 'Hoạt động';
                    statusEl.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60';
                }
                if (onlineDot) onlineDot.className = 'w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0';
                
                if (statusCard) statusCard.className = 'p-4 rounded-2xl border border-emerald-100 bg-emerald-50/40 flex items-center justify-between gap-4 transition-colors';
                if (statusIcon) {
                    statusIcon.className = 'w-9 h-9 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0';
                    statusIcon.innerHTML = '<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>';
                }
                if (statusTitle) statusTitle.textContent = 'Tài khoản đang hoạt động';
                if (statusDesc) statusDesc.textContent = 'Người dùng có thể đăng nhập và sử dụng đầy đủ chức năng.';
                if (statusBtn) {
                    statusBtn.className = 'px-4 py-2 rounded-xl text-xs font-bold border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 transition-colors shadow-2xs cursor-pointer flex items-center gap-1.5';
                    statusBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg><span>Khóa tài khoản</span>';
                }
            }

            // Tab 1 Info Cards
            const cardName = document.getElementById('modal-card-name');
            const cardJoined = document.getElementById('modal-card-joined');
            const cardPhone = document.getElementById('modal-card-phone');
            const cardEmail = document.getElementById('modal-card-email');
            const cardGender = document.getElementById('modal-card-gender');
            const cardBirthday = document.getElementById('modal-card-birthday');
            const cardAddress = document.getElementById('modal-card-address');

            if (cardName) cardName.textContent = u.name;
            if (cardJoined) cardJoined.textContent = u.created_at;
            if (cardPhone) cardPhone.textContent = u.phone;
            if (cardEmail) cardEmail.textContent = u.email;
            if (cardGender) cardGender.textContent = u.gender;
            if (cardBirthday) cardBirthday.textContent = u.birthday;
            if (cardAddress) cardAddress.textContent = u.default_address;

            // Tab 2: Orders
            const ordersCount = document.getElementById('modal-orders-count');
            const totalSpent = document.getElementById('modal-total-spent');
            const totalOrders = document.getElementById('modal-total-orders');

            if (ordersCount) ordersCount.textContent = u.total_orders_count;
            if (totalSpent) totalSpent.textContent = u.total_spent;
            if (totalOrders) totalOrders.textContent = u.total_orders_count + ' đơn hàng';
            
            const ordersList = document.getElementById('modal-orders-list');
            if (ordersList) {
                ordersList.innerHTML = '';
                if (u.recent_orders && u.recent_orders.length > 0) {
                    u.recent_orders.forEach(o => {
                        ordersList.innerHTML += `
                            <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-100 flex items-center justify-between">
                                <div>
                                    <span class="font-mono font-bold text-gray-900">${o.order_number}</span>
                                    <span class="text-gray-400 text-[10px] block">${o.created_at} • ${o.items_count} sản phẩm</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-black text-gray-900 block">${o.total_amount}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${o.status === 'completed' || o.status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200'}">${o.status === 'completed' ? 'Đã giao' : (o.status === 'shipping' ? 'Đang giao' : o.status)}</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    ordersList.innerHTML = '<p class="text-gray-400 italic text-center py-4">Chưa có đơn hàng phát sinh.</p>';
                }
            }

            // Tab 3: Finance / Wallet
            const financeRole = document.getElementById('modal-finance-role');
            const financeWallet = document.getElementById('modal-finance-wallet');
            const resetPwForm = document.getElementById('modal-reset-password-form');

            if (financeRole) financeRole.textContent = u.role_label;
            if (financeWallet) financeWallet.textContent = u.wallet_balance;
            if (resetPwForm) resetPwForm.action = `/admin/users/${u.id}/reset-password`;

            // Tab 4: Activities
            const activitiesList = document.getElementById('modal-activities-list');
            if (activitiesList) {
                activitiesList.innerHTML = '';
                if (u.activities && u.activities.length > 0) {
                    u.activities.forEach(act => {
                        let iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>';
                        let bgClass = 'bg-teal-50 text-teal-600';
                        if (act.icon === 'cart') { 
                            iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>'; 
                            bgClass = 'bg-blue-50 text-blue-600'; 
                        }
                        if (act.icon === 'lock') { 
                            iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>'; 
                            bgClass = 'bg-indigo-50 text-indigo-600'; 
                        }
                        if (act.icon === 'user') { 
                            iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'; 
                            bgClass = 'bg-purple-50 text-purple-600'; 
                        }
                        if (act.icon === 'chat') { 
                            iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>'; 
                            bgClass = 'bg-pink-50 text-pink-600'; 
                        }

                        activitiesList.innerHTML += `
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full ${bgClass} flex items-center justify-center shrink-0 font-bold">
                                    ${iconSvg}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold text-gray-900 text-xs">${act.title}</p>
                                        <span class="text-[10px] text-gray-400 font-medium">${act.time}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5">${act.subtext}</p>
                                </div>
                            </div>
                        `;
                    });
                }
            }

            loading.classList.add('hidden');
            content.classList.remove('hidden');
        }
    } catch (e) {
        console.error(e);
        loading.innerHTML = '<p class="text-rose-500 py-6">Lỗi khi tải dữ liệu người dùng.</p>';
    }
}

export function closeUserDetailModal() {
    const modal = document.getElementById('admin-user-detail-modal');
    if (modal) modal.classList.add('hidden');
}

export function switchModalTab(tabId, btn) {
    document.querySelectorAll('.modal-tab-btn').forEach(b => {
        b.className = 'modal-tab-btn pb-3 pt-3 border-b-2 border-transparent text-gray-500 hover:text-gray-800 cursor-pointer font-bold flex items-center gap-1.5';
    });
    if (btn) {
        btn.className = 'modal-tab-btn active pb-3 pt-3 border-b-2 border-primary text-primary font-black cursor-pointer flex items-center gap-1.5';
    }

    document.querySelectorAll('.modal-section').forEach(sec => {
        sec.classList.add('hidden');
    });

    const targetSec = document.getElementById(`section-${tabId}`);
    if (targetSec) targetSec.classList.remove('hidden');
}

export function initAdminUsersPage() {
    // 1. Delegated click handlers
    document.addEventListener('click', (e) => {
        const viewUserBtn = e.target.closest('[data-view-user-id]');
        if (viewUserBtn) {
            e.preventDefault();
            const userId = viewUserBtn.getAttribute('data-view-user-id');
            openUserDetailModal(userId);
            return;
        }

        const closeBtn = e.target.closest('[data-close-user-modal]');
        if (closeBtn) {
            e.preventDefault();
            closeUserDetailModal();
            return;
        }

        const tabBtn = e.target.closest('.modal-tab-btn');
        if (tabBtn) {
            e.preventDefault();
            const tabId = tabBtn.getAttribute('data-tab-id') || 'profile';
            switchModalTab(tabId, tabBtn);
        }
    });

    // 2. Expose functions to window for backwards compatibility
    window.openUserDetailModal = openUserDetailModal;
    window.closeUserDetailModal = closeUserDetailModal;
    window.switchModalTab = switchModalTab;
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminUsersPage();
});
