@php
    $authUser = auth()->user();
@endphp

@if($authUser && !$authUser->hasCustomPassword())
<div id="third-party-password-alert" class="hidden mb-6 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-50 via-rose-50 to-orange-50 border border-amber-200/80 shadow-xs transition-all duration-300">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Left: Icon & Text -->
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/25 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-black text-gray-900">Bảo vệ tài khoản: Bạn chưa thiết lập mật khẩu riêng</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wide">Khuyến nghị</span>
                </div>
                <p class="text-xs text-gray-600 mt-1 max-w-2xl leading-relaxed">
                    Bạn đang đăng nhập trực tiếp qua <strong>{{ ucfirst($authUser->provider ?? 'Google') }}</strong>. Hãy tạo mật khẩu ngay để có thể đăng nhập bằng email hoặc số điện thoại bất cứ lúc nào và bảo vệ tài khoản an toàn hơn.
                </p>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2 sm:self-center shrink-0">
            <button 
                type="button" 
                onclick="dismissThirdPartyAlert()" 
                class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-500 hover:text-gray-800 hover:bg-black/5 transition-colors cursor-pointer"
            >
                Bỏ qua
            </button>
            <button 
                type="button" 
                onclick="openPasswordModal()" 
                class="btn btn-primary btn-sm px-4 py-2 text-xs font-bold inline-flex items-center gap-1.5 shadow-sm active:scale-95 transition-all"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Tạo mật khẩu ngay</span>
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
        const alertBox = document.getElementById('third-party-password-alert');
        if (!alertBox) return;

        // Check if user dismissed recently (12 hour cooldown for frequent re-prompt)
        const dismissedUntil = localStorage.getItem('dismiss_3rdparty_pwd_alert');
        const now = Date.now();

        if (!dismissedUntil || now > parseInt(dismissedUntil, 10)) {
            alertBox.classList.remove('hidden');
        }
    })();

    function dismissThirdPartyAlert() {
        const alertBox = document.getElementById('third-party-password-alert');
        if (alertBox) {
            alertBox.classList.add('hidden');
            // Re-prompt after 12 hours (thường xuyên cảnh báo lại)
            const twelveHours = 12 * 60 * 60 * 1000;
            localStorage.setItem('dismiss_3rdparty_pwd_alert', (Date.now() + twelveHours).toString());
        }
    }
</script>
@endif
