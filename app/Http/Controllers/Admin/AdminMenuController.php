<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NavigationMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminMenuController extends Controller
{
    public function index(): View
    {
        return view('admin.menus.index', [
            'menus' => NavigationMenu::with('category')->orderBy('sort_order')->orderBy('id')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
    
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:100', 'category_id' => 'nullable|exists:categories,id',
            'url' => 'nullable|string|max:255', 'icon_svg' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0', 'is_active' => 'nullable|boolean',
        ]);
        
        $insertData = $this->payload($data);
        
        // DÒNG NÀY SẼ GHI ĐÈ BẤT CHẤP HÀM PAYLOAD, ÉP BUỘC LÀ TRUE
        $insertData['is_active'] = true;

        NavigationMenu::create($insertData);

        return back()->with('success', 'Đã thêm menu bên trái.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $menu = NavigationMenu::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string|max:100', 'category_id' => 'nullable|exists:categories,id',
            'url' => 'nullable|string|max:255', 'icon_svg' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0', 'is_active' => 'nullable|boolean',
        ]);
        
        $updateData = $this->payload($data);
        
        // Form cập nhật: Nếu checkbox được gửi lên là true, không có là false
        $updateData['is_active'] = $request->boolean('is_active');

        $menu->update($updateData);

        return back()->with('success', 'Đã cập nhật menu.');
    }

    public function destroy(int $id): RedirectResponse
    {
        NavigationMenu::findOrFail($id)->delete();

        return back()->with('success', 'Đã xóa menu.');
    }

    private function payload(array $data): array
    {
        return [
            'title' => $data['title'], 
            'slug' => Str::slug($data['title']),
            'category_id' => $data['category_id'] ?? null, 
            'url' => $data['url'] ?? null,
            'icon_svg' => $data['icon_svg'] ?? null, 
            'sort_order' => $data['sort_order'] ?? 0,
            // Đã xóa bỏ xử lý is_active ở đây để tránh xung đột
        ];
    }
}