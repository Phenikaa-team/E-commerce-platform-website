@extends('layouts.admin')

@section('title', ($faq->exists ? 'Sửa câu hỏi trợ giúp' : 'Thêm câu hỏi trợ giúp') . ' - ShopMart Admin')
@section('page_title', $faq->exists ? 'Chỉnh sửa câu hỏi trợ giúp' : 'Thêm câu hỏi trợ giúp mới')

@section('content')
<div class="max-w-3xl bg-white rounded-2xl border border-gray-100 shadow-xs p-6">
    <div class="pb-4 mb-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h1 class="text-base font-black text-gray-900">{{ $faq->exists ? 'Chỉnh sửa câu hỏi #' . $faq->id : 'Tạo mới câu hỏi trợ giúp' }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">Nội dung sẽ hiển thị ở trang Trung tâm trợ giúp và cung cấp context prompt cho AI Chatbot</p>
        </div>
        <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary btn-md rounded-xl text-xs font-bold">
            Quay lại danh sách
        </a>
    </div>

    <form method="POST" action="{{ $faq->exists ? route('admin.faqs.update', $faq->id) : route('admin.faqs.store') }}" class="space-y-5">
        @csrf
        @if($faq->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="category" class="form-label">Chủ đề câu hỏi <span class="text-rose-500">*</span></label>
                <select name="category" id="category" required class="form-select w-full text-xs">
                    @foreach($categories as $key => $cat)
                        <option value="{{ $key }}" @selected(old('category', $faq->category) === $key)>{{ $cat['icon'] }} {{ $cat['title'] }}</option>
                    @endforeach
                </select>
                @error('category')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sort_order" class="form-label">Thứ tự hiển thị (ưu tiên số nhỏ)</label>
                <input 
                    type="number" 
                    name="sort_order" 
                    id="sort_order" 
                    min="0" 
                    value="{{ old('sort_order', $faq->sort_order ?? 0) }}" 
                    class="form-input w-full text-xs font-semibold"
                >
                @error('sort_order')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="question" class="form-label">Câu hỏi thắc mắc <span class="text-rose-500">*</span></label>
            <input 
                type="text" 
                name="question" 
                id="question" 
                required 
                value="{{ old('question', $faq->question) }}" 
                placeholder="Ví dụ: Làm sao để theo dõi lộ trình đơn hàng?" 
                class="form-input w-full text-xs font-bold text-gray-900"
            >
            @error('question')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="answer" class="form-label">Câu trả lời chi tiết <span class="text-rose-500">*</span></label>
            <textarea 
                name="answer" 
                id="answer" 
                rows="5" 
                required 
                placeholder="Nhập nội dung giải đáp cho khách hàng..." 
                class="form-textarea w-full text-xs leading-relaxed"
            >{{ old('answer', $faq->answer) }}</textarea>
            @error('answer')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="prompt" class="form-label">Gợi ý câu hỏi cho Trợ lý AI (Prompt action)</label>
            <div class="relative">
                <input 
                    type="text" 
                    name="prompt" 
                    id="prompt" 
                    value="{{ old('prompt', $faq->prompt) }}" 
                    placeholder="Ví dụ: Hướng dẫn tôi cách kiểm tra lộ trình đơn hàng..." 
                    class="form-input w-full text-xs pl-8"
                >
                <svg class="w-3.5 h-3.5 text-amber-500 absolute left-2.5 top-3" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l2.4 6.6L21 11l-6.6 2.4L12 20l-2.4-6.6L3 11l6.6-2.4L12 2z"/>
                </svg>
            </div>
            <p class="text-[11px] text-gray-400 mt-1">Khi khách hàng bấm vào nút "Hỏi thêm Chatbot AI" dưới câu hỏi này, câu lệnh này sẽ được gửi tự động vào bot.</p>
            @error('prompt')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    value="1" 
                    class="rounded border-gray-300 text-primary focus:ring-primary/20 w-4 h-4 cursor-pointer"
                    @checked(old('is_active', $faq->is_active ?? true))
                >
                <span class="text-xs font-bold text-gray-700">Kích hoạt hiển thị cho khách hàng</span>
            </label>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="btn btn-primary btn-md rounded-xl px-6 py-2.5 text-xs font-bold shadow-xs">
                {{ $faq->exists ? 'Cập nhật câu hỏi' : 'Lưu câu hỏi mới' }}
            </button>
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary btn-md rounded-xl px-5 py-2.5 text-xs font-bold">
                Hủy bỏ
            </a>
        </div>
    </form>
</div>
@endsection
