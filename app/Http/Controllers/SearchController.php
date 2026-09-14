<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Provide instant search suggestions for the smart header search bar.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'products' => [],
                'categories' => [],
                'brands' => [],
                'stores' => [],
                'total_products' => 0,
                'view_all_url' => '',
            ]);
        }

        $products = Product::with('store')
            ->where('status', 'active')
            ->where(function ($sub) use ($query) {
                $sub->where('name', 'like', "%{$query}%")
                    ->orWhere('brand', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->take(5)
            ->get(['id', 'name', 'slug', 'price', 'main_image_url', 'store_id', 'rating', 'sold_count'])
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'url' => route('product.detail', $product->slug),
                    'price' => number_format((float) $product->price, 0, ',', '.').'₫',
                    'image' => $product->main_image_url,
                    'store' => $product->store?->name,
                    'rating' => (float) ($product->rating ?? 5.0),
                ];
            });

        $categories = Category::where('name', 'like', "%{$query}%")
            ->take(4)
            ->get(['id', 'name', 'slug'])
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'url' => route('catalog.category', $cat->slug),
                ];
            });

        $brands = Product::where('status', 'active')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->where('brand', 'like', "%{$query}%")
            ->distinct()
            ->pluck('brand')
            ->take(4)
            ->map(function ($brand) {
                return [
                    'name' => $brand,
                    'url' => route('catalog.brand', $brand),
                ];
            });

        $stores = Store::where('status', 'active')
            ->where('name', 'like', "%{$query}%")
            ->take(3)
            ->get(['id', 'name', 'slug', 'logo_url', 'is_mall'])
            ->map(function ($store) {
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'slug' => $store->slug,
                    'url' => route('store.show', $store->slug),
                    'logo' => $store->logo_url,
                    'is_mall' => (bool) $store->is_mall,
                ];
            });

        $totalProducts = Product::where('status', 'active')
            ->where(function ($sub) use ($query) {
                $sub->where('name', 'like', "%{$query}%")
                    ->orWhere('brand', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->count();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'stores' => $stores,
            'total_products' => $totalProducts,
            'view_all_url' => route('catalog.search', ['q' => $query]),
        ]);
    }
}
