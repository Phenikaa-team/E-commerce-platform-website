<!DOCTYPE html>
<html lang="vi" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đặt lại mật khẩu qua OTP - ShopMart</title>
    <meta name="description" content="Khôi phục và đặt lại mật khẩu tài khoản ShopMart nhanh chóng, an toàn bằng mã xác thực OTP qua Email hoặc Số điện thoại.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="forgot-page text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-rose-500 selection:text-white relative overflow-x-hidden">

    <!-- Ambient / Concept Background Elements (Exact slant upward with flat horizontal cut) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-[#f4f7fb]/80">
        <!-- LEFT SIDE: 3 parallel upward-slanted stripes with flat top & bottom edges -->
        <div class="bg-slant bg-slant-left-top"></div>
        <div class="bg-slant bg-slant-left-mid"></div>
        <div class="bg-slant bg-slant-left-bot"></div>

        <!-- RIGHT SIDE: 2 parallel upward-slanted stripes with flat top & bottom edges -->
        <div class="bg-slant bg-slant-right-top"></div>
        <div class="bg-slant bg-slant-right-bot"></div>
    </div>

    <!-- Header matching concept -->
    <header class="relative z-10 w-full bg-white/95 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 sm:px-12 py-3.5 flex items-center justify-between">
            <!-- Logo matching image -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-decoration-none group">
                <div class="w-8.5 h-8.5 rounded-lg bg-[#ea2840] flex items-center justify-center text-white shadow-2xs">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="text-xl font-black tracking-tight text-gray-900">
                    Shop<span class="text-[#ea2840]">Mart</span>
                </span>
            </a>

            <!-- Back to login link -->
            <a href="{{ route('login') }}" class="text-[13px] font-medium text-gray-500 hover:text-gray-800 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Quay lại Đăng nhập</span>
            </a>
        </div>
    </header>

    <!-- Main Container matching concept card layout -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 relative z-10">
        <!-- Exact white box: rounded-xl (~10px), crisp subtle border and shadow -->
        <div class="forgot-card">
            <!-- Top orange/red gradient line across the entire top edge -->
            <div class="forgot-gradient-line"></div>

            <!-- Step badge & titles -->
            <div class="mb-6">
                <div class="inline-flex items-center mb-2.5">
                    <span id="step-badge" class="forgot-step-badge">
                        BƯỚC 1 / 3
                    </span>
                </div>
                <h1 id="step-title" class="text-[25px] font-extrabold text-[#111827] tracking-tight leading-tight">
                    Quên Mật Khẩu?
                </h1>
                <p id="step-desc" class="text-[12.5px] text-gray-400 mt-1 leading-relaxed font-normal">
                    Nhập email hoặc số điện thoại của tài khoản để nhận mã xác thực OTP bảo mật.
                </p>
            </div>

            <!-- Global Toast Alert in Card (compact, elegant) -->
            <div id="forgot-alert" class="hidden mb-3.5 py-2 px-3 rounded-lg text-[11.5px] font-semibold flex items-center gap-2"></div>

            <!-- ==================== STEP 1: ENTER ACCOUNT & CHOOSE CHANNEL ==================== -->
            <div id="step-1" class="space-y-3.5">
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">
                        Email, Số điện thoại hoặc Tên tài khoản <span class="text-[#f43f5e]">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <!-- User outline icon matching screenshot -->
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="forgot-account" 
                            placeholder="Nhập email, SĐT hoặc tên đăng nhập" 
                            class="forgot-input forgot-input-icon-wrapper"
                        >
                    </div>
                </div>

                <!-- Simple subtle notice replacing large channel card -->
                <div class="flex items-center gap-2 px-1 text-[12px] text-gray-500 font-normal">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Mã OTP bảo mật sẽ được gửi miễn phí qua Email của tài khoản.</span>
                </div>

                <div class="pt-1.5">
                    <button 
                        type="button" 
                        id="btn-send-otp" 
                        class="forgot-btn-submit"
                    >
                        <span>Gửi Mã OTP Xác Thực</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- ==================== STEP 2: VERIFY OTP ==================== -->
            <div id="step-2" class="space-y-4 hidden">
                <div class="py-1">
                    <label class="block text-[12px] font-bold text-gray-700 mb-2.5 text-center">
                        Nhập mã xác thực OTP 6 số
                    </label>
                    <!-- 6 Separate OTP Boxes (evenly spaced, clean) -->
                    <div class="flex justify-center items-center gap-2 sm:gap-2.5 transition-all duration-300" id="otp-inputs-container">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit-box w-11 h-13 sm:w-11.5 sm:h-13.5 text-center text-xl font-black rounded-lg border border-gray-200 bg-white focus:bg-white focus:border-[#ea2840] focus:ring-2 focus:ring-rose-500/20 focus:outline-none transition-all shadow-2xs" data-index="0" autofocus>
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit-box w-11 h-13 sm:w-11.5 sm:h-13.5 text-center text-xl font-black rounded-lg border border-gray-200 bg-white focus:bg-white focus:border-[#ea2840] focus:ring-2 focus:ring-rose-500/20 focus:outline-none transition-all shadow-2xs" data-index="1">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit-box w-11 h-13 sm:w-11.5 sm:h-13.5 text-center text-xl font-black rounded-lg border border-gray-200 bg-white focus:bg-white focus:border-[#ea2840] focus:ring-2 focus:ring-rose-500/20 focus:outline-none transition-all shadow-2xs" data-index="2">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit-box w-11 h-13 sm:w-11.5 sm:h-13.5 text-center text-xl font-black rounded-lg border border-gray-200 bg-white focus:bg-white focus:border-[#ea2840] focus:ring-2 focus:ring-rose-500/20 focus:outline-none transition-all shadow-2xs" data-index="3">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit-box w-11 h-13 sm:w-11.5 sm:h-13.5 text-center text-xl font-black rounded-lg border border-gray-200 bg-white focus:bg-white focus:border-[#ea2840] focus:ring-2 focus:ring-rose-500/20 focus:outline-none transition-all shadow-2xs" data-index="4">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit-box w-11 h-13 sm:w-11.5 sm:h-13.5 text-center text-xl font-black rounded-lg border border-gray-200 bg-white focus:bg-white focus:border-[#ea2840] focus:ring-2 focus:ring-rose-500/20 focus:outline-none transition-all shadow-2xs" data-index="5">
                    </div>
                    <input type="hidden" id="forgot-otp-code">
                </div>

                <div class="flex items-center justify-between text-[11.5px] text-gray-500 pt-0.5">
                    <span>Không nhận được mã?</span>
                    <button type="button" id="btn-resend-otp" class="font-bold text-[#ea2840] hover:underline cursor-pointer disabled:text-gray-400 disabled:no-underline">
                        Gửi lại mã (<span id="resend-countdown">45</span>s)
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <button 
                        type="button" 
                        id="btn-back-step-1" 
                        class="py-2.5 px-4 rounded-md border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold transition-all cursor-pointer"
                    >
                        Đổi thông tin
                    </button>
                    <button 
                        type="button" 
                        id="btn-verify-otp" 
                        class="py-2.5 px-4 rounded-md bg-[#e61e38] hover:bg-[#d6162f] text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <span>Xác Nhận OTP &rarr;</span>
                    </button>
                </div>
            </div>

            <!-- ==================== STEP 3: RESET NEW PASSWORD ==================== -->
            <div id="step-3" class="space-y-3.5 hidden">
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1">
                        Mật khẩu mới <span class="text-[#f43f5e]">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="new-password" 
                        placeholder="Tối thiểu 6 ký tự" 
                        class="forgot-input"
                    >
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1">
                        Xác nhận mật khẩu mới <span class="text-[#f43f5e]">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="new-password-confirmation" 
                        placeholder="Nhập lại mật khẩu mới" 
                        class="forgot-input"
                    >
                </div>

                <div class="pt-1.5">
                    <button 
                        type="button" 
                        id="btn-submit-reset" 
                        class="forgot-btn-submit"
                    >
                        <span>Hoàn Tất & Đổi Mật Khẩu</span>
                    </button>
                </div>
            </div>

            <!-- Bottom Note matching concept footer text -->
            <div class="mt-6 pt-3.5 text-center text-[11.5px] text-gray-400 border-t border-gray-100">
                <span class="inline-flex items-center gap-1.5 text-gray-400">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Gặp khó khăn? Liên hệ tổng đài hỗ trợ <strong class="text-gray-600 font-bold">1900 8888</strong> (Hỗ trợ 24/7)
                </span>
            </div>
        </div>
    </main>

    <!-- Script for Forgot Password Flow -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let currentTarget = '';
            let currentChannel = 'email';
            let verifiedToken = '';
            let countdownTimer = null;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Elements
            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');
            const step3 = document.getElementById('step-3');

            const stepBadge = document.getElementById('step-badge');
            const stepTitle = document.getElementById('step-title');
            const stepDesc = document.getElementById('step-desc');
            const alertBox = document.getElementById('forgot-alert');

            const btnSendOtp = document.getElementById('btn-send-otp');
            const btnVerifyOtp = document.getElementById('btn-verify-otp');
            const btnResendOtp = document.getElementById('btn-resend-otp');
            const btnBackStep1 = document.getElementById('btn-back-step-1');
            const btnSubmitReset = document.getElementById('btn-submit-reset');

            const radioEmail = document.getElementById('label-channel-email');
            const radioSms = document.getElementById('label-channel-sms');

            // Toggle radio channels styling
            document.querySelectorAll('input[name="forgot_channel"]').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    currentChannel = e.target.value;
                    if (currentChannel === 'email') {
                        radioEmail.classList.add('border-primary', 'bg-rose-50/40');
                        radioEmail.classList.remove('border-gray-200', 'bg-white');
                        radioSms.classList.remove('border-emerald-600', 'bg-emerald-50/40');
                        radioSms.classList.add('border-gray-200', 'bg-white');
                    } else {
                        radioSms.classList.add('border-emerald-600', 'bg-emerald-50/40');
                        radioSms.classList.remove('border-gray-200', 'bg-white');
                        radioEmail.classList.remove('border-primary', 'bg-rose-50/40');
                        radioEmail.classList.add('border-gray-200', 'bg-white');
                    }
                });
            });

            function showAlert(msg, type = 'error') {
                alertBox.classList.remove('hidden', 'bg-rose-50', 'text-rose-800', 'border-rose-200', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200', 'border');
                if (type === 'success') {
                    alertBox.classList.add('bg-emerald-50/90', 'text-emerald-800', 'border', 'border-emerald-200/80');
                    alertBox.innerHTML = `<svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><div class="leading-tight">${msg}</div>`;
                } else {
                    alertBox.classList.add('bg-rose-50/90', 'text-rose-800', 'border', 'border-rose-200/80');
                    alertBox.innerHTML = `<svg class="w-3.5 h-3.5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div class="leading-tight">${msg}</div>`;
                }
            }

            function startCountdown(seconds = 15) {
                clearInterval(countdownTimer);
                if (btnResendOtp) btnResendOtp.disabled = true;
                const countdownSpan = document.getElementById('resend-countdown');
                let left = seconds;
                if (countdownSpan) {
                    countdownSpan.innerText = left;
                } else if (btnResendOtp) {
                    btnResendOtp.innerText = `Gửi lại mã (${left}s)`;
                }

                countdownTimer = setInterval(() => {
                    left--;
                    if (countdownSpan) {
                        countdownSpan.innerText = left;
                    } else if (btnResendOtp) {
                        btnResendOtp.innerText = `Gửi lại mã (${left}s)`;
                    }
                    if (left <= 0) {
                        clearInterval(countdownTimer);
                        if (btnResendOtp) {
                            btnResendOtp.disabled = false;
                            btnResendOtp.innerHTML = 'Gửi lại mã (<span id="resend-countdown">15</span>s)';
                        }
                    }
                }, 1000);
            }

            // Step 1 & Resend: Send OTP
            async function handleSendOtp(isResend = false) {
                const account = document.getElementById('forgot-account')?.value.trim();
                if (!account) {
                    showAlert('Vui lòng nhập Email, Số điện thoại hoặc Tên đăng nhập của tài khoản.');
                    return;
                }

                if (isResend) {
                    if (btnResendOtp) {
                        btnResendOtp.disabled = true;
                        btnResendOtp.innerText = 'Đang gửi lại...';
                    }
                } else {
                    if (btnSendOtp) {
                        btnSendOtp.disabled = true;
                        btnSendOtp.innerText = 'Đang gửi mã...';
                    }
                }
                alertBox.classList.add('hidden');

                try {
                    const res = await fetch("{{ route('password.forgot.send-otp') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ account, channel: 'email' })
                    });
                    
                    let data = {};
                    try {
                        data = await res.json();
                    } catch (parseErr) {
                        data = { message: 'Lỗi phản hồi máy chủ (' + res.status + ')' };
                    }

                    if (!res.ok || !data.success) {
                        showAlert(data.message || 'Không thể gửi mã OTP (mã lỗi ' + res.status + ').');
                        if (isResend && btnResendOtp) {
                            btnResendOtp.disabled = false;
                            btnResendOtp.innerHTML = 'Gửi lại mã (<span id="resend-countdown">15</span>s)';
                        }
                        return;
                    }

                    currentTarget = data.target;

                    // Switch to Step 2 if not already in Step 2
                    step1?.classList.add('hidden');
                    step2?.classList.remove('hidden');
                    if (stepBadge) stepBadge.innerText = 'Bước 2 / 3';
                    if (stepTitle) stepTitle.innerText = 'Nhập Mã OTP';
                    if (stepDesc) stepDesc.innerText = 'Mã xác thực gồm 6 chữ số vừa được gửi đến email tài khoản của bạn.';

                    showAlert(data.message, 'success');
                    startCountdown(15);

                    // Reset and focus first OTP box
                    setTimeout(() => {
                        otpBoxes.forEach(b => b.value = '');
                        otpBoxes[0]?.focus();
                    }, 100);
                } catch (e) {
                    console.error('Send OTP error:', e);
                    showAlert('Lỗi: ' + (e.message || 'Vui lòng thử lại sau.'));
                } finally {
                    if (btnSendOtp) {
                        btnSendOtp.disabled = false;
                        btnSendOtp.innerText = 'Gửi Mã OTP Xác Thực';
                    }
                }
            }

            btnSendOtp?.addEventListener('click', () => handleSendOtp(false));
            btnResendOtp?.addEventListener('click', () => handleSendOtp(true));

            // ==================== OTP 6-BOX INTERACTION LOGIC ====================
            const otpContainer = document.getElementById('otp-inputs-container');
            const otpBoxes = Array.from(document.querySelectorAll('.otp-digit-box'));
            const hiddenOtpInput = document.getElementById('forgot-otp-code');
            let otpFailedAttempts = 0;
            const MAX_OTP_ATTEMPTS = 5;

            function syncHiddenOtp() {
                const fullCode = otpBoxes.map(b => b.value).join('');
                hiddenOtpInput.value = fullCode;
                return fullCode;
            }

            function resetOtpBoxes() {
                otpBoxes.forEach(b => {
                    b.value = '';
                    b.disabled = false;
                    b.classList.remove('border-rose-500', 'bg-rose-50/50', 'text-rose-600', 'border-emerald-500', 'bg-emerald-50', 'text-emerald-700', 'scale-95', 'opacity-60');
                    b.classList.add('border-gray-200', 'bg-white', 'text-gray-900');
                    b.style.transform = '';
                    b.style.margin = '';
                    b.style.borderRadius = '';
                });
                otpContainer.classList.remove('animate-shake', 'animate-lock');
            }

            otpBoxes.forEach((box, idx) => {
                box.addEventListener('input', (e) => {
                    const val = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = val ? val[val.length - 1] : '';

                    const fullCode = syncHiddenOtp();
                    if (val && idx < otpBoxes.length - 1) {
                        otpBoxes[idx + 1].focus();
                    }

                    // Auto-trigger verification when 6 digits are typed
                    if (fullCode.length === 6) {
                        handleVerifyOtp();
                    }
                });

                box.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !box.value && idx > 0) {
                        otpBoxes[idx - 1].focus();
                    }
                });

                box.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                    if (pasted) {
                        const digits = pasted.slice(0, 6).split('');
                        digits.forEach((d, i) => {
                            if (otpBoxes[i]) otpBoxes[i].value = d;
                        });
                        const nextIdx = Math.min(digits.length, otpBoxes.length - 1);
                        otpBoxes[nextIdx]?.focus();
                        const full = syncHiddenOtp();
                        if (full.length === 6) {
                            handleVerifyOtp();
                        }
                    }
                });
            });

            // Step 2: Back to Step 1
            btnBackStep1.addEventListener('click', () => {
                clearInterval(countdownTimer);
                otpFailedAttempts = 0;
                resetOtpBoxes();
                hiddenOtpInput.value = '';
                step2.classList.add('hidden');
                step1.classList.remove('hidden');
                stepBadge.innerText = 'Bước 1 / 3';
                stepTitle.innerText = 'Quên Mật Khẩu?';
                stepDesc.innerText = 'Nhập email hoặc số điện thoại của tài khoản để nhận mã xác thực OTP bảo mật.';
                alertBox.classList.add('hidden');
                document.getElementById('forgot-account')?.focus();
            });

            // Step 2: Verify OTP Function with animations
            async function handleVerifyOtp() {
                if (btnVerifyOtp.disabled) return;

                if (otpFailedAttempts >= MAX_OTP_ATTEMPTS) {
                    showAlert('Bạn đã nhập sai quá số lần quy định. Vui lòng gửi lại mã OTP mới để tiếp tục.');
                    otpContainer.classList.add('animate-shake');
                    setTimeout(() => otpContainer.classList.remove('animate-shake'), 600);
                    return;
                }

                const otpCode = syncHiddenOtp().trim();
                if (otpCode.length !== 6) {
                    showAlert('Vui lòng nhập đủ 6 chữ số mã OTP.');
                    const emptyIdx = otpBoxes.findIndex(b => !b.value);
                    if (emptyIdx !== -1) otpBoxes[emptyIdx].focus();
                    return;
                }

                // 1. Loading / Checking Animation: disable inputs, subtle pulse
                btnVerifyOtp.disabled = true;
                btnVerifyOtp.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>Đang xác thực...`;
                
                otpBoxes.forEach(b => {
                    b.disabled = true;
                    b.classList.add('opacity-70', 'scale-98');
                });

                try {
                    const res = await fetch("{{ route('password.forgot.verify-otp') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ target: currentTarget, otp_code: otpCode })
                    });
                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        otpFailedAttempts++;
                        const remaining = MAX_OTP_ATTEMPTS - otpFailedAttempts;

                        // 2. ERROR ANIMATION: Turn all nodes red and SHAKE EACH NODE WITH RANDOM FREQUENCIES
                        const shakeClasses = ['node-shake-0', 'node-shake-1', 'node-shake-2', 'node-shake-3', 'node-shake-4', 'node-shake-5'];
                        otpBoxes.forEach((b, i) => {
                            b.disabled = false;
                            b.classList.remove('opacity-70', 'scale-98', 'border-gray-200', 'bg-white', 'text-gray-900', ...shakeClasses);
                            b.classList.add('border-rose-500', 'bg-rose-50/70', 'text-rose-600', `node-shake-${i}`);
                        });

                        if (remaining <= 0) {
                            showAlert('Bạn đã nhập sai 5 lần! Mã OTP này đã bị khóa vì bảo mật. Vui lòng bấm "Gửi lại mã".');
                            otpBoxes.forEach(b => {
                                b.disabled = true;
                                b.classList.add('opacity-40', 'cursor-not-allowed');
                            });
                            btnVerifyOtp.disabled = true;
                            btnVerifyOtp.innerText = 'Đã khóa lượt nhập';
                            return;
                        }

                        showAlert((data.message || 'Mã OTP không chính xác.') + ` (Còn ${remaining} lần thử)`);

                        // After shaking, smoothly clear, remove node-shake classes, and refocus index 0
                        setTimeout(() => {
                            otpBoxes.forEach((b, i) => {
                                b.value = '';
                                b.classList.remove('border-rose-500', 'bg-rose-50/70', 'text-rose-600', `node-shake-${i}`);
                                b.classList.add('border-gray-200', 'bg-white', 'text-gray-900');
                            });
                            hiddenOtpInput.value = '';
                            otpBoxes[0]?.focus();
                            btnVerifyOtp.disabled = false;
                            btnVerifyOtp.innerText = 'Xác Nhận OTP \u2192';
                        }, 600);

                        return;
                    }

                    // 3. SUCCESS ANIMATION: Nodes turn green and smoothly merge together
                    verifiedToken = data.reset_token;
                    clearInterval(countdownTimer);

                    otpBoxes.forEach((b, i) => {
                        b.classList.remove('border-gray-200', 'bg-white', 'opacity-70', 'scale-98');
                        b.classList.add('border-emerald-500', 'bg-emerald-500', 'text-white', 'shadow-md');
                        // Collapse gaps between boxes so they visually merge into a unified pill
                        if (i > 0) b.style.marginLeft = '-8px';
                        b.style.borderRadius = i === 0 ? '8px 0 0 8px' : (i === 5 ? '0 8px 8px 0' : '0');
                        b.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
                    });

                    btnVerifyOtp.className = 'py-2.5 px-4 rounded-md bg-emerald-600 text-white text-xs font-bold transition-all flex items-center justify-center gap-1.5 animate-pop-success';
                    btnVerifyOtp.innerHTML = `<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Xác thực thành công!`;

                    showAlert('Xác thực OTP thành công!', 'success');

                    // Smooth transition to Step 3 after completion animation
                    setTimeout(() => {
                        step2.classList.add('hidden');
                        step3.classList.remove('hidden');
                        stepBadge.innerText = 'Bước 3 / 3';
                        stepTitle.innerText = 'Đặt Mật Khẩu Mới';
                        stepDesc.innerText = 'Nhập mật khẩu mới an toàn và xác nhận để hoàn tất khôi phục tài khoản.';
                        document.getElementById('new-password')?.focus();
                    }, 800);

                } catch (e) {
                    console.error('Verify error:', e);
                    showAlert('Lỗi xác thực đường truyền. Vui lòng thử lại.');
                    resetOtpBoxes();
                    btnVerifyOtp.disabled = false;
                    btnVerifyOtp.innerText = 'Xác Nhận OTP \u2192';
                }
            }

            btnVerifyOtp.addEventListener('click', handleVerifyOtp);

            // Step 3: Submit Reset Password
            btnSubmitReset.addEventListener('click', async () => {
                const password = document.getElementById('new-password').value;
                const passwordConfirmation = document.getElementById('new-password-confirmation').value;

                if (!password || password.length < 6) {
                    showAlert('Mật khẩu mới phải có tối thiểu 6 ký tự.');
                    return;
                }

                if (password !== passwordConfirmation) {
                    showAlert('Xác nhận mật khẩu mới không khớp.');
                    return;
                }

                btnSubmitReset.disabled = true;
                btnSubmitReset.innerText = 'Đang cập nhật mật khẩu...';

                try {
                    const res = await fetch("{{ route('password.forgot.reset') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            reset_token: verifiedToken,
                            password: password,
                            password_confirmation: passwordConfirmation
                        })
                    });
                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        showAlert(data.message || 'Không thể đặt lại mật khẩu.');
                        btnSubmitReset.disabled = false;
                        btnSubmitReset.innerText = 'Hoàn Tất & Đổi Mật Khẩu';
                        return;
                    }

                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || "{{ route('login') }}";
                    }, 1500);
                } catch (e) {
                    showAlert('Lỗi xử lý yêu cầu. Vui lòng thử lại.');
                    btnSubmitReset.disabled = false;
                    btnSubmitReset.innerText = 'Hoàn Tất & Đổi Mật Khẩu';
                }
            });
        });
    </script>
</body>
</html>
