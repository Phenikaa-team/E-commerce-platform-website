@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex justify-between mb-5">
            <h1 class="text-lg font-black">Sản phẩm toàn sàn</h1>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ Thêm sản phẩm</a>
        </div>

        <form class="flex gap-2 mb-4">
            <input name="search" value="{{ $search }}" placeholder="Tìm sản phẩm" class="form-input">
            <select name="category" class="form-select">
                <option value="">Tất cả danh mục</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-secondary">Lọc</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-2">Tên</th>
                        <th class="p-2">Danh mục</th>
                        <th class="p-2">Giá</th>
                        <th class="p-2">Kho</th>
                        <th class="p-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="border-b">
                            <td class="p-2 font-bold">{{ $product->name }}</td>
                            <td class="p-2">{{ $product->category?->name ?? 'Chưa phân loại' }}</td>
                            <td class="p-2">{{ number_format($product->price, 0, ',', '.') }}₫</td>
                            <td class="p-2">{{ $product->stock }}</td>
                            <td class="p-2 text-right">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-primary font-bold">Sửa</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="inline" onsubmit="return confirm('Xóa sản phẩm?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="ml-2 text-rose-600 font-bold">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    </div>
@endsection