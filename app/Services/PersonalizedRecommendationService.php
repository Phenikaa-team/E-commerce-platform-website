<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\RecommendationEvent;
use App\Models\User;
use App\Models\UserRecommendationSnapshot;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PersonalizedRecommendationService
{
    private const EVENT_WEIGHTS = [
        'purchase' => 10.0,
        'wishlist' => 8.0,
        'cart' => 6.0,
        'view' => 2.0,
        'search' => 1.5,
    ];

    public function track(?User $user, string $eventType, ?Product $product = null, array $metadata = []): void
    {
        if (! $user || ! isset(self::EVENT_WEIGHTS[$eventType])) {
            return;
        }

        $duplicateWindow = $eventType === 'view' ? 30 : 5;
        $alreadyTracked = RecommendationEvent::query()
            ->where('user_id', $user->id)
            ->where('event_type', $eventType)
            ->where('product_id', $product?->id)
            ->where('created_at', '>=', now()->subMinutes($duplicateWindow))
            ->exists();

        if ($alreadyTracked) {
            return;
        }

        RecommendationEvent::create([
            'user_id' => $user->id,
            'product_id' => $product?->id,
            'category_id' => $product?->category_id,
            'event_type' => $eventType,
            'weight' => self::EVENT_WEIGHTS[$eventType],
            'metadata' => $metadata ?: null,
            'session_id' => request()->hasSession() ? request()->session()->getId() : null,
        ]);

        UserRecommendationSnapshot::where('user_id', $user->id)->delete();
    }

    public function recommend(?User $user, int $limit = 24, ?int $excludeProductId = null, string $context = 'home'): Collection
    {
        if (! $user) {
            return $this->popular($limit, $excludeProductId);
        }

        $snapshot = UserRecommendationSnapshot::query()
            ->where('user_id', $user->id)
            ->where('context', $context)
            ->where('expires_at', '>', now())
            ->first();

        if ($snapshot && ! $excludeProductId) {
            $ids = collect(data_get($snapshot->payload, 'items', []))->pluck('product_id')->all();
            if ($ids) {
                return Product::with(['category', 'images', 'store'])
                    ->where('status', 'active')
                    ->whereIn('id', $ids)
                    ->get()
                    ->sortBy(fn (Product $product) => array_search($product->id, $ids, true))
                    ->values();
            }
        }

        $events = RecommendationEvent::query()
            ->with('product.category')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(180))
            ->latest()
            ->limit(500)
            ->get();

        $purchasedIds = Order::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['processing', 'shipping', 'completed'])
            ->with('items:id,order_id,product_id')
            ->get()
            ->flatMap(fn (Order $order) => $order->items->pluck('product_id'))
            ->map(fn ($id) => (int) $id)
            ->unique();

        $profile = $this->buildProfile($events);
        $query = Product::with(['category', 'images', 'store'])
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->where('is_flash_sale', false);

        if ($excludeProductId) {
            $query->where('id', '!=', $excludeProductId);
        }

        $candidates = $query->orderByDesc('sold_count')->limit(300)->get();
        $scored = $candidates
            ->reject(fn (Product $product) => $purchasedIds->contains($product->id))
            ->map(function (Product $product) use ($profile): array {
                $categoryScore = $this->profileScore($profile['categories'], (string) ($product->category_id ?? ''));
                $brandScore = $this->profileScore($profile['brands'], Str::lower(trim((string) $product->brand)));
                $tagScore = collect($this->tags($product))->sum(fn (string $tag) => $this->profileScore($profile['tags'], $tag));
                $priceScore = $this->priceScore((float) $product->price, $profile['average_price']);
                $popularScore = min(1, ((int) $product->sold_count / 500) * 0.7 + ((float) $product->rating / 5) * 0.3);
                $score = ($categoryScore * 35) + ($brandScore * 25) + (min(1, $tagScore) * 20) + ($priceScore * 10) + ($popularScore * 10);

                return [
                    'product' => $product,
                    'score' => round($score, 4),
                    'reason' => $this->reason($categoryScore, $brandScore, $tagScore, $priceScore),
                ];
            })
            ->sortByDesc(fn (array $item) => [$item['score'], $item['product']->sold_count, $item['product']->rating])
            ->take($limit)
            ->values();

        if ($scored->isEmpty()) {
            return $this->popular($limit, $excludeProductId);
        }

        UserRecommendationSnapshot::updateOrCreate(
            ['user_id' => $user->id, 'context' => $context],
            [
                'payload' => [
                    'items' => $scored->map(fn (array $item) => [
                        'product_id' => $item['product']->id,
                        'score' => $item['score'],
                        'reason' => $item['reason'],
                    ])->all(),
                    'profile' => $profile['summary'],
                ],
                'generated_at' => now(),
                'expires_at' => now()->addMinutes(30),
            ]
        );

        return $scored->map(fn (array $item) => $item['product'])->values();
    }

    private function buildProfile(Collection $events): array
    {
        $categories = [];
        $brands = [];
        $tags = [];
        $prices = [];
        $totalWeight = 0.0;

        foreach ($events as $event) {
            $product = $event->product;
            if (! $product) {
                continue;
            }

            $daysOld = max(0, now()->diffInDays($event->created_at));
            $weight = (float) $event->weight * max(0.25, 1 - ($daysOld / 180));
            $totalWeight += $weight;
            $categories[(string) $product->category_id] = ($categories[(string) $product->category_id] ?? 0) + $weight;

            $brand = Str::lower(trim((string) $product->brand));
            if ($brand !== '') {
                $brands[$brand] = ($brands[$brand] ?? 0) + $weight;
            }
            foreach ($this->tags($product) as $tag) {
                $tags[$tag] = ($tags[$tag] ?? 0) + $weight;
            }
            $prices[] = ['price' => (float) $product->price, 'weight' => $weight];
        }

        $averagePrice = $totalWeight > 0
            ? collect($prices)->sum(fn (array $item) => $item['price'] * $item['weight']) / $totalWeight
            : null;

        $normalise = fn (array $values): array => collect($values)->map(function (float $value) use ($values) {
            return min(1, $value / max(array_values($values) ?: [1]));
        })->all();

        return [
            'categories' => $normalise($categories),
            'brands' => $normalise($brands),
            'tags' => $normalise($tags),
            'average_price' => $averagePrice,
            'summary' => [
                'events' => $events->count(),
                'top_categories' => array_keys(array_slice($categories, 0, 5, true)),
                'top_brands' => array_keys(array_slice($brands, 0, 5, true)),
                'average_price' => $averagePrice,
            ],
        ];
    }

    private function profileScore(array $profile, string $key): float
    {
        return $key !== '' ? (float) ($profile[$key] ?? 0) : 0;
    }

    private function priceScore(float $price, ?float $averagePrice): float
    {
        return $averagePrice && $averagePrice > 0
            ? max(0, 1 - (abs($price - $averagePrice) / max($averagePrice, 1)))
            : 0.5;
    }

    private function tags(Product $product): array
    {
        $metadata = is_array($product->ai_metadata) ? $product->ai_metadata : [];

        return collect($metadata['tags'] ?? [])
            ->filter(fn ($tag) => is_string($tag))
            ->map(fn (string $tag) => Str::lower(trim($tag)))
            ->filter()
            ->values()
            ->all();
    }

    private function reason(float $category, float $brand, float $tags, float $price): string
    {
        return match (true) {
            $brand >= 0.8 => 'Phù hợp thương hiệu bạn quan tâm',
            $tags >= 0.8 => 'Phù hợp với sở thích sản phẩm của bạn',
            $category >= 0.8 => 'Cùng danh mục bạn thường xem',
            $price >= 0.8 => 'Phù hợp mức giá bạn thường chọn',
            default => 'Được chọn từ xu hướng mua sắm phổ biến',
        };
    }

    private function popular(int $limit, ?int $excludeProductId = null): Collection
    {
        return Product::with(['category', 'images', 'store'])
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->when($excludeProductId, fn ($query) => $query->where('id', '!=', $excludeProductId))
            ->orderByDesc('sold_count')
            ->orderByDesc('rating')
            ->limit($limit)
            ->get();
    }
}
