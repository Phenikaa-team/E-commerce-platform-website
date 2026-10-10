@extends($isSeller ? 'layouts.seller' : 'layouts.app')

@section('title', $isSeller ? 'Chat khách hàng' : 'Tin nhắn với shop')

@section('content')
<div class="store-chat-page" @if($conversation) data-chat-room data-current-user-id="{{ auth()->id() }}" data-messages-url="{{ $isSeller ? route('seller.chats.messages', $conversation->id) : route('chat.messages', $conversation->id) }}" data-send-url="{{ $isSeller ? route('seller.chats.send', $conversation->id) : route('chat.send', $conversation->id) }}" data-last-message-id="{{ $messages->last()?->id ?? 0 }}" @endif>
    <header class="store-chat-heading">
        <div>
            <p class="store-chat-eyebrow">SHOPMART</p>
            <h1>{{ $isSeller ? 'Chat khách hàng' : 'Tin nhắn với shop' }}</h1>
        </div>
        @if(!$isSeller)
            <a href="{{ route('profile') }}" class="store-chat-back">Tài khoản của tôi</a>
        @endif
    </header>

    <div class="store-chat-layout">
        <aside class="store-chat-inbox" aria-label="Danh sách cuộc trò chuyện">
            <div class="store-chat-inbox-heading">
                <h2>Cuộc trò chuyện</h2>
                <span>{{ $conversations->count() }}</span>
            </div>

            @forelse($conversations as $item)
                @php
                    $itemName = $isSeller ? $item->buyer->name : $item->store->name;
                    $itemUnread = $isSeller ? $item->seller_unread_count : $item->buyer_unread_count;
                    $itemUrl = $isSeller ? route('seller.chats.show', $item->id) : route('chat.show', $item->id);
                @endphp
                <a href="{{ $itemUrl }}" class="store-chat-contact {{ $conversation?->is($item) ? 'is-active' : '' }}">
                    <span class="store-chat-avatar">{{ mb_substr($itemName, 0, 1) }}</span>
                    <span class="store-chat-contact-copy">
                        <span class="store-chat-contact-name">{{ $itemName }}</span>
                        <span class="store-chat-preview">{{ $item->lastMessage?->body ?? 'Bắt đầu trò chuyện' }}</span>
                    </span>
                    @if($itemUnread > 0)
                        <span class="store-chat-unread">{{ $itemUnread }}</span>
                    @endif
                </a>
            @empty
                <p class="store-chat-empty-inbox">{{ $isSeller ? 'Chưa có khách hàng nhắn tin.' : 'Chưa có cuộc trò chuyện. Chọn một gian hàng bên dưới để nhắn tin.' }}</p>
            @endforelse

            @unless($isSeller)
                <section class="store-chat-shop-directory">
                    <div class="store-chat-inbox-heading">
                        <h2>Gian hàng</h2>
                    </div>
                    <form action="{{ route('chat.index') }}" method="GET" class="store-chat-shop-search">
                        <label class="sr-only" for="store-chat-shop-search">Tìm gian hàng</label>
                        <input id="store-chat-shop-search" type="search" name="store_q" value="{{ $storeSearch }}" maxlength="100" placeholder="Tìm shop...">
                        <button type="submit">Tìm</button>
                    </form>
                    @forelse($stores as $store)
                        <form action="{{ route('chat.start', $store->slug) }}" method="POST" class="store-chat-shop-form">
                            @csrf
                            <button type="submit" class="store-chat-contact store-chat-shop-button">
                                <span class="store-chat-avatar">{{ mb_substr($store->name, 0, 1) }}</span>
                                <span class="store-chat-contact-copy">
                                    <span class="store-chat-contact-name">{{ $store->name }}</span>
                                    <span class="store-chat-preview">Mở cuộc trò chuyện</span>
                                </span>
                                <span class="store-chat-start-label">Chat</span>
                            </button>
                        </form>
                    @empty
                        <p class="store-chat-empty-inbox">{{ $storeSearch !== '' ? 'Không tìm thấy gian hàng phù hợp.' : 'Chưa có gian hàng đang hoạt động.' }}</p>
                    @endforelse
                    @if($stores->hasPages())
                        <nav class="store-chat-shop-pagination" aria-label="Phân trang gian hàng">
                            @if($stores->previousPageUrl())
                                <a href="{{ $stores->previousPageUrl() }}">Trước</a>
                            @endif
                            <span>{{ $stores->currentPage() }} / {{ $stores->lastPage() }}</span>
                            @if($stores->nextPageUrl())
                                <a href="{{ $stores->nextPageUrl() }}">Tiếp</a>
                            @endif
                        </nav>
                    @endif
                </section>
            @endunless
        </aside>

        <section class="store-chat-thread" aria-label="Nội dung trò chuyện">
            @if($conversation)
                @php($threadName = $isSeller ? $conversation->buyer->name : $conversation->store->name)
                <header class="store-chat-thread-heading">
                    <span class="store-chat-avatar">{{ mb_substr($threadName, 0, 1) }}</span>
                    <div>
                        <h2>{{ $threadName }}</h2>
                        <p>{{ $isSeller ? 'Khách hàng' : 'Shop' }}</p>
                    </div>
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
                    <label class="sr-only" for="store-chat-message">Tin nhắn</label>
                    <textarea id="store-chat-message" name="body" rows="1" maxlength="2000" placeholder="Nhập tin nhắn..." required></textarea>
                    <button type="submit">Gửi</button>
                    <p class="store-chat-error" data-chat-error role="alert" hidden></p>
                </form>
            @else
                <div class="store-chat-no-selection">
                    <svg class="store-chat-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h8M8 14h5m-9 6 1.5-4A8 8 0 1 1 20 16l-4 1-4 1-4-.5L4 20z"/></svg>
                    <h2>{{ $conversations->isEmpty() ? 'Bắt đầu trò chuyện với shop' : 'Chọn một cuộc trò chuyện' }}</h2>
                    <p>{{ $conversations->isEmpty() ? 'Tìm gian hàng ở danh sách bên trái rồi chọn “Chat” để gửi tin nhắn.' : 'Tin nhắn mới sẽ xuất hiện tại đây.' }}</p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
