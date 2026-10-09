@extends('layouts.admin')

@section('title', 'Quản Lý & Kiểm Duyệt Đánh Giá - ShopMart Admin')

@section('content')
<div class="space-y-6">

    <!-- Page Header & Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-6 bg-amber-500 rounded-full inline-block"></span>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Kiểm Duyệt & Đánh Giá Sản Phẩm</h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">Giám sát các nhận xét, hình ảnh phản hồi từ người mua để đảm bảo chất lượng và ngăn chặn spam</p>
        </div>

        <div class="flex items-center gap-2.5">
            <div class="px-4 py-2 bg-white rounded-xl border border-gray-200 text-xs shadow-2xs">
                <span class="text-gray-400 block text-[10px] uppercase font-bold">Tổng đánh giá</span>
                <span class="font-black text-gray-900">{{ number_format($statusCounts['all']) }}</span>
            </div>
            <div class="px-4 py-2 bg-white rounded-xl border border-gray-200 text-xs shadow-2xs">
                <span class="text-gray-400 block text-[10px] uppercase font-bold">Đã duyệt</span>
                <span class="font-black text-emerald-600">{{ number_format($statusCounts['approved']) }}</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <!-- Status Tabs -->
            <div class="flex items-center gap-1 overflow-x-auto text-xs font-semibold">
                <a href="{{ route('admin.reviews.index', ['status' => 'all', 'q' => $search, 'rating' => $rating]) }}" 
                   class="px-3 py-1.5 rounded-lg transition-all {{ $status === 'all' ? 'bg-primary-light text-primary font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                    Tất cả ({{ $statusCounts['all'] }})
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'approved', 'q' => $search, 'rating' => $rating]) }}" 
                   class="px-3 py-1.5 rounded-lg transition-all {{ $status === 'approved' ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                    Đã duyệt ({{ $statusCounts['approved'] }})
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'pending', 'q' => $search, 'rating' => $rating]) }}" 
                   class="px-3 py-1.5 rounded-lg transition-all {{ $status === 'pending' ? 'bg-amber-50 text-amber-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                    Chờ duyệt ({{ $statusCounts['pending'] }})
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'rejected', 'q' => $search, 'rating' => $rating]) }}" 
                   class="px-3 py-1.5 rounded-lg transition-all {{ $status === 'rejected' ? 'bg-rose-50 text-rose-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                    Bị ẩn ({{ $statusCounts['rejected'] }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Tìm người dùng, sản phẩm..." 
                           class="w-full pl-9 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-primary outline-hidden transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 transition-colors shrink-0">
                    Lọc
                </button>
            </form>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/70 text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="py-3.5 px-5">Khách hàng</th>
                        <th class="py-3.5 px-4">Sản phẩm & Gian hàng</th>
                        <th class="py-3.5 px-4 text-center">Đánh giá</th>
                        <th class="py-3.5 px-5">Nội dung nhận xét</th>
                        <th class="py-3.5 px-4 text-center">Trạng thái</th>
                        <th class="py-3.5 px-5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reviews as $rev)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <!-- User info -->
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $rev->user->avatar_url ?? project_asset('images/placeholders/avatar-placeholder.svg') }}" 
                                         alt="{{ $rev->user->name ?? 'User' }}" 
                                         class="w-8 h-8 rounded-full object-cover border border-gray-100 bg-gray-50 shrink-0">
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-900 truncate">{{ $rev->user->name ?? 'Người dùng' }}</div>
                                        <div class="text-[10px] text-gray-400 truncate">{{ $rev->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Product info -->
                            <td class="py-4 px-4">
                                <div class="min-w-0 max-w-xs">
                                    <div class="font-bold text-gray-900 truncate" title="{{ $rev->product->name ?? 'N/A' }}">
                                        {{ $rev->product->name ?? 'Sản phẩm đã xóa' }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        Gian hàng: <span class="text-gray-600 font-medium">{{ $rev->product->store->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Rating -->
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-amber-50 text-amber-700 font-bold">
                                    <span>{{ $rev->rating }}</span>
                                    <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </div>
                            </td>

                            <!-- Comment & Date -->
                            <td class="py-4 px-5">
                                <div class="text-gray-700 max-w-sm line-clamp-2 leading-relaxed">
                                    {{ $rev->comment }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1">
                                    {{ $rev->created_at ? $rev->created_at->format('d/m/Y H:i') : '' }}
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-4 text-center">
                                @if($rev->status === 'approved')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        Đã duyệt
                                    </span>
                                @elseif($rev->status === 'pending')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                        Chờ duyệt
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                                        Đã ẩn
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($rev->status !== 'approved')
                                        <form action="{{ route('admin.reviews.status', $rev->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl text-[11px] font-bold transition-colors cursor-pointer" title="Duyệt hiển thị">
                                                ✓ Duyệt
                                            </button>
                                        </form>
                                    @endif

                                    @if($rev->status !== 'rejected')
                                        <form action="{{ route('admin.reviews.status', $rev->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2.5 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl text-[11px] font-bold transition-colors cursor-pointer" title="Ẩn đánh giá">
                                                Ẩn
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-[11px] font-bold transition-colors cursor-pointer" title="Xóa vĩnh viễn">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                Không tìm thấy đánh giá nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
