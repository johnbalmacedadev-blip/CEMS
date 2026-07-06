@php
    $chatUser = auth()->user();
    $chatInitials = \App\Models\ChatMessage::initials($chatUser->name);
@endphp

<div id="liveChatWidget" class="live-chat-widget is-minimized" aria-live="polite">
    {{-- Minimized pill --}}
    <button type="button" class="live-chat-pill" id="liveChatPill" aria-label="Open team chat">
        <span class="live-chat-pill-avatar">{{ $chatInitials }}</span>
        <span class="live-chat-pill-text">Team Chat</span>
        <span class="live-chat-badge" id="liveChatBadge" hidden>0</span>
        <i class="fas fa-chevron-up live-chat-pill-chevron"></i>
    </button>

    {{-- Expanded panel --}}
    <div class="live-chat-panel" id="liveChatPanel" role="dialog" aria-label="Team chat">
        <div class="live-chat-header">
            <div class="live-chat-header-main">
                <span class="live-chat-header-avatar">{{ $chatInitials }}</span>
                <div>
                    <div class="live-chat-header-title">Team Chat</div>
                    <div class="live-chat-header-sub" id="liveChatOnlineLabel">Live · 1 online</div>
                </div>
            </div>
            <button type="button" class="live-chat-toggle-btn" id="liveChatMinimize" aria-label="Minimize chat">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>

        <div class="live-chat-online-bar" id="liveChatOnlineBar"></div>

        <div class="live-chat-messages" id="liveChatMessages">
            <div class="live-chat-empty" id="liveChatEmpty">No messages yet. Say hello to the team!</div>
        </div>

        <div class="live-chat-compose">
            <div class="live-chat-attach-preview" id="liveChatAttachPreview" hidden></div>
            <div class="live-chat-input-row">
                <input type="file" id="liveChatFileInput" class="d-none" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">
                <button type="button" class="live-chat-icon-btn" id="liveChatAttachBtn" title="Attach file">
                    <i class="fas fa-paperclip"></i>
                </button>
                <button type="button" class="live-chat-icon-btn" id="liveChatLinkBtn" title="Attach link">
                    <i class="fas fa-link"></i>
                </button>
                <input type="text" class="live-chat-text-input" id="liveChatInput" placeholder="Type a message..." maxlength="5000" autocomplete="off">
                <button type="button" class="live-chat-send-btn" id="liveChatSendBtn" title="Send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.live-chat-widget {
    position: fixed;
    right: 20px;
    bottom: 72px;
    z-index: 10050;
    font-family: inherit;
}

.live-chat-pill {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.45rem 0.85rem 0.45rem 0.45rem;
    border: none;
    border-radius: 999px;
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
    color: #fff;
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
    cursor: pointer;
    min-width: 200px;
    position: relative;
}

.live-chat-pill-avatar,
.live-chat-header-avatar,
.live-chat-msg-avatar,
.live-chat-online-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.85);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}

.live-chat-pill-text {
    font-weight: 700;
    font-size: 0.95rem;
    flex: 1;
    text-align: left;
}

.live-chat-pill-chevron {
    font-size: 0.85rem;
    opacity: 0.9;
}

.live-chat-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 999px;
    background: #ef4444;
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
}

.live-chat-panel {
    display: none;
    width: min(380px, calc(100vw - 24px));
    height: min(520px, calc(100vh - 120px));
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 16px 48px rgba(15, 23, 42, 0.22);
    overflow: hidden;
    flex-direction: column;
}

.live-chat-widget:not(.is-minimized) .live-chat-pill { display: none; }
.live-chat-widget:not(.is-minimized) .live-chat-panel { display: flex; }

.live-chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1rem;
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
    color: #fff;
}

.live-chat-header-main {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}

.live-chat-header-title {
    font-weight: 700;
    font-size: 0.95rem;
    line-height: 1.2;
}

.live-chat-header-sub {
    font-size: 0.72rem;
    opacity: 0.9;
}

