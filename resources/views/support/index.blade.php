@extends($isAdmin ? 'layouts.admin' : 'layouts.app')

@section('title', $isAdmin ? 'Hỗ trợ khách hàng' : 'Chat hỗ trợ ShopMart')

@section('content')
<div class="store-chat-page" @if($conversation) data-chat-room data-current-user-id="{{ auth()->id() }}" data-messages-url="{{ $isAdmin ? route('admin.support-chats.messages', $conversation->id) : route('support.messages', $conversation->id) }}" data-send-url="{{ $isAdmin ? route('admin.support-chats.send', $conversation->id) : route('support.send', $conversation->id) }}" data-last-message-id="{{ $messages->last()?->id ?? 0 }}" @endif>
    <header class="store-chat-heading">
        <div>
            <p class="store-chat-eyebrow">SHOPMART</p>
            <h1>{{ $isAdmin ? 'Hỗ trợ khách hàng' : 'Chat hỗ trợ ShopMart' }}</h1>
        </div>
        @unless($isAdmin)
            <div class="flex items-center gap-3">
                <a href="{{ route('support.index') }}" class="store-chat-back">Trung tâm trợ giúp (FAQ)</a>
                <a href="{{ route('profile') }}" class="store-chat-back">Tài khoản của tôi</a>
            </div>
        @endunless
    </header>

    <div class="store-chat-layout">
        <aside class="store-chat-inbox" aria-label="Danh sách yêu cầu hỗ trợ">
            <div class="store-chat-inbox-heading">
                <h2>{{ $isAdmin ? 'Khách hàng' : 'Hỗ trợ' }}</h2>
                <span>{{ $conversations->count() }}</span>
            </div>

            @forelse($conversations as $item)
                @php
                    $itemName = $isAdmin ? $item->customer->name : 'Hỗ trợ ShopMart';
                    $itemUnread = $isAdmin ? $item->admin_unread_count : $item->customer_unread_count;
                    $itemUrl = $isAdmin ? route('admin.support-chats.show', $item->id) : route('support.show', $item->id);
                @endphp
                <a href="{{ $itemUrl }}" class="store-chat-contact {{ $conversation?->is($item) ? 'is-active' : '' }}">
                    <span class="store-chat-avatar">{{ mb_substr($itemName, 0, 1) }}</span>
                    <span class="store-chat-contact-copy">
                        <span class="store-chat-contact-name">{{ $itemName }}</span>
                        <span class="store-chat-preview">{{ $item->lastMessage?->body ?? 'Chưa có tin nhắn' }}</span>
                    </span>
                    @if($itemUnread > 0)
                        <span class="store-chat-unread">{{ $itemUnread }}</span>
                    @endif
                </a>
            @empty
                <p class="store-chat-empty-inbox">{{ $isAdmin ? 'Chưa có yêu cầu hỗ trợ nào.' : 'Bạn chưa có cuộc trò chuyện hỗ trợ.' }}</p>
            @endforelse

            @if($isAdmin && $conversations->hasPages())
                <nav class="store-chat-shop-pagination" aria-label="Phân trang yêu cầu hỗ trợ">
                    @if($conversations->previousPageUrl())
                        <a href="{{ $conversations->previousPageUrl() }}">Trước</a>
                    @endif
                    <span>{{ $conversations->currentPage() }} / {{ $conversations->lastPage() }}</span>
                    @if($conversations->nextPageUrl())
                        <a href="{{ $conversations->nextPageUrl() }}">Tiếp</a>
                    @endif
                </nav>
            @endif
        </aside>

        <section class="store-chat-thread" aria-label="Nội dung hỗ trợ">
            @if($conversation)
                @php($threadName = $isAdmin ? $conversation->customer->name : 'Hỗ trợ ShopMart')
                <header class="store-chat-thread-heading flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="store-chat-avatar">{{ mb_substr($threadName, 0, 1) }}</span>
                        <div>
                            <h2>{{ $threadName }}</h2>
                            <p>{{ $isAdmin ? 'Khách hàng' : 'Đội ngũ hỗ trợ ShopMart' }}</p>
                        </div>
                    </div>
                    @unless($isAdmin)
                        <button type="button" onclick="window.ShopMartAi ? window.ShopMartAi.open() : null" class="btn btn-outline text-xs px-3 py-1.5 flex items-center gap-1.5" title="Hỏi trợ lý AI giải đáp nhanh">
                            <span>Mở Trợ Lý AI</span>
                        </button>
                    @endunless
                </header>

                <div class="store-chat-messages" data-chat-messages aria-live="polite">
                    @foreach($messages as $message)
                        <article class="store-chat-message {{ (int) $message->sender_id === (int) auth()->id() ? 'is-own' : '' }}" data-message-id="{{ $message->id }}">
                            <p>{{ $message->body }}</p>
                            <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('H:i') }}</time>
                        </article>
                    @endforeach
                </div>

                <form class="store-chat-composer" data-chat-form>
                    <label class="sr-only" for="support-chat-message">Tin nhắn</label>
                    <textarea id="support-chat-message" name="body" rows="1" maxlength="2000" placeholder="Nhập tin nhắn..." required></textarea>
                    <button type="submit">Gửi</button>
                    <p class="store-chat-error" data-chat-error role="alert" hidden></p>
                </form>
            @else
                <div class="store-chat-no-selection">
                    <svg class="store-chat-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h8M8 14h5m-9 6 1.5-4A8 8 0 1 1 20 16l-4 1-4 1-4-.5L4 20z"/></svg>
                    @if($isAdmin)
                        <h2>{{ $conversations->isEmpty() ? 'Chưa có yêu cầu hỗ trợ' : 'Chọn một cuộc trò chuyện' }}</h2>
                        <p>{{ $conversations->isEmpty() ? 'Khi khách hàng gửi tin nhắn, cuộc trò chuyện sẽ xuất hiện ở đây.' : 'Chọn khách hàng để xem và trả lời tin nhắn.' }}</p>
                    @else
                        <h2>Bạn cần hỗ trợ từ Admin?</h2>
                        <p>Gửi tin nhắn trực tiếp cho đội ngũ Quản trị viên ShopMart hoặc trò chuyện với Trợ lý AI để được giải đáp ngay lập tức.</p>
                        <div class="mt-5 flex items-center justify-center gap-3 flex-wrap">
                            <form action="{{ route('support.start') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary px-5 py-2.5">Nhắn tin cho Admin</button>
                            </form>
                            <button type="button" onclick="window.ShopMartAi ? window.ShopMartAi.open() : null" class="btn btn-outline px-4 py-2.5 flex items-center gap-2">
                                <span>Trò chuyện với AI</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
