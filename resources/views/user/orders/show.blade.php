@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_code . ' - ShopMart')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between mb-6">
        <nav class="flex items-center gap-2 text-xs font-medium text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-[#ea384c]">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('user.orders') }}" class="hover:text-[#ea384c]">Đơn mua</a>
            <span>/</span>
            <span class="text-gray-900 font-bold">#{{ $order->order_code }}</span>
        </nav>

        <a href="{{ route('user.orders') }}" class="text-xs font-semibold text-gray-500 hover:text-[#ea384c] flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Quay lại danh sách</span>
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-xs mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Đơn hàng #{{ $order->order_code }}</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $order->status_badge }}">
                        {{ $order->status_label }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Ngày đặt: {{ $order->created_at->format('H:i - d/m/Y') }}</p>
            </div>

            <div class="flex items-center gap-2">
                @if($order->status === 'pending')
                    <form action="{{ route('user.orders.cancel', $order->order_code) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                            Hủy đơn hàng
                        </button>
                    </form>
                @endif
                <form action="{{ route('user.orders.reorder', $order->order_code) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                        Mua lại đơn này
                    </button>
                </form>
            </div>
        </div>

        <!-- ==================== VISUAL STEPPER TIMELINE ==================== -->
        <div class="pt-8 pb-4">
            @if($order->status === 'cancelled')
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-3 text-xs text-rose-700">
                    <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <p class="font-bold">Đơn hàng đã bị hủy</p>
                        <p class="text-rose-500 mt-0.5">Đơn hàng đã kết thúc và toàn bộ sản phẩm đã được hoàn lại vào kho hàng.</p>
                    </div>
                </div>
            @else
                @php
                    $currentStep = $order->timeline_step;
                    $stages = [
                        1 => ['title' => 'Đặt hàng thành công', 'desc' => $order->created_at->format('H:i d/m')],
                        2 => ['title' => 'Chờ lấy hàng', 'desc' => 'Người bán đang chuẩn bị'],
                        3 => ['title' => 'Đang giao hàng', 'desc' => 'Đang trên đường đến bạn'],
                        4 => ['title' => 'Hoàn thành', 'desc' => 'Đã nhận hàng an toàn'],
                    ];
                @endphp

                <div class="relative flex items-center justify-between">
                    <!-- Progress Line Background -->
                    <div class="absolute left-6 right-6 top-5 h-1 bg-gray-200 -z-0"></div>
                    <!-- Progress Line Active Fill -->
                    <div class="absolute left-6 top-5 h-1 bg-emerald-500 -z-0 transition-all duration-500"
                         style="width: {{ $currentStep == 1 ? '0%' : ($currentStep == 2 ? '33%' : ($currentStep == 3 ? '66%' : '95%')) }};"></div>

                    @foreach($stages as $stepIndex => $stage)
                        @php
                            $isPassed = $currentStep >= $stepIndex;
                            $isCurrent = $currentStep === $stepIndex;
                        @endphp
                        <div class="flex flex-col items-center text-center relative z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all {{ $isPassed ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30 ring-4 ring-emerald-50' : 'bg-gray-100 text-gray-400 border border-gray-200' }}">
                                @if($isPassed)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ $stepIndex }}
                                @endif
                            </div>
                            <span class="text-xs font-bold mt-2 {{ $isPassed ? 'text-gray-900' : 'text-gray-400' }}">{{ $stage['title'] }}</span>
                            <span class="text-[10px] text-gray-400 mt-0.5 hidden sm:block">{{ $stage['desc'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Grid: Details & Items -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Left 2 Cols: Products Table & Review CTA -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xs">
                <h2 class="text-sm font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">
                    Sản phẩm trong kiện hàng ({{ $order->items->count() }})
                </h2>

                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center gap-4">
                            <img src="{{ $item->product->main_image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=150&q=80' }}" alt="{{ $item->product_name }}" class="w-16 h-16 object-cover rounded-xl border border-gray-100 shrink-0">

                            <div class="flex-1 min-w-0">
                                <a href="{{ route('product.detail', $item->product->slug ?? '#') }}" class="text-xs sm:text-sm font-bold text-gray-900 hover:text-[#ea384c] truncate block">
                                    {{ $item->product_name }}
                                </a>
                                @if($item->selected_variant)
                                    <p class="text-xs text-gray-400 mt-0.5">Phân loại: <span class="text-gray-600">{{ $item->selected_variant }}</span></p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">{{ number_format($item->unit_price, 0, ',', '.') }}₫ x {{ $item->quantity }}</p>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-sm font-black text-gray-900 block">{{ $item->formatted_subtotal }}</span>
                                
                                <!-- Rating Button (Active when completed) -->
                                @if($order->status === 'completed')
                                    <button type="button" 
                                            class="btn-open-review-modal text-[11px] font-bold text-amber-600 hover:text-amber-700 mt-1 inline-flex items-center gap-1 cursor-pointer"
                                            data-product-id="{{ $item->product_id }}"
                                            data-product-name="{{ $item->product_name }}"
                                            data-order-id="{{ $order->id }}">
                                        <svg class="w-3.5 h-3.5 fill-amber-500 text-amber-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span>Đánh giá</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Col: Shipping & Payment Summary -->
        <div class="space-y-6">
            
            <!-- Recipient Address -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xs">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Thông tin nhận hàng</h3>
                <p class="font-bold text-gray-800 text-xs">
                    {{ $order->shipping_address['name'] ?? '' }} - {{ $order->shipping_address['phone'] ?? '' }}
                </p>
                <p class="text-gray-500 text-xs mt-1 leading-relaxed">
                    {{ $order->shipping_address['address'] ?? '' }}
                </p>
                @if(!empty($order->shipping_address['notes']))
                    <p class="text-gray-400 text-[11px] mt-2 italic bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                        "{{ $order->shipping_address['notes'] }}"
                    </p>
                @endif
            </div>

            <!-- Payment Summary -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xs space-y-3 text-xs">
                <h3 class="font-bold text-gray-400 uppercase tracking-wider mb-3">Chi tiết thanh toán</h3>
                
                <div class="flex justify-between text-gray-500">
                    <span>Tiền hàng:</span>
                    <span class="font-bold text-gray-800">{{ $order->formatted_subtotal }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Phí vận chuyển:</span>
                    <span class="font-bold text-gray-800">{{ $order->formatted_shipping_fee }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600 font-bold">
                        <span>Giảm giá (Voucher):</span>
                        <span>-{{ $order->formatted_discount }}</span>
                    </div>
                @endif

                <div class="pt-3 border-t border-gray-100 flex items-baseline justify-between text-sm">
                    <span class="font-bold text-gray-900">Tổng thanh toán:</span>
                    <span class="text-xl font-black text-[#ea384c]">{{ $order->formatted_total }}</span>
                </div>

                <div class="pt-3 border-t border-gray-100">
                    <span class="text-gray-400 block text-[11px]">Hình thức thanh toán:</span>
                    <span class="font-bold text-gray-800 text-xs block mt-0.5">{{ $order->payment_method_label }}</span>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán khi nhận hàng' }}
                    </span>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- ==================== REVIEW MODAL ==================== -->
<div id="review-modal" class="fixed inset-0 z-[200] bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-base font-bold text-gray-900">Đánh giá sản phẩm</h3>
            <button type="button" id="btn-close-review-modal" class="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('user.reviews.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="order_id" id="review-order-id" value="{{ $order->id }}">
            <input type="hidden" name="product_id" id="review-product-id" value="">

            <p class="text-xs text-gray-500 mb-4" id="review-product-name-display"></p>

            <!-- Star Rating Selection -->
            <div class="mb-4 text-center">
                <span class="text-xs font-bold text-gray-700 block mb-2">Chất lượng sản phẩm:</span>
                <div class="flex items-center justify-center gap-2" id="star-rating-wrapper">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="star-btn text-amber-400 hover:scale-110 transition-transform cursor-pointer p-1" data-rating="{{ $i }}">
                            <svg class="w-6 h-6 fill-current pointer-events-none" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="review-rating-input" value="5">
            </div>

            <!-- Comment Input -->
            <div class="mb-4">
                <label for="review-comment" class="block text-xs font-semibold text-gray-700 mb-1">Nhận xét chi tiết của bạn:</label>
                <textarea name="comment" id="review-comment" rows="3" required placeholder="Chia sẻ trải nghiệm sử dụng, chất lượng đóng gói, thời gian giao hàng..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden"></textarea>
            </div>

            <!-- Photo Upload -->
            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Hình ảnh đính kèm (tùy chọn):</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-[#ea384c] hover:file:bg-rose-100 cursor-pointer">
            </div>

            <button type="submit" class="w-full py-3 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl transition-all shadow-md cursor-pointer">
                Gửi Đánh Giá Ngay
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const reviewModal = document.getElementById('review-modal');
    const closeBtn = document.getElementById('btn-close-review-modal');
    const prodIdInput = document.getElementById('review-product-id');
    const prodNameDisplay = document.getElementById('review-product-name-display');

    document.querySelectorAll('.btn-open-review-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            prodIdInput.value = btn.getAttribute('data-product-id');
            prodNameDisplay.textContent = 'Sản phẩm: ' + btn.getAttribute('data-product-name');
            reviewModal.classList.remove('hidden');
        });
    });

    if (closeBtn && reviewModal) {
        closeBtn.addEventListener('click', () => reviewModal.classList.add('hidden'));
    }

    // Star selector
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('review-rating-input');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const r = parseInt(star.getAttribute('data-rating'));
            ratingInput.value = r;

            stars.forEach((s, idx) => {
                if (idx < r) {
                    s.classList.add('text-amber-400');
                    s.classList.remove('text-gray-300');
                } else {
                    s.classList.add('text-gray-300');
                    s.classList.remove('text-amber-400');
                }
            });
        });
    });
});
</script>
@endpush
@endsection
