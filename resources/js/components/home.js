/**
 * Homepage Interactive Components:
 * - Flash Sale Live Countdown Timer
 * - Hero Banner Carousel
 * - Recommended Products Category Tabs (Sliding Pill Indicator & Staggered Cascade)
 */

/**
 * 1. Flash Sale Live Countdown Timer
 */
export function initCountdown() {
    let totalSeconds = 4 * 3600 + 18 * 60 + 27; // 04:18:27

    const updateDisplay = () => {
        if (totalSeconds <= 0) {
            totalSeconds = 24 * 3600; // Reset to 24h
        }

        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        const pad = (num) => String(num).padStart(2, '0');

        document.querySelectorAll('.timer-hours').forEach(el => el.textContent = pad(hours));
        document.querySelectorAll('.timer-minutes').forEach(el => el.textContent = pad(minutes));
        document.querySelectorAll('.timer-seconds').forEach(el => el.textContent = pad(seconds));

        totalSeconds--;
    };

    updateDisplay();
    setInterval(updateDisplay, 1000);
}

/**
 * 2. Hero Banner Carousel
 */
export function initHeroCarousel() {
    const carousels = document.querySelectorAll('[data-carousel]');
    
    carousels.forEach(carousel => {
        const slides = carousel.querySelectorAll('[data-carousel-slide]');
        const dots = carousel.querySelectorAll('[data-carousel-dot]');
        const prevBtn = carousel.querySelector('[data-carousel-prev]');
        const nextBtn = carousel.querySelector('[data-carousel-next]');
        let currentIndex = 0;
        let timer = null;

        const showSlide = (index) => {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;
            currentIndex = index;

            slides.forEach((slide, i) => {
                if (i === currentIndex) {
                    slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                    slide.classList.add('opacity-100', 'z-10');
                } else {
                    slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                    slide.classList.remove('opacity-100', 'z-10');
                }
            });

            dots.forEach((dot, i) => {
                if (i === currentIndex) {
                    dot.classList.add('bg-white', 'w-6');
                    dot.classList.remove('bg-white/40', 'w-2');
                } else {
                    dot.classList.remove('bg-white', 'w-6');
                    dot.classList.add('bg-white/40', 'w-2');
                }
            });
        };

        const nextSlide = () => showSlide(currentIndex + 1);
        const prevSlide = () => showSlide(currentIndex - 1);

        if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); resetTimer(); });
        if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); resetTimer(); });

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                showSlide(idx);
                resetTimer();
            });
        });

        const startTimer = () => {
            timer = setInterval(nextSlide, 5000);
        };

        const resetTimer = () => {
            clearInterval(timer);
            startTimer();
        };

        showSlide(0);
        startTimer();
    });
}

/**
 * 3. Recommended Products Category Tabs WITH SLIDING PILL INDICATOR & SMOOTH HEIGHT
 */
export function initRecommendedTabs() {
    const container = document.getElementById('tab-nav-container');
    const indicator = document.getElementById('tab-indicator');
    const tabButtons = document.querySelectorAll('[data-filter-tab]');
    const wrapper = document.getElementById('recommended-products-wrapper');
    const grid = document.getElementById('recommended-products-grid');
    const productCards = document.querySelectorAll('[data-product-category]');

    let isAnimating = false;

    // Function to calculate and slide indicator pill
    const moveIndicator = (activeBtn) => {
        if (!indicator || !activeBtn || !container) return;
        indicator.style.left = `${activeBtn.offsetLeft}px`;
        indicator.style.width = `${activeBtn.offsetWidth}px`;
    };

    // Position indicator on load
    const initialActive = document.querySelector('[data-filter-tab].active-tab') || tabButtons[0];
    if (initialActive) {
        setTimeout(() => moveIndicator(initialActive), 60);
    }

    // Keep indicator position accurate on window resize
    window.addEventListener('resize', () => {
        const active = document.querySelector('[data-filter-tab].active-tab');
        if (active) moveIndicator(active);
    });

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.classList.contains('active-tab') || isAnimating) return;

            // Highlight Tab Buttons
            tabButtons.forEach(b => {
                b.classList.remove('active-tab', 'text-[#ea384c]', 'font-bold');
                b.classList.add('text-gray-600', 'font-medium');
            });
            btn.classList.add('active-tab', 'text-[#ea384c]', 'font-bold');
            btn.classList.remove('text-gray-600', 'font-medium');

            // Slide indicator smoothly
            moveIndicator(btn);

            const category = btn.getAttribute('data-filter-tab');

            if (!wrapper || !grid) return;

            isAnimating = true;

            // Step 1: Capture current height
            const currentHeight = wrapper.offsetHeight;
            wrapper.style.height = `${currentHeight}px`;

            // Step 2: Smoothly fade out existing cards
            grid.style.transition = 'opacity 0.15s ease';
            grid.style.opacity = '0.2';

            setTimeout(() => {
                // Step 3: Toggle cards visibility with staggered cascade animation
                let visibleCount = 0;
                productCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-product-category');
                    if (category === 'all' || cardCategory === category) {
                        card.style.display = '';
                        card.classList.remove('animate-card-appear');
                        void card.offsetWidth; // force reflow for animation restart
                        card.style.animationDelay = `${visibleCount * 35}ms`;
                        card.classList.add('animate-card-appear');
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                        card.classList.remove('animate-card-appear');
                        card.style.animationDelay = '0ms';
                    }
                });

                // Step 4: Measure target natural height
                wrapper.style.transition = 'none';
                wrapper.style.height = 'auto';
                const targetHeight = wrapper.offsetHeight;

                // Step 5: Restore current height and animate to target height smoothly
                wrapper.style.height = `${currentHeight}px`;
                void wrapper.offsetHeight; // force reflow

                wrapper.style.transition = 'height 0.38s cubic-bezier(0.25, 1, 0.5, 1)';
                wrapper.style.height = `${targetHeight}px`;

                // Fade grid back in
                grid.style.opacity = '1';

                // Step 6: Cleanup after animation completes
                setTimeout(() => {
                    wrapper.style.height = 'auto';
                    wrapper.style.transition = '';
                    isAnimating = false;
                }, 400);
            }, 150);
        });
    });
}
