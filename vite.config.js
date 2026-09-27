import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/pages/invoice.css',
                'resources/js/app.js',
                'resources/js/components/image-picker.js',
                'resources/js/pages/auth.js',
                'resources/js/pages/vouchers.js',
                'resources/js/pages/cart.js',
                'resources/js/pages/checkout.js',
                'resources/js/pages/catalog-listing.js',
                'resources/js/pages/order-review.js',
                'resources/js/pages/profile-info.js',
                'resources/js/pages/profile-addresses.js',
                'resources/js/pages/seller-dashboard.js',
                'resources/js/pages/seller-revenue.js',
                'resources/js/pages/seller-orders.js',
                'resources/js/pages/seller-product-form.js',
                'resources/js/pages/seller-coupons.js',
                'resources/js/pages/seller-finances.js',
                'resources/js/pages/admin-dashboard.js',
                'resources/js/pages/admin-revenue.js',
                'resources/js/pages/admin-users.js',
                'resources/js/pages/admin-orders.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
