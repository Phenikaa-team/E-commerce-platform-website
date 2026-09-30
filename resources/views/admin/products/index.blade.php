@extends('layouts.admin')

@section('title', 'Quản Lý Sản Phẩm - ShopMart Admin')
@section('page_title', 'Quản Lý Sản Phẩm')

@section('content')
<div class="space-y-6">

    <!-- 1. Header Section: Title, Filter Toolbar & Action Button -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">Sản phẩm</h1>
                <p class="text-xs text-gray-500 mt-0.5">Quản lý sản phẩm theo từng gian hàng. Bạn có thể tìm kiếm, lọc và thao tác nhanh.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Nút Bật/Tắt Bộ Lọc Nâng Cao -->
            <button 
                type="button" 
                id="btn-toggle-filter"
                class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-700 rounded-xl transition-colors cursor-pointer shadow-2xs {{ ($search || $status || $stockStatus) ? 'border-primary text-primary font-bold' : '' }}"
            >
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Bộ lọc</span>
                @if($search || $status || $stockStatus)
                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                @endif
            </button>

            <!-- Quick Category Filter Dropdown -->
            <form method="GET" action="{{ route('admin.products.index') }}" class="inline-block" id="category-filter-form">
                @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
                @if($stockStatus) <input type="hidden" name="stock" value="{{ $stockStatus }}"> @endif

                <select name="category" onchange="document.getElementById('category-filter-form').submit()" class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 focus:outline-hidden focus:border-primary shadow-2xs cursor-pointer">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected((string)$categoryId === (string)$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Nút Thêm sản phẩm -->
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-md text-xs font-bold shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Thêm sản phẩm</span>
            </a>
        </div>
    </div>

    <!-- Thanh Bộ Lọc Mở Rộng (Expandable Filter Drawer) -->
    <div id="filter-drawer" class="{{ ($search || $status || $stockStatus) ? 'block' : 'hidden' }} bg-white rounded-xl p-4 border border-gray-100 shadow-2xs">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tìm kiếm sản phẩm / gian hàng</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        id="product-search-input"
                        value="{{ $search }}" 
                        placeholder="Nhập tên sản phẩm..." 
                        class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-primary outline-hidden"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Danh mục ngành hàng</label>
                <select name="category" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-primary outline-hidden">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected((string)$categoryId === (string)$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Trạng thái bán</label>
                <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-primary outline-hidden">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" @selected($status === 'active')>Đang bán</option>
                    <option value="inactive" @selected($status === 'inactive')>Tạm ẩn</option>
                    <option value="draft" @selected($status === 'draft')>Bản nháp</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tình trạng kho</label>
                    <select name="stock" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-primary outline-hidden">
                        <option value="">Tất cả tồn kho</option>
                        <option value="low" @selected($stockStatus === 'low')>Sắp hết (≤ 5 SP)</option>
                        <option value="out" @selected($stockStatus === 'out')>Hết hàng (0 SP)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary btn-md text-xs font-bold shadow-2xs shrink-0">
                    Áp dụng
                </button>
                @if($search || $categoryId || $status || $stockStatus)
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-md text-gray-600 text-xs font-bold shrink-0" title="Xóa bộ lọc">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 2. Summary KPI Metrics Card Strip (Matching Mockup with 3 metrics) -->
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-2xs grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <!-- Metric 1: Gian hàng -->
        <div class="flex items-center gap-4 py-2 md:py-0 md:pr-6">
            <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-700 shrink-0">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <div class="text-base font-black text-gray-900">{{ number_format($totalStoresCount) }} gian hàng</div>
                <div class="text-xs text-gray-400 mt-0.5">Tổng số gian hàng đang hoạt động</div>
            </div>
        </div>

        <!-- Metric 2: Sản phẩm -->
        <div class="flex items-center gap-4 py-2 md:py-0 md:px-6">
            <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-700 shrink-0">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div class="text-base font-black text-gray-900">{{ number_format($totalProductsCount) }} sản phẩm</div>
                <div class="text-xs text-gray-400 mt-0.5">Tổng số sản phẩm trên sàn</div>
            </div>
        </div>

        <!-- Metric 3: Doanh thu ước tính -->
        <div class="flex items-center justify-between py-2 md:py-0 md:pl-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-700 shrink-0">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-base font-black text-gray-900">₫ {{ number_format($estimatedRevenue30d, 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Doanh thu ước tính (30 ngày)</div>
                </div>
            </div>
            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                12.5%
            </span>
        </div>
    </div>

    <!-- 3. Store-Grouped Product Accordion Cards List -->
    <div class="space-y-4">
        @forelse($stores as $st)
            @php
                $isFirst = $loop->first;
                $prods = $st->products;
                $prodCount = $st->products_count ?? $prods->count();
                $avatarColors = [
                    'bg-blue-600',
                    'bg-gray-900',
                    'bg-orange-500',
                    'bg-emerald-600',
                    'bg-purple-600',
                    'bg-rose-600',
                ];
                $storeColor = $avatarColors[$st->id % count($avatarColors)];
            @endphp

            <div class="store-accordion-card bg-white rounded-xl border border-gray-100 shadow-2xs overflow-hidden" data-store-id="{{ $st->id }}">
                
                <!-- Store Accordion Header Bar -->
                <div class="store-accordion-toggle flex items-center justify-between p-4.5 bg-white hover:bg-gray-50/50 cursor-pointer select-none transition-colors border-b border-gray-100">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Store Icon / Logo -->
                        <div class="w-10 h-10 rounded-xl {{ $storeColor }} text-white flex items-center justify-center font-black text-sm shrink-0 shadow-2xs">
                            @if(!empty($st->logo) && !str_contains($st->logo, 'placeholder'))
                                <img src="{{ $st->logo_url }}" class="w-full h-full object-cover rounded-xl" alt="{{ $st->name }}">
                            @else
                                {{ strtoupper(mb_substr($st->name, 0, 1)) }}
                            @endif
                        </div>

                        <!-- Store Name & Badges -->
                        <div class="flex flex-wrap items-center gap-2.5 min-w-0">
                            <span class="font-bold text-sm text-gray-900 truncate">{{ $st->name }}</span>
                            <span class="text-xs text-gray-400 font-medium">{{ $prodCount }} sản phẩm</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Đang bán
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-5 shrink-0">
                        <div class="text-right text-xs">
                            <span class="text-gray-400 font-medium">Tổng doanh thu:</span>
                            <span class="font-black text-gray-900 ml-1">₫ {{ number_format($st->total_revenue, 0, ',', '.') }}</span>
                        </div>
                        <div class="store-arrow-icon w-6 h-6 flex items-center justify-center text-gray-400 transition-transform duration-200 {{ $isFirst ? 'rotate-180' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Products Table Inside Store (Collapsible Content) -->
                <div class="store-accordion-content {{ $isFirst ? '' : 'hidden' }}">
                    @if($prods->isEmpty())
                        <div class="p-8 text-center text-gray-400 text-xs">
                            Gian hàng này chưa có sản phẩm nào phù hợp với bộ lọc tìm kiếm.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-gray-50/60 text-gray-400 font-bold border-b border-gray-100 text-[11px]">
                                        <th class="py-3 px-4 w-10 text-center">
                                            <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                                        </th>
                                        <th class="py-3 px-3">Sản phẩm</th>
                                        <th class="py-3 px-4">Danh mục</th>
                                        <th class="py-3 px-4">Giá bán</th>
                                        <th class="py-3 px-4">Tồn kho</th>
                                        <th class="py-3 px-4">Trạng thái</th>
                                        <th class="py-3 px-4 text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($prods as $p)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <!-- Checkbox col -->
                                            <td class="py-3.5 px-4 text-center">
                                                <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                                            </td>

                                            <!-- Product thumbnail & Title & ID -->
                                            <td class="py-3.5 px-3 min-w-[280px]">
                                                <div class="flex items-center gap-3">
                                                    <img 
                                                        src="{{ $p->main_image_url }}" 
                                                        alt="{{ $p->name }}" 
                                                        class="w-11 h-11 rounded-lg object-cover border border-gray-100 shrink-0 bg-gray-50"
                                                        loading="lazy"
                                                    >
                                                    <div class="min-w-0">
                                                        <a href="{{ route('product.detail', $p->slug) }}" target="_blank" class="font-bold text-gray-900 hover:text-primary transition-colors block line-clamp-1">
                                                            {{ $p->name }}
                                                        </a>
                                                        <span class="text-[10px] text-gray-400 font-mono">ID: #{{ $p->id }}</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Category -->
                                            <td class="py-3.5 px-4 text-gray-600 font-medium">
                                                {{ $p->category?->name ?? 'Điện thoại & Phụ kiện' }}
                                            </td>

                                            <!-- Price -->
                                            <td class="py-3.5 px-4">
                                                <span class="font-bold text-gray-900 block">₫ {{ number_format($p->price, 0, ',', '.') }}</span>
                                                @if($p->original_price && $p->original_price > $p->price)
                                                    <span class="text-[10px] text-gray-400 line-through block">₫ {{ number_format($p->original_price, 0, ',', '.') }}</span>
                                                @endif
                                            </td>

                                            <!-- Stock -->
                                            <td class="py-3.5 px-4 font-semibold {{ $p->stock <= 5 ? 'text-rose-600' : 'text-gray-700' }}">
                                                {{ number_format($p->stock) }}
                                            </td>

                                            <!-- Status -->
                                            <td class="py-3.5 px-4">
                                                @if($p->status === 'active')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                        Đang bán
                                                    </span>
                                                @elseif($p->status === 'inactive')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                                        <span class="w-1 h-1 rounded-full bg-gray-400"></span>
                                                        Tạm ẩn
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700">
                                                        <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                                        Bản nháp
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Actions: Edit & More dots -->
                                            <td class="py-3.5 px-4 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <!-- Nút sửa (bút chì theo concept) -->
                                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="w-7 h-7 rounded-md hover:bg-gray-100 text-gray-500 hover:text-gray-900 flex items-center justify-center transition-colors" title="Chỉnh sửa sản phẩm">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </a>

                                                    <!-- Dropdown menu thao tác 3 chấm -->
                                                    <details class="inline-block relative text-left">
                                                        <summary class="w-7 h-7 rounded-md hover:bg-gray-100 text-gray-500 hover:text-gray-900 flex items-center justify-center transition-colors cursor-pointer list-none">
                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                                            </svg>
                                                        </summary>
                                                        <div class="absolute right-0 mt-1 w-36 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-20">
                                                            <a href="{{ route('product.detail', $p->slug) }}" target="_blank" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50">
                                                                Xem trên sàn
                                                            </a>
                                                            <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="w-full text-left px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 font-semibold cursor-pointer">
                                                                    Xóa sản phẩm
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </details>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 p-12 text-center text-gray-400 text-xs">
                Không tìm thấy gian hàng hoặc sản phẩm nào phù hợp với điều kiện tìm kiếm.
            </div>
        @endforelse
    </div>

</div>

<!-- Vanilla JS for Accordion toggle & Filter drawer -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Accordion click handling
    document.querySelectorAll('.store-accordion-toggle').forEach(header => {
        header.addEventListener('click', () => {
            const card = header.closest('.store-accordion-card');
            const content = card.querySelector('.store-accordion-content');
            const arrow = card.querySelector('.store-arrow-icon');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        });
    });

    // 2. Toggle Filter Drawer
    const btnToggleFilter = document.getElementById('btn-toggle-filter');
    const filterDrawer = document.getElementById('filter-drawer');
    if (btnToggleFilter && filterDrawer) {
        btnToggleFilter.addEventListener('click', () => {
            filterDrawer.classList.toggle('hidden');
            const searchInput = document.getElementById('product-search-input');
            if (!filterDrawer.classList.contains('hidden') && searchInput) {
                searchInput.focus();
            }
        });
    }
});
</script>
@endsection
