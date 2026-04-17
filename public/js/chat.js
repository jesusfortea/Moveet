'use strict';

window.onload = () => {
    const chatConfig = window.chatConfig || {};
    const messagesContainer = document.getElementById('chat-messages');
    const composer = document.getElementById('chat-composer');
    if (!messagesContainer || !composer || !chatConfig.contactId || !chatConfig.messagesUrl || !chatConfig.sendUrl) {
        return;
    }

    const input = composer.querySelector('input[name="contenido"]');
    const sendButton = composer.querySelector('.chat-send-btn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let lastMessageId = Number(messagesContainer.dataset.lastMessageId || chatConfig.lastMessageId || 0);
    let isFetching = false;
    const SCROLL_THRESHOLD = 28;

    const isNearBottom = () => {
        const distanceToBottom = messagesContainer.scrollHeight - (messagesContainer.scrollTop + messagesContainer.clientHeight);
        return distanceToBottom <= SCROLL_THRESHOLD;
    };

    const escapeHtml = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const renderMessages = (messages, { stickToBottom = false } = {}) => {
        const previousScrollTop = messagesContainer.scrollTop;

        if (!messages.length) {
            messagesContainer.innerHTML = '<div class="chat-empty"><p>Selecciona un contacto para empezar a chatear.</p></div>';
            return;
        }

        messagesContainer.innerHTML = messages.map((message) => {
            const bubbleClass = message.mio ? 'message-bubble message-bubble--out' : 'message-bubble message-bubble--in';
            return `
                <article class="${bubbleClass}" data-message-id="${message.id}">
                    <p>${escapeHtml(message.contenido)}</p>
                    <span>${message.hora || ''}</span>
                </article>
            `;
        }).join('');

        if (stickToBottom) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            return;
        }

        messagesContainer.scrollTop = previousScrollTop;
    };

    const refreshMessages = async ({ forceScroll = false } = {}) => {
        if (isFetching) {
            return;
        }

        isFetching = true;

        try {
            const response = await fetch(chatConfig.messagesUrl, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const shouldStickToBottom = forceScroll || isNearBottom();
            const payload = await response.json();
            const messages = payload.messages || [];
            const latestId = Number(payload.last_message_id || 0);
            const shouldRender = forceScroll || latestId !== lastMessageId || messagesContainer.children.length !== messages.length;

            if (shouldRender) {
                renderMessages(messages, { stickToBottom: shouldStickToBottom });
                lastMessageId = latestId;
            }
        } catch (error) {
            console.error('No se pudieron cargar los mensajes', error);
        } finally {
            isFetching = false;
        }
    };

    composer.onsubmit = async (event) => {
        event.preventDefault();

        const contenido = input.value.trim();
        if (!contenido) {
            input.focus();
            return;
        }

        sendButton.disabled = true;

        try {
            const response = await fetch(chatConfig.sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ contenido }),
            });

            if (!response.ok) {
                const errorPayload = await response.json().catch(() => ({}));
                throw new Error(errorPayload.message || 'No se pudo enviar el mensaje');
            }

            input.value = '';
            await refreshMessages({ forceScroll: true });
            input.focus();
        } catch (error) {
            console.error(error);
        } finally {
            sendButton.disabled = false;
        }
    });

    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    refreshMessages({ forceScroll: true });
    window.setInterval(refreshMessages, 1500);
};
