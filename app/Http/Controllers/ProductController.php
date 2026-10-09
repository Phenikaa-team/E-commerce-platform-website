<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\NavigationSection;
use App\Models\Product;
use App\Models\RecommendationSnapshot;
use App\Services\PersonalizedRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display the ShopMart homepage with dynamic products, search, category & sort filtering.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category');
        $sort = $request->query('sort', 'default');

        $hasFilter = ! empty($search) || ! empty($categoryId) || ($sort !== 'default');

        // Flash sale products (only shown on default view or if matching search)
        if (! $hasFilter) {
            $flashSaleIds = Cache::remember('shopmart_flash_sale_ids_v1', 300, function () {
                return Product::where('status', 'active')
                    ->where('is_flash_sale', true)
                    ->pluck('id')
                    ->all();
            });

            $flashSaleProducts = ! empty($flashSaleIds)
                ? Product::with(['category', 'images', 'store'])
                    ->whereIn('id', $flashSaleIds)
                    ->get()
                : collect();
        } else {
            $flashSaleQuery = Product::with(['category', 'images', 'store'])
                ->where('status', 'active')
                ->where('is_flash_sale', true);

            if (! empty($search)) {
                $flashSaleQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if (! empty($categoryId)) {
                $flashSaleQuery->where(function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId)
                        ->orWhereHas('category', fn ($cat) => $cat->where('slug', $categoryId));
                });
            }

            $flashSaleProducts = $flashSaleQuery->get();
        }

        // Recommended / Main products query
        $recommendedQuery = Product::with(['category', 'images', 'store'])
            ->where('status', 'active')
            ->where('is_flash_sale', false);

        if (! empty($search)) {
            $recommendedQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($categoryId)) {
            $recommendedQuery->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('category', fn ($cat) => $cat->where('slug', $categoryId));
            });
        }

        // Sorting
        match ($sort) {
            'price_asc' => $recommendedQuery->orderBy('price', 'asc'),
            'price_desc' => $recommendedQuery->orderBy('price', 'desc'),
            'newest' => $recommendedQuery->latest(),
            'rating' => $recommendedQuery->orderBy('rating', 'desc'),
            'popular' => $recommendedQuery->orderBy('sold_count', 'desc'),
            default => $recommendedQuery->orderBy('sold_count', 'desc'),
        };

        // Personalised recommendations are used on the unfiltered homepage.
        // Search/category/sort pages keep their existing deterministic query.
        $recommendedProducts = (! $hasFilter)
            ? app(PersonalizedRecommendationService::class)->recommend(auth()->user(), 24)
            : $recommendedQuery->take(24)->get();

        $activeCategory = null;
        if (! empty($categoryId)) {
            $activeCategory = Category::where('id', $categoryId)
                ->orWhere('slug', $categoryId)
                ->first();
        }

        $menuIds = Cache::remember('shopmart_menu_ids_v1', 1800, function () {
            return NavigationMenu::where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('url')->orWhere('url', '!=', '__quick__');
                })
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id')
                ->all();
        });

        $menus = ! empty($menuIds)
            ? NavigationMenu::with('category')->whereIn('id', $menuIds)->orderBy('sort_order')->orderBy('id')->get()
            : collect();

        $homepageCategoryIds = Cache::remember('shopmart_homepage_category_ids_v1', 1800, function () {
            return Category::whereNull('parent_id')->orderBy('name')->pluck('id')->all();
        });

        $homepageCategories = ! empty($homepageCategoryIds)
            ? Category::whereIn('id', $homepageCategoryIds)->orderBy('name')->get()
            : collect();

        $quickCategories = $menus->where('url', '__quick__')->pluck('category')->filter();
        if ($quickCategories->isEmpty()) {
            $quickCategories = $homepageCategories;
        }

        // Cache the heavy multi-table flyout configuration (pure array data) for 1 hour
        $flyoutConfigs = Cache::remember('shopmart_flyout_configs_v1', 3600, function () use ($menus, $homepageCategories) {
            $flyouts = [];
            $menuCategories = $menus->map(function ($menu) use ($homepageCategories) {
                return $menu->category ?: $this->resolveMenuCategory($menu->title, $menu->slug, $homepageCategories);
            })->filter()->unique('id')->values();

            if ($menuCategories->isNotEmpty()) {
                $categoryIds = $menuCategories->pluck('id')->all();

                $childrenByCategory = Category::whereIn('parent_id', $categoryIds)
                    ->with(['products' => fn ($query) => $query
                        ->where('status', 'active')
                        ->select(['id', 'category_id', 'main_image_url'])
                        ->latest()
                        ->limit(1)])
                    ->orderBy('name')
                    ->get()
                    ->groupBy('parent_id');

                $sectionsByCategory = NavigationSection::with(['items' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('id')])
                    ->whereIn('category_id', $categoryIds)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
                    ->groupBy('category_id');

                $snapshotsByCategory = RecommendationSnapshot::whereIn('category_id', $categoryIds)
                    ->where(function ($query) {
                        $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->get()
                    ->groupBy('category_id');

                foreach ($menus as $menu) {
                    $menuCategory = $menu->category ?: $this->resolveMenuCategory($menu->title, $menu->slug, $homepageCategories);
                    if (! $menuCategory) {
                        continue;
                    }

                    $children = $childrenByCategory->get($menuCategory->id, collect())->take(5);
                    $sections = $sectionsByCategory->get($menuCategory->id, collect());
                    $snapshots = $snapshotsByCategory->get($menuCategory->id, collect())->keyBy('section_key');

                    $columns = $sections->map(function (NavigationSection $section) use ($snapshots) {
                        $manualItems = $section->items;

                        if ($manualItems->isNotEmpty()) {
                            $items = $manualItems->map(fn (NavigationItem $item) => [
                                'name' => $item->name,
                                'url' => $item->url ?: '/search?q='.urlencode($item->name),
                                'item_type' => $item->item_type,
                            ])->all();
                        } else {
                            $payload = $snapshots->get($section->section_key)?->payload ?? [];
                            $items = data_get($payload, 'items', data_get($payload, 'sections.'.$section->section_key, []));
                        }

                        return [
                            'heading' => $section->title,
                            'items' => is_array($items) ? array_slice($items, 0, $section->item_limit) : [],
                        ];
                    })->values()->all();

                    $config = [
                        'title' => $menuCategory->name,
                        'subtitle' => 'Khám phá các sản phẩm nổi bật trong danh mục này',
                        'topCards' => $children->map(function (Category $child) {
                            return [
                                'title' => $child->name,
                                'image' => $child->products->first()?->main_image_url,
                                'url' => route('catalog.category', $child->slug),
                            ];
                        })->values()->all(),
                        'columns' => $columns,
                        'managed' => true,
                    ];

                    $flyouts[$menuCategory->slug] = $config;
                    if ($menu->slug) {
                        $flyouts[$menu->slug] = $config;
                    }
                }
            }

            return $flyouts;
        });

        return view('shopmart', compact(
            'flashSaleProducts',
            'recommendedProducts',
            'search',
            'activeCategory',
            'sort',
            'hasFilter', 'menus', 'homepageCategories', 'quickCategories', 'flyoutConfigs'
        ));
    }

    private function resolveMenuCategory(string $title, ?string $slug, $categories): ?Category
    {
        $menuKey = Str::slug($slug ?: $title);

        return $categories->first(function (Category $category) use ($menuKey, $title) {
            $categoryKey = Str::slug($category->slug ?: $category->name);
            if ($menuKey === $categoryKey || Str::contains($menuKey, $categoryKey) || Str::contains($categoryKey, $menuKey)) {
                return true;
            }

            $menuWords = collect(explode('-', Str::slug($title)))->filter(fn ($word) => strlen($word) > 2);
            $categoryWords = collect(explode('-', Str::slug($category->name)))->filter(fn ($word) => strlen($word) > 2);
            $overlap = $menuWords->intersect($categoryWords)->count();

            return $overlap >= 2;
        });
    }

    /**
     * Display the dynamic product detail page.
     */
    public function show(string $slug, PersonalizedRecommendationService $recommendationService): View
    {
        $productId = Cache::remember("product_id_slug_{$slug}", 1800, function () use ($slug) {
            return Product::where('slug', $slug)->where('status', 'active')->value('id');
        });

        $product = Product::with([
            'store' => fn ($query) => $query->withCount('products'),
            'category',
            'images',
            'reviews' => fn ($query) => $query->with('user:id,name,avatar_url')->latest()->take(10),
            'productVariants',
        ])
            ->where('id', $productId ?: 0)
            ->where('status', 'active')
            ->firstOrFail();

        $user = auth()->user();
        $recommendationService->track($user, 'view', $product);

        // 1. Same category products (cache list of IDs for 30 minutes)
        $sameCategoryProducts = collect();
        if ($product->category_id) {
            $sameCategoryIds = Cache::remember("product_same_cat_ids_{$product->category_id}_{$product->id}", 1800, function () use ($product) {
                return Product::where('status', 'active')
                    ->where('category_id', $product->category_id)
                    ->where('id', '!=', $product->id)
                    ->orderByDesc('sold_count')
                    ->limit(6)
                    ->pluck('id')
                    ->all();
            });

            if (! empty($sameCategoryIds)) {
                $sameCategoryProducts = Product::with(['store', 'category', 'images'])
                    ->select(['id', 'store_id', 'category_id', 'name', 'slug', 'price', 'original_price', 'rating', 'sold_count', 'main_image_url', 'status'])
                    ->whereIn('id', $sameCategoryIds)
                    ->get()
                    ->sortBy(fn (Product $p) => array_search($p->id, $sameCategoryIds, true))
                    ->values();
            }
        }

        // 2. Curated recommended products
        $recommendedProducts = $recommendationService->recommend($user, 8, $product->id, 'product_detail');

        // Backward compatibility for tabs
        $relatedProducts = $sameCategoryProducts->isNotEmpty() ? $sameCategoryProducts : $recommendedProducts->take(4);

        return view('product-detail', compact('product', 'relatedProducts', 'sameCategoryProducts', 'recommendedProducts'));
    }
}
