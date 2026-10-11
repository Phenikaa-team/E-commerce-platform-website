/**
 * Seller Product Form (Create / Edit) Module
 * Handles Gallery Preview Grid, Gallery Image Deletion,
 * and Dynamic Variant Pricing & Stock Matrix.
 */

export function deleteGalleryImage(url) {
    if (confirm('Bạn có chắc muốn xóa ảnh này khỏi bộ sưu tập?')) {
        const form = document.getElementById('delete-gallery-img-form');
        if (form) {
            form.action = url;
            form.submit();
        }
    }
}

export function initGalleryPreview() {
    const imagesInput = document.getElementById('images');
    const previewGrid = document.getElementById('gallery-preview-grid');
    const errorMsg = document.getElementById('gallery-error-msg');

    if (imagesInput && previewGrid) {
        imagesInput.addEventListener('change', (e) => {
            previewGrid.innerHTML = '';
            if (errorMsg) errorMsg.classList.add('hidden');

            const files = Array.from(e.target.files || []);

            if (files.length > 8) {
                if (errorMsg) {
                    errorMsg.textContent = 'Bạn chỉ được chọn tối đa 8 ảnh trong một lần tải lên.';
                    errorMsg.classList.remove('hidden');
                }
                imagesInput.value = '';
                return;
            }

            for (const file of files) {
                if (file.size > 3 * 1024 * 1024) {
                    if (errorMsg) {
                        errorMsg.textContent = `Ảnh "${file.name}" vượt quá dung lượng 3MB. Vui lòng nén hoặc chọn ảnh nhỏ hơn.`;
                        errorMsg.classList.remove('hidden');
                    }
                    imagesInput.value = '';
                    previewGrid.innerHTML = '';
                    return;
                }
            }

            files.forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const card = document.createElement('div');
                card.className = 'seller-gallery-slot group';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'Ảnh ' + (index + 1);

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'seller-gallery-slot-remove';
                removeBtn.textContent = '✕';
                removeBtn.title = 'Bỏ ảnh này';
                removeBtn.addEventListener('click', (ev) => {
                    ev.stopPropagation();
                    card.remove();
                });

                card.appendChild(img);
                card.appendChild(removeBtn);
                previewGrid.appendChild(card);
            });
        });
    }
}

