<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\RecommendationEvent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RecommendationDataSeeder extends Seeder
{
    private const SEED_KEY = 'interdisciplinary-recommendation-v1';

    private const EVENT_WEIGHTS = [
        'view' => 2.0,
        'cart' => 6.0,
        'wishlist' => 8.0,
        'purchase' => 10.0,
    ];

    public function run(): void
    {
        $personas = [
            [
                'email' => 'recommendation-tech@example.com',
                'name' => 'Recommendation Test - Công nghệ',
                'password' => 'recommendation',
                'category' => 'dien-thoai',
            ],
            [
                'email' => 'recommendation-fashion@example.com',
                'name' => 'Recommendation Test - Làm đẹp',
                'password' => 'recommendation',
                'category' => 'lam-dep-va-suc-khoe',
            ],
            [
                'email' => 'recommendation-books@example.com',
                'name' => 'Recommendation Test - Máy tính',
                'password' => 'recommendation',
                'category' => 'laptop-va-cac-thiet-bi-so',
            ],
        ];

        foreach ($personas as $persona) {
            $user = User::updateOrCreate(
                ['email' => $persona['email']],
                [
                    'name' => $persona['name'],
                    'password' => Hash::make($persona['password']),
                    'role' => 'buyer',
                    'status' => 'active',
                ]
            );

            $this->seedPersonaEvents($user, $persona['category']);
        }
    }

    private function seedPersonaEvents(User $user, string $categorySlug): void
    {
        $category = Category::query()->where('slug', $categorySlug)->first();

        if (! $category) {
            return;
        }

        $products = Product::query()
            ->where('category_id', $category->id)
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->orderByDesc('sold_count')
            ->limit(6)
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        RecommendationEvent::query()
            ->where('user_id', $user->id)
            ->whereJsonContains('metadata', ['seed_key' => self::SEED_KEY])
            ->delete();

        $productAt = fn (int $index): Product => $products->get($index % $products->count());

        $events = [
            ['product' => $productAt(0), 'type' => 'purchase', 'days_ago' => 21],
            ['product' => $productAt(1), 'type' => 'wishlist', 'days_ago' => 12],
            ['product' => $productAt(2), 'type' => 'cart', 'days_ago' => 7],
            ['product' => $productAt(3), 'type' => 'view', 'days_ago' => 2],
            ['product' => $productAt(4), 'type' => 'view', 'days_ago' => 1],
        ];

        foreach ($events as $index => $event) {
            $product = $event['product'];

            RecommendationEvent::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'category_id' => $product->category_id,
                'event_type' => $event['type'],
                'weight' => self::EVENT_WEIGHTS[$event['type']],
                'metadata' => [
                    'seed_key' => self::SEED_KEY,
                    'persona_category' => $categorySlug,
                    'sequence' => $index + 1,
                ],
                'created_at' => now()->subDays($event['days_ago']),
                'updated_at' => now()->subDays($event['days_ago']),
            ]);
        }
    }
}
