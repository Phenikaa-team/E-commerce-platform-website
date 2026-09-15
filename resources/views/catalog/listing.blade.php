@extends('layouts.app')

@section('title', $pageTitle . ' - ShopMart')
@section('meta_description', 'Khám phá hàng ngàn sản phẩm ' . $pageTitle . ' chính hãng giá tốt, nhiều khuyến mãi tại ShopMart.')

@section('content')
@php
    $clearAllUrl = request()->url() . (request()->filled('q') ? '?q=' . urlencode(request('q')) : '');
@endphp
<div class="catalog-page">
    <div class="page-container">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-4 overflow-x-auto whitespace-nowrap py-1">
            @foreach($breadcrumbs as $index => $crumb)
                @if($loop->last)
                    <span class="font-bold text-gray-900 truncate max-w-[240px]">{{ $crumb['label'] }}</span>
                @else
                    <a href="{{ $crumb['url'] }}" class="hover:text-primary transition-colors shrink-0">{{ $crumb['label'] }}</a>
                    <span class="text-gray-300 shrink-0">/</span>
                @endif
            @endforeach
        </nav>

        <!-- Category / Brand Hero Header (if on category or brand page) -->
        @if($currentCategory)
            <div class="bg-white rounded-2xl p-5 sm:p-6 mb-6 border border-gray-100 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ $currentCategory->name }}</h1>
                    @if($currentCategory->children && $currentCategory->children->isNotEmpty())
                        <div class="flex items-center gap-2 mt-3 flex-wrap">
                            <span class="text-xs font-semibold text-gray-400">Danh mục con:</span>
                            @foreach($currentCategory->children as $child)
                                <a href="{{ route('catalog.category', $child->slug) }}" class="catalog-filter-pill">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="text-xs font-bold text-gray-500 bg-gray-50 px-3 py-1.5 rounded-xl shrink-0">
                    {{ number_format($totalCount) }} sản phẩm
                </div>
            </div>
        @elseif($currentBrand)
            <div class="bg-white rounded-2xl p-5 sm:p-6 mb-6 border border-gray-100 shadow-xs flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-gray-900 to-gray-700 text-white flex items-center justify-center font-black text-lg shadow-sm">
                        {{ strtoupper(substr($currentBrand, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ $currentBrand }}</h1>
                        <p class="text-xs text-gray-500 mt-0.5">Thương hiệu chính hãng tại ShopMart</p>
                    </div>
                </div>
                <div class="text-xs font-bold text-gray-500 bg-gray-50 px-3 py-1.5 rounded-xl shrink-0">
                    {{ number_format($totalCount) }} sản phẩm
                </div>
            </div>
        @else
            <!-- Search Results Header -->
            <div class="mb-5">
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                    {{ $pageTitle }}
                </h1>
                <p class="text-xs text-gray-500 mt-1">
                    Tìm thấy <span class="font-bold text-primary">{{ number_format($totalCount) }}</span> sản phẩm phù hợp
                </p>
            </div>
        @endif

        <!-- Active Filter Chips Bar -->
        @if(!empty($activeFilters))
            <div class="flex items-center gap-2 mb-5 flex-wrap">
                <span class="text-xs font-semibold text-gray-500">Đang lọc theo:</span>
                @foreach($activeFilters as $key => $chip)
                    <a href="{{ $chip['remove_url'] }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-rose-50 text-gray-800 hover:text-primary rounded-full text-xs font-semibold border border-gray-200 hover:border-rose-200 shadow-2xs transition-all group">
                        <span>{{ $chip['label'] }}</span>
                        <span class="text-gray-400 group-hover:text-primary font-black text-sm leading-none">&times;</span>
                    </a>
                @endforeach

                @php
                    $clearAllUrl = request()->url() . (request()->filled('q') ? '?q=' . urlencode(request('q')) : '');
                @endphp
                <a href="{{ $clearAllUrl }}" class="text-xs font-bold text-primary hover:underline ml-1">
                    Xóa tất cả
                </a>
            </div>
        @endif

        <!-- Main Content Area: Sidebar Filter + Product Results -->
        <div class="flex items-start gap-6">

            <!-- ================= DESKTOP SIDEBAR FILTER ================= -->
            <aside class="hidden lg:block w-64 shrink-0 bg-white rounded-2xl p-5 border border-gray-100 shadow-xs sticky top-20">
                <div class="flex items-center justify-between pb-3.5 border-b border-gray-100 mb-4">
                    <div class="flex items-center gap-2">
                        <x-icon name="filter" class="w-4 h-4 text-primary" />
                        <h2 class="text-sm font-extrabold text-gray-900 uppercase tracking-wide">Bộ lọc tìm kiếm</h2>
                    </div>
                    @if(!empty($activeFilters))
                        <a href="{{ $clearAllUrl }}" class="text-[11px] font-semibold text-gray-400 hover:text-primary transition-colors">
                            Xóa hết
                        </a>
                    @endif
                </div>

                <form id="desktop-filter-form" action="{{ request()->url() }}" method="GET" class="space-y-5 text-xs">
                    <!-- Preserve Search Keyword & Sort -->
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <!-- 1. Categories Filter -->
                    @if($availableCategories->isNotEmpty())
                        <div>
                            <h3 class="font-extrabold text-gray-800 uppercase tracking-wider text-[11px] mb-2.5">
                                Theo Danh Mục
                            </h3>
                            <div class="space-y-1 max-h-48 overflow-y-auto pr-1">
                                <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="flex items-center justify-between py-1 px-1.5 rounded-lg {{ !request('category') ? 'text-primary font-bold bg-rose-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition-colors">
                                    <span>Tất cả danh mục</span>
                                </a>
                                @foreach($availableCategories as $cat)
                                    <a href="{{ request()->fullUrlWithQuery(['category' => $cat->slug, 'page' => null]) }}" class="flex items-center justify-between py-1 px-1.5 rounded-lg {{ request('category') == $cat->slug ? 'text-primary font-bold bg-rose-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition-colors">
                                        <span class="truncate pr-1">{{ $cat->name }}</span>
                                        <span class="text-[10px] text-gray-400 shrink-0">({{ $cat->products_count }})</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 2. Brands Filter -->
                    @if($availableBrands->isNotEmpty())
                        <div class="pt-4 border-t border-gray-100">
                            <h3 class="font-extrabold text-gray-800 uppercase tracking-wider text-[11px] mb-2.5">
                                Thương Hiệu
                            </h3>
                            <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                @php
                                    $selectedBrands = (array) request('brand', []);
                                @endphp
                                @foreach($availableBrands as $brandName => $count)
                                    <label class="flex items-center justify-between py-0.5 px-1 hover:bg-gray-50 rounded-md cursor-pointer transition-colors">
                                        <div class="flex items-center gap-2 truncate">
                                            <input 
                                                type="checkbox" 
                                                name="brand[]" 
                                                value="{{ $brandName }}"
                                                {{ in_array($brandName, $selectedBrands) ? 'checked' : '' }}
                                                class="w-3.5 h-3.5 text-primary rounded border-gray-300 focus:ring-rose-500 cursor-pointer"
                                                onchange="document.getElementById('desktop-filter-form').submit()"
                                            >
                                            <span class="text-gray-700 font-medium truncate">{{ $brandName }}</span>
                                        </div>
                                        <span class="text-[10px] text-gray-400 shrink-0">({{ $count }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 3. Price Range Filter -->
                    <div class="pt-4 border-t border-gray-100">
                        <h3 class="font-extrabold text-gray-800 uppercase tracking-wider text-[11px] mb-2.5">
                            Khoảng Giá (₫)
                        </h3>
                        <div class="flex items-center gap-2 mb-2.5">
                            <input 
                                type="number" 
                                name="min_price" 
                                placeholder="Từ" 
                                value="{{ request('min_price') }}"
                                min="0" 
                                step="10000"
                                class="form-input text-xs py-1.5"
                            >
                            <span class="text-gray-400">-</span>
                            <input 
                                type="number" 
                                name="max_price" 
                                placeholder="Đến" 
                                value="{{ request('max_price') }}"
                                min="0" 
                                step="10000"
                                class="form-input text-xs py-1.5"
                            >
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-full">
                            Áp Dụng
                        </button>

                        <!-- Price presets -->
                        <div class="flex flex-col gap-1 mt-2.5">
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => 0, 'max_price' => 500000, 'page' => null]) }}" class="py-0.5 text-[11px] text-gray-500 hover:text-primary transition-colors">
                                Dưới 500.000₫
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => 500000, 'max_price' => 2000000, 'page' => null]) }}" class="py-0.5 text-[11px] text-gray-500 hover:text-primary transition-colors">
                                500.000₫ - 2.000.000₫
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => 2000000, 'max_price' => 10000000, 'page' => null]) }}" class="py-0.5 text-[11px] text-gray-500 hover:text-primary transition-colors">
                                2.000.000₫ - 10.000.000₫
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => 10000000, 'max_price' => null, 'page' => null]) }}" class="py-0.5 text-[11px] text-gray-500 hover:text-primary transition-colors">
                                Trên 10.000.000₫
                            </a>
                        </div>
                    </div>

                    <!-- 4. Rating Filter -->
                    <div class="pt-4 border-t border-gray-100">
                        <h3 class="font-extrabold text-gray-800 uppercase tracking-wider text-[11px] mb-2">
                            Đánh Giá
                        </h3>
                        <div class="space-y-1">
                            @foreach([5 => '5 sao', 4 => 'Từ 4 sao', 3 => 'Từ 3 sao'] as $stars => $text)
                                <a href="{{ request()->fullUrlWithQuery(['rating' => request('rating') == $stars ? null : $stars, 'page' => null]) }}" class="flex items-center gap-1.5 py-1 px-1.5 rounded-lg {{ request('rating') == $stars ? 'bg-rose-50 font-bold' : 'hover:bg-gray-50' }} transition-colors">
                                    <div class="flex items-center text-amber-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <x-icon name="star" class="w-3 h-3 {{ $i <= $stars ? 'fill-amber-400 text-amber-400' : 'text-gray-200' }}" />
                                        @endfor
                                    </div>
                                    <span class="text-[11px] text-gray-700 ml-1">{{ $text }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- 5. Status & Mall Badges Filter -->
                    <div class="pt-4 border-t border-gray-100 space-y-2">
                        <h3 class="font-extrabold text-gray-800 uppercase tracking-wider text-[11px] mb-2">
                            Dịch Vụ & Khuyến Mãi
                        </h3>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="in_stock" 
                                value="1" 
                                {{ request()->boolean('in_stock') ? 'checked' : '' }}
                                class="w-3.5 h-3.5 text-primary rounded border-gray-300 focus:ring-rose-500 cursor-pointer"
                                onchange="document.getElementById('desktop-filter-form').submit()"
                            >
                            <span class="text-gray-700 font-medium">Chỉ hiện còn hàng</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_mall" 
                                value="1" 
                                {{ request()->boolean('is_mall') ? 'checked' : '' }}
                                class="w-3.5 h-3.5 text-primary rounded border-gray-300 focus:ring-rose-500 cursor-pointer"
                                onchange="document.getElementById('desktop-filter-form').submit()"
                            >
                            <span class="text-gray-700 font-medium flex items-center gap-1.5">
                                <span class="badge-mall">Mall</span>
                                ShopMart Mall
                            </span>
                        </label>
                    </div>

                    <!-- Reset Button -->
                    <div class="pt-4 border-t border-gray-100">
                        <a href="{{ $clearAllUrl }}" class="btn btn-outline btn-sm w-full">
                            <x-icon name="refresh" class="w-3.5 h-3.5" />
                            <span>Thiết lập lại</span>
                        </a>
                    </div>
                </form>
            </aside>

            <!-- ================= PRODUCT RESULTS LIST ================= -->
            <main class="flex-1 min-w-0">

                <!-- Sort Bar Toolbar -->
                <div class="catalog-sort-bar">
                    <!-- Left: Sort Options -->
                    <div class="flex items-center gap-1 sm:gap-2 flex-wrap text-xs">
                        <span class="text-gray-400 font-semibold mr-1 hidden sm:inline">Sắp xếp theo:</span>
                        
                        @php
                            $currentSort = request('sort', 'popular');
                            $sortOptions = [
                                'popular' => 'Phổ biến',
                                'newest' => 'Mới nhất',
                                'rating' => 'Đánh giá cao',
                                'price_asc' => 'Giá thấp → cao',
                                'price_desc' => 'Giá cao → thấp',
                            ];
                        @endphp

                        @foreach($sortOptions as $key => $label)
                            <a 
                                href="{{ request()->fullUrlWithQuery(['sort' => $key, 'page' => null]) }}" 
                                class="catalog-filter-pill {{ $currentSort === $key ? 'is-active' : '' }}"
                            >
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Right: Mobile Filter Button (Visible on mobile/tablet) -->
                    <div class="lg:hidden">
                        <button 
                            type="button" 
                            id="mobile-filter-open-btn"
                            class="px-3.5 py-1.5 bg-gray-100 hover:bg-rose-50 hover:text-primary text-gray-800 rounded-xl text-xs font-bold flex items-center gap-1.5 border border-gray-200 transition-colors"
                        >
                            <x-icon name="filter" class="w-3.5 h-3.5 text-primary" />
                            <span>Bộ lọc</span>
                            @if(!empty($activeFilters))
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                            @endif
                        </button>
                    </div>
                </div>

                <!-- Product Grid -->
                @if($products->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-4">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- Useful Empty State -->
                    <div class="bg-white rounded-2xl p-8 sm:p-12 border border-gray-100 text-center shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-primary flex items-center justify-center mx-auto mb-4">
                            <x-icon name="search" class="w-8 h-8" />
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-1.5">
                            Không tìm thấy sản phẩm phù hợp
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto mb-6">
                            @if(request('q'))
                                Không có sản phẩm nào khớp với từ khóa "<strong>{{ request('q') }}</strong>" theo các bộ lọc đã chọn.
                            @else
                                Không có sản phẩm nào phù hợp với bộ lọc hiện tại.
                            @endif
                        </p>

                        <div class="flex items-center justify-center gap-3 flex-wrap">
                            @if(!empty($activeFilters))
                                <a href="{{ $clearAllUrl }}" class="px-5 py-2.5 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-bold transition-all shadow-xs">
                                    Xóa tất cả bộ lọc
                                </a>
                            @endif
                            <a href="{{ route('home') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all">
                                Khám phá trang chủ
                            </a>
                        </div>

                        <!-- Popular Categories Suggestion -->
                        @if($availableCategories->isNotEmpty())
                            <div class="mt-10 pt-6 border-t border-gray-100 text-left">
                                <h3 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider mb-3">
                                    Có thể bạn quan tâm:
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($availableCategories->take(6) as $cat)
                                        <a href="{{ route('catalog.category', $cat->slug) }}" class="px-3 py-1.5 bg-gray-50 hover:bg-rose-50 hover:text-primary text-gray-700 font-medium text-xs rounded-xl border border-gray-200 transition-colors">
                                            {{ $cat->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

            </main>
        </div>

    </div>
</div>

<!-- ================= MOBILE FILTER DRAWER ================= -->
<div id="mobile-filter-drawer" class="fixed inset-0 z-50 hidden transition-all duration-300">
    <!-- Backdrop -->
    <div id="mobile-filter-backdrop" class="fixed inset-0 bg-black/50 backdrop-blur-xs"></div>

    <!-- Drawer Content Panel -->
    <div class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-2xl flex flex-col z-10">
        <!-- Header -->
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-icon name="filter" class="w-4 h-4 text-primary" />
                <h3 class="text-sm font-extrabold text-gray-900 uppercase">Bộ lọc tìm kiếm</h3>
            </div>
            <button type="button" id="mobile-filter-close-btn" class="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg">
                <x-icon name="close" class="w-5 h-5" />
            </button>
        </div>

        <!-- Scrollable Form Body -->
        <form id="mobile-filter-form" action="{{ request()->url() }}" method="GET" class="flex-1 overflow-y-auto p-4 space-y-5 text-xs">
            @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <!-- Categories -->
            @if($availableCategories->isNotEmpty())
                <div>
                    <h4 class="font-extrabold text-gray-800 uppercase text-[11px] mb-2">Theo Danh Mục</h4>
                    <div class="space-y-1 max-h-40 overflow-y-auto">
                        <label class="flex items-center gap-2 py-1">
                            <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="text-primary">
                            <span>Tất cả</span>
                        </label>
                        @foreach($availableCategories as $cat)
                            <label class="flex items-center gap-2 py-1">
                                <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }} class="text-primary">
                                <span class="truncate">{{ $cat->name }} ({{ $cat->products_count }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Brands -->
            @if($availableBrands->isNotEmpty())
                <div class="pt-4 border-t border-gray-100">
                    <h4 class="font-extrabold text-gray-800 uppercase text-[11px] mb-2">Thương Hiệu</h4>
                    <div class="space-y-1.5 max-h-40 overflow-y-auto">
                        @foreach($availableBrands as $brandName => $count)
                            <label class="flex items-center gap-2 py-0.5">
                                <input type="checkbox" name="brand[]" value="{{ $brandName }}" {{ in_array($brandName, (array) request('brand', [])) ? 'checked' : '' }} class="text-primary rounded">
                                <span class="truncate">{{ $brandName }} ({{ $count }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Price -->
            <div class="pt-4 border-t border-gray-100">
                <h4 class="font-extrabold text-gray-800 uppercase text-[11px] mb-2">Khoảng Giá</h4>
                <div class="flex items-center gap-2">
                    <input type="number" name="min_price" placeholder="Từ" value="{{ request('min_price') }}" class="w-full px-2.5 py-1.5 bg-gray-50 border rounded-lg">
                    <span>-</span>
                    <input type="number" name="max_price" placeholder="Đến" value="{{ request('max_price') }}" class="w-full px-2.5 py-1.5 bg-gray-50 border rounded-lg">
                </div>
            </div>

            <!-- Rating -->
            <div class="pt-4 border-t border-gray-100">
                <h4 class="font-extrabold text-gray-800 uppercase text-[11px] mb-2">Đánh Giá</h4>
                <div class="space-y-1">
                    @foreach([5 => '5 sao', 4 => 'Từ 4 sao', 3 => 'Từ 3 sao'] as $stars => $text)
                        <label class="flex items-center gap-2 py-1">
                            <input type="radio" name="rating" value="{{ $stars }}" {{ request('rating') == $stars ? 'checked' : '' }} class="text-primary">
                            <span>{{ $text }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Mall & In Stock -->
            <div class="pt-4 border-t border-gray-100 space-y-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="in_stock" value="1" {{ request()->boolean('in_stock') ? 'checked' : '' }} class="text-primary rounded">
                    <span>Chỉ hiện còn hàng</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_mall" value="1" {{ request()->boolean('is_mall') ? 'checked' : '' }} class="text-primary rounded">
                    <span>ShopMart Mall</span>
                </label>
            </div>
        </form>

        <!-- Drawer Footer Actions -->
        <div class="p-4 border-t border-gray-100 grid grid-cols-2 gap-2 bg-gray-50">
            <a href="{{ $clearAllUrl }}" class="py-2.5 text-center bg-white border border-gray-200 text-gray-700 font-bold rounded-xl text-xs">
                Thiết lập lại
            </a>
            <button type="submit" form="mobile-filter-form" class="btn btn-primary py-2.5 text-xs shadow-xs">
                Áp dụng
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('mobile-filter-open-btn');
    const closeBtn = document.getElementById('mobile-filter-close-btn');
    const drawer = document.getElementById('mobile-filter-drawer');
    const backdrop = document.getElementById('mobile-filter-backdrop');

    const toggleDrawer = (show) => {
        if (!drawer) return;
        if (show) {
            drawer.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            drawer.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    if (openBtn) openBtn.addEventListener('click', () => toggleDrawer(true));
    if (closeBtn) closeBtn.addEventListener('click', () => toggleDrawer(false));
    if (backdrop) backdrop.addEventListener('click', () => toggleDrawer(false));
});
</script>
@endsection
