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

/**
 * Builds personalized product recommendations from user behaviour.
 *
 * This service is a deterministic, rule-based recommender rather than a
 * machine-learning model. It records weighted events, builds a lightweight
 * preference profile, scores eligible products, and caches the ranked result
 * per user and context.
 */
class PersonalizedRecommendationService
{
    /**
     * Behaviour weights used to translate an action into recommendation signal.
     *
     * A stronger action such as purchase has more influence than a passive view.
     * Negative events reduce preference for a product's category, brand, and
     * tags when the profile is rebuilt.
     */
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

    /**
     * Store one user/session behaviour event.
     *
     * Guests are tracked by session_id and their events are attached to the
     * authenticated user later by stitchSession(). Repeated passive events are
     * throttled so refreshing a page cannot inflate the profile indefinitely.
     * Important profile-changing events invalidate the user's cached snapshot.
     */
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

        // The window is deliberately shorter for strong actions and disabled
        // for cart changes because each cart action should be recorded.
        $duplicateWindow = match ($eventType) {
            'view' => 30,
            'dwell_time' => 30,
            'share' => 10,
            'cart', 'cart_remove' => 0,
            default => 5,
        };

        if ($duplicateWindow > 0) {
            // Prevent repeated views/searches from overpowering meaningful
            // actions such as adding to cart or completing a purchase.
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

        // A changed intent must force a fresh ranking on the next request.
        if ($userId && in_array($eventType, ['purchase', 'cart', 'cart_remove', 'wishlist'], true)) {
            UserRecommendationSnapshot::where('user_id', $userId)->delete();
        }
    }

    /**
     * Attach guest-session events to the user after login.
     *
     * This preserves browsing behaviour collected before authentication so
     * the first personalized result is not based on an empty profile.
     */
    public function stitchSession(User $user, string $sessionId): void
    {
        RecommendationEvent::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        UserRecommendationSnapshot::where('user_id', $user->id)->delete();
    }

    /**
     * Return products ranked for a user/session and display context.
     *
     * The order of operations is:
     * 1. Reuse a valid 30-minute snapshot when available.
     * 2. Build a profile from the user's last 180 days/300 events.
     * 3. Score the top eligible product candidates.
     * 4. Persist the ranked IDs and scores for later inspection and reuse.
     * 5. Fall back to popularity for guests or users without behaviour data.
     */
    public function recommend(?User $user, int $limit = 24, ?int $excludeProductId = null, string $context = 'home'): Collection
    {
        $userId = $user?->id;
        $sessionId = request()->hasSession() ? request()->session()->getId() : null;

        // There is no identity to personalize without either a user or a
        // session, so use the platform-wide popular list.
        if (! $userId && ! $sessionId) {
            return $this->popular($limit, $excludeProductId);
        }

        if ($userId) {
            $cacheKey = "user_rec_{$userId}_{$context}_{$limit}_".($excludeProductId ?: '0');
            $cachedProductIds = Cache::get($cacheKey);
            if ($cachedProductIds && is_array($cachedProductIds)) {
                return Product::with(['category.parent', 'brandModel', 'images', 'store', 'flashSaleItems.campaign'])
                    ->where('status', 'active')
                    ->whereIn('id', $cachedProductIds)
                    ->get()
                    ->sortBy(fn (Product $product) => array_search($product->id, $cachedProductIds, true))
                    ->values();
            }

            // The snapshot avoids recalculating the same ranking on every page
            // view. It is scoped by context so home and product-detail lists
            // can have different recommendation results.
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
                    Cache::put($cacheKey, $ids, 900);

                    return Product::with(['category.parent', 'brandModel', 'images', 'store', 'flashSaleItems.campaign'])
                        ->where('status', 'active')
                        ->whereIn('id', $ids)
                        ->get()
                        ->sortBy(fn (Product $product) => array_search($product->id, $ids, true))
                        ->values();
                }
            }
        }

        // Only recent, bounded behaviour is used to keep the profile relevant
        // and the request predictable as the event table grows.
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

        // Products already bought are excluded from new purchase-oriented
        // suggestions, while their category/brand/tags still teach the profile.
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

        // Candidate generation is popularity-bounded first. Personalization
        // then ranks this manageable set instead of scanning every product.
        $query = Product::with(['category.parent', 'brandModel', 'images', 'store', 'flashSaleItems.campaign'])
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->withoutActiveFlashSale();

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
                $priceScore = $this->priceScore((float) $product->effective_price, $profile['average_price']);
                $popularScore = min(1, ((int) $product->sold_count / 500) * 0.7 + ((float) $product->rating / 5) * 0.3);

                // Final score is a transparent weighted formula. Category
                // affinity has the highest influence; popularity is only a
                // tie-breaking safety net worth 10% of the maximum score.
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
            // Keep the score and reason in JSON so the result can be audited
            // in Supabase (user_recommendation_snapshots.payload).
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

    /**
     * Build normalized preferences from weighted historical events.
     *
     * Each event contributes to the product's category, brand, tags, and
     * weighted average price. Older events lose influence linearly over 180
     * days but retain a 25% minimum signal.
     *
     * @return array{
     *     categories: array<string, float>,
     *     brands: array<string, float>,
     *     tags: array<string, float>,
     *     average_price: float|null,
     *     summary: array<string, mixed>
     * }
     */
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

            // Recency decay makes recent actions more influential without
            // erasing long-term interests completely.
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
            $prices[] = ['price' => (float) $product->effective_price, 'weight' => $weight];
        }

        $averagePrice = $totalWeight > 0
            ? collect($prices)->sum(fn (array $item) => $item['price'] * $item['weight']) / $totalWeight
            : null;

        // Normalization converts each preference dimension to 0..1, allowing
        // the fixed formula in recommend() to combine unlike quantities.
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

    /**
     * Read one normalized preference value, returning zero for no match.
     */
    private function profileScore(array $profile, string $key): float
    {
        return $key !== '' ? (float) ($profile[$key] ?? 0) : 0;
    }

    /**
     * Score how close a candidate price is to the user's weighted average.
     */
    private function priceScore(float $price, ?float $averagePrice): float
    {
        return $averagePrice && $averagePrice > 0
            ? max(0, 1 - (abs($price - $averagePrice) / max($averagePrice, 1)))
            : 0.5;
    }

    /**
     * Extract normalized AI/product tags used for content matching.
     *
     * @return array<int, string>
     */
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

    /**
     * Produce a human-readable explanation for the highest matching signal.
     */
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

    /**
     * Cold-start fallback for guests or users without usable behaviour data.
     *
     * This is popularity-based, not personalized: sales lead and rating breaks
     * ties. The result is cached for ten minutes to keep anonymous traffic cheap.
     */
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

        return Product::with(['category.parent', 'brandModel', 'images', 'store', 'flashSaleItems.campaign'])
            ->where('status', 'active')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Product $product) => array_search($product->id, $ids, true))
            ->values();
    }
}