export function initVariantMatrix() {
    const colorInput = document.getElementById('color_variants');
    const sizeInput = document.getElementById('size_variants');
    const matrixContainer = document.getElementById('variant-matrix-container');
    const matrixTbody = document.getElementById('variant-matrix-tbody');
    const hiddenDataInput = document.getElementById('variants_data');
    const btnSyncBase = document.getElementById('btn-sync-base-prices');
    const basePriceInput = document.getElementById('price');
    const baseOriginalPriceInput = document.getElementById('original_price');
    const baseStockInput = document.getElementById('stock');

    if (!colorInput || !sizeInput || !matrixContainer || !matrixTbody || !hiddenDataInput) return;

    // Load pre-existing variants from JSON bridge if available
    const existingDataBridge = document.getElementById('existing-variants-data');
    let stateVariants = {};
    if (existingDataBridge) {
        try {
            const raw = JSON.parse(existingDataBridge.textContent || '[]');
            raw.forEach(v => {
                if (v && v.name) {
                    stateVariants[v.name] = {
                        name: v.name,
                        color: v.color || '',
                        option: v.option || '',
                        price: v.price || '',
                        original_price: v.original_price || '',
                        stock: v.stock !== undefined ? v.stock : 10,
                        sku: v.sku || ''
                    };
                }
            });
        } catch (e) {
            console.error('Failed to parse existing-variants-data:', e);
        }
    }

    const serializeMatrix = () => {
        const rows = matrixTbody.querySelectorAll('tr[data-combo-name]');
        const result = [];
        rows.forEach(row => {
            const name = row.getAttribute('data-combo-name');
            const color = row.getAttribute('data-combo-color') || null;
            const option = row.getAttribute('data-combo-option') || null;
            const priceVal = row.querySelector('.matrix-price-input')?.value;
            const origPriceVal = row.querySelector('.matrix-orig-price-input')?.value;
            const stockVal = row.querySelector('.matrix-stock-input')?.value;
            const skuVal = row.querySelector('.matrix-sku-input')?.value;

            const item = {
                name: name,
                color: color,
                option: option,
                price: priceVal !== '' ? parseFloat(priceVal) : null,
                original_price: origPriceVal !== '' ? parseFloat(origPriceVal) : null,
                stock: stockVal !== '' ? parseInt(stockVal) : 10,
                sku: skuVal || null,
            };

            stateVariants[name] = item;
            result.push(item);
        });

        hiddenDataInput.value = JSON.stringify(result);
    };

    const renderMatrix = () => {
        const colors = colorInput.value.split(',').map(s => s.trim()).filter(Boolean);
        const options = sizeInput.value.split(',').map(s => s.trim()).filter(Boolean);

        const combos = [];
        if (colors.length > 0 && options.length > 0) {
            colors.forEach(c => {
                options.forEach(o => {
                    combos.push({
                        name: `${c} - ${o}`,
                        color: c,
                        option: o,
                    });
                });
            });
        } else if (colors.length > 0) {
            colors.forEach(c => {
                combos.push({
                    name: c,
                    color: c,
                    option: null,
                });
            });
        } else if (options.length > 0) {
            options.forEach(o => {
                combos.push({
                    name: o,
                    color: null,
                    option: o,
                });
            });
        }

        if (combos.length === 0) {
            matrixContainer.classList.add('hidden');
            matrixTbody.innerHTML = '';
            hiddenDataInput.value = '';
            return;
        }

        matrixContainer.classList.remove('hidden');
        matrixTbody.innerHTML = '';

        const basePrice = basePriceInput?.value || '';
        const baseOriginalPrice = baseOriginalPriceInput?.value || '';
        const baseStock = baseStockInput?.value || '10';

        combos.forEach((combo, idx) => {
            const saved = stateVariants[combo.name] || {};
            const price = saved.price !== undefined && saved.price !== null ? saved.price : basePrice;
            const originalPrice = saved.original_price !== undefined && saved.original_price !== null ? saved.original_price : baseOriginalPrice;
            const stock = saved.stock !== undefined && saved.stock !== null ? saved.stock : baseStock;
            const sku = saved.sku || `SKU-${idx + 1}`;

            const tr = document.createElement('tr');
            tr.setAttribute('data-combo-name', combo.name);
            if (combo.color) tr.setAttribute('data-combo-color', combo.color);
            if (combo.option) tr.setAttribute('data-combo-option', combo.option);
            tr.className = 'hover:bg-amber-50/30 transition-colors';

            tr.innerHTML = `
                <td class="py-2.5 px-3 font-semibold text-gray-900">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span>${combo.name}</span>
                    </div>
                </td>
                <td class="py-2.5 px-3">
                    <input 
                        type="number" 
                        min="0" 
                        step="1000" 
                        placeholder="Giá bán" 
                        value="${price}" 
                        class="matrix-price-input w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs font-bold text-primary focus:bg-white focus:border-amber-500 focus:outline-hidden"
                    >
                </td>
                <td class="py-2.5 px-3">
                    <input 
                        type="number" 
                        min="0" 
                        step="1000" 
                        placeholder="Giá niêm yết" 
                        value="${originalPrice}" 
                        class="matrix-orig-price-input w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500 focus:bg-white focus:border-amber-500 focus:outline-hidden"
                    >
                </td>
                <td class="py-2.5 px-3">
                    <input 
                        type="number" 
                        min="0" 
                        placeholder="Kho" 
                        value="${stock}" 
                        class="matrix-stock-input w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium text-gray-800 focus:bg-white focus:border-amber-500 focus:outline-hidden"
                    >
                </td>
                <td class="py-2.5 px-3">
                    <input 
                        type="text" 
                        placeholder="SKU" 
                        value="${sku}" 
                        class="matrix-sku-input w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500 focus:bg-white focus:border-amber-500 focus:outline-hidden"
                    >
                </td>
            `;

            matrixTbody.appendChild(tr);
        });

        serializeMatrix();
    };

    // Event listeners
    colorInput.addEventListener('input', renderMatrix);
    sizeInput.addEventListener('input', renderMatrix);
    matrixTbody.addEventListener('input', serializeMatrix);

    if (btnSyncBase) {
        btnSyncBase.addEventListener('click', () => {
            const basePrice = basePriceInput?.value || '';
            const baseOriginalPrice = baseOriginalPriceInput?.value || '';
            const baseStock = baseStockInput?.value || '10';

            matrixTbody.querySelectorAll('tr').forEach(tr => {
                const pInp = tr.querySelector('.matrix-price-input');
                const origInp = tr.querySelector('.matrix-orig-price-input');
                const sInp = tr.querySelector('.matrix-stock-input');

                if (pInp && basePrice) pInp.value = basePrice;
                if (origInp && baseOriginalPrice) origInp.value = baseOriginalPrice;
                if (sInp && baseStock) sInp.value = baseStock;
            });

            serializeMatrix();
        });
    }

    // Initial render
    renderMatrix();
}

