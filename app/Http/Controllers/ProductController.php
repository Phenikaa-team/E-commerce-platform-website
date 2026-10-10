<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\NavigationSection;
use App\Models\Product;
use App\Models\RecommendationEvent;
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

        $menus = NavigationMenu::with('category')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('url')->orWhere('url', '!=', '__quick__');
            })
            ->orderBy('sort_order')->orderBy('id')->get();
        $homepageCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        $quickCategories = $menus->where('url', '__quick__')->pluck('category')->filter();
        if ($quickCategories->isEmpty()) {
            $quickCategories = $homepageCategories;
        }

        // Load the managed flyout data once. Running these queries inside the
        // menu loop creates a costly N+1 pattern on the Supabase connection.
        $menuCategoriesByMenuId = $menus->mapWithKeys(function (NavigationMenu $menu) use ($homepageCategories): array {
            return [$menu->id => $menu->category ?: $this->resolveMenuCategory($menu->title, $menu->slug, $homepageCategories)];
        });
        $menuCategoryIds = $menuCategoriesByMenuId->filter()->pluck('id')->unique()->values();

        $childrenByParent = Category::whereIn('parent_id', $menuCategoryIds)
            ->with(['products' => fn ($query) => $query
                ->where('status', 'active')
                ->select(['id', 'category_id', 'main_image_url'])
                ->latest()
                ->limit(1)])
            ->orderBy('name')
            ->get()
            ->groupBy('parent_id')
            ->map(fn ($children) => $children->take(5));

        $sectionsByCategory = NavigationSection::whereIn('category_id', $menuCategoryIds)
            ->where('is_active', true)
            ->with(['items' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('category_id');

        $snapshotsByCategory = RecommendationSnapshot::whereIn('category_id', $menuCategoryIds)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get()
            ->groupBy('category_id');

        $hasPersonalizedHistory = auth()->check()
            && RecommendationEvent::where('user_id', auth()->id())->exists();
        $preferredBrands = collect();
        $personalizedProducts = collect();
        $flyoutProductPool = collect();

        if ($hasPersonalizedHistory) {
            $recentEvents = RecommendationEvent::query()
                ->with('product.brandModel')
                ->where('user_id', auth()->id())
                ->whereIn('event_type', ['purchase', 'cart', 'view'])
                ->latest()
                ->limit(100)
                ->get();
            $recentProductIds = $recentEvents
                ->pluck('product_id')
                ->filter()
                ->unique()
                ->values();
            $preferredBrands = $recentEvents
                ->map(fn (RecommendationEvent $event) => $event->product?->brandModel?->name
                    ?: $event->product?->brand)
                ->filter()
                ->unique(fn (string $brand) => Str::lower(trim($brand)))
                ->values();

            $recommendedProductIds = $recommendedProducts->pluck('id')->values();
            $personalizedProducts = $recommendedProducts->sortBy(function (Product $product) use ($recentProductIds, $recommendedProductIds): array {
                $recentPosition = $recentProductIds->search($product->id);
                $recommendationPosition = $recommendedProductIds->search($product->id);

                return [
                    $recentPosition === false ? 1 : 0,
                    $recentPosition === false ? PHP_INT_MAX : $recentPosition,
                    $recommendationPosition === false ? PHP_INT_MAX : $recommendationPosition,
                ];
            })
                ->values();

            $flyoutProductPool = Product::query()
                ->with(['category.parent', 'brandModel'])
                ->where('status', 'active')
                ->where(function ($query) use ($menuCategoryIds): void {
                    $query->whereIn('category_id', $menuCategoryIds)
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->whereIn('parent_id', $menuCategoryIds));
                })
                ->get();
        }

        // Build the managed flyout configuration per menu category. A managed
        // category no longer needs to rely on the JavaScript demo data.
        $flyoutConfigs = [];
        foreach ($menus as $menu) {
            // Older menu rows may not have category_id even though their
            // title clearly points to a real root category. Resolve those
            // rows so they also use the managed flyout instead of JS fallback.
            $menuCategory = $menuCategoriesByMenuId->get($menu->id);
            if (! $menuCategory) {
                continue;
            }

            $children = $childrenByParent->get($menuCategory->id, collect());
            $sections = $sectionsByCategory->get($menuCategory->id, collect());
            $snapshots = $snapshotsByCategory->get($menuCategory->id, collect())->keyBy('section_key');

            $categoryProducts = $flyoutProductPool
                ->filter(fn (Product $product) => $product->category_id === $menuCategory->id
                    || $product->category?->parent_id === $menuCategory->id)
                ->values();
            $categoryPersonalizedProducts = $personalizedProducts
                ->filter(fn (Product $product) => $product->category_id === $menuCategory->id
                    || $product->category?->parent_id === $menuCategory->id)
                ->concat($categoryProducts)
                ->unique('id')
                ->values();

            $personalizedProductSectionCount = max(1, $sections->filter(function (NavigationSection $section): bool {
                $sectionKey = Str::lower($section->section_key.' '.$section->title);

                return $section->items->isEmpty()
                    && ! Str::contains($sectionKey, ['brand', 'thương hiệu']);
            })->count());
            $personalizedProductSectionIndex = 0;
            $usedPersonalizedProductIds = [];

            $columns = $sections->map(function (NavigationSection $section) use ($snapshots, $categoryPersonalizedProducts, $preferredBrands, $hasPersonalizedHistory, $personalizedProductSectionCount, &$personalizedProductSectionIndex, &$usedPersonalizedProductIds) {
                $manualItems = $section->items;

                if ($manualItems->isNotEmpty()) {
                    $items = $manualItems->map(fn (NavigationItem $item) => [
                        'name' => $item->name,
                        'url' => $item->url ?: '/search?q='.urlencode($item->name),
                        'item_type' => $item->item_type,
                    ])->all();
                } elseif ($hasPersonalizedHistory && $categoryPersonalizedProducts->isNotEmpty()) {
                    $items = $this->personalizedFlyoutItems(
                        $section,
                        $categoryPersonalizedProducts,
                        $preferredBrands,
                        $usedPersonalizedProductIds,
                        $personalizedProductSectionIndex,
                        $personalizedProductSectionCount,
                    );
                    $sectionKey = Str::lower($section->section_key.' '.$section->title);
                    if (! Str::contains($sectionKey, ['brand', 'thương hiệu'])) {
                        $personalizedProductSectionIndex++;
                    }
                    if (empty($items)) {
                        $items = [];
                    }
                } else {
                    $payload = $snapshots->get($section->section_key)?->payload ?? [];
                    $items = data_get($payload, 'items', data_get($payload, 'sections.'.$section->section_key, []));
                }

                return [
                    'heading' => $section->title,
                    'items' => is_array($items) ? array_slice($items, 0, $section->item_limit) : [],
                ];
            })->values()->all();

            $flyoutConfigs[$menuCategory->slug] = [
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
            $flyoutConfigs[$menu->slug] = $flyoutConfigs[$menuCategory->slug];
        }

        return view('shopmart', compact(
            'flashSaleProducts',
            'recommendedProducts',
            'search',
            'activeCategory',
            'sort',
            'hasFilter', 'menus', 'homepageCategories', 'quickCategories', 'flyoutConfigs'
        ));
    }

    private function personalizedFlyoutItems(
        NavigationSection $section,
        $products,
        $preferredBrands,
        array &$usedProductIds,
        int $sectionIndex,
        int $sectionCount,
    ): array {
        $sectionKey = Str::lower($section->section_key.' '.$section->title);
        $isBrandColumn = Str::contains($sectionKey, ['brand', 'thương hiệu']);
        $isSeriesColumn = Str::contains($sectionKey, ['series', 'dòng sản phẩm', 'dòng']);
        $isAccessoryColumn = Str::contains($sectionKey, ['accessory', 'phụ kiện']);
        $accessoryWords = ['sạc', 'cáp', 'tai nghe', 'ốp', 'bao da', 'chuột', 'bàn phím', 'pin dự phòng', 'adapter', 'hub', 'kính cường lực'];
        if (Str::contains($sectionKey, ['giày dép', 'thời trang', 'fashion'])) {
            $accessoryWords = array_merge($accessoryWords, ['giày', 'sneaker', 'dép', 'sandal', 'túi', 'ví', 'đồng hồ']);
        }
        $topicWords = match (true) {
            Str::contains($sectionKey, ['chăm sóc da', 'skincare']) => ['serum', 'da', 'kem chống nắng', 'sữa rửa mặt', 'dưỡng'],
            Str::contains($sectionKey, ['trang điểm', 'nước hoa', 'makeup', 'perfume']) => ['son', 'phấn', 'trang điểm', 'nước hoa', 'mascara', 'kem nền', 'cọ'],
            Str::contains($sectionKey, ['thời trang nam nữ', 'quần áo', 'clothing']) => ['áo', 'hoodie', 'quần', 'váy', 'đầm', 'áo khoác', 'thời trang'],
            Str::contains($sectionKey, ['giày dép', 'shoes', 'footwear']) => ['giày', 'sneaker', 'dép', 'sandal', 'túi', 'ví', 'đồng hồ'],
            default => [],
        };

        $filteredProducts = $products->filter(function (Product $product) use ($isAccessoryColumn, $isSeriesColumn, $accessoryWords, $usedProductIds): bool {
            if (in_array($product->id, $usedProductIds, true)) {
                return false;
            }

            $tags = is_array($product->ai_metadata) ? ($product->ai_metadata['tags'] ?? []) : [];
            $searchable = Str::lower(trim($product->name.' '.($product->category?->name ?? '').' '.implode(' ', $tags)));
            $isAccessory = Str::contains($searchable, $accessoryWords);

            return $isAccessoryColumn ? $isAccessory : (! $isSeriesColumn || ! $isAccessory);
        });

        if ($topicWords !== []) {
            $topicProducts = $filteredProducts->filter(function (Product $product) use ($topicWords): bool {
                $tags = is_array($product->ai_metadata) ? ($product->ai_metadata['tags'] ?? []) : [];
                $searchable = Str::lower(trim($product->name.' '.($product->category?->name ?? '').' '.implode(' ', $tags)));

                return Str::contains($searchable, $topicWords);
            });

            if ($topicProducts->isNotEmpty()) {
                $filteredProducts = $topicProducts;
            }
        }

        if (! $isBrandColumn && ! $isSeriesColumn && ! $isAccessoryColumn && $sectionCount > 1 && $topicWords === []) {
            $remainingSections = max(1, $sectionCount - $sectionIndex);
            $filteredProducts = $filteredProducts->take((int) ceil($filteredProducts->count() / $remainingSections));
        }

        $items = $filteredProducts
            ->map(function (Product $product) use ($isBrandColumn, $isSeriesColumn): array {
                $name = $isBrandColumn
                    ? ($product->brandModel?->name ?: trim((string) $product->brand))
                    : $product->name;

                return [
                    'name' => $name,
                    'url' => $isBrandColumn
                        ? route('catalog.brand', Str::slug($name))
                        : route('product.detail', $product->slug),
                    'item_type' => $isBrandColumn ? 'brand' : ($isSeriesColumn ? 'series' : 'product'),
                ];
            })
            ->filter(fn (array $item) => trim((string) $item['name']) !== '')
            ->unique(fn (array $item) => Str::lower($item['name']))
            ->take($section->item_limit)
            ->values()
            ->all();

        if (! $isBrandColumn) {
            $usedProductIds = array_merge($usedProductIds, $filteredProducts->pluck('id')->all());
        }

        if ($isBrandColumn) {
            $categoryBrands = $products
                ->map(fn (Product $product) => $product->brandModel?->name ?: trim((string) $product->brand))
                ->filter()
                ->map(fn (string $brand) => Str::lower(trim($brand)))
                ->unique()
                ->values();

            $brandItems = $preferredBrands
                ->filter(fn (string $brand) => $categoryBrands->contains(Str::lower(trim($brand))))
                ->map(fn (string $brand): array => [
                    'name' => trim($brand),
                    'url' => route('catalog.brand', Str::slug($brand)),
                    'item_type' => 'brand',
                ])
                ->filter(fn (array $item) => $item['name'] !== '')
                ->unique(fn (array $item) => Str::lower($item['name']))
                ->values();

            $items = $brandItems
                ->concat($items)
                ->unique(fn (array $item) => Str::lower($item['name']))
                ->take($section->item_limit)
                ->values()
                ->all();
        }

        return $items;
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
        $product = Product::with([
            'store' => fn ($query) => $query->withCount('products'),
            'category',
            'images',
            'reviews' => fn ($query) => $query->with('user:id,name')->latest()->take(15),
            'productVariants',
        ])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $recommendationService->track(auth()->user(), 'view', $product);

        // 1. Same category products (cached IDs for safe serialization and fast response)
        $sameCategoryProducts = $product->category_id
            ? (function () use ($product) {
                $cacheKey = "prod_detail_cat_ids_{$product->category_id}_exclude_{$product->id}";
                $ids = Cache::remember($cacheKey, 300, function () use ($product) {
                    return Product::query()
                        ->where('id', '!=', $product->id)
                        ->where('status', 'active')
                        ->where('category_id', $product->category_id)
                        ->limit(6)
                        ->pluck('id')
                        ->all();
                });

                if (empty($ids)) {
                    return collect();
                }

                return Product::with(['store:id,name', 'category:id,name,slug', 'images:id,product_id,url'])
                    ->whereIn('id', $ids)
                    ->get()
                    ->sortBy(fn (Product $p) => array_search($p->id, $ids, true))
                    ->values();
            })()
            : collect();

        // 2. Curated recommended products (limit to 8)
        $recommendedProducts = $recommendationService->recommend(auth()->user(), 8, $product->id, 'product_detail');

        // Backward compatibility for tabs
        $relatedProducts = $sameCategoryProducts->isNotEmpty() ? $sameCategoryProducts : $recommendedProducts->take(4);

        return view('product-detail', compact('product', 'relatedProducts', 'sameCategoryProducts', 'recommendedProducts'));
    }

    /**
     * AJAX endpoint to track user interactions (dwell_time, share, etc.)
     */
    public function trackInteraction(Request $request, PersonalizedRecommendationService $recommendationService): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => ['required', 'string', 'in:view,dwell_time,share,chat_inquiry,review_positive,review_negative,cart_remove'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'metadata' => ['nullable', 'array'],
        ]);

        $product = ! empty($validated['product_id'])
            ? Product::find($validated['product_id'])
            : null;

        $recommendationService->track(
            auth()->user(),
            $validated['event_type'],
            $product,
            $validated['metadata'] ?? []
        );

        return response()->json(['success' => true]);
    }
}
