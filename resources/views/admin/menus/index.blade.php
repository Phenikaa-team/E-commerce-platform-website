@extends('layouts.admin')

@section('title', 'Quản Lý Menu Bên Trái - ShopMart Admin')
@section('page_title', 'Quản Lý Danh Mục & Menu Bên Trái')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <!-- Left Column: Add Menu Form (4 cols) -->
    <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
        <h3 class="text-sm font-black text-gray-900 mb-1">Thêm menu mới</h3>
        <p class="text-xs text-gray-500 mb-6">Thêm đường dẫn menu vào thanh danh mục bên trái trang chủ</p>

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
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-gray-400 mt-1">Khi chọn danh mục, click vào menu sẽ chuyển thẳng tới trang danh mục tương ứng.</p>
            </div>

            <div>
                <label for="menu-sort" class="form-label">Thứ tự sắp xếp</label>
                <input type="number" name="sort_order" id="menu-sort" value="0" min="0" class="form-input w-full">
            </div>

            <button type="submit" class="btn btn-primary w-full py-2.5">
                Thêm Menu Mới
            </button>
        </form>
    </div>

    <!-- Right Column: Menu List Table (8 cols) -->
    <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden p-6">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-black text-gray-900">Danh sách menu điều hướng bên trái</h3>
                <p class="text-xs text-gray-500">Quản lý thứ tự hiển thị và liên kết danh mục trang chủ</p>
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
                                <details class="inline-block text-left mr-2">
                                    <summary class="cursor-pointer px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors">
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
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" @selected($menu->category_id === $category->id)>{{ $category->name }}</option>
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
                                        <button class="btn btn-primary w-full py-2 text-xs">Lưu thay đổi</button>
                                    </form>
                                </details>

                                <form method="POST" action="{{ route('admin.menus.destroy', $menu->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa menu này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-50 text-primary hover:bg-rose-100 transition-colors cursor-pointer">
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
@endsection