export function initCustomDropdowns() {
    const wrappers = document.querySelectorAll('.custom-select-wrapper');

    wrappers.forEach(wrapper => {
        const trigger = wrapper.querySelector('.custom-select-trigger');
        const triggerText = wrapper.querySelector('.custom-select-text');
        const hiddenSelect = wrapper.querySelector('select');
        const optionsList = wrapper.querySelector('.custom-select-options');

        if (!trigger || !hiddenSelect || !optionsList) return;

        // Toggle options
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            // Close other open selects
            wrappers.forEach(w => {
                if (w !== wrapper) w.classList.remove('is-open');
            });
            wrapper.classList.toggle('is-open');
        });

        // Option click
        optionsList.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = opt.getAttribute('data-value');
                const label = opt.getAttribute('data-label') || opt.textContent.trim();

                hiddenSelect.value = val;
                hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));

                triggerText.textContent = label;
                optionsList.querySelectorAll('.custom-select-option').forEach(o => o.classList.remove('is-selected'));
                opt.classList.add('is-selected');

                wrapper.classList.remove('is-open');
            });
        });
    });

    // Close on click outside
    document.addEventListener('click', () => {
        wrappers.forEach(w => w.classList.remove('is-open'));
    });
}

export function initYoutubeTags() {
    const container = document.getElementById('youtube-tag-container');
    const input = document.getElementById('youtube-tag-input');
    const hiddenTagsInput = document.getElementById('tags');

    if (!container || !input || !hiddenTagsInput) return;

    let tags = [];
    if (hiddenTagsInput.value) {
        tags = hiddenTagsInput.value.split(',').map(t => t.trim()).filter(Boolean);
    }

    const renderTags = () => {
        // Remove existing pills
        container.querySelectorAll('.youtube-tag-pill').forEach(el => el.remove());

        // Insert new pills before input
        tags.forEach((tagText, idx) => {
            const pill = document.createElement('span');
            pill.className = 'youtube-tag-pill';
            pill.innerHTML = `
                <span>${tagText}</span>
                <button type="button" class="youtube-tag-remove" data-index="${idx}" title="Xóa">✕</button>
            `;
            container.insertBefore(pill, input);
        });

        hiddenTagsInput.value = tags.join(', ');
    };

    container.addEventListener('click', () => {
        input.focus();
    });

    container.addEventListener('click', (e) => {
        if (e.target.classList.contains('youtube-tag-remove')) {
            const idx = parseInt(e.target.getAttribute('data-index'));
            if (!isNaN(idx)) {
                tags.splice(idx, 1);
                renderTags();
            }
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const val = input.value.trim().replace(/^,+|,+$/g, '');
            if (val && !tags.includes(val)) {
                tags.push(val);
                renderTags();
            }
            input.value = '';
        } else if (e.key === 'Backspace' && input.value === '' && tags.length > 0) {
            tags.pop();
            renderTags();
        }
    });

    // Initial render
    renderTags();
}

