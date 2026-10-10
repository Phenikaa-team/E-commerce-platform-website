export function initStoreChat() {
    const room = document.querySelector('[data-chat-room]');
    const form = room?.querySelector('[data-chat-form]');
    const messageList = room?.querySelector('[data-chat-messages]');

    if (!room || !form || !messageList) {
        return;
    }

    const currentUserId = Number(room.dataset.currentUserId);
    const error = form.querySelector('[data-chat-error]');
    const textarea = form.querySelector('textarea');
    const sendButton = form.querySelector('button[type="submit"]');
    let lastMessageId = Number(room.dataset.lastMessageId || 0);

    const appendMessage = (message) => {
        if (messageList.querySelector(`[data-message-id="${message.id}"]`)) {
            return;
        }

        const article = document.createElement('article');
        article.className = `store-chat-message${Number(message.sender_id) === currentUserId ? ' is-own' : ''}`;
        article.dataset.messageId = message.id;

        const body = document.createElement('p');
        body.textContent = message.body;

        const time = document.createElement('time');
        time.dateTime = message.created_at;
        time.textContent = new Intl.DateTimeFormat('vi-VN', { hour: '2-digit', minute: '2-digit' })
            .format(new Date(message.created_at));

        article.append(body, time);
        messageList.append(article);
        lastMessageId = Math.max(lastMessageId, Number(message.id));
        messageList.scrollTop = messageList.scrollHeight;
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const refreshMessages = async () => {
        const response = await fetch(`${room.dataset.messagesUrl}?after=${lastMessageId}`, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            return;
        }

        (await response.json()).forEach(appendMessage);
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const body = textarea.value.trim();

        if (!body) {
            return;
        }

        error.hidden = true;
        sendButton.disabled = true;

        try {
            const response = await fetch(room.dataset.sendUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ body }),
            });

            if (!response.ok) {
                const result = await response.json().catch(() => ({}));
                throw new Error(result.message || 'Không gửi được tin nhắn. Vui lòng thử lại.');
            }

            appendMessage(await response.json());
            textarea.value = '';
            textarea.focus();
        } catch (exception) {
            error.textContent = exception.message;
            error.hidden = false;
        } finally {
            sendButton.disabled = false;
        }
    });

    messageList.scrollTop = messageList.scrollHeight;
    window.setInterval(() => refreshMessages().catch(() => {}), 3500);
}
