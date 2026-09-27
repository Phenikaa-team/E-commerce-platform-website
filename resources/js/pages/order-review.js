/**
 * Order Review Modal & Image Upload Controller
 */

export function initOrderReview() {
    const reviewModal = document.getElementById('review-modal');
    const closeBtn = document.getElementById('btn-close-review-modal');
    const prodIdInput = document.getElementById('review-product-id');
    const prodNameDisplay = document.getElementById('review-product-name-display');

    document.querySelectorAll('.btn-open-review-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (prodIdInput) prodIdInput.value = btn.getAttribute('data-product-id');
            if (prodNameDisplay) prodNameDisplay.textContent = 'Sản phẩm: ' + (btn.getAttribute('data-product-name') || '');
            if (reviewModal) reviewModal.classList.remove('hidden');
        });
    });

    if (closeBtn && reviewModal) {
        closeBtn.addEventListener('click', () => reviewModal.classList.add('hidden'));
    }

    // Star selector
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('review-rating-input');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const r = parseInt(star.getAttribute('data-rating') || '5', 10);
            if (ratingInput) ratingInput.value = r;

            stars.forEach((s, idx) => {
                if (idx < r) {
                    s.classList.add('text-amber-400');
                    s.classList.remove('text-gray-300');
                } else {
                    s.classList.add('text-gray-300');
                    s.classList.remove('text-amber-400');
                }
            });
        });
    });

    // Review images preview
    const reviewImgInput = document.getElementById('review-images-input');
    const reviewImgGrid = document.getElementById('review-images-preview-grid');
    const btnClearReviewImgs = document.getElementById('btn-clear-review-images');

    if (reviewImgInput && reviewImgGrid) {
        reviewImgInput.addEventListener('change', (e) => {
            reviewImgGrid.innerHTML = '';
            const files = Array.from(e.target.files || []);
            if (files.length > 0 && btnClearReviewImgs) {
                btnClearReviewImgs.classList.remove('hidden');
            }

            files.forEach((file) => {
                if (!file.type.startsWith('image/')) return;
                const card = document.createElement('div');
                card.className = 'image-preview-card image-preview-card--sm';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'image-preview-card__img';
                card.appendChild(img);
                reviewImgGrid.appendChild(card);
            });
        });

        if (btnClearReviewImgs) {
            btnClearReviewImgs.addEventListener('click', () => {
                reviewImgInput.value = '';
                reviewImgGrid.innerHTML = '';
                btnClearReviewImgs.classList.add('hidden');
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initOrderReview();
});