export function initEditorToolbar() {
    const textarea = document.getElementById('description');
    const toolbar = document.querySelector('.seller-editor-toolbar');
    const togglePreview = document.getElementById('toggle-preview');
    const previewArea = document.getElementById('description-preview-area');

    if (!textarea) return;

    if (toolbar) {
        toolbar.querySelectorAll('[data-editor-action]').forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.getAttribute('data-editor-action');
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const text = textarea.value;
                const selected = text.substring(start, end);

                let replacement = '';
                switch (action) {
                    case 'bold':
                        replacement = `**${selected || 'Văn bản in đậm'}**`;
                        break;
                    case 'italic':
                        replacement = `*${selected || 'Văn bản in nghiêng'}*`;
                        break;
                    case 'underline':
                        replacement = `<u>${selected || 'Văn bản gạch chân'}</u>`;
                        break;
                    case 'strike':
                        replacement = `~~${selected || 'Văn bản gạch ngang'}~~`;
                        break;
                    case 'bullet':
                        replacement = `\n- ${selected || 'Mục danh sách'}`;
                        break;
                    case 'number':
                        replacement = `\n1. ${selected || 'Mục danh sách số'}`;
                        break;
                    case 'link': {
                        const url = prompt('Nhập đường dẫn URL:', 'https://');
                        if (url) replacement = `[${selected || 'Liên kết'}](${url})`;
                        else return;
                        break;
                    }
                    case 'image': {
                        const imgUrl = prompt('Nhập đường dẫn ảnh:', 'https://');
                        if (imgUrl) replacement = `\n![Mô tả ảnh](${imgUrl})\n`;
                        else return;
                        break;
                    }
                    default:
                        return;
                }

                textarea.setRangeText(replacement, start, end, 'end');
                textarea.focus();
            });
        });
    }

    if (togglePreview && previewArea) {
        togglePreview.addEventListener('change', () => {
            if (togglePreview.checked) {
                previewArea.innerHTML = textarea.value
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/~~(.*?)~~/g, '<del>$1</del>')
                    .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-primary underline" target="_blank">$1</a>')
                    .replace(/\n- (.*?)/g, '<br>• $1')
                    .replace(/\n/g, '<br>');
                previewArea.classList.remove('hidden');
                textarea.classList.add('hidden');
            } else {
                previewArea.classList.add('hidden');
                textarea.classList.remove('hidden');
            }
        });
    }
}

export function initActionButtons() {
    // Quick Add Category prompt
    document.querySelectorAll('[data-action-add-category]').forEach(btn => {
        btn.addEventListener('click', () => {
            const catName = prompt('Nhập tên danh mục mới muốn thêm nhanh:');
            if (catName && catName.trim()) {
                const sel = document.getElementById('category_id');
                if (sel) {
                    const opt = document.createElement('option');
                    opt.value = sel.options[1] ? sel.options[1].value : '1';
                    opt.textContent = catName.trim() + ' (Mới)';
                    sel.appendChild(opt);
                    sel.value = opt.value;
                    sel.dispatchEvent(new Event('change', { bubbles: true }));

                    // Update custom select trigger text if exists
                    const triggerText = document.querySelector('[data-select-for="category_id"] .custom-select-text');
                    if (triggerText) triggerText.textContent = catName.trim();
                }
            }
        });
    });

    // Quick Add Brand prompt
    document.querySelectorAll('[data-action-add-brand]').forEach(btn => {
        btn.addEventListener('click', () => {
            const brandName = prompt('Nhập tên thương hiệu mới:');
            if (brandName && brandName.trim()) {
                const brandInput = document.getElementById('brand');
                if (brandInput) {
                    brandInput.value = brandName.trim();
                }
            }
        });
    });

    // Promotion schedule prompt
    document.querySelectorAll('[data-action-promo-schedule]').forEach(btn => {
        btn.addEventListener('click', () => {
            alert('Khuyến mãi được tự động áp dụng khi đăng sản phẩm. Bạn có thể thiết lập Flash Sale trong khung giờ vàng của gian hàng!');
        });
    });
}

export function initSellerProductForm() {
    window.deleteGalleryImage = deleteGalleryImage;
    initGalleryPreview();
    initVariantMatrix();
    initCustomDropdowns();
    initYoutubeTags();
    initEditorToolbar();
    initActionButtons();
}

document.addEventListener('DOMContentLoaded', () => {
    initSellerProductForm();
});

