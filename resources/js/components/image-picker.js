/**
 * Image Picker Blade Component Controller
 */

export function initImagePickers() {
    document.querySelectorAll('.image-picker-component').forEach(wrapper => {
        if (wrapper.dataset.initialized) return;
        wrapper.dataset.initialized = 'true';

        const fileInput = wrapper.querySelector('input[type="file"]');
        const img = wrapper.querySelector('img');
        const clearBtn = wrapper.querySelector('button[id^="btn-clear-"]');
        const errorMsg = wrapper.querySelector('p[id^="error-msg-"]');
        const initialSrc = wrapper.getAttribute('data-initial-src');
        const maxSizeMb = parseFloat(wrapper.getAttribute('data-max-size') || '3');

        if (!fileInput || !img) return;

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (errorMsg) errorMsg.classList.add('hidden');

            if (!file) return;

            // Client-side file size check
            if (file.size > maxSizeMb * 1024 * 1024) {
                if (errorMsg) {
                    errorMsg.textContent = `Dung lượng ảnh vượt quá giới hạn ${maxSizeMb}MB. Vui lòng chọn ảnh nhỏ hơn.`;
                    errorMsg.classList.remove('hidden');
                }
                fileInput.value = '';
                return;
            }

            // Client-side MIME check
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'];
            if (!allowedTypes.includes(file.type)) {
                if (errorMsg) {
                    errorMsg.textContent = 'Định dạng tệp không được hỗ trợ. Vui lòng chọn ảnh JPG, PNG, WEBP hoặc GIF.';
                    errorMsg.classList.remove('hidden');
                }
                fileInput.value = '';
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            img.src = objectUrl;
            if (clearBtn) clearBtn.classList.remove('hidden');
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                fileInput.value = '';
                img.src = initialSrc || '';
                clearBtn.classList.add('hidden');
                if (errorMsg) errorMsg.classList.add('hidden');
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initImagePickers();
});
