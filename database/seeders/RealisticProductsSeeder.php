<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Seeder;

class RealisticProductsSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::query()->where('status', 'active')->orderBy('id')->first();
        if (! $store) {
            return;
        }

        // 1. Chuẩn hóa sửa lỗi chính tả sản phẩm id=1 (Iphong -> iPhone)
        $p1 = Product::find(1);
        if ($p1) {
            $p1->update([
                'name' => 'Điện thoại iPhone 14 Pro Max 256GB Chính Hãng VNA',
                'slug' => 'dien-thoai-iphone-14-pro-max-256gb-chinh-hang-vna',
                'brand' => 'Apple',
                'price' => 22490000,
                'original_price' => 26990000,
                'discount_percent' => 17,
                'rating' => 4.9,
                'reviews_count' => 1250,
                'sold_count' => 3420,
                'description' => 'iPhone 14 Pro Max 256GB chính hãng Apple Việt Nam (VN/A). Sở hữu màn hình Dynamic Island đột phá 6.7 inch Super Retina XDR 120Hz, chip A16 Bionic 4nm siêu mạnh và cụm 3 camera 48MP quay chụp đỉnh cao.',
                'main_image_url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=800&q=80',
                'specs' => [
                    'Thương hiệu' => 'Apple',
                    'Màn hình' => '6.7 inch, Super Retina XDR OLED, 120Hz ProMotion',
                    'Chip xử lý' => 'Apple A16 Bionic (4nm)',
                    'Bộ nhớ trong' => '256GB',
                    'RAM' => '6GB',
                    'Camera chính' => '48MP (chính) + 12MP (góc siêu rộng) + 12MP (tele 3x)',
                    'Pin & Sạc' => '4.323 mAh, sạc nhanh 20W, sạc MagSafe 15W',
                    'Hệ điều hành' => 'iOS 17',
                    'Xuất xứ' => 'Chính hãng Apple Việt Nam (VN/A)',
                    'Bảo hành' => '12 tháng chính hãng 1 đổi 1 tại trung tâm bảo hành Apple',
                ],
                'features' => [
                    ['title' => 'Dynamic Island', 'desc' => 'Tương tác thông minh các thông báo tức thì'],
                    ['title' => 'Camera 48MP Pro', 'desc' => 'Chụp ProRAW và zoom quang 3x sắc nét'],
                    ['title' => 'Chip A16 Bionic', 'desc' => 'Đồ họa mượt mà, tiết kiệm pin vượt trội'],
                ],
            ]);
        }

        // 2. Đảm bảo danh mục điện thoại chuẩn
        $catPhone = Category::firstOrCreate(
            ['slug' => 'dien-thoai-va-phu-kien'],
            ['name' => 'Điện thoại và phụ kiện', 'parent_id' => null]
        );

        $catIphone = Category::firstOrCreate(
            ['slug' => 'iphone'],
            ['name' => 'iPhone', 'parent_id' => $catPhone->id]
        );

        $catAndroid = Category::firstOrCreate(
            ['slug' => 'dien-thoai-android'],
            ['name' => 'Điện thoại Android', 'parent_id' => $catPhone->id]
        );

        // 3. Danh sách sản phẩm thực tế bổ sung đa dạng mọi phân khúc (từ <5tr đến cao cấp)
        $products = [
            // --- ĐIỆN THOẠI CAO CẤP & TẦM TRUNG ---
            [
                'name' => 'iPhone 15 128GB Chính Hãng Apple VN/A',
                'slug' => 'iphone-15-128gb-chinh-hang-apple-vna',
                'category_id' => $catIphone->id,
                'brand' => 'Apple',
                'price' => 18990000,
                'original_price' => 21990000,
                'discount_percent' => 14,
                'rating' => 4.9,
                'reviews_count' => 980,
                'sold_count' => 2150,
                'stock' => 45,
                'is_mall' => true,
                'badge_text' => 'Chính hãng VN/A',
                'main_image_url' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?auto=format&fit=crop&w=800&q=80',
                'description' => 'iPhone 15 trang bị Dynamic Island, cụm camera chính 48MP với độ phân giải siêu cao và cổng kết nối USB-C tiện lợi hoàn toàn mới. Thiết kế mặt lưng kính pha màu bền bỉ.',
                'specs' => [
                    'Thương hiệu' => 'Apple',
                    'Màn hình' => '6.1 inch, Super Retina XDR OLED, độ sáng 2000 nits',
                    'Chip xử lý' => 'Apple A16 Bionic',
                    'Dung lượng' => '128GB',
                    'Camera sau' => '48MP + 12MP',
                    'Cổng kết nối' => 'USB-C',
                    'Kháng nước' => 'IP68',
                ],
            ],
            [
                'name' => 'Samsung Galaxy A55 5G 8GB/128GB',
                'slug' => 'samsung-galaxy-a55-5g-8gb-128gb',
                'category_id' => $catAndroid->id,
                'brand' => 'Samsung',
                'price' => 8490000,
                'original_price' => 9990000,
                'discount_percent' => 15,
                'rating' => 4.8,
                'reviews_count' => 740,
                'sold_count' => 3890,
                'stock' => 60,
                'is_mall' => true,
                'badge_text' => 'Bán chạy',
                'main_image_url' => 'https://images.unsplash.com/photo-1580910051074-3eb694886505?auto=format&fit=crop&w=800&q=80',
                'description' => 'Samsung Galaxy A55 5G với khung kim loại sang trọng, camera chụp đêm Nightography 50MP chống rung OIS, màn hình Super AMOLED 120Hz rực rỡ và chip Exynos 1480 mạnh mẽ.',
                'specs' => [
                    'Thương hiệu' => 'Samsung',
                    'Màn hình' => '6.6 inch Super AMOLED FHD+, 120Hz',
                    'Chip xử lý' => 'Exynos 1480 (4nm)',
                    'RAM / ROM' => '8GB / 128GB',
                    'Camera' => '50MP OIS + 12MP + 5MP',
                    'Pin & Sạc' => '5.000 mAh, sạc nhanh 25W',
                ],
            ],
            [
                'name' => 'Xiaomi Redmi 13C 6GB/128GB (Phân khúc giá rẻ)',
                'slug' => 'xiaomi-redmi-13c-6gb-128gb',
                'category_id' => $catAndroid->id,
                'brand' => 'Xiaomi',
                'price' => 2990000,
                'original_price' => 3490000,
                'discount_percent' => 14,
                'rating' => 4.7,
                'reviews_count' => 1120,
                'sold_count' => 6540,
                'stock' => 90,
                'is_mall' => true,
                'badge_text' => 'Giá rẻ < 3 triệu',
                'main_image_url' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=800&q=80',
                'description' => 'Smartphone quốc dân giá mềm dưới 3 triệu với màn hình lớn 6.74 inch 90Hz mượt mà, camera AI 50MP sắc nét và pin khủng 5.000 mAh thoải mái sử dụng cả ngày.',
                'specs' => [
                    'Thương hiệu' => 'Xiaomi',
                    'Màn hình' => '6.74 inch HD+, tần số quét 90Hz',
                    'Chip xử lý' => 'MediaTek Helio G85',
                    'RAM / ROM' => '6GB / 128GB (Hỗ trợ mở rộng RAM)',
                    'Pin' => '5.000 mAh, sạc nhanh 18W',
                ],
            ],
            [
                'name' => 'OPPO Reno11 F 5G 8GB/256GB Chuyên Gia Chân Dung',
                'slug' => 'oppo-reno11-f-5g-8gb-256gb',
                'category_id' => $catAndroid->id,
                'brand' => 'OPPO',
                'price' => 7990000,
                'original_price' => 8990000,
                'discount_percent' => 11,
                'rating' => 4.8,
                'reviews_count' => 610,
                'sold_count' => 1890,
                'stock' => 40,
                'is_mall' => true,
                'badge_text' => 'Chân dung sắc nét',
                'main_image_url' => 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?auto=format&fit=crop&w=800&q=80',
                'description' => 'OPPO Reno11 F 5G sở hữu thiết kế vân kim sa độc đáo, viền màn hình siêu mỏng AMOLED 120Hz, camera chuyên gia chân dung 64MP và sạc siêu nhanh 67W SUPERVOOC.',
                'specs' => [
                    'Thương hiệu' => 'OPPO',
                    'Màn hình' => '6.7 inch AMOLED 120Hz viền siêu mỏng',
                    'Chip' => 'MediaTek Dimensity 7050 5G',
                    'RAM / ROM' => '8GB / 256GB',
                    'Camera' => '64MP + 8MP + 2MP',
                    'Sạc' => '67W SUPERVOOC (sạc đầy trong 48 phút)',
                ],
            ],
            [
                'name' => 'Samsung Galaxy Z Flip5 5G 256GB Gập Thời Thượng',
                'slug' => 'samsung-galaxy-z-flip5-5g-256gb',
                'category_id' => $catAndroid->id,
                'brand' => 'Samsung',
                'price' => 16490000,
                'original_price' => 22990000,
                'discount_percent' => 28,
                'rating' => 4.9,
                'reviews_count' => 830,
                'sold_count' => 1640,
                'stock' => 25,
                'is_mall' => true,
                'badge_text' => 'Màn gập sành điệu',
                'main_image_url' => 'https://images.unsplash.com/photo-1585060544812-6b45742d762f?auto=format&fit=crop&w=800&q=80',
                'description' => 'Điện thoại màn hình gập cao cấp với Flex Window 3.4 inch độc đáo, bản lề Flex không kẽ hở, camera quay chụp rảnh tay FlexCam và hiệu năng đỉnh cao với Snapdragon 8 Gen 2.',
                'specs' => [
                    'Thương hiệu' => 'Samsung',
                    'Màn hình chính' => '6.7 inch Dynamic AMOLED 2X 120Hz',
                    'Màn hình ngoài' => '3.4 inch Super AMOLED Flex Window',
                    'Chip' => 'Snapdragon 8 Gen 2 for Galaxy',
                    'RAM / ROM' => '8GB / 256GB',
                    'Kháng nước' => 'IPX8 bền bỉ',
                ],
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'store_id' => $store->id,
                    'status' => 'active',
                ])
            );
        }
    }
}
