/**
 * User Sidebar Component Controller (Password Modal)
 */

export function openPasswordModal() {
    const modal = document.getElementById('change-password-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

export function closePasswordModal() {
    const modal = document.getElementById('change-password-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

export function initUserSidebar() {
    window.openPasswordModal = openPasswordModal;
    window.closePasswordModal = closePasswordModal;

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePasswordModal();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initUserSidebar();
});
