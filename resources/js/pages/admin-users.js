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

/**
 * Store Detail Modal Functions
 */
export async function openStoreDetailModal(storeId) {
    const modal = document.getElementById('admin-store-detail-modal');
    const loading = document.getElementById('store-modal-loading');
    const content = document.getElementById('store-modal-content');
    if (!modal || !loading || !content) return;

    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    content.classList.add('hidden');

    // Reset to first tab
    switchStoreModalTab('overview', document.querySelector('.store-modal-tab-btn'));

    try {
        const res = await fetch(`/admin/stores/${storeId}`);
        const data = await res.json();

        if (data.success) {
            const st = data.store;

            // Identity
            const idEl = document.getElementById('modal-store-id');
            const logoEl = document.getElementById('modal-store-logo');
            const nameEl = document.getElementById('modal-store-name');
            const slugEl = document.getElementById('modal-store-slug');
            const statusBadge = document.getElementById('modal-store-status-badge');
            const statusDot = document.getElementById('modal-store-status-dot');
            const mallBadge = document.getElementById('modal-store-mall-badge');
            const planBadge = document.getElementById('modal-store-plan-badge');
            const bTypeEl = document.getElementById('modal-store-business-type');
            const phoneEl = document.getElementById('modal-store-phone');
            const addressEl = document.getElementById('modal-store-address');

            if (idEl) idEl.textContent = st.id;
            if (logoEl) logoEl.src = st.logo_url;
            if (nameEl) nameEl.textContent = st.name;
            if (slugEl) slugEl.textContent = st.slug;
            if (bTypeEl) bTypeEl.textContent = st.business_type_label;
            if (phoneEl) phoneEl.querySelector('span').textContent = st.phone || 'Chưa có SĐT';
            if (addressEl) addressEl.querySelector('span').textContent = st.address || 'Chưa cập nhật địa chỉ kho';

            // Plan badge
            if (planBadge) {
                planBadge.textContent = `Gói: ${st.package_plan_label}`;
                if (st.package_plan === 'enterprise') {
                    planBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300';
                } else if (st.package_plan === 'pro') {
                    planBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300';
                } else {
                    planBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200';
                }
            }

            // Mall badge
            if (mallBadge) {
                if (st.is_mall) {
                    mallBadge.classList.remove('hidden');
                } else {
                    mallBadge.classList.add('hidden');
                }
            }

            // Status badge & dot
            if (statusBadge) {
                statusBadge.textContent = st.status_label;
                if (st.status === 'active') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60';
                    if (statusDot) statusDot.className = 'w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0';
                } else if (st.status === 'pending') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200';
                    if (statusDot) statusDot.className = 'w-3.5 h-3.5 rounded-full bg-amber-500 border-2 border-white absolute bottom-0 right-0';
                } else {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-200/60';
                    if (statusDot) statusDot.className = 'w-3.5 h-3.5 rounded-full bg-rose-500 border-2 border-white absolute bottom-0 right-0';
                }
            }

            // Tab 1: Overview Metrics
            const prodCountEl = document.getElementById('modal-store-products-count');
            const ordersCountEl = document.getElementById('modal-store-orders-count');
            const revenueEl = document.getElementById('modal-store-revenue');
            const ratingFollowEl = document.getElementById('modal-store-rating-follow');

            if (prodCountEl) prodCountEl.textContent = st.products_count;
            if (ordersCountEl) ordersCountEl.textContent = st.orders_count;
            if (revenueEl) revenueEl.textContent = st.total_revenue;
            if (ratingFollowEl) ratingFollowEl.textContent = `${st.rating} • ${st.followers} theo dõi`;

            // Owner Details
            const ownerRoleEl = document.getElementById('modal-store-owner-role');
            const ownerAvatarEl = document.getElementById('modal-store-owner-avatar');
            const ownerNameEl = document.getElementById('modal-store-owner-name');
            const ownerEmailEl = document.getElementById('modal-store-owner-email');
            const ownerPhoneEl = document.getElementById('modal-store-owner-phone');

            if (ownerRoleEl) ownerRoleEl.textContent = (st.owner.role || 'SELLER').toUpperCase();
            if (ownerAvatarEl) ownerAvatarEl.src = st.owner.avatar_url;
            if (ownerNameEl) ownerNameEl.textContent = st.owner.name;
            if (ownerEmailEl) ownerEmailEl.textContent = st.owner.email;
            if (ownerPhoneEl) ownerPhoneEl.textContent = st.owner.phone || 'Chưa có SĐT';

            // Store metadata
            const createdAtEl = document.getElementById('modal-store-created-at');
            const descEl = document.getElementById('modal-store-description');
            const planNameEl = document.getElementById('modal-store-plan-name');

            if (createdAtEl) createdAtEl.textContent = `Tham gia: ${st.created_at}`;
            if (descEl) descEl.textContent = st.description || 'Chưa cập nhật mô tả.';
            if (planNameEl) planNameEl.textContent = st.package_plan_label;

            // Forms & Actions
            const toggleMallForm = document.getElementById('modal-store-toggle-mall-form');
            const toggleMallBtn = document.getElementById('modal-store-toggle-mall-btn');
            if (toggleMallForm) toggleMallForm.action = `/admin/stores/${st.id}/toggle-status`;
            if (toggleMallBtn) {
                toggleMallBtn.textContent = st.is_mall ? 'Hủy ShopMall' : 'Cấp quyền ShopMall';
            }

            const toggleStatusForm = document.getElementById('modal-store-toggle-status-form');
            const toggleStatusBtn = document.getElementById('modal-store-toggle-status-btn');
            if (toggleStatusForm) toggleStatusForm.action = `/admin/stores/${st.id}/toggle-status`;
            if (toggleStatusBtn) {
                toggleStatusBtn.textContent = st.status === 'banned' ? 'Mở khóa Shop' : 'Khóa gian hàng';
                toggleStatusBtn.className = st.status === 'banned' 
                    ? 'px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors cursor-pointer'
                    : 'px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 transition-colors cursor-pointer';
            }

            // Pending specific actions
            const pendingActions = document.getElementById('modal-store-pending-actions');
            const approveForm = document.getElementById('modal-store-approve-form');
            const rejectForm = document.getElementById('modal-store-reject-form');
            if (pendingActions) {
                if (st.status === 'pending') {
                    pendingActions.classList.remove('hidden');
                    pendingActions.classList.add('flex');
                    if (approveForm) approveForm.action = `/admin/stores/${st.id}/approve`;
                    if (rejectForm) rejectForm.action = `/admin/stores/${st.id}/reject`;
                } else {
                    pendingActions.classList.add('hidden');
                    pendingActions.classList.remove('flex');
                }
            }

            // Tab 2: Legal KYC
            const legalTypeEl = document.getElementById('modal-store-legal-type');
            const legalRepEl = document.getElementById('modal-store-legal-rep');
            const legalTaxEl = document.getElementById('modal-store-legal-tax');
            const legalIdcardEl = document.getElementById('modal-store-legal-idcard');

            if (legalTypeEl) legalTypeEl.textContent = st.business_type_label;
            if (legalRepEl) legalRepEl.textContent = st.representative_name || st.owner.name || 'N/A';
            if (legalTaxEl) legalTaxEl.textContent = st.tax_code || 'Không áp dụng (Cá nhân)';
            if (legalIdcardEl) legalIdcardEl.textContent = st.id_card_number || 'Chưa cung cấp';

            // Document previews
            const licensePreview = document.getElementById('modal-store-license-preview');
            const licenseLink = document.getElementById('modal-store-license-link');
            if (st.business_license_image) {
                licensePreview.innerHTML = `<img src="${st.business_license_image}" class="w-full h-full object-contain cursor-pointer" onclick="window.open('${st.business_license_image}')">`;
                if (licenseLink) {
                    licenseLink.href = st.business_license_image;
                    licenseLink.classList.remove('hidden');
                }
            } else {
                licensePreview.innerHTML = '<span class="text-xs text-gray-400 italic">Không yêu cầu hoặc chưa tải lên GPKD</span>';
                if (licenseLink) licenseLink.classList.add('hidden');
            }

            const idcardPreview = document.getElementById('modal-store-idcard-preview');
            const idcardLink = document.getElementById('modal-store-idcard-link');
            if (st.id_card_image) {
                idcardPreview.innerHTML = `<img src="${st.id_card_image}" class="w-full h-full object-contain cursor-pointer" onclick="window.open('${st.id_card_image}')">`;
                if (idcardLink) {
                    idcardLink.href = st.id_card_image;
                    idcardLink.classList.remove('hidden');
                }
            } else {
                idcardPreview.innerHTML = '<span class="text-xs text-gray-400 italic">Chưa tải lên ảnh CCCD</span>';
                if (idcardLink) idcardLink.classList.add('hidden');
            }

            // Tab 3: Banking & Shipping
            const bankNameEl = document.getElementById('modal-store-bank-name');
            const bankAccEl = document.getElementById('modal-store-bank-acc');
            const bankHolderEl = document.getElementById('modal-store-bank-holder');

            if (bankNameEl) bankNameEl.textContent = st.bank_name || 'CHƯA LIÊN KẾT NGÂN HÀNG';
            if (bankAccEl) bankAccEl.textContent = st.bank_account_number || '---- ---- ----';
            if (bankHolderEl) bankHolderEl.textContent = st.bank_account_name || st.owner.name;

            const shippingList = document.getElementById('modal-store-shipping-list');
            if (shippingList) {
                shippingList.innerHTML = '';
                if (st.shipping_partners && st.shipping_partners.length > 0) {
                    st.shipping_partners.forEach(partner => {
                        const nameMap = {
                            ghn: 'Giao Hàng Nhanh (GHN)',
                            ghtk: 'Giao Hàng Tiết Kiệm (GHTK)',
                            viettel: 'Viettel Post',
                            jnt: 'J&T Express'
                        };
                        shippingList.innerHTML += `<span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">${nameMap[partner] || partner.toUpperCase()}</span>`;
                    });
                } else {
                    shippingList.innerHTML = '<span class="text-xs text-gray-400 italic">Mặc định tiêu chuẩn toàn sàn</span>';
                }
            }

            const paymentList = document.getElementById('modal-store-payment-list');
            if (paymentList) {
                paymentList.innerHTML = '';
                if (st.payment_methods && st.payment_methods.length > 0) {
                    st.payment_methods.forEach(method => {
                        const methodMap = {
                            cod: 'Tiền mặt khi nhận hàng (COD)',
                            vnpay: 'VNPAY QR & Thẻ nội địa',
                            momo: 'Ví điện tử MoMo',
                            bank_transfer: 'Chuyển khoản trực tiếp'
                        };
                        paymentList.innerHTML += `<span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">${methodMap[method] || method.toUpperCase()}</span>`;
                    });
                } else {
                    paymentList.innerHTML = '<span class="text-xs text-gray-400 italic">Mặc định COD & VNPAY sàn</span>';
                }
            }

            // Tab 4: Products Grid
            const viewShopLink = document.getElementById('modal-store-view-shop-link');
            if (viewShopLink) viewShopLink.href = `/stores/${st.slug}`;

            const productsGrid = document.getElementById('modal-store-products-grid');
            if (productsGrid) {
                productsGrid.innerHTML = '';
                if (st.recent_products && st.recent_products.length > 0) {
                    st.recent_products.forEach(p => {
                        productsGrid.innerHTML += `
                            <div class="p-2.5 rounded-xl border border-gray-100 bg-white hover:border-gray-200 transition-all flex flex-col justify-between">
                                <img src="${p.thumbnail_url}" class="w-full h-24 object-cover rounded-lg bg-gray-50 mb-2">
                                <div>
                                    <h5 class="font-bold text-gray-900 text-xs line-clamp-2">${p.name}</h5>
                                    <div class="flex items-center justify-between mt-2 pt-1 border-t border-gray-50">
                                        <span class="font-black text-primary text-xs">${p.price}</span>
                                        <span class="text-[10px] text-gray-400">Kho: ${p.stock}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    productsGrid.innerHTML = '<div class="col-span-4 py-8 text-center text-gray-400 italic">Gian hàng chưa có sản phẩm nào đăng bán.</div>';
                }
            }

            loading.classList.add('hidden');
            content.classList.remove('hidden');
        }
    } catch (e) {
        console.error(e);
        loading.innerHTML = '<p class="text-rose-500 py-6">Lỗi khi tải dữ liệu gian hàng.</p>';
    }
}

export function closeStoreDetailModal() {
    const modal = document.getElementById('admin-store-detail-modal');
    if (modal) modal.classList.add('hidden');
}

export function switchStoreModalTab(tabId, btn) {
    document.querySelectorAll('.store-modal-tab-btn').forEach(b => {
        b.className = 'store-modal-tab-btn pb-3 pt-3 border-b-2 border-transparent text-gray-500 hover:text-gray-800 cursor-pointer font-bold flex items-center gap-1.5';
    });
    if (btn) {
        btn.className = 'store-modal-tab-btn active pb-3 pt-3 border-b-2 border-primary text-primary font-black cursor-pointer flex items-center gap-1.5';
    }

    document.querySelectorAll('.store-modal-section').forEach(sec => {
        sec.classList.add('hidden');
    });

    const targetSec = document.getElementById(`store-section-${tabId}`);
    if (targetSec) targetSec.classList.remove('hidden');
}

export function initAdminUsersPage() {
    // 1. Delegated click handlers
    document.addEventListener('click', (e) => {
        // User Inspector
        const viewUserBtn = e.target.closest('[data-view-user-id]');
        if (viewUserBtn) {
            e.preventDefault();
            const userId = viewUserBtn.getAttribute('data-view-user-id');
            openUserDetailModal(userId);
            return;
        }

        const closeUserBtn = e.target.closest('[data-close-user-modal]');
        if (closeUserBtn) {
            e.preventDefault();
            closeUserDetailModal();
            return;
        }

        const tabBtn = e.target.closest('.modal-tab-btn');
        if (tabBtn) {
            e.preventDefault();
            const tabId = tabBtn.getAttribute('data-tab-id') || 'profile';
            switchModalTab(tabId, tabBtn);
            return;
        }

        // Store Inspector
        const viewStoreBtn = e.target.closest('[data-view-store-id]');
        if (viewStoreBtn) {
            e.preventDefault();
            const storeId = viewStoreBtn.getAttribute('data-view-store-id');
            openStoreDetailModal(storeId);
            return;
        }

        const closeStoreBtn = e.target.closest('[data-close-store-modal]');
        if (closeStoreBtn) {
            e.preventDefault();
            closeStoreDetailModal();
            return;
        }

        const storeTabBtn = e.target.closest('.store-modal-tab-btn');
        if (storeTabBtn) {
            e.preventDefault();
            const tabId = storeTabBtn.getAttribute('data-store-tab-id') || 'overview';
            switchStoreModalTab(tabId, storeTabBtn);
            return;
        }
    });

    // 2. Expose functions to window for backwards compatibility
    window.openUserDetailModal = openUserDetailModal;
    window.closeUserDetailModal = closeUserDetailModal;
    window.switchModalTab = switchModalTab;

    window.openStoreDetailModal = openStoreDetailModal;
    window.closeStoreDetailModal = closeStoreDetailModal;
    window.switchStoreModalTab = switchStoreModalTab;
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminUsersPage();
});
