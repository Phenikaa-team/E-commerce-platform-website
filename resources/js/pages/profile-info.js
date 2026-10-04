/**
 * Profile Info Page Script
 * Manages Avatar Upload Preview & Delete Account Modal
 */

export function openDeleteAccountModal() {
    document.getElementById('delete-account-modal')?.classList.remove('hidden');
}

export function closeDeleteAccountModal() {
    document.getElementById('delete-account-modal')?.classList.add('hidden');
}

export function initProfileOtp() {
    const radioEmail = document.getElementById('profile-channel-email-label');
    const radioSms = document.getElementById('profile-channel-sms-label');
    const btnSendOtp = document.getElementById('btn-profile-send-otp');
    const alertBox = document.getElementById('profile-otp-alert');
    const otpInput = document.getElementById('profile-otp-input');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let currentChannel = 'email';
    let countdownTimer = null;

    if (!btnSendOtp) return;

    // Toggle styling radio channels
    document.querySelectorAll('input[name="channel"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            currentChannel = e.target.value;
            if (currentChannel === 'email') {
                radioEmail?.classList.add('border-primary', 'bg-rose-50/40');
                radioEmail?.classList.remove('border-gray-200', 'bg-white');
                radioSms?.classList.remove('border-emerald-600', 'bg-emerald-50/40');
                radioSms?.classList.add('border-gray-200', 'bg-white');
            } else {
                radioSms?.classList.add('border-emerald-600', 'bg-emerald-50/40');
                radioSms?.classList.remove('border-gray-200', 'bg-white');
                radioEmail?.classList.remove('border-primary', 'bg-rose-50/40');
                radioEmail?.classList.add('border-gray-200', 'bg-white');
            }
        });
    });

    function showAlert(msg, isSuccess = false) {
        if (!alertBox) return;
        alertBox.className = 'mt-4 p-3.5 rounded-xl text-xs font-semibold flex items-center gap-2.5 ' + 
            (isSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200');
        alertBox.innerHTML = isSuccess 
            ? `<svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>${msg}</span>`
            : `<svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>${msg}</span>`;
        alertBox.classList.remove('hidden');
    }

    function startCountdown(seconds = 15) {
        clearInterval(countdownTimer);
        btnSendOtp.disabled = true;
        let left = seconds;
        btnSendOtp.innerHTML = `<span>Gửi lại sau (${left}s)</span>`;

        countdownTimer = setInterval(() => {
            left--;
            if (left <= 0) {
                clearInterval(countdownTimer);
                btnSendOtp.disabled = false;
                btnSendOtp.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg><span>Gửi lại OTP</span>`;
            } else {
                btnSendOtp.innerHTML = `<span>Gửi lại sau (${left}s)</span>`;
            }
        }, 1000);
    }

    btnSendOtp.addEventListener('click', async () => {
        btnSendOtp.disabled = true;
        btnSendOtp.innerHTML = `<span>Đang gửi mã...</span>`;

        try {
            const res = await fetch('/profile/password/send-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ channel: currentChannel })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                showAlert(data.message || 'Không thể gửi mã OTP.');
                btnSendOtp.disabled = false;
                btnSendOtp.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg><span>Gửi mã OTP</span>`;
                return;
            }

            showAlert(data.message, true);
            startCountdown(45);
            if (otpInput) otpInput.focus();
        } catch (e) {
            showAlert('Lỗi kết nối máy chủ khi gửi OTP.');
            btnSendOtp.disabled = false;
            btnSendOtp.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg><span>Gửi mã OTP</span>`;
        }
    });
}

export function initProfileInfo() {
    window.openDeleteAccountModal = openDeleteAccountModal;
    window.closeDeleteAccountModal = closeDeleteAccountModal;

    initProfileOtp();

    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('user-avatar-preview');
    const btnCancelAvatar = document.getElementById('btn-cancel-avatar');
    const originalAvatarSrc = avatarPreview ? avatarPreview.src : '';

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (file) {
                if (file.size > 3 * 1024 * 1024) {
                    alert('Dung lượng ảnh vượt quá 3MB. Vui lòng chọn ảnh nhỏ hơn.');
                    avatarInput.value = '';
                    return;
                }
                avatarPreview.src = URL.createObjectURL(file);
                if (btnCancelAvatar) btnCancelAvatar.classList.remove('hidden');
            }
        });

        if (btnCancelAvatar) {
            btnCancelAvatar.addEventListener('click', () => {
                avatarInput.value = '';
                avatarPreview.src = originalAvatarSrc;
                btnCancelAvatar.classList.add('hidden');
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initProfileInfo();
});
