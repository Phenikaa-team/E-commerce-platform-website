<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NavigationMenu;
use App\Models\NavigationItem;
use App\Models\NavigationSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminNavigationController extends Controller
{
    /** Manage one category's flyout independently from every other category. */
    public function index(int $categoryId): View
    {
        $category = Category::withCount('products')->findOrFail($categoryId);

        $children = $category->children()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $sections = NavigationSection::where('category_id', $category->id)
            ->with('items')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $menus = NavigationMenu::with('category')
            ->where('is_active', true)
            ->whereNotNull('category_id')
            ->where(function ($query) {
                $query->whereNull('url')->orWhere('url', '!=', '__quick__');
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.navigation.flyout', compact('category', 'children', 'sections', 'menus'));
    }

    public function storeSection(Request $request, int $categoryId): RedirectResponse
    {
        $category = Category::findOrFail($categoryId);

        if (NavigationSection::where('category_id', $category->id)->count() >= 3) {
            return back()->with('error', 'Mỗi danh mục chỉ được có tối đa 3 cột flyout.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'item_limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
            'recommendation_enabled' => ['nullable', 'boolean'],
        ]);

        NavigationSection::create([
            'category_id' => $category->id,
            'section_key' => $this->sectionKey($data['title']),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'item_limit' => $data['item_limit'] ?? 6,
            'is_active' => $request->boolean('is_active', true),
            'recommendation_enabled' => $request->boolean('recommendation_enabled', true),
        ]);

        return back()->with('success', 'Đã thêm cột recommendation cho danh mục '.$category->name.'.');
    }

    public function updateSection(Request $request, int $categoryId, int $sectionId): RedirectResponse
    {
        $section = NavigationSection::where('category_id', $categoryId)->findOrFail($sectionId);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'item_limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
            'recommendation_enabled' => ['nullable', 'boolean'],
        ]);

        $section->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'item_limit' => $data['item_limit'] ?? 6,
            'is_active' => $request->boolean('is_active'),
            'recommendation_enabled' => $request->boolean('recommendation_enabled'),
        ]);

        return back()->with('success', 'Đã cập nhật cột recommendation.');
    }

    public function destroySection(int $categoryId, int $sectionId): RedirectResponse
    {
        NavigationSection::where('category_id', $categoryId)->findOrFail($sectionId)->delete();

        return back()->with('success', 'Đã xóa cột recommendation khỏi danh mục này.');
    }

    public function storeItem(Request $request, int $categoryId, int $sectionId): RedirectResponse
    {
        $section = NavigationSection::where('category_id', $categoryId)->findOrFail($sectionId);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'url' => ['nullable', 'string', 'max:500'],
            'item_type' => ['nullable', 'string', 'in:brand,series,accessory,custom'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $sortOrder = (int) ($data['sort_order'] ?? 0);
        if ($sortOrder === 0) {
            $sortOrder = ((int) $section->items()->max('sort_order')) + 1;
        }

        $section->items()->create([
            'name' => $data['name'],
            'url' => $data['url'] ?? null,
            'item_type' => $data['item_type'] ?? 'custom',
            'source' => 'manual',
            'sort_order' => $sortOrder,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Đã thêm mục vào cột '.$section->title.'.');
    }

    public function updateItem(Request $request, int $categoryId, int $sectionId, int $itemId): RedirectResponse
    {
        $section = NavigationSection::where('category_id', $categoryId)->findOrFail($sectionId);
        $item = $section->items()->findOrFail($itemId);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'url' => ['nullable', 'string', 'max:500'],
            'item_type' => ['nullable', 'string', 'in:brand,series,accessory,custom'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $item->update([
            'name' => $data['name'],
            'url' => $data['url'] ?? null,
            'item_type' => $data['item_type'] ?? 'custom',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Đã cập nhật mục trong cột.');
    }

    public function destroyItem(int $categoryId, int $sectionId, int $itemId): RedirectResponse
    {
        $section = NavigationSection::where('category_id', $categoryId)->findOrFail($sectionId);
        $section->items()->findOrFail($itemId)->delete();

        return back()->with('success', 'Đã xóa mục khỏi cột.');
    }

    private function sectionKey(string $title): string
    {
        return Str::limit(Str::slug($title, '_'), 70, '').'_'.Str::lower(Str::random(6));
    }
}
