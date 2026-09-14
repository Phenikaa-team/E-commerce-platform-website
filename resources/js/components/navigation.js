/**
 * Navigation, Menus & Search Components:
 * - Mobile bottom nav active indicator
 * - Top navbar mega dropdown
 * - Sidebar category flyout with rich details
 * - User account dropdown menu
 * - Smart live search with ajax suggestions
 */

import categoryFlyoutData from '../data/category-flyout.js';
import { initSmartCardBlend } from '../utils/image-blend.js';

/**
 * 5. Mobile Bottom Navigation active item switch
 */
export function initMobileNav() {
    const navItems = document.querySelectorAll('[data-mobile-nav]');
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            navItems.forEach(i => {
                i.classList.remove('text-[#ea384c]');
                i.classList.add('text-gray-500');
            });
            item.classList.remove('text-gray-500');
            item.classList.add('text-[#ea384c]');
        });
    });
}

/**
 * 6. Top Navbar "Danh mục sản phẩm" Detailed Mega Dropdown Menu
 */
export function initTopMegaMenu() {
    const btn = document.getElementById('top-mega-menu-btn');
    const dropdown = document.getElementById('top-mega-dropdown');
    const chevron = document.getElementById('top-mega-menu-chevron');

    if (!btn || !dropdown) return;

    let closeTimeout = null;

    const openMenu = () => {
        clearTimeout(closeTimeout);
        dropdown.classList.remove('hidden');
        if (chevron) chevron.classList.add('rotate-180');
    };

    const closeMenu = () => {
        closeTimeout = setTimeout(() => {
            dropdown.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }, 150);
    };

    btn.addEventListener('mouseenter', openMenu);
    btn.addEventListener('mouseleave', closeMenu);

    dropdown.addEventListener('mouseenter', openMenu);
    dropdown.addEventListener('mouseleave', closeMenu);

    btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (dropdown.classList.contains('hidden')) {
            openMenu();
        } else {
            dropdown.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });

    document.addEventListener('click', (e) => {
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });
}

/**
 * 7. Sidebar Flyout Sub-menu next to Hero Banner
 */
