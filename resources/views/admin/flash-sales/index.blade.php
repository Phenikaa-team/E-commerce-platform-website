@extends('layouts.admin')

@section('title', 'Quản lý Flash Sale - ShopMart Admin')
@section('page_title', 'Quản lý Flash Sale')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-black text-gray-900">Quản lý Flash Sale</h1>
        <p class="mt-1 text-xs text-gray-500">Tách riêng lịch sale thủ công và quy tắc tự động để dễ quản lý.</p>
    </div>

    <section class="seller-card overflow-hidden">
        <div class="seller-card-header flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="mb-1 inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-rose-600">Phần 1 · Thủ công</div>
                <h2 class="seller-card-title">Lịch Flash Sale thủ công</h2>
                <p class="seller-card-subtitle">Tạo từng đợt theo ngày và giờ cụ thể, ví dụ 14/10 20:00 đến 15/10 00:00.</p>
            </div>
            <a href="{{ route('admin.flash-sales.schedules.create') }}" class="seller-btn-save inline-flex items-center gap-2">
                <span class="text-lg leading-none">+</span>
                <span>Thêm thời gian sale</span>
            </a>
        </div>

        @if($manualCampaigns->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-gray-500">Chưa có lịch sale thủ công.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400">
                            <th class="px-4 pb-3">Tên đợt</th>
                            <th class="px-4 pb-3">Bắt đầu</th>
                            <th class="px-4 pb-3">Kết thúc</th>
                            <th class="px-4 pb-3">Sản phẩm</th>
                            <th class="px-4 pb-3">Trạng thái</th>
                            <th class="px-4 pb-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($manualCampaigns as $campaign)
                            @include('admin.flash-sales.partials.row', ['campaign' => $campaign, 'schedule' => 'manual'])
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">{{ $manualCampaigns->links() }}</div>
        @endif
    </section>

    <section class="seller-card overflow-hidden">
        <div class="seller-card-header flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="mb-1 inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-sky-600">Phần 2 · Tự động</div>
                <h2 class="seller-card-title">Lịch Flash Sale tự động</h2>
                <p class="seller-card-subtitle">Khai báo một lần; hệ thống tự kích hoạt các ngày trùng hoặc ngày đặc biệt hằng năm.</p>
            </div>
            <a href="{{ route('admin.flash-sales.automations.create') }}" class="seller-btn-save inline-flex items-center gap-2">
                <span class="text-lg leading-none">+</span>
                <span>Thêm lịch tự động</span>
            </a>
        </div>

        @if($automationCampaigns->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-gray-500">Chưa có quy tắc Flash Sale tự động.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400">
                            <th class="px-4 pb-3">Tên quy tắc</th>
                            <th class="px-4 pb-3">Ngày tự động</th>
                            <th class="px-4 pb-3">Khung giờ</th>
                            <th class="px-4 pb-3">Sản phẩm</th>
                            <th class="px-4 pb-3">Trạng thái</th>
                            <th class="px-4 pb-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($automationCampaigns as $campaign)
                            @include('admin.flash-sales.partials.row', ['campaign' => $campaign, 'schedule' => 'automation'])
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">{{ $automationCampaigns->links() }}</div>
        @endif
    </section>
</div>
@endsection
