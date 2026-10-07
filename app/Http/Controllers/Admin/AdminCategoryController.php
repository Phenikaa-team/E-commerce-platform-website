<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NavigationMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    /**
     * Display categories tree and table.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'categories');

        $allCategories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        // Present the existing table in a real tree order for the Admin.
        $categoriesByParent = $allCategories->groupBy(fn (Category $category) => $category->parent_id ?? 0);
        $categories = collect();
        $appendTree = function (int $parentId, int $level = 0) use (&$appendTree, &$categories, $categoriesByParent): void {
            foreach ($categoriesByParent->get($parentId, collect()) as $category) {
                $category->setAttribute('_tree_level', $level);
                $categories->push($category);
                $appendTree($category->id, $level + 1);
            }
        };
        $appendTree(0);

        // A child can itself have children, e.g. Electronics > Phones > iPhone.
        $parentCategories = $categories;

        $menus = NavigationMenu::with('category')
            ->where(function ($q) {
                $q->whereNull('url')->orWhere('url', '!=', '__quick__');
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $quickCategoryIds = NavigationMenu::where('url', '__quick__')->pluck('category_id')->all();

        return view('admin.categories.index', compact('tab', 'categories', 'parentCategories', 'allCategories', 'menus', 'quickCategoryIds'));
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

        $parentId = $data['parent_id'] ?? null;
        $this->assertValidParent($category, $parentId);

        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'parent_id' => $parentId,
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

        if ($category->children()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục này vì vẫn còn danh mục con. Hãy chuyển hoặc xóa danh mục con trước.');
        }

        $category->delete();

        return back()->with('success', 'Đã xóa danh mục thành công.');
    }

    /** Prevent a category from becoming its own parent or a descendant of itself. */
    private function assertValidParent(Category $category, ?int $parentId): void
    {
        if ($parentId === null) {
            return;
        }

        if ($parentId === $category->id) {
            throw ValidationException::withMessages([
                'parent_id' => 'Danh mục không thể là cha của chính nó.',
            ]);
        }

        $descendantIds = collect();
        $pending = [$category->id];
        while ($pending !== []) {
            $children = Category::whereIn('parent_id', $pending)->pluck('id')->all();
            $pending = array_values(array_diff($children, $descendantIds->all()));
            $descendantIds = $descendantIds->merge($pending);
        }

        if ($descendantIds->contains($parentId)) {
            throw ValidationException::withMessages([
                'parent_id' => 'Không thể chọn danh mục con làm danh mục cha.',
            ]);
        }
    }
}
