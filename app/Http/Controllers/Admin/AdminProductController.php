<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category');
        $status = $request->input('status');
        $stockStatus = $request->input('stock');

        // Query stores that have products matching criteria
        $storesQuery = Store::with(['products' => function ($q) use ($search, $categoryId, $status, $stockStatus) {
            $q->with('category');
            if (! empty($search)) {
                $q->where('name', 'like', '%'.$search.'%');
            }
            if (! empty($categoryId)) {
                $q->where('category_id', $categoryId);
            }
            if (! empty($status)) {
                $q->where('status', $status);
            }
            if ($stockStatus === 'low') {
                $q->where('stock', '<=', 5);
            } elseif ($stockStatus === 'out') {
                $q->where('stock', '<=', 0);
            }
            $q->orderBy('id', 'asc');
        }])->withCount('products');

        if (! empty($search) || ! empty($categoryId) || ! empty($status) || ! empty($stockStatus)) {
            $storesQuery->whereHas('products', function ($q) use ($search, $categoryId, $status, $stockStatus) {
                if (! empty($search)) {
                    $q->where('name', 'like', '%'.$search.'%');
                }
                if (! empty($categoryId)) {
                    $q->where('category_id', $categoryId);
                }
                if (! empty($status)) {
                    $q->where('status', $status);
                }
                if ($stockStatus === 'low') {
                    $q->where('stock', '<=', 5);
                } elseif ($stockStatus === 'out') {
                    $q->where('stock', '<=', 0);
                }
            });
        }

        $stores = $storesQuery->orderBy('id', 'asc')->get();

        // Calculate store revenue (30 days or sum of subtotal)
        foreach ($stores as $store) {
            $storeRevenue = $store->orders()
                ->where('status', '!=', 'cancelled')
                ->sum('subtotal');
            $store->total_revenue = $storeRevenue ?: $store->products->sum(fn ($p) => (float) $p->price * max(1, (int) $p->sold_count));
        }

        // Summary KPI stats
        $totalStoresCount = Store::count();
        $totalProductsCount = Product::count();
        $estimatedRevenue30d = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('subtotal');
        if ($estimatedRevenue30d <= 0) {
            $estimatedRevenue30d = Order::where('status', '!=', 'cancelled')->sum('subtotal');
        }

        return view('admin.products.index', [
            'stores' => $stores,
            'categories' => Category::orderBy('name')->get(),
            'totalStoresCount' => $totalStoresCount,
            'totalProductsCount' => $totalProductsCount,
            'estimatedRevenue30d' => $estimatedRevenue30d,
            'search' => $search,
            'categoryId' => $categoryId,
            'status' => $status,
            'stockStatus' => $stockStatus,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product, 'categories' => Category::orderBy('name')->get(), 'stores' => Store::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validated($request, true));

        return redirect()->route('admin.products.index')->with('success', 'Đã thêm sản phẩm.');
    }

    public function edit(int $id): View
    {
        return view('admin.products.form', ['product' => Product::findOrFail($id), 'categories' => Category::orderBy('name')->get(), 'stores' => Store::orderBy('name')->get()]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        Product::findOrFail($id)->update($this->validated($request));

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Product::findOrFail($id)->delete();

        return back()->with('success', 'Đã xóa sản phẩm.');
    }

    private function validated(Request $request, bool $creating = false): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:200', 'store_id' => 'required|exists:stores,id',
            'category_id' => 'nullable|exists:categories,id', 'brand' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0', 'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0', 'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft', 'is_flash_sale' => 'nullable|boolean',
        ]);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(6));
        if (! $creating) {
            unset($data['slug']);
        }
        $data['discount_percent'] = ! empty($data['original_price']) && $data['original_price'] > $data['price']
            ? round((($data['original_price'] - $data['price']) / $data['original_price']) * 100) : 0;
        $data['is_flash_sale'] = $request->boolean('is_flash_sale');

        return $data;
    }
}
