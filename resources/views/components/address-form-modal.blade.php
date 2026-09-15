{{-- resources/views/components/address-form-modal.blade.php --}}
@props([
    'id' => 'address-form-modal',
])

<div id="{{ $id }}" class="modal-backdrop hidden" style="z-index: 250;">
    <div class="modal-dialog max-w-xl w-full max-h-[92vh] flex flex-col bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
        
        <!-- Header -->
        <div class="px-5 py-4 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-base font-bold text-gray-900" id="address-form-title">Thêm địa chỉ nhận hàng</h3>
                <p class="text-xs text-gray-500 mt-0.5">Thông tin dùng để giao đơn hàng</p>
            </div>
            <button 
                type="button" 
                onclick="window.AddressModalManager?.closeFormModal()"
                class="text-gray-400 hover:text-gray-700 p-1.5 rounded-full hover:bg-gray-100 transition-colors cursor-pointer"
                aria-label="Đóng"
            >
                <x-icon name="close" class="w-4 h-4" />
            </button>
        </div>

        <!-- Form Body -->
        <form id="address-form-inner" class="flex-1 overflow-y-auto p-5 space-y-4" onsubmit="window.AddressModalManager?.handleFormSubmit(event)">
            @csrf
            <input type="hidden" id="addr-mode" value="create">
            <input type="hidden" id="addr-id" value="">
            
            <!-- Row 1: Name & Phone (2 columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="addr-recipient-name" class="block text-xs font-semibold text-gray-700 mb-1">
                        Họ và tên người nhận <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="addr-recipient-name" 
                        name="recipient_name" 
                        required 
                        placeholder="Họ và tên người nhận"
                        class="w-full px-3.5 py-2.5 bg-gray-50/70 hover:bg-gray-50 focus:bg-white border border-gray-200 focus:border-primary rounded-xl text-xs sm:text-sm text-gray-900 transition-colors focus:outline-hidden"
                    >
                    <span class="text-[11px] text-rose-500 hidden mt-1" id="err-addr-name"></span>
                </div>

                <div>
                    <label for="addr-phone" class="block text-xs font-semibold text-gray-700 mb-1">
                        Số điện thoại <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="tel" 
                        id="addr-phone" 
                        name="phone" 
                        required 
                        placeholder="Số điện thoại người nhận"
                        class="w-full px-3.5 py-2.5 bg-gray-50/70 hover:bg-gray-50 focus:bg-white border border-gray-200 focus:border-primary rounded-xl text-xs sm:text-sm text-gray-900 transition-colors focus:outline-hidden"
                    >
                    <span class="text-[11px] text-rose-500 hidden mt-1" id="err-addr-phone"></span>
                </div>
            </div>

            <!-- Row 2: Administrative Division (Province & District) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="addr-province" class="block text-xs font-semibold text-gray-700 mb-1">
                        Tỉnh / Thành phố <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="addr-province" 
                        class="w-full px-3 py-2.5 bg-gray-50/70 hover:bg-gray-50 focus:bg-white border border-gray-200 focus:border-primary rounded-xl text-xs sm:text-sm text-gray-900 transition-colors focus:outline-hidden"
                    >
                        <option value="">-- Chọn Tỉnh / Thành phố --</option>
                        <option value="TP. Hà Nội">TP. Hà Nội</option>
                        <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                        <option value="TP. Đà Nẵng">TP. Đà Nẵng</option>
                        <option value="TP. Hải Phòng">TP. Hải Phòng</option>
                        <option value="TP. Cần Thơ">TP. Cần Thơ</option>
                        <option value="An Giang">An Giang</option>
                        <option value="Bà Rịa - Vũng Tàu">Bà Rịa - Vũng Tàu</option>
                        <option value="Bắc Giang">Bắc Giang</option>
                        <option value="Bắc Kạn">Bắc Kạn</option>
                        <option value="Bạc Liêu">Bạc Liêu</option>
                        <option value="Bắc Ninh">Bắc Ninh</option>
                        <option value="Bến Tre">Bến Tre</option>
                        <option value="Bình Định">Bình Định</option>
                        <option value="Bình Dương">Bình Dương</option>
                        <option value="Bình Phước">Bình Phước</option>
                        <option value="Bình Thuận">Bình Thuận</option>
                        <option value="Cà Mau">Cà Mau</option>
                        <option value="Cao Bằng">Cao Bằng</option>
                        <option value="Đắk Lắk">Đắk Lắk</option>
                        <option value="Đắk Nông">Đắk Nông</option>
                        <option value="Điện Biên">Điện Biên</option>
                        <option value="Đồng Nai">Đồng Nai</option>
                        <option value="Đồng Tháp">Đồng Tháp</option>
                        <option value="Gia Lai">Gia Lai</option>
                        <option value="Hà Giang">Hà Giang</option>
                        <option value="Hà Nam">Hà Nam</option>
                        <option value="Hà Tĩnh">Hà Tĩnh</option>
                        <option value="Hải Dương">Hải Dương</option>
                        <option value="Hậu Giang">Hậu Giang</option>
                        <option value="Hòa Bình">Hòa Bình</option>
                        <option value="Hưng Yên">Hưng Yên</option>
                        <option value="Khánh Hòa">Khánh Hòa</option>
                        <option value="Kiên Giang">Kiên Giang</option>
                        <option value="Kon Tum">Kon Tum</option>
                        <option value="Lai Châu">Lai Châu</option>
                        <option value="Lâm Đồng">Lâm Đồng</option>
                        <option value="Lạng Sơn">Lạng Sơn</option>
                        <option value="Lào Cai">Lào Cai</option>
                        <option value="Long An">Long An</option>
                        <option value="Nam Định">Nam Định</option>
                        <option value="Nghệ An">Nghệ An</option>
                        <option value="Ninh Bình">Ninh Bình</option>
                        <option value="Ninh Thuận">Ninh Thuận</option>
                        <option value="Phú Thọ">Phú Thọ</option>
                        <option value="Phú Yên">Phú Yên</option>
                        <option value="Quảng Bình">Quảng Bình</option>
                        <option value="Quảng Nam">Quảng Nam</option>
                        <option value="Quảng Ngãi">Quảng Ngãi</option>
                        <option value="Quảng Ninh">Quảng Ninh</option>
                        <option value="Quảng Trị">Quảng Trị</option>
                        <option value="Sóc Trăng">Sóc Trăng</option>
                        <option value="Sơn La">Sơn La</option>
                        <option value="Tây Ninh">Tây Ninh</option>
                        <option value="Thái Bình">Thái Bình</option>
                        <option value="Thái Nguyên">Thái Nguyên</option>
                        <option value="Thanh Hóa">Thanh Hóa</option>
                        <option value="Thừa Thiên Huế">Thừa Thiên Huế</option>
                        <option value="Tiền Giang">Tiền Giang</option>
                        <option value="Trà Vinh">Trà Vinh</option>
                        <option value="Tuyên Quang">Tuyên Quang</option>
                        <option value="Vĩnh Long">Vĩnh Long</option>
                        <option value="Vĩnh Phúc">Vĩnh Phúc</option>
                        <option value="Yên Bái">Yên Bái</option>
                    </select>
                </div>

                <div>
                    <label for="addr-district" class="block text-xs font-semibold text-gray-700 mb-1">
                        Quận / Huyện <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="addr-district" 
                        placeholder="Ví dụ: Quận 1, Cầu Giấy..."
                        class="w-full px-3.5 py-2.5 bg-gray-50/70 hover:bg-gray-50 focus:bg-white border border-gray-200 focus:border-primary rounded-xl text-xs sm:text-sm text-gray-900 transition-colors focus:outline-hidden"
                    >
                </div>
            </div>

            <!-- Row 3: Ward / Commune -->
            <div>
                <label for="addr-ward" class="block text-xs font-semibold text-gray-700 mb-1">
                    Phường / Xã
                </label>
                <input 
                    type="text" 
                    id="addr-ward" 
                    placeholder="Ví dụ: Phường Bến Nghé, Xã An Khánh..."
                    class="w-full px-3.5 py-2.5 bg-gray-50/70 hover:bg-gray-50 focus:bg-white border border-gray-200 focus:border-primary rounded-xl text-xs sm:text-sm text-gray-900 transition-colors focus:outline-hidden"
                >
            </div>

            <!-- Row 4: Detailed Address -->
            <div>
                <label for="addr-detail" class="block text-xs font-semibold text-gray-700 mb-1">
                    Địa chỉ cụ thể <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="addr-detail" 
                    required 
                    placeholder="Số nhà, tên đường, tòa nhà, số phòng..."
                    class="w-full px-3.5 py-2.5 bg-gray-50/70 hover:bg-gray-50 focus:bg-white border border-gray-200 focus:border-primary rounded-xl text-xs sm:text-sm text-gray-900 transition-colors focus:outline-hidden"
                >
                <span class="text-[11px] text-rose-500 hidden mt-1" id="err-addr-line"></span>
            </div>

            <!-- Row 5: Address Type & Default Setting (No emojis, SVG icons) -->
            <div class="pt-1 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-4">
                    <span class="text-gray-500 font-medium">Loại địa chỉ:</span>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                        <input type="radio" name="addr_type" value="home" checked class="accent-primary cursor-pointer">
                        <span class="inline-flex items-center gap-1 text-gray-800 font-medium">
                            <x-icon name="home" class="w-3.5 h-3.5 text-gray-500" />
                            Nhà riêng
                        </span>
                    </label>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                        <input type="radio" name="addr_type" value="office" class="accent-primary cursor-pointer">
                        <span class="inline-flex items-center gap-1 text-gray-800 font-medium">
                            <x-icon name="building" class="w-3.5 h-3.5 text-gray-500" />
                            Văn phòng
                        </span>
                    </label>
                </div>

                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        id="addr-is-default" 
                        name="is_default" 
                        value="1" 
                        class="w-4 h-4 rounded text-primary focus:ring-rose-400 border-gray-300 accent-primary cursor-pointer"
                    >
                    <span class="text-gray-800 font-semibold">Đặt làm địa chỉ mặc định</span>
                </label>
            </div>

            <!-- Map / GPS (Secondary, Collapsed by Default) -->
            <div class="pt-2 border-t border-gray-100">
                <div class="flex items-center justify-between gap-2 flex-wrap text-xs">
                    <button 
                        type="button" 
                        id="btn-toggle-map" 
                        onclick="window.AddressModalManager?.toggleMap()"
                        class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-700 font-semibold cursor-pointer"
                    >
                        <x-icon name="map-pin" class="w-3.5 h-3.5 text-primary" />
                        <span id="map-toggle-text">Chọn trên bản đồ</span>
                    </button>

                    <button 
                        type="button" 
                        id="btn-trigger-gps" 
                        onclick="window.AddressModalManager?.triggerGPS()"
                        class="inline-flex items-center gap-1 text-primary hover:text-rose-700 font-semibold cursor-pointer"
                    >
                        <span>Dùng vị trí hiện tại (GPS)</span>
                    </button>
                </div>

                <!-- Collapsible Map Container -->
                <div id="addr-map-wrapper" class="hidden mt-3 space-y-2">
                    <div id="addr-map-container" class="w-full h-44 rounded-xl border border-gray-200 overflow-hidden relative shadow-inner">
                        <div id="addr-map" class="w-full h-full"></div>
                    </div>
                    <p class="text-[11px] text-gray-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span>Nhấp trên bản đồ hoặc kéo ghim để cập nhật địa chỉ.</span>
                    </p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3 shrink-0">
                <button 
                    type="button" 
                    onclick="window.AddressModalManager?.closeFormModal()" 
                    class="px-4 py-2 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-100 text-xs font-semibold transition-colors cursor-pointer"
                >
                    Hủy
                </button>
                <button 
                    type="submit" 
                    id="btn-save-address-submit" 
                    class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5"
                >
                    <span id="btn-save-address-text">Lưu địa chỉ</span>
                </button>
            </div>
        </form>

    </div>
</div>
