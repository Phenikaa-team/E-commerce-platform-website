<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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
            ]);
        }

        $products = Product::with('store')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('brand', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->take(6)
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
                ];
            });

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
