<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandAlias;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminBrandController extends Controller
{
    public function index(): View
    {
        return view('admin.brands.index', [
            'brands' => Brand::with('aliases')->withCount('products')->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'logo_url' => 'nullable|url|max:500',
            'status' => 'required|in:pending,approved,hidden',
        ]);

        Brand::create([
            ...$data,
            'slug' => Str::slug($data['name']),
        ]);

        return back()->with('success', 'Đã thêm brand vào danh mục quản lý.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'logo_url' => 'nullable|url|max:500',
            'status' => 'required|in:pending,approved,hidden',
        ]);

        $brand->update([
            ...$data,
            'slug' => Str::slug($data['name']),
        ]);

        return back()->with('success', 'Đã cập nhật brand.');
    }

    public function approve(int $id): RedirectResponse
    {
        Brand::findOrFail($id)->update(['status' => 'approved']);

        return back()->with('success', 'Đã duyệt brand.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['status' => $brand->status === 'hidden' ? 'approved' : 'hidden']);

        return back()->with('success', $brand->status === 'hidden' ? 'Đã ẩn brand.' : 'Đã hiển thị brand.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['status' => 'hidden']);

        return back()->with('success', 'Brand đã được ẩn để không ảnh hưởng sản phẩm đang sử dụng.');
    }

    public function storeAlias(Request $request, int $id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $data = $request->validate(['alias' => 'required|string|max:120']);

        BrandAlias::create([
            'brand_id' => $brand->id,
            'alias' => trim($data['alias']),
            'slug' => Str::slug($data['alias']),
        ]);

        return back()->with('success', 'Đã thêm từ khóa thay thế cho brand.');
    }

    public function destroyAlias(int $id): RedirectResponse
    {
        BrandAlias::findOrFail($id)->delete();

        return back()->with('success', 'Đã xóa từ khóa thay thế.');
    }

    public function updateAlias(Request $request, int $id): RedirectResponse
    {
        $alias = BrandAlias::findOrFail($id);
        $data = $request->validate(['alias' => 'required|string|max:120']);

        $alias->update([
            'alias' => trim($data['alias']),
            'slug' => Str::slug($data['alias']),
        ]);

        return back()->with('success', 'Đã cập nhật từ khóa thay thế.');
    }
}
