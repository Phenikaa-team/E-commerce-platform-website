<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductImagesUpdateSeeder extends Seeder
{
    /**
     * Cập nhật toàn bộ ảnh đại diện sản phẩm sang chuẩn chụp studio / nền sạch TMĐT chuyên nghiệp
     */
    public function run(): void
    {
        $imageMapping = [
            // --- 1. ĐIỆN THOẠI & TABLET ---
            'iPhone 15 Pro Max 256GB Chính Hãng' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=800&q=80',
            'Điện thoại iPhone 14 Pro Max 256GB Chính Hãng VNA' => 'https://images.unsplash.com/photo-1696446701796-da61225697cc?auto=format&fit=crop&w=800&q=80',
            'iPhone 15 128GB Chính Hãng Apple VN/A' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?auto=format&fit=crop&w=800&q=80',
            'Samsung Galaxy S24 Ultra 5G' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80',
            'Samsung Galaxy A55 5G 8GB/128GB' => 'https://images.unsplash.com/photo-1580910051074-3eb694886505?auto=format&fit=crop&w=800&q=80',
            'Samsung Galaxy Z Flip5 5G 256GB Gập Thời Thượng' => 'https://images.unsplash.com/photo-1585060544812-6b45742d762f?auto=format&fit=crop&w=800&q=80',
            'Xiaomi Redmi Note 13 Pro 5G' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=800&q=80',
            'Xiaomi Redmi 13C 6GB/128GB (Phân khúc giá rẻ)' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02560?auto=format&fit=crop&w=800&q=80',
            'OPPO Reno11 F 5G 8GB/256GB Chuyên Gia Chân Dung' => 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?auto=format&fit=crop&w=800&q=80',

            // --- 2. TAI NGHE & ÂM THANH ---
            'Tai nghe Airpod pro' => 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?auto=format&fit=crop&w=800&q=80',
            'Tai nghe chụp tai chống ồn ANC' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',

            // --- 3. PHỤ KIỆN ĐIỆN THOẠI & SẠC ---
            'Sạc nhanh Type-C 20W' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=800&q=80',
            'Củ sạc nhanh GaN 65W đa cổng' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=800&q=80',
            'Pin sạc dự phòng không dây 10000mAh' => 'https://images.unsplash.com/photo-1609592424847-7c9c5d5a1f4d?auto=format&fit=crop&w=800&q=80',
            'Ốp lưng chống sốc iPhone 15 Pro' => 'https://images.unsplash.com/photo-1603313011107-5b2b3c7b8b5a?auto=format&fit=crop&w=800&q=80',
            'Hub USB-C 7 trong 1 cho laptop' => 'https://images.unsplash.com/photo-1625842268584-8f3296236761?auto=format&fit=crop&w=800&q=80',

            // --- 4. LAPTOP & MÀN HÌNH ---
            'MacBook Air M3 13 inch 8GB 256GB' => 'https://images.unsplash.com/photo-1517336714739-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
            'MacBook Air M3 15 inch 16GB 512GB' => 'https://images.unsplash.com/photo-1517336714739-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
            'Laptop Gaming ASUS TUF Gaming F15' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80',
            'Laptop Gaming NovaTech RTX 4060 16GB' => 'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?auto=format&fit=crop&w=800&q=80',
            'Laptop Ultrabook 14 inch Gen 3' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80',
            'Màn hình 4K 27 inch viền mỏng' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80',

            // --- 5. BÀN PHÍM & CHUỘT ---
            'Bàn phím cơ compact silent switch' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
            'Bàn phím cơ gaming RGB Pro' => 'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&w=800&q=80',
            'Bàn phím cơ văn phòng switch nhẹ' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
            'Chuột không dây Silent Click Pro' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=800&q=80',

            // --- 6. MỸ PHẨM & CHĂM SÓC DA ---
            'Serum Vitamin C dưỡng sáng da 30ml' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
            'Serum phục hồi da nhạy cảm 30ml' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
            'Kem chống nắng SPF50+ cho da nhạy cảm' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&w=800&q=80',
            'Kem chống nắng dạng gel SPF50+' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&w=800&q=80',
            'Sữa rửa mặt dịu nhẹ phục hồi da' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
            'Kem dưỡng ẩm chuyên sâu 50ml' => 'https://images.unsplash.com/photo-1611930022073-b7a4ba5fcccd?auto=format&fit=crop&w=800&q=80',
            'Son môi lì chuẩn màu lâu trôi' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
            'Nước hoa nữ EDP hương hoa thanh lịch' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=800&q=80',
            'Kem nền mỏng nhẹ tự nhiên SPF20' => 'https://images.unsplash.com/photo-1631214524020-7e18db9a8f92?auto=format&fit=crop&w=800&q=80',

            // --- 7. THỜI TRANG & GIÀY DÉP ---
            'Áo khoác hoodie nỉ unisex basic' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=800&q=80',
            'Áo hoodie basic unisex form rộng' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=800&q=80',
            'Túi đeo chéo canvas đa năng' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80',
            'Giày sneaker trắng unisex Urban Run' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
            'Giày sneaker casual đế êm Daily Walk' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?auto=format&fit=crop&w=800&q=80',
            'Giày chạy bộ nam nữ Pro Move' => 'https://images.unsplash.com/photo-1552346154-21d32810aba3?auto=format&fit=crop&w=800&q=80',
        ];

        foreach ($imageMapping as $name => $imageUrl) {
            Product::where('name', $name)->update([
                'main_image_url' => $imageUrl,
            ]);
        }
    }
}
