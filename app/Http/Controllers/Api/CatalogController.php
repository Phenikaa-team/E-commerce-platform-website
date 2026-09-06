<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function categories()
    {
        return Category::query()->withCount('products')->orderBy('name')->paginate(20);
    }

    public function stores()
    {
        return Store::query()->withCount('products')->where('status', 'active')->paginate(20);
    }

    public function products(Request $request)
    {
        return Product::query()->with(['store:id,name,slug', 'category:id,name,slug'])->where('status', 'active')->when($request->category_id, fn ($q, $v) => $q->where('category_id', $v))->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))->latest()->paginate(20);
    }

    public function showProduct(Product $product)
    {
        return $product->load(['store:id,name,slug', 'category:id,name,slug']);
    }
}
