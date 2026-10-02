@extends('layouts.admin')

@section('title', 'Quản lý menu bên trái')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h1 class="text-lg font-black mb-4">Quản lý menu bên trái</h1>
        <form method="POST" action="{{ route('admin.menus.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
            @csrf
            <input name="title" required placeholder="Tên menu" class="form-input">
            <select name="category_id" class="form-select">
                <option value="">Không gắn danh mục</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <input name="sort_order" type="number" value="0" min="0" class="form-input">
            <input type="hidden" name="is_active" value="1">
            <button class="btn btn-primary">Thêm menu</button>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead><tr class="text-left border-b"><th class="p-2">Tên</th><th class="p-2">Danh mục</th><th class="p-2">Thứ tự</th><th class="p-2"></th></tr></thead>
                <tbody>
                    @foreach ($menus as $menu)
                        <tr class="border-b">
                            <td class="p-2 font-bold">{{ $menu->title }}</td>
                            <td class="p-2">{{ $menu->category?->name ?? '—' }}</td>
                            <td class="p-2">{{ $menu->sort_order }}</td>
                            <td class="p-2 text-right">
                                <details class="inline-block mr-2">
                                    <summary class="cursor-pointer text-primary font-bold">Sửa</summary>
                                    <form method="POST" action="{{ route('admin.menus.update', $menu->id) }}" class="bg-gray-50 p-3 mt-2 rounded-lg space-y-2 text-left">
                                        @csrf
                                        @method('PUT')
                                        <input name="title" value="{{ $menu->title }}" required class="form-input">
                                        <select name="category_id" class="form-select">
                                            <option value="">Không gắn danh mục</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @selected($menu->category_id === $category->id)>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <input name="sort_order" type="number" value="{{ $menu->sort_order }}" class="form-input">
                                        <label><input type="checkbox" name="is_active" value="1" @checked($menu->is_active)> Hiển thị</label>
                                        <button class="btn btn-primary">Lưu</button>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('admin.menus.destroy', $menu->id) }}" class="inline" onsubmit="return confirm('Xóa menu?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rose-600 font-bold">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection