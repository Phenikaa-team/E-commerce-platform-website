/**
 * Seller Coupons Creation & Editing Script
 * - Realtime Voucher Card Preview (Code, Discount value, Min Order, Dates, Scope)
 * - Custom Dropdowns
 * - Random Code Generator
 * - Markdown / Format Toolbar for Description
 * - Character count
 */

export function initSellerCoupons() {
    const codeInput = document.getElementById('coupon_code');
    const nameInput = document.getElementById('coupon_name');
    const typeSelect = document.getElementById('coupon_type');
    const valueInput = document.getElementById('coupon_value');
    const valueSuffix = document.getElementById('coupon_value_suffix');
    const minOrderInput = document.getElementById('min_order_value');
    const maxDiscountWrapper = document.getElementById('max_discount_wrapper');
    const expiresAtInput = document.getElementById('expires_at');
    const descTextarea = document.getElementById('description');
    const charCountEl = document.getElementById('description-char-count');
    const btnGenerateCode = document.getElementById('btn-generate-code');

    const maxDiscountInput = document.getElementById('max_discount_amount');

    // Preview Elements
    const previewAmount = document.getElementById('preview-discount-amount');
    const previewCondition = document.getElementById('preview-condition');
    const previewCodeTag = document.getElementById('preview-code-tag');
    const previewDateRange = document.getElementById('preview-date-range');
    const previewScopeText = document.getElementById('preview-scope-text');

    function formatNumberWithDots(val) {
        if (!val && val !== 0) return '';
        const clean = String(val).replace(/\D/g, '');
        if (!clean) return '';
        return new Intl.NumberFormat('vi-VN').format(Number(clean));
    }

    function parseRawNumber(val) {
        if (!val) return 0;
        const clean = String(val).replace(/\./g, '').replace(/,/g, '').trim();
        return parseFloat(clean) || 0;
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }

    // Attach thousand separator formatter to number inputs
    function setupCurrencyInput(inputEl, isPercentAllowed = false) {
        if (!inputEl) return;

        function applyFormat() {
            if (isPercentAllowed && typeSelect?.value === 'percent') {
                const val = parseFloat(inputEl.value) || 0;
                if (val > 100) inputEl.value = '100';
                return;
            }
            const raw = parseRawNumber(inputEl.value);
            if (raw > 0) {
                inputEl.value = formatNumberWithDots(raw);
            }
        }

        inputEl.addEventListener('input', () => {
            if (isPercentAllowed && typeSelect?.value === 'percent') {
                let num = parseFloat(inputEl.value);
                if (!isNaN(num) && num > 100) {
                    inputEl.value = '100';
                }
                updatePreview();
                return;
            }
            const selectionStart = inputEl.selectionStart;
            const originalLength = inputEl.value.length;
            const raw = inputEl.value.replace(/\D/g, '');
            if (raw) {
                inputEl.value = formatNumberWithDots(raw);
                const newLength = inputEl.value.length;
                const newPos = Math.max(0, selectionStart + (newLength - originalLength));
                inputEl.setSelectionRange(newPos, newPos);
            }
            updatePreview();
        });

        // Initialize formatted value on page load
        applyFormat();
    }

    function updatePreview() {
        // 1. Code Tag
        const codeVal = (codeInput?.value || 'SHOP20K').trim().toUpperCase();
        if (previewCodeTag) {
            previewCodeTag.textContent = `Mã: ${codeVal}`;
        }

        // 2. Discount Type & Value
        const isPercent = typeSelect?.value === 'percent';
        const numVal = parseRawNumber(valueInput?.value);

        if (valueSuffix) {
            valueSuffix.textContent = isPercent ? '%' : '₫';
        }

        if (previewAmount) {
            if (isPercent) {
                previewAmount.textContent = `${numVal}%`;
            } else {
                previewAmount.textContent = `${formatCurrency(numVal)}đ`;
            }
        }

        // 3. Condition (Min order) & Max discount preview
        const minOrder = parseRawNumber(minOrderInput?.value);
        const maxDiscount = parseRawNumber(maxDiscountInput?.value);

        if (previewCondition) {
            let condText = `Cho đơn từ ${formatCurrency(minOrder)}đ`;
            if (isPercent && maxDiscount > 0) {
                condText += ` • Tối đa ${formatCurrency(maxDiscount)}đ`;
            }
            previewCondition.textContent = condText;
        }

        // 4. Date Range
        if (expiresAtInput && previewDateRange) {
            const today = new Date();
            const todayFormatted = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;
            if (expiresAtInput.value) {
                const expDate = new Date(expiresAtInput.value);
                const expFormatted = `${String(expDate.getDate()).padStart(2, '0')}/${String(expDate.getMonth() + 1).padStart(2, '0')}/${expDate.getFullYear()}`;
                previewDateRange.textContent = `${todayFormatted} - ${expFormatted}`;
            } else {
                previewDateRange.textContent = `${todayFormatted} - Vô thời hạn`;
            }
        }

        // 5. Max discount field visibility
        if (maxDiscountWrapper) {
            if (isPercent) {
                maxDiscountWrapper.classList.remove('hidden');
            } else {
                maxDiscountWrapper.classList.add('hidden');
            }
        }
    }

    // Bind currency formatting to inputs
    setupCurrencyInput(valueInput, true);
    setupCurrencyInput(minOrderInput, false);
    setupCurrencyInput(maxDiscountInput, false);

    // Unformat numbers before form submission so backend receives clean numeric values
    const form = codeInput?.closest('form');
    form?.addEventListener('submit', () => {
        if (valueInput && (typeSelect?.value !== 'percent')) {
            valueInput.value = parseRawNumber(valueInput.value) || '';
        }
        if (minOrderInput) {
            minOrderInput.value = parseRawNumber(minOrderInput.value) || 0;
        }
        if (maxDiscountInput && maxDiscountInput.value) {
            maxDiscountInput.value = parseRawNumber(maxDiscountInput.value) || '';
        }
    });

    // Bind event listeners for realtime preview
    codeInput?.addEventListener('input', () => {
        codeInput.value = codeInput.value.toUpperCase();
        updatePreview();
    });

    expiresAtInput?.addEventListener('change', updatePreview);

    // Random Code Generator
    btnGenerateCode?.addEventListener('click', () => {
        const prefixes = ['SHOP', 'SALE', 'DEAL', 'VIP', 'MUAHE', 'GIAM'];
        const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
        const randNum = Math.floor(1000 + Math.random() * 9000);
        if (codeInput) {
            codeInput.value = `${prefix}${randNum}`;
            updatePreview();
            codeInput.focus();
        }
    });

    // Character counter for description
    descTextarea?.addEventListener('input', () => {
        const len = descTextarea.value.length;
        if (charCountEl) {
            charCountEl.textContent = `${len}/1000`;
        }
    });

    // Scope Radio Buttons Handler
    const scopeRadios = document.querySelectorAll('input[name="apply_scope"]');
    scopeRadios.forEach((radio) => {
        radio.addEventListener('change', () => {
            if (!previewScopeText) return;
            const span = previewScopeText.querySelector('span');
            if (!span) return;
            if (radio.value === 'all') {
                span.textContent = 'Áp dụng toàn bộ sản phẩm';
            } else if (radio.value === 'specific') {
                span.textContent = 'Áp dụng cho sản phẩm được chọn';
            } else if (radio.value === 'categories') {
                span.textContent = 'Áp dụng theo danh mục sản phẩm';
            }
        });
    });

    // Description Editor Toolbar
    initDescriptionToolbar(descTextarea);

    function handleTypeChange(newType) {
        const isPercent = newType === 'percent';

        // Update placeholder dynamically based on selected type
        if (valueInput) {
            valueInput.placeholder = isPercent 
                ? 'Nhập phần trăm giảm (ví dụ: 10)' 
                : 'Nhập số tiền giảm (ví dụ: 20.000)';

            const currentRaw = parseRawNumber(valueInput.value);
            if (isPercent) {
                // If switching to percent, cap value at 100% or default to empty/reasonable value
                if (currentRaw > 100) {
                    valueInput.value = '10'; // Reset to reasonable 10% instead of overflowing with currency amount
                }
            } else {
                // If switching to fixed currency, format properly with thousand separators
                if (currentRaw > 0 && currentRaw <= 100) {
                    valueInput.value = formatNumberWithDots(currentRaw * 1000); // e.g. 20 -> 20.000
                } else if (currentRaw > 0) {
                    valueInput.value = formatNumberWithDots(currentRaw);
                }
            }
        }

        updatePreview();
    }

    typeSelect?.addEventListener('change', () => {
        handleTypeChange(typeSelect.value);
    });

    // Custom Dropdown Integration
    initCustomDropdowns((name, val) => {
        if (name === 'coupon_type' || name === 'discount_type') {
            handleTypeChange(val);
        }
    });

    // Initial update
    updatePreview();
}

