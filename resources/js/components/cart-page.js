/**
 * Cart Page Interactions & Checkout Stepper
 */
import { showToast, updateAllCartBadges } from './cart.js';

export function initCartPageInteractions() {
    const step1View = document.getElementById('cart-step-1-view');
    const step2View = document.getElementById('cart-step-2-view');
    const step3View = document.getElementById('cart-step-3-view');
    const mobileBottomBar = document.getElementById('mobile-sticky-checkout-bar');
    const mobileAppNav = document.getElementById('mobile-bottom-app-nav');
    const shopeeBottomWrapper = document.getElementById('shopee-bottom-wrapper');

    if (!step1View) return; // Not on cart page

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Switch: Step 1 -> Step 2 (Checkout)
    const proceedButtons = document.querySelectorAll(
        '#btn-proceed-checkout-desktop, #btn-mobile-checkout-submit, [data-btn-proceed-checkout]'
    );

    proceedButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            step1View.classList.add('hidden');
            if (shopeeBottomWrapper) shopeeBottomWrapper.classList.add('hidden');
            if (mobileBottomBar) mobileBottomBar.classList.add('hidden');
            if (mobileAppNav) mobileAppNav.classList.add('hidden');
            if (step2View) step2View.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Switch: Step 2 -> Step 1 (Back to Cart)
    const backButtons = document.querySelectorAll(
        '#btn-back-to-cart, #btn-edit-cart-items, [data-btn-back-to-cart]'
    );

    backButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (step2View) step2View.classList.add('hidden');
            step1View.classList.remove('hidden');
            if (shopeeBottomWrapper) shopeeBottomWrapper.classList.remove('hidden');
            if (mobileBottomBar) mobileBottomBar.classList.remove('hidden');
            if (mobileAppNav) mobileAppNav.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // ShopMart Points Toggle (-500.000đ)
    const pointsToggle = document.getElementById('toggle-reward-points');
    const pointsRow = document.getElementById('points-discount-row');
    const grandTotalEl = document.getElementById('billing-grand-total');
    const totalSavingsEl = document.getElementById('billing-total-savings');

    let baseTotal = 0;
    if (grandTotalEl) {
        baseTotal = parseInt(grandTotalEl.textContent.replace(/[^\d]/g, '')) || 0;
    }

    if (pointsToggle && pointsRow) {
        pointsToggle.addEventListener('change', () => {
            if (pointsToggle.checked) {
                pointsRow.classList.remove('hidden');
                if (grandTotalEl) {
                    const newTotal = Math.max(0, baseTotal - 500000);
                    grandTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(newTotal) + '₫';
                }
                if (totalSavingsEl) {
                    totalSavingsEl.textContent = '1.150.000₫';
                }
                showToast('Đã áp dụng 500.000 điểm ShopMart (-500.000₫)', 'success');
            } else {
                pointsRow.classList.add('hidden');
                if (grandTotalEl) {
                    grandTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(baseTotal) + '₫';
                }
                if (totalSavingsEl) {
                    totalSavingsEl.textContent = '650.000₫';
                }
            }
        });
    }

    // Place Order Button (Step 2 -> Step 3)
    const placeOrderBtn = document.getElementById('btn-place-order');
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            placeOrderBtn.disabled = true;
            placeOrderBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Đang xử lý đặt hàng...</span>
            `;

            const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value || 'wallet';
            const pointsUsed = pointsToggle?.checked ? 500000 : 0;

            try {
                const response = await fetch('/checkout/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        payment_method: selectedPayment,
                        points_used: pointsUsed
                    })
                });

                const result = await response.json();
                if (result && result.success) {
                    if (step2View) step2View.classList.add('hidden');
                    if (step3View) {
                        step3View.classList.remove('hidden');
                        const orderCodeEl = document.getElementById('success-order-code');
                        if (orderCodeEl) orderCodeEl.textContent = result.order_code;
                    }
                    updateAllCartBadges('0');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    showToast('🎉 Đặt hàng thành công!', 'success');
                } else {
                    showToast(result.message || 'Không thể đặt hàng, vui lòng thử lại', 'error');
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.innerHTML = `<span>Đặt hàng</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
                }
            } catch (err) {
                console.error(err);
                showToast('Có lỗi xảy ra khi xử lý đơn hàng', 'error');
                placeOrderBtn.disabled = false;
                placeOrderBtn.innerHTML = `<span>Đặt hàng</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
            }
        });
    }

    // Quantity Stepper +/-
    document.querySelectorAll('[data-qty-btn]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const itemId = btn.getAttribute('data-item-id');
            const action = btn.getAttribute('data-action');
            if (!itemId) return;

            btn.disabled = true;
            try {
                const response = await fetch(`/cart/item/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ action })
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
                btn.disabled = false;
            }
        });
    });

    // Remove single item (Trash icon)
    document.querySelectorAll('[data-remove-item]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const itemId = btn.getAttribute('data-item-id');
            if (!itemId) return;

            if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) return;

            btn.disabled = true;
            try {
                const response = await fetch(`/cart/item/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
                btn.disabled = false;
            }
        });
    });

    // =========================================================================
    // Rich Variant Selection Popup / Modal with Live Color-Image Preview
    // =========================================================================
    const variantModal = document.getElementById('variant-selection-modal');
    const modalBackdrop = document.getElementById('variant-modal-backdrop');
    const modalCard = document.getElementById('variant-modal-card');
    const modalCloseBtn = document.getElementById('close-variant-modal-btn');
    const modalCancelBtn = document.getElementById('btn-cancel-variant-modal');
    const modalConfirmBtn = document.getElementById('btn-confirm-variant-modal');
    const modalImg = document.getElementById('variant-modal-img');
    const modalTitle = document.getElementById('variant-modal-title');
    const modalPrice = document.getElementById('variant-modal-price');
    const modalOriginalPrice = document.getElementById('variant-modal-original-price');
    const modalSelectedText = document.getElementById('variant-modal-selected-text');
    const colorsSection = document.getElementById('variant-modal-colors-section');
    const colorsList = document.getElementById('variant-modal-colors-list');
    const activeColorLabel = document.getElementById('variant-modal-active-color-label');
    const optionsSection = document.getElementById('variant-modal-options-section');
    const optionsList = document.getElementById('variant-modal-options-list');
    const activeOptionLabel = document.getElementById('variant-modal-active-option-label');

    let currentVariantItemId = null;
    let currentTriggerBtn = null;
    let modalColors = [];
    let modalOptions = [];
    let activeSelectedColor = null;
    let activeSelectedColorImg = null;
    let activeSelectedOption = null;

    function updateModalSummaryText() {
        if (!modalSelectedText) return;
        const parts = [];
        if (activeSelectedColor) parts.push(activeSelectedColor);
        if (activeSelectedOption) parts.push(activeSelectedOption);
        modalSelectedText.textContent = parts.length > 0 ? parts.join(' | ') : 'Mặc định';
        if (activeColorLabel) activeColorLabel.textContent = activeSelectedColor || 'Chưa chọn';
        if (activeOptionLabel) activeOptionLabel.textContent = activeSelectedOption || 'Chưa chọn';
    }

    function openVariantModal(btn) {
        if (!variantModal || !modalCard) return;

        currentTriggerBtn = btn;
        currentVariantItemId = btn.getAttribute('data-item-id');
        const productName = btn.getAttribute('data-product-name') || 'Sản phẩm';
        const price = btn.getAttribute('data-product-price') || '';
        const originalPrice = btn.getAttribute('data-product-original-price') || '';
        const defaultImg = btn.getAttribute('data-default-img') || '';
        const currentVariant = btn.getAttribute('data-selected-variant') || '';

        try {
            modalColors = JSON.parse(btn.getAttribute('data-colors') || '[]');
        } catch (e) {
            modalColors = [];
        }

        try {
            modalOptions = JSON.parse(btn.getAttribute('data-options') || '[]');
        } catch (e) {
            modalOptions = [];
        }

        if (modalTitle) modalTitle.textContent = productName;
        if (modalPrice) modalPrice.textContent = price;

        if (modalOriginalPrice) {
            if (originalPrice) {
                modalOriginalPrice.textContent = originalPrice;
                modalOriginalPrice.classList.remove('hidden');
            } else {
                modalOriginalPrice.classList.add('hidden');
            }
        }

        // Identify currently selected color and option from saved variant string
        activeSelectedColor = null;
        activeSelectedColorImg = defaultImg;
        activeSelectedOption = null;

        if (modalColors.length > 0) {
            for (const c of modalColors) {
                if (currentVariant && currentVariant.includes(c.label)) {
                    activeSelectedColor = c.label;
                    activeSelectedColorImg = c.image || defaultImg;
                    break;
                }
            }
            if (!activeSelectedColor) {
                activeSelectedColor = modalColors[0].label;
                activeSelectedColorImg = modalColors[0].image || defaultImg;
            }
        }

        if (modalOptions.length > 0) {
            for (const opt of modalOptions) {
                if (currentVariant && currentVariant.includes(opt)) {
                    activeSelectedOption = opt;
                    break;
                }
            }
            if (!activeSelectedOption) {
                activeSelectedOption = modalOptions[0];
            }
        }

        // Set preview image
        if (modalImg) {
            modalImg.src = activeSelectedColorImg || defaultImg;
            modalImg.alt = productName;
        }

        // Render Colors
        if (colorsSection && colorsList) {
            if (modalColors.length === 0) {
                colorsSection.classList.add('hidden');
                colorsList.innerHTML = '';
            } else {
                colorsSection.classList.remove('hidden');
                colorsList.innerHTML = '';

                modalColors.forEach(colorItem => {
                    const isSelected = (colorItem.label === activeSelectedColor);
                    const colorBtn = document.createElement('button');
                    colorBtn.type = 'button';
                    colorBtn.className = `p-1.5 sm:p-2 rounded-xl border flex items-center gap-2 text-left transition-all cursor-pointer group ${
                        isSelected 
                            ? 'border-2 border-[#ea384c] bg-rose-50/60 shadow-3xs' 
                            : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50'
                    }`;

                    const imgThumb = colorItem.image || defaultImg;
                    colorBtn.innerHTML = `
                        <div class="w-8 h-8 rounded-lg overflow-hidden shrink-0 border border-gray-100 bg-gray-50">
                            <img src="${imgThumb}" alt="${colorItem.label}" class="w-full h-full object-cover">
                        </div>
                        <span class="text-[11px] sm:text-xs font-bold leading-tight truncate flex-1 ${
                            isSelected ? 'text-[#ea384c]' : 'text-gray-800'
                        }">
                            ${colorItem.label}
                        </span>
                        ${isSelected ? '<span class="w-2 h-2 rounded-full bg-[#ea384c] shrink-0 mr-1"></span>' : ''}
                    `;

                    colorBtn.addEventListener('click', () => {
                        activeSelectedColor = colorItem.label;
                        activeSelectedColorImg = colorItem.image || defaultImg;

                        // Visual flash/zoom on modal image to delight the user
                        if (modalImg) {
                            modalImg.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                modalImg.src = activeSelectedColorImg;
                                modalImg.style.transform = 'scale(1)';
                            }, 120);
                        }

                        // Re-render color buttons active state
                        colorsList.querySelectorAll('button').forEach(btnEl => {
                            btnEl.className = 'p-1.5 sm:p-2 rounded-xl border border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50 flex items-center gap-2 text-left transition-all cursor-pointer group';
                            const labelSpan = btnEl.querySelector('span');
                            if (labelSpan) {
                                labelSpan.className = 'text-[11px] sm:text-xs font-bold leading-tight truncate flex-1 text-gray-800';
                            }
                            const dot = btnEl.querySelector('.bg-\\[\\#ea384c\\]');
                            if (dot) dot.remove();
                        });

                        colorBtn.className = 'p-1.5 sm:p-2 rounded-xl border-2 border-[#ea384c] bg-rose-50/60 shadow-3xs flex items-center gap-2 text-left transition-all cursor-pointer group';
                        const labelSpan = colorBtn.querySelector('span');
                        if (labelSpan) {
                            labelSpan.className = 'text-[11px] sm:text-xs font-bold leading-tight truncate flex-1 text-[#ea384c]';
                        }
                        const dot = document.createElement('span');
                        dot.className = 'w-2 h-2 rounded-full bg-[#ea384c] shrink-0 mr-1';
                        colorBtn.appendChild(dot);

                        updateModalSummaryText();
                    });

                    colorsList.appendChild(colorBtn);
                });
            }
        }

        // Render Options / Storage / Sizes
        if (optionsSection && optionsList) {
            if (modalOptions.length === 0) {
                optionsSection.classList.add('hidden');
                optionsList.innerHTML = '';
            } else {
                optionsSection.classList.remove('hidden');
                optionsList.innerHTML = '';

                modalOptions.forEach(opt => {
                    const isSelected = (opt === activeSelectedOption);
                    const optBtn = document.createElement('button');
                    optBtn.type = 'button';
                    optBtn.className = `px-3.5 py-2 rounded-xl text-xs font-bold border transition-all cursor-pointer ${
                        isSelected 
                            ? 'border-2 border-blue-600 bg-blue-50/70 text-blue-600 shadow-3xs' 
                            : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50 text-gray-700'
                    }`;
                    optBtn.textContent = opt;

                    optBtn.addEventListener('click', () => {
                        activeSelectedOption = opt;

                        optionsList.querySelectorAll('button').forEach(b => {
                            b.className = 'px-3.5 py-2 rounded-xl text-xs font-bold border border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50 text-gray-700 transition-all cursor-pointer';
                        });

                        optBtn.className = 'px-3.5 py-2 rounded-xl text-xs font-bold border-2 border-blue-600 bg-blue-50/70 text-blue-600 shadow-3xs transition-all cursor-pointer';
                        updateModalSummaryText();
                    });

                    optionsList.appendChild(optBtn);
                });
            }
        }

        updateModalSummaryText();

        // Show Modal
        variantModal.classList.remove('opacity-0', 'pointer-events-none');
        variantModal.classList.add('opacity-100', 'pointer-events-auto');
        modalCard.classList.remove('translate-y-full', 'sm:scale-95');
        modalCard.classList.add('translate-y-0', 'sm:scale-100');
        document.body.classList.add('overflow-hidden');
    }

    function closeVariantModal() {
        if (!variantModal || !modalCard) return;
        variantModal.classList.remove('opacity-100', 'pointer-events-auto');
        variantModal.classList.add('opacity-0', 'pointer-events-none');
        modalCard.classList.remove('translate-y-0', 'sm:scale-100');
        modalCard.classList.add('translate-y-full', 'sm:scale-95');
        document.body.classList.remove('overflow-hidden');
    }

    // Attach listeners to all open-variant buttons
    document.querySelectorAll('[data-open-variant-popup]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            openVariantModal(btn);
        });
    });

    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeVariantModal);
    if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeVariantModal);
    if (modalBackdrop) modalBackdrop.addEventListener('click', closeVariantModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && variantModal && !variantModal.classList.contains('pointer-events-none')) {
            closeVariantModal();
        }
    });

    // Confirm Variant Selection
    if (modalConfirmBtn) {
        modalConfirmBtn.addEventListener('click', async () => {
            if (!currentVariantItemId) return;

            const parts = [];
            if (activeSelectedColor) parts.push(activeSelectedColor);
            if (activeSelectedOption) parts.push(activeSelectedOption);
            const combinedVariant = parts.length > 0 ? parts.join(' | ') : 'Mặc định';

            modalConfirmBtn.disabled = true;
            modalConfirmBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Đang cập nhật...</span>
            `;

            try {
                const response = await fetch(`/cart/item/${currentVariantItemId}/variant`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ variant: combinedVariant })
                });

                const data = await response.json();
                if (data && data.success) {
                    // Update trigger button display label
                    const displayEl = document.querySelector(`[data-variant-display="${currentVariantItemId}"]`);
                    if (displayEl) displayEl.textContent = data.variant;

                    if (currentTriggerBtn) {
                        currentTriggerBtn.setAttribute('data-selected-variant', data.variant);
                    }

                    // Update row thumbnail image if a color image is returned or picked
                    const rowImg = document.querySelector(`[data-cart-item-img="${currentVariantItemId}"]`);
                    const newImgUrl = data.image_url || activeSelectedColorImg;
                    if (rowImg && newImgUrl) {
                        rowImg.style.opacity = '0.5';
                        setTimeout(() => {
                            rowImg.src = newImgUrl;
                            rowImg.style.opacity = '1';
                        }, 150);
                    }

                    closeVariantModal();
                    showToast(`Đã đổi phân loại thành: <b>${data.variant}</b>`, 'success');
                } else {
                    showToast(data.message || 'Không thể đổi phân loại', 'error');
                }
            } catch (err) {
                console.error('Error updating variant:', err);
                showToast('Lỗi kết nối khi cập nhật phân loại', 'error');
            } finally {
                modalConfirmBtn.disabled = false;
                modalConfirmBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Xác nhận thay đổi</span>
                `;
            }
        });
    }

    // Remove selected items (desktop, mobile top, and sticky bottom)
    const removeSelectedButtons = document.querySelectorAll(
        '#btn-remove-selected, #btn-remove-selected-desktop, #btn-remove-selected-top, #btn-remove-selected-mobile, #btn-remove-selected-sticky'
    );
    removeSelectedButtons.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm đã chọn?')) return;

            try {
                const response = await fetch('/cart/remove-selected', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    // Select All Checkboxes (Desktop header, Mobile header, and Sticky Bottom bar)
    const selectAllCheckboxes = document.querySelectorAll(
        '[data-select-all-checkbox], #select-all-desktop-top, #select-all-mobile-top, #mobile-select-all-bottom, #select-all-top-checkbox, #select-all-mobile-checkbox'
    );

    selectAllCheckboxes.forEach(cb => {
        cb.addEventListener('change', async () => {
            const isSelected = cb.checked;
            try {
                const response = await fetch('/cart/toggle-select', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'all',
                        is_selected: isSelected
                    })
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    // Store Group Checkbox
    document.querySelectorAll('[data-store-checkbox]').forEach(cb => {
        cb.addEventListener('change', async () => {
            const storeId = cb.getAttribute('data-store-id');
            const isSelected = cb.checked;
            try {
                const response = await fetch('/cart/toggle-select', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'store',
                        store_id: storeId,
                        is_selected: isSelected
                    })
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    // Single Item Checkbox
    document.querySelectorAll('[data-item-checkbox]').forEach(cb => {
        cb.addEventListener('change', async () => {
            const itemId = cb.getAttribute('data-item-id');
            const isSelected = cb.checked;
            try {
                const response = await fetch('/cart/toggle-select', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'item',
                        item_id: itemId,
                        is_selected: isSelected
                    })
                });
                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });
}
