<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DownloadTikiImages extends Command
{
    protected $signature = 'products:download-ecommerce-images';

    protected $description = 'Cào và download ảnh sản phẩm thực tế từ gian hàng FPT Shop về local storage';

    public function handle(): int
    {
        $storagePath = storage_path('app/public/products');
        if (! File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // Danh sách URL trang sản phẩm chính hãng hoặc direct CDN image
        $sourceUrls = [
            'iPhone 15 Pro Max 256GB Chính Hãng' => 'https://fptshop.com.vn/dien-thoai/iphone-15-pro-max',
            'Điện thoại iPhone 14 Pro Max 256GB Chính Hãng VNA' => 'https://fptshop.com.vn/dien-thoai/iphone-14-pro-max',
            'iPhone 15 128GB Chính Hãng Apple VN/A' => 'https://fptshop.com.vn/dien-thoai/iphone-15',
            'Samsung Galaxy S24 Ultra 5G' => 'https://fptshop.com.vn/dien-thoai/samsung-galaxy-s24-ultra',
            'Samsung Galaxy A55 5G 8GB/128GB' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/a/samsung-galaxy-a55_14_.png',
            'Samsung Galaxy Z Flip5 5G 256GB Gập Thời Thượng' => 'https://fptshop.com.vn/dien-thoai/samsung-galaxy-z-flip5',
            'Xiaomi Redmi Note 13 Pro 5G' => 'https://fptshop.com.vn/dien-thoai/xiaomi-redmi-note-13-pro',
            'Xiaomi Redmi 13C 6GB/128GB (Phân khúc giá rẻ)' => 'https://fptshop.com.vn/dien-thoai/xiaomi-redmi-13c',
            'OPPO Reno11 F 5G 8GB/256GB Chuyên Gia Chân Dung' => 'https://fptshop.com.vn/dien-thoai/oppo-reno11-f',
            'MacBook Air M3 13 inch 8GB 256GB' => 'https://cellphones.com.vn/macbook-air-m3-13-inch-2024.html',
            'MacBook Air M3 15 inch 16GB 512GB' => 'https://cellphones.com.vn/macbook-air-m3-15-inch-2024.html',
            'Laptop Gaming ASUS TUF Gaming F15' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_9_7.png',
            'Laptop Gaming NovaTech RTX 4060 16GB' => 'https://fptshop.com.vn/may-tinh-xach-tay/asus-tuf-gaming-a15-fa506ncr-hn047w',
            'Laptop Ultrabook 14 inch Gen 3' => 'https://fptshop.com.vn/may-tinh-xach-tay/macbook-pro-14-2023-m3-pro-12-cpu-18-gpu-18gb-1tb',
            'Tai nghe Airpod pro' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/p/apple-airpods-pro-2-usb-c_1_.png',
            'Tai nghe chụp tai chống ồn ANC' => 'https://cellphones.com.vn/tai-nghe-chup-tai-sony-wh-1000xm5.html',
            'Sạc nhanh Type-C 20W' => 'https://fptshop.com.vn/phu-kien/sac-20w-usb-c-power-adapter',
            'Củ sạc nhanh GaN 65W đa cổng' => 'https://fptshop.com.vn/phu-kien/cu-sac-nhanh-3-cong-65w-2-usb-c-usb-a-gan-ad653c-cuktech',
            'Pin sạc dự phòng không dây 10000mAh' => 'https://fptshop.com.vn/phu-kien/pin-du-phong-magsafe-anker-maggo-2-pro-a110r-10000mah-25w',
            'Bàn phím cơ gaming RGB Pro' => 'https://fptshop.com.vn/phu-kien/ban-phim-gaming-co-day-asus-tuf-k1',
            'Bàn phím cơ compact silent switch' => 'https://fptshop.com.vn/phu-kien/ban-phim-bluetooth-logitech-k380',
            'Bàn phím cơ văn phòng switch nhẹ' => 'https://fptshop.com.vn/phu-kien/ban-phim-bluetooth-logitech-k380',
            'Chuột không dây Silent Click Pro' => 'https://fptshop.com.vn/phu-kien/chuot-khong-day-logitech-m171',
            'Kem nền mỏng nhẹ tự nhiên SPF20' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80',
            'Serum Vitamin C dưỡng sáng da 30ml' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
            'Serum phục hồi da nhạy cảm 30ml' => 'https://hasaki.vn/san-pham/serum-la-roche-posay-giup-tai-tao-phuc-hoi-da-30ml-80155.html',
            'Kem chống nắng SPF50+ cho da nhạy cảm' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
            'Kem chống nắng dạng gel SPF50+' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
            'Kem dưỡng ẩm chuyên sâu 50ml' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=800&q=80',
            'Sữa rửa mặt dịu nhẹ phục hồi da' => 'https://cdn.nhathuoclongchau.com.vn/unsafe/800x0/filters:quality(90):format(webp)/00502145_sua_rua_mat_diu_nhe_khong_xa_phong_cetaphil_gentle_skin_cleanser_new_500ml_7019_6335_large_ab2ba68ac4.jpg',
            'Son môi lì chuẩn màu lâu trôi' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
            'Nước hoa nữ EDP hương hoa thanh lịch' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=800&q=80',
            'Áo khoác hoodie nỉ unisex basic' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=800&q=80',
            'Áo hoodie basic unisex form rộng' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=800&q=80',
            'Túi đeo chéo canvas đa năng' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80',
            'Giày chạy bộ nam nữ Pro Move' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
            'Giày sneaker trắng unisex Urban Run' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=800&q=80',
            'Giày sneaker casual đế êm Daily Walk' => 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?auto=format&fit=crop&w=800&q=80',
            'Ốp lưng chống sốc iPhone 15 Pro' => 'https://images.unsplash.com/photo-1601593346740-925612772716?auto=format&fit=crop&w=800&q=80',
            'Hub USB-C 7 trong 1 cho laptop' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
            'Màn hình 4K 27 inch viền mỏng' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80',
        ];

        foreach ($sourceUrls as $productName => $pageUrl) {
            $this->line("Đang cào ảnh cho: {$productName}...");
            $product = Product::where('name', $productName)->first();
            if (! $product) {
                continue;
            }

            try {
                $imageUrl = null;
                if (preg_match('/\.(jpg|jpeg|png|webp|gif)($|\?)/i', $pageUrl) || str_contains($pageUrl, 'unsplash.com') || str_contains($pageUrl, 'cdn')) {
                    $imageUrl = $pageUrl;
                } else {
                    $response = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    ])->timeout(10)->get($pageUrl);

                    if (! $response->successful()) {
                        $this->warn(" -> Không tải được trang (HTTP {$response->status()})");

                        continue;
                    }

                    $html = $response->body();

                    if (preg_match('/<meta[^>]+property=[\'"]og:image[\'"][^>]+content=[\'"]([^\'"]+)[\'"]/i', $html, $m)) {
                        $imageUrl = $m[1];
                    } elseif (preg_match('/<meta[^>]+content=[\'"]([^\'"]+)[\'"][^>]+property=[\'"]og:image[\'"]/i', $html, $m)) {
                        $imageUrl = $m[1];
                    }
                }

                if ($imageUrl) {
                    $this->info(" -> Tìm thấy ảnh: {$imageUrl}");

                    $imgResp = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    ])->timeout(15)->get($imageUrl);

                    if ($imgResp->successful() && strlen($imgResp->body()) > 2000) {
                        $ext = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'webp';
                        $filename = Str::uuid()->toString().'.'.$ext;
                        File::put($storagePath.'/'.$filename, $imgResp->body());

                        $localUrl = '/storage/products/'.$filename;
                        $product->update([
                            'main_image_url' => $localUrl,
                        ]);
                        $this->info(" -> [THÀNH CÔNG] Đã lưu vào local: {$localUrl}");
                    } else {
                        $this->warn(" -> Không tải được ảnh! Status: {$imgResp->status()}, Size: ".strlen($imgResp->body()));
                    }
                }
            } catch (\Throwable $e) {
                $this->error(' -> Lỗi: '.$e->getMessage());
            }

            usleep(200000);
        }

        $this->info('Hoàn tất tải ảnh chuẩn từ sàn TMĐT!');

        return self::SUCCESS;
    }
}