.live-chat-toggle-btn {
    border: none;
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
}

.live-chat-online-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    padding: 0.5rem 0.75rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    min-height: 44px;
    align-items: center;
}

.live-chat-online-avatar {
    width: 28px;
    height: 28px;
    font-size: 0.62rem;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    color: #fff;
    border-color: #fff;
}

.live-chat-online-label {
    font-size: 0.72rem;
    color: #64748b;
    margin-right: 0.25rem;
}

.live-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 0.75rem;
    background: #f1f5f9;
}

.live-chat-empty {
    text-align: center;
    color: #64748b;
    font-size: 0.85rem;
    padding: 2rem 1rem;
}

.live-chat-msg {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    max-width: 100%;
}

.live-chat-msg.is-own {
    flex-direction: row-reverse;
}

.live-chat-msg-avatar {
    width: 30px;
    height: 30px;
    font-size: 0.65rem;
    background: #64748b;
    color: #fff;
    border: none;
}

.live-chat-msg.is-own .live-chat-msg-avatar {
    background: linear-gradient(135deg, #2563eb, #7c3aed);
}

.live-chat-msg-bubble {
    max-width: 78%;
    background: #fff;
    border-radius: 12px;
    padding: 0.5rem 0.65rem;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
}

.live-chat-msg.is-own .live-chat-msg-bubble {
    background: #dbeafe;
}

.live-chat-msg-meta {
    font-size: 0.68rem;
    color: #64748b;
    margin-bottom: 0.15rem;
}

.live-chat-msg.is-own .live-chat-msg-meta {
    text-align: right;
}

.live-chat-msg-body {
    font-size: 0.875rem;
    color: #0f172a;
    white-space: pre-wrap;
    word-break: break-word;
}

.live-chat-msg-link {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.82rem;
    color: #2563eb;
    text-decoration: none;
    word-break: break-all;
}

.live-chat-msg-link:hover { text-decoration: underline; }

.live-chat-msg-attachment {
    margin-top: 0.35rem;
}

.live-chat-msg-attachment img {
    max-width: 100%;
    border-radius: 8px;
    max-height: 160px;
    object-fit: cover;
}

.live-chat-msg-file {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.82rem;
    color: #334155;
    text-decoration: none;
    padding: 0.35rem 0.5rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.live-chat-compose {
    border-top: 1px solid #e2e8f0;
    background: #fff;
    padding: 0.65rem;
}

.live-chat-attach-preview {
    font-size: 0.78rem;
    color: #475569;
    padding: 0.35rem 0.5rem;
    margin-bottom: 0.35rem;
    background: #f8fafc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.live-chat-input-row {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.live-chat-icon-btn,
.live-chat-send-btn {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 10px;
    background: #f1f5f9;
    color: #475569;
    cursor: pointer;
    flex-shrink: 0;
}

.live-chat-send-btn {
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    color: #fff;
}

.live-chat-text-input {
    flex: 1;
    min-width: 0;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.5rem 0.65rem;
    font-size: 0.875rem;
}

.live-chat-text-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

@media (max-width: 576px) {
    .live-chat-widget {
        right: 12px;
        bottom: 68px;
    }
}
</style>

<script>
(function () {
    const widget = document.getElementById('liveChatWidget');
    const pill = document.getElementById('liveChatPill');
    const panel = document.getElementById('liveChatPanel');
    const minimizeBtn = document.getElementById('liveChatMinimize');
    const messagesEl = document.getElementById('liveChatMessages');
    const emptyEl = document.getElementById('liveChatEmpty');
    const inputEl = document.getElementById('liveChatInput');
    const sendBtn = document.getElementById('liveChatSendBtn');
    const fileInput = document.getElementById('liveChatFileInput');
    const attachBtn = document.getElementById('liveChatAttachBtn');
    const linkBtn = document.getElementById('liveChatLinkBtn');
    const attachPreview = document.getElementById('liveChatAttachPreview');
    const badge = document.getElementById('liveChatBadge');
    const onlineBar = document.getElementById('liveChatOnlineBar');
    const onlineLabel = document.getElementById('liveChatOnlineLabel');

    const syncUrl = @json(route('chat.sync'));
    const storeUrl = @json(route('chat.store'));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let lastMessageId = 0;
    let currentUserId = @json($chatUser->id);
    let isOpen = false;
    let pendingFile = null;
    let pendingLink = '';
    let pollTimer = null;
    let renderedIds = new Set();
    let unreadCount = 0;
    let initialized = false;

    const storageKey = 'liveChatLastReadId';

    function getLastReadId() {
        return parseInt(localStorage.getItem(storageKey) || '0', 10);
    }

    function setLastReadId(id) {
        localStorage.setItem(storageKey, String(id));
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

    function openChat() {
        isOpen = true;
        widget.classList.remove('is-minimized');
        unreadCount = 0;
        badge.hidden = true;
        badge.textContent = '0';
        if (lastMessageId > 0) {
            setLastReadId(lastMessageId);
        }
        inputEl.focus();
        scrollToBottom();
    }

    function closeChat() {
        isOpen = false;
        widget.classList.add('is-minimized');
        if (lastMessageId > 0) {
            setLastReadId(lastMessageId);
        }
    }

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function renderOnline(users) {
        const count = users.length;
        onlineLabel.textContent = 'Live · ' + count + ' online';
        onlineBar.innerHTML = '<span class="live-chat-online-label">Online:</span>';
        users.forEach(u => {
            const span = document.createElement('span');
            span.className = 'live-chat-online-avatar';
            span.title = u.name;
            span.textContent = u.initials;
            onlineBar.appendChild(span);
        });
    }

    function renderMessage(msg) {
        if (renderedIds.has(msg.id)) return;
        renderedIds.add(msg.id);

        if (emptyEl) emptyEl.style.display = 'none';

        const own = msg.user_id === currentUserId;
        const wrap = document.createElement('div');
        wrap.className = 'live-chat-msg' + (own ? ' is-own' : '');
        wrap.dataset.id = msg.id;

        let inner = '';
        inner += '<div class="live-chat-msg-avatar">' + escHtml(msg.user_initials) + '</div>';
        inner += '<div class="live-chat-msg-bubble">';
        inner += '<div class="live-chat-msg-meta">' + escHtml(msg.user_name) + ' · ' + escHtml(msg.created_label || '') + '</div>';

        if (msg.body) {
            inner += '<div class="live-chat-msg-body">' + escHtml(msg.body) + '</div>';
        }
        if (msg.link_url) {
            inner += '<a class="live-chat-msg-link" href="' + escHtml(msg.link_url) + '" target="_blank" rel="noopener noreferrer"><i class="fas fa-external-link-alt"></i> ' + escHtml(msg.link_url) + '</a>';
        }
        if (msg.attachment_url) {
            inner += '<div class="live-chat-msg-attachment">';
            if (msg.is_image) {
                inner += '<a href="' + escHtml(msg.attachment_url) + '" target="_blank" rel="noopener"><img src="' + escHtml(msg.attachment_url) + '" alt=""></a>';
            } else {
                inner += '<a class="live-chat-msg-file" href="' + escHtml(msg.attachment_url) + '" target="_blank" rel="noopener"><i class="fas fa-file"></i> ' + escHtml(msg.attachment_name || 'Download file') + '</a>';
            }
            inner += '</div>';
        }
        inner += '</div>';

        wrap.innerHTML = inner;
        messagesEl.appendChild(wrap);
    }

    function showUnreadBadge() {
        if (unreadCount <= 0) {
            badge.hidden = true;
            return;
        }
        badge.hidden = false;
        badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
    }

    function updateUnread() {
        showUnreadBadge();
    }

    async function syncChat(initial) {
        try {
            const url = syncUrl + (lastMessageId > 0 && !initial ? '?after=' + lastMessageId : '');
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.current_user) {
                currentUserId = data.current_user.id;
            }

            if (initial) {
                messagesEl.querySelectorAll('.live-chat-msg').forEach(el => el.remove());
                renderedIds.clear();
                if (emptyEl) emptyEl.style.display = '';
            }

            let hadNew = false;
            (data.messages || []).forEach(msg => {
                if (msg.id > lastMessageId) lastMessageId = msg.id;
                const isNew = !renderedIds.has(msg.id);
                renderMessage(msg);
                if (isNew) hadNew = true;
                if (isNew && !isOpen && msg.user_id !== currentUserId) {
                    unreadCount++;
                }
            });

            if (data.latest_id && data.latest_id > lastMessageId) {
                lastMessageId = data.latest_id;
            }

            renderOnline(data.online_users || []);

            if (!initialized) {
                initialized = true;
                if (!isOpen && lastMessageId > 0) {
                    setLastReadId(lastMessageId);
                }
            }

            if (hadNew && isOpen) {
                setLastReadId(lastMessageId);
                scrollToBottom();
            } else if (hadNew && !isOpen) {
                updateUnread();
            }
        } catch (e) {
            /* ignore network errors during poll */
        }
    }

    function clearPending() {
        pendingFile = null;
        pendingLink = '';
        fileInput.value = '';
        attachPreview.hidden = true;
        attachPreview.innerHTML = '';
    }

    function showPendingPreview() {
        if (pendingFile) {
            attachPreview.hidden = false;
            attachPreview.innerHTML = '<span><i class="fas fa-paperclip me-1"></i>' + escHtml(pendingFile.name) + '</span><button type="button" class="btn btn-sm btn-link text-danger p-0" id="liveChatClearAttach">Remove</button>';
            document.getElementById('liveChatClearAttach')?.addEventListener('click', clearPending);
        } else if (pendingLink) {
            attachPreview.hidden = false;
            attachPreview.innerHTML = '<span><i class="fas fa-link me-1"></i>' + escHtml(pendingLink) + '</span><button type="button" class="btn btn-sm btn-link text-danger p-0" id="liveChatClearAttach">Remove</button>';
            document.getElementById('liveChatClearAttach')?.addEventListener('click', clearPending);
        }
    }

    async function sendMessage() {
        const body = inputEl.value.trim();
        if (!body && !pendingFile && !pendingLink) return;

        sendBtn.disabled = true;
        const form = new FormData();
        if (body) form.append('body', body);
        if (pendingLink) form.append('link_url', pendingLink);
        if (pendingFile) form.append('attachment', pendingFile);

        try {
            const res = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: form
            });
            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Could not send message.');
                return;
            }
            inputEl.value = '';
            clearPending();
            if (data.message) {
                if (data.message.id > lastMessageId) lastMessageId = data.message.id;
                renderMessage(data.message);
                setLastReadId(lastMessageId);
                scrollToBottom();
                if (emptyEl) emptyEl.style.display = 'none';
            }
        } catch (e) {
            alert('Could not send message. Please try again.');
        } finally {
            sendBtn.disabled = false;
        }
    }

    pill.addEventListener('click', openChat);
    minimizeBtn.addEventListener('click', closeChat);

    sendBtn.addEventListener('click', sendMessage);
    inputEl.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    attachBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        pendingLink = '';
        pendingFile = fileInput.files?.[0] || null;
        showPendingPreview();
    });

    linkBtn.addEventListener('click', () => {
        const url = prompt('Enter link URL (https://...)');
        if (!url) return;
        try {
            new URL(url);
        } catch (e) {
            alert('Please enter a valid URL including https://');
            return;
        }
        pendingFile = null;
        fileInput.value = '';
        pendingLink = url.trim();
        showPendingPreview();
    });

    syncChat(true);
    pollTimer = setInterval(() => syncChat(false), 3000);

    window.addEventListener('beforeunload', () => {
        if (pollTimer) clearInterval(pollTimer);
    });
})();
</script>
