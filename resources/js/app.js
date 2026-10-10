/**
 * ShopMart Interactive Features — Application Entry Point
 * Modularized architecture: Components, Utilities & Data
 */

import { initCountdown, initHeroCarousel, initRecommendedTabs } from './components/home.js';
import { initCart, initAddToCartToast, showToast, updateAllCartBadges } from './components/cart.js';
import { initCartPageInteractions } from './components/cart-page.js';
import { initMobileNav, initTopMegaMenu, initSidebarFlyout, initUserDropdownMenus, initSmartSearch } from './components/navigation.js';
import { initProductGallery, initMobileImageSwipe, initProductTabs, initQuantitySelector, initVariantSelector, initProductTracking } from './components/product-detail.js';
import { initUserSidebar } from './components/user-sidebar.js';
import { initThirdPartyPasswordAlert } from './components/password-alert.js';
import { initChatAi } from './components/chat-ai.js';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Navigation, Menus & Smart Search
    initMobileNav();
    initTopMegaMenu();
    initSidebarFlyout();

    initUserDropdownMenus();
    initSmartSearch();

    // 2. Global Cart & Add to Cart Toast
    initCart();
    initAddToCartToast();

    // 3. Homepage Widgets
    initCountdown();
    initHeroCarousel();
    initRecommendedTabs();

    // 4. Cart Page & Checkout Stepper
    initCartPageInteractions();

    // 5. Product Detail Gallery, Tabs, Variants & Interaction Tracking
    initProductGallery();
    initMobileImageSwipe();
    initProductTabs();
    initQuantitySelector();
    initVariantSelector();
    initProductTracking();

    // 6. User Sidebar & Security Alerts
    initUserSidebar();
    initThirdPartyPasswordAlert();

    // 7. ShopMart Floating AI Chat Assistant
    initChatAi();
});

// Re-export common utilities for module inter-operability
export { showToast, updateAllCartBadges };
