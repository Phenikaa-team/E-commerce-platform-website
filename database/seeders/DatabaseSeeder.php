<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@shopmart.vn'],
            ['name' => 'ShopMart Administrator', 'password' => bcrypt('password')]
        );

        // 1. Stores
        $appleStore = Store::create([
            'user_id' => $admin->id,
            'name' => 'Apple Official Store',
            'slug' => 'apple-official-store',
            'description' => 'Cửa hàng chính hãng Apple phân phối tại Việt Nam.',
            'rating' => 4.9,
            'response_rate' => '99%',
            'followers' => '1.2tr',
            'is_mall' => true,
            'online_status' => 'Online 5 phút trước',
        ]);

        $samsungStore = Store::create([
            'user_id' => $admin->id,
            'name' => 'Samsung Flagship Store',
            'slug' => 'samsung-flagship-store',
            'description' => 'Cửa hàng phân phối thiết bị Samsung chính hãng toàn quốc.',
            'rating' => 4.9,
            'response_rate' => '98%',
            'followers' => '950k',
            'is_mall' => true,
            'online_status' => 'Online 3 phút trước',
        ]);

        $asusStore = Store::create([
            'user_id' => $admin->id,
            'name' => 'ASUS ROG Official',
            'slug' => 'asus-rog-official',
            'description' => 'Trung tâm phân phối thiết bị gaming và đồ họa cao cấp ASUS ROG.',
            'rating' => 4.9,
            'response_rate' => '97%',
            'followers' => '420k',
            'is_mall' => true,
            'online_status' => 'Online 10 phút trước',
        ]);

        $techStore = Store::create([
            'user_id' => $admin->id,
            'name' => 'TechZone Official Mall',
            'slug' => 'techzone-official',
            'description' => 'Phụ kiện công nghệ, âm thanh và phụ kiện thông minh đỉnh cao.',
            'rating' => 4.8,
            'response_rate' => '96%',
            'followers' => '680k',
            'is_mall' => true,
            'online_status' => 'Online 1 phút trước',
        ]);

        $fashionStore = Store::create([
            'user_id' => $admin->id,
            'name' => 'ShopMart Fashion Mall',
            'slug' => 'shopmart-fashion-mall',
            'description' => 'Thời trang cao cấp đón đầu xu hướng phong cách Hàn Quốc & Quốc tế.',
            'rating' => 4.8,
            'response_rate' => '95%',
            'followers' => '350k',
            'is_mall' => true,
            'online_status' => 'Online 8 phút trước',
        ]);

        $homeStore = Store::create([
            'user_id' => $admin->id,
            'name' => 'HomeLife Official Store',
            'slug' => 'homelife-official-store',
            'description' => 'Thiết bị gia dụng thông minh tiện ích cho mọi gia đình.',
            'rating' => 4.9,
            'response_rate' => '99%',
            'followers' => '520k',
            'is_mall' => true,
            'online_status' => 'Online 15 phút trước',
        ]);

        $beautyStore = Store::create([
            'user_id' => $admin->id,
            'name' => 'Cosmetics & Beauty Official',
            'slug' => 'cosmetics-beauty-official',
            'description' => 'Mỹ phẩm, chăm sóc da chính hãng uy tín cam kết 100%.',
            'rating' => 5.0,
            'response_rate' => '99%',
            'followers' => '890k',
            'is_mall' => true,
            'online_status' => 'Online 4 phút trước',
        ]);

        $bookStore = Store::create([
            'user_id' => $admin->id,
            'name' => 'Nhã Nam Bookstore',
            'slug' => 'nha-nam-bookstore',
            'description' => 'Tủ sách tinh hoa văn học, kinh tế và phát triển bản thân.',
            'rating' => 4.9,
            'response_rate' => '99%',
            'followers' => '1.1tr',
            'is_mall' => true,
            'online_status' => 'Online 2 phút trước',
        ]);

        // 2. Categories
        $catPhone = Category::create(['name' => 'Điện thoại & Phụ kiện', 'slug' => 'phone', 'badge' => 'Hot']);
        $catLaptop = Category::create(['name' => 'Laptop & Máy tính', 'slug' => 'laptop', 'badge' => 'Giảm 20%']);
        $catFashion = Category::create(['name' => 'Thời trang & Phụ kiện', 'slug' => 'fashion', 'badge' => 'Mới']);
        $catAppliances = Category::create(['name' => 'Gia dụng thông minh', 'slug' => 'home', 'badge' => 'Sale']);
        $catBeauty = Category::create(['name' => 'Làm đẹp & Sức khỏe', 'slug' => 'beauty', 'badge' => 'Chính hãng']);
        $catBooks = Category::create(['name' => 'Sách & Văn phòng phẩm', 'slug' => 'books', 'badge' => 'Bán chạy']);

        // 3. Products list with rich gallery, specs, variants
        $productsData = [
            // ==================== FLASH SALE PRODUCTS ====================
            [
                'store_id' => $techStore->id,
                'category_id' => $catPhone->id,
                'name' => 'Tai nghe Bluetooth TWS Pro',
                'slug' => 'tai-nghe-bluetooth-tws-pro',
                'brand' => 'SoundPro',
                'badge_text' => 'Chống ồn ANC',
                'price' => 1190000,
                'original_price' => 1990000,
                'discount_percent' => 40,
                'rating' => 4.8,
                'reviews_count' => 540,
                'sold_count' => 320,
                'stock' => 45,
                'is_mall' => true,
                'is_flash_sale' => true,
                'flash_sale_percent' => 78,
                'main_image_url' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Tai nghe không dây Bluetooth TWS Pro mang đến trải nghiệm âm thanh sống động với công nghệ chống ồn chủ động ANC tiên tiến, thời lượng pin lên đến 36 giờ cùng dock sạc nhanh không dây.',
                'specs' => [
                    'Thương hiệu' => 'SoundPro',
                    'Model' => 'TWS Pro Wireless',
                    'Chuẩn kết nối' => 'Bluetooth 5.3',
                    'Chống ồn' => 'Chủ động ANC 35dB',
                    'Thời lượng pin' => '8 giờ (tai nghe) + 28 giờ (dock)',
                    'Kháng nước' => 'IPX5 chống mồ hôi và mưa nhẹ',
                    'Trọng lượng' => '4.2g mỗi tai',
                    'Bảo hành' => '12 tháng chính hãng 1 đổi 1',
                ],
                'features' => [
                    ['title' => 'Chống ồn ANC', 'desc' => 'Triệt tiêu tạp âm môi trường hiệu quả'],
                    ['title' => 'Pin 36 Giờ', 'desc' => 'Thoải mái nghe nhạc cả tuần'],
                    ['title' => 'Bluetooth 5.3', 'desc' => 'Độ trễ siêu thấp chỉ 45ms'],
                    ['title' => 'Kháng nước IPX5', 'desc' => 'An tâm tập luyện thể thao'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Đen Nhám', 'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Trắng Sữa', 'image' => 'https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Bản Tiêu Chuẩn', 'Bản Kèm Ốp Bảo Vệ'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1608156639585-34a0a56ee6c9?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catPhone->id,
                'name' => 'Đồng hồ thông minh Series 9',
                'slug' => 'dong-ho-thong-minh-series-9',
                'brand' => 'TechFit',
                'badge_text' => 'Màn Retina OLED',
                'price' => 2790000,
                'original_price' => 3990000,
                'discount_percent' => 30,
                'rating' => 4.9,
                'reviews_count' => 310,
                'sold_count' => 156,
                'stock' => 25,
                'is_mall' => true,
                'is_flash_sale' => true,
                'flash_sale_percent' => 45,
                'main_image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Đồng hồ thông minh Series 9 tích hợp màn hình Always-On OLED sắc nét, đo nhịp tim điện tâm đồ ECG, nồng độ oxy trong máu SpO2 và hỗ trợ hơn 100 chế độ thể thao thông minh.',
                'specs' => [
                    'Thương hiệu' => 'TechFit',
                    'Màn hình' => '1.9 inch AMOLED Retina Always-On',
                    'Thời lượng pin' => 'Lên tới 5 ngày sử dụng liên tục',
                    'Tính năng sức khỏe' => 'Đo nhịp tim, SpO2, theo dõi giấc ngủ sâu',
                    'Định vị' => 'GPS độc lập đa băng tần',
                    'Kháng nước' => '5ATM (50 mét lặn biển)',
                    'Bảo hành' => '12 tháng chính hãng',
                ],
                'features' => [
                    ['title' => 'Màn AMOLED', 'desc' => 'Hiển thị rực rỡ dưới nắng'],
                    ['title' => 'Đo SpO2 & ECG', 'desc' => 'Chăm sóc sức khỏe 24/7'],
                    ['title' => 'GPS Độc Lập', 'desc' => 'Lộ trình chạy chính xác'],
                    ['title' => 'Kháng nước 5ATM', 'desc' => 'Bơi lội thoải mái'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Bạc Titan', 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Đen Midnight', 'image' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['41mm', '45mm'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1510017803434-a899398421b3?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catPhone->id,
                'name' => 'Loa Bluetooth Bass Trầm 40W',
                'slug' => 'loa-bluetooth-bass-tram-40w',
                'brand' => 'BoomAudio',
                'badge_text' => 'Bass Boost',
                'price' => 890000,
                'original_price' => 1490000,
                'discount_percent' => 40,
                'rating' => 4.7,
                'reviews_count' => 620,
                'sold_count' => 410,
                'stock' => 60,
                'is_mall' => true,
                'is_flash_sale' => true,
                'flash_sale_percent' => 62,
                'main_image_url' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Loa di động chống nước công suất 40W với dải âm bass uy lực, đèn LED RGB chuyển động theo nhịp nhạc, pin 6000mAh chơi nhạc suốt 15 tiếng.',
                'specs' => [
                    'Công suất' => '40W RMS',
                    'Dung lượng pin' => '6.000mAh (15 giờ nghe)',
                    'Kết nối' => 'Bluetooth 5.2, AUX 3.5mm, thẻ nhớ TF',
                    'Kháng nước' => 'IPX7 ngâm nước 1 mét',
                    'Bảo hành' => '12 tháng chính hãng',
                ],
                'features' => [
                    ['title' => 'Bass Cực Sâu', 'desc' => 'Màng cộng hưởng kép 40W'],
                    ['title' => 'Pin 15 Giờ', 'desc' => 'Chơi nhạc xuyên đêm tiệc'],
                    ['title' => 'Kháng nước IPX7', 'desc' => 'Bền bỉ bên hồ bơi'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Đen Carbon', 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Xanh Rêu Quân Đội', 'image' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Tiêu chuẩn', 'Kèm micro mini'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashion->id,
                'name' => 'Giày thể thao unisex chạy bộ êm ái',
                'slug' => 'giay-the-thao-unisex',
                'brand' => 'AeroRun',
                'badge_text' => 'Siêu nhẹ 180g',
                'price' => 750000,
                'original_price' => 990000,
                'discount_percent' => 24,
                'rating' => 4.8,
                'reviews_count' => 430,
                'sold_count' => 287,
                'stock' => 80,
                'is_mall' => true,
                'is_flash_sale' => true,
                'flash_sale_percent' => 54,
                'main_image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Giày thể thao dáng thể thao unisex với đệm đế EVA siêu nhẹ đàn hồi tốt, lớp vải dệt thoáng khí FlyKnit ôm sát chân giúp bạn vận động cả ngày không đau mỏi.',
                'specs' => [
                    'Chất liệu' => 'Vải dệt FlyKnit thoáng khí cao cấp',
                    'Đế giày' => 'Đệm EVA nguyên khối chống trơn trượt',
                    'Trọng lượng' => 'Chỉ 180g/chiếc',
                    'Xuất xứ' => 'Việt Nam xuất khẩu',
                ],
                'features' => [
                    ['title' => 'Đệm Siêu Êm', 'desc' => 'Giảm chấn tối đa cho khớp gối'],
                    ['title' => 'Vải Thoáng Khí', 'desc' => 'Không gây bí mùi hôi chân'],
                    ['title' => 'Thiết Kế Trẻ Trung', 'desc' => 'Dễ phối mọi outfit năng động'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Đỏ Năng Động', 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Trắng Tinh Khôi', 'image' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['38', '39', '40', '41', '42', '43'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $techStore->id,
                'category_id' => $catLaptop->id,
                'name' => 'Bàn phím cơ Gaming RGB Hot-swap',
                'slug' => 'ban-phim-co-gaming-rgb',
                'brand' => 'KeyCraft',
                'badge_text' => 'Hot-swap 5 pin',
                'price' => 1490000,
                'original_price' => 2290000,
                'discount_percent' => 35,
                'rating' => 4.9,
                'reviews_count' => 280,
                'sold_count' => 198,
                'stock' => 35,
                'is_mall' => true,
                'is_flash_sale' => true,
                'flash_sale_percent' => 38,
                'main_image_url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Bàn phím cơ layout 75% gọn gàng, hỗ trợ 3 chế độ kết nối (Type-C, 2.4Ghz, Bluetooth 5.0), switch pre-lubed êm ái cùng led RGB 16.8 triệu màu.',
                'specs' => [
                    'Layout' => '75% (84 phím bấm)',
                    'Switch' => 'Gateron Pro Yellow pre-lubed',
                    'Kết nối' => 'Type-C dây rời, Wireless 2.4Ghz, Bluetooth 5.0',
                    'Pin' => '4.000mAh dùng đến 200 giờ',
                    'Bảo hành' => '24 tháng đổi mới',
                ],
                'features' => [
                    ['title' => 'Hot-swap 5 Pin', 'desc' => 'Tự do thay thế switch dễ dàng'],
                    ['title' => 'Gasket Mount', 'desc' => 'Gõ phím êm tai không rung lắc'],
                    ['title' => '3 Chế độ kết nối', 'desc' => 'Tương thích Mac & Windows'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Retro White', 'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Dark Knight', 'image' => 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Yellow Switch (Linear)', 'Brown Switch (Tactile)'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashion->id,
                'name' => 'Balo laptop chống nước cao cấp 15.6 inch',
                'slug' => 'balo-laptop-chong-nuoc-cao-cap',
                'brand' => 'UrbanShield',
                'badge_text' => 'Chống nước Oxford',
                'price' => 420000,
                'original_price' => 700000,
                'discount_percent' => 40,
                'rating' => 4.8,
                'reviews_count' => 510,
                'sold_count' => 321,
                'stock' => 55,
                'is_mall' => true,
                'is_flash_sale' => true,
                'flash_sale_percent' => 82,
                'main_image_url' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Balo thời trang đựng laptop 15.6 inch chống sốc dày dặn, chất vải Oxford kháng nước, tích hợp cổng sạc USB thông minh ngoài balo tiện lợi.',
                'specs' => [
                    'Chất liệu' => 'Vải Oxford 900D chống thấm',
                    'Ngăn laptop' => 'Chống sốc đệm tổ ong vừa máy 15.6 inch',
                    'Kích thước' => '45 x 30 x 15 cm',
                    'Trọng lượng' => '650g',
                ],
                'features' => [
                    ['title' => 'Chống Nước Tuyệt Đối', 'desc' => 'Bảo vệ laptop dưới trời mưa'],
                    ['title' => 'Đệm Lưng Công Thái Học', 'desc' => 'Giảm áp lực vai khi mang nặng'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Đen Nhám', 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Xám Tro', 'image' => 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Size Vừa 14 inch', 'Size Lớn 15.6 inch'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?auto=format&fit=crop&w=800&q=80',
                ],
            ],

            // ==================== RECOMMENDED FOR YOU PRODUCTS ====================
            [
                'store_id' => $appleStore->id,
                'category_id' => $catPhone->id,
                'name' => 'iPhone 15 Pro Max 256GB',
                'slug' => 'iphone-15-pro-max-256gb',
                'brand' => 'Apple',
                'badge_text' => 'Chính hãng VN/A',
                'price' => 28990000,
                'original_price' => 34990000,
                'discount_percent' => 17,
                'rating' => 4.9,
                'reviews_count' => 2100,
                'sold_count' => 12400,
                'stock' => 18,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=1200&q=80',
                'description' => 'iPhone 15 Pro Max sở hữu thiết kế titan bền bỉ, chip A17 Pro mạnh mẽ, hệ thống camera chuyên nghiệp và thời lượng pin ấn tượng. Trải nghiệm hiệu năng đỉnh cao trong một thiết kế tinh tế và sang trọng bậc nhất.',
                'specs' => [
                    'Thương hiệu' => 'Apple',
                    'Model' => 'iPhone 15 Pro Max',
                    'Màn hình' => '6.7 inch, Super Retina XDR, OLED 120Hz ProMotion',
                    'Chip' => 'A17 Pro (3nm)',
                    'RAM' => '8GB',
                    'Dung lượng' => '256GB',
                    'Camera sau' => '48MP + 12MP + 12MP (Zoom 5x)',
                    'Camera trước' => '12MP TrueDepth',
                    'Pin' => '4.422 mAh, Sạc nhanh 20W, MagSafe 15W',
                    'Hệ điều hành' => 'iOS 17',
                    'Xuất xứ' => 'Chính hãng Apple Việt Nam (VN/A)',
                    'Bảo hành' => '12 tháng chính hãng toàn quốc',
                ],
                'features' => [
                    ['title' => 'Chip A17 Pro', 'desc' => 'Hiệu năng vượt trội gaming đồ họa cao'],
                    ['title' => 'Camera 48MP', 'desc' => 'Chụp ảnh chuyên nghiệp & Zoom 5x quang học'],
                    ['title' => 'Màn hình 6.7"', 'desc' => 'Super Retina XDR với ProMotion 120Hz mượt mà'],
                    ['title' => 'Khung Titan', 'desc' => 'Bền bỉ, siêu nhẹ cấp độ hàng không'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Titan Tự Nhiên', 'image' => 'https://images.unsplash.com/photo-1696446701796-da61225697cc?auto=format&fit=crop&w=600&q=80'],
                        ['label' => 'Titan Đen', 'image' => 'https://images.unsplash.com/photo-1695048065059-d830b429015c?auto=format&fit=crop&w=600&q=80'],
                        ['label' => 'Titan Trắng', 'image' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?auto=format&fit=crop&w=600&q=80'],
                        ['label' => 'Titan Xanh', 'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=600&q=80'],
                    ],
                    'options' => ['256GB', '512GB', '1TB'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1695048065059-d830b429015c?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $samsungStore->id,
                'category_id' => $catPhone->id,
                'name' => 'Samsung Galaxy S24 Ultra 5G 512GB Titanium',
                'slug' => 'samsung-galaxy-s24-ultra-5g-512gb',
                'brand' => 'Samsung',
                'badge_text' => 'Galaxy AI',
                'price' => 27990000,
                'original_price' => 31990000,
                'discount_percent' => 12,
                'rating' => 4.9,
                'reviews_count' => 1890,
                'sold_count' => 8900,
                'stock' => 22,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Mở ra kỷ nguyên quyền năng Galaxy AI hoàn toàn mới với Galaxy S24 Ultra. Khoanh tròn để tìm kiếm đa năng, phiên dịch cuộc gọi trực tiếp và cụm camera zoom 100x đỉnh cao cùng bút S-Pen tích hợp.',
                'specs' => [
                    'Thương hiệu' => 'Samsung',
                    'Model' => 'Galaxy S24 Ultra 5G',
                    'Màn hình' => '6.8 inch Dynamic AMOLED 2X, QHD+, 120Hz, 2600 nits',
                    'Chip' => 'Snapdragon 8 Gen 3 for Galaxy (4nm)',
                    'RAM' => '12GB',
                    'Dung lượng' => '512GB',
                    'Camera sau' => '200MP + 50MP + 12MP + 10MP (Zoom 100x)',
                    'Bút S-Pen' => 'Tích hợp sẵn trong thân máy',
                    'Pin' => '5.000 mAh, Sạc siêu nhanh 45W',
                    'Bảo hành' => '12 tháng chính hãng Samsung VN',
                ],
                'features' => [
                    ['title' => 'Galaxy AI Quyền Năng', 'desc' => 'Khoanh tròn tìm kiếm & Dịch thuật trực tiếp'],
                    ['title' => 'Camera 200MP', 'desc' => 'Chi tiết đáng kinh ngạc, Zoom đêm siêu nét'],
                    ['title' => 'Bút S-Pen Tiện Lợi', 'desc' => 'Ghi chú và sáng tạo mọi lúc mọi nơi'],
                    ['title' => 'Khung Titan Đẳng Cấp', 'desc' => 'Độ bền vượt trội và cảm giác cầm nắm cao cấp'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Titan Xám', 'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Titan Đen', 'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Titan Tím', 'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['256GB', '512GB', '1TB'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1580910051074-3eb694886505?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $appleStore->id,
                'category_id' => $catLaptop->id,
                'name' => 'MacBook Pro 14 inch M3 Space Black 18GB/512GB',
                'slug' => 'macbook-pro-14-inch-m3-space-black',
                'brand' => 'Apple',
                'badge_text' => 'Freeship Xtra',
                'price' => 39990000,
                'original_price' => 45990000,
                'discount_percent' => 13,
                'rating' => 5.0,
                'reviews_count' => 920,
                'sold_count' => 4800,
                'stock' => 12,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1200&q=80',
                'description' => 'MacBook Pro 14 inch với chip M3 Pro mang lại hiệu năng phi thường cho các tác vụ chuyên nghiệp nặng nhất. Màn hình Liquid Retina XDR sáng đến 1600 nits cùng pin lên tới 22 giờ liên tục.',
                'specs' => [
                    'Thương hiệu' => 'Apple',
                    'Model' => 'MacBook Pro 14" M3 Pro',
                    'Màn hình' => '14.2 inch Liquid Retina XDR (3024 x 1964), 120Hz ProMotion',
                    'Chip' => 'Apple M3 Pro (11-core CPU, 14-core GPU)',
                    'RAM' => '18GB Unified Memory',
                    'Ổ cứng' => '512GB SSD siêu tốc',
                    'Cổng kết nối' => '3x Thunderbolt 4, HDMI, khe thẻ SDXC, MagSafe 3',
                    'Pin' => 'Lên tới 22 giờ sử dụng',
                    'Bảo hành' => '12 tháng chính hãng Apple Care',
                ],
                'features' => [
                    ['title' => 'Chip M3 Pro', 'desc' => 'Xử lý video 8K và đồ họa 3D mượt mà'],
                    ['title' => 'Màn XDR 120Hz', 'desc' => 'Độ sáng 1600 nits hiển thị HDR sống động'],
                    ['title' => 'Màu Space Black', 'desc' => 'Lớp phủ anodized chống bám vân tay'],
                    ['title' => 'Thời lượng pin 22h', 'desc' => 'Làm việc cả ngày không cần cắm sạc'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Space Black', 'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Silver', 'image' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['18GB / 512GB', '36GB / 1TB'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $asusStore->id,
                'category_id' => $catLaptop->id,
                'name' => 'Laptop Gaming ROG Zephyrus G16 OLED Core Ultra 9',
                'slug' => 'laptop-gaming-rog-zephyrus-g16-oled',
                'brand' => 'ASUS ROG',
                'badge_text' => 'RTX 4070 OLED',
                'price' => 48990000,
                'original_price' => 54990000,
                'discount_percent' => 11,
                'rating' => 4.9,
                'reviews_count' => 124,
                'sold_count' => 520,
                'stock' => 8,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Quái vật gaming siêu mỏng nhẹ ASUS ROG Zephyrus G16 sở hữu màn hình OLED 2.5K 240Hz sắc sảo, vi xử lý Intel Core Ultra 9 AI cùng card đồ họa NVIDIA RTX 4070.',
                'specs' => [
                    'Thương hiệu' => 'ASUS ROG',
                    'Model' => 'Zephyrus G16 GU605',
                    'Màn hình' => '16 inch ROG Nebula OLED 2.5K (2560x1600), 240Hz, 0.2ms',
                    'CPU' => 'Intel Core Ultra 9 185H (16 nhân, 22 luồng, NPU AI)',
                    'VGA' => 'NVIDIA GeForce RTX 4070 8GB GDDR6',
                    'RAM' => '32GB LPDDR5X 7467MHz',
                    'Ổ cứng' => '1TB PCIe 4.0 NVMe M.2 SSD',
                    'Trọng lượng' => 'Chỉ 1.85 kg siêu mỏng nhôm CNC nguyên khối',
                    'Bảo hành' => '24 tháng chính hãng ASUS Việt Nam',
                ],
                'features' => [
                    ['title' => 'Màn OLED 240Hz', 'desc' => 'Độ phản hồi 0.2ms chuẩn màu DCI-P3 100%'],
                    ['title' => 'Intel Core Ultra 9', 'desc' => 'Tích hợp AI NPU tối ưu hóa tác vụ'],
                    ['title' => 'RTX 4070 8GB', 'desc' => 'Chiến mượt mọi tựa game AAA đỉnh cao'],
                    ['title' => 'Vỏ Nhôm CNC', 'desc' => 'Mỏng nhẹ chỉ 1.49cm, dải đèn Slash Lighting'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Eclipse Gray', 'image' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Platinum White', 'image' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['RTX 4060 / 16GB', 'RTX 4070 / 32GB'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashion->id,
                'name' => 'Áo khoác Bomber Nam Minimalist phong cách Hàn Quốc',
                'slug' => 'ao-khoac-bomber-nam-minimalist',
                'brand' => 'K-Style',
                'badge_text' => 'Trending 2026',
                'price' => 390000,
                'original_price' => 590000,
                'discount_percent' => 34,
                'rating' => 4.9,
                'reviews_count' => 340,
                'sold_count' => 3400,
                'stock' => 50,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Áo khoác Bomber nam phom dáng suông hiện đại chuẩn phong cách Seoul, vải dù 2 lớp cản gió chống nước nhẹ, khóa kéo kim loại cao cấp dập nổi.',
                'specs' => [
                    'Chất liệu' => 'Vải dù Polyester 2 lớp cao cấp',
                    'Kiểu dáng' => 'Bomber suông thoải mái',
                    'Mùa phù hợp' => 'Thu Đông - Xuân',
                    'Xuất xứ' => 'Việt Nam',
                ],
                'features' => [
                    ['title' => 'Vải Cản Gió', 'desc' => 'Giữ ấm và chống bám bụi hiệu quả'],
                    ['title' => 'Form Dáng Hàn Quốc', 'desc' => 'Dễ dàng mix match trang phục'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Đen Classic', 'image' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Xanh Rêu Vintage', 'image' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['M (50-60kg)', 'L (60-70kg)', 'XL (70-82kg)'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $fashionStore->id,
                'category_id' => $catFashion->id,
                'name' => 'Váy đầm xòe Linen hoa nhí phong cách Pháp',
                'slug' => 'vay-dam-xoe-linen-hoa-nhi',
                'brand' => 'Fleur Design',
                'badge_text' => 'Linen Tự Nhiên',
                'price' => 320000,
                'original_price' => 450000,
                'discount_percent' => 28,
                'rating' => 4.8,
                'reviews_count' => 190,
                'sold_count' => 890,
                'stock' => 40,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Váy đầm xòe chất vải linen tự nhiên nhẹ nhàng, thấm hút mồ hôi cực tốt với họa tiết hoa nhí tinh tế, mang lại vẻ đẹp dịu dàng và thanh lịch cho phái nữ.',
                'specs' => [
                    'Chất liệu' => '100% Linen bột cao cấp',
                    'Họa tiết' => 'Hoa nhí pastel vintage',
                    'Chiều dài váy' => 'Qua gối dịu dàng',
                ],
                'features' => [
                    ['title' => 'Chất Vải Linen', 'desc' => 'Thoáng mát, nhẹ êm với làn da'],
                    ['title' => 'Thiết Kế Tôn Dáng', 'desc' => 'Chiết eo nhẹ nhàng khéo léo'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Hoa Vàng Nhạt', 'image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Hoa Xanh Baby', 'image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Size S (42-48kg)', 'Size M (48-55kg)', 'Size L (55-62kg)'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $homeStore->id,
                'category_id' => $catAppliances->id,
                'name' => 'Nồi chiên không dầu điện tử 6.5L Philips Rapid Air',
                'slug' => 'noi-chien-khong-dau-dien-tu-6-5l-philips',
                'brand' => 'Philips',
                'badge_text' => 'Giảm 90% Dầu Mỡ',
                'price' => 1890000,
                'original_price' => 2890000,
                'discount_percent' => 35,
                'rating' => 4.9,
                'reviews_count' => 710,
                'sold_count' => 7120,
                'stock' => 30,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Nồi chiên không dầu Philips dung tích lớn 6.5L nướng nguyên con gà, công nghệ Rapid Air xoáy nhiệt độc quyền giúp giòn rụm bên ngoài mọng nước bên trong mà không cần dầu mỡ.',
                'specs' => [
                    'Thương hiệu' => 'Philips',
                    'Dung tích' => '6.5 Lít (vừa gà nguyên con 2kg)',
                    'Công suất' => '2.000W gia nhiệt siêu nhanh',
                    'Bảng điều khiển' => 'Màn hình cảm ứng 8 menu tự động',
                    'Lòng nồi' => 'Chống dính sao biển QuickClean an toàn máy rửa chén',
                    'Bảo hành' => '24 tháng toàn cầu',
                ],
                'features' => [
                    ['title' => 'Công nghệ Rapid Air', 'desc' => 'Giòn ngon giảm đến 90% dầu mỡ'],
                    ['title' => 'Dung Tích Khủng 6.5L', 'desc' => 'Phục vụ bữa tiệc 4-8 người'],
                    ['title' => 'Dễ Dàng Vệ Sinh', 'desc' => 'Chống dính cao cấp tháo rời được'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Đen Bóng Sang Trọng', 'image' => 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Trắng Bạc Hiện Đại', 'image' => 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Bản Thường', 'Bản Kèm Khay Nướng Pizza'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1584992236310-6edddc08acff?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $homeStore->id,
                'category_id' => $catAppliances->id,
                'name' => 'Máy hút bụi cầm tay không dây Dyson V12 Detect Slim',
                'slug' => 'may-hut-bui-cam-tay-khong-day-dyson-v12',
                'brand' => 'Dyson',
                'badge_text' => 'Laser Soi Bụi',
                'price' => 12490000,
                'original_price' => 16490000,
                'discount_percent' => 24,
                'rating' => 5.0,
                'reviews_count' => 180,
                'sold_count' => 520,
                'stock' => 15,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1558317374-067fb5f30001?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1558317374-067fb5f30001?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Máy hút bụi thông minh Dyson V12 trang bị tia laser xanh soi rõ vi hạt bụi vô hình trên sàn cứng, cảm biến piezo tự động điều chỉnh lực hút theo mật độ bụi.',
                'specs' => [
                    'Thương hiệu' => 'Dyson',
                    'Model' => 'V12 Detect Slim Fluffy',
                    'Lực hút' => '150 AW mạnh mẽ',
                    'Thời lượng pin' => 'Lên tới 60 phút liên tục',
                    'Hệ thống lọc' => 'HEPA kín 5 lớp lọc sạch 99.99% bụi mịn 0.3 micromet',
                    'Trọng lượng' => 'Chỉ 2.2 kg siêu nhẹ',
                    'Bảo hành' => '24 tháng chính hãng Dyson VN',
                ],
                'features' => [
                    ['title' => 'Laser Soi Bụi', 'desc' => 'Nhìn thấy bụi vi mô mắt thường không thấy'],
                    ['title' => 'Cảm Biến Piezo', 'desc' => 'Tự đo đếm hạt bụi và tăng tốc lực hút'],
                    ['title' => 'Lọc HEPA Toàn Thân', 'desc' => 'Giữ không khí phòng luôn trong lành'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Vàng Đồng Dyson', 'image' => 'https://images.unsplash.com/photo-1558317374-067fb5f30001?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Bản Slim Fluffy', 'Bản Total Clean'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1558317374-067fb5f30001?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $beautyStore->id,
                'category_id' => $catBeauty->id,
                'name' => 'Tinh chất Serum Phục hồi & Dưỡng sáng da Estée Lauder 50ml',
                'slug' => 'serum-phuc-hoi-duong-sang-da-estee-lauder',
                'brand' => 'Estée Lauder',
                'badge_text' => 'Chính hãng 100%',
                'price' => 2450000,
                'original_price' => 3350000,
                'discount_percent' => 27,
                'rating' => 4.9,
                'reviews_count' => 1100,
                'sold_count' => 11000,
                'stock' => 50,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Serum phục hồi ban đêm số 1 thế giới với công nghệ độc quyền Chronolux™ Power Signal giúp giảm thiểu nếp nhăn, tăng cường độ săn chắc và phục hồi làn da rạng rỡ sau giấc ngủ.',
                'specs' => [
                    'Thương hiệu' => 'Estée Lauder',
                    'Dung tích' => '50ml',
                    'Loại da phù hợp' => 'Mọi loại da, kể cả da nhạy cảm lão hóa',
                    'Công dụng' => 'Phục hồi, dưỡng ẩm 72 giờ, chống lão hóa',
                    'Xuất xứ' => 'Mỹ',
                ],
                'features' => [
                    ['title' => 'Chronolux™ Độc Quyền', 'desc' => 'Tái sinh làn da ngay trong giấc ngủ'],
                    ['title' => 'Cấp Ẩm 72 Giờ', 'desc' => 'Axit Hyaluronic đậm đặc duy trì độ bóng khỏe'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Chai Thủy Tinh Nâu Hổ Phách', 'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['30ml', '50ml', '75ml'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $beautyStore->id,
                'category_id' => $catBeauty->id,
                'name' => 'Kem chống nắng phổ rộng bảo vệ toàn diện SPF50+ PA++++',
                'slug' => 'kem-chong-nang-pho-rong-bao-ve-toan-dien-spf50',
                'brand' => 'La Roche-Posay',
                'badge_text' => 'Chống Nắng Kiềm Dầu',
                'price' => 450000,
                'original_price' => 560000,
                'discount_percent' => 20,
                'rating' => 4.9,
                'reviews_count' => 5800,
                'sold_count' => 58000,
                'stock' => 120,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Kem chống nắng kiểm soát dầu hiệu quả suốt 12 tiếng với màng lọc quang phổ rộng Mexoryl 400 bảo vệ da trước tia UVA siêu dài, không gây bết dính và không nâng tông quá đà.',
                'specs' => [
                    'Thương hiệu' => 'La Roche-Posay',
                    'Chỉ số' => 'SPF50+ / PA++++',
                    'Dung tích' => '50ml',
                    'Kết cấu' => 'Gel cream mỏng nhẹ không bóng dầu',
                    'Xuất xứ' => 'Pháp',
                ],
                'features' => [
                    ['title' => 'Màng Lọc Mexoryl 400', 'desc' => 'Ngăn ngừa nám và lão hóa da do tia UV'],
                    ['title' => 'Kiềm Dầu 12 Giờ', 'desc' => 'Khô thoáng tức thì, không vệt trắng'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Vạch Xanh (Da Dầu Mụn)', 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Vạch Vàng (Da Khô Thường)', 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Tuýp 50ml', 'Combo 2 Tuýp'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $bookStore->id,
                'category_id' => $catBooks->id,
                'name' => 'Sách - Tâm Lý Học Về Tiền (Morgan Housel)',
                'slug' => 'sach-tam-ly-hoc-ve-tien',
                'brand' => 'NXB Trẻ',
                'badge_text' => 'Bestseller Toàn Cầu',
                'price' => 145000,
                'original_price' => 190000,
                'discount_percent' => 24,
                'rating' => 4.9,
                'reviews_count' => 310,
                'sold_count' => 3100,
                'stock' => 150,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Cuốn sách tài chính bán chạy nhất thế giới phân tích cách con người suy nghĩ về tiền bạc, may mắn, lòng tham và hạnh phúc. Những bài học vượt thời gian về sự giàu có và thịnh vượng.',
                'specs' => [
                    'Tác giả' => 'Morgan Housel',
                    'Dịch giả' => 'Hải Đăng',
                    'Nhà xuất bản' => 'NXB Trẻ',
                    'Số trang' => '384 trang',
                    'Kích thước' => '14 x 20.5 cm',
                ],
                'features' => [
                    ['title' => 'Góc Nhìn Khác Biệt', 'desc' => 'Tài chính không phải là toán học mà là tâm lý học'],
                    ['title' => '20 Câu Chuyện Đắt Giá', 'desc' => 'Dễ hiểu, sâu sắc và áp dụng được ngay'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Bìa Mềm Tiêu Chuẩn', 'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Bìa Mềm', 'Bìa Cứng Đặc Biệt'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
                ],
            ],
            [
                'store_id' => $bookStore->id,
                'category_id' => $catBooks->id,
                'name' => 'Sách - Rèn Luyện Tư Duy Phản Biện Sâu Trong Kỷ Nguyên Số',
                'slug' => 'sach-ren-luyen-tu-duy-phan-bien-sau',
                'brand' => 'Nhã Nam',
                'badge_text' => 'Top Khuyên Đọc',
                'price' => 98000,
                'original_price' => 135000,
                'discount_percent' => 27,
                'rating' => 4.8,
                'reviews_count' => 920,
                'sold_count' => 9200,
                'stock' => 90,
                'is_mall' => true,
                'is_flash_sale' => false,
                'main_image_url' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=800&q=80',
                'banner_image_url' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Khám phá phương pháp tư duy độc lập, nhận diện các ngụy biện phổ biến và rèn luyện kỹ năng phân tích đa chiều để đưa ra quyết định sáng suốt nhất trong cuộc sống và công việc.',
                'specs' => [
                    'Tác giả' => 'Nhiều tác giả',
                    'Nhà xuất bản' => 'NXB Thế Giới - Nhã Nam',
                    'Số trang' => '288 trang',
                    'Kích thước' => '13 x 20.5 cm',
                ],
                'features' => [
                    ['title' => 'Thực Hành Trực Quan', 'desc' => 'Nhiều bài tập tình huống thực tế'],
                    ['title' => 'Vượt Qua Thiên Kiến', 'desc' => 'Nhìn nhận vấn đề khách quan hơn'],
                ],
                'variants' => [
                    'colors' => [
                        ['label' => 'Bản In Mới 2026', 'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Bìa Mềm'],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=800&q=80',
                ],
            ],
        ];

        foreach ($productsData as $data) {
            $gallery = $data['gallery'] ?? [];
            unset($data['gallery']);

            // Ensure warranty_info, policy, and faqs are always stored in database
            if (empty($data['warranty_info'])) {
                $categoryId = $data['category_id'] ?? null;
                if ($categoryId === $catPhone->id || $categoryId === $catLaptop->id) {
                    $data['warranty_info'] = 'Bảo hành 12 tháng chính hãng 1 đổi 1 trong 30 ngày nếu phát sinh lỗi phần cứng từ nhà sản xuất.';
                } elseif ($categoryId === $catFashion->id) {
                    $data['warranty_info'] = 'Bảo hành chỉ may, cúc bấm và khóa kéo 6 tháng. Hỗ trợ đổi size miễn phí trong 7 ngày.';
                } elseif ($categoryId === $catAppliances->id) {
                    $data['warranty_info'] = 'Bảo hành chính hãng 24 tháng tại các trung tâm dịch vụ khách hàng trên toàn quốc.';
                } elseif ($categoryId === $catBeauty->id) {
                    $data['warranty_info'] = 'Cam kết 100% hàng chính hãng, hạn sử dụng trên 2 năm. Đền gấp 2 lần nếu phát hiện hàng giả.';
                } else {
                    $data['warranty_info'] = 'Cam kết xuất xứ rõ ràng, chuẩn chính hãng từ nhà phân phối ủy quyền.';
                }
            }

            if (empty($data['policy'])) {
                $data['policy'] = 'Đổi trả miễn phí trong vòng 7 ngày nếu không hài lòng hoặc sản phẩm có lỗi. Hỗ trợ kiểm tra hàng đồng kiểm cùng shipper trước khi nhận.';
            }

            if (empty($data['faqs'])) {
                $productName = $data['name'];
                $data['faqs'] = [
                    [
                        'question' => "Sản phẩm {$productName} có được đồng kiểm trước khi thanh toán không?",
                        'answer' => 'Có, bạn hoàn toàn được kiểm tra ngoại quan cùng nhân viên giao hàng trước khi nhận và thanh toán.',
                    ],
                    [
                        'question' => 'Thời gian giao hàng dự kiến đến tay người nhận là bao lâu?',
                        'answer' => 'Nội thành Hà Nội & TP.HCM giao hỏa tốc từ 2h - trong ngày, các tỉnh thành khác từ 1 - 3 ngày làm việc.',
                    ],
                    [
                        'question' => 'Nếu sản phẩm gặp sự cố thì liên hệ bảo hành ở đâu?',
                        'answer' => 'Bạn có thể mang trực tiếp đến các trung tâm bảo hành của hãng hoặc gửi yêu cầu trên ShopMart để nhân viên hỗ trợ thu hồi tận nhà.',
                    ],
                ];
            }

            $product = Product::create($data);

            // Add gallery images
            foreach ($gallery as $index => $imageUrl) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $imageUrl,
                    'is_video' => false,
                    'sort_order' => $index,
                ]);
            }
        }

        // 4. Seed user 'pigku' matching Mockup 3
        $pigku = User::updateOrCreate(
            ['email' => 'pigku@gmail.com'],
            [
                'name' => 'pigku',
                'username' => 'pigku',
                'phone' => '+84 912 345 678',
                'password' => bcrypt('123456'),
                'avatar_url' => 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=400&q=80',
                'cover_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1200&q=80',
                'membership_tier' => 'Thành viên Bạc',
                'joined_date' => 'Tham gia từ 06/2024',
                'gender' => 'Chưa cập nhật',
                'birthday' => 'Chưa cập nhật',
                'coins' => 120,
                'voucher_count' => 3,
                'favorite_count' => 4,
                'order_count' => 12,
                'review_count' => 3,
            ]
        );

        // Seed sample shipping addresses for pigku
        $pigku->addresses()->delete();
        $pigku->addresses()->create([
            'recipient_name' => 'Nguyễn Văn A',
            'phone' => '(+84) 912 345 678',
            'address_line' => 'Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
            'is_default' => true,
        ]);
        $pigku->addresses()->create([
            'recipient_name' => 'Nguyễn Văn A',
            'phone' => '(+84) 912 345 678',
            'address_line' => 'Số 456 Đường Lê Lợi, Phường Đống Đa, Quận Đống Đa, Hà Nội',
            'is_default' => false,
        ]);
    }
}
