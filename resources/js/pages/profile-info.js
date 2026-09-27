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

export function initProfileInfo() {
    window.openDeleteAccountModal = openDeleteAccountModal;
    window.closeDeleteAccountModal = closeDeleteAccountModal;

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
