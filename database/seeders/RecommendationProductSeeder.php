<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Seeder;

class RecommendationProductSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::query()->where('status', 'active')->orderBy('id')->first();

        if (! $store) {
            return;
        }

        $placeholderImage = asset('images/placeholders/product-placeholder.svg');

        $products = [
            ['slug' => 'rec-test-iphone-15-pro-case', 'name' => 'Ốp lưng chống sốc iPhone 15 Pro', 'category' => 'op-lung-va-bao-da', 'brand' => 'TestCase', 'price' => 189000, 'image' => 'https://images.unsplash.com/photo-1603313011107-5b2b3c7b8b5a?auto=format&fit=crop&w=800&q=80', 'tags' => ['iphone', 'phu kien', 'bao ve dien thoai']],
            ['slug' => 'rec-test-android-fast-charger', 'name' => 'Củ sạc nhanh GaN 65W đa cổng', 'category' => 'sac-nhanh', 'brand' => 'ChargeLab', 'price' => 459000, 'image' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=800&q=80', 'tags' => ['sac nhanh', 'phu kien', 'cong nghe']],
            ['slug' => 'rec-test-wireless-power-bank', 'name' => 'Pin sạc dự phòng không dây 10000mAh', 'category' => 'pin-du-phong', 'brand' => 'ChargeLab', 'price' => 599000, 'image' => 'https://images.unsplash.com/photo-1609592424847-7c9c5d5a1f4d?auto=format&fit=crop&w=800&q=80', 'tags' => ['pin du phong', 'khong day', 'cong nghe']],
            ['slug' => 'rec-test-ultrabook-14', 'name' => 'Laptop Ultrabook 14 inch Gen 3', 'category' => 'laptop-va-cac-thiet-bi-so', 'brand' => 'NovaTech', 'price' => 15990000, 'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80', 'tags' => ['laptop', 'van phong', 'mang nhe']],
            ['slug' => 'rec-test-gaming-laptop-flash', 'name' => 'Laptop Gaming NovaTech RTX 4060 16GB', 'category' => 'laptop-va-cac-thiet-bi-so', 'brand' => 'NovaTech', 'price' => 18990000, 'original_price' => 24990000, 'flash_sale_percent' => 24, 'is_flash_sale' => true, 'image' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80', 'tags' => ['laptop', 'gaming', 'rtx 4060']],
            ['slug' => 'rec-test-macbook-air-flash', 'name' => 'MacBook Air M3 15 inch 16GB 512GB', 'category' => 'macbook-m3', 'brand' => 'Apple', 'price' => 24990000, 'original_price' => 29990000, 'flash_sale_percent' => 17, 'is_flash_sale' => true, 'image' => 'https://images.unsplash.com/photo-1517336714739-489689fd1ca8?auto=format&fit=crop&w=800&q=80', 'tags' => ['laptop', 'macbook', 'apple', 'van phong']],
            ['slug' => 'rec-test-4k-monitor-flash', 'name' => 'Màn hình 4K 27 inch viền mỏng', 'category' => 'man-hinh-4k', 'brand' => 'ViewMax', 'price' => 5990000, 'original_price' => 7490000, 'flash_sale_percent' => 20, 'is_flash_sale' => true, 'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80', 'tags' => ['man hinh', '4k', 'van phong', 'gaming']],
            ['slug' => 'rec-test-wireless-mouse-pro', 'name' => 'Chuột không dây Silent Click Pro', 'category' => 'chuot-khong-day', 'brand' => 'KeyCraft', 'price' => 349000, 'image' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=800&q=80', 'tags' => ['chuot khong day', 'phu kien may tinh', 'silent click']],
            ['slug' => 'rec-test-mechanical-keyboard-office', 'name' => 'Bàn phím cơ văn phòng switch nhẹ', 'category' => 'ban-phim-co', 'brand' => 'KeyCraft', 'price' => 790000, 'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80', 'tags' => ['ban phim co', 'phu kien may tinh', 'van phong']],
            ['slug' => 'rec-test-usbc-hub-7in1', 'name' => 'Hub USB-C 7 trong 1 cho laptop', 'category' => 'laptop-va-cac-thiet-bi-so', 'brand' => 'TechLink', 'price' => 499000, 'image' => 'https://images.unsplash.com/photo-1625842268584-8f3296236761?auto=format&fit=crop&w=800&q=80', 'tags' => ['hub usb c', 'phu kien may tinh', 'laptop']],
            ['slug' => 'rec-test-gaming-keyboard', 'name' => 'Bàn phím cơ gaming RGB Pro', 'category' => 'ban-phim-co', 'brand' => 'KeyCraft', 'price' => 890000, 'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80', 'tags' => ['gaming', 'ban phim co', 'rgb']],
            ['slug' => 'rec-test-anc-headphones', 'name' => 'Tai nghe chụp tai chống ồn ANC', 'category' => 'tai-nghe', 'brand' => 'SoundLab', 'price' => 1290000, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80', 'tags' => ['tai nghe', 'chong on', 'am thanh']],
            ['slug' => 'rec-test-mechanical-keyboard-compact', 'name' => 'Bàn phím cơ compact silent switch', 'category' => 'ban-phim-co', 'brand' => 'KeyCraft', 'price' => 1190000, 'image' => 'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&w=800&q=80', 'tags' => ['gaming', 'ban phim co', 'van phong']],
            ['slug' => 'rec-test-sensitive-skin-serum', 'name' => 'Serum phục hồi da nhạy cảm 30ml', 'category' => 'lam-dep-va-suc-khoe', 'brand' => 'PureSkin', 'price' => 379000, 'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80', 'tags' => ['cham soc da', 'da nhay cam', 'serum']],
            ['slug' => 'rec-test-sunscreen-gel', 'name' => 'Kem chống nắng dạng gel SPF50+', 'category' => 'lam-dep-va-suc-khoe', 'brand' => 'PureSkin', 'price' => 289000, 'image' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&w=800&q=80', 'tags' => ['cham soc da', 'kem chong nang', 'da dau']],
            ['slug' => 'rec-test-gentle-cleansing-foam', 'name' => 'Sữa rửa mặt dịu nhẹ phục hồi da', 'category' => 'lam-dep-va-suc-khoe', 'brand' => 'PureSkin', 'price' => 219000, 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80', 'tags' => ['cham soc da', 'sua rua mat', 'skincare']],
            ['slug' => 'rec-test-hydrating-moisturizer', 'name' => 'Kem dưỡng ẩm chuyên sâu 50ml', 'category' => 'lam-dep-va-suc-khoe', 'brand' => 'PureSkin', 'price' => 329000, 'image' => 'https://images.unsplash.com/photo-1611930022073-b7a4ba5fcccd?auto=format&fit=crop&w=800&q=80', 'tags' => ['cham soc da', 'duong am', 'skincare']],
            ['slug' => 'rec-test-matte-lipstick', 'name' => 'Son môi lì chuẩn màu lâu trôi', 'category' => 'lam-dep-va-suc-khoe', 'brand' => 'Lumière Beauty', 'price' => 269000, 'image' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80', 'tags' => ['trang diem', 'son', 'makeup']],
            ['slug' => 'rec-test-edp-perfume', 'name' => 'Nước hoa nữ EDP hương hoa thanh lịch', 'category' => 'lam-dep-va-suc-khoe', 'brand' => 'Lumière Beauty', 'price' => 799000, 'image' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=800&q=80', 'tags' => ['trang diem', 'nuoc hoa', 'perfume']],
            ['slug' => 'rec-test-natural-foundation', 'name' => 'Kem nền mỏng nhẹ tự nhiên SPF20', 'category' => 'lam-dep-va-suc-khoe', 'brand' => 'Lumière Beauty', 'price' => 389000, 'image' => 'https://images.unsplash.com/photo-1631214524020-7e18db9a8f92?auto=format&fit=crop&w=800&q=80', 'tags' => ['trang diem', 'kem nen', 'makeup']],
            ['slug' => 'rec-test-white-running-shoes', 'name' => 'Giày sneaker trắng unisex Urban Run', 'category' => 'giay-sneaker', 'brand' => 'UrbanStep', 'price' => 699000, 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80', 'tags' => ['giay sneaker', 'giay the thao', 'unisex']],
            ['slug' => 'rec-test-casual-sneakers', 'name' => 'Giày sneaker casual đế êm Daily Walk', 'category' => 'giay-sneaker', 'brand' => 'UrbanStep', 'price' => 549000, 'image' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?auto=format&fit=crop&w=800&q=80', 'tags' => ['giay sneaker', 'giay casual', 'di hang ngay']],
            ['slug' => 'rec-test-running-shoes-pro', 'name' => 'Giày chạy bộ nam nữ Pro Move', 'category' => 'giay-sneaker', 'brand' => 'MoveFit', 'price' => 899000, 'image' => 'https://images.unsplash.com/photo-1552346154-21d32810aba3?auto=format&fit=crop&w=800&q=80', 'tags' => ['giay sneaker', 'giay chay bo', 'the thao']],
            ['slug' => 'rec-test-hoodie-basic', 'name' => 'Áo hoodie basic unisex form rộng', 'category' => 'thoi-trang-va-phu-kien', 'brand' => 'UrbanBasic', 'price' => 349000, 'image' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=800&q=80', 'tags' => ['thoi trang', 'hoodie', 'unisex']],
            ['slug' => 'rec-test-crossbody-bag', 'name' => 'Túi đeo chéo canvas đa năng', 'category' => 'thoi-trang-va-phu-kien', 'brand' => 'UrbanBasic', 'price' => 259000, 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80', 'tags' => ['thoi trang', 'tui deo cheo', 'phu kien']],
        ];

        foreach ($products as $data) {
            $category = Category::query()->where('slug', $data['category'])->first();

            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'store_id' => $store->id,
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'brand' => $data['brand'],
                    'description' => 'Sản phẩm dữ liệu thô dùng để kiểm thử recommendation. Chưa phát sinh giao dịch thực tế.',
                    'main_image_url' => $data['image'] ?? $placeholderImage,
                    'banner_image_url' => $data['image'] ?? $placeholderImage,
                    'price' => $data['price'],
                    'original_price' => $data['original_price'] ?? $data['price'],
                    'discount_percent' => $data['flash_sale_percent'] ?? 0,
                    'rating' => 5.0,
                    'reviews_count' => 0,
                    'sold_count' => 0,
                    'stock' => 100,
                    'status' => 'active',
                    'is_mall' => false,
                    'is_flash_sale' => $data['is_flash_sale'] ?? false,
                    'flash_sale_percent' => $data['flash_sale_percent'] ?? 0,
                    'ai_metadata' => ['tags' => $data['tags'], 'source' => 'recommendation-test-data'],
                    'ai_analysis_status' => 'completed',
                ]
            );
        }
    }
}
