/**
 * ShopMart Interactive Features — Application Entry Point
 * Modularized architecture: Components, Utilities & Data
 */

import { initCountdown, initHeroCarousel, initRecommendedTabs } from './components/home.js';
import { initCartAndWishlist, initAddToCartToast, showToast, updateAllCartBadges } from './components/cart.js';
import { initCartPageInteractions } from './components/cart-page.js';
import { initMobileNav, initTopMegaMenu, initSidebarFlyout, initUserDropdownMenus, initSmartSearch } from './components/navigation.js';
import { initProductGallery, initMobileImageSwipe, initProductTabs, initQuantitySelector, initVariantSelector } from './components/product-detail.js';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Navigation, Menus & Smart Search
    initMobileNav();
    initTopMegaMenu();
    initSidebarFlyout();
    initUserDropdownMenus();
    initSmartSearch();

    // 2. Global Cart, Wishlist & Add to Cart Toast
    initCartAndWishlist();
    initAddToCartToast();

    // 3. Homepage Widgets
    initCountdown();
    initHeroCarousel();
    initRecommendedTabs();

    // 4. Cart Page & Checkout Stepper
    initCartPageInteractions();

    // 5. Product Detail Gallery, Tabs & Variants
    initProductGallery();
    initMobileImageSwipe();
    initProductTabs();
    initQuantitySelector();
    initVariantSelector();
});

// Re-export common utilities for module inter-operability
export { showToast, updateAllCartBadges };
