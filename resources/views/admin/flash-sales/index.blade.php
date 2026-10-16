@extends('layouts.admin')

@section('title', 'Quản lý Flash Sale - ShopMart Admin')
@section('page_title', 'Quản lý Flash Sale')

@section('content')
<div class="space-y-5 max-w-[1600px] w-full mx-auto">
    <!-- Top Header: Title + Subtitle + Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Quản lý đợt Flash Sale</h1>
            <p class="text-xs text-gray-500 mt-1">
                Lên lịch sale toàn sàn cho phép nhà bán hàng (Seller) đăng ký sản phẩm và thiết lập giá sốc.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.flash-sales.schedules.create') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs hover:shadow-md transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>+ Lịch sale thủ công</span>
            </a>
            <a href="{{ route('admin.flash-sales.automations.create') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold shadow-xs hover:shadow-md transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>+ Lịch sale tự động</span>
            </a>
        </div>
    </div>

    <!-- Highlight Banner / Metric Overview -->
    <div class="seller-voucher-hero-card">
        <div class="flex items-center gap-3.5">
            <div class="seller-voucher-hero-icon-box">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m13 2-9 12h7l-1 8 9-12h-7l1-8Z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Chiến dịch Flash Sale sàn ShopMart</h3>
                <p class="text-xs text-gray-500 mt-0.5">Bao gồm đợt sale chọn mốc thời gian cố định và các quy tắc tự động lặp lại hàng năm.</p>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
            <div class="seller-voucher-hero-status">
                <span class="seller-voucher-hero-dot"></span>
                <span><strong class="text-gray-900 font-bold">{{ $activeCount ?? 0 }}</strong> đang kích hoạt</span>
            </div>
            <div class="hidden md:flex items-center gap-2 text-gray-500">
                <span class="inline-flex items-center rounded-md bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700">Thủ công: {{ $manualCount ?? 0 }}</span>
                <span class="inline-flex items-center rounded-md bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700">Tự động: {{ $automationCount ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Main Card with Tab Navigation -->
    <div class="seller-voucher-main-card">
        <div class="mb-4">
            <h2 class="text-base font-bold text-gray-900">Danh sách các đợt Flash Sale</h2>
            <p class="text-xs text-gray-400 mt-0.5">Chọn tab để chuyển đổi nhanh giữa lịch thủ công và lịch sale lặp tự động.</p>
        </div>

        <!-- Filter Tabs Row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 pb-0.5">
            <div class="seller-voucher-tabs-bar border-b-0 pb-0">
                <a href="{{ route('admin.flash-sales.index', ['tab' => 'all']) }}" class="seller-voucher-tab-btn {{ ($tab ?? 'all') === 'all' ? 'is-active' : '' }}">
                    Tất cả ({{ $totalCount ?? 0 }})
                </a>
                <a href="{{ route('admin.flash-sales.index', ['tab' => 'manual']) }}" class="seller-voucher-tab-btn {{ ($tab ?? 'all') === 'manual' ? 'is-active' : '' }}">
                    Lịch thủ công ({{ $manualCount ?? 0 }})
                </a>
                <a href="{{ route('admin.flash-sales.index', ['tab' => 'automation']) }}" class="seller-voucher-tab-btn {{ ($tab ?? 'all') === 'automation' ? 'is-active' : '' }}">
                    Quy tắc tự động ({{ $automationCount ?? 0 }})
                </a>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="mt-4 space-y-6">
            {{-- Section Manual: shown if tab is all or manual --}}
            @if(in_array($tab, ['all', 'manual']))
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50/60 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wide text-rose-600">Lịch thủ công</span>
                            <span class="text-xs text-gray-500 font-medium">Theo ngày giờ cụ thể</span>
                        </div>
                        <a href="{{ route('admin.flash-sales.schedules.create') }}" class="text-xs font-bold text-primary hover:underline">
                            + Thêm thời gian
                        </a>
                    </div>

                    @if($manualCampaigns->isEmpty())
                        <div class="px-6 py-8 text-center text-xs text-gray-400">Chưa có lịch sale thủ công nào.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-gray-100 text-gray-400">
                                        <th class="px-4 py-3">Tên đợt</th>
                                        <th class="px-4 py-3">Bắt đầu</th>
                                        <th class="px-4 py-3">Kết thúc</th>
                                        <th class="px-4 py-3">Sản phẩm</th>
                                        <th class="px-4 py-3">Trạng thái</th>
                                        <th class="px-4 py-3 text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($manualCampaigns as $campaign)
                                        @include('admin.flash-sales.partials.row', ['campaign' => $campaign, 'schedule' => 'manual'])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($manualCampaigns->hasPages())
                            <div class="border-t border-gray-100 px-4 py-3">{{ $manualCampaigns->appends(['tab' => $tab])->links() }}</div>
                        @endif
                    @endif
                </div>
            @endif

            {{-- Section Automation: shown if tab is all or automation --}}
            @if(in_array($tab, ['all', 'automation']))
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50/60 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wide text-sky-600">Quy tắc tự động</span>
                            <span class="text-xs text-gray-500 font-medium">Tự kích hoạt theo ngày đôi hoặc ngày đặc biệt</span>
                        </div>
                        <a href="{{ route('admin.flash-sales.automations.create') }}" class="text-xs font-bold text-sky-600 hover:underline">
                            + Thêm quy tắc
                        </a>
                    </div>

                    @if($automationCampaigns->isEmpty())
                        <div class="px-6 py-8 text-center text-xs text-gray-400">Chưa có quy tắc Flash Sale tự động nào.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-gray-100 text-gray-400">
                                        <th class="px-4 py-3">Tên quy tắc</th>
                                        <th class="px-4 py-3">Ngày tự động</th>
                                        <th class="px-4 py-3">Khung giờ</th>
                                        <th class="px-4 py-3">Sản phẩm</th>
                                        <th class="px-4 py-3">Trạng thái</th>
                                        <th class="px-4 py-3 text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($automationCampaigns as $campaign)
                                        @include('admin.flash-sales.partials.row', ['campaign' => $campaign, 'schedule' => 'automation'])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($automationCampaigns->hasPages())
                            <div class="border-t border-gray-100 px-4 py-3">{{ $automationCampaigns->appends(['tab' => $tab])->links() }}</div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
