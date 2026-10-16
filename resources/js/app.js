/**
 * ShopMart Interactive Features — Application Entry Point
 * Modularized architecture: Components, Utilities & Data
 */

import { initCountdown, initHeroCarousel, initRecommendedTabs } from './components/home.js';
import { initCart, initAddToCartToast, showToast, updateAllCartBadges } from './components/cart.js';
import { initMobileNav, initTopMegaMenu, initSidebarFlyout, initUserDropdownMenus, initSmartSearch, initInstantNavigation } from './components/navigation.js';
import { initPageLoader } from './components/page-loader.js';
import { initProductGallery, initMobileImageSwipe, initProductTabs, initQuantitySelector, initVariantSelector, initProductTracking } from './components/product-detail.js';
import { initUserSidebar } from './components/user-sidebar.js';
import { initThirdPartyPasswordAlert } from './components/password-alert.js';
import { initChatAi } from './components/chat-ai.js';
import { initStoreChat } from './components/store-chat.js';
import { initOrderCountdowns } from './components/order-countdown.js';
import './components/notifications.js';

document.addEventListener('DOMContentLoaded', () => {
    // 0. Instant Global Page Loader
    initPageLoader();

    // 1. Navigation, Menus & Smart Search
    initMobileNav();
    initTopMegaMenu();
    initSidebarFlyout();
    initInstantNavigation();

    initUserDropdownMenus();
    initSmartSearch();

    // 2. Global Cart & Add to Cart Toast
    initCart();
    initAddToCartToast();

    // 3. Homepage Widgets
    initCountdown();
    initHeroCarousel();
    initRecommendedTabs();

    // 4. Product Detail Gallery, Tabs, Variants & Interaction Tracking
    initProductGallery();
    initMobileImageSwipe();
    initProductTabs();
    initQuantitySelector();
    initVariantSelector();
    initProductTracking();

    // 5. User Sidebar & Security Alerts
    initUserSidebar();
    initThirdPartyPasswordAlert();

    // 6. ShopMart Floating AI Chat Assistant
    initChatAi();
    initStoreChat();

    // 7. Order Payment Realtime Countdowns
    initOrderCountdowns();
});

// Re-export common utilities for module inter-operability
export { showToast, updateAllCartBadges };

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
