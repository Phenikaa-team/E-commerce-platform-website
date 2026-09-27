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

    if (imagesInput && previewGrid) {
        imagesInput.addEventListener('change', (e) => {
            previewGrid.innerHTML = '';
            const files = Array.from(e.target.files || []);

            files.forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const card = document.createElement('div');
                card.className = 'image-preview-card group';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'image-preview-card__img';

                const badge = document.createElement('span');
                badge.className = 'image-preview-card__badge';
                badge.textContent = '#' + (index + 1);

                card.appendChild(img);
                card.appendChild(badge);
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

export function initSellerProductForm() {
    window.deleteGalleryImage = deleteGalleryImage;
    initGalleryPreview();
    initVariantMatrix();
}

document.addEventListener('DOMContentLoaded', () => {
    initSellerProductForm();
});