export function initSidebarFlyout() {
    const sidebar = document.getElementById('sidebar-categories');
    const flyout = document.getElementById('sidebar-flyout-panel');
    const flyoutContent = document.getElementById('sidebar-flyout-content');
    const items = document.querySelectorAll('[data-sidebar-item]');

    if (!sidebar || !flyout || !flyoutContent) return;

    let activeSlug = null;
    let hideTimer = null;

    const renderFlyout = (slug) => {
        const data = categoryFlyoutData[slug] || {
            title: 'Danh mục chi tiết',
            subtitle: 'Khám phá hàng ngàn sản phẩm chất lượng cao với ưu đãi hấp dẫn',
            iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`,
            topCards: [
                { title: 'Nổi bật 1', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=300&q=80' },
                { title: 'Nổi bật 2', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=300&q=80' },
                { title: 'Nổi bật 3', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=300&q=80' },
                { title: 'Nổi bật 4', image: 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=300&q=80' },
                { title: 'Nổi bật 5', image: 'https://images.unsplash.com/photo-1586105251261-72a756497a11?auto=format&fit=crop&w=300&q=80' }
            ],
            columns: [
                {
                    heading: 'SẢN PHẨM PHỔ BIẾN',
                    items: [
                        { name: 'Hàng bán chạy nhất' },
                        { name: 'Hàng mới cập bến' },
                        { name: 'Top đánh giá 5 sao' },
                        { name: 'Hàng chính hãng 100%' },
                        { name: 'Combo siêu tiết kiệm' },
                        { name: 'Sản phẩm độc quyền' }
                    ]
                },
                {
                    heading: 'PHÂN LOẠI CHI TIẾT',
                    items: [
                        { name: 'Dòng sản phẩm cao cấp' },
                        { name: 'Dòng sản phẩm phổ thông' },
                        { name: 'Bộ quà tặng tuyển chọn' },
                        { name: 'Phụ kiện đi kèm' },
                        { name: 'Gói bảo hành vàng' },
                        { name: 'Hàng nhập khẩu chính ngạch' }
                    ]
                },
                {
                    heading: 'ƯU ĐÃI NỔI BẬT',
                    items: [
                        { name: 'Giảm sốc cuối tuần' },
                        { name: 'Voucher giảm 50K' },
                        { name: 'Miễn phí vận chuyển' },
                        { name: 'Tặng quà tri ân' },
                        { name: 'Đổi trả 30 ngày' },
                        { name: 'Flash sale khung giờ vàng' }
                    ]
                }
            ],
            deal: {
                badge: 'HOT DEAL',
                title: 'Ưu đãi thành viên ShopMart',
                desc: 'Tích điểm đổi quà không giới hạn, giảm thêm 10% mỗi hóa đơn.',
                btnText: 'Xem ngay',
                image: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=400&q=80'
            }
        };

        const topCardsHtml = (data.topCards || []).map(card => `
            <a href="#cat-${slug}-${encodeURIComponent(card.title)}" class="bg-gray-50/70 hover:bg-white rounded-xl border border-gray-100 hover:border-rose-200 hover:shadow-xs p-2.5 flex flex-col items-center text-center group transition-all duration-200">
                <div class="w-14 h-14 rounded-lg bg-white border border-gray-100/80 flex items-center justify-center mb-1.5 p-1 overflow-hidden shadow-2xs group-hover:scale-108 transition-transform duration-200">
                    <img src="${card.image}" alt="${card.title}" class="w-full h-full object-contain" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=200&q=80';">
                </div>
                <span class="text-xs font-semibold text-gray-800 group-hover:text-[#ea384c] transition-colors truncate max-w-full block leading-tight px-0.5">${card.title}</span>
            </a>
        `).join('');

        const columnsHtml = (data.columns || []).map(col => `
            <div>
                <h4 class="font-extrabold text-gray-900 text-xs uppercase tracking-wider mb-2.5">
                    ${col.heading}
                </h4>
                <ul class="space-y-1.5">
                    ${(col.items || []).map(item => `
                        <li>
                            <a href="#search?q=${encodeURIComponent(item.name)}" class="flex items-center justify-between py-0.5 text-xs text-gray-600 hover:text-[#ea384c] group transition-colors">
                                <div class="flex items-center gap-2 truncate pr-1">
                                    ${item.iconHtml ? item.iconHtml : ''}
                                    <span class="group-hover:translate-x-0.5 transition-transform truncate">${item.name}</span>
                                </div>
                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `).join('');

        flyoutContent.innerHTML = `
            <div class="flex flex-col h-full justify-between gap-2.5">
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#ea384c] shrink-0">
                                ${data.iconSvg || '<svg class="w-4 h-4 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>'}
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-gray-900 tracking-tight leading-tight">${data.title}</h3>
                                <p class="text-[11px] text-gray-400 mt-0.5 leading-none">${data.subtitle || 'Khám phá thế giới công nghệ, kết nối mọi khoảnh khắc'}</p>
                            </div>
                        </div>
                        <a href="/category/${slug}" class="text-[11.5px] font-semibold text-[#ea384c] hover:underline flex items-center gap-1 group">
                            <span>Xem tất cả</span>
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>

                    <!-- Top 5 Visual Category Cards -->
                    <div class="grid grid-cols-5 gap-2 mt-2.5">
                        ${topCardsHtml}
                    </div>

                    <!-- Middle 3 Columns -->
                    <div class="grid grid-cols-3 gap-5 mt-3">
                        ${columnsHtml}
                    </div>
                </div>

                <!-- Bottom Promo Banner -->
                <div class="bg-gradient-to-r from-rose-50/90 via-pink-50/60 to-rose-100/40 rounded-xl p-3 border border-rose-100/80 relative overflow-hidden flex items-center justify-between mt-2">
                    <div class="relative z-10 max-w-[62%]">
                        <span class="inline-block px-2.5 py-0.5 bg-[#ea384c] text-white text-[9px] font-black rounded-full uppercase tracking-wider">
                            ${data.deal.badge || 'HOT DEAL'}
                        </span>
                        <h4 class="text-sm font-extrabold text-gray-900 mt-1 leading-snug">
                            ${data.deal.title}
                        </h4>
                        <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">
                            ${data.deal.desc}
                        </p>
                        <a href="#deal" class="inline-flex items-center gap-1 px-3.5 py-1 bg-[#ea384c] hover:bg-[#d3273b] text-white text-[11px] font-bold rounded-full mt-2 shadow-xs transition-transform active:scale-95">
                            <span>${data.deal.btnText || 'Mua ngay'}</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                    
                    <div class="w-44 h-24 relative flex items-center justify-end overflow-hidden shrink-0">
                        <img src="${data.deal.image}" alt="${data.deal.title}" class="h-full object-contain object-right drop-shadow-xl" loading="lazy">
                    </div>
                </div>
            </div>
        `;

        initSmartCardBlend(flyoutContent);
    };

    const clearActiveSidebarItems = () => {
        items.forEach(i => {
            i.classList.remove('bg-white', 'text-[#ea384c]', 'font-bold', 'border', 'border-rose-200', 'shadow-xs');
            const svgs = i.querySelectorAll('svg');
            if (svgs[0]) {
                svgs[0].classList.remove('text-[#ea384c]');
                svgs[0].classList.add('text-gray-400');
            }
            if (svgs[1]) {
                svgs[1].classList.remove('text-[#ea384c]');
                svgs[1].classList.add('text-gray-400');
            }
        });
    };

    items.forEach(item => {
        item.addEventListener('mouseenter', () => {
            clearTimeout(hideTimer);
            const slug = item.getAttribute('data-sidebar-item');
            activeSlug = slug;

            clearActiveSidebarItems();

            // Highlight active sidebar item like in screenshot
            item.classList.add('bg-white', 'text-[#ea384c]', 'font-bold', 'border', 'border-rose-200', 'shadow-xs');
            const activeSvgs = item.querySelectorAll('svg');
            if (activeSvgs[0]) {
                activeSvgs[0].classList.remove('text-gray-400');
                activeSvgs[0].classList.add('text-[#ea384c]');
            }
            if (activeSvgs[1]) {
                activeSvgs[1].classList.remove('text-gray-400');
                activeSvgs[1].classList.add('text-[#ea384c]');
            }

            renderFlyout(slug);
            flyout.classList.remove('hidden');
        });
    });

    const hideFlyout = () => {
        hideTimer = setTimeout(() => {
            flyout.classList.add('hidden');
            clearActiveSidebarItems();
            activeSlug = null;
        }, 150);
    };

    sidebar.addEventListener('mouseleave', hideFlyout);
    flyout.addEventListener('mouseenter', () => clearTimeout(hideTimer));
    flyout.addEventListener('mouseleave', hideFlyout);
}

/**
 * Global User Account Dropdown (Click Toggle + Click Outside Close)
 */
export function initUserDropdownMenus() {
    const triggers = document.querySelectorAll('#user-menu-toggle, #user-menu-dropdown-toggle, #user-menu-wrapper > button, #user-menu-dropdown > button');
    
    triggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const wrapper = trigger.closest('#user-menu-wrapper, #user-menu-dropdown, .group');
            if (!wrapper) return;
            const panel = wrapper.querySelector('#user-dropdown-panel, #profile-user-dropdown-panel');
            if (!panel) return;
            
            const isCurrentlyShown = (panel.style.display === 'block' || panel.classList.contains('active-open'));
            // Close other panels
            document.querySelectorAll('#user-dropdown-panel, #profile-user-dropdown-panel').forEach(p => {
                p.style.display = '';
                p.classList.remove('active-open');
            });
            
            if (!isCurrentlyShown) {
                panel.style.display = 'block';
                panel.classList.add('active-open');
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#user-menu-wrapper, #user-menu-dropdown, .group')) {
            document.querySelectorAll('#user-dropdown-panel, #profile-user-dropdown-panel').forEach(p => {
                p.style.display = '';
                p.classList.remove('active-open');
            });
        }
    });
}

