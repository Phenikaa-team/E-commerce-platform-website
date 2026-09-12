@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục - ShopMart Admin')
@section('page_title', 'Danh Mục Sản Phẩm Đa Cấp')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <!-- Left Column: Add Category Form (4 cols) -->
    <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
        <h3 class="text-sm font-black text-gray-900 mb-1">Thêm danh mục mới</h3>
        <p class="text-xs text-gray-500 mb-6">Tạo mới danh mục cha hoặc danh mục con theo cấu trúc phân cấp</p>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="cat-name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tên danh mục <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="cat-name" required placeholder="Ví dụ: Điện Thoại, Đồng Hồ..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden">
            </div>

            <div>
                <label for="parent_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Danh mục cha (Tùy chọn)</label>
                <select name="parent_id" id="parent_id" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden">
                    <option value="">-- Danh mục gốc (Cấp 1) --</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="badge" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nhãn Badge (Tùy chọn)</label>
                <input type="text" name="badge" id="badge" placeholder="Ví dụ: HOT, SALE, MỚI..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden">
            </div>

            <div>
                <label for="icon_svg" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Mã SVG Icon</label>
                <textarea name="icon_svg" id="icon_svg" rows="3" placeholder="Nhập mã SVG: <svg ...>...</svg>" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden font-mono"></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
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
                                <span class="font-bold text-gray-900 block">{{ $category->name }}</span>
                                @if($category->badge)
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-rose-50 text-[#ea384c] border border-rose-100">
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
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-50 text-[#ea384c] hover:bg-rose-100 transition-colors cursor-pointer" {{ $category->products_count > 0 ? 'disabled title=Không_thể_xóa_vì_đang_có_sản_phẩm' : '' }}>
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>

</div>
@endsection
