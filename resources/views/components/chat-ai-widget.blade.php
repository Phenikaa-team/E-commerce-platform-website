<div id="shopmart-ai-widget" class="ai-chat-widget">
    <!-- Chat Button Toggle -->
    <button 
        type="button" 
        id="ai-chat-toggle-btn" 
        class="ai-chat-toggle-btn" 
        aria-label="Mở trợ lý AI ShopMart"
        title="Trò chuyện với Trợ lý AI ShopMart"
    >
        <span class="ai-chat-toggle-icon">
            <svg class="ai-icon-sparkle" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l2.4 6.6L21 11l-6.6 2.4L12 20l-2.4-6.6L3 11l6.6-2.4L12 2z"/>
            </svg>
            <svg class="ai-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </span>
        <span class="ai-chat-status-dot"></span>
    </button>

    <!-- Chat Modal Window -->
    <div id="ai-chat-window" class="ai-chat-window hidden" role="dialog" aria-labelledby="ai-chat-title">
        <!-- Header -->
        <div class="ai-chat-header">
            <div class="ai-chat-header-info">
                <div class="ai-avatar-wrapper">
                    <span class="ai-avatar-inner">
                        <svg class="ai-icon-sparkle-fill" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l2.4 6.6L21 11l-6.6 2.4L12 20l-2.4-6.6L3 11l6.6-2.4L12 2z"/>
                        </svg>
                    </span>
                    <span class="ai-status-indicator"></span>
                </div>
                <div>
                    <h3 id="ai-chat-title" class="ai-chat-name">ShopMart AI</h3>
                    <p class="ai-chat-status">Tư vấn viên AI trực tuyến</p>
                </div>
            </div>
            <div class="ai-chat-header-actions">
                <button type="button" id="ai-chat-clear-btn" class="ai-header-btn" title="Xóa lịch sử chat" aria-label="Xóa lịch sử chat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
                <div class="ai-header-divider"></div>
                <button type="button" id="ai-chat-close-btn" class="ai-header-btn" title="Đóng cửa sổ" aria-label="Đóng cửa sổ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div id="ai-chat-messages" class="ai-chat-messages">
            <!-- Welcome Bot Message -->
            <div class="ai-msg-row ai-msg-bot">
                <div class="ai-msg-avatar">
                    <svg class="ai-avatar-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l2.4 6.6L21 11l-6.6 2.4L12 20l-2.4-6.6L3 11l6.6-2.4L12 2z"/>
                    </svg>
                </div>
                <div class="ai-msg-bubble">
                    <div class="ai-msg-text">
                        Xin chào quý khách! 👋<br>
                        ShopMart AI rất vui được hỗ trợ bạn. Bạn cần tư vấn về sản phẩm, chính sách mua sắm, đổi trả hay chương trình khuyến mãi nào không ạ? Hãy cho mình biết nhé!
                    </div>
                    <div class="ai-msg-meta">
                        <span class="ai-msg-time">00:24</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Suggestions -->
        <div id="ai-quick-suggestions" class="ai-quick-suggestions">
            <button type="button" class="ai-suggest-chip" data-prompt="Gợi ý sản phẩm hot nhất hôm nay">
                <span class="ai-chip-icon chip-fire">🔥</span>
                <span>Sản phẩm hot</span>
            </button>
            <button type="button" class="ai-suggest-chip" data-prompt="Có mã giảm giá nào đang áp dụng không?">
                <span class="ai-chip-icon chip-ticket">🏷️</span>
                <span>Mã giảm giá</span>
            </button>
            <button type="button" class="ai-suggest-chip" data-prompt="Chính sách bảo hành và đổi trả thế nào?">
                <span class="ai-chip-icon chip-shield">🛡️</span>
                <span>Chính sách đổi trả</span>
            </button>
        </div>

        <!-- Input Bar -->
        <form id="ai-chat-form" class="ai-chat-input-bar">
            <button type="button" class="ai-attach-btn" title="Thêm tập tin hoặc tác vụ" aria-label="Thêm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
            <div class="ai-input-wrap">
                <input 
                    type="text" 
                    id="ai-chat-input" 
                    class="ai-input-field" 
                    placeholder="Nhập câu hỏi của bạn..." 
                    autocomplete="off"
                    maxlength="500"
                    required
                >
            </div>
            <button type="submit" id="ai-chat-send-btn" class="ai-send-btn" aria-label="Gửi tin nhắn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                </svg>
            </button>
        </form>
    </div>
</div>
