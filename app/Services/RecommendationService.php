<?php

namespace App\Services;

use App\Models\NavigationSection;
use App\Models\RecommendationSnapshot;
use Illuminate\Support\Str;

class RecommendationService
{
    public function __construct(private readonly RecommendationCandidateService $candidateService) {}

    /** Generate snapshots for all active AI columns in one category. */
    public function generateForCategory(int $categoryId): array
    {
        $candidates = $this->candidateService->buildForCategory($categoryId);
        $sections = NavigationSection::query()
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->where('recommendation_enabled', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $results = [];
        foreach ($sections as $section) {
            $bucket = $this->bucketForSection($section->section_key, $section->title);
            $source = 'fallback';
            $selectedIds = $this->candidateService->fallbackIds($candidates, $bucket, $section->item_limit);

            $items = $this->displayItems($candidates[$bucket] ?? [], $selectedIds, $bucket);
            RecommendationSnapshot::updateOrCreate(
                ['category_id' => $categoryId, 'section_key' => $section->section_key],
                [
                    'payload' => ['items' => $items, 'candidate_count' => count($candidates[$bucket] ?? [])],
                    'source' => $source,
                    'generated_at' => now(),
                    'expires_at' => now()->addHours(24),
                ]
            );

            $results[$section->section_key] = ['source' => $source, 'bucket' => $bucket, 'ids' => $selectedIds];
        }

        return $results;
    }

    private function bucketForSection(string $key, string $title): string
    {
        $value = Str::lower($key.' '.$title);

        return match (true) {
            Str::contains($value, ['brand', 'thương hiệu']) => 'brands',
            Str::contains($value, ['series', 'dòng sản phẩm', 'dòng']) => 'series',
            Str::contains($value, ['accessory', 'phụ kiện']) => 'accessories',
            default => 'products',
        };
    }

    private function displayItems(array $candidates, array $selectedIds, string $bucket): array
    {
        $lookup = collect($candidates)->keyBy(fn (array $item) => (string) $item['id']);

        return collect($selectedIds)
            ->map(fn ($id) => $lookup->get((string) $id))
            ->filter()
            ->map(function (array $item) use ($bucket): array {
                $url = match ($bucket) {
                    'brands' => '/brand/'.Str::slug($item['name']),
                    'series' => '/search?q='.urlencode($item['name']),
                    default => isset($item['slug']) ? route('product.detail', $item['slug']) : '/search?q='.urlencode($item['name']),
                };

                return ['id' => $item['id'], 'name' => $item['name'], 'url' => $url, 'item_type' => rtrim($bucket, 's')];
            })
            ->values()
            ->all();
    }
}
