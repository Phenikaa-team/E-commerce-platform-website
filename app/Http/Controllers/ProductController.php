<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display the ShopMart homepage with dynamic products.
     */
    public function index(): View
    {
        $flashSaleProducts = Product::with(['category', 'images', 'store'])
            ->where('is_flash_sale', true)
            ->get();

        $recommendedProducts = Product::with(['category', 'images', 'store'])
            ->where('is_flash_sale', false)
            ->get();

        return view('shopmart', compact('flashSaleProducts', 'recommendedProducts'));
    }

    /**
     * Display the dynamic product detail page.
     */
    public function show(string $slug): View
    {
        $product = Product::with(['store', 'category', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        // 1. Same category products
        $sameCategoryProducts = Product::with(['store', 'category'])
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->take(6)
            ->get();

        // 2. Curated recommended products (same category first, then top sold / high rated)
        $categoryIds = [$product->category_id];
        $recommendedProducts = Product::with(['store', 'category'])
            ->where('id', '!=', $product->id)
            ->orderByRaw("CASE WHEN category_id = {$product->category_id} THEN 0 ELSE 1 END")
            ->orderBy('sold_count', 'desc')
            ->take(12)
            ->get();

        // Backward compatibility for tabs
        $relatedProducts = $sameCategoryProducts->isNotEmpty() ? $sameCategoryProducts : $recommendedProducts->take(4);

        return view('product-detail', compact('product', 'relatedProducts', 'sameCategoryProducts', 'recommendedProducts'));
    }
}
