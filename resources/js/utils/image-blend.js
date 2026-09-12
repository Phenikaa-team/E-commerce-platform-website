/**
 * Canvas Image Dominant Edge Color Extraction & Smart Blend Utility
 */

const cardColorCache = new Map();

/**
 * Extracts the dominant/background edge color of an image via an offscreen canvas.
 * Samples perimeter pixels (top, bottom, left, right edges) where the image's background is situated.
 */
export function extractImageDominantEdgeColor(img) {
    if (!img.naturalWidth || !img.naturalHeight) return null;

    const imgSrc = img.currentSrc || img.src;
    if (cardColorCache.has(imgSrc)) {
        return cardColorCache.get(imgSrc);
    }

    try {
        const canvas = document.createElement('canvas');
        const w = 32;
        const h = 32;
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        if (!ctx) return null;

        ctx.drawImage(img, 0, 0, w, h);
        const data = ctx.getImageData(0, 0, w, h).data;

        let rSum = 0;
        let gSum = 0;
        let bSum = 0;
        let count = 0;

        const sample = (x, y) => {
            const i = (y * w + x) * 4;
            const a = data[i + 3];
            if (a > 40) { // ignore transparent pixels
                rSum += data[i];
                gSum += data[i + 1];
                bSum += data[i + 2];
                count++;
            }
        };

        // Sample perimeter rows and columns (corners and outer edges)
        for (let x = 0; x < w; x++) {
            sample(x, 0);
            sample(x, 1);
            sample(x, h - 2);
            sample(x, h - 1);
        }
        for (let y = 2; y < h - 2; y++) {
            sample(0, y);
            sample(1, y);
            sample(w - 2, y);
            sample(w - 1, y);
        }

        if (count === 0) return null;

        const r = Math.round(rSum / count);
        const g = Math.round(gSum / count);
        const b = Math.round(bSum / count);
        const lum = (0.299 * r + 0.587 * g + 0.114 * b);

        const result = {
            r, g, b, lum,
            css: `rgb(${r}, ${g}, ${b})`
        };

        cardColorCache.set(imgSrc, result);
        return result;
    } catch (err) {
        // Safe fallback if cross-origin canvas security restriction occurs
        return null;
    }
}

/**
 * Dynamically applies smart dominant color sampling to top card containers,
 * ensuring image edge feathering blends seamlessly into the top half background.
 */
export function initSmartCardBlend(container) {
    if (!container) return;
    const boxes = container.querySelectorAll('[data-smart-card-box]');

    boxes.forEach(box => {
        const img = box.querySelector('[data-smart-card-img]');
        const underlay = box.querySelector('[data-smart-card-underlay]');
        if (!img) return;

        const applyColor = () => {
            const color = extractImageDominantEdgeColor(img);
            if (color) {
                // Determine if photo has a white / light neutral background
                const isLightBackground = color.lum > 215 || (color.r > 220 && color.g > 220 && color.b > 220);

                if (isLightBackground) {
                    // White or clean neutral background: blend multiply seamlessly dissolves any white boundary
                    box.style.backgroundColor = '#ffffff';
                    img.style.mixBlendMode = 'multiply';
                    if (underlay) {
                        underlay.style.opacity = '0';
                    }
                } else {
                    // Rich colored background (e.g. Nike red, headphone yellow, bed slate)
                    box.style.backgroundColor = color.css;
                    img.style.mixBlendMode = 'normal';
                    if (underlay) {
                        underlay.style.opacity = '0.35';
                    }

                    // Adjust inner highlight ring if the background is dark
                    if (color.lum < 95) {
                        const ring = box.querySelector('[data-smart-card-ring]');
                        if (ring) {
                            ring.classList.remove('ring-black/[0.05]');
                            ring.classList.add('ring-white/15');
                        }
                    }
                }
            }
        };

        if (img.complete && img.naturalWidth > 0) {
            applyColor();
        } else {
            img.addEventListener('load', applyColor, { once: true });
        }
    });
}
