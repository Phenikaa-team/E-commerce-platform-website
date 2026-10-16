/**
 * Global Page Loader & Instant Frontend Transition Orchestrator
 *
 * Requirements fulfilled:
 * 1. Keep the top progress bar.
 * 2. Instant Frontend Rendering: Upon click, preserve the existing Header, Navigation,
 *    and Sidebar layouts without freezing the screen.
 * 3. Replace the content area immediately with a skeleton/content placeholder of the next page
 *    so the frontend renders immediately while the backend query completes.
 * 4. Replace the blocking center modal with a non-blocking subtle circle spinner badge.
 */

class PageLoader {
    constructor() {
        this.progressBar = null;
        this.cornerSpinner = null;
        this.progressTimer = null;
        this.currentProgress = 0;
        this.isNavigating = false;
    }

    init() {
        if (typeof window === 'undefined' || typeof document === 'undefined') return;

        this.ensureDomElements();
        this.bindEvents();
    }

    ensureDomElements() {
        // Top progress bar
        let bar = document.getElementById('page-progress-bar');
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'page-progress-bar';
            document.body.prepend(bar);
        }
        this.progressBar = bar;

        // Subtle non-blocking corner circle spinner
        let corner = document.getElementById('page-corner-spinner');
        if (!corner) {
            corner = document.createElement('div');
            corner.id = 'page-corner-spinner';
            corner.setAttribute('aria-live', 'polite');
            corner.innerHTML = `
                <div class="page-corner-spin-icon"></div>
                <span class="page-corner-text">Đang tải...</span>
            `;
            document.body.appendChild(corner);
        }
        this.cornerSpinner = corner;

