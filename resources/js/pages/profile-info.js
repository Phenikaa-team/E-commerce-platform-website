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

export function initBirthdayControls(onDateChange) {
    const dayInput = document.getElementById('birthday-day');
    const monthInput = document.getElementById('birthday-month');
    const yearInput = document.getElementById('birthday-year');
    const dayDisplay = document.getElementById('birthday-day-display');
    const monthDisplay = document.getElementById('birthday-month-display');
    const yearDisplay = document.getElementById('birthday-year-display');
    const nativePicker = document.getElementById('birthday-native-picker');
    const hiddenInput = document.getElementById('birthday-hidden');

    if (!hiddenInput) return;

    let isInternalUpdate = false;

    // Helper: Parse DD/MM/YYYY into components
    function parseDateString(str) {
        if (!str || typeof str !== 'string') return null;
        const parts = str.trim().split('/');
        if (parts.length === 3) {
            const d = parts[0].padStart(2, '0');
            const m = parts[1].padStart(2, '0');
            const y = parts[2];
            if (!isNaN(d) && !isNaN(m) && !isNaN(y) && y.length === 4) {
                return { day: d, month: m, year: y };
            }
        }
        return null;
    }

    // Helper: update dropdown active highlight in list
    function updateOptionActive(menuId, val) {
        const menu = document.getElementById(menuId);
        if (!menu) return;
        menu.querySelectorAll('.profile-birthday-option').forEach(opt => {
            if (opt.dataset.value === (val || '')) {
                opt.classList.add('selected');
            } else {
                opt.classList.remove('selected');
            }
        });
    }

    // Populate selects & displays from string
    function updateControlsFromValue(val) {
        const parsed = parseDateString(val);
        if (parsed) {
            if (dayInput) dayInput.value = parsed.day;
            if (monthInput) monthInput.value = parsed.month;
            if (yearInput) yearInput.value = parsed.year;
            if (dayDisplay) dayDisplay.textContent = parsed.day;
            if (monthDisplay) monthDisplay.textContent = parsed.month;
            if (yearDisplay) yearDisplay.textContent = parsed.year;
            if (nativePicker) nativePicker.value = `${parsed.year}-${parsed.month}-${parsed.day}`;
            updateOptionActive('birthday-day-menu', parsed.day);
            updateOptionActive('birthday-month-menu', parsed.month);
            updateOptionActive('birthday-year-menu', parsed.year);
        } else {
            if (dayInput) dayInput.value = '';
            if (monthInput) monthInput.value = '';
            if (yearInput) yearInput.value = '';
            if (dayDisplay) dayDisplay.textContent = 'DD';
            if (monthDisplay) monthDisplay.textContent = 'MM';
            if (yearDisplay) yearDisplay.textContent = 'YYYY';
            if (nativePicker) nativePicker.value = '';
            updateOptionActive('birthday-day-menu', '');
            updateOptionActive('birthday-month-menu', '');
            updateOptionActive('birthday-year-menu', '');
        }
    }

    // Sync from internal inputs to hidden master input
    function syncFromCustomInputs() {
        if (isInternalUpdate) return;
        const d = dayInput?.value;
        const m = monthInput?.value;
        const y = yearInput?.value;

        if (d && m && y) {
            isInternalUpdate = true;
            hiddenInput.value = `${d}/${m}/${y}`;
            if (nativePicker) nativePicker.value = `${y}-${m}-${d}`;
            isInternalUpdate = false;
        } else if (!d && !m && !y) {
            isInternalUpdate = true;
            hiddenInput.value = '';
            if (nativePicker) nativePicker.value = '';
            isInternalUpdate = false;
        }
        if (onDateChange) onDateChange();
    }

    // Initialize custom dropdown triggers and menus
    const segments = document.querySelectorAll('.profile-birthday-segment[data-dropdown]');

    function closeAllDropdowns() {
        segments.forEach(seg => {
            seg.classList.remove('open');
            const trigger = seg.querySelector('.profile-birthday-trigger');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        });
    }

    segments.forEach(seg => {
        const trigger = seg.querySelector('.profile-birthday-trigger');
        const menu = seg.querySelector('.profile-birthday-menu');
        const input = seg.querySelector('input[type="hidden"]');
        const display = seg.querySelector('.profile-birthday-value');
        const type = seg.dataset.dropdown;

        if (!trigger || !menu) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = seg.classList.contains('open');
            closeAllDropdowns();
            if (!isOpen) {
                seg.classList.add('open');
                trigger.setAttribute('aria-expanded', 'true');
                // Scroll selected item into view
                const selectedOpt = menu.querySelector('.profile-birthday-option.selected');
                if (selectedOpt) {
                    selectedOpt.scrollIntoView({ block: 'nearest' });
                }
            }
        });

        menu.querySelectorAll('.profile-birthday-option').forEach(opt => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const chosenVal = opt.dataset.value;
                if (input) input.value = chosenVal;
                if (display) {
                    display.textContent = chosenVal || (type === 'day' ? 'DD' : type === 'month' ? 'MM' : 'YYYY');
                }
                updateOptionActive(menu.id, chosenVal);
                closeAllDropdowns();
                syncFromCustomInputs();
            });
        });
    });

    // Close on click outside or Esc key
    document.addEventListener('click', () => {
        closeAllDropdowns();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAllDropdowns();
    });

    // Sync from native date picker
    if (nativePicker) {
        nativePicker.addEventListener('change', (e) => {
            const val = e.target.value; // YYYY-MM-DD
            if (val && val.includes('-')) {
                const [y, m, d] = val.split('-');
                isInternalUpdate = true;
                hiddenInput.value = `${d}/${m}/${y}`;
                if (dayInput) dayInput.value = d;
                if (monthInput) monthInput.value = m;
                if (yearInput) yearInput.value = y;
                if (dayDisplay) dayDisplay.textContent = d;
                if (monthDisplay) monthDisplay.textContent = m;
                if (yearDisplay) yearDisplay.textContent = y;
                updateOptionActive('birthday-day-menu', d);
                updateOptionActive('birthday-month-menu', m);
                updateOptionActive('birthday-year-menu', y);
                isInternalUpdate = false;
                if (onDateChange) onDateChange();
            }
        });
    }

    // Sync from manual typing in hidden/direct input
    hiddenInput.addEventListener('input', () => {
        if (isInternalUpdate) return;
        isInternalUpdate = true;
        updateControlsFromValue(hiddenInput.value);
        isInternalUpdate = false;
        if (onDateChange) onDateChange();
    });

    // Initial populate
    updateControlsFromValue(hiddenInput.value);
}

