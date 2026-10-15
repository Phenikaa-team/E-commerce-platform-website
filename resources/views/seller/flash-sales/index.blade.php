@extends('layouts.seller')

@section('title', 'Flash Sale sàn - ShopMart Seller')
@section('page_title', 'Đăng ký Flash Sale sàn')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-black text-gray-900">Flash Sale sàn</h1>
        <p class="mt-1 text-xs text-gray-500">Chọn đợt do admin mở, đăng ký sản phẩm và đặt giá Flash Sale riêng cho đợt đó.</p>
    </div>

    @if($campaigns->isEmpty())
        <div class="seller-card px-6 py-14 text-center text-sm text-gray-500">Hiện chưa có đợt Flash Sale nào đang nhận đăng ký.</div>
    @else
        @foreach($campaigns as $campaign)
            @php
                $storeItems = $campaign->items->filter(fn ($item) => $item->product?->store_id === $store->id);
            @endphp
            <section class="seller-card overflow-hidden">
                <div class="seller-card-header flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="seller-card-title">{{ $campaign->name }}</h2>
                        <p class="seller-card-subtitle">
                            @if($campaign->schedule_type === 'range')
                                {{ $campaign->starts_at?->format('d/m/Y H:i') }} – {{ $campaign->ends_at?->format('d/m/Y H:i') }}
                            @else
                                Ngày {{ collect($campaign->recurring_dates ?? [])->map(fn ($date) => substr($date, 3, 2).'/'.substr($date, 0, 2))->implode(', ') }} · {{ substr((string) $campaign->daily_starts_at, 0, 5) }} – {{ substr((string) $campaign->daily_ends_at, 0, 5) }}
                            @endif
                        </p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Đang nhận đăng ký</span>
                </div>

                <form action="{{ route('seller.flash-sales.products.store', $campaign->id) }}" method="POST" class="grid gap-3 border-b border-gray-100 bg-gray-50/60 p-4 md:grid-cols-[1fr_220px_auto] md:items-end">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-gray-700">Sản phẩm của {{ $store->name }}</label>
                        <select name="product_id" required class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm">
                            <option value="">Chọn sản phẩm</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} · {{ number_format((float) $product->regular_selling_price, 0, ',', '.') }}₫</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-gray-700">Giá Flash Sale</label>
                        <input type="number" name="flash_sale_price" min="0" step="0.01" required class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm" placeholder="80000">
                    </div>
                    <button type="submit" class="seller-btn-save">Đăng ký sản phẩm</button>
                </form>

                <div class="p-4">
                    <h3 class="mb-3 text-xs font-black uppercase tracking-wide text-gray-500">Sản phẩm shop đã đăng ký</h3>
                    @if($storeItems->isEmpty())
                        <p class="text-sm text-gray-400">Chưa có sản phẩm nào trong đợt này.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($storeItems as $item)
                                <div class="flex flex-col gap-2 rounded-xl border border-gray-100 p-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $item->product->name }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500">Giá thường: {{ number_format((float) $item->regular_price, 0, ',', '.') }}₫ · Flash Sale: <span class="font-bold text-rose-600">{{ number_format((float) $item->flash_sale_price, 0, ',', '.') }}₫</span></p>
                                    </div>
                                    <form action="{{ route('seller.flash-sales.products.destroy', [$campaign->id, $item->id]) }}" method="POST" onsubmit="return confirm('Rút sản phẩm khỏi đợt Flash Sale?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-50">Rút sản phẩm</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endforeach
    @endif
</div>
@endsection
