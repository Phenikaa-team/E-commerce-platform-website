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
            let rawAddrLine = address.address_line || '';
            if (/^\d+\.\d+,\s*\d+\.\d+/.test(rawAddrLine)) {
                rawAddrLine = rawAddrLine.replace(/^\d+\.\d+,\s*\d+\.\d+,?\s*/, '').trim();
            }
            document.getElementById('addr-detail').value = rawAddrLine;
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

        showLocationToast(msg) {
            let toast = document.getElementById('addr-location-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'addr-location-toast';
                toast.className = 'fixed bottom-5 left-1/2 -translate-x-1/2 z-[9999] bg-gray-900/95 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-xl backdrop-blur-sm border border-white/10 transition-all duration-300 opacity-0 pointer-events-none text-center max-w-sm';
                document.body.appendChild(toast);
            }
            toast.textContent = msg;
            toast.classList.remove('opacity-0', 'pointer-events-none');
            toast.classList.add('opacity-100');
            clearTimeout(toast._timer);
            toast._timer = setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0', 'pointer-events-none');
            }, 4000);
        },

        async reverseGeocode(lat, lng) {
            const detailInput = document.getElementById('addr-detail');
            const provSelect = document.getElementById('addr-province');
            const distInput = document.getElementById('addr-district');
            const wardInput = document.getElementById('addr-ward');
            if (!detailInput) return;

            const originalPh = detailInput.placeholder;
            detailInput.placeholder = 'Đang tra cứu địa chỉ văn bản...';

            try {
                let data = null;

                // 1. Try our reliable backend geocoding endpoint
                try {
                    const res = await fetch(`/api/geocode/reverse?lat=${lat}&lng=${lng}`);
                    if (res.ok) {
                        data = await res.json();
                    }
                } catch (e) {
                    console.warn('Backend geocode fetch error, trying direct provider:', e);
                }

                // 2. Direct Nominatim if backend was unreachable
                if (!data || !data.success) {
                    try {
                        const directRes = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=vi&addressdetails=1`
                        );
                        if (directRes.ok) {
                            const d = await directRes.json();
                            const addr = d.address || {};
                            const rawProv = addr.city || addr.state || addr.province || '';
                            const road = addr.road || addr.street || addr.pedestrian || '';
                            const num = addr.house_number ? 'Số ' + addr.house_number : '';
                            const detailText = [num, road].filter(Boolean).join(', ') || d.name || '';
                            data = {
                                success: true,
                                province: rawProv,
                                district: addr.district || addr.city_district || addr.county || addr.town || '',
                                ward: addr.suburb || addr.quarter || addr.neighbourhood || addr.village || addr.hamlet || '',
                                detail: detailText,
                                full_address: d.display_name || ''
                            };
                        }
                    } catch (e) {
                        console.warn('Direct Nominatim failed:', e);
                    }
                }

                // 3. Fallback: BigDataCloud
                if (!data || !data.success) {
                    try {
                        const bdcRes = await fetch(
                            `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=vi`
                        );
                        if (bdcRes.ok) {
                            const bdc = await bdcRes.json();
                            data = {
                                success: true,
                                province: bdc.principalSubdivision || '',
                                district: bdc.locality || '',
                                ward: '',
                                detail: bdc.locality ? 'Khu vực ' + bdc.locality : 'Khu vực ' + (bdc.principalSubdivision || ''),
                                full_address: [bdc.locality, bdc.principalSubdivision].filter(Boolean).join(', ')
                            };
                        }
                    } catch (e) {
                        console.warn('BigDataCloud failed:', e);
                    }
                }

                if (data && data.success) {
                    // Match Province / City dropdown
                    if (provSelect && data.province) {
                        const targetProv = data.province.toLowerCase();
                        Array.from(provSelect.options).forEach((opt) => {
                            if (!opt.value) return;
                            const optLower = opt.value.toLowerCase();
                            if (
                                optLower.includes(targetProv) ||
                                targetProv.includes(optLower.replace('tp. ', '')) ||
                                (targetProv.includes('hà nội') && optLower.includes('hà nội')) ||
                                (targetProv.includes('hồ chí minh') && optLower.includes('hồ chí minh')) ||
                                (targetProv.includes('đà nẵng') && optLower.includes('đà nẵng'))
                            ) {
                                provSelect.value = opt.value;
                            }
                        });
                    }

                    // Fill District if empty or updated
                    if (distInput && data.district) {
                        distInput.value = data.district;
                    }

                    // Fill Ward if empty or updated
                    if (wardInput && data.ward) {
                        wardInput.value = data.ward;
                    }

                    // Fill Detail: MUST BE MEANINGFUL TEXT, NEVER COORDINATES NUMBERS!
                    const textDetail = data.detail || (data.ward ? 'Khu vực ' + data.ward : (data.district ? 'Khu vực ' + data.district : 'Khu vực ' + (data.province || '')));
                    if (textDetail && !/^\d+\.\d+,\s*\d+\.\d+/.test(textDetail)) {
                        detailInput.value = textDetail;
                    } else if (data.ward || data.district) {
                        detailInput.value = 'Khu vực ' + (data.ward || data.district);
                    }
                } else {
                    // Fallback to text area name, NEVER raw numbers!
                    if (!detailInput.value || /^\d+\.\d+,\s*\d+\.\d+/.test(detailInput.value)) {
                        detailInput.value = 'Vị trí đã chọn trên bản đồ';
                    }
                }
            } catch (err) {
                console.warn('Reverse geocode error:', err);
                if (!detailInput.value || /^\d+\.\d+,\s*\d+\.\d+/.test(detailInput.value)) {
                    detailInput.value = 'Vị trí đã chọn trên bản đồ';
                }
            } finally {
                detailInput.placeholder = originalPh;
            }
        },

        async triggerGPS() {
            const btn = document.getElementById('btn-trigger-gps');
            const originalText = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span>Đang định vị...</span>';
            }

            const done = () => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            };

            const applyPosition = (lat, lng, source = 'gps') => {
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

                if (source === 'ip') {
                    this.showLocationToast('Đã định vị theo vị trí mạng (IP). Bạn có thể bấm chọn trên bản đồ để chỉnh chính xác hơn.');
                } else {
                    this.showLocationToast('Đã định vị thành công vị trí của bạn!');
                }
            };

            // Seamless IP fallback when device GPS is blocked, unavailable, or times out
            const tryIpFallback = async () => {
                try {
                    const res = await fetch('/api/geocode/ip');
                    if (res.ok) {
                        const data = await res.json();
                        if (data && data.success && data.lat && data.lng) {
                            applyPosition(data.lat, data.lng, 'ip');
                            return;
                        }
                    }
                } catch (e) {
                    console.warn('Backend IP location failed, trying direct:', e);
                }

                try {
                    const res = await fetch('http://ip-api.com/json/');
                    if (res.ok) {
                        const data = await res.json();
                        if (data && data.lat && data.lon) {
                            applyPosition(data.lat, data.lon, 'ip');
                            return;
                        }
                    }
                } catch (e) {
                    console.warn('Direct IP location failed:', e);
                }

                // If even IP lookup fails, gracefully pan to default (Hanoi) without blocking alert
                applyPosition(DEFAULT_LAT, DEFAULT_LNG, 'ip');
            };

            if (!navigator.geolocation) {
                await tryIpFallback();
                return;
            }

            let resolved = false;
            // 6 second timeout before switching to IP fallback
            const fallbackTimer = setTimeout(() => {
                if (!resolved) {
                    resolved = true;
                    tryIpFallback();
                }
            }, 6000);

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    if (resolved) return;
                    resolved = true;
                    clearTimeout(fallbackTimer);
                    applyPosition(pos.coords.latitude, pos.coords.longitude, 'gps');
                },
                (err) => {
                    if (resolved) return;
                    resolved = true;
                    clearTimeout(fallbackTimer);
                    console.warn('Browser GPS error or blocked by OS settings, falling back to IP:', err);
                    // Silently fall back to IP without showing an annoying blocking popup!
                    tryIpFallback();
                },
                {
                    enableHighAccuracy: true,
                    timeout: 5500,
                    maximumAge: 10000,
                }
            );
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
            let detail = document.getElementById('addr-detail').value.trim();
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

            // Strip any raw coordinate numbers if present
            if (/^\d+\.\d+,\s*\d+\.\d+/.test(detail)) {
                detail = detail.replace(/^\d+\.\d+,\s*\d+\.\d+,?\s*/, '').trim();
                if (!detail) {
                    detail = ward ? `Khu vực ${ward}` : (district ? `Khu vực ${district}` : 'Vị trí bản đồ');
                }
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
