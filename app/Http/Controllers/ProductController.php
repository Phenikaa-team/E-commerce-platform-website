<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\NavigationMenu;
use App\Models\NavigationItem;
use App\Models\NavigationSection;
use App\Models\RecommendationSnapshot;
use App\Models\Product;
use Illuminate\Http\Request;
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

        // Limit homepage products to avoid loading excessive rows over remote db
        $recommendedProducts = $recommendedQuery->take(24)->get();

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

        // Build the managed flyout configuration per menu category. A managed
        // category no longer needs to rely on the JavaScript demo data.
        $flyoutConfigs = [];
        foreach ($menus as $menu) {
            // Older menu rows may not have category_id even though their
            // title clearly points to a real root category. Resolve those
            // rows so they also use the managed flyout instead of JS fallback.
            $menuCategory = $menu->category ?: $this->resolveMenuCategory($menu->title, $menu->slug, $homepageCategories);
            if (! $menuCategory) {
                continue;
            }

            $children = Category::where('parent_id', $menuCategory->id)
                ->with(['products' => fn ($query) => $query
                    ->where('status', 'active')
                    ->select(['id', 'category_id', 'main_image_url'])
                    ->latest()
                    ->limit(1)])
                ->orderBy('name')
                ->limit(5)
                ->get();

            $sections = NavigationSection::where('category_id', $menuCategory->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $snapshots = RecommendationSnapshot::where('category_id', $menuCategory->id)
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->get()
                ->keyBy('section_key');

            $columns = $sections->map(function (NavigationSection $section) use ($snapshots) {
                $manualItems = NavigationItem::where('navigation_section_id', $section->id)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();

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
    public function show(string $slug): View
    {
        $product = Product::with(['store', 'category', 'images', 'reviews.user', 'productVariants'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // 1. Same category products (limit fields & count)
        $sameCategoryProducts = $product->category_id
            ? Product::with(['store', 'category', 'images'])
                ->where('id', '!=', $product->id)
                ->where('status', 'active')
                ->where('category_id', $product->category_id)
                ->take(6)
                ->get()
            : collect();

        // 2. Curated recommended products (limit to 8)
        $recommendedQuery = Product::with(['store', 'category', 'images'])
            ->where('id', '!=', $product->id)
            ->where('status', 'active');

        if ($product->category_id) {
            $recommendedQuery->orderByRaw('CASE WHEN category_id = ? THEN 0 ELSE 1 END', [$product->category_id]);
        }

        $recommendedProducts = $recommendedQuery
            ->orderBy('sold_count', 'desc')
            ->take(8)
            ->get();

        // Backward compatibility for tabs
        $relatedProducts = $sameCategoryProducts->isNotEmpty() ? $sameCategoryProducts : $recommendedProducts->take(4);

        return view('product-detail', compact('product', 'relatedProducts', 'sameCategoryProducts', 'recommendedProducts'));
    }
}
