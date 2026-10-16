@extends('layouts.admin')

@section('title', 'Quản lý câu hỏi trợ giúp (FAQ) - ShopMart Admin')
@section('page_title', 'Quản lý câu hỏi trợ giúp (FAQ)')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
        <div>
            <h2 class="text-base font-black text-gray-900">Danh sách câu hỏi hỗ trợ</h2>
            <p class="text-xs text-gray-500 mt-0.5">Quản lý nội dung hiển thị ở Trung tâm trợ giúp và kịch bản gợi ý cho Trợ lý AI</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('support.index') }}" target="_blank" class="btn btn-secondary btn-md rounded-xl text-xs font-bold inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Xem trang Trợ giúp</span>
            </a>
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary btn-md rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Thêm câu hỏi mới</span>
            </a>
        </div>
    </div>

    <!-- Filter and Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-xs">
        <form method="GET" action="{{ route('admin.faqs.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-5">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Tìm câu hỏi, câu trả lời, prompt AI..." 
                        class="form-input w-full pl-9 text-xs"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <div class="md:col-span-4">
                <select name="category" class="form-select w-full text-xs">
                    <option value="">-- Tất cả chủ đề --</option>
                    @foreach($categories as $key => $cat)
                        <option value="{{ $key }}" @selected($categoryFilter === $key)>{{ $cat['icon'] }} {{ $cat['title'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3 flex items-center gap-2">
                <button type="submit" class="btn btn-primary btn-md text-xs font-bold flex-1">
                    Lọc
                </button>
                @if($search || $categoryFilter)
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary btn-md text-xs font-bold">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- FAQs Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">Thứ tự</th>
                        <th class="py-3 px-4 w-40">Chủ đề</th>
                        <th class="py-3 px-4">Câu hỏi & Câu trả lời</th>
                        <th class="py-3 px-4 w-60">Gợi ý prompt hỏi AI</th>
                        <th class="py-3 px-4 w-28 text-center">Trạng thái</th>
                        <th class="py-3 px-4 w-32 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($faqs as $item)
                        @php
                            $catInfo = $categories[$item->category] ?? ['title' => $item->category, 'icon' => '❓'];
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-3 px-4 text-center font-bold text-gray-400">
                                {{ $item->sort_order }}
                            </td>
                            <td class="py-3 px-4 align-top">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-[11px] font-bold text-slate-700">
                                    <span>{{ $catInfo['icon'] }}</span>
                                    <span>{{ $catInfo['title'] }}</span>
                                </span>
                            </td>
                            <td class="py-3 px-4 align-top">
                                <p class="font-bold text-gray-900 leading-snug">{{ $item->question }}</p>
                                <p class="text-gray-500 text-[11px] mt-1 line-clamp-2 leading-relaxed">{{ $item->answer }}</p>
                            </td>
                            <td class="py-3 px-4 align-top text-gray-500 text-[11px]">
                                @if($item->prompt)
                                    <div class="inline-flex items-center gap-1 text-slate-600 bg-slate-50 border border-slate-200/60 px-2 py-1 rounded-md max-w-full">
                                        <svg class="w-3 h-3 text-amber-500 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l2.4 6.6L21 11l-6.6 2.4L12 20l-2.4-6.6L3 11l6.6-2.4L12 2z"/>
                                        </svg>
                                        <span class="truncate">{{ $item->prompt }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-300 italic">Không có</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 align-top text-center">
                                <form method="POST" action="{{ route('admin.faqs.toggle', $item->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button 
                                        type="submit" 
                                        class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold transition-colors {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                        title="Bấm để bật/tắt hiển thị"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        <span>{{ $item->is_active ? 'Hiển thị' : 'Đang ẩn' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-4 align-top text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a 
                                        href="{{ route('admin.faqs.edit', $item->id) }}" 
                                        class="p-1.5 text-gray-500 hover:text-primary hover:bg-red-50 rounded-lg transition-colors"
                                        title="Chỉnh sửa câu hỏi"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.faqs.destroy', $item->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                            title="Xóa câu hỏi"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="font-medium text-xs text-gray-500">Chưa có câu hỏi hỗ trợ nào phù hợp</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faqs->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
