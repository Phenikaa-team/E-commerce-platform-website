<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\NavigationMenu;
use App\Services\RecommendationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('recommendations:generate {categoryId? : ID danh mục chính, bỏ trống để chạy tất cả}')]
#[Description('Tạo snapshot recommendation bằng AI hoặc fallback từ dữ liệu sản phẩm thật')]
class GenerateRecommendationsCommand extends Command
{
    public function handle(RecommendationService $recommendationService): int
    {
        $categoryId = $this->argument('categoryId');
        $categories = $categoryId
            ? Category::whereKey($categoryId)->get()
            : NavigationMenu::with('category')
                ->where('is_active', true)
                ->whereNotNull('category_id')
                ->where(function ($query) {
                    $query->whereNull('url')->orWhere('url', '!=', '__quick__');
                })
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->pluck('category')
                ->filter()
                ->unique('id')
                ->values();

        if ($categories->isEmpty()) {
            $this->error('Không tìm thấy danh mục phù hợp.');

            return self::FAILURE;
        }

        foreach ($categories as $category) {
            $this->info("Đang tạo recommendation cho: {$category->name} (#{$category->id})");
            $results = $recommendationService->generateForCategory($category->id);

            if (empty($results)) {
                $this->line('  Chưa có cột recommendation đang bật.');

                continue;
            }

            foreach ($results as $sectionKey => $result) {
                $this->line(sprintf(
                    '  %s: source=%s, bucket=%s, %d mục',
                    $sectionKey,
                    $result['source'],
                    $result['bucket'],
                    count($result['ids'])
                ));
            }
        }

        $this->info('Đã tạo recommendation snapshot thành công.');

        return self::SUCCESS;
    }
}
