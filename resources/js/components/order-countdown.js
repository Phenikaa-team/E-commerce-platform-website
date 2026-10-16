/**
 * Order Countdown Timer
 * Handles real-time countdown for orders awaiting online payment (12-hour limit).
 */

export function initOrderCountdowns() {
    const countdownElements = document.querySelectorAll('[data-order-countdown]');
    if (!countdownElements.length) return;

    const pad = (num) => String(num).padStart(2, '0');

    const updateAll = () => {
        const now = Date.now();

        countdownElements.forEach((el) => {
            const deadlineRaw = el.getAttribute('data-order-countdown');
            const endAt = Date.parse(deadlineRaw);
            if (!Number.isFinite(endAt)) return;

            const remainingSeconds = Math.floor((endAt - now) / 1000);
            const orderCode = el.getAttribute('data-order-code');
            const payBtns = orderCode ? document.querySelectorAll(`[data-order-pay-btn="${orderCode}"]`) : [];

            if (remainingSeconds <= 0) {
                // Expired
                const displayEl = el.querySelector('.timer-display') || el;
                displayEl.textContent = 'Đã hết hạn';

                el.classList.remove('border-rose-200', 'bg-rose-50', 'text-rose-700', 'border-amber-200', 'bg-amber-50', 'text-amber-900');
                el.classList.add('border-gray-200', 'bg-gray-100', 'text-gray-500');

                // Hide or disable associated pay button
                payBtns.forEach((btn) => {
                    btn.classList.add('opacity-50', 'pointer-events-none', 'cursor-not-allowed');
                    btn.setAttribute('aria-disabled', 'true');
                    const textSpan = btn.querySelector('span');
                    if (textSpan) textSpan.textContent = 'Hết hạn thanh toán';
                });
            } else {
                const hours = Math.floor(remainingSeconds / 3600);
                const minutes = Math.floor((remainingSeconds % 3600) / 60);
                const seconds = remainingSeconds % 60;

                const displayEl = el.querySelector('.timer-display, .timer-big');
                if (displayEl) {
                    displayEl.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                } else {
                    el.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                }

                // If button also has a timer span
                payBtns.forEach((btn) => {
                    const btnTimer = btn.querySelector('.btn-timer-display');
                    if (btnTimer) {
                        btnTimer.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                    }
                });
            }
        });
    };

    updateAll();
    setInterval(updateAll, 1000);
}
