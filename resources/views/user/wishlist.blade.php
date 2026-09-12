@extends('layouts.app')

@section('title', 'Sản Phẩm Yêu Thích - ShopMart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Danh Sách Yêu Thích</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Các sản phẩm bạn đã lưu để mua sắm sau này</p>
        </div>

        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-50 text-[#ea384c] text-xs font-bold hover:bg-rose-100 transition-colors shrink-0">
            <span>Tiếp tục tìm kiếm sản phẩm</span>
        </a>
    </div>

    @if($wishlists->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-xs max-w-md mx-auto my-12">
            <div class="w-16 h-16 bg-rose-50 text-[#ea384c] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Chưa có sản phẩm yêu thích</h3>
            <p class="text-xs text-gray-500 mb-6">Hãy nhấn vào biểu tượng trái tim ở bất kỳ sản phẩm nào để lưu lại tại đây.</p>
            <a href="{{ route('home') }}" class="px-6 py-2.5 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl shadow-md transition-all">
                Khám phá sản phẩm hot
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($wishlists as $item)
                @php $product = $item->product; @endphp
                @if($product)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition-all overflow-hidden flex flex-col group relative" id="wishlist-item-{{ $product->id }}">
                        
                        <!-- Product Image -->
                        <a href="{{ route('product.detail', $product->slug) }}" class="block relative aspect-square overflow-hidden bg-gray-50">
                            <img src="{{ $product->main_image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @if($product->discount_percent > 0)
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-black bg-[#ea384c] text-white">
                                    -{{ $product->discount_percent }}%
                                </span>
                            @endif
                        </a>

                        <!-- Remove Wishlist Button -->
                        <button type="button" class="btn-remove-wishlist absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 backdrop-blur-xs text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors shadow-sm cursor-pointer" data-id="{{ $product->id }}" title="Xóa khỏi yêu thích">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </button>

                        <!-- Product Info -->
                        <div class="p-3.5 flex flex-col flex-1">
                            <span class="text-[10px] text-gray-400 truncate">{{ $product->store->name ?? 'ShopMart' }}</span>
                            <a href="{{ route('product.detail', $product->slug) }}" class="text-xs font-bold text-gray-800 hover:text-[#ea384c] line-clamp-2 mt-1 mb-2">
                                {{ $product->name }}
                            </a>
                            
                            <div class="mt-auto pt-2 border-t border-gray-50 flex items-center justify-between">
                                <span class="text-sm font-black text-[#ea384c]">{{ $product->formatted_price }}</span>
                                <button type="button" class="p-2 rounded-lg bg-rose-50 hover:bg-[#ea384c] text-[#ea384c] hover:text-white transition-colors cursor-pointer" data-add-to-cart data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" title="Thêm vào giỏ hàng">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </button>
                            </div>
                        </div>

                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-6">
            {{ $wishlists->links() }}
        </div>
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    document.querySelectorAll('.btn-remove-wishlist').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const prodId = btn.getAttribute('data-id');
            const card = document.getElementById('wishlist-item-' + prodId);

            try {
                const res = await fetch('{{ route("user.wishlist.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: prodId })
                });
                const data = await res.json();
                if (data.success && !data.favorited) {
                    if (card) card.remove();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });
});
</script>
@endpush
@endsection
