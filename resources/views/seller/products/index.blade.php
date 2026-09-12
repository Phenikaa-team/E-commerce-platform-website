@extends('layouts.seller')

@section('title', 'Quản Lý Sản Phẩm - ShopMart Seller')
@section('page_title', 'Danh Sách Sản Phẩm Trong Gian Hàng')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Header -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <form action="{{ route('seller.products.index') }}" method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Tìm theo tên sản phẩm..." class="px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden">
            
            <select name="category" class="px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2">
                <select name="stock" class="flex-1 px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden">
                    <option value="">Tất cả trạng thái kho</option>
                    <option value="low" {{ $stockStatus == 'low' ? 'selected' : '' }}>Sắp hết (≤ 5)</option>
                    <option value="out" {{ $stockStatus == 'out' ? 'selected' : '' }}>Hết hàng (0)</option>
                </select>

                <button type="submit" class="px-4 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                    Lọc
                </button>
            </div>
        </form>

        <a href="{{ route('seller.products.create') }}" class="px-5 py-2.5 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl shadow-md shadow-rose-500/20 transition-all flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Thêm sản phẩm mới</span>
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        @if($products->isEmpty())
            <div class="p-12 text-center text-gray-400 text-xs">
                Chưa có sản phẩm nào phù hợp với bộ lọc.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50/75 text-gray-500 font-bold border-b border-gray-100">
                            <th class="py-3.5 px-6">Sản phẩm</th>
                            <th class="py-3.5 px-4">Danh mục</th>
                            <th class="py-3.5 px-4">Giá bán</th>
                            <th class="py-3.5 px-4">Tồn kho</th>
                            <th class="py-3.5 px-4">Đã bán</th>
                            <th class="py-3.5 px-6 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($products as $prod)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-6 flex items-center gap-3 min-w-[280px]">
                                    <img src="{{ $prod->main_image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=100&q=80' }}" class="w-12 h-12 object-cover rounded-xl border border-gray-100 shrink-0">
                                    <div class="min-w-0">
                                        <a href="{{ route('product.detail', $prod->slug) }}" target="_blank" class="font-bold text-gray-900 hover:text-[#ea384c] truncate block">
                                            {{ $prod->name }}
                                        </a>
                                        <span class="text-[10px] text-gray-400">Thương hiệu: {{ $prod->brand ?? 'N/A' }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-gray-600">
                                    {{ $prod->category->name ?? 'Chưa phân loại' }}
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-[#ea384c]">{{ $prod->formatted_price }}</span>
                                    @if($prod->original_price)
                                        <span class="text-[10px] text-gray-400 line-through block">{{ $prod->formatted_original_price }}</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $prod->stock <= 5 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $prod->stock }} trong kho
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 font-bold text-gray-700">
                                    {{ $prod->sold_count }}
                                </td>

                                <td class="py-3.5 px-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('seller.products.edit', $prod->id) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Chỉnh sửa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <form action="{{ route('seller.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="Xóa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
