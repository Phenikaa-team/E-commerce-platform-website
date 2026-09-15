@extends('layouts.app')

@section('title', ($store->name ?? 'Cửa hàng') . ' - Gian hàng chính hãng tại ShopMart')
@section('meta_description', 'Khám phá gian hàng ' . ($store->name ?? '') . ' tại ShopMart. ' . ($store->description ?? 'Cam kết chất lượng, bảo hành chính hãng và nhiều voucher giảm giá hấp dẫn.'))

@section('content')
<div class="page-container py-6 space-y-6">

    <!-- ==================== BREADCRUMB ==================== -->
    <nav class="flex items-center gap-2 text-xs font-medium text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Trang chủ</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">{{ $store->name }}</span>
    </nav>

    <!-- ==================== STORE HERO BANNER ==================== -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xs overflow-hidden">
        <!-- Banner Top Cover -->
        <div class="relative h-36 sm:h-48 bg-gradient-to-r from-gray-900 via-gray-800 to-rose-950 overflow-hidden">
            @if($store->banner_url && !str_contains($store->banner_url, 'placeholder'))
                <img src="{{ $store->banner_url }}" alt="{{ $store->name }}" class="w-full h-full object-cover opacity-60">
            @else
                <div class="absolute inset-0 bg-radial from-rose-600/30 to-transparent"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
        </div>

        <!-- Banner Info Card -->
        <div class="px-6 sm:px-8 pb-6 sm:pb-8 pt-3 relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                
                <!-- Left: Avatar & Identity -->
                <div class="lg:col-span-6 flex items-center gap-4">
                    <div class="-mt-14 sm:-mt-16 w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-white p-1 shadow-xl border border-gray-100 shrink-0 relative overflow-hidden z-10">
                        <div class="w-full h-full rounded-xl bg-rose-50 flex items-center justify-center text-primary font-black text-3xl overflow-hidden">
                            @if($store->logo_url && !str_contains($store->logo_url, 'placeholder'))
                                <img src="{{ $store->logo_url }}" alt="{{ $store->name }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_substr($store->name ?? 'S', 0, 1) }}
                            @endif
                        </div>
                        @if($store->is_mall)
                            <span class="badge-mall absolute bottom-1 right-1">
                                Mall
                            </span>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ $store->name }}</h1>
                            @if($store->status === 'active')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                    Đang hoạt động
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ $store->online_status ?? 'Online 5 phút trước' }}</span>
                            <span>•</span>
                            <span>Tham gia {{ $store->created_at ? $store->created_at->format('m/Y') : '2024' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Store Quick Stats & Actions -->
                <div class="lg:col-span-6 flex flex-col sm:flex-row sm:items-center justify-between lg:justify-end gap-4">
                    <!-- Stats Badges -->
                    <div class="grid grid-cols-3 gap-2.5 sm:gap-4 text-center">
                        <div class="px-3 py-2 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="text-sm font-black text-gray-900 flex items-center justify-center gap-1">
                                <svg class="w-4 h-4 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>{{ number_format((float) ($store->rating ?? 4.9), 1) }}</span>
                            </div>
                            <span class="text-[11px] text-gray-500">Đánh giá</span>
                        </div>

                        <div class="px-3 py-2 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="text-sm font-black text-gray-900">{{ $totalProducts }}</div>
                            <span class="text-[11px] text-gray-500">Sản phẩm</span>
                        </div>

                        <div class="px-3 py-2 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="text-sm font-black text-gray-900">{{ $store->response_rate ?? '98%' }}</div>
                            <span class="text-[11px] text-gray-500">Phản hồi chat</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            onclick="alert('Tính năng chat với shop đang được kết nối trong phiên làm việc!')"
                            class="px-4 py-2.5 bg-primary-light hover:bg-rose-100 text-primary text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 cursor-pointer shadow-3xs"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Chat ngay</span>
                        </button>
                    </div>
                </div>

            </div>

            @if($store->description)
                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-600 leading-relaxed max-w-4xl">
                    <p><span class="font-bold text-gray-800">Giới thiệu shop:</span> {{ $store->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== STORE VOUCHERS SECTION ==================== -->
    @if($coupons->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xs">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                <h2 class="text-base font-extrabold text-gray-900">Voucher ưu đãi từ Shop</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($coupons as $coupon)
                    <div class="p-3.5 rounded-2xl bg-gradient-to-r from-rose-50/70 to-orange-50/50 border border-rose-100 flex items-center justify-between gap-3 relative overflow-hidden">
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-black text-primary uppercase tracking-wider block">MÃ: {{ $coupon->code }}</span>
                            <div class="text-xs font-bold text-gray-900">
                                @if($coupon->type === 'percent')
                                    Giảm {{ (int) $coupon->value }}%
                                @else
                                    Giảm {{ number_format($coupon->value, 0, ',', '.') }}₫
                                @endif
                            </div>
                            <span class="text-[10px] text-gray-500 block">Đơn từ {{ number_format($coupon->min_order_value, 0, ',', '.') }}₫</span>
                        </div>
                        <button 
                            type="button"
                            onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); alert('Đã sao chép mã {{ $coupon->code }}!');"
                            class="px-2.5 py-1.5 bg-primary hover:bg-primary-hover text-white text-[11px] font-bold rounded-lg transition-colors shadow-2xs shrink-0 cursor-pointer"
                        >
                            Lưu mã
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ==================== IN-STORE SEARCH & FILTER CONTROLS ==================== -->
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- In-Store Search Form -->
            <form action="{{ route('store.show', $store->slug) }}" method="GET" class="flex-1 max-w-md">
                @if($selectedCategory)
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif
                @if($sort)
                    <input type="hidden" name="sort" value="{{ $sort }}">
                @endif
                <div class="relative flex items-center">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $search }}"
                        placeholder="Tìm sản phẩm tại shop này..." 
                        class="w-full pl-9 pr-20 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-primary focus:outline-hidden"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="submit" class="absolute right-1.5 px-3 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">
                        Tìm
                    </button>
                </div>
            </form>

            <!-- Sorting Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs">
                <span class="text-gray-400 font-medium mr-1 shrink-0">Sắp xếp:</span>
                @php
                    $sortOptions = [
                        'popular' => 'Phổ biến',
                        'newest' => 'Mới nhất',
                        'rating' => 'Đánh giá cao',
                        'price_asc' => 'Giá: Thấp -> Cao',
                        'price_desc' => 'Giá: Cao -> Thấp',
                    ];
                @endphp
                @foreach($sortOptions as $key => $label)
                    <a 
                        href="{{ route('store.show', array_merge(['slug' => $store->slug], request()->query(), ['sort' => $key])) }}"
                        class="px-3 py-1.5 rounded-xl font-bold transition-all shrink-0 {{ $sort === $key ? 'bg-primary text-white shadow-xs' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

        </div>

        <!-- In-Store Categories Filter Pills -->
        @if($categories->isNotEmpty())
            <div class="pt-3 border-t border-gray-100 flex items-center gap-2 overflow-x-auto text-xs pb-1">
                <span class="text-gray-400 font-medium shrink-0">Danh mục shop:</span>
                <a 
                    href="{{ route('store.show', array_merge(['slug' => $store->slug], request()->except('category'))) }}"
                    class="px-3 py-1 rounded-full font-bold transition-all shrink-0 {{ empty($selectedCategory) || $selectedCategory === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Tất cả ({{ $totalProducts }})
                </a>
                @foreach($categories as $cat)
                    <a 
                        href="{{ route('store.show', array_merge(['slug' => $store->slug], request()->query(), ['category' => $cat->slug])) }}"
                        class="px-3 py-1 rounded-full font-bold transition-all shrink-0 {{ $selectedCategory === $cat->slug ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if($search !== '')
            <div class="text-xs text-gray-500 pt-2 flex items-center justify-between">
                <span>Kết quả tìm kiếm cho: <strong class="text-gray-900">"{{ $search }}"</strong> ({{ $products->total() }} sản phẩm)</span>
                <a href="{{ route('store.show', array_merge(['slug' => $store->slug], request()->except('q'))) }}" class="text-primary hover:underline">
                    Xóa tìm kiếm
                </a>
            </div>
        @endif
    </div>

    <!-- ==================== PRODUCT GRID ==================== -->
    <div class="space-y-6">
        @if($products->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-xs space-y-3">
                <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800">Không tìm thấy sản phẩm nào</h3>
                <p class="text-xs text-gray-400 max-w-sm mx-auto">Cửa hàng chưa có sản phẩm phù hợp với bộ lọc hiện tại hoặc từ khóa tìm kiếm của bạn.</p>
                <a href="{{ route('store.show', $store->slug) }}" class="inline-block px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl transition-colors">
                    Xem tất cả sản phẩm của Shop
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
