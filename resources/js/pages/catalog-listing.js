/**
 * Catalog Listing Page Script
 * Manages Mobile Filter Drawer & Actions
 */

export function initCatalogListing() {
    const openBtn = document.getElementById('mobile-filter-open-btn');
    const closeBtn = document.getElementById('mobile-filter-close-btn');
    const drawer = document.getElementById('mobile-filter-drawer');
    const backdrop = document.getElementById('mobile-filter-backdrop');

    const toggleDrawer = (show) => {
        if (!drawer) return;
        if (show) {
            drawer.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            drawer.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    if (openBtn) openBtn.addEventListener('click', () => toggleDrawer(true));
    if (closeBtn) closeBtn.addEventListener('click', () => toggleDrawer(false));
    if (backdrop) backdrop.addEventListener('click', () => toggleDrawer(false));
}

document.addEventListener('DOMContentLoaded', () => {
    initCatalogListing();
});