/**
 * Initialize Markdown / Text Formatting Toolbar
 */
function initDescriptionToolbar(textarea) {
    if (!textarea) return;

    const toolbarBtns = document.querySelectorAll('[data-editor-action]');
    toolbarBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const action = btn.getAttribute('data-editor-action');
            insertFormatting(textarea, action);
        });
    });
}

function insertFormatting(textarea, action) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const selected = text.substring(start, end);

    let replacement = '';
    let newCursorPos = start;

    switch (action) {
        case 'bold':
            replacement = `**${selected || 'in đậm'}**`;
            newCursorPos = selected ? end + 4 : start + 2;
            break;
        case 'italic':
            replacement = `*${selected || 'in nghiêng'}*`;
            newCursorPos = selected ? end + 2 : start + 1;
            break;
        case 'underline':
            replacement = `<u>${selected || 'gạch chân'}</u>`;
            newCursorPos = selected ? end + 7 : start + 3;
            break;
        case 'bullet':
            replacement = selected
                ? selected.split('\n').map((l) => `- ${l}`).join('\n')
                : '- Mục mới';
            newCursorPos = start + replacement.length;
            break;
        case 'number':
            replacement = selected
                ? selected.split('\n').map((l, i) => `${i + 1}. ${l}`).join('\n')
                : '1. Mục mới';
            newCursorPos = start + replacement.length;
            break;
        case 'link': {
            const url = prompt('Nhập địa chỉ URL (ví dụ: https://example.com):', 'https://');
            if (url) {
                replacement = `[${selected || 'Văn bản liên kết'}](${url})`;
                newCursorPos = start + replacement.length;
            } else {
                return;
            }
            break;
        }
        default:
            return;
    }

    textarea.setRangeText(replacement, start, end, 'end');
    textarea.focus();
    textarea.dispatchEvent(new Event('input'));
}

