@extends('layouts.app')

@section('title', 'Trung tâm trợ giúp & Câu hỏi thường gặp - ShopMart')
@section('meta_description', 'Giải đáp các thắc mắc thường gặp về đơn hàng, thanh toán, vận chuyển, đổi trả và hỗ trợ trực tuyến 24/7 tại ShopMart.')

@section('content')
<div class="help-center-page">
    <!-- Hero Banner with Live Search -->
    <section class="help-center-hero">
        <div class="help-center-hero-container">
            <span class="help-center-tag">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Trung Tâm Trợ Giúp</span>
            </span>
            <h1 class="help-center-title">Chúng tôi có thể giúp gì cho bạn?</h1>
            <p class="help-center-subtitle">
                Tìm kiếm câu trả lời nhanh chóng hoặc trò chuyện với Trợ lý AI và đội ngũ Quản trị viên ShopMart.
            </p>

            <div class="help-center-search-wrap">
                <svg class="help-center-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    id="faq-search-input" 
                    class="help-center-search-input" 
                    placeholder="Tìm câu hỏi, từ khóa (ví dụ: đổi trả, vận chuyển, thanh toán, hủy đơn...)" 
                    autocomplete="off"
                >
            </div>
        </div>
    </section>

    <!-- Main Content Container -->
    <div class="help-center-container">
        <!-- Category Filter Bar (Slightly larger, crisp SVG icons, clean rounded borders) -->
        <nav class="help-center-category-bar" aria-label="Lọc chủ đề trợ giúp">
            <button type="button" class="help-category-btn is-active" data-filter="all">
                <svg class="help-category-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                </svg>
                <span>Tất cả câu hỏi</span>
            </button>
            @foreach($faqs as $catKey => $category)
                <button type="button" class="help-category-btn" data-filter="{{ $catKey }}">
                    @if($catKey === 'orders')
                        <svg class="help-category-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                            <path d="m3.3 7 8.7 5 8.7-5"/>
                            <path d="M12 22V12"/>
                        </svg>
                    @elseif($catKey === 'shipping')
                        <svg class="help-category-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                            <path d="M15 18H9"/>
                            <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.24-4.05a1 1 0 0 0-.78-.38H14v8h1"/>
                            <circle cx="17" cy="18.5" r="2.5"/>
                            <circle cx="7" cy="18.5" r="2.5"/>
                        </svg>
                    @elseif($catKey === 'payment')
                        <svg class="help-category-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                    @elseif($catKey === 'returns')
                        <svg class="help-category-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    @elseif($catKey === 'account')
                        <svg class="help-category-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/>
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/>
                            <path d="M2 7h20"/>
                            <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"/>
                        </svg>
                    @else
                        <svg class="help-category-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                            <path d="M12 17h.01"/>
                        </svg>
                    @endif
                    <span>{{ $category['title'] }}</span>
                </button>
            @endforeach
        </nav>

        <!-- Main Grid Layout -->
        <div class="help-center-grid">
            <!-- Left Side: FAQs Accordion -->
            <div class="help-faq-group" id="faq-list">
                @foreach($faqs as $catKey => $category)
                    @foreach($category['items'] as $index => $item)
                        <article class="help-faq-item" data-category="{{ $catKey }}" data-question="{{ mb_strtolower($item['q']) }}" data-answer="{{ mb_strtolower($item['a']) }}">
                            <button type="button" class="help-faq-trigger" aria-expanded="false">
                                <div class="help-faq-badge-wrap">
                                    <span class="help-faq-badge">
                                        @if($catKey === 'orders')
                                            @if($index === 0)
                                                {{-- Package / Box icon --}}
                                                <svg class="help-faq-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                                    <path d="m3.3 7 8.7 5 8.7-5"/>
                                                    <path d="M12 22V12"/>
                                                </svg>
                                            @elseif($index === 1)
                                                {{-- Credit Card / Payment Cancel icon --}}
                                                <svg class="help-faq-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                                                    <line x1="2" x2="22" y1="10" y2="10"/>
                                                </svg>
                                            @else
                                                {{-- Undo / Discount / Return curve arrow icon --}}
                                                <svg class="help-faq-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 7v6h6"/>
                                                    <path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/>
                                                </svg>
                                            @endif
                                        @elseif($catKey === 'shipping')
                                            @if($index === 0)
                                                {{-- Delivery Truck --}}
                                                <svg class="help-faq-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                                                    <path d="M15 18H9"/>
                                                    <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.24-4.05a1 1 0 0 0-.78-.38H14v8h1"/>
                                                    <circle cx="17" cy="18.5" r="2.5"/>
                                                    <circle cx="7" cy="18.5" r="2.5"/>
                                                </svg>
                                            @else
                                                {{-- Inspection / Shield --}}
                                                <svg class="help-faq-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                                                    <path d="m9 12 2 2 4-4"/>
                                                </svg>
                                            @endif
                                        @elseif($catKey === 'payment')
                                            <svg class="help-faq-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="20" height="14" x="2" y="5" rx="2"/>
                                                <line x1="2" x2="22" y1="10" y2="10"/>
                                            </svg>
                                        @elseif($catKey === 'returns')
                                            <svg class="help-faq-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                                                <path d="m9 12 2 2 4-4"/>
                                            </svg>
                                        @elseif($catKey === 'account')
                                            <svg class="help-faq-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/>
                                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                                                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/>
                                            </svg>
                                        @else
                                            <svg class="help-faq-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/>
                                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                                <path d="M12 17h.01"/>
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="help-faq-question">{{ $item['q'] }}</span>
                                </div>
                                <svg class="help-faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="help-faq-content">
                                <p>{{ $item['a'] }}</p>
                                <div class="help-faq-actions">
                                    <button 
                                        type="button" 
                                        class="help-ask-bot-btn" 
                                        data-prompt="{{ $item['prompt'] }}"
                                        title="Nhờ AI tư vấn chi tiết hơn về vấn đề này"
                                    >
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l2.4 6.6L21 11l-6.6 2.4L12 20l-2.4-6.6L3 11l6.6-2.4L12 2z"/>
                                        </svg>
                                        <span>Hỏi thêm Chatbot AI</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @endforeach

                <!-- Empty Search Fallback State -->
                <div class="help-no-results hidden" id="faq-no-results">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-base font-bold text-gray-800">Không tìm thấy câu hỏi phù hợp</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                        Bạn có thể hỏi trực tiếp Trợ lý AI hoặc gửi yêu cầu đến Đội ngũ Quản trị viên ShopMart ở khung bên cạnh.
                    </p>
                    <button type="button" class="btn btn-primary text-xs mt-4 px-4 py-2" onclick="window.ShopMartAi ? window.ShopMartAi.open() : null">
                        Hỏi Chatbot AI ngay
                    </button>
                </div>
            </div>

            <!-- Right Side: Direct Contact & Admin Escalation Card -->
            <aside class="help-support-card">
                <div class="help-support-card-head">
                    <div class="icon-wrap">
                        {{-- Cute AI Robot Avatar matching concept --}}
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="12" x="3" y="8" rx="3"/>
                            <path d="M12 2v6"/>
                            <path d="M8 2h8"/>
                            <circle cx="8" cy="13" r="1.5" fill="currentColor"/>
                            <circle cx="16" cy="13" r="1.5" fill="currentColor"/>
                            <path d="M10 17h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Chưa tìm thấy giải đáp?</h3>
                        <p>Đội ngũ ShopMart luôn sẵn sàng hỗ trợ</p>
                    </div>
                </div>

                <div class="help-support-options">
                    <!-- Option 1: AI Assistant (Click to open AI Chatbot) -->
                    <button type="button" class="help-option-item" onclick="window.ShopMartAi ? window.ShopMartAi.open() : null">
                        <div class="help-option-icon">
                            <svg class="w-4 h-4 text-slate-800" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l2.4 6.6L21 11l-6.6 2.4L12 20l-2.4-6.6L3 11l6.6-2.4L12 2z"/>
                            </svg>
                        </div>
                        <div class="help-option-text">
                            <h4>Trợ lý AI ShopMart</h4>
                            <p>Phản hồi tức thì, tra cứu đơn hàng, tư vấn mã giảm giá và hướng dẫn mua sắm 24/7.</p>
                        </div>
                        <svg class="help-option-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    <!-- Option 2: Live Support Agent (Click to go to Admin Chat) -->
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.support-chats.index') }}" class="help-option-item">
                        @else
                            <a href="{{ route('support.chat') }}" class="help-option-item">
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="help-option-item">
                    @endauth
                        <div class="help-option-icon">
                            {{-- Headset / Support Agent SVG matching concept --}}
                            <svg class="w-4 h-4 text-slate-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/>
                                <path d="M16 19a2 2 0 0 1 2 2v0a2 2 0 0 1-2 2h-1"/>
                            </svg>
                        </div>
                        <div class="help-option-text">
                            <h4>Hỗ trợ từ Quản trị viên (Admin)</h4>
                            <p>Khi chatbot không giải quyết được, bạn có thể chuyển tiếp vấn đề đến nhân viên sàn can thiệp giải quyết trực tiếp.</p>
                        </div>
                        <svg class="help-option-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Primary Red CTA: Matching Concept solid red button -->
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.support-chats.index') }}" class="help-admin-cta-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Xem danh sách yêu cầu (Admin)</span>
                        </a>
                    @else
                        <a href="{{ route('support.chat') }}" class="help-admin-cta-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Nhận hỗ trợ từ Admin</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="help-admin-cta-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Nhận hỗ trợ từ Admin</span>
                    </a>
                @endauth
            </aside>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('faq-search-input');
    const categoryBtns = document.querySelectorAll('.help-category-btn');
    const faqItems = document.querySelectorAll('.help-faq-item');
    const noResults = document.getElementById('faq-no-results');
    let activeCategory = 'all';

    // Toggle FAQ item accordion
    faqItems.forEach(item => {
        const trigger = item.querySelector('.help-faq-trigger');
        trigger?.addEventListener('click', () => {
            const isOpen = item.classList.contains('is-open');
            // Close other items for neatness
            faqItems.forEach(other => {
                if (other !== item) {
                    other.classList.remove('is-open');
                    other.querySelector('.help-faq-trigger')?.setAttribute('aria-expanded', 'false');
                }
            });
            item.classList.toggle('is-open', !isOpen);
            trigger.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    // Ask chatbot AI directly from FAQ action button
    document.querySelectorAll('.help-ask-bot-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const prompt = btn.getAttribute('data-prompt');
            if (window.ShopMartAi && typeof window.ShopMartAi.ask === 'function') {
                window.ShopMartAi.ask(prompt);
            } else if (window.ShopMartAi && typeof window.ShopMartAi.open === 'function') {
                window.ShopMartAi.open();
            }
        });
    });

    // Filter by Category
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryBtns.forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            activeCategory = btn.getAttribute('data-filter') || 'all';
            filterFaqs();
        });
    });

    // Search input filtering
    searchInput?.addEventListener('input', () => {
        filterFaqs();
    });

    function filterFaqs() {
        const query = (searchInput?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        faqItems.forEach(item => {
            const itemCat = item.getAttribute('data-category');
            const q = item.getAttribute('data-question') || '';
            const a = item.getAttribute('data-answer') || '';

            const matchCat = activeCategory === 'all' || itemCat === activeCategory;
            const matchQuery = !query || q.includes(query) || a.includes(query);

            if (matchCat && matchQuery) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (noResults) {
            noResults.classList.toggle('hidden', visibleCount > 0);
        }
    }
});
</script>
@endsection
