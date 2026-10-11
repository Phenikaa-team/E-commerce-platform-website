<div class="relative inline-block text-left" id="notification-bell-wrapper-{{ $context }}" data-context="{{ $context }}">
    <button 
        type="button" 
        id="notification-bell-btn-{{ $context }}"
        class="notification-bell-trigger relative p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100/80 rounded-xl transition-colors cursor-pointer focus:outline-none" 
        aria-label="Thông báo"
        aria-expanded="false"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span 
            id="notification-badge-{{ $context }}" 
            class="notification-badge-count absolute top-1 right-1 min-w-[1.05rem] h-[1.05rem] px-1 rounded-full bg-rose-600 text-white text-[10px] font-bold flex items-center justify-center hidden"
        >0</span>
    </button>

    <!-- Notification Dropdown Panel -->
    <div 
        id="notification-dropdown-{{ $context }}"
        class="notification-dropdown-panel absolute right-0 top-full mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 hidden animate-in fade-in slide-in-from-top-1 duration-150 overflow-hidden"
    >
        <div class="p-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-900 uppercase tracking-wider">Thông báo</span>
                <span id="notification-header-count-{{ $context }}" class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">0 mới</span>
            </div>
            <button 
                type="button" 
                id="notification-mark-all-{{ $context }}"
                class="text-[11px] font-bold text-gray-500 hover:text-rose-600 transition-colors cursor-pointer"
            >
                Đánh dấu đã đọc
            </button>
        </div>

        <div id="notification-list-{{ $context }}" class="max-h-96 overflow-y-auto divide-y divide-gray-50">
            <!-- Loading indicator -->
            <div class="p-6 text-center text-xs text-gray-400">
                <svg class="animate-spin h-5 w-5 mx-auto text-rose-600 mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Đang tải thông báo...</span>
            </div>
        </div>

        <div class="p-2.5 border-t border-gray-100 text-center bg-gray-50/30">
            @if($context === 'seller')
                <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-gray-600 hover:text-rose-600 transition-colors">Xem quản lý đơn hàng &rarr;</a>
            @elseif($context === 'admin')
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-gray-600 hover:text-rose-600 transition-colors">Xem toàn bộ đơn hàng sàn &rarr;</a>
            @else
                <a href="{{ route('user.orders') }}" class="text-xs font-bold text-gray-600 hover:text-rose-600 transition-colors">Xem đơn mua của bạn &rarr;</a>
            @endif
        </div>
    </div>
</div>
