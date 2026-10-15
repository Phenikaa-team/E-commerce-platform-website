@extends('layouts.admin')

@php
    $isEdit = $campaign->exists;
    $mode = $mode ?? ($campaign->schedule_type === 'recurring' ? 'automation' : 'manual');
    $scheduleType = $mode === 'automation' ? 'recurring' : 'range';
    $matchingDates = collect(range(1, 12))->map(fn (int $number): string => sprintf('%02d/%02d', $number, $number))->all();
    $allMatchingDates = collect($campaign->recurring_dates ?? [])->sort()->values()->all() === collect($matchingDates)->sort()->values()->all();
    $defaultRecurringDates = collect($campaign->recurring_dates ?? [])
        ->map(fn (string $date): string => substr($date, 3, 2).'/'.substr($date, 0, 2))
        ->implode(', ');
@endphp

@section('title', ($isEdit ? 'Sửa' : 'Tạo').' Flash Sale - ShopMart Admin')
@section('page_title', ($isEdit ? 'Sửa' : 'Tạo').' đợt Flash Sale')

@section('content')
<div class="mx-auto max-w-4xl">
    <form action="{{ $isEdit ? route('admin.flash-sales.update', $campaign->id) : route('admin.flash-sales.store') }}" method="POST" class="space-y-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        <input type="hidden" name="schedule_type" value="{{ $scheduleType }}">

        <div class="seller-card space-y-5 p-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wide {{ $mode === 'automation' ? 'bg-sky-100 text-sky-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ $mode === 'automation' ? 'Phần 2 · Tự động' : 'Phần 1 · Thủ công' }}
                    </span>
                </div>
                <h1 class="mt-3 text-xl font-black text-gray-900">
                    {{ $mode === 'automation' ? 'Tạo lịch Flash Sale tự động' : 'Thêm thời gian Flash Sale thủ công' }}
                </h1>
                <p class="mt-1 text-xs text-gray-500">
                    {{ $mode === 'automation' ? 'Thiết lập ngày lặp hằng năm để hệ thống tự kích hoạt đợt sale theo giờ đã chọn.' : 'Thêm một khoảng thời gian cụ thể. Seller có thể đăng ký sản phẩm vào đúng đợt sale này.' }}
                </p>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold text-gray-700">Tên đợt sale <span class="text-rose-500">*</span></label>
                <input name="name" value="{{ old('name', $campaign->name) }}" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-rose-400" placeholder="Ví dụ: Flash Sale giữa tháng 10">
                @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            @if($mode === 'manual')
                <div class="rounded-2xl border border-rose-100 bg-rose-50/50 p-4">
                    <h2 class="text-sm font-black text-gray-900">Khoảng thời gian sale</h2>
                    <p class="mt-1 text-xs text-gray-500">Ví dụ: từ ngày 14 đến ngày 16. Hai mốc thời gian áp dụng theo giờ máy chủ.</p>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-gray-700">Bắt đầu <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $campaign->starts_at?->format('Y-m-d\\TH:i')) }}" required class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm">
                            @error('starts_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-gray-700">Kết thúc <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $campaign->ends_at?->format('Y-m-d\\TH:i')) }}" required class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm">
                            @error('ends_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-4">
                    <h2 class="text-sm font-black text-gray-900">Quy tắc ngày tự động</h2>
                    <p class="mt-1 text-xs text-gray-500">Hệ thống dùng lại cùng một đợt sale vào các ngày được chọn mỗi năm; không cần tạo thủ công từng ngày.</p>

                    <label class="mt-4 flex items-start gap-3 rounded-xl border border-sky-100 bg-white p-3 text-sm font-bold text-gray-700">
                        <input type="checkbox" name="all_matching_dates" value="1" @checked(old('all_matching_dates', $allMatchingDates)) class="mt-0.5 h-4 w-4 rounded border-gray-300 text-sky-500">
                        <span>
                            Tự động áp dụng tất cả ngày trùng trong năm
                            <span class="mt-0.5 block text-xs font-normal text-gray-500">01/01, 02/02, 03/03 ... 12/12</span>
                        </span>
                    </label>

                    <div class="mt-4">
                        <label class="mb-1.5 block text-xs font-bold text-gray-700">Hoặc nhập ngày lặp thủ công <span class="text-rose-500">*</span></label>
                        <input name="recurring_dates" value="{{ old('recurring_dates', $defaultRecurringDates) }}" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm" placeholder="01/01, 02/02, 03/03">
                        <p class="mt-1 text-xs text-gray-500">Nhập dạng ngày/tháng, cách nhau bằng dấu phẩy. Nếu chọn tất cả ngày trùng, ô này sẽ được bỏ qua.</p>
                        @error('recurring_dates')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-gray-700">Giờ bắt đầu mỗi ngày <span class="text-rose-500">*</span></label>
                            <input type="time" name="daily_starts_at" value="{{ old('daily_starts_at', substr((string) $campaign->daily_starts_at, 0, 5)) }}" required class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm">
                            @error('daily_starts_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-gray-700">Giờ kết thúc mỗi ngày <span class="text-rose-500">*</span></label>
                            <input type="time" name="daily_ends_at" value="{{ old('daily_ends_at', substr((string) $campaign->daily_ends_at, 0, 5)) }}" required class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm">
                            @error('daily_ends_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            @endif

            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $campaign->exists ? $campaign->is_active : true)) class="h-4 w-4 rounded border-gray-300 text-rose-500">
                Cho phép seller đăng ký và bật đợt sale
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.flash-sales.index') }}" class="seller-btn-cancel">Hủy</a>
            <button type="submit" class="seller-btn-save">{{ $isEdit ? 'Lưu thay đổi' : 'Tạo đợt sale' }}</button>
        </div>
    </form>
</div>
@endsection
