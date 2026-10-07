@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục & Menu Bên Trái - ShopMart Admin')
@section('page_title', 'Danh Mục Sản Phẩm & Điều Hướng')

@section('content')
<div class="space-y-6">

    <!-- Header Navigation Tabs -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="bg-white p-1.5 rounded-2xl border border-gray-100 shadow-xs flex gap-1 text-xs font-bold">
            <a href="{{ route('admin.categories.index', ['tab' => 'categories']) }}" class="px-5 py-2 rounded-xl transition-all {{ $tab === 'categories' ? 'bg-primary text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Cây Danh Mục Ngành Hàng ({{ $categories->count() }})
            </a>
            <a href="{{ route('admin.categories.index', ['tab' => 'menus']) }}" class="px-5 py-2 rounded-xl transition-all {{ $tab === 'menus' ? 'bg-primary text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Menu Điều Hướng Bên Trái ({{ $menus->count() }})
            </a>
        </div>
    </div>

    @if($tab === 'categories')
        <!-- Tab 1: Cây Danh Mục Ngành Hàng -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Column: Add Category Form (4 cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
                <h3 class="text-sm font-black text-gray-900 mb-1">Thêm danh mục mới</h3>
                <p class="text-xs text-gray-500 mb-6">Tạo mới danh mục cha hoặc danh mục con theo cấu trúc phân cấp</p>

                <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="cat-name" class="form-label">Tên danh mục <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="cat-name" required placeholder="Ví dụ: Điện Thoại, Đồng Hồ..." class="form-input">
                    </div>

                    <div>
                        <label for="parent_id" class="form-label">Danh mục cha (Tùy chọn)</label>
                        <select name="parent_id" id="parent_id" class="form-select">
                <option value="">-- Danh mục gốc (Cấp 1) --</option>
                            @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}">{{ str_repeat('— ', (int) ($parent->_tree_level ?? 0)) }}{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="badge" class="form-label">Nhãn Badge (Tùy chọn)</label>
                        <input type="text" name="badge" id="badge" placeholder="Ví dụ: HOT, SALE, MỚI..." class="form-input">
                    </div>

                    <div>
                        <label for="icon_svg" class="form-label">Mã SVG Icon</label>
                        <textarea name="icon_svg" id="icon_svg" rows="3" placeholder="Nhập mã SVG: <svg ...>...</svg>" class="form-textarea font-mono"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-md w-full text-xs font-bold shadow-xs">
                        Tạo Danh Mục Mới
                    </button>
                </form>
            </div>

            <!-- Right Column: Categories List Table (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Danh sách danh mục hiện có</h3>
                        <p class="text-xs text-gray-500">Hiển thị cấu trúc cây danh mục và số lượng sản phẩm liên kết</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 font-bold border-b border-gray-100">
                                <th class="pb-3 px-3">Tên danh mục</th>
                                <th class="pb-3 px-3">Phân cấp</th>
                                <th class="pb-3 px-3">Đường dẫn tĩnh (Slug)</th>
                                <th class="pb-3 px-3">Sản phẩm</th>
                                <th class="pb-3 px-3 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($categories as $category)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2" style="padding-left: {{ min((int) ($category->_tree_level ?? 0), 5) * 18 }}px">
                                            @if(($category->_tree_level ?? 0) > 0)
                                                <span class="text-gray-300">└</span>
                                            @endif
                                            <span class="font-bold text-gray-900 block">{{ $category->name }}</span>
                                        </div>
                                        @if($category->badge)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-rose-50 text-primary border border-rose-100">
                                                {{ $category->badge }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 text-gray-500">
                                        @if($category->parent)
                                            <span class="inline-flex items-center gap-1 text-rose-600 font-semibold">
                                                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                Con của: {{ $category->parent->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-800 font-bold">Danh mục gốc (Cấp 1)</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 font-mono text-gray-400">
                                        {{ $category->slug }}
                                    </td>

                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                            {{ $category->products_count }} sản phẩm
                                        </span>
                                    </td>

                                    <td class="py-3 px-3 text-right">
                                        <details class="inline-block text-left mr-2">
                                            <summary class="cursor-pointer px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors">Sửa</summary>
                                            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="absolute right-8 mt-2 z-10 bg-white border border-gray-200 rounded-xl shadow-lg p-3 w-64 space-y-2 text-left">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label class="form-label text-[11px]">Tên danh mục</label>
                                                    <input name="name" value="{{ $category->name }}" required class="form-input w-full text-xs">
                                                </div>
                                                <div>
                                                    <label class="form-label text-[11px]">Danh mục cha</label>
                                                    <select name="parent_id" class="form-select w-full text-xs">
                                                        <option value="">-- Danh mục gốc --</option>
                                                        @foreach($parentCategories as $parent)
                                                            @if($parent->id !== $category->id)
                                                                <option value="{{ $parent->id }}" @selected($category->parent_id === $parent->id)>{{ str_repeat('— ', (int) ($parent->_tree_level ?? 0)) }}{{ $parent->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="form-label text-[11px]">Badge</label>
                                                    <input name="badge" value="{{ $category->badge }}" placeholder="Badge" class="form-input w-full text-xs">
                                                </div>
                                                <button class="btn btn-primary btn-md rounded-xl w-full py-2 text-xs font-bold">Lưu thay đổi</button>
                                            </form>
                                        </details>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-50 text-primary hover:bg-rose-100 transition-colors cursor-pointer" {{ $category->products_count > 0 ? 'disabled title=Không_thể_xóa_vì_đang_có_sản_phẩm' : '' }}>
                                                Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-400">
                    Đang hiển thị toàn bộ cây danh mục theo thứ tự phân cấp.
                </div>
            </div>
        </div>

    @else
        <!-- Tab 2: Menu Điều Hướng Bên Trái Trang Chủ -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Column: Add Menu Form (4 cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
                <h3 class="text-sm font-black text-gray-900 mb-1">Thêm menu bên trái mới</h3>
                <p class="text-xs text-gray-500 mb-6">Thêm đường dẫn vào cột menu danh mục bên trái trang chủ</p>

                <form method="POST" action="{{ route('admin.menus.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="menu-title" class="form-label">Tên hiển thị menu <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="menu-title" required placeholder="Ví dụ: Thiết Bị Điện Tử, Thời Trang..." class="form-input w-full">
                    </div>

                    <div>
                        <label for="menu-category" class="form-label">Liên kết danh mục</label>
                        <select name="category_id" id="menu-category" class="form-select w-full">
                            <option value="">-- Không gắn danh mục --</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">Khi chọn danh mục, click vào menu sẽ chuyển thẳng tới trang lọc sản phẩm tương ứng.</p>
                    </div>

                    <div>
                        <label for="menu-sort" class="form-label">Thứ tự sắp xếp</label>
                        <input type="number" name="sort_order" id="menu-sort" value="0" min="0" class="form-input w-full">
                        <p class="text-[10px] text-gray-400 mt-1">Để 0 để tự động xếp sau menu cuối cùng.</p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-md w-full py-2.5 text-xs font-bold shadow-xs">
                        Thêm Menu Mới
                    </button>
                </form>
            </div>

            <!-- Right Column: Menu List Table (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Danh sách menu điều hướng bên trái</h3>
                        <p class="text-xs text-gray-500">Quản lý thứ tự hiển thị, bật/tắt và liên kết danh mục trang chủ</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 font-bold border-b border-gray-100">
                                <th class="pb-3 px-3">Tên menu</th>
                                <th class="pb-3 px-3">Danh mục liên kết</th>
                                <th class="pb-3 px-3">Thứ tự</th>
                                <th class="pb-3 px-3">Trạng thái</th>
                                <th class="pb-3 px-3 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($menus as $menu)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="py-3 px-3">
                                        <span class="font-bold text-gray-900">{{ $menu->title }}</span>
                                    </td>
                                    <td class="py-3 px-3 text-gray-600">
                                        @if($menu->category)
                                            <span class="inline-flex items-center gap-1 font-semibold text-rose-600">
                                                {{ $menu->category->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 font-semibold text-gray-700">
                                        {{ $menu->sort_order }}
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($menu->is_active)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                Hiển thị
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">
                                                Ẩn
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        @if($menu->category)
                                            <a href="{{ route('admin.navigation.flyout', $menu->category->id) }}" class="inline-flex items-center gap-1 mr-2 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                                Quản lý flyout
                                            </a>
                                        @endif
                                        <details class="inline-block text-left mr-2">
                                            <summary class="cursor-pointer px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors">
                                                Sửa
                                            </summary>
                                            <form method="POST" action="{{ route('admin.menus.update', $menu->id) }}" class="absolute right-8 mt-2 z-10 bg-white border border-gray-200 rounded-xl shadow-lg p-4 w-72 space-y-3">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label class="form-label text-[11px]">Tên menu</label>
                                                    <input name="title" value="{{ $menu->title }}" required class="form-input w-full text-xs">
                                                </div>
                                                <div>
                                                    <label class="form-label text-[11px]">Danh mục</label>
                                                    <select name="category_id" class="form-select w-full text-xs">
                                                        <option value="">-- Không gắn danh mục --</option>
                                                        @foreach($allCategories as $cat)
                                                            <option value="{{ $cat->id }}" @selected($menu->category_id === $cat->id)>{{ $cat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="form-label text-[11px]">Thứ tự</label>
                                                    <input name="sort_order" type="number" value="{{ $menu->sort_order }}" min="0" class="form-input w-full text-xs">
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" name="is_active" id="active-{{ $menu->id }}" value="1" @checked($menu->is_active) class="rounded border-gray-300 text-primary focus:ring-primary">
                                                    <label for="active-{{ $menu->id }}" class="text-xs font-semibold text-gray-700 cursor-pointer">Hiển thị trên menu</label>
                                                </div>
                                                <button class="btn btn-primary btn-md rounded-xl w-full py-2 text-xs font-bold">Lưu thay đổi</button>
                                            </form>
                                        </details>

                                        <form method="POST" action="{{ route('admin.menus.destroy', $menu->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa menu này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-50 text-primary hover:bg-rose-100 transition-colors cursor-pointer">
                                                Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-400">
                                        Chưa có menu nào được tạo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
