<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MarketplaceFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds for the 3 roles, coupons, and orders.
     */
    public function run(): void
    {
        // 1. Admin User (Password: admin)
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'ShopMart Administrator']
        );
        $admin->update([
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 2. Dedicated Seller Users for ALL Stores (Email & Password derived from Shop Name)
        $sellersConfig = [
            [
                'store_slug' => 'techzone-official',
                'store_name' => 'TechZone Official Mall',
                'email' => 'techzone@gmail.com',
                'password' => 'techzone',
                'seller_name' => 'TechZone Official Mall Team',
                'phone' => '+84 908 111 222',
            ],
            [
                'store_slug' => 'samsung-flagship-store',
                'store_name' => 'Samsung Flagship Store',
                'email' => 'samsung@gmail.com',
                'legacy_email' => 'seller@gmail.com',
                'password' => 'samsung',
                'seller_name' => 'Samsung Electronics VN',
                'phone' => '+84 912 345 678',
            ],
            [
                'store_slug' => 'apple-official-store',
                'store_name' => 'Apple Official Store',
                'email' => 'apple@gmail.com',
                'legacy_email' => 'apple_seller@gmail.com',
                'password' => 'apple',
                'seller_name' => 'Apple Vietnam Authorized Reseller',
                'phone' => '+84 988 777 666',
            ],
            [
                'store_slug' => 'asus-rog-official',
                'store_name' => 'ASUS ROG Official',
                'email' => 'asus@gmail.com',
                'legacy_email' => 'asus_seller@gmail.com',
                'password' => 'asus',
                'seller_name' => 'ASUS ROG Vietnam',
                'phone' => '+84 903 888 999',
            ],
            [
                'store_slug' => 'shopmart-fashion-mall',
                'store_name' => 'ShopMart Fashion Mall',
                'email' => 'fashion@gmail.com',
                'password' => 'fashion',
                'seller_name' => 'ShopMart Fashion Mall Team',
                'phone' => '+84 909 333 444',
            ],
            [
                'store_slug' => 'nova-living',
                'alt_slug' => 'homelife-official-store',
                'store_name' => 'Nova Living',
                'email' => 'novaliving@gmail.com',
                'legacy_email' => 'living_seller@gmail.com',
                'password' => 'novaliving',
                'seller_name' => 'Nova Living Home',
                'phone' => '+84 905 123 456',
            ],
            [
                'store_slug' => 'cosmetics-beauty-official',
                'store_name' => 'Cosmetics & Beauty Official',
                'email' => 'beauty@gmail.com',
                'password' => 'beauty',
                'seller_name' => 'Cosmetics & Beauty Official Team',
                'phone' => '+84 907 555 666',
            ],
            [
                'store_slug' => 'nha-nam-bookstore',
                'store_name' => 'Nhã Nam Bookstore',
                'email' => 'nhanam@gmail.com',
                'password' => 'nhanam',
                'seller_name' => 'Nhã Nam Bookstore Team',
                'phone' => '+84 902 777 888',
            ],
        ];

        foreach ($sellersConfig as $cfg) {
            // Primary seller account: Email & Password based on store name
            $sellerUser = User::firstOrCreate(
                ['email' => $cfg['email']],
                [
                    'name' => $cfg['seller_name'],
                    'phone' => $cfg['phone'],
                    'avatar_url' => asset('images/placeholders/store-logo-placeholder.svg'),
                ]
            );
            $sellerUser->update([
                'name' => $cfg['seller_name'],
                'password' => Hash::make($cfg['password']),
                'role' => 'seller',
                'status' => 'active',
                'phone' => $cfg['phone'],
            ]);

            // Legacy backward-compatible email if exists (e.g. seller@gmail.com)
            if (! empty($cfg['legacy_email'])) {
                $legacyUser = User::firstOrCreate(
                    ['email' => $cfg['legacy_email']],
                    [
                        'name' => $cfg['seller_name'],
                        'phone' => $cfg['phone'],
                        'avatar_url' => asset('images/placeholders/store-logo-placeholder.svg'),
                    ]
                );
                $legacyUser->update([
                    'name' => $cfg['seller_name'],
                    'password' => Hash::make('seller'),
                    'role' => 'seller',
                    'status' => 'active',
                ]);
            }

            // Link store to the primary seller
            $storeQuery = Store::where('slug', $cfg['store_slug']);
            if (! empty($cfg['alt_slug'])) {
                $storeQuery->orWhere('slug', $cfg['alt_slug']);
            }
            $storeQuery->orWhere('name', 'like', '%'.explode(' ', $cfg['store_name'])[0].'%');

            $store = $storeQuery->first();
            if ($store) {
                $storeUpdateData = [
                    'user_id' => $sellerUser->id,
                    'name' => $cfg['store_name'],
                    'slug' => $cfg['store_slug'],
                ];
                if (empty($store->logo_url) || str_contains($store->logo_url, 'unsplash')) {
                    $storeUpdateData['logo_url'] = asset('images/placeholders/store-logo-placeholder.svg');
                }
                if (empty($store->banner_url) || str_contains($store->banner_url, 'unsplash')) {
                    $storeUpdateData['banner_url'] = asset('images/placeholders/store-banner-placeholder.svg');
                }
                $store->update($storeUpdateData);

                // Also link legacy user store if needed
                if (! empty($cfg['legacy_email']) && isset($legacyUser)) {
                    // Legacy user can also act as seller
                }
            }
        }

        // 3. Demo Buyer
        $buyer = User::where('email', 'example@gmail.com')->first();
        if ($buyer) {
            $buyer->update(['role' => 'buyer', 'status' => 'active']);
        }

        // 4. Coupons (Shopee Style with min_order_value = 0 so they always apply cleanly)
        $couponsData = [
            [
                'code' => 'FREESHIP',
                'name' => 'Miễn Phí Vận Chuyển Toàn Quốc',
                'description' => 'Giảm tối đa 30.000₫ cho mọi đơn hàng',
                'discount_type' => 'fixed',
                'discount_value' => 30000,
                'min_order_value' => 0,
                'max_discount_amount' => 30000,
                'usage_limit' => 1000,
                'used_count' => 142,
                'expires_at' => now()->addDays(60),
                'is_active' => true,
            ],
            [
                'code' => 'GIAM10',
                'name' => 'Giảm 10% Toàn Sàn',
                'description' => 'Áp dụng cho mọi đơn hàng, giảm tối đa 100.000₫',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_value' => 0,
                'max_discount_amount' => 100000,
                'usage_limit' => 500,
                'used_count' => 38,
                'expires_at' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'SALE50K',
                'name' => 'Voucher 50K Mọi Đơn Hàng',
                'description' => 'Giảm ngay 50.000₫ cho đơn hàng',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'min_order_value' => 0,
                'max_discount_amount' => 50000,
                'usage_limit' => 300,
                'used_count' => 12,
                'expires_at' => now()->addDays(15),
                'is_active' => true,
            ],
            [
                'code' => 'TECH100K',
                'name' => 'Voucher Điện Tử & Công Nghệ',
                'description' => 'Giảm 100.000₫ cho thiết bị số, điện thoại, phụ kiện',
                'discount_type' => 'fixed',
                'discount_value' => 100000,
                'min_order_value' => 0,
                'max_discount_amount' => 100000,
                'usage_limit' => 200,
                'used_count' => 19,
                'expires_at' => now()->addDays(20),
                'is_active' => true,
            ],
            [
                'code' => 'FASHION20',
                'name' => 'Voucher Thời Trang Sành Điệu',
                'description' => 'Giảm 20% tối đa 80.000₫ cho mọi đơn thời trang',
                'discount_type' => 'percent',
                'discount_value' => 20,
                'min_order_value' => 0,
                'max_discount_amount' => 80000,
                'usage_limit' => 250,
                'used_count' => 45,
                'expires_at' => now()->addDays(25),
                'is_active' => true,
            ],
            [
                'code' => 'MALL50',
                'name' => 'ShopMart Mall Chính Hãng',
                'description' => 'Giảm 50.000₫ khi mua các sản phẩm gian hàng chính hãng Mall',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'min_order_value' => 0,
                'max_discount_amount' => 50000,
                'usage_limit' => 400,
                'used_count' => 67,
                'expires_at' => now()->addDays(40),
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP25K',
                'name' => 'Mã Vận Chuyển FreeShip 25K',
                'description' => 'Giảm tối đa ₫25.000 phí vận chuyển',
                'discount_type' => 'fixed',
                'discount_value' => 25000,
                'min_order_value' => 0,
                'max_discount_amount' => 25000,
                'usage_limit' => 1000,
                'used_count' => 53,
                'expires_at' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP70K',
                'name' => 'Mã Vận Chuyển FreeShip 70K',
                'description' => 'Giảm tối đa ₫70.000 phí vận chuyển',
                'discount_type' => 'fixed',
                'discount_value' => 70000,
                'min_order_value' => 0,
                'max_discount_amount' => 70000,
                'usage_limit' => 500,
                'used_count' => 88,
                'expires_at' => now()->addDays(20),
                'is_active' => true,
            ],
            [
                'code' => 'DIENTU500K',
                'name' => 'Giảm ₫500K Thiết Bị Điện Tử',
                'description' => 'Giảm ngay ₫500.000 cho đơn hàng công nghệ',
                'discount_type' => 'fixed',
                'discount_value' => 500000,
                'min_order_value' => 0,
                'max_discount_amount' => 500000,
                'usage_limit' => 100,
                'used_count' => 14,
                'expires_at' => now()->addDays(45),
                'is_active' => true,
            ],
            [
                'code' => 'FASHION8',
                'name' => 'Thời Trang Giảm 8%',
                'description' => 'Giảm 8% tối đa ₫40.000',
                'discount_type' => 'percent',
                'discount_value' => 8,
                'min_order_value' => 0,
                'max_discount_amount' => 40000,
                'usage_limit' => 300,
                'used_count' => 42,
                'expires_at' => now()->addDays(15),
                'is_active' => true,
            ],
            [
                'code' => 'ABC',
                'name' => 'Voucher Ưu Đãi Đặc Biệt ABC',
                'description' => 'Giảm 15% tối đa ₫50.000 cho mọi đơn hàng',
                'discount_type' => 'percent',
                'discount_value' => 15,
                'min_order_value' => 0,
                'max_discount_amount' => 50000,
                'usage_limit' => 500,
                'used_count' => 10,
                'expires_at' => now()->addDays(60),
                'is_active' => true,
            ],
        ];

        foreach ($couponsData as $cd) {
            Coupon::updateOrCreate(['code' => $cd['code']], $cd);
        }

        // 5. Seed Sample Orders and Reviews for all stores ensuring complete brand consistency
        $storesToSeed = Store::all();
        $buyerUsers = User::where('role', 'buyer')->take(6)->get();
        if ($buyerUsers->isEmpty() && $buyer) {
            $buyerUsers = collect([$buyer]);
        }

        foreach ($storesToSeed as $s) {
            if ($s->orders()->count() < 3) {
                $storeProducts = $s->products;
                if ($storeProducts->isNotEmpty()) {
                    foreach ($storeProducts->take(3) as $pIdx => $prod) {
                        $currentBuyer = $buyerUsers[$pIdx % $buyerUsers->count()] ?? $buyer;
                        $shippingAddress = [
                            'name' => $currentBuyer->name ?? 'Khách hàng',
                            'phone' => $currentBuyer->phone ?? '0912 345 678',
                            'address' => 'Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
                        ];

                        $subtotal = (float) $prod->price;
                        $shippingFee = 30000;
                        $total = $subtotal + $shippingFee;

                        $order = Order::create([
                            'user_id' => $currentBuyer->id,
                            'order_code' => 'SM-'.strtoupper(substr($s->slug, 0, 3)).rand(1000, 9999),
                            'status' => 'completed',
                            'payment_method' => 'vnpay',
                            'payment_status' => 'paid',
                            'subtotal' => $subtotal,
                            'shipping_fee' => $shippingFee,
                            'discount_amount' => 0,
                            'total' => $total,
                            'shipping_address' => $shippingAddress,
                            'created_at' => now()->subDays($pIdx + 1),
                        ]);

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $prod->id,
                            'product_name' => $prod->name,
                            'selected_variant' => 'Chính hãng',
                            'quantity' => 1,
                            'unit_price' => $prod->price,
                            'subtotal' => $prod->price,
                        ]);

                        Review::create([
                            'user_id' => $currentBuyer->id,
                            'product_id' => $prod->id,
                            'order_id' => $order->id,
                            'rating' => 5,
                            'comment' => 'Sản phẩm '.$prod->name.' chính hãng chất lượng rất tốt, shop đóng gói cẩn thận và giao nhanh!',
                            'status' => 'approved',
                            'created_at' => now()->subDays($pIdx),
                        ]);
                    }
                }
            }
        }
    }
}
