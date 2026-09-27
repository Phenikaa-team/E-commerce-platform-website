/**
 * Number & Currency Formatting Utilities
 */

/**
 * Format a numeric amount to Vietnamese Dong (₫) currency string
 * @param {number|string} amount 
 * @returns {string} e.g. "150.000₫"
 */
export function formatCurrencyVND(amount) {
    const num = Number(amount) || 0;
    return new Intl.NumberFormat('vi-VN').format(num) + '₫';
}

/**
 * Format a number with thousand separators
 * @param {number|string} number 
 * @returns {string} e.g. "1.250.000"
 */
export function formatNumber(number) {
    const num = Number(number) || 0;
    return new Intl.NumberFormat('vi-VN').format(num);
}

export const formatVnd = formatCurrencyVND;

