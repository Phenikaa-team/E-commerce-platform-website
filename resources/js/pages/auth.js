/**
 * Authentication Page Controller (Login & Register Tabs, Password Visibility)
 */

export function switchAuthTab(tab) {
    const loginBtn = document.getElementById('tab-btn-login');
    const registerBtn = document.getElementById('tab-btn-register');
    const loginContent = document.getElementById('tab-content-login');
    const registerContent = document.getElementById('tab-content-register');

    if (!loginBtn || !registerBtn || !loginContent || !registerContent) {
        return;
    }

    if (tab === 'login') {
        loginBtn.classList.add('text-primary', 'active-tab-line');
        loginBtn.classList.remove('text-gray-400');
        registerBtn.classList.remove('text-primary', 'active-tab-line');
        registerBtn.classList.add('text-gray-400');

        loginContent.classList.remove('hidden');
        registerContent.classList.add('hidden');
    } else {
        registerBtn.classList.add('text-primary', 'active-tab-line');
        registerBtn.classList.remove('text-gray-400');
        loginBtn.classList.remove('text-primary', 'active-tab-line');
        loginBtn.classList.add('text-gray-400');

        registerContent.classList.remove('hidden');
        loginContent.classList.add('hidden');
    }
}

export function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>';
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    }
}

export function initAuthPage() {
    // 1. Tab buttons click delegation
    document.addEventListener('click', (e) => {
        const tabBtn = e.target.closest('[data-auth-tab]');
        if (tabBtn) {
            e.preventDefault();
            const tab = tabBtn.getAttribute('data-auth-tab');
            switchAuthTab(tab);
            return;
        }

        const eyeBtn = e.target.closest('[data-toggle-password]');
        if (eyeBtn) {
            e.preventDefault();
            const targetInput = eyeBtn.getAttribute('data-toggle-password');
            const targetIcon = eyeBtn.getAttribute('data-target-icon') || eyeBtn.querySelector('svg')?.id;
            togglePasswordVisibility(targetInput, targetIcon);
        }
    });

    // 2. Expose functions to window for any remaining inline callers
    window.switchTab = switchAuthTab;
    window.togglePasswordVisibility = togglePasswordVisibility;

    // 3. Auto switch tab if requested in URL query (e.g. /login?tab=register)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'register' || window.location.pathname.endsWith('/register')) {
        switchAuthTab('register');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initAuthPage();
});
