/**
 * ShopMart Address Modal & Selector Manager
 * Reusable across Checkout and Profile without duplicate models or logic.
 */
(function () {
    'use strict';

    const DEFAULT_LAT = 21.028511;
    const DEFAULT_LNG = 105.854444;

    let leafletMap = null;
    let leafletMarker = null;
    let mapInitialized = false;

    window.AddressModalManager = {
        pageContext: 'checkout', // 'checkout' or 'profile'
        currentlySelectedId: null,

        init(context = 'checkout') {
            this.pageContext = context;

            // Keyboard ESC listener
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const formModal = document.getElementById('address-form-modal');
                    const selectorModal = document.getElementById('address-selector-modal');
                    if (formModal && !formModal.classList.contains('hidden')) {
                        this.closeFormModal();
                    } else if (selectorModal && !selectorModal.classList.contains('hidden')) {
                        this.closeSelectorModal();
                    }
                }
            });
        },

        // ==================== SELECTOR MODAL (CHECKOUT) ====================
        openSelectorModal() {
            const modal = document.getElementById('address-selector-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        },

        closeSelectorModal() {
            const modal = document.getElementById('address-selector-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        },

        selectItem(cardEl) {
            if (!cardEl) return;
            document.querySelectorAll('.address-select-card').forEach((el) => {
                el.classList.remove('is-selected');
                const dot = el.querySelector('.address-radio-dot');
                if (dot) dot.classList.add('hidden');
            });

            cardEl.classList.add('is-selected');
            const dot = cardEl.querySelector('.address-radio-dot');
            if (dot) dot.classList.remove('hidden');

            this.currentlySelectedId = cardEl.getAttribute('data-id');
        },

        confirmSelection() {
            const selectedCard = document.querySelector('.address-select-card.is-selected');
            if (!selectedCard) {
                this.closeSelectorModal();
                return;
            }

            const name = selectedCard.getAttribute('data-name');
            const phone = selectedCard.getAttribute('data-phone');
            const address = selectedCard.getAttribute('data-address');
            const isDefault = selectedCard.getAttribute('data-default') === '1';
            const id = selectedCard.getAttribute('data-id');

            // Update Checkout Display
            const dispName = document.getElementById('display-recipient-name');
            const dispPhone = document.getElementById('display-recipient-phone');
            const dispAddr = document.getElementById('display-recipient-address');
            const dispBadge = document.getElementById('display-badge-default');

            if (dispName) dispName.textContent = name;
            if (dispPhone) dispPhone.textContent = `(${phone})`;
            if (dispAddr) dispAddr.textContent = address;
            if (dispBadge) {
                if (isDefault) {
                    dispBadge.classList.remove('hidden');
                } else {
                    dispBadge.classList.add('hidden');
                }
            }

            // Update hidden form inputs
            const inName = document.getElementById('input-recipient-name');
            const inPhone = document.getElementById('input-recipient-phone');
            const inAddr = document.getElementById('input-recipient-address');
            const inId = document.getElementById('input-recipient-id');

            if (inName) inName.value = name;
            if (inPhone) inPhone.value = phone;
            if (inAddr) inAddr.value = address;
            if (inId) inId.value = id;

            // Show address display, hide empty state
            document.getElementById('checkout-address-display')?.classList.remove('hidden');
            document.getElementById('checkout-address-empty')?.classList.add('hidden');

            this.closeSelectorModal();
        },

        // ==================== FORM MODAL (ADD / EDIT) ====================
        openCreateModal() {
            const modal = document.getElementById('address-form-modal');
            if (!modal) return;

            document.getElementById('address-form-title').textContent = 'Thêm địa chỉ nhận hàng';
            document.getElementById('btn-save-address-text').textContent = 'Lưu địa chỉ';
            document.getElementById('addr-mode').value = 'create';
            document.getElementById('addr-id').value = '';

            // Reset inputs
            document.getElementById('addr-recipient-name').value = '';
            document.getElementById('addr-phone').value = '';
            document.getElementById('addr-province').value = '';
            document.getElementById('addr-district').value = '';
            document.getElementById('addr-ward').value = '';
            document.getElementById('addr-detail').value = '';
            document.getElementById('addr-is-default').checked = false;

            this.clearErrors();
            this.hideMap();

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        },

        openEditModal(address) {
            const modal = document.getElementById('address-form-modal');
            if (!modal) return;

            document.getElementById('address-form-title').textContent = 'Cập nhật địa chỉ nhận hàng';
            document.getElementById('btn-save-address-text').textContent = 'Cập nhật';
            document.getElementById('addr-mode').value = 'edit';
            document.getElementById('addr-id').value = address.id;

            document.getElementById('addr-recipient-name').value = address.recipient_name || '';
            document.getElementById('addr-phone').value = address.phone || '';
            document.getElementById('addr-detail').value = address.address_line || '';
            document.getElementById('addr-is-default').checked = Boolean(address.is_default);

            // Attempt to match province from existing line if present
            const provSelect = document.getElementById('addr-province');
            if (provSelect && address.address_line) {
                Array.from(provSelect.options).forEach((opt) => {
                    if (opt.value && address.address_line.includes(opt.value)) {
                        provSelect.value = opt.value;
                    }
                });
            }

            this.clearErrors();
            this.hideMap();

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        },

        closeFormModal() {
            const modal = document.getElementById('address-form-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            // If selector modal is not open, restore scroll
            const selectorModal = document.getElementById('address-selector-modal');
            if (!selectorModal || selectorModal.classList.contains('hidden')) {
                document.body.style.overflow = '';
            }
        },

        clearErrors() {
            ['err-addr-name', 'err-addr-phone', 'err-addr-line'].forEach((id) => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = '';
                    el.classList.add('hidden');
                }
            });
        },

        showFieldError(id, msg) {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = msg;
                el.classList.remove('hidden');
            }
        },

        // ==================== MAP / GPS ====================
        toggleMap() {
            const wrapper = document.getElementById('addr-map-wrapper');
            const toggleText = document.getElementById('map-toggle-text');
            if (!wrapper) return;

            if (wrapper.classList.contains('hidden')) {
                wrapper.classList.remove('hidden');
                if (toggleText) toggleText.textContent = 'Ẩn bản đồ';
                this.initMapIfNeeded();
            } else {
                wrapper.classList.add('hidden');
                if (toggleText) toggleText.textContent = 'Chọn trên bản đồ';
            }
        },

        hideMap() {
            const wrapper = document.getElementById('addr-map-wrapper');
            const toggleText = document.getElementById('map-toggle-text');
            if (wrapper) wrapper.classList.add('hidden');
            if (toggleText) toggleText.textContent = 'Chọn trên bản đồ';
        },

        initMapIfNeeded() {
            if (typeof L === 'undefined') {
                console.warn('Leaflet map library is not loaded.');
                return;
            }

            const mapEl = document.getElementById('addr-map');
            if (!mapEl) return;

            if (!leafletMap) {
                leafletMap = L.map('addr-map').setView([DEFAULT_LAT, DEFAULT_LNG], 14);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(leafletMap);

                leafletMarker = L.marker([DEFAULT_LAT, DEFAULT_LNG], { draggable: true }).addTo(leafletMap);

                leafletMap.on('click', (e) => {
                    const { lat, lng } = e.latlng;
                    leafletMarker.setLatLng([lat, lng]);
                    this.reverseGeocode(lat, lng);
                });

                leafletMarker.on('dragend', (e) => {
                    const { lat, lng } = e.target.getLatLng();
                    this.reverseGeocode(lat, lng);
                });
            }

            // Double-call invalidateSize: once after layout paint, once after tiles have time to queue
            requestAnimationFrame(() => {
                if (leafletMap) leafletMap.invalidateSize();
                setTimeout(() => { if (leafletMap) leafletMap.invalidateSize(); }, 400);
            });
        },

        async reverseGeocode(lat, lng) {
            const detailInput = document.getElementById('addr-detail');
            if (!detailInput) return;
            const originalPh = detailInput.placeholder;
            detailInput.placeholder = 'Đang lấy địa chỉ từ tọa độ...';

            try {
                // BigDataCloud: free, no API key, works in Vietnam
                const res = await fetch(
                    `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=vi`
                );
                if (res.ok) {
                    const data = await res.json();
                    // Build address from most-specific admin areas (order 3+ = sub-country level)
                    const adminParts = (data.localityInfo?.administrative || [])
                        .sort((a, b) => b.order - a.order)
                        .filter((a) => a.order >= 3)
                        .slice(0, 4)
                        .map((a) => a.name)
                        .filter(Boolean);

                    const address =
                        adminParts.join(', ') ||
                        [data.locality, data.city, data.principalSubdivision]
                            .filter(Boolean)
                            .join(', ');

                    if (address) detailInput.value = address;
                }
            } catch (err) {
                console.warn('Reverse geocode error:', err);
                // Fallback: show raw coordinates so user knows something happened
                detailInput.value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            } finally {
                detailInput.placeholder = originalPh;
            }
        },

        triggerGPS() {
            if (!navigator.geolocation) {
                alert('Trình duyệt của bạn không hỗ trợ Geolocation.');
                return;
            }

            const btn = document.getElementById('btn-trigger-gps');
            const originalText = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<span>Đang định vị...</span>'; }

            const done = () => { if (btn) { btn.disabled = false; btn.innerHTML = originalText; } };

            const onSuccess = (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                // Update map if open
                const wrapper = document.getElementById('addr-map-wrapper');
                if (wrapper && wrapper.classList.contains('hidden')) {
                    this.toggleMap();
                }
                if (leafletMap && leafletMarker) {
                    leafletMap.setView([lat, lng], 16);
                    leafletMarker.setLatLng([lat, lng]);
                }

                this.reverseGeocode(lat, lng);
                done();
            };

            const onError = (err) => {
                const msgs = {
                    1: 'Bạn đã từ chối quyền vị trí. Vui lòng cho phép trong cài đặt trình duyệt.',
                    2: 'Không thể xác định vị trí (không có GPS/WiFi).',
                    3: 'Hết thời gian chờ. Vui lòng thử lại.',
                };
                alert(msgs[err.code] || 'Không thể lấy vị trí GPS.');
                done();
            };

            // Use network-based location (works on desktop without GPS chip)
            // enableHighAccuracy:true times out on desktops — use false as primary
            navigator.geolocation.getCurrentPosition(onSuccess, onError, {
                enableHighAccuracy: false,
                timeout: 12000,
                maximumAge: 30000,
            });
        },

        // ==================== FORM SUBMISSION (AJAX) ====================
        async handleFormSubmit(e) {
            e.preventDefault();
            this.clearErrors();

            const mode = document.getElementById('addr-mode').value;
            const id = document.getElementById('addr-id').value;
            const name = document.getElementById('addr-recipient-name').value.trim();
            const phone = document.getElementById('addr-phone').value.trim();
            const province = document.getElementById('addr-province')?.value.trim() || '';
            const district = document.getElementById('addr-district')?.value.trim() || '';
            const ward = document.getElementById('addr-ward')?.value.trim() || '';
            const detail = document.getElementById('addr-detail').value.trim();
            const isDefault = document.getElementById('addr-is-default').checked;

            if (!name) {
                this.showFieldError('err-addr-name', 'Vui lòng nhập họ và tên người nhận.');
                return;
            }
            if (!phone) {
                this.showFieldError('err-addr-phone', 'Vui lòng nhập số điện thoại.');
                return;
            }
            if (!detail) {
                this.showFieldError('err-addr-line', 'Vui lòng nhập địa chỉ cụ thể.');
                return;
            }

            // Build full address line if administrative fields were provided
            let fullAddress = detail;
            const adminParts = [ward, district, province].filter(Boolean);
            if (adminParts.length > 0) {
                const adminString = adminParts.join(', ');
                if (!fullAddress.includes(province) && !fullAddress.includes(district)) {
                    fullAddress = `${detail}, ${adminString}`;
                }
            }

            const submitBtn = document.getElementById('btn-save-address-submit');
            const submitText = document.getElementById('btn-save-address-text');
            if (submitBtn) submitBtn.disabled = true;
            if (submitText) submitText.textContent = 'Đang lưu...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value;

            const url = mode === 'edit' && id ? `/profile/address/${id}` : '/profile/address';
            const method = mode === 'edit' ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        recipient_name: name,
                        phone: phone,
                        address_line: fullAddress,
                        is_default: isDefault
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.closeFormModal();

                    if (this.pageContext === 'profile') {
                        // In Profile page, reload to refresh the address list and flash message
                        window.location.reload();
                        return;
                    }

                    // In Checkout page: refresh address selector list and select newly saved address!
                    await this.reloadCheckoutAddresses(data.address);
                } else {
                    const msg = data.message || 'Có lỗi xảy ra khi lưu địa chỉ.';
                    alert(msg);
                }
            } catch (err) {
                console.error('Save address error:', err);
                alert('Không thể kết nối đến máy chủ. Vui lòng thử lại!');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
                if (submitText) submitText.textContent = mode === 'edit' ? 'Cập nhật' : 'Lưu địa chỉ';
            }
        },

        // ==================== CHECKOUT SYNC ====================
        async reloadCheckoutAddresses(selectedAddress = null) {
            try {
                const res = await fetch('/profile/addresses', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;

                const data = await res.json();
                if (!data.success || !data.addresses) return;

                const listContainer = document.getElementById('address-selector-list');
                if (!listContainer) return;

                const activeTargetId = selectedAddress ? String(selectedAddress.id) : this.currentlySelectedId;

                listContainer.innerHTML = '';

                data.addresses.forEach((addr) => {
                    const isSelected = activeTargetId ? (String(addr.id) === String(activeTargetId)) : Boolean(addr.is_default);

                    const card = document.createElement('div');
                    card.className = `address-select-card p-3.5 bg-white rounded-xl border-2 transition-all cursor-pointer group ${isSelected ? 'is-selected' : ''}`;
                    card.setAttribute('data-id', addr.id);
                    card.setAttribute('data-name', addr.recipient_name);
                    card.setAttribute('data-phone', addr.phone);
                    card.setAttribute('data-address', addr.address_line);
                    card.setAttribute('data-default', addr.is_default ? '1' : '0');
                    card.onclick = () => window.AddressModalManager.selectItem(card);

                    card.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 address-radio-circle border-gray-300">
                                    <span class="w-2 h-2 rounded-full bg-primary ${isSelected ? '' : 'hidden'} address-radio-dot"></span>
                                </div>
                                <div class="min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs sm:text-sm font-bold text-gray-900 addr-item-name">${this.escapeHtml(addr.recipient_name)}</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-xs text-gray-600 font-medium font-mono addr-item-phone">${this.escapeHtml(addr.phone)}</span>
                                        ${addr.is_default ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">Mặc định</span>' : ''}
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed addr-item-address">
                                        ${this.escapeHtml(addr.address_line)}
                                    </p>
                                </div>
                            </div>
                            <button 
                                type="button" 
                                class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline shrink-0 pt-0.5 cursor-pointer btn-edit-addr"
                            >
                                Sửa
                            </button>
                        </div>
                    `;

                    card.querySelector('.btn-edit-addr')?.addEventListener('click', (e) => {
                        e.stopPropagation();
                        window.AddressModalManager.openEditModal(addr);
                    });

                    listContainer.appendChild(card);
                });

                // Auto-confirm the newly created/selected address onto checkout page
                if (selectedAddress) {
                    const newlySelectedCard = listContainer.querySelector(`.address-select-card[data-id="${selectedAddress.id}"]`);
                    if (newlySelectedCard) {
                        this.selectItem(newlySelectedCard);
                        this.confirmSelection();
                    }
                }
            } catch (err) {
                console.error('Failed to reload checkout addresses:', err);
            }
        },

        escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    };

})();
