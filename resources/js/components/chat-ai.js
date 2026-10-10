/**
 * ShopMart AI Chat Widget Component Logic
 */

export function initChatAi() {
    const widget = document.getElementById('shopmart-ai-widget');
    if (!widget) return;

    const toggleBtn = document.getElementById('ai-chat-toggle-btn');
    const chatWindow = document.getElementById('ai-chat-window');
    const closeBtn = document.getElementById('ai-chat-close-btn');
    const clearBtn = document.getElementById('ai-chat-clear-btn');
    const chatForm = document.getElementById('ai-chat-form');
    const chatInput = document.getElementById('ai-chat-input');
    const messagesContainer = document.getElementById('ai-chat-messages');
    const sendBtn = document.getElementById('ai-chat-send-btn');
    const suggestionChips = document.querySelectorAll('.ai-suggest-chip');

    // User context scoping
    const userId = widget.getAttribute('data-user-id') || 'guest';
    const userRole = widget.getAttribute('data-user-role') || 'customer';
    const STORAGE_KEY = `shopmart_ai_history_${userId}_${userRole}_v2`;

    // Conversation state
    let history = [];

    // Save initial welcome message from server Blade template
    const defaultWelcomeHtml = messagesContainer?.innerHTML || '';

    // Load history from localStorage
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            history = JSON.parse(saved);
            renderSavedHistory();
        }
    } catch {
        history = [];
    }

    // Toggle Open / Close
    function toggleChat(isOpen) {
        if (typeof isOpen === 'boolean') {
            if (isOpen) {
                chatWindow.classList.remove('hidden');
                widget.classList.add('is-open');
                chatInput.focus();
                scrollToBottom();
            } else {
                chatWindow.classList.add('hidden');
                widget.classList.remove('is-open');
            }
        } else {
            const isCurrentlyHidden = chatWindow.classList.contains('hidden');
            toggleChat(isCurrentlyHidden);
        }
    }

    toggleBtn?.addEventListener('click', () => toggleChat());
    closeBtn?.addEventListener('click', () => toggleChat(false));

    // Clear history
    clearBtn?.addEventListener('click', () => {
        if (confirm('Bạn có muốn xóa toàn bộ đoạn trò chuyện này không?')) {
            history = [];
            localStorage.removeItem(STORAGE_KEY);
            // Reset to default greeting of current role
            if (messagesContainer) {
                messagesContainer.innerHTML = defaultWelcomeHtml;
            }
        }
    });

    // Handle suggestion chips
    suggestionChips.forEach((chip) => {
        chip.addEventListener('click', () => {
            const prompt = chip.getAttribute('data-prompt');
            if (prompt) {
                sendMessage(prompt);
            }
        });
    });

    // Handle Form Submit
    chatForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        const text = chatInput.value.trim();
        if (!text) return;
        sendMessage(text);
    });

    function getCurrentTimeStr() {
        const d = new Date();
        const h = String(d.getHours()).padStart(2, '0');
        const m = String(d.getMinutes()).padStart(2, '0');
        return `${h}:${m}`;
    }

    // Send Message
    async function sendMessage(text) {
        const timeStr = getCurrentTimeStr();
        // Append user bubble
        appendMessage('user', text, timeStr);
        chatInput.value = '';
        history.push({ role: 'user', content: text, time: timeStr });
        saveHistory();

        // Show typing indicator
        const typingEl = appendTypingIndicator();
        scrollToBottom();

        // Disable input while generating
        if (sendBtn) sendBtn.disabled = true;
        if (chatInput) chatInput.disabled = true;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const res = await fetch('/api/chat-ai', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: text,
                    history: history.slice(-6).map(h => ({
                        role: h.role,
                        content: (h.content || '').slice(0, 2000), // Cắt ngắn để không bao giờ vượt giới hạn
                    })),
                }),
            });

            const data = await res.json();
            typingEl?.remove();

            const botTime = getCurrentTimeStr();
            if (res.ok && data.success) {
                appendMessage('assistant', data.reply, botTime);
                history.push({ role: 'assistant', content: data.reply, time: botTime });
                saveHistory();
            } else {
                let errMsg = data.message;
                if (!errMsg && data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    errMsg = data.errors[firstKey]?.[0] || 'Dữ liệu không hợp lệ.';
                }
                appendMessage('assistant', errMsg || 'Xin lỗi, hệ thống AI tạm thời gặp sự cố. Bạn thử lại nhé!', botTime);
            }
        } catch {
            typingEl?.remove();
            const botTime = getCurrentTimeStr();
            appendMessage('assistant', 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng của bạn.', botTime);
        } finally {
            if (sendBtn) sendBtn.disabled = false;
            if (chatInput) {
                chatInput.disabled = false;
                chatInput.focus();
            }
            scrollToBottom();
        }
    }

    // Append Message to UI
    function appendMessage(role, rawContent, timeStr) {
        const time = timeStr || getCurrentTimeStr();
        const row = document.createElement('div');
        row.className = `ai-msg-row ${role === 'user' ? 'ai-msg-user' : 'ai-msg-bot'}`;

        const avatar = role === 'user' ? '' : `
            <div class="ai-msg-avatar">
                <svg class="ai-avatar-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l2.4 6.6L21 11l-6.6 2.4L12 20l-2.4-6.6L3 11l6.6-2.4L12 2z"/>
                </svg>
            </div>
        `;

        const checkMark = role === 'user' ? `
            <svg class="ai-read-check" viewBox="0 0 16 16" fill="currentColor">
                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                <path d="M16 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-1-1a.5.5 0 1 1 .708-.708l.646.647 6.646-6.647a.5.5 0 0 1 .708 0z"/>
            </svg>
        ` : '';

        const formatted = formatMarkdown(rawContent);

        row.innerHTML = `
            ${avatar}
            <div class="ai-msg-bubble">
                <div class="ai-msg-text">${formatted}</div>
                <div class="ai-msg-meta">
                    <span class="ai-msg-time">${time}</span>
                    ${checkMark}
                </div>
            </div>
        `;

        messagesContainer.appendChild(row);
        scrollToBottom();
    }

    // Typing indicator element
    function appendTypingIndicator() {
        const row = document.createElement('div');
        row.className = 'ai-msg-row ai-msg-bot ai-typing-row';
        row.innerHTML = `
            <div class="ai-msg-avatar">
                <svg class="ai-avatar-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l2.4 6.6L21 11l-6.6 2.4L12 20l-2.4-6.6L3 11l6.6-2.4L12 2z"/>
                </svg>
            </div>
            <div class="ai-msg-bubble">
                <div class="ai-typing-indicator">
                    <span class="ai-typing-dot"></span>
                    <span class="ai-typing-dot"></span>
                    <span class="ai-typing-dot"></span>
                </div>
            </div>
        `;
        messagesContainer.appendChild(row);
        return row;
    }

    // Enhanced markdown parser for tables, headers, lists, blockquotes, and links
    function formatMarkdown(content) {
        if (!content) return '';

        // Tách theo dòng để xử lý bảng và blockquote một cách an toàn
        const rawLines = content.split('\n');
        const formattedBlocks = [];
        let inTable = false;
        let tableRows = [];

        function renderTable(rows) {
            if (rows.length < 2) return rows.join('<br>');
            const headerRow = rows[0];
            // rows[1] thường là |---|---| ngăn cách
            const bodyRows = rows.slice(2);

            const parseCells = (rowStr) => {
                const trimmed = rowStr.trim().replace(/^\|/, '').replace(/\|$/, '');
                return trimmed.split('|').map(c => c.trim());
            };

            const headerCells = parseCells(headerRow);
            let thHtml = headerCells.map(c => `<th>${formatInline(c)}</th>`).join('');

            let trHtml = '';
            bodyRows.forEach(row => {
                if (!row.trim() || !row.includes('|')) return;
                const cells = parseCells(row);
                const tds = cells.map((c, idx) => {
                    // Nếu là cột số lượng / giá trị, căn giữa/phải hoặc làm nổi bật
                    const isValCol = idx === cells.length - 1;
                    return `<td class="${isValCol ? 'ai-cell-val' : ''}">${formatInline(c)}</td>`;
                }).join('');
                trHtml += `<tr>${tds}</tr>`;
            });

            return `<div class="ai-table-wrap"><table class="ai-report-table"><thead><tr>${thHtml}</tr></thead><tbody>${trHtml}</tbody></table></div>`;
        }

        function formatInline(str) {
            let s = escapeHtml(str);
            // Links [text](url)
            s = s.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_self">$1</a>');
            // Bold **text**
            s = s.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
            // Italic *text*
            s = s.replace(/(^|[^*])\*([^*]+)\*([^*]|$)/g, '$1<em>$2</em>$3');
            // Code `text`
            s = s.replace(/`([^`]+)`/g, '<code class="ai-inline-code">$1</code>');
            return s;
        }

        for (let i = 0; i < rawLines.length; i++) {
            const line = rawLines[i].trim();

            // Nhận diện dòng bảng markdown (|...|)
            if (line.startsWith('|') && line.endsWith('|')) {
                inTable = true;
                tableRows.push(line);
                continue;
            } else if (inTable) {
                // Kết thúc bảng
                formattedBlocks.push(renderTable(tableRows));
                tableRows = [];
                inTable = false;
            }

            // Headers: ### Title hoặc ## Title
            if (line.startsWith('### ')) {
                formattedBlocks.push(`<h4 class="ai-msg-h4">${formatInline(line.substring(4))}</h4>`);
            } else if (line.startsWith('## ')) {
                formattedBlocks.push(`<h3 class="ai-msg-h3">${formatInline(line.substring(3))}</h3>`);
            } else if (line.startsWith('> ')) {
                // Blockquote
                formattedBlocks.push(`<div class="ai-blockquote">${formatInline(line.substring(2))}</div>`);
            } else if (line.startsWith('- ') || line.startsWith('* ')) {
                // List item
                formattedBlocks.push(`<div class="ai-list-item"><span class="ai-bullet">•</span><span>${formatInline(line.substring(2))}</span></div>`);
            } else if (line === '') {
                formattedBlocks.push('<div class="ai-spacer"></div>');
            } else {
                formattedBlocks.push(`<div class="ai-text-line">${formatInline(line)}</div>`);
            }
        }

        if (inTable && tableRows.length > 0) {
            formattedBlocks.push(renderTable(tableRows));
        }

        return formattedBlocks.join('');
    }

    function escapeHtml(string) {
        const div = document.createElement('div');
        div.textContent = string;
        return div.innerHTML;
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function saveHistory() {
        try {
            // Keep maximum 20 latest messages
            if (history.length > 20) history = history.slice(-20);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
        } catch {
            // ignore storage full
        }
    }

    function renderSavedHistory() {
        if (!history || history.length === 0) return;
        history.forEach((item) => {
            appendMessage(item.role, item.content, item.time);
        });
    }
}
