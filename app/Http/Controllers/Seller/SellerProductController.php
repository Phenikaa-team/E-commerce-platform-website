<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerProductController extends Controller
{
    /**
     * List all products belonging to current seller's store.
     */
    public function index(Request $request): View
    {
        $store = auth()->user()->store;
        $search = $request->query('search');
        $categoryId = $request->query('category');
        $stockStatus = $request->query('stock');

        $query = Product::with('category')
            ->where('store_id', $store->id)
            ->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockStatus === 'out') {
            $query->where('stock', 0);
        } elseif ($stockStatus === 'low') {
            $query->where('stock', '>', 0)->where('stock', '<=', 5);
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('seller.products.index', compact('products', 'categories', 'search', 'categoryId', 'stockStatus'));
    }

    /**
     * Show form to create new product.
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('seller.products.create', compact('categories'));
    }

    /**
     * Store new product in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'main_image' => 'nullable|image|max:3072',
            'images.*' => 'nullable|image|max:3072',
            'color_variants' => 'nullable|string', // Comma separated or JSON
            'size_variants' => 'nullable|string', // Comma separated or JSON
            'is_flash_sale' => 'nullable|boolean',
        ]);

        $store = auth()->user()->store;

        $mainImageUrl = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=600&q=80';
        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products', 'public');
            $mainImageUrl = '/storage/'.$path;
        }

        // Parse variants
        $variants = [];
        if (! empty($data['color_variants'])) {
            $colors = array_map('trim', explode(',', $data['color_variants']));
            $variants['colors'] = array_map(fn ($c) => ['label' => $c, 'image' => $mainImageUrl], $colors);
        }
        if (! empty($data['size_variants'])) {
            $variants['options'] = array_map('trim', explode(',', $data['size_variants']));
        }

        $discountPercent = 0;
        if (! empty($data['original_price']) && $data['original_price'] > $data['price']) {
            $discountPercent = round((($data['original_price'] - $data['price']) / $data['original_price']) * 100);
        }

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.rand(1000, 9999),
            'brand' => $data['brand'] ?? $store->name,
            'price' => $data['price'],
            'original_price' => $data['original_price'] ?? null,
            'discount_percent' => $discountPercent,
            'stock' => $data['stock'],
            'description' => $data['description'],
            'main_image_url' => $mainImageUrl,
            'variants' => ! empty($variants) ? $variants : null,
            'status' => 'active',
            'is_mall' => $store->is_mall ?? false,
            'is_flash_sale' => $request->boolean('is_flash_sale', false),
            'flash_sale_percent' => $request->boolean('is_flash_sale') ? $discountPercent : null,
            'rating' => 5.0,
            'sold_count' => 0,
            'reviews_count' => 0,
        ]);

        // Upload gallery images
        if ($request->hasFile('images')) {
            $order = 1;
            foreach ($request->file('images') as $imgFile) {
                $imgPath = $imgFile->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => '/storage/'.$imgPath,
                    'sort_order' => $order++,
                ]);
            }
        }

        return redirect()->route('seller.products.index')->with('success', 'Đã thêm sản phẩm thành công!');
    }

    /**
     * Show edit product form.
     */
    public function edit(int $id): View
    {
        $store = auth()->user()->store;
        $product = Product::with('images')->where('store_id', $store->id)->findOrFail($id);
        $categories = Category::all();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product details.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $store = auth()->user()->store;
        $product = Product::where('store_id', $store->id)->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'main_image' => 'nullable|image|max:3072',
            'images.*' => 'nullable|image|max:3072',
            'color_variants' => 'nullable|string',
            'size_variants' => 'nullable|string',
            'is_flash_sale' => 'nullable|boolean',
        ]);

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products', 'public');
            $product->main_image_url = '/storage/'.$path;
        }

        $discountPercent = 0;
        if (! empty($data['original_price']) && $data['original_price'] > $data['price']) {
            $discountPercent = round((($data['original_price'] - $data['price']) / $data['original_price']) * 100);
        }

        // Parse variants
        $variants = $product->variants ?? [];
        if (! empty($data['color_variants'])) {
            $colors = array_map('trim', explode(',', $data['color_variants']));
            $variants['colors'] = array_map(fn ($c) => ['label' => $c, 'image' => $product->main_image_url], $colors);
        }
        if (! empty($data['size_variants'])) {
            $variants['options'] = array_map('trim', explode(',', $data['size_variants']));
        }

        $product->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'brand' => $data['brand'] ?? $store->name,
            'price' => $data['price'],
            'original_price' => $data['original_price'] ?? null,
            'discount_percent' => $discountPercent,
            'stock' => $data['stock'],
            'description' => $data['description'],
            'variants' => ! empty($variants) ? $variants : null,
            'is_flash_sale' => $request->boolean('is_flash_sale', false),
            'flash_sale_percent' => $request->boolean('is_flash_sale') ? $discountPercent : null,
        ]);

        // Upload additional images
        if ($request->hasFile('images')) {
            $order = $product->images()->count() + 1;
            foreach ($request->file('images') as $imgFile) {
                $imgPath = $imgFile->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => '/storage/'.$imgPath,
                    'sort_order' => $order++,
                ]);
            }
        }

        return redirect()->route('seller.products.index')->with('success', 'Cập nhật thông tin sản phẩm thành công!');
    }

    /**
     * Delete product.
     */
    public function destroy(int $id): RedirectResponse
    {
        $store = auth()->user()->store;
        $product = Product::where('store_id', $store->id)->findOrFail($id);
        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Đã xóa sản phẩm.');
    }
}
