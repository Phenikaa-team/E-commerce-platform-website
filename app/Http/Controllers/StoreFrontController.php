<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use App\Services\ProductFilterService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreFrontController extends Controller
{
    public function __construct(
        protected ProductFilterService $filterService
    ) {}

    /**
     * Display public store page with products, search, categories and filter/sort.
     */
    public function show(Request $request, string $slug): View
    {
        $store = Store::where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? (int) $slug : 0)
            ->firstOrFail();

        // Reuse unified ProductFilterService with store constraint
        $baseQuery = Product::where('store_id', $store->id);
        $data = $this->filterService->paginate($request, $baseQuery, 12);
        $products = $data['products'];

        // Store statistics
        $totalProducts = Product::where('store_id', $store->id)->where('status', 'active')->count();
        $totalReviews = Review::whereHas('product', fn ($q) => $q->where('store_id', $store->id))->count();

        // Categories available in this store
        $categories = Category::whereHas('products', fn ($q) => $q->where('store_id', $store->id)->where('status', 'active'))
            ->get();

        // Active vouchers from this store
        $coupons = $store->coupons()
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        $search = trim((string) $request->query('q', $request->query('search', '')));
        $selectedCategory = $request->query('category');
        $sort = $request->query('sort', 'popular');

        return view('store.show', compact(
            'store',
            'products',
            'totalProducts',
            'totalReviews',
            'categories',
            'coupons',
            'search',
            'selectedCategory',
            'sort'
        ));
    }
}
