<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\RecommendationEvent;
use App\Models\User;
use App\Models\UserRecommendationSnapshot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PersonalizedRecommendationService
{
    private const EVENT_WEIGHTS = [
        'purchase' => 10.0,
        'wishlist' => 8.0,
        'coupon_applied' => 7.0,
        'cart' => 6.0,
        'dwell_time' => 4.0,
        'share' => 4.0,
        'chat_inquiry' => 4.0,
        'review_positive' => 7.0,
        'review_negative' => -8.0,
        'cart_remove' => -3.0,
        'view' => 2.0,
        'search' => 1.5,
    ];

    public function track(?User $user, string $eventType, ?Product $product = null, array $metadata = []): void
    {
        if (! isset(self::EVENT_WEIGHTS[$eventType])) {
            return;
        }

        $userId = $user?->id;
        $sessionId = request()->hasSession() ? request()->session()->getId() : null;

        if (! $userId && ! $sessionId) {
            return;
        }

        $duplicateWindow = match ($eventType) {
            'view' => 30,
            'dwell_time' => 30,
            'share' => 10,
            'cart', 'cart_remove' => 0,
            default => 5,
        };

        if ($duplicateWindow > 0) {
            $alreadyTracked = RecommendationEvent::query()
                ->when($userId, fn ($q) => $q->where('user_id', $userId), fn ($q) => $q->where('session_id', $sessionId))
                ->where('event_type', $eventType)
                ->where('product_id', $product?->id)
                ->where('created_at', '>=', now()->subMinutes($duplicateWindow))
                ->exists();

            if ($alreadyTracked) {
                return;
            }
        }

        RecommendationEvent::create([
            'user_id' => $userId,
            'product_id' => $product?->id,
            'category_id' => $product?->category_id,
            'event_type' => $eventType,
            'weight' => self::EVENT_WEIGHTS[$eventType],
            'metadata' => $metadata ?: null,
            'session_id' => $sessionId,
        ]);

        if ($userId && in_array($eventType, ['purchase', 'cart', 'cart_remove', 'wishlist'], true)) {
            UserRecommendationSnapshot::where('user_id', $userId)->delete();
        }
    }

    /**
     * Merge guest session events to user upon login.
     */
    public function stitchSession(User $user, string $sessionId): void
    {
        RecommendationEvent::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        UserRecommendationSnapshot::where('user_id', $user->id)->delete();
    }

    public function recommend(?User $user, int $limit = 24, ?int $excludeProductId = null, string $context = 'home'): Collection
    {
        $userId = $user?->id;
        $sessionId = request()->hasSession() ? request()->session()->getId() : null;

        if (! $userId && ! $sessionId) {
            return $this->popular($limit, $excludeProductId);
        }

        if ($userId) {
            $snapshot = UserRecommendationSnapshot::query()
                ->where('user_id', $userId)
                ->where('context', $context)
                ->where('expires_at', '>', now())
                ->first();

            if ($snapshot) {
                $ids = collect(data_get($snapshot->payload, 'items', []))
                    ->pluck('product_id')
                    ->filter(fn ($id) => (int) $id !== (int) $excludeProductId)
                    ->take($limit)
                    ->all();

                if (! empty($ids)) {
                    return Product::with(['category.parent', 'brandModel', 'images', 'store'])
                        ->where('status', 'active')
                        ->whereIn('id', $ids)
                        ->get()
                        ->sortBy(fn (Product $product) => array_search($product->id, $ids, true))
                        ->values();
                }
            }
        }

        $eventsQuery = RecommendationEvent::query()
            ->with('product.category')
            ->where('created_at', '>=', now()->subDays(180))
            ->latest()
            ->limit(300);

        if ($userId) {
            $eventsQuery->where('user_id', $userId);
        } else {
            $eventsQuery->where('session_id', $sessionId);
        }

        $events = $eventsQuery->get();

        if ($events->isEmpty()) {
            return $this->popular($limit, $excludeProductId);
        }

        $purchasedIds = $userId
            ? Order::query()
                ->where('user_id', $userId)
                ->whereIn('status', ['processing', 'shipping', 'completed'])
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->pluck('order_items.product_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
            : collect();

        $profile = $this->buildProfile($events);

        // Fetch top candidate products with lightweight query
        $query = Product::with(['category.parent', 'brandModel', 'images', 'store'])
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->where('is_flash_sale', false);

        if ($excludeProductId) {
            $query->where('id', '!=', $excludeProductId);
        }

        $candidates = $query->orderByDesc('sold_count')->limit(100)->get();

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
                    'product_id' => $product->id,
                    'score' => round($score, 4),
                    'sold_count' => (int) $product->sold_count,
                    'rating' => (float) $product->rating,
                    'reason' => $this->reason($categoryScore, $brandScore, $tagScore, $priceScore),
                ];
            })
            ->sortByDesc(fn (array $item) => [$item['score'], $item['sold_count'], $item['rating']])
            ->take($limit)
            ->values();

        if ($scored->isEmpty()) {
            return $this->popular($limit, $excludeProductId);
        }

        if ($userId) {
            UserRecommendationSnapshot::updateOrCreate(
                ['user_id' => $userId, 'context' => $context],
                [
                    'payload' => [
                        'items' => $scored->map(fn (array $item) => [
                            'product_id' => $item['product_id'],
                            'score' => $item['score'],
                            'reason' => $item['reason'],
                        ])->all(),
                        'profile' => $profile['summary'],
                    ],
                    'generated_at' => now(),
                    'expires_at' => now()->addMinutes(30),
                ]
            );
        }

        $winnerIds = $scored->pluck('product_id')->all();

        return $candidates
            ->whereIn('id', $winnerIds)
            ->sortBy(fn (Product $product) => array_search($product->id, $winnerIds, true))
            ->values();
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
            $max = max(array_values($values) ?: [1]);

            return $max > 0 ? min(1, $value / $max) : 0;
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
        $cacheKey = 'popular_product_ids_'.($excludeProductId ?: 'none')."_{$limit}";

        $ids = Cache::remember($cacheKey, 600, function () use ($limit, $excludeProductId) {
            return Product::query()
                ->where('status', 'active')
                ->where('stock', '>', 0)
                ->when($excludeProductId, fn ($query) => $query->where('id', '!=', $excludeProductId))
                ->orderByDesc('sold_count')
                ->orderByDesc('rating')
                ->limit($limit)
                ->pluck('id')
                ->all();
        });

        if (empty($ids)) {
            return collect();
        }

        return Product::with(['category.parent', 'brandModel', 'images', 'store'])
            ->where('status', 'active')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Product $product) => array_search($product->id, $ids, true))
            ->values();
    }
}
