/**
 * ShopMart Realtime Notification Bell Component
 * Handles notification fetching, dropdown display, mark-as-read, and unread badge updates
 * for User (Buyer), Seller, and Admin.
 */
document.addEventListener('DOMContentLoaded', () => {
    const bellWrappers = document.querySelectorAll('[id^="notification-bell-wrapper-"]');
    if (!bellWrappers.length) return;

    bellWrappers.forEach(wrapper => {
        const context = wrapper.dataset.context || 'user';
        const triggerBtn = document.getElementById(`notification-bell-btn-${context}`);
        const dropdown = document.getElementById(`notification-dropdown-${context}`);
        const badge = document.getElementById(`notification-badge-${context}`);
        const headerCount = document.getElementById(`notification-header-count-${context}`);
        const listContainer = document.getElementById(`notification-list-${context}`);
        const markAllBtn = document.getElementById(`notification-mark-all-${context}`);

        if (!triggerBtn || !dropdown) return;

        let hasLoaded = false;

        // Toggle dropdown
        triggerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = dropdown.classList.contains('hidden');
            
            // Close any other open notification dropdowns
            document.querySelectorAll('.notification-dropdown-panel').forEach(p => {
                if (p !== dropdown) p.classList.add('hidden');
            });

            if (isHidden) {
                dropdown.classList.remove('hidden');
                triggerBtn.setAttribute('aria-expanded', 'true');
                fetchNotifications();
            } else {
                dropdown.classList.add('hidden');
                triggerBtn.setAttribute('aria-expanded', 'false');
            }
        });

        // Mark all as read
        markAllBtn?.addEventListener('click', async (e) => {
            e.stopPropagation();
            try {
                const res = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ context })
                });
                if (res.ok) {
                    updateBadge(0);
                    // Update items visually
                    listContainer.querySelectorAll('.notification-item-unread').forEach(item => {
                        item.classList.remove('notification-item-unread', 'bg-rose-50/40');
                        item.classList.add('bg-white');
                        const dot = item.querySelector('.notification-dot');
                        if (dot) dot.remove();
                    });
                }
            } catch (err) {
                console.error('Failed to mark all notifications read:', err);
            }
        });

        // Initial background fetch to display unread badge on page load
        fetchNotifications(true);

        async function fetchNotifications(silent = false) {
            try {
                const res = await fetch(`/notifications?context=${encodeURIComponent(context)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!res.ok) return;
                const data = await res.json();

                updateBadge(data.unread_count || 0);

                if (!silent || !hasLoaded) {
                    renderNotificationList(data.notifications || []);
                    hasLoaded = true;
                }
            } catch (err) {
                if (!silent) {
                    listContainer.innerHTML = `
                        <div class="p-6 text-center text-xs text-gray-400">
                            Không thể tải thông báo. Vui lòng thử lại sau.
                        </div>
                    `;
                }
            }
        }

        function updateBadge(count) {
            if (!badge) return;
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            if (headerCount) {
                headerCount.textContent = count > 0 ? `${count} mới` : 'Tất cả đã đọc';
            }
        }

        function renderNotificationList(items) {
            if (!items.length) {
                listContainer.innerHTML = `
                    <div class="p-8 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-xs font-semibold text-gray-600">Bạn chưa có thông báo nào</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Các cập nhật đơn hàng và tài khoản sẽ hiển thị tại đây.</p>
                    </div>
                `;
                return;
            }

            listContainer.innerHTML = items.map(item => {
                const isUnread = !item.is_read;
                const iconSvg = getNotificationIcon(item.type);
                const linkHref = item.link || '#';

                return `
                    <a 
                        href="${escapeHtml(linkHref)}" 
                        data-id="${item.id}"
                        class="notification-item flex items-start gap-3 p-3.5 hover:bg-gray-50 transition-colors text-left relative ${isUnread ? 'notification-item-unread bg-rose-50/40' : 'bg-white'}"
                    >
                        <div class="notification-icon-wrap w-8 h-8 rounded-lg flex items-center justify-center shrink-0 ${getIconBackground(item.type)}">
                            ${iconSvg}
                        </div>
                        <div class="flex-1 min-w-0 pr-2">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-xs font-bold text-gray-900 truncate ${isUnread ? 'font-black' : ''}">${escapeHtml(item.title)}</h4>
                                <span class="text-[10px] text-gray-400 shrink-0">${escapeHtml(item.created_at_human)}</span>
                            </div>
                            <p class="text-[11px] text-gray-600 line-clamp-2 mt-0.5 leading-relaxed">${escapeHtml(item.message)}</p>
                        </div>
                        ${isUnread ? '<span class="notification-dot w-2 h-2 rounded-full bg-rose-600 absolute right-3 top-4 shrink-0"></span>' : ''}
                    </a>
                `;
            }).join('');

            // Click item to mark as read
            listContainer.querySelectorAll('.notification-item').forEach(el => {
                el.addEventListener('click', async (e) => {
                    const id = el.dataset.id;
                    if (el.classList.contains('notification-item-unread')) {
                        try {
                            await fetch(`/notifications/${id}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                    'Accept': 'application/json'
                                }
                            });
                        } catch (err) {
                            console.error(err);
                        }
                    }
                });
            });
        }

        function getNotificationIcon(type) {
            switch (type) {
                case 'order':
                    return '<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>';
                case 'wallet':
                    return '<svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                case 'promo':
                    return '<svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>';
                case 'review':
                    return '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>';
                default:
                    return '<svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            }
        }

        function getIconBackground(type) {
            switch (type) {
                case 'order': return 'bg-emerald-50';
                case 'wallet': return 'bg-amber-50';
                case 'promo': return 'bg-rose-50';
                case 'review': return 'bg-blue-50';
                default: return 'bg-purple-50';
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('[id^="notification-bell-wrapper-"]')) {
            document.querySelectorAll('.notification-dropdown-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            document.querySelectorAll('.notification-bell-trigger').forEach(btn => {
                btn.setAttribute('aria-expanded', 'false');
            });
        }
    });
});
