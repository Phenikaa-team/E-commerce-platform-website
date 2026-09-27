/**
 * Third Party Password Alert Component Controller
 */

export function dismissThirdPartyAlert() {
    const alertBox = document.getElementById('third-party-password-alert');
    if (alertBox) {
        alertBox.classList.add('hidden');
        const twelveHours = 12 * 60 * 60 * 1000;
        localStorage.setItem('dismiss_3rdparty_pwd_alert', (Date.now() + twelveHours).toString());
    }
}

export function initThirdPartyPasswordAlert() {
    window.dismissThirdPartyAlert = dismissThirdPartyAlert;

    const alertBox = document.getElementById('third-party-password-alert');
    if (!alertBox) return;

    const dismissedUntil = localStorage.getItem('dismiss_3rdparty_pwd_alert');
    const now = Date.now();

    if (!dismissedUntil || now > parseInt(dismissedUntil, 10)) {
        alertBox.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initThirdPartyPasswordAlert();
});
