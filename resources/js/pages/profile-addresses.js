/**
 * Profile Addresses Page Script
 */

export function initProfileAddresses() {
    if (window.AddressModalManager) {
        window.AddressModalManager.init('profile');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initProfileAddresses();
});