/**
 * Smart Live Search with Ajax Suggestions & Keyboard Navigation
 */
export function initSmartSearch() {
    const input = document.getElementById('smart-search-input');
    const dropdown = document.getElementById('smart-search-dropdown');
    const loading = document.getElementById('search-loading');
    const content = document.getElementById('search-results-content');
    let debounceTimer = null;

    if (!input || !dropdown) return;

    const renderSuggestions = (data, q) => {
        let html = '';
        const hasProducts = data.products && data.products.length > 0;
        const hasCategories = data.categories && data.categories.length > 0;
        const hasBrands = data.brands && data.brands.length > 0;
        const hasStores = data.stores && data.stores.length > 0;

        if (!hasProducts && !hasCategories && !hasBrands && !hasStores) {
            content.innerHTML = `
                <div class="py-6 text-center text-xs text-gray-400">
                    <p class="font-medium text-gray-500">Không tìm thấy kết quả phù hợp cho "${escapeHtml(q)}"</p>
                    <p class="mt-1 text-[11px] text-gray-400">Vui lòng thử tìm với từ khóa khác hoặc kiểm tra lại chính tả</p>
                </div>
            `;
            return;
        }

        // 1. Categories Section
        if (hasCategories) {
            html += `
                <div class="mb-3">
                    <div class="flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 px-1">
                        <span>Danh mục</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 px-1">
                        ${data.categories.map(c => `
                            <a href="${c.url || ('/category/' + c.slug)}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 hover:bg-rose-50 hover:text-[#ea384c] rounded-lg text-xs font-medium text-gray-700 transition-colors">
                                <svg class="w-3 h-3 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                <span>${escapeHtml(c.name)}</span>
                            </a>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // 2. Brands Section
        if (hasBrands) {
            html += `
                <div class="mb-3 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 px-1">
                        <span>Thương hiệu</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 px-1">
                        ${data.brands.map(b => `
                            <a href="${b.url || ('/brand/' + encodeURIComponent(b.name))}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 hover:bg-rose-50 hover:text-[#ea384c] rounded-lg text-xs font-medium text-gray-700 transition-colors">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#ea384c]"></span>
                                <span>${escapeHtml(b.name)}</span>
                            </a>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // 3. Stores Section
        if (hasStores) {
            html += `
                <div class="mb-3 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 px-1">
                        <span>Cửa hàng liên quan</span>
                    </div>
                    <div class="space-y-1">
                        ${data.stores.map(s => `
                            <a href="${s.url}" class="flex items-center justify-between p-2 rounded-xl hover:bg-rose-50/60 transition-colors group">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <img src="${s.logo || '/images/placeholders/store-logo-placeholder.svg'}" alt="${escapeHtml(s.name)}" class="w-8 h-8 object-cover rounded-full border border-gray-200 shrink-0">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-bold text-gray-800 group-hover:text-[#ea384c] truncate">${escapeHtml(s.name)}</span>
                                            ${s.is_mall ? '<span class="px-1.5 py-0.2 bg-[#ea384c] text-white text-[9px] font-black rounded uppercase">Mall</span>' : ''}
                                        </div>
                                        <span class="text-[10px] text-gray-400">Xem gian hàng</span>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // 4. Products Section
        if (hasProducts) {
            html += `
                <div class="mb-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 px-1">
                        <span>Sản phẩm gợi ý</span>
                        ${data.total_products ? `<span class="text-[10px] text-gray-400 lowercase font-normal">tìm thấy ${data.total_products} sản phẩm</span>` : ''}
                    </div>
                    <div class="space-y-1.5">
                        ${data.products.map(p => `
                            <a href="${p.url}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-rose-50/60 transition-colors group">
                                <img src="${p.image || 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=100&q=80'}" class="w-11 h-11 object-cover rounded-lg border border-gray-100 shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-800 group-hover:text-[#ea384c] truncate">${escapeHtml(p.name)}</p>
                                    <div class="flex items-center justify-between mt-0.5">
                                        <p class="text-xs font-extrabold text-[#ea384c]">${p.price}</p>
                                        ${p.store ? `<span class="text-[10px] text-gray-400 truncate max-w-[120px]">${escapeHtml(p.store)}</span>` : ''}
                                    </div>
                                </div>
                            </a>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // 5. "See all results" Footer Button
        const viewAllUrl = data.view_all_url || `/search?q=${encodeURIComponent(q)}`;
        html += `
            <div class="pt-2.5 border-t border-gray-100 text-center">
                <a href="${viewAllUrl}" class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3 bg-gray-50 hover:bg-rose-50 text-gray-700 hover:text-[#ea384c] font-bold text-xs rounded-xl transition-all group">
                    <span>Xem tất cả kết quả cho "${escapeHtml(q)}"</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        `;

        content.innerHTML = html;
    };

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const q = input.value.trim();
        if (q.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }

        dropdown.classList.remove('hidden');
        if (loading) loading.classList.remove('hidden');
        if (content) content.innerHTML = '';

        debounceTimer = setTimeout(() => {
            fetch(`/api/search/suggestions?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    if (loading) loading.classList.add('hidden');
                    renderSuggestions(data, q);
                })
                .catch(() => {
                    if (loading) loading.classList.add('hidden');
                });
        }, 250);
    });

    // Keyboard support: Escape closes dropdown
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#header-search-container')) {
            dropdown.classList.add('hidden');
        }
    });
}
