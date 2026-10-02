<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
        $query = Product::with(['category', 'store'])->latest();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        return view('admin.products.index', [
            'products' => $query->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'search' => $request->search, 'categoryId' => $request->category,
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
