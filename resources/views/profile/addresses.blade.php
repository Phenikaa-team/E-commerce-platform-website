@extends('layouts.app')

@section('title', 'Địa chỉ của tôi - ' . ($user->username ?? $user->name) . ' | ShopMart')
@section('meta_description', 'Quản lý sổ địa chỉ nhận hàng tiện lợi với bản đồ định vị thông minh tại ShopMart.')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .leaflet-container {
        font-family: inherit;
        z-index: 10 !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: SIDEBAR MENU -->
            <div class="lg:col-span-3">
                <x-user-sidebar active="addresses" />
            </div>

            <!-- RIGHT COLUMN: ADDRESS LIST -->
            <div class="lg:col-span-9 space-y-6">
                
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-xs">
                    <!-- Top Bar -->
                    <div class="pb-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-xl font-black text-gray-900">Địa chỉ của tôi</h1>
                            <p class="text-xs text-gray-500 mt-1">Quản lý các địa chỉ giao nhận hàng với bản đồ định vị thông minh</p>
                        </div>
                        <button 
                            type="button" 
                            onclick="openAddAddressModal()" 
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#ea384c] to-[#ff5c6c] hover:from-[#d3273b] hover:to-[#ea384c] text-white text-xs sm:text-sm font-bold shadow-md shadow-rose-500/20 active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Thêm địa chỉ mới</span>
                        </button>
                    </div>

                    <!-- Address List Items -->
                    <div class="divide-y divide-gray-100">
                        @forelse($user->addresses as $address)
                            <div class="py-5 flex flex-col sm:flex-row sm:items-start justify-between gap-4 group">
                                <div class="space-y-2 flex-1">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="font-bold text-sm text-gray-900">{{ $address->recipient_name }}</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-xs text-gray-600 font-medium">{{ $address->phone }}</span>
                                        @if($address->is_default)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-[#ea384c] border border-[#ea384c] bg-rose-50/50">
                                                Mặc định
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed max-w-2xl flex items-start gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $address->address_line }}</span>
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-3 shrink-0 self-start sm:self-center">
                                    <button 
                                        type="button" 
                                        onclick='openEditAddressModal(@json($address))' 
                                        class="text-xs text-blue-600 hover:text-blue-700 font-semibold cursor-pointer hover:underline"
                                    >
                                        Cập nhật
                                    </button>

                                    @if(! $address->is_default)
                                        <form action="{{ route('profile.address.delete', $address->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-semibold cursor-pointer hover:underline">
                                                Xóa
                                            </button>
                                        </form>

                                        <form action="{{ route('profile.address.default', $address->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs text-gray-600 hover:text-[#ea384c] font-medium border border-gray-200 hover:border-[#ea384c] px-3 py-1 rounded-lg transition-colors cursor-pointer">
                                                Thiết lập mặc định
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-16 h-16 rounded-full bg-rose-50 text-[#ea384c] mx-auto flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-800">Chưa có địa chỉ nào</h3>
                                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Thêm địa chỉ nhận hàng để việc đặt hàng và thanh toán diễn ra nhanh chóng hơn.</p>
                                <button 
                                    type="button" 
                                    onclick="openAddAddressModal()" 
                                    class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#ea384c] text-white text-xs font-bold hover:bg-[#d3273b] transition-all cursor-pointer"
                                >
                                    + Thêm địa chỉ mới
                                </button>
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>

        </div>
    </div>


    <!-- ==================== MODAL: THÊM ĐỊA CHỈ MỚI CÓ MAP ==================== -->
    <div id="add-address-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl relative border border-gray-100 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Thêm địa chỉ nhận hàng</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Chọn vị trí nhanh trên bản đồ hoặc định vị GPS tự động</p>
                </div>
                <button type="button" onclick="closeAddAddressModal()" class="text-gray-400 hover:text-gray-700 p-1 rounded-full cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Interactive Map Section -->
            <div class="mb-5 space-y-2">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-gray-700">
                        <svg class="w-4 h-4 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Chọn vị trí trên bản đồ</span>
                    </div>

                    <!-- GPS Location Button -->
                    <button 
                        type="button" 
                        onclick="useCurrentLocation('add')" 
                        id="add-gps-btn"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-[#ea384c] hover:bg-rose-100 text-xs font-bold transition-all cursor-pointer border border-rose-200 shadow-2xs"
                    >
                        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Lấy vị trí hiện tại (GPS)</span>
                    </button>
                </div>

                <!-- Leaflet Map Container -->
                <div id="add-map-container" class="w-full h-56 rounded-2xl border border-gray-200 overflow-hidden relative shadow-inner">
                    <div id="add-map" class="w-full h-full"></div>
                </div>
                <p class="text-[11px] text-gray-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>Nhấp trực tiếp trên bản đồ hoặc kéo ghim để tự động lấy địa chỉ chính xác.</span>
                </p>
            </div>

            <!-- Form -->
            <form action="{{ route('profile.address.add') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Họ và tên người nhận <span class="text-rose-500">*</span></label>
                        <input type="text" name="recipient_name" required placeholder="Ví dụ: Nguyễn Văn A" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Số điện thoại <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" required placeholder="Ví dụ: (+84) 912 345 678" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Địa chỉ nhận hàng chi tiết <span class="text-rose-500">*</span></label>
                    <textarea 
                        name="address_line" 
                        id="add-address-line" 
                        rows="2" 
                        required 
                        placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" 
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none"
                    ></textarea>
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox" name="is_default" value="1" class="w-4 h-4 rounded text-[#ea384c] focus:ring-rose-500 border-gray-300 accent-[#ea384c]">
                        <span>Đặt làm địa chỉ mặc định</span>
                    </label>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeAddAddressModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 cursor-pointer">
                        Hủy
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#ea384c] to-[#ff5c6c] hover:from-[#d3273b] hover:to-[#ea384c] text-white text-xs font-bold shadow-md shadow-rose-500/20 cursor-pointer">
                        Lưu địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL: CHỈNH SỬA ĐỊA CHỈ CÓ MAP ==================== -->
    <div id="edit-address-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl relative border border-gray-100 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Cập nhật địa chỉ nhận hàng</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Chỉnh sửa thông tin hoặc chọn lại vị trí trên bản đồ</p>
                </div>
                <button type="button" onclick="closeEditAddressModal()" class="text-gray-400 hover:text-gray-700 p-1 rounded-full cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Interactive Map Section for Edit -->
            <div class="mb-5 space-y-2">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-gray-700">
                        <svg class="w-4 h-4 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Chọn lại vị trí trên bản đồ</span>
                    </div>

                    <!-- GPS Location Button for Edit -->
                    <button 
                        type="button" 
                        onclick="useCurrentLocation('edit')" 
                        id="edit-gps-btn"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-[#ea384c] hover:bg-rose-100 text-xs font-bold transition-all cursor-pointer border border-rose-200 shadow-2xs"
                    >
                        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Lấy vị trí hiện tại (GPS)</span>
                    </button>
                </div>

                <div id="edit-map-container" class="w-full h-56 rounded-2xl border border-gray-200 overflow-hidden relative shadow-inner">
                    <div id="edit-map" class="w-full h-full"></div>
                </div>
                <p class="text-[11px] text-gray-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>Nhấp vào bản đồ hoặc bấm 'Lấy vị trí hiện tại' để cập nhật nhanh địa chỉ.</span>
                </p>
            </div>

            <!-- Form -->
            <form id="edit-address-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Họ và tên người nhận <span class="text-rose-500">*</span></label>
                        <input type="text" name="recipient_name" id="edit-recipient-name" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Số điện thoại <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" id="edit-phone" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Địa chỉ nhận hàng chi tiết <span class="text-rose-500">*</span></label>
                    <textarea 
                        name="address_line" 
                        id="edit-address-line" 
                        rows="2" 
                        required 
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none"
                    ></textarea>
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox" name="is_default" id="edit-is-default" value="1" class="w-4 h-4 rounded text-[#ea384c] focus:ring-rose-500 border-gray-300 accent-[#ea384c]">
                        <span>Đặt làm địa chỉ mặc định</span>
                    </label>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeEditAddressModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 cursor-pointer">
                        Hủy
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#ea384c] to-[#ff5c6c] hover:from-[#d3273b] hover:to-[#ea384c] text-white text-xs font-bold shadow-md shadow-rose-500/20 cursor-pointer">
                        Cập nhật địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Map instances and markers
        let addMap = null;
        let addMarker = null;
        let editMap = null;
        let editMarker = null;

        // Default coordinate (Hanoi centre)
        const DEFAULT_LAT = 21.028511;
        const DEFAULT_LNG = 105.854444;

        // Helper: reverse geocode coordinate into human address string
        async function reverseGeocode(lat, lng, targetInputId) {
            const input = document.getElementById(targetInputId);
            if (!input) return;
            const originalPlaceholder = input.placeholder;
            input.placeholder = "Đang tải tên địa chỉ từ tọa độ bản đồ...";
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=vi`);
                if (response.ok) {
                    const data = await response.json();
                    if (data && data.display_name) {
                        input.value = data.display_name;
                    }
                }
            } catch (err) {
                console.warn("Reverse geocode error:", err);
            } finally {
                input.placeholder = originalPlaceholder;
            }
        }

        // Initialize Add Address Map
        function initAddMap() {
            if (addMap) {
                setTimeout(() => addMap.invalidateSize(), 200);
                return;
            }

            addMap = L.map('add-map').setView([DEFAULT_LAT, DEFAULT_LNG], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(addMap);

            addMarker = L.marker([DEFAULT_LAT, DEFAULT_LNG], { draggable: true }).addTo(addMap);

            // Click on map to place pin and geocode
            addMap.on('click', function(e) {
                const { lat, lng } = e.latlng;
                addMarker.setLatLng([lat, lng]);
                reverseGeocode(lat, lng, 'add-address-line');
            });

            // Drag marker to geocode
            addMarker.on('dragend', function(e) {
                const { lat, lng } = e.target.getLatLng();
                reverseGeocode(lat, lng, 'add-address-line');
            });

            setTimeout(() => addMap.invalidateSize(), 250);
        }

        // Initialize Edit Address Map
        function initEditMap(initialLat = DEFAULT_LAT, initialLng = DEFAULT_LNG) {
            if (editMap) {
                editMap.setView([initialLat, initialLng], 15);
                if (editMarker) editMarker.setLatLng([initialLat, initialLng]);
                setTimeout(() => editMap.invalidateSize(), 200);
                return;
            }

            editMap = L.map('edit-map').setView([initialLat, initialLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(editMap);

            editMarker = L.marker([initialLat, initialLng], { draggable: true }).addTo(editMap);

            editMap.on('click', function(e) {
                const { lat, lng } = e.latlng;
                editMarker.setLatLng([lat, lng]);
                reverseGeocode(lat, lng, 'edit-address-line');
            });

            editMarker.on('dragend', function(e) {
                const { lat, lng } = e.target.getLatLng();
                reverseGeocode(lat, lng, 'edit-address-line');
            });

            setTimeout(() => editMap.invalidateSize(), 250);
        }

        // Browser Geolocation API: Prompts user for permission and locates GPS
        function useCurrentLocation(type) {
            const btnId = type === 'add' ? 'add-gps-btn' : 'edit-gps-btn';
            const inputId = type === 'add' ? 'add-address-line' : 'edit-address-line';
            const btn = document.getElementById(btnId);
            const originalText = btn.innerHTML;

            if (!navigator.geolocation) {
                alert("Trình duyệt của bạn không hỗ trợ Geolocation.");
                return;
            }

            btn.innerHTML = `<svg class="w-4 h-4 animate-spin inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Đang định vị GPS...`;
            btn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    if (type === 'add') {
                        if (addMap && addMarker) {
                            addMap.setView([lat, lng], 16);
                            addMarker.setLatLng([lat, lng]);
                        }
                    } else {
                        if (editMap && editMarker) {
                            editMap.setView([lat, lng], 16);
                            editMarker.setLatLng([lat, lng]);
                        }
                    }

                    reverseGeocode(lat, lng, inputId);

                    btn.innerHTML = originalText;
                    btn.disabled = false;
                },
                (error) => {
                    let message = "Không thể lấy vị trí hiện tại.";
                    if (error.code === error.PERMISSION_DENIED) {
                        message = "Bạn đã từ chối cấp quyền truy cập vị trí. Vui lòng cho phép quyền truy cập vị trí trên trình duyệt hoặc nhấp chọn trực tiếp trên bản đồ.";
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        message = "Thông tin vị trí hiện không khả dụng.";
                    } else if (error.code === error.TIMEOUT) {
                        message = "Thời gian yêu cầu vị trí đã hết hạn. Vui lòng thử lại.";
                    }
                    alert(message);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }

        // Modal Controls
        function openAddAddressModal() {
            document.getElementById('add-address-modal').classList.remove('hidden');
            initAddMap();
        }

        function closeAddAddressModal() {
            document.getElementById('add-address-modal').classList.add('hidden');
        }

        function openEditAddressModal(address) {
            document.getElementById('edit-address-form').action = `/profile/address/${address.id}`;
            document.getElementById('edit-recipient-name').value = address.recipient_name || '';
            document.getElementById('edit-phone').value = address.phone || '';
            document.getElementById('edit-address-line').value = address.address_line || '';
            document.getElementById('edit-is-default').checked = Boolean(address.is_default);

            document.getElementById('edit-address-modal').classList.remove('hidden');
            initEditMap();
        }

        function closeEditAddressModal() {
            document.getElementById('edit-address-modal').classList.add('hidden');
        }
    </script>
@endpush
