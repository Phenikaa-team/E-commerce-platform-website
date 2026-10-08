@extends('layouts.admin')

@section('title', 'Quản lý Flyout - '.$category->name)
@section('page_title', 'Quản lý Flyout danh mục')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <a href="{{ route('admin.categories.index', ['tab' => 'menus']) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-primary transition-colors mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Quay lại menu bên trái
            </a>
            <h2 class="text-xl font-black text-gray-900">{{ $category->name }}</h2>
            <p class="text-xs text-gray-500 mt-1">Quản lý độc lập danh mục con và bố cục flyout của thư mục này.</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.navigation.recommendations.regenerate', $category->id) }}" method="POST">
                @csrf
                <button class="btn btn-primary btn-md text-xs font-bold">Cập nhật recommendation</button>
            </form>
            <a href="{{ route('admin.navigation.flyout', $category->id) }}" class="btn btn-outline btn-md text-xs font-bold">Làm mới</a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        <div class="xl:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
                <div class="flex items-start justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Thẻ danh mục phía trên</h3>
                        <p class="text-xs text-gray-500 mt-1">Các mục này thuộc riêng {{ $category->name }}.</p>
                    </div>
                    <span class="px-2 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold">{{ $children->count() }} mục</span>
                </div>

                <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-3 pb-5 mb-5 border-b border-gray-100">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $category->id }}">
                    <label class="form-label">Thêm danh mục con</label>
                    <div class="flex gap-2">
                        <input name="name" required class="form-input flex-1" placeholder="Ví dụ: Laptop Gaming">
                        <button class="btn btn-primary btn-md text-xs font-bold whitespace-nowrap">Thêm</button>
                    </div>
                </form>

                <div class="space-y-2">
                    @forelse($children as $child)
                        <details class="group border border-gray-100 rounded-xl bg-gray-50/60">
                            <summary class="list-none cursor-pointer flex items-center justify-between gap-3 px-3 py-2.5">
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-gray-800 block truncate">{{ $child->name }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $child->products_count }} sản phẩm · /category/{{ $child->slug }}</span>
                                </div>
                                <span class="text-gray-400 group-open:rotate-180 transition-transform">⌄</span>
                            </summary>
                            <div class="px-3 pb-3 pt-1 border-t border-gray-100">
                                <form action="{{ route('admin.categories.update', $child->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="parent_id" value="{{ $category->id }}">
                                    <label class="form-label text-[11px]">Tên danh mục con</label>
                                    <input name="name" value="{{ $child->name }}" required class="form-input w-full text-xs">
                                    <label class="form-label text-[11px]">Badge</label>
                                    <input name="badge" value="{{ $child->badge }}" class="form-input w-full text-xs" placeholder="HOT, MỚI..."><input type="hidden" name="icon_svg" value="{{ $child->icon_svg }}">
                                    <div class="flex items-center justify-between gap-2 pt-1">
                                        <button class="btn btn-primary btn-md text-xs font-bold">Lưu</button>
                                        <button type="submit" form="delete-child-{{ $child->id }}" class="text-[11px] font-bold text-primary hover:underline">Xóa</button>
                                    </div>
                                </form>
                                <form id="delete-child-{{ $child->id }}" action="{{ route('admin.categories.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Xóa danh mục con này?')">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </details>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-200 px-4 py-7 text-center text-xs text-gray-400">
                            Chưa có danh mục con. Hãy thêm mục đầu tiên cho flyout này.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="xl:col-span-7 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex items-start justify-between gap-3 mb-5">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Các cột nội dung recommendation</h3>
                    <p class="text-xs text-gray-500 mt-1">Tiêu đề và cấu hình chỉ áp dụng cho {{ $category->name }}.</p>
                </div>
                <span class="px-2 py-1 rounded-full bg-rose-50 text-primary text-[10px] font-bold">{{ $sections->count() }} cột</span>
            </div>

            <form action="{{ route('admin.navigation.sections.store', $category->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 rounded-xl bg-gray-50/70 border border-gray-100 mb-5">
                @csrf
                <div class="md:col-span-2">
                    <label class="form-label text-[11px]">Tên cột mới</label>
                    <input name="title" required class="form-input w-full text-xs" placeholder="Ví dụ: SẢN PHẨM PHỔ BIẾN">
                </div>
                <div>
                    <label class="form-label text-[11px]">Mô tả</label>
                    <input name="description" class="form-input w-full text-xs" placeholder="Mô tả ngắn cho AI">
                </div>
                <div>
                    <label class="form-label text-[11px]">Số mục AI tối đa</label>
                    <input name="item_limit" type="number" min="1" max="20" value="6" class="form-input w-full text-xs">
                </div>
                <div>
                    <label class="form-label text-[11px]">Thứ tự cột</label>
                    <input name="sort_order" type="number" min="0" value="0" class="form-input w-full text-xs">
                </div>
                <div class="flex items-center gap-4 self-end pb-2">
                    <label class="inline-flex items-center gap-2 text-[11px] font-semibold text-gray-700"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary"> Hiển thị</label>
                    <label class="inline-flex items-center gap-2 text-[11px] font-semibold text-gray-700"><input type="checkbox" name="recommendation_enabled" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary"> Tự động gợi ý</label>
                </div>
                <button class="md:col-span-2 btn btn-primary btn-md w-full text-xs font-bold">Thêm cột</button>
            </form>

            <div class="space-y-3">
                @forelse($sections as $section)
                    <details class="group border border-gray-100 rounded-xl overflow-hidden">
                        <summary class="list-none cursor-pointer flex items-center justify-between gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-7 h-7 rounded-lg bg-rose-50 text-primary flex items-center justify-center text-xs font-black">{{ $section->sort_order + 1 }}</span>
                                <div class="min-w-0"><span class="font-black text-xs text-gray-900 block truncate">{{ $section->title }}</span><span class="text-[10px] text-gray-400">{{ $section->items->count() }} mục quản lý · Tối đa {{ $section->item_limit }} mục · {{ $section->recommendation_enabled ? 'Tự động bật' : 'Tự động tắt' }}</span></div>
                            </div>
                            <span class="text-gray-400 group-open:rotate-180 transition-transform">⌄</span>
                        </summary>
                        <form action="{{ route('admin.navigation.sections.update', [$category->id, $section->id]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 bg-gray-50/60 border-t border-gray-100">
                            @csrf
                            @method('PUT')
                            <div class="md:col-span-2"><label class="form-label text-[11px]">Tên cột</label><input name="title" value="{{ $section->title }}" required class="form-input w-full text-xs"></div>
                            <div><label class="form-label text-[11px]">Mô tả</label><input name="description" value="{{ $section->description }}" class="form-input w-full text-xs"></div>
                            <div><label class="form-label text-[11px]">Số mục tối đa</label><input name="item_limit" type="number" min="1" max="20" value="{{ $section->item_limit }}" class="form-input w-full text-xs"></div>
                            <div><label class="form-label text-[11px]">Thứ tự</label><input name="sort_order" type="number" min="0" value="{{ $section->sort_order }}" class="form-input w-full text-xs"></div>
                            <div class="flex items-center gap-4 self-end pb-2"><label class="inline-flex items-center gap-2 text-[11px] font-semibold"><input type="checkbox" name="is_active" value="1" @checked($section->is_active) class="rounded border-gray-300 text-primary focus:ring-primary"> Hiển thị</label><label class="inline-flex items-center gap-2 text-[11px] font-semibold"><input type="checkbox" name="recommendation_enabled" value="1" @checked($section->recommendation_enabled) class="rounded border-gray-300 text-primary focus:ring-primary"> Tự động gợi ý</label></div>
                            <div class="md:col-span-2 flex items-center justify-between gap-3"><button class="btn btn-primary btn-md text-xs font-bold">Lưu thay đổi</button><button type="submit" form="delete-section-{{ $section->id }}" class="text-[11px] font-bold text-primary hover:underline">Xóa cột</button></div>
                        </form>
                        <div class="p-4 pt-0 bg-gray-50/60">
                            <div class="rounded-xl border border-gray-200 bg-white p-3">
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <div>
                                        <p class="text-[11px] font-black text-gray-800">Các mục trong cột</p>
                                        <p class="text-[10px] text-gray-400">Admin có thể thêm, sửa, xóa độc lập cho danh mục này.</p>
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-400">{{ $section->items->count() }} mục</span>
                                </div>
                                <form action="{{ route('admin.navigation.items.store', [$category->id, $section->id]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-2 mb-3">
                                    @csrf
                                    <input name="name" required class="form-input md:col-span-4 text-xs" placeholder="Tên mục, ví dụ: Apple">
                                    <input name="url" class="form-input md:col-span-4 text-xs" placeholder="URL tùy chọn">
                                    <select name="item_type" class="form-input md:col-span-2 text-xs">
                                        <option value="custom">Tùy chỉnh</option>
                                        <option value="brand">Thương hiệu</option>
                                        <option value="series">Dòng sản phẩm</option>
                                        <option value="accessory">Phụ kiện</option>
                                    </select>
                                    <button class="btn btn-primary btn-md md:col-span-2 text-xs font-bold">Thêm mục</button>
                                    <input type="hidden" name="sort_order" value="0">
                                    <input type="hidden" name="is_active" value="1">
                                </form>
                                <div class="space-y-2">
                                    @forelse($section->items as $item)
                                        <details class="group rounded-lg border border-gray-100">
                                            <summary class="list-none cursor-pointer flex items-center justify-between gap-3 px-3 py-2 hover:bg-gray-50">
                                                <div class="min-w-0"><span class="block truncate text-xs font-bold text-gray-800">{{ $item->name }}</span><span class="text-[10px] text-gray-400">{{ $item->item_type }} · thứ tự {{ $item->sort_order }} · {{ $item->is_active ? 'Đang hiển thị' : 'Đang ẩn' }}</span></div>
                                                <span class="text-gray-400 group-open:rotate-180 transition-transform">⌄</span>
                                            </summary>
                                            <div class="border-t border-gray-100 p-3">
                                                <form action="{{ route('admin.navigation.items.update', [$category->id, $section->id, $item->id]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input name="name" value="{{ $item->name }}" required class="form-input md:col-span-4 text-xs">
                                                    <input name="url" value="{{ $item->url }}" class="form-input md:col-span-4 text-xs" placeholder="URL tùy chọn">
                                                    <select name="item_type" class="form-input md:col-span-2 text-xs">
                                                        @foreach(['custom' => 'Tùy chỉnh', 'brand' => 'Thương hiệu', 'series' => 'Dòng sản phẩm', 'accessory' => 'Phụ kiện'] as $type => $label)
                                                            <option value="{{ $type }}" @selected($item->item_type === $type)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input name="sort_order" type="number" min="0" value="{{ $item->sort_order }}" class="form-input md:col-span-1 text-xs">
                                                    <label class="inline-flex items-center gap-1 text-[10px] font-semibold md:col-span-1"><input type="checkbox" name="is_active" value="1" @checked($item->is_active) class="rounded border-gray-300 text-primary focus:ring-primary"> Hiện</label>
                                                    <button class="btn btn-primary btn-md md:col-span-2 text-xs font-bold">Lưu</button>
                                                    <button type="submit" form="delete-item-{{ $item->id }}" class="md:col-span-2 text-[11px] font-bold text-primary hover:underline">Xóa mục</button>
                                                </form>
                                                <form id="delete-item-{{ $item->id }}" action="{{ route('admin.navigation.items.destroy', [$category->id, $section->id, $item->id]) }}" method="POST" onsubmit="return confirm('Xóa mục này khỏi cột?')">@csrf @method('DELETE')</form>
                                            </div>
                                        </details>
                                    @empty
                                        <p class="rounded-lg border border-dashed border-gray-200 px-3 py-4 text-center text-[10px] text-gray-400">Chưa có mục quản lý. Nếu để trống, hệ thống có thể dùng kết quả recommendation.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <form id="delete-section-{{ $section->id }}" action="{{ route('admin.navigation.sections.destroy', [$category->id, $section->id]) }}" method="POST" onsubmit="return confirm('Xóa cột này khỏi danh mục?')">@csrf @method('DELETE')</form>
                    </details>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 px-4 py-8 text-center text-xs text-gray-400">Chưa có cột nào. Bạn có thể tạo tên cột riêng cho danh mục này.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
