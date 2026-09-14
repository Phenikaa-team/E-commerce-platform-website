<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductFilterService
{
    /**
     * Filter and paginate products based on request parameters.
     *
     * @param  Builder<Product>|null  $baseQuery
     * @return array{
     *     products: LengthAwarePaginator,
     *     totalCount: int,
     *     availableCategories: Collection,
     *     availableBrands: Collection,
     *     priceBounds: array{min: float, max: float},
     *     activeFilters: array<string, mixed>,
     * }
     */
    public function paginate(Request $request, ?Builder $baseQuery = null, int $perPage = 16): array
    {
        $query = $baseQuery ? clone $baseQuery : Product::query();
        $query->with(['category', 'store', 'images']);

        // Default to active status if column exists
        $query->where('status', 'active');

        // 1. Search Query
        $q = trim((string) $request->query('q', $request->query('search', '')));
        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%")
                    ->orWhereHas('store', fn ($sq) => $sq->where('name', 'like', "%{$q}%"));
            });
        }

        // 2. Category Filter (supports slug or ID, including child subcategories)
        $categoryParam = $request->query('category');
        if (! empty($categoryParam) && $categoryParam !== 'all') {
            $category = Category::with('children')
                ->where('slug', $categoryParam)
                ->orWhere('id', is_numeric($categoryParam) ? (int) $categoryParam : 0)
                ->first();

            if ($category) {
                $categoryIds = $category->children->pluck('id')->push($category->id)->all();
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // 3. Brand Filter (can be single brand or array)
        $brandParam = $request->query('brand');
        if (! empty($brandParam)) {
            if (is_array($brandParam)) {
                $query->whereIn('brand', array_filter($brandParam));
            } else {
                $query->where('brand', $brandParam);
            }
        }

        // 4. Price Range Filter
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        if (is_numeric($minPrice) && (float) $minPrice >= 0) {
            $query->where('price', '>=', (float) $minPrice);
        }
        if (is_numeric($maxPrice) && (float) $maxPrice > 0) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // 5. Rating Filter
        $minRating = $request->query('rating');
        if (is_numeric($minRating) && (float) $minRating > 0) {
            $query->where('rating', '>=', (float) $minRating);
        }

        // 6. Stock / Availability Filter
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // 7. Mall Filter (Official Store Products)
        if ($request->boolean('is_mall')) {
            $query->where('is_mall', true);
        }

        // 8. Sorting
        $sort = (string) $request->query('sort', 'popular');
        match ($sort) {
            'newest' => $query->latest('id'),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderByDesc('rating')->orderByDesc('reviews_count'),
            default => $query->orderByDesc('sold_count')->orderByDesc('id'),
        };

        // Get facet data BEFORE pagination from active filter criteria (without specific facet to allow facet counts)
        $facetQuery = $baseQuery ? clone $baseQuery : Product::query();
        $facetQuery->where('status', 'active');
        if ($q !== '') {
            $facetQuery->where(function (Builder $sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%");
            });
        }

        // Available Categories with product count
        $availableCategories = Category::whereHas('products', function ($pq) {
            $pq->where('status', 'active');
        })
            ->withCount(['products' => fn ($pq) => $pq->where('status', 'active')])
            ->orderByDesc('products_count')
            ->take(12)
            ->get();

        // Available Brands from database
        $availableBrands = (clone $facetQuery)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->selectRaw('brand, count(*) as count')
            ->groupBy('brand')
            ->orderByDesc('count')
            ->take(15)
            ->pluck('count', 'brand');

        // Price Bounds across matching products
        $minPossiblePrice = (float) ((clone $facetQuery)->min('price') ?? 0);
        $maxPossiblePrice = (float) ((clone $facetQuery)->max('price') ?? 100000000);

        // Paginate results with persistent query string
        $products = $query->paginate($perPage)->withQueryString();

        // Collect human-readable active filters for easy chip removal
        $activeFilters = $this->extractActiveFilters($request);

        return [
            'products' => $products,
            'totalCount' => $products->total(),
            'availableCategories' => $availableCategories,
            'availableBrands' => $availableBrands,
            'priceBounds' => [
                'min' => $minPossiblePrice,
                'max' => $maxPossiblePrice,
            ],
            'activeFilters' => $activeFilters,
        ];
    }

    /**
     * Parse human-readable active filters for display chips.
     *
     * @return array<string, array{label: string, remove_url: string}>
     */
    protected function extractActiveFilters(Request $request): array
    {
        $chips = [];
        $currentParams = $request->query();

        // Category chip
        if (! empty($currentParams['category']) && $currentParams['category'] !== 'all') {
            $cat = Category::where('slug', $currentParams['category'])
                ->orWhere('id', is_numeric($currentParams['category']) ? (int) $currentParams['category'] : 0)
                ->first();
            $label = $cat ? 'Danh mục: '.$cat->name : 'Danh mục: '.$currentParams['category'];
            $params = $currentParams;
            unset($params['category'], $params['page']);
            $chips['category'] = [
                'label' => $label,
                'remove_url' => $request->fullUrlWithQuery(array_merge($params, ['category' => null, 'page' => null])),
            ];
        }

        // Brand chip
        if (! empty($currentParams['brand'])) {
            $brandVal = is_array($currentParams['brand']) ? implode(', ', $currentParams['brand']) : $currentParams['brand'];
            $params = $currentParams;
            unset($params['brand'], $params['page']);
            $chips['brand'] = [
                'label' => 'Thương hiệu: '.$brandVal,
                'remove_url' => $request->fullUrlWithQuery(array_merge($params, ['brand' => null, 'page' => null])),
            ];
        }

        // Price chip
        if (isset($currentParams['min_price']) || isset($currentParams['max_price'])) {
            $minFmt = isset($currentParams['min_price']) && is_numeric($currentParams['min_price'])
                ? number_format((float) $currentParams['min_price'], 0, ',', '.').'₫'
                : '0₫';
            $maxFmt = isset($currentParams['max_price']) && is_numeric($currentParams['max_price'])
                ? number_format((float) $currentParams['max_price'], 0, ',', '.').'₫'
                : 'Trở lên';

            $params = $currentParams;
            unset($params['min_price'], $params['max_price'], $params['page']);
            $chips['price'] = [
                'label' => "Giá: {$minFmt} - {$maxFmt}",
                'remove_url' => $request->fullUrlWithQuery(array_merge($params, ['min_price' => null, 'max_price' => null, 'page' => null])),
            ];
        }

        // Rating chip
        if (! empty($currentParams['rating']) && is_numeric($currentParams['rating'])) {
            $params = $currentParams;
            unset($params['rating'], $params['page']);
            $chips['rating'] = [
                'label' => 'Từ '.$currentParams['rating'].' ★',
                'remove_url' => $request->fullUrlWithQuery(array_merge($params, ['rating' => null, 'page' => null])),
            ];
        }

        // In stock chip
        if ($request->boolean('in_stock')) {
            $params = $currentParams;
            unset($params['in_stock'], $params['page']);
            $chips['in_stock'] = [
                'label' => 'Còn hàng',
                'remove_url' => $request->fullUrlWithQuery(array_merge($params, ['in_stock' => null, 'page' => null])),
            ];
        }

        // Mall chip
        if ($request->boolean('is_mall')) {
            $params = $currentParams;
            unset($params['is_mall'], $params['page']);
            $chips['is_mall'] = [
                'label' => 'ShopMart Mall',
                'remove_url' => $request->fullUrlWithQuery(array_merge($params, ['is_mall' => null, 'page' => null])),
            ];
        }

        return $chips;
    }
}
