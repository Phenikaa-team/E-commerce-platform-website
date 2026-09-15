@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_code . ' - ShopMart')

@section('content')
<div class="page-container py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Persistent User Sidebar -->
        <div class="lg:col-span-3">
            <x-user-sidebar active="orders" />
        </div>

        <div class="lg:col-span-9 space-y-6">
            <!-- Breadcrumb & Back -->
            <div class="flex items-center justify-between">
                <nav class="flex items-center gap-2 text-xs font-medium text-gray-500">
                    <a href="{{ route('home') }}" class="hover:text-primary">Trang chủ</a>
                    <span>/</span>
                    <a href="{{ route('user.orders') }}" class="hover:text-primary">Đơn mua</a>
                    <span>/</span>
                    <span class="text-gray-900 font-bold">#{{ $order->order_code }}</span>
                </nav>

                <a href="{{ route('user.orders') }}" class="text-xs font-semibold text-gray-500 hover:text-primary flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Quay lại danh sách</span>
                </a>
            </div>

    <!-- Header Card -->
    <div class="order-detail-header">
        <div class="order-detail-header__top">
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
                    <button type="submit" class="btn btn-primary text-xs font-bold py-2 px-4 shadow-xs">
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

                <div class="order-stepper">
                    <!-- Progress Line Background -->
                    <div class="order-stepper__track"></div>
                    <!-- Progress Line Active Fill -->
                    <div class="order-stepper__fill"
                         style="width: {{ $currentStep == 1 ? '0%' : ($currentStep == 2 ? '33%' : ($currentStep == 3 ? '66%' : '95%')) }};"></div>

                    @foreach($stages as $stepIndex => $stage)
                        @php
                            $isPassed = $currentStep >= $stepIndex;
                        @endphp
                        <div class="order-stepper__step">
                            <div class="order-stepper__node {{ $isPassed ? 'is-passed' : '' }}">
                                @if($isPassed)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ $stepIndex }}
                                @endif
                            </div>
                            <span class="order-stepper__label {{ $isPassed ? 'is-passed' : '' }}">{{ $stage['title'] }}</span>
                            <span class="order-stepper__desc hidden sm:block">{{ $stage['desc'] }}</span>
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
            <div class="order-detail-card">
                @php
                    $store = $order->store ?? $order->items->first()?->product?->store;
                @endphp
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        @if($store)
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-500 text-white">Yêu thích+</span>
                            <a href="{{ route('store.show', $store->slug ?? $store->id) }}" class="text-xs font-bold text-gray-900 hover:text-primary transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>{{ $store->name }}</span>
                            </a>
                        @else
                            <span class="text-xs font-bold text-gray-900">ShopMart Mall</span>
                        @endif
                    </div>
                    @if($store)
                        <a href="{{ route('store.show', $store->slug ?? $store->id) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                            Xem Shop &rarr;
                        </a>
                    @endif
                </div>

                <h2 class="order-detail-card__title">
                    Sản phẩm trong kiện hàng ({{ $order->items->count() }})
                </h2>

                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center gap-4">
                            <img src="{{ $item->product->main_image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=150&q=80' }}" alt="{{ $item->product_name }}" class="w-16 h-16 object-cover rounded-xl border border-gray-100 shrink-0">

                            <div class="flex-1 min-w-0">
                                <a href="{{ route('product.detail', $item->product->slug ?? '#') }}" class="text-xs sm:text-sm font-bold text-gray-900 hover:text-primary truncate block">
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
                                    @php
                                        $reviewed = $order->reviews->firstWhere('product_id', $item->product_id);
                                    @endphp
                                    @if($reviewed)
                                        <span class="text-[11px] font-bold text-emerald-600 mt-1 inline-flex items-center gap-1 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                            <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span>Đã đánh giá ({{ $reviewed->rating }}★)</span>
                                        </span>
                                    @else
                                        <button type="button" 
                                                class="btn-open-review-modal text-[11px] font-bold text-amber-600 hover:text-amber-700 mt-1 inline-flex items-center gap-1 cursor-pointer bg-amber-50 hover:bg-amber-100/80 px-2.5 py-1 rounded-lg transition-colors border border-amber-200"
                                                data-product-id="{{ $item->product_id }}"
                                                data-product-name="{{ $item->product_name }}"
                                                data-order-id="{{ $order->id }}">
                                            <svg class="w-3.5 h-3.5 fill-amber-500 text-amber-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span>Đánh giá</span>
                                        </button>
                                    @endif
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
            <div class="order-detail-card">
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
            <div class="order-detail-card space-y-3 text-xs">
                <h3 class="font-bold text-gray-400 uppercase tracking-wider mb-3">Chi tiết thanh toán</h3>
                
                <div class="order-summary-row">
                    <span>Tiền hàng:</span>
                    <span class="font-bold text-gray-800">{{ $order->formatted_subtotal }}</span>
                </div>
                <div class="order-summary-row">
                    <span>Phí vận chuyển:</span>
                    <span class="font-bold text-gray-800">{{ $order->formatted_shipping_fee }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="order-summary-row text-emerald-600 font-bold">
                        <span>Giảm giá (Voucher):</span>
                        <span>-{{ $order->formatted_discount }}</span>
                    </div>
                @endif

                <div class="order-summary-row--total">
                    <span class="font-bold text-gray-900">Tổng thanh toán:</span>
                    <span class="order-summary-row__price">{{ $order->formatted_total }}</span>
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
    </div>
</div>

<!-- ==================== REVIEW MODAL ==================== -->
<div id="review-modal" class="modal-backdrop hidden">
    <div class="modal-dialog max-w-lg p-6 sm:p-8">
        <div class="modal-header px-0 pt-0 pb-3 mb-4">
            <h3 class="modal-title text-base">Đánh giá sản phẩm</h3>
            <button type="button" id="btn-close-review-modal" class="modal-close-btn rounded-full bg-gray-100">
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
                <label for="review-comment" class="form-label text-xs">Nhận xét chi tiết của bạn:</label>
                <textarea name="comment" id="review-comment" rows="3" required placeholder="Chia sẻ trải nghiệm sử dụng, chất lượng đóng gói, thời gian giao hàng..." class="form-textarea"></textarea>
            </div>

            <!-- Photo Upload -->
            <div class="mb-6">
                <label class="form-label text-xs">Hình ảnh đính kèm (tùy chọn):</label>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label for="review-images-input" class="px-3.5 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 shadow-2xs transition-all cursor-pointer inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Thêm ảnh đánh giá</span>
                        </label>
                        <button type="button" id="btn-clear-review-images" class="text-xs text-rose-500 hover:underline hidden cursor-pointer">Xóa ảnh đã chọn</button>
                    </div>
                    <input type="file" name="images[]" id="review-images-input" multiple accept="image/jpeg,image/png,image/webp,image/gif,image/avif" class="hidden">
                    <p class="text-[11px] text-gray-400">Định dạng JPG, PNG, WEBP. Tối đa 3MB/ảnh.</p>
                    <div id="review-images-preview-grid" class="flex flex-wrap gap-2 pt-1"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full py-3 shadow-md">
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

    // Review images preview
    const reviewImgInput = document.getElementById('review-images-input');
    const reviewImgGrid = document.getElementById('review-images-preview-grid');
    const btnClearReviewImgs = document.getElementById('btn-clear-review-images');

    if (reviewImgInput && reviewImgGrid) {
        reviewImgInput.addEventListener('change', (e) => {
            reviewImgGrid.innerHTML = '';
            const files = Array.from(e.target.files || []);
            if (files.length > 0 && btnClearReviewImgs) {
                btnClearReviewImgs.classList.remove('hidden');
            }

            files.forEach((file, idx) => {
                if (!file.type.startsWith('image/')) return;
                const card = document.createElement('div');
                card.className = 'image-preview-card image-preview-card--sm';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'image-preview-card__img';
                card.appendChild(img);
                reviewImgGrid.appendChild(card);
            });
        });

        if (btnClearReviewImgs) {
            btnClearReviewImgs.addEventListener('click', () => {
                reviewImgInput.value = '';
                reviewImgGrid.innerHTML = '';
                btnClearReviewImgs.classList.add('hidden');
            });
        }
    }
});
</script>
@endpush
@endsection
