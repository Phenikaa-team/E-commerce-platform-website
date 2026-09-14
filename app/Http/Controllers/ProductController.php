<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
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

        $recommendedProducts = $recommendedQuery->get();

        $activeCategory = null;
        if (! empty($categoryId)) {
            $activeCategory = Category::where('id', $categoryId)
                ->orWhere('slug', $categoryId)
                ->first();
        }

        return view('shopmart', compact(
            'flashSaleProducts',
            'recommendedProducts',
            'search',
            'activeCategory',
            'sort',
            'hasFilter'
        ));
    }

    /**
     * Display the dynamic product detail page.
     */
    public function show(string $slug): View
    {
        $product = Product::with(['store', 'category', 'images', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // 1. Same category products
        $sameCategoryProducts = $product->category_id
            ? Product::with(['store', 'category'])
                ->where('id', '!=', $product->id)
                ->where('category_id', $product->category_id)
                ->take(6)
                ->get()
            : collect();

        // 2. Curated recommended products (same category first, then top sold / high rated)
        $recommendedQuery = Product::with(['store', 'category'])
            ->where('id', '!=', $product->id);

        if ($product->category_id) {
            $recommendedQuery->orderByRaw('CASE WHEN category_id = ? THEN 0 ELSE 1 END', [$product->category_id]);
        }

        $recommendedProducts = $recommendedQuery
            ->orderBy('sold_count', 'desc')
            ->take(12)
            ->get();

        // Backward compatibility for tabs
        $relatedProducts = $sameCategoryProducts->isNotEmpty() ? $sameCategoryProducts : $recommendedProducts->take(4);

        return view('product-detail', compact('product', 'relatedProducts', 'sameCategoryProducts', 'recommendedProducts'));
    }
}
