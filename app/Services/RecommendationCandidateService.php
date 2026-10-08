<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RecommendationCandidateService
{
    /**
     * Build a whitelist of real database candidates for one main category.
     * The AI must choose from these values and may not invent marketplace data.
     */
    public function buildForCategory(int $categoryId, int $limit = 100): array
    {
        $products = Product::query()
            ->with('category')
            ->where('status', 'active')
            ->where(function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId)
                    ->orWhereHas('category', fn ($category) => $category->where('parent_id', $categoryId));
            })
            ->orderByDesc('sold_count')
            ->orderByDesc('rating')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return [
            'category_id' => $categoryId,
            'products' => $products->map(fn (Product $product) => $this->productCandidate($product))->values()->all(),
            'brands' => $this->brandCandidates($products),
            'series' => $this->seriesCandidates($products),
            'accessories' => $this->accessoryCandidates($products),
        ];
    }

    /** Return deterministic fallback IDs when the AI is unavailable. */
    public function fallbackIds(array $candidates, string $sectionKey, int $limit = 6): array
    {
        return collect($candidates[$sectionKey] ?? [])
            ->sortByDesc(fn (array $item) => [(int) ($item['sold_count'] ?? 0), (float) ($item['rating'] ?? 0)])
            ->take($limit)
            ->pluck('id')
            ->values()
            ->all();
    }

    /** Keep only IDs that were present in the original candidate whitelist. */
    public function validateIds(array $candidates, string $sectionKey, mixed $ids, int $limit = 6): array
    {
        $allowed = collect($candidates[$sectionKey] ?? [])
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        if (! is_array($ids)) {
            return [];
        }

        return collect($ids)
            ->map(fn ($id) => (string) $id)
            ->filter(fn (string $id) => in_array($id, $allowed, true))
            ->unique()
            ->take($limit)
            ->values()
            ->all();
    }

    private function brandCandidates(Collection $products): array
    {
        return $products
            ->filter(fn (Product $product) => filled($product->brand))
            ->groupBy(fn (Product $product) => Str::slug($product->brand))
            ->map(function (Collection $items, string $brandKey): array {
                return [
                    'id' => 'brand:'.$brandKey,
                    'name' => $items->first()->brand,
                    'product_count' => $items->count(),
                    'sold_count' => (int) $items->sum('sold_count'),
                    'rating' => round((float) $items->avg('rating'), 1),
                    'product_ids' => $items->pluck('id')->values()->all(),
                ];
            })
            ->sortByDesc('sold_count')
            ->values()
            ->all();
    }

    private function seriesCandidates(Collection $products): array
    {
        return $products
            ->map(function (Product $product): ?array {
                $metadata = is_array($product->ai_metadata) ? $product->ai_metadata : [];
                $series = $metadata['series'] ?? $metadata['product_type'] ?? $product->name;

                if (! is_string($series) || trim($series) === '') {
                    return null;
                }

                return ['key' => Str::slug($series), 'name' => trim($series), 'product' => $product];
            })
            ->filter()
            ->groupBy('key')
            ->map(function (Collection $items, string $seriesKey): array {
                return [
                    'id' => 'series:'.$seriesKey,
                    'name' => $items->first()['name'],
                    'product_count' => $items->count(),
                    'sold_count' => (int) $items->sum(fn (array $item) => $item['product']->sold_count),
                    'rating' => round((float) $items->avg(fn (array $item) => $item['product']->rating), 1),
                    'product_ids' => $items->pluck('product.id')->values()->all(),
                ];
            })
            ->sortByDesc('sold_count')
            ->values()
            ->all();
    }

    private function accessoryCandidates(Collection $products): array
    {
        return $products
            ->filter(fn (Product $product) => $this->looksLikeAccessory($product))
            ->map(fn (Product $product) => $this->productCandidate($product))
            ->values()
            ->all();
    }

    private function productCandidate(Product $product): array
    {
        $metadata = is_array($product->ai_metadata) ? $product->ai_metadata : [];

        return [
            'id' => (int) $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'brand' => $product->brand,
            'category_id' => (int) $product->category_id,
            'sold_count' => (int) $product->sold_count,
            'rating' => (float) $product->rating,
            'tags' => array_values(array_filter($metadata['tags'] ?? [], 'is_string')),
        ];
    }

    private function looksLikeAccessory(Product $product): bool
    {
        $metadata = is_array($product->ai_metadata) ? $product->ai_metadata : [];
        $text = Str::lower(implode(' ', array_filter([
            $product->name,
            $metadata['product_type'] ?? null,
            implode(' ', $metadata['tags'] ?? []),
        ])));

        foreach (['phụ kiện', 'ốp', 'sạc', 'cáp', 'tai nghe', 'chuột', 'bàn phím', 'bao da', 'case', 'charger', 'cable', 'headphone'] as $keyword) {
            if (Str::contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
