<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Signature('images:migrate-to-local {--force : Tải lại cả những ảnh đã lưu local}')]
#[Description('Tải tất cả ảnh external (Unsplash, UI-Avatars) về lưu trực tiếp trong storage local và cập nhật đường dẫn database')]
class MigrateImagesToStorageCommand extends Command
{
    public function handle(): int
    {
        $this->info('=== BẮT ĐẦU CHUYỂN ĐỔI TOÀN BỘ ẢNH EXTERNAL VỀ STORAGE LOCAL ===');

        $this->migrateProducts();
        $this->migrateProductImages();
        $this->migrateStores();
        $this->migrateUsers();

        $this->newLine();
        $this->info('✓ Hoàn tất di chuyển và đồng bộ dữ liệu ảnh sang Storage Local!');

        return Command::SUCCESS;
    }

    protected function migrateProducts(): void
    {
        $products = Product::where('main_image_url', 'like', 'http%')->get();
        $this->info("Đang xử lý {$products->count()} ảnh chính sản phẩm...");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $localPath = $this->downloadOrGenerateImage(
                $product->main_image_url,
                'products',
                $product->name,
                '#3b82f6'
            );

            if ($localPath) {
                $product->update(['main_image_url' => $localPath]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    protected function migrateProductImages(): void
    {
        $images = ProductImage::where('image_url', 'like', 'http%')->get();
        $this->info("Đang xử lý {$images->count()} ảnh thư viện sản phẩm (ProductImage)...");

        $bar = $this->output->createProgressBar($images->count());
        $bar->start();

        foreach ($images as $img) {
            $localPath = $this->downloadOrGenerateImage(
                $img->image_url,
                'products',
                'Product Gallery',
                '#10b981'
            );

            if ($localPath) {
                $img->update(['image_url' => $localPath]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    protected function migrateStores(): void
    {
        $stores = Store::all();
        $this->info("Đang xử lý ảnh cửa hàng (Logo & Banner) cho {$stores->count()} store...");

        foreach ($stores as $store) {
            $storeName = (string) ($store->name ?? 'Store');
            if ($store->logo_url && str_starts_with($store->logo_url, 'http')) {
                $localLogo = $this->downloadOrGenerateImage(
                    $store->logo_url,
                    'stores/logos',
                    $storeName,
                    '#f59e0b'
                );
                if ($localLogo) {
                    $store->update(['logo_url' => $localLogo]);
                }
            }

            if ($store->banner_url && str_starts_with($store->banner_url, 'http')) {
                $localBanner = $this->downloadOrGenerateImage(
                    $store->banner_url,
                    'stores/banners',
                    $storeName.' Banner',
                    '#6366f1'
                );
                if ($localBanner) {
                    $store->update(['banner_url' => $localBanner]);
                }
            }
        }
    }

    protected function migrateUsers(): void
    {
        $users = User::where('avatar_url', 'like', 'http%')->get();
        $this->info("Đang xử lý {$users->count()} avatar người dùng...");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $userName = (string) ($user->name ?? 'User');
            $localAvatar = $this->downloadOrGenerateImage(
                $user->avatar_url,
                'avatars',
                $userName,
                '#8b5cf6'
            );

            if ($localAvatar) {
                $user->update(['avatar_url' => $localAvatar]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Tải ảnh từ URL hoặc tạo file ảnh placeholder local nếu mạng không tải được.
     */
    protected function downloadOrGenerateImage(string $url, string $folder, string $label, string $color): ?string
    {
        $filename = Str::uuid();

        try {
            $response = Http::timeout(8)->withoutVerifying()->get($url);

            if ($response->successful() && strlen($response->body()) > 100) {
                $contentType = $response->header('Content-Type') ?? '';
                $extension = 'jpg';
                if (str_contains($contentType, 'png')) {
                    $extension = 'png';
                } elseif (str_contains($contentType, 'webp')) {
                    $extension = 'webp';
                } elseif (str_contains($contentType, 'svg')) {
                    $extension = 'svg';
                }

                $path = "{$folder}/{$filename}.{$extension}";
                Storage::disk('public')->put($path, $response->body());

                return "/storage/{$path}";
            }
        } catch (\Throwable $e) {
            // Trường hợp không có mạng hoặc request fail, fallback tạo placeholder SVG local chất lượng cao
        }

        // Fallback: Tạo SVG vector sắc nét lưu trực tiếp vào local storage
        $escapedLabel = htmlspecialchars(mb_substr($label, 0, 30), ENT_QUOTES, 'UTF-8');
        $initials = htmlspecialchars(mb_strtoupper(mb_substr($label, 0, 2)), ENT_QUOTES, 'UTF-8');

        $svgContent = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$color};stop-opacity:1" />
      <stop offset="100%" style="stop-color:#1e293b;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="600" height="600" fill="url(#grad)" />
  <circle cx="300" cy="260" r="110" fill="rgba(255,255,255,0.15)" />
  <text x="300" y="295" font-size="85" font-weight="bold" font-family="system-ui, -apple-system, sans-serif" fill="#ffffff" text-anchor="middle">{$initials}</text>
  <text x="300" y="440" font-size="28" font-weight="600" font-family="system-ui, -apple-system, sans-serif" fill="#ffffff" text-anchor="middle">{$escapedLabel}</text>
  <text x="300" y="480" font-size="16" font-family="system-ui, -apple-system, sans-serif" fill="rgba(255,255,255,0.7)" text-anchor="middle">ShopMart Local Storage</text>
</svg>
SVG;

        $path = "{$folder}/{$filename}.svg";
        Storage::disk('public')->put($path, $svgContent);

        return "/storage/{$path}";
    }
}
