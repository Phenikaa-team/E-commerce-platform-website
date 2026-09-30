<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NavigationMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    /**
     * Display categories tree and table.
     */
    public function index(Request $request): View
    {
        $categories = Category::with(['parent', 'children'])
            ->withCount('products')
            ->orderBy('parent_id', 'asc')
            ->paginate(15);

        $parentCategories = Category::whereNull('parent_id')->get();

        $quickCategoryIds = NavigationMenu::where('url', '__quick__')->pluck('category_id')->all();
        return view('admin.categories.index', compact('categories', 'parentCategories', 'quickCategoryIds'));
    }

    /**
     * Store a new category.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'badge' => 'nullable|string|max:50',
            'icon_svg' => 'nullable|string',
        ]);

        Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'parent_id' => $data['parent_id'] ?? null,
            'badge' => $data['badge'] ?? null,
            'icon_svg' => $data['icon_svg'] ?? null,
        ]);

        return back()->with('success', 'Đã thêm danh mục mới thành công!');
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,'.$category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'badge' => 'nullable|string|max:50',
            'icon_svg' => 'nullable|string',
        ]);

        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'parent_id' => $data['parent_id'] ?? null,
            'badge' => $data['badge'] ?? null,
            'icon_svg' => $data['icon_svg'] ?? null,
        ]);

        return back()->with('success', 'Cập nhật danh mục thành công!');
    }

    /**
     * Delete a category.
     */
    public function toggleQuick(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);
        $menu = NavigationMenu::where('url', '__quick__')->where('category_id', $category->id)->first();
        if ($request->boolean('enabled') && ! $menu) {
            NavigationMenu::create(['category_id' => $category->id, 'title' => $category->name, 'slug' => $category->slug, 'url' => '__quick__', 'sort_order' => NavigationMenu::where('url', '__quick__')->count()]);
        } elseif (! $request->boolean('enabled') && $menu) {
            $menu->delete();
        }
        return back()->with('success', 'Đã cập nhật danh mục nhanh.');
    }
    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục này vì còn sản phẩm trực thuộc.');
        }

        $category->delete();

        return back()->with('success', 'Đã xóa danh mục thành công.');
    }
}