export function initProfileInfo() {
    window.openDeleteAccountModal = openDeleteAccountModal;
    window.closeDeleteAccountModal = closeDeleteAccountModal;

    initProfileOtp();

    const form = document.getElementById('profile-form');
    const btnSubmit = document.getElementById('btn-profile-submit');
    const btnCancel = document.getElementById('btn-profile-cancel');
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('user-avatar-preview');
    const btnCancelAvatar = document.getElementById('btn-cancel-avatar');
    const originalAvatarSrc = avatarPreview ? avatarPreview.src : '';

    if (!form || !btnSubmit || !btnCancel) return;

    // Snapshot initial form values to accurately detect changes
    const getFormSnapshot = () => {
        const formData = new FormData(form);
        const data = {};
        for (const [key, value] of formData.entries()) {
            if (key !== '_token' && key !== 'avatar') {
                data[key] = typeof value === 'string' ? value.trim() : value;
            }
        }
        return data;
    };

    const initialSnapshot = getFormSnapshot();
    let hasAvatarChanged = false;

    // Check if form is dirty
    const checkDirtyState = () => {
        const currentSnapshot = getFormSnapshot();
        let isDirty = hasAvatarChanged;

        if (!isDirty) {
            for (const key of Object.keys(initialSnapshot)) {
                if ((currentSnapshot[key] || '') !== (initialSnapshot[key] || '')) {
                    isDirty = true;
                    break;
                }
            }
            if (!isDirty) {
                for (const key of Object.keys(currentSnapshot)) {
                    if ((currentSnapshot[key] || '') !== (initialSnapshot[key] || '')) {
                        isDirty = true;
                        break;
                    }
                }
            }
        }

        btnSubmit.disabled = !isDirty;
        btnCancel.disabled = !isDirty;
    };

    // Initialize birthday controls with dirty state trigger
    initBirthdayControls(checkDirtyState);

    // Track input/change events across the form
    form.addEventListener('input', checkDirtyState);
    form.addEventListener('change', checkDirtyState);

    // Avatar upload handling
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
                hasAvatarChanged = true;
                if (btnCancelAvatar) btnCancelAvatar.classList.remove('hidden');
                checkDirtyState();
            }
        });

        if (btnCancelAvatar) {
            btnCancelAvatar.addEventListener('click', () => {
                avatarInput.value = '';
                avatarPreview.src = originalAvatarSrc;
                btnCancelAvatar.classList.add('hidden');
                hasAvatarChanged = false;
                checkDirtyState();
            });
        }
    }

    // Cancel Button Click: Revert all changes
    btnCancel.addEventListener('click', () => {
        form.reset();
        hasAvatarChanged = false;

        if (avatarInput) avatarInput.value = '';
        if (avatarPreview) avatarPreview.src = originalAvatarSrc;
        if (btnCancelAvatar) btnCancelAvatar.classList.add('hidden');

        // Re-sync birthday controls with restored value
        const hiddenInput = document.getElementById('birthday-hidden');
        if (hiddenInput) {
            hiddenInput.value = initialSnapshot['birthday'] || '';
            const daySelect = document.getElementById('birthday-day');
            const monthSelect = document.getElementById('birthday-month');
            const yearSelect = document.getElementById('birthday-year');
            const nativePicker = document.getElementById('birthday-native-picker');

            const parts = (hiddenInput.value || '').trim().split('/');
            if (parts.length === 3) {
                if (daySelect) daySelect.value = parts[0].padStart(2, '0');
                if (monthSelect) monthSelect.value = parts[1].padStart(2, '0');
                if (yearSelect) yearSelect.value = parts[2];
                if (nativePicker) nativePicker.value = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
            } else {
                if (daySelect) daySelect.value = '';
                if (monthSelect) monthSelect.value = '';
                if (yearSelect) yearSelect.value = '';
                if (nativePicker) nativePicker.value = '';
            }
        }

        checkDirtyState();
    });

    // Run dirty state check on initial render
    checkDirtyState();
}

document.addEventListener('DOMContentLoaded', () => {
    initProfileInfo();
});

