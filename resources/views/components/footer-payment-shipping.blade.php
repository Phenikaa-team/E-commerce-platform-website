<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    <div>
        <h4 class="font-bold text-gray-900 text-sm mb-2">Thanh toán & Vận chuyển</h4>
        <p class="mb-2.5 text-[11px] text-gray-400 leading-relaxed">Hỗ trợ các phương thức thanh toán an toàn hàng đầu.</p>
        
        <!-- Thanh toán (Payments) -->
        <div class="grid grid-cols-3 gap-1.5 mb-3">
            <!-- VISA -->
            <div class="footer-partner-badge is-visa group" title="Thanh toán thẻ quốc tế VISA">
                <svg class="h-3 w-auto" viewBox="0 0 48 16" fill="none">
                    <path d="M19.4 0.8L12.7 15.2H8.3L5.1 3.5C4.9 2.5 4.7 2.1 3.9 1.7C2.6 1 1.2 0.5 0 0.2L0.1 0.8H6.9C7.8 0.8 8.6 1.4 8.8 2.5L10.5 11.2L14.7 0.8H19.4ZM36.5 10.3C36.5 6.4 31 6.2 31.1 4.4C31.1 3.9 31.6 3.3 32.8 3.1C33.4 3 35.1 3 36.9 3.8L37.6 1C36.6 0.6 35.4 0.3 33.9 0.3C29.8 0.3 26.9 2.5 26.9 5.6C26.9 7.9 29 9.2 30.6 10C32.2 10.8 32.8 11.3 32.8 12C32.8 13.1 31.4 13.6 30.2 13.6C28.1 13.6 26.9 13.2 25.8 12.7L25.1 15.6C26.3 16.1 28.5 16.5 30.7 16.5C35.1 16.5 38 14.3 36.5 10.3ZM47.3 15.2H50.8L47.7 0.8H44.4C43.6 0.8 42.9 1.3 42.6 2L36.3 15.2H40.7L41.6 12.8H46.9L47.3 15.2ZM42.8 9.8L45 3.8L46.2 9.8H42.8ZM25.7 0.8L22.3 15.2H18.2L21.6 0.8H25.7Z" fill="#1434CB"/>
                </svg>
                <span class="text-[10px] font-bold text-gray-700">Visa</span>
            </div>

            <!-- MasterCard -->
            <div class="footer-partner-badge is-mastercard group" title="Thanh toán thẻ quốc tế MasterCard">
                <svg class="h-3.5 w-auto shrink-0" viewBox="0 0 32 20" fill="none">
                    <circle cx="10" cy="10" r="8.5" fill="#EB001B"/>
                    <circle cx="22" cy="10" r="8.5" fill="#F79E1B"/>
                    <path d="M16 4.2A8.5 8.5 0 0 1 19.2 10 8.5 8.5 0 0 1 16 15.8 8.5 8.5 0 0 1 12.8 10 8.5 8.5 0 0 1 16 4.2Z" fill="#FF5F00"/>
                </svg>
                <span class="text-[9px] font-bold text-gray-700">Master</span>
            </div>

            <!-- MoMo -->
            <div class="footer-partner-badge is-momo group" title="Ví điện tử MoMo">
                <div class="w-3.5 h-3.5 rounded partner-logo-momo flex items-center justify-center text-[7px] font-black shrink-0">M</div>
                <span class="text-[10px] font-extrabold partner-text-momo">MoMo</span>
            </div>

            <!-- ZaloPay -->
            <div class="footer-partner-badge is-zalopay group" title="Ví điện tử ZaloPay">
                <div class="w-3.5 h-3.5 rounded partner-logo-zalopay flex items-center justify-center text-[7px] font-black shrink-0">Z</div>
                <span class="text-[10px] font-extrabold partner-text-zalopay">Zalo<span class="partner-text-zalopay-green">Pay</span></span>
            </div>

            <!-- VNPAY -->
            <div class="footer-partner-badge is-vnpay group" title="Cổng VNPAY-QR">
                <div class="w-3.5 h-3.5 rounded partner-logo-vnpay flex items-center justify-center text-[7px] font-black shrink-0">V</div>
                <span class="text-[10px] font-extrabold partner-text-vnpay">VN<span class="partner-text-vnpay-red">PAY</span></span>
            </div>

            <!-- COD -->
            <div class="footer-partner-badge hover:border-emerald-400 group" title="Thanh toán khi nhận hàng (COD)">
                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-[10px] font-bold text-emerald-700">COD</span>
            </div>
        </div>

        <!-- Vận chuyển (Shipping Carriers) -->
        <p class="mb-2 text-[11px] text-gray-400 leading-relaxed">Đơn vị vận chuyển uy tín:</p>
        <div class="grid grid-cols-3 gap-1.5">
            <!-- SPX Express -->
            <div class="footer-partner-badge is-spx group" title="SPX Express">
                <div class="w-3.5 h-3.5 rounded partner-logo-spx flex items-center justify-center text-[7px] font-black italic shrink-0">S</div>
                <span class="text-[10px] font-black partner-text-spx">SPX</span>
            </div>

            <!-- GHN -->
            <div class="footer-partner-badge is-ghn group" title="Giao Hàng Nhanh (GHN)">
                <div class="w-3.5 h-3.5 rounded partner-logo-ghn flex items-center justify-center text-[7px] font-black shrink-0">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-[10px] font-black partner-text-ghn">GHN</span>
            </div>

            <!-- GHTK -->
            <div class="footer-partner-badge is-ghtk group" title="Giao Hàng Tiết Kiệm (GHTK)">
                <div class="w-3.5 h-3.5 rounded partner-logo-ghtk flex items-center justify-center text-[7px] font-black shrink-0">G</div>
                <span class="text-[10px] font-black partner-text-ghtk">GHTK</span>
            </div>

            <!-- Viettel Post -->
            <div class="footer-partner-badge is-viettel group" title="Viettel Post">
                <div class="w-3.5 h-3.5 rounded-full partner-logo-viettel flex items-center justify-center text-[7px] font-black shrink-0">V</div>
                <span class="text-[10px] font-black partner-text-viettel">Viettel</span>
            </div>

            <!-- J&T Express -->
            <div class="footer-partner-badge is-jnt group" title="J&T Express">
                <div class="w-3.5 h-3.5 rounded partner-logo-jnt flex items-center justify-center text-[7px] font-black shrink-0">J</div>
                <span class="text-[10px] font-black partner-text-jnt">J&T</span>
            </div>

            <!-- Hỏa Tốc 2H -->
            <div class="footer-partner-badge hover:border-amber-400 group" title="Giao hàng hỏa tốc trong 2 giờ">
                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[9px] font-bold text-gray-700">Hỏa tốc</span>
            </div>
        </div>
    </div>
</div>