/**
 * Modern Custom Dropdowns Driver
 */
function initCustomDropdowns(onChangeCallback) {
    const wrappers = document.querySelectorAll('.custom-select-wrapper');

    wrappers.forEach((wrapper) => {
        const trigger = wrapper.querySelector('.custom-select-trigger');
        const optionsList = wrapper.querySelector('.custom-select-options');
        const options = wrapper.querySelectorAll('.custom-select-option');
        const selectName = wrapper.getAttribute('data-select-for');
        const hiddenSelect = wrapper.querySelector('select') || document.querySelector(`select[name="${selectName}"]`) || document.getElementById(selectName);
        const label = wrapper.querySelector('.custom-select-label');

        if (!trigger || !optionsList) return;

        function updateTriggerLabel() {
            if (!hiddenSelect) return;
            const selectedOpt = hiddenSelect.options[hiddenSelect.selectedIndex];
            if (selectedOpt && label) {
                label.textContent = selectedOpt.text;
            }
        }

        updateTriggerLabel();

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = wrapper.classList.contains('is-open');

            document.querySelectorAll('.custom-select-wrapper.is-open').forEach((w) => {
                if (w !== wrapper) w.classList.remove('is-open');
            });

            wrapper.classList.toggle('is-open', !isOpen);
        });

        options.forEach((opt) => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = opt.getAttribute('data-value');
                if (hiddenSelect) {
                    hiddenSelect.value = val;
                    hiddenSelect.dispatchEvent(new Event('change'));
                }
                updateTriggerLabel();
                wrapper.classList.remove('is-open');

                if (onChangeCallback && selectName) {
                    onChangeCallback(selectName, val);
                }
            });
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-select-wrapper.is-open').forEach((w) => {
            w.classList.remove('is-open');
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSellerCoupons();
});