        // Clean up any old blocking overlay if it exists in DOM
        const oldOverlay = document.getElementById('page-loader-overlay');
        if (oldOverlay) {
            oldOverlay.remove();
        }
    }

    start(message = 'Đang tải...') {
        this.ensureDomElements();
        this.isNavigating = true;

        if (this.cornerSpinner) {
            const textEl = this.cornerSpinner.querySelector('.page-corner-text');
            if (textEl && message) textEl.textContent = message;
            this.cornerSpinner.classList.add('is-active');
        }

        if (this.progressBar) {
            this.progressBar.classList.add('is-loading');
            this.currentProgress = 20;
            this.progressBar.style.width = `${this.currentProgress}%`;

            clearInterval(this.progressTimer);
            this.progressTimer = setInterval(() => {
                if (this.currentProgress < 85) {
                    const step = Math.max(1, Math.floor((90 - this.currentProgress) / 7));
                    this.currentProgress += step;
                    if (this.progressBar) {
                        this.progressBar.style.width = `${this.currentProgress}%`;
                    }
                }
            }, 100);
        }
    }

    stop() {
        this.isNavigating = false;
        clearInterval(this.progressTimer);

        if (this.progressBar) {
            this.progressBar.style.width = '100%';
            setTimeout(() => {
                if (this.progressBar) {
                    this.progressBar.classList.remove('is-loading');
                    this.progressBar.style.width = '0%';
                }
            }, 250);
        }

        if (this.cornerSpinner) {
            this.cornerSpinner.classList.remove('is-active');
        }

        const main = document.querySelector('main');
        if (main) {
            main.style.opacity = '1';
        }
    }

    /**
     * Instantly renders the frontend skeleton of the target page before the backend finishes querying
     */
    renderTargetSkeleton(targetPath) {
        const main = document.querySelector('main');
        if (!main) return;

        const path = (targetPath || '').toLowerCase();

        // 1. Seller & Admin Table/Dashboard views
        if (path.includes('/seller') || path.includes('/admin')) {
            if (path.includes('order') || path.includes('product') || path.includes('coupon') || path.includes('user') || path.includes('categor')) {
                main.innerHTML = `
                    <div class="page-loading-skeleton-container space-y-5">
                        <div class="flex items-center justify-between">
                            <div class="space-y-2">
                                <div class="page-skeleton-pulse h-7 w-56"></div>
                                <div class="page-skeleton-pulse h-4 w-96"></div>
                            </div>
                            <div class="page-skeleton-pulse h-10 w-32 rounded-xl"></div>
                        </div>
                        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="page-skeleton-pulse h-9 w-28 rounded-xl"></div>
                                <div class="page-skeleton-pulse h-9 w-28 rounded-xl"></div>
                                <div class="page-skeleton-pulse h-9 w-28 rounded-xl"></div>
                            </div>
                            <div class="space-y-3 pt-2">
                                <div class="page-skeleton-pulse h-12 w-full rounded-xl"></div>
                                <div class="page-skeleton-pulse h-12 w-full rounded-xl"></div>
                                <div class="page-skeleton-pulse h-12 w-full rounded-xl"></div>
                                <div class="page-skeleton-pulse h-12 w-full rounded-xl"></div>
                                <div class="page-skeleton-pulse h-12 w-full rounded-xl"></div>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }

            // Dashboard overview
            main.innerHTML = `
                <div class="page-loading-skeleton-container space-y-6">
                    <div class="page-skeleton-pulse h-44 w-full rounded-2xl"></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="page-skeleton-pulse h-28 w-full rounded-2xl"></div>
                        <div class="page-skeleton-pulse h-28 w-full rounded-2xl"></div>
                        <div class="page-skeleton-pulse h-28 w-full rounded-2xl"></div>
                        <div class="page-skeleton-pulse h-28 w-full rounded-2xl"></div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                        <div class="lg:col-span-8 page-skeleton-pulse h-72 rounded-2xl"></div>
                        <div class="lg:col-span-4 page-skeleton-pulse h-72 rounded-2xl"></div>
                    </div>
                </div>
            `;
            return;
        }

        // 2. Product Detail Page
        if (path.includes('/product/')) {
            main.innerHTML = `
                <div class="page-loading-skeleton-container max-w-7xl mx-auto px-4 py-6 space-y-6">
                    <div class="page-skeleton-pulse h-5 w-48 rounded-md mb-4"></div>
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-xs grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <div class="lg:col-span-5 space-y-4">
                            <div class="page-skeleton-pulse h-96 w-full rounded-2xl"></div>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="page-skeleton-pulse h-20 rounded-xl"></div>
                                <div class="page-skeleton-pulse h-20 rounded-xl"></div>
                                <div class="page-skeleton-pulse h-20 rounded-xl"></div>
                                <div class="page-skeleton-pulse h-20 rounded-xl"></div>
                            </div>
                        </div>
                        <div class="lg:col-span-7 space-y-5">
                            <div class="page-skeleton-pulse h-8 w-4/5 rounded-xl"></div>
                            <div class="page-skeleton-pulse h-5 w-1/3 rounded-lg"></div>
                            <div class="page-skeleton-pulse h-16 w-full rounded-2xl"></div>
                            <div class="page-skeleton-pulse h-24 w-full rounded-2xl"></div>
                            <div class="flex gap-4 pt-4">
                                <div class="page-skeleton-pulse h-12 flex-1 rounded-xl"></div>
                                <div class="page-skeleton-pulse h-12 flex-1 rounded-xl"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        // 3. Cart Page
        if (path.includes('/cart')) {
            main.innerHTML = `
                <div class="page-loading-skeleton-container max-w-6xl mx-auto px-4 py-6 space-y-5">
                    <div class="page-skeleton-pulse h-7 w-40 rounded-lg"></div>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-8 space-y-4">
                            <div class="page-skeleton-pulse h-32 rounded-2xl"></div>
                            <div class="page-skeleton-pulse h-32 rounded-2xl"></div>
                        </div>
                        <div class="lg:col-span-4 space-y-4">
                            <div class="page-skeleton-pulse h-64 rounded-2xl"></div>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        // 4. Catalog / Search / Category Listing
        if (path.includes('/search') || path.includes('/category') || path.includes('/brand')) {
            main.innerHTML = `
                <div class="page-loading-skeleton-container max-w-7xl mx-auto px-4 py-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="hidden lg:block lg:col-span-3 space-y-4">
                            <div class="page-skeleton-pulse h-48 rounded-2xl"></div>
                            <div class="page-skeleton-pulse h-64 rounded-2xl"></div>
                        </div>
                        <div class="lg:col-span-9 space-y-4">
                            <div class="page-skeleton-pulse h-12 rounded-xl"></div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                ${Array(8).fill(0).map(() => `
                                    <div class="bg-white rounded-2xl p-3 border border-gray-100 shadow-2xs space-y-3">
                                        <div class="page-skeleton-pulse h-44 rounded-xl"></div>
                                        <div class="page-skeleton-pulse h-4 w-3/4 rounded-md"></div>
                                        <div class="page-skeleton-pulse h-5 w-1/2 rounded-md"></div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        // 5. Default General Layout (Home or Other pages)
        main.innerHTML = `
            <div class="page-loading-skeleton-container max-w-7xl mx-auto px-4 py-6 space-y-6">
                <div class="page-skeleton-pulse h-64 w-full rounded-2xl"></div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    ${Array(6).fill(0).map(() => `
                        <div class="bg-white rounded-2xl p-3 border border-gray-100 shadow-2xs space-y-3">
                            <div class="page-skeleton-pulse h-40 rounded-xl"></div>
                            <div class="page-skeleton-pulse h-4 w-3/4 rounded-md"></div>
                            <div class="page-skeleton-pulse h-5 w-1/2 rounded-md"></div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    bindEvents() {
        // Intercept link clicks
        document.addEventListener('click', (e) => {
            // If already navigating or modified click (Ctrl/Cmd/Shift/Alt/Right Click), ignore
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;

            const anchor = e.target.closest('a[href]');
            if (!anchor) return;

            // Ignore target="_blank", download, or javascript/hash links
            if (anchor.target === '_blank' || anchor.hasAttribute('download')) return;
            if (anchor.getAttribute('data-no-loader') !== null) return;

            const rawHref = anchor.getAttribute('href');
            if (!rawHref || rawHref.startsWith('#') || rawHref.startsWith('javascript:') || rawHref.startsWith('tel:') || rawHref.startsWith('mailto:')) {
                return;
            }

            try {
                const targetUrl = new URL(anchor.href, window.location.origin);
                // Same origin only
                if (targetUrl.origin !== window.location.origin) return;

                // Same path and search (in-page anchor jump)
                if (targetUrl.pathname === window.location.pathname && targetUrl.search === window.location.search) {
                    return;
                }

                // Start top progress bar and corner badge
                this.start('Đang tải dữ liệu...');

                // Keep existing layout and content intact, apply subtle transition opacity
                const main = document.querySelector('main');
                if (main) {
                    main.style.transition = 'opacity 0.2s ease';
                    main.style.opacity = '0.7';
                }
            } catch {
                // Ignore URL parse error
            }
        }, { capture: true });

        // Intercept standard form submissions (e.g. filters, search, post data)
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form || form.target === '_blank' || form.getAttribute('data-no-loader') !== null) return;

            // If AJAX form, let component handle it
            if (form.classList.contains('ajax-form') || form.hasAttribute('data-ajax')) return;

            const action = form.getAttribute('action');
            if (action && !action.startsWith('#') && !action.startsWith('javascript:')) {
                this.start('Đang xử lý...');
            }
        }, { capture: true });

        // Handle browser back/forward buttons & page restores
        window.addEventListener('pageshow', (e) => {
            this.stop();
        });

        window.addEventListener('load', () => {
            this.stop();
        });
    }
}

export const pageLoader = new PageLoader();

export function initPageLoader() {
    pageLoader.init();
}
