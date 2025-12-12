/**
 * 🚀 ChannelMessagingApp - Chat de Canal en Temps Réel
 * Interface moderne type WhatsApp/Slack pour les discussions de groupe
 * @version 2.0.0
 */

class ChannelMessagingApp {
    constructor(options) {
        this.channelId = options.channelId;
        this.channelName = options.channelName;
        this.channelType = options.channelType;
        this.userId = options.userId;
        this.userName = options.userName;
        this.authToken = options.authToken;
        this.isAdmin = options.isAdmin || false;

        // État de l'application
        this.messages = [];
        this.members = [];
        this.onlineMembers = new Set();
        this.typingUsers = new Map();
        this.replyTo = null;
        this.selectedFiles = [];
        this.isLoading = false;
        this.hasMoreMessages = true;
        this.currentPage = 1;
        this.perPage = 50;

        // E2EE (AES-GCM) par canal
        this.cryptoReady = typeof window.crypto !== 'undefined' && !!window.crypto.subtle;
        this.channelKey = null; // CryptoKey
        this.channelKeyRaw = null; // Uint8Array
        this.decryptionWarned = false;

        // Voice recorder
        this.voiceRecorder = null;
        this.isRecording = false;
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.recordingStartTime = null;
        this.recordingTimer = null;

        // Emoji picker state
        this.emojiPickerVisible = false;
        this.reactionPickerVisible = false;
        this.reactionTargetMessageId = null;

        // WebSocket
        this.echoChannel = null;
        this.notificationSoundUrl = options.notificationSoundUrl || null;
        this.notificationAudio = null;

        console.log('🚀 ChannelMessagingApp initialized for channel:', this.channelId);

        // Auto-init
        this.init().catch(err => console.error('❌ Init error:', err));
    }

    /**
     * Initialisation
     */
    async init() {
        this.bindElements();
        await this.setupEncryption();
        this.setupEventListeners();
        await this.loadMessages();
        await this.loadMembers();
        this.setupWebSocket();
        this.setupVoiceRecorder();
        this.scrollToBottom();
        console.log('✅ ChannelMessagingApp ready');
    }

    /**
     * === E2EE: Setup channel encryption (AES-GCM) ===
     */
    async setupEncryption(providedB64 = null) {
        if (!this.cryptoReady) {
            console.warn('⚠️ WebCrypto non disponible, chiffrement désactivé.');
            return false;
        }

        const storageKey = this.getEncryptionStorageKey();

        try {
            let b64 = providedB64?.trim() || null;

            if (!b64) {
                const serverKey = await this.fetchChannelKeyFromServer();
                if (serverKey) {
                    b64 = serverKey.trim();
                    localStorage.setItem(storageKey, b64);
                }
            } else {
                localStorage.setItem(storageKey, b64);
            }

            if (!b64) {
                const stored = localStorage.getItem(storageKey);
                if (stored) {
                    b64 = stored.trim();
                }
            }

            if (!b64) {
                const rawGenerated = new Uint8Array(32);
                window.crypto.getRandomValues(rawGenerated);
                b64 = this.base64Encode(rawGenerated);
                localStorage.setItem(storageKey, b64);
                await this.saveChannelKeyToServer(b64);
            }

            const raw = this.base64Decode(b64);
            if (!raw || raw.length !== 32) {
                if (providedB64) {
                    throw new Error('Clé de canal invalide (32 octets requis).');
                }
                console.warn('⚠️ Clé de canal invalide reçue. Suppression locale…');
                localStorage.removeItem(storageKey);
                return this.setupEncryption();
            }

            this.channelKeyRaw = raw;
            this.channelKey = await window.crypto.subtle.importKey(
                'raw', raw, { name: 'AES-GCM' }, false, ['encrypt', 'decrypt']
            );
            console.log('🔐 Clé de canal prête (E2EE activé)');
            this.updateKeyPreview();
            return true;
        } catch (e) {
            console.error('❌ Échec setup E2EE:', e);
            if (providedB64) {
                throw e;
            }
            return false;
        }
    }

    base64Encode(bytes) {
        let binary = '';
        bytes.forEach(b => binary += String.fromCharCode(b));
        return btoa(binary);
    }

    base64Decode(b64) {
        try {
            const binary = atob(b64);
            const bytes = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
            return bytes;
        } catch (e) {
            console.warn('⚠️ Base64 invalide:', e);
            return null;
        }
    }

    async encryptText(plainText) {
        if (!this.cryptoReady || !this.channelKey) return { ciphertext: plainText, iv: null, encrypted: false };
        const enc = new TextEncoder();
        const data = enc.encode(plainText);
        const iv = new Uint8Array(12);
        window.crypto.getRandomValues(iv);
        const ctBuf = await window.crypto.subtle.encrypt({ name: 'AES-GCM', iv }, this.channelKey, data);
        const ctBytes = new Uint8Array(ctBuf);
        return {
            ciphertext: this.base64Encode(ctBytes),
            iv: this.base64Encode(iv),
            encrypted: true
        };
    }

    async decryptText(ciphertextB64, ivB64) {
        if (!this.cryptoReady || !this.channelKey || !ivB64) return ciphertextB64;
        try {
            const ct = this.base64Decode(ciphertextB64);
            const iv = this.base64Decode(ivB64);
            if (!ct || !iv) throw new Error('Invalid ciphertext or IV');
            const ptBuf = await window.crypto.subtle.decrypt({ name: 'AES-GCM', iv }, this.channelKey, ct);
            const dec = new TextDecoder();
            return dec.decode(ptBuf);
        } catch (e) {
            console.warn('🔓 Échec déchiffrement, affichage brut:', e);
            this.handleDecryptionFailure(e);
            return '🔐 Message chiffré (clé manquante ou invalide)';
        }
    }

    /**
     * Lier les éléments DOM
     */
    bindElements() {
        // Éléments principaux - IDs correspondant à channels/show.blade.php
        this.messagesContainer = document.getElementById('messagesContainer');
        this.messagesList = document.getElementById('messagesList');
        this.messageInput = document.getElementById('messageTextarea');
        this.sendButton = document.getElementById('sendMessageBtn');
        this.attachButton = document.getElementById('attachFileBtn');
        this.emojiButton = document.getElementById('emojiPickerBtn');
        this.voiceButton = document.getElementById('voiceRecordBtn');
        this.typingIndicator = document.getElementById('typingIndicator');
        this.replyPreview = document.getElementById('replyingTo');
        this.replyPreviewUser = document.getElementById('replyToUser');
        this.replyPreviewText = document.getElementById('replyToContent');
        this.replyPreviewClose = document.getElementById('cancelReply');
        this.pinnedMessagesBar = document.getElementById('pinnedMessagesBar');
        this.emojiPicker = document.getElementById('emojiPicker');
        this.fileInput = document.getElementById('fileInput');
        this.loadingMessages = document.getElementById('loadingMessages');
        this.onlineCountEl = document.getElementById('onlineCount');
        this.exportKeyBtn = document.getElementById('exportKeyBtn');
        this.importKeyBtn = document.getElementById('importKeyBtn');
        this.keyPreviewEl = document.getElementById('channelKeyPreview');

        console.log('📌 Elements bound:', {
            messagesContainer: !!this.messagesContainer,
            messageInput: !!this.messageInput,
            sendButton: !!this.sendButton
        });
    }

    /**
     * Configurer les écouteurs d'événements
     */
    setupEventListeners() {
        // Envoi de message
        if (this.sendButton) {
            this.sendButton.addEventListener('click', () => this.sendMessage());
        }

        // Entrée clavier
        if (this.messageInput) {
            this.messageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            this.messageInput.addEventListener('input', () => {
                this.autoExpandTextarea();
                this.updateSendButtonState();
                this.sendTypingIndicator();
            });
        }

        if (this.replyPreviewClose) {
            this.replyPreviewClose.addEventListener('click', () => this.cancelReply());
        }

        // Pièces jointes
        if (this.attachButton) {
            this.attachButton.addEventListener('click', () => {
                if (this.fileInput) this.fileInput.click();
            });
        }

        if (this.fileInput) {
            this.fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }

        if (this.exportKeyBtn) {
            this.exportKeyBtn.addEventListener('click', () => this.exportChannelKey());
        }

        if (this.importKeyBtn) {
            this.importKeyBtn.addEventListener('click', () => this.importChannelKey());
        }

        // Emoji picker
        if (this.emojiButton) {
            this.emojiButton.addEventListener('click', () => this.toggleEmojiPicker());
        }

        // Voice recording
        if (this.voiceButton) {
            this.voiceButton.addEventListener('click', () => this.toggleVoiceRecording());
        }

        // Scroll infini pour charger plus de messages
        if (this.messagesContainer) {
            this.messagesContainer.addEventListener('scroll', () => {
                if (this.messagesContainer.scrollTop < 100 && !this.isLoading && this.hasMoreMessages) {
                    this.loadMoreMessages();
                }
            });
        }

        // Fermer emoji picker quand on clique ailleurs
        document.addEventListener('click', (e) => {
            if (this.emojiPicker && !this.emojiPicker.contains(e.target) && e.target !== this.emojiButton) {
                this.closeEmojiPicker();
            }
        });

        // Context menu pour les messages
        if (this.messagesList) {
            this.messagesList.addEventListener('contextmenu', (e) => this.handleContextMenu(e));
        }
    }

    /**
     * Charger les messages du canal
     */
    async loadMessages() {
        if (this.isLoading) return;
        this.isLoading = true;

        console.log('📥 Loading messages for channel:', this.channelId);

        try {
            this.showLoadingState();

            const url = `/api/channels/${this.channelId}/messages?page=${this.currentPage}&per_page=${this.perPage}`;
            console.log('📡 Fetching:', url);

            const response = await fetch(url, {
                headers: this.getHeaders()
            });

            console.log('📬 Response status:', response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ API Error:', errorText);
                throw new Error('Erreur de chargement des messages');
            }

            const data = await response.json();
            console.log('📦 Data received:', data);

            this.messages = (data.messages || []).reverse().map(m => this.normalizeMessage(m));
            this.hasMoreMessages = data.pagination?.current_page < data.pagination?.last_page;

            console.log('📝 Messages loaded:', this.messages.length);

            this.renderMessages();
            this.scrollToBottom();

            // Cacher le loading
            if (this.loadingMessages) {
                this.loadingMessages.style.display = 'none';
            }
        } catch (error) {
            console.error('❌ Erreur chargement messages:', error);
            this.showError('Erreur de chargement des messages');
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Charger plus de messages (pagination)
     */
    async loadMoreMessages() {
        if (this.isLoading || !this.hasMoreMessages) return;
        this.isLoading = true;
        this.currentPage++;

        try {
            const scrollHeight = this.messagesContainer.scrollHeight;

            const response = await fetch(`/api/channels/${this.channelId}/messages?page=${this.currentPage}&per_page=${this.perPage}`, {
                headers: this.getHeaders()
            });

            if (!response.ok) throw new Error('Erreur de chargement');

            const data = await response.json();
            const olderMessages = (data.messages || []).reverse().map(m => this.normalizeMessage(m));
            this.messages = [...olderMessages, ...this.messages];
            this.hasMoreMessages = data.pagination?.current_page < data.pagination?.last_page;

            this.renderMessages();

            // Maintenir la position de scroll
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight - scrollHeight;
        } catch (error) {
            console.error('❌ Erreur chargement messages:', error);
            this.currentPage--;
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Charger les membres du canal
     */
    async loadMembers() {
        try {
            const response = await fetch(`/api/channels/${this.channelId}`, {
                headers: this.getHeaders()
            });

            if (!response.ok) throw new Error('Erreur de chargement');

            const data = await response.json();
            this.members = data.data?.users || [];
            this.updateMembersUI();
        } catch (error) {
            console.error('❌ Erreur chargement membres:', error);
        }
    }

    /**
     * Afficher les messages
     */
    renderMessages() {
        console.log('🎨 Rendering messages, container:', this.messagesList);

        if (!this.messagesList) {
            console.error('❌ messagesList element not found!');
            return;
        }

        if (this.messages.length === 0) {
            console.log('📭 No messages to display');
            this.messagesList.innerHTML = `
                <div class="empty-messages">
                    <div class="empty-messages-icon">💬</div>
                    <h3>Aucun message</h3>
                    <p>Soyez le premier à envoyer un message dans ce canal !</p>
                </div>
            `;
            return;
        }

        let html = '';
        let lastDate = null;
        let lastSenderId = null;

        this.messages.forEach((msg, index) => {
            const msgDate = new Date(msg.created_at).toLocaleDateString('fr-FR');

            // Séparateur de date
            if (msgDate !== lastDate) {
                html += `<div class="message-date-separator"><span>${this.formatDateSeparator(msg.created_at)}</span></div>`;
                lastDate = msgDate;
                lastSenderId = null; // Reset pour afficher le nom
            }

            const isOwn = msg.user_id === this.userId;
            const showSenderName = !isOwn && msg.user_id !== lastSenderId;
            const isConsecutive = msg.user_id === lastSenderId;

            html += this.renderMessage(msg, isOwn, showSenderName, isConsecutive);
            lastSenderId = msg.user_id;
        });

        console.log('🎨 HTML generated, length:', html.length);
        this.messagesList.innerHTML = html;
        this.attachMessageListeners();
        this.renderPinnedMessages();
    }

    renderPinnedMessages() {
        if (!this.pinnedMessagesBar) return;

        const pinnedMessages = this.messages.filter(msg => msg.is_pinned);

        if (!pinnedMessages.length) {
            this.pinnedMessagesBar.innerHTML = '';
            this.pinnedMessagesBar.style.display = 'none';
            return;
        }

        const sorted = [...pinnedMessages].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        const cardsHtml = sorted.map((msg) => {
            if (msg.encrypted && msg.iv && !msg.decrypted_content && !msg._pinnedRefreshQueued) {
                msg._pinnedRefreshQueued = true;
                this.decryptMessageRecord(msg)
                    .then(() => {
                        msg._pinnedRefreshQueued = false;
                        const stillPinned = this.messages.some(m => m.id === msg.id && m.is_pinned);
                        if (stillPinned) {
                            this.renderPinnedMessages();
                        }
                    })
                    .catch(() => { msg._pinnedRefreshQueued = false; });
            }

            const author = msg.user_name || 'Utilisateur';
            const previewText = this.truncate(this.getMessagePreviewText(msg) || 'Message épinglé', 120);
            const time = this.formatTime(msg.created_at);
            const messageIdAttr = this.escapeHtml(String(msg.id));
            const messageIdCall = JSON.stringify(msg.id);

            const unpinButton = (this.isAdmin || msg.user_id === this.userId)
                ? `<button class="pinned-message-unpin" title="Désépingler" onclick="event.stopPropagation(); channelApp.togglePin(${messageIdCall});">✕</button>`
                : '';

            return `
                <div class="pinned-message-card" data-message-id="${messageIdAttr}" onclick="channelApp.scrollToMessage(${messageIdCall})">
                    <div class="pinned-message-icon">📌</div>
                    <div class="pinned-message-content">
                        <div class="pinned-message-header">
                            <span class="pinned-message-author">${this.escapeHtml(author)}</span>
                            <span class="pinned-message-time">${this.escapeHtml(time)}</span>
                        </div>
                        <div class="pinned-message-text">${this.escapeHtml(previewText)}</div>
                    </div>
                    ${unpinButton}
                </div>
            `;
        }).join('');

        this.pinnedMessagesBar.innerHTML = `
            <div class="pinned-messages-header">
                <span class="pinned-messages-title">Messages épinglés</span>
                <span class="pinned-messages-count">${sorted.length}</span>
            </div>
            <div class="pinned-messages-list">
                ${cardsHtml}
            </div>
        `;

        this.pinnedMessagesBar.style.display = 'flex';
    }

    /**
     * Afficher un seul message
     */
    renderMessage(msg, isOwn, showSenderName, isConsecutive) {
        const displayName = msg.user_name || 'Utilisateur';
        const avatarHtml = msg.user_avatar
            ? `<img src="/storage/${msg.user_avatar}" alt="${this.escapeHtml(displayName)}">`
            : `<span class="avatar-initial">${this.escapeHtml(displayName.charAt(0).toUpperCase())}</span>`;

        const replyHtml = msg.reply_to ? this.renderReplySnippet(msg.reply_to, msg.id) : '';

        const reactionsHtml = this.renderReactions(msg);
        const contentHtml = this.renderMessageContent(msg);
        const pinnedBadge = msg.is_pinned ? '<span class="pinned-badge">📌</span>' : '';
        const pinnedMeta = msg.is_pinned ? '<span class="channel-message-pinned">📌 Épinglé</span>' : '';
        const bubblePinnedClass = msg.is_pinned ? ' pinned' : '';
        const messageIdAttr = this.escapeHtml(String(msg.id));
        const messageIdCall = JSON.stringify(msg.id);

        return `
            <div class="channel-message ${isOwn ? 'own' : ''} ${isConsecutive ? 'consecutive' : ''}"
                 data-message-id="${messageIdAttr}"
                 data-user-id="${msg.user_id}">
                ${!isOwn && !isConsecutive ? `
                    <div class="channel-message-avatar">
                        ${avatarHtml}
                    </div>
                ` : ''}
                <div class="channel-message-wrapper">
                    ${showSenderName ? `
                        <div class="channel-message-sender" style="color: ${this.getColorForUser(msg.user_id)}">
                            ${this.escapeHtml(displayName)} ${pinnedBadge}
                        </div>
                    ` : ''}
                    <div class="channel-message-bubble${bubblePinnedClass}">
                        ${replyHtml}
                        <div class="channel-message-content">
                            ${contentHtml}
                        </div>
                        <div class="channel-message-meta">
                            <span class="channel-message-time">${this.formatTime(msg.created_at)}</span>
                            ${pinnedMeta}
                        </div>
                        <div class="channel-message-actions">
                            <button class="msg-action-btn" onclick="channelApp.showReactionPicker(${messageIdCall}, event)" title="Réagir">😀</button>
                            <button class="msg-action-btn" onclick="channelApp.replyToMessage(${messageIdCall})" title="Répondre">↩️</button>
                            ${this.isAdmin || msg.user_id === this.userId ? `
                                <button class="msg-action-btn" onclick="channelApp.togglePin(${messageIdCall})" title="${msg.is_pinned ? 'Désépingler' : 'Épingler'}">📌</button>
                            ` : ''}
                            ${msg.user_id === this.userId || this.isAdmin ? `
                                <button class="msg-action-btn danger" onclick="channelApp.deleteMessage(${messageIdCall})" title="Supprimer">🗑️</button>
                            ` : ''}
                        </div>
                    </div>
                    ${reactionsHtml}
                </div>
            </div>
        `;
    }

    /**
     * Afficher le contenu du message selon son type
     */
    renderMessageContent(msg) {
        // Déchiffrement à l'affichage si nécessaire (texte chiffré)
        if ((msg.content_type === 'text' || msg.type === 'text' || !msg.type) && typeof msg.content === 'string') {
            if (msg.encrypted && msg.iv) {
                const tempId = `dec-${msg.id}-${Math.random().toString(36).slice(2)}`;
                setTimeout(() => {
                    this.decryptMessageRecord(msg).then((plain) => {
                        const span = document.querySelector(`[data-message-id="${msg.id}"] .channel-message-content [data-temp-id="${tempId}"]`);
                        if (!span) return;
                        const paragraph = span.closest('p');
                        if (paragraph) {
                            paragraph.innerHTML = this.parseMessageContent(plain);
                        } else {
                            span.textContent = this.escapeHtml(plain);
                        }
                    });
                }, 0);
                return `<p class="channel-message-text"><span data-temp-id="${tempId}">🔐 Déchiffrement…</span></p>`;
            }

            msg.decrypted_content = msg.content;
            return `<p class="channel-message-text">${this.parseMessageContent(msg.content)}</p>`;
        }
        if (msg.type === 'voice' || (msg.attachments && msg.attachments.some(a => a.type === 'audio'))) {
            const audioUrl = msg.attachments?.[0]?.url || msg.voice_url;
            return `
                <div class="channel-audio-player">
                    <button class="audio-play-btn" onclick="channelApp.toggleAudio(this)">
                        <span class="audio-icon">▶️</span>
                    </button>
                    <div class="audio-waveform">
                        <div class="audio-progress"></div>
                    </div>
                    <span class="audio-duration">${msg.attachments?.[0]?.duration || '0:00'}</span>
                    <audio src="${audioUrl}" preload="metadata"></audio>
                </div>
            `;
        }

        if (msg.type === 'image' || (msg.attachments && msg.attachments.some(a => a.type === 'image'))) {
            const images = msg.attachments?.filter(a => a.type === 'image') || [];
            let imagesHtml = images.map(img => `
                <img src="${img.url}" alt="Image" class="channel-message-image" onclick="channelApp.viewImage('${img.url}')">
            `).join('');

            if (msg.content && msg.content !== 'Image') {
                imagesHtml += `<p class="channel-message-text">${this.parseMessageContent(msg.content)}</p>`;
            }
            return imagesHtml;
        }

        if (msg.type === 'file' || (msg.attachments && msg.attachments.length > 0)) {
            const files = msg.attachments || [];
            let filesHtml = files.map(file => `
                <div class="channel-message-file">
                    <span class="file-icon">${this.getFileIcon(file.name || file.file_name)}</span>
                    <div class="file-info">
                        <span class="file-name">${this.escapeHtml(file.name || file.file_name)}</span>
                        <span class="file-size">${file.size || ''}</span>
                    </div>
                    <a href="${file.url}" download class="file-download">⬇️</a>
                </div>
            `).join('');

            if (msg.content && msg.content !== 'Fichier') {
                filesHtml += `<p class="channel-message-text">${this.parseMessageContent(msg.content)}</p>`;
            }
            return filesHtml;
        }

        // Message texte normal
        if (typeof msg.content === 'string' && !msg.decrypted_content) {
            msg.decrypted_content = msg.content;
        }

        return `<p class="channel-message-text">${this.parseMessageContent(msg.content || '')}</p>`;
    }

    /**
     * Afficher les réactions d'un message
     */
    renderReactions(msg) {
        if (!msg.reactions || msg.reactions.length === 0) return '';

        const reactionsHtml = msg.reactions.map(reaction => `
            <button class="channel-reaction ${reaction.users.includes(this.userName) ? 'own' : ''}"
                    onclick="channelApp.toggleReaction(${msg.id}, '${reaction.emoji}')">
                <span class="reaction-emoji">${reaction.emoji}</span>
                <span class="reaction-count">${reaction.count}</span>
            </button>
        `).join('');

        return `<div class="channel-reactions">${reactionsHtml}</div>`;
    }

    /**
     * Envoyer un message
     */
    async sendMessage() {
        const content = this.messageInput?.value?.trim();
        if (!content && this.selectedFiles.length === 0) return;

        // Chiffrement du contenu texte si présent
        let toSend = content || '';
        let encrypted = false;
        let iv = null;
        if (toSend) {
            const encRes = await this.encryptText(toSend);
            toSend = encRes.ciphertext;
            encrypted = encRes.encrypted;
            iv = encRes.iv;
        }

        const messageData = {
            content: toSend || 'Fichier',
            type: 'text',
            reply_to: this.replyTo?.id || null,
            attachments: this.selectedFiles,
            encrypted: encrypted ? 1 : 0,
            iv: iv || null
        };

        // Optimistic UI - ajouter immédiatement
        const tempId = 'temp_' + Date.now();
        const tempMessage = this.normalizeMessage({
            id: tempId,
            content: content,
            type: 'text',
            user_id: this.userId,
            user_name: this.userName,
            user_avatar: null,
            created_at: new Date().toISOString(),
            reply_to: this.replyTo,
            reactions: [],
            encrypted: false,
            _sending: true
        });

        tempMessage.decrypted_content = content;

        this.messages.push(tempMessage);
        this.renderMessages();
        this.scrollToBottom();

        // Clear input
        if (this.messageInput) this.messageInput.value = '';
        this.cancelReply();
        this.selectedFiles = [];
        this.updateSendButtonState();

        this.updateKeyPreview();

        try {
            const response = await fetch(`/api/channels/${this.channelId}/messages`, {
                method: 'POST',
                headers: {
                    ...this.getHeaders(),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(messageData)
            });

            if (!response.ok) throw new Error('Erreur d\'envoi');

            const data = await response.json();

            // Remplacer le message temporaire par le vrai
            const index = this.messages.findIndex(m => m.id === tempId);
            if (index !== -1) {
                const persisted = this.normalizeMessage({
                    ...data.data,
                    user_name: this.userName
                });

                persisted._sending = false;
                if (content) {
                    persisted.decrypted_content = content;
                }

                this.messages[index] = persisted;
                if (!persisted.encrypted) {
                    persisted.decrypted_content = persisted.decrypted_content || persisted.content;
                }
                this.renderMessages();
            }
        } catch (error) {
            console.error('❌ Erreur envoi message:', error);
            // Marquer le message comme échoué
            const index = this.messages.findIndex(m => m.id === tempId);
            if (index !== -1) {
                this.messages[index]._failed = true;
                this.renderMessages();
            }
            this.showToast('Erreur d\'envoi du message', 'error');
        }
    }

    /**
     * Configuration WebSocket pour temps réel
     */
    setupWebSocket() {
        const echoInstance = window.Echo || (typeof Echo !== 'undefined' ? Echo : null);
        if (!echoInstance) {
            console.warn('⚠️ Echo non disponible pour le temps réel');
            return;
        }

        console.log('🔌 Configuration WebSocket pour canal:', this.channelId);

        const channelName = `channel.${this.channelId}`;
        if (typeof echoInstance.private === 'function') {
            this.echoChannel = echoInstance.private(channelName);
        } else if (typeof echoInstance.channel === 'function') {
            console.warn('ℹ️ Echo.private indisponible, fallback sur Echo.channel');
            this.echoChannel = echoInstance.channel(channelName);
        } else {
            console.warn('⚠️ Impossible de souscrire au canal: aucune méthode private/channel disponible.');
            return;
        }

        this.echoChannel
            .listen('.channel-message.sent', (e) => {
                console.log('📨 Nouveau message canal reçu:', e);
                this.handleNewMessage(e);
            })
            .listen('.channel-message.deleted', (e) => {
                this.handleMessageDeleted(e.message_id);
            })
            .listen('.channel-message.reaction', (e) => {
                this.handleReactionUpdate(e);
            })
            .listen('.channel-user.typing', (e) => {
                this.handleTypingIndicator(e);
            })
            .listen('.channel.updated', (e) => {
                console.log('🔔 Canal mis à jour:', e);
                this.handleChannelUpdated(e);
            });

        if (typeof this.echoChannel.subscribed === 'function') {
            this.echoChannel.subscribed(() => {
                console.log('✅ Connecté au canal WebSocket:', this.channelId);
            });
        }

        // Canal de présence pour les membres en ligne
        if (typeof echoInstance.join === 'function') {
            echoInstance.join(channelName)
                .here((users) => {
                    this.onlineMembers = new Set(users.map(u => u.id));
                    this.updateMembersUI();
                })
                .joining((user) => {
                    this.onlineMembers.add(user.id);
                    this.updateMembersUI();
                    this.showToast(`${user.name} est en ligne`, 'info');
                })
                .leaving((user) => {
                    this.onlineMembers.delete(user.id);
                    this.updateMembersUI();
                });
        } else {
            console.warn('ℹ️ Echo.join indisponible, présence temps réel désactivée.');
        }
    }

    /**
     * Gérer les mises à jour du canal (membre rejoint/quitte, etc.)
     */
    handleChannelUpdated(data) {
        const message = data.message || 'Le canal a été mis à jour';

        switch (data.action) {
            case 'member_joined':
                this.showToast(message, 'success');
                this.loadMembers(); // Recharger la liste des membres
                break;
            case 'member_left':
                this.showToast(message, 'info');
                this.loadMembers();
                break;
            case 'updated':
                this.showToast(message, 'info');
                // Rafraîchir si nécessaire
                break;
            case 'deleted':
                this.showToast('Ce canal a été supprimé', 'warning');
                setTimeout(() => {
                    window.location.href = '/channels';
                }, 2000);
                break;
        }
    }

    /**
     * Gérer un nouveau message reçu via WebSocket
     */
    handleNewMessage(data) {
        // Ne pas ajouter si c'est notre propre message (déjà ajouté en optimistic UI)
        if (data.user_id === this.userId) return;

        const message = this.normalizeMessage({
            id: data.id,
            content: data.content,
            type: data.type,
            encrypted: data.encrypted || false,
            iv: data.iv || null,
            user_id: data.user_id,
            user_name: data.sender?.name || 'Utilisateur',
            user_avatar: data.sender?.avatar || null,
            created_at: data.created_at,
            reply_to: data.reply_to,
            reactions: data.reactions || [],
            attachments: data.attachments,
            is_pinned: data.is_pinned || false
        });

        if (!message.encrypted && typeof message.content === 'string') {
            message.decrypted_content = message.content;
        }

        this.messages.push(message);
        this.renderMessages();

        // Auto-scroll si on est en bas
        if (this.isAtBottom()) {
            this.scrollToBottom();
        } else {
            this.showNewMessageIndicator();
        }

        // Jouer un son de notification
        this.playNotificationSound();
    }

    /**
     * Gérer suppression de message
     */
    handleMessageDeleted(messageId) {
        this.messages = this.messages.filter(m => m.id !== messageId);
        this.renderMessages();
    }

    /**
     * Gérer mise à jour des réactions
     */
    handleReactionUpdate(data) {
        const message = this.messages.find(m => m.id === data.message_id);
        if (message) {
            // Mettre à jour les réactions
            this.loadMessages(); // Recharger pour avoir les bonnes réactions
        }
    }

    /**
     * Afficher indicateur de frappe
     */
    handleTypingIndicator(data) {
        if (data.user_id === this.userId) return;

        this.typingUsers.set(data.user_id, {
            name: data.user_name,
            timestamp: Date.now()
        });

        this.updateTypingIndicator();

        // Supprimer après 3 secondes
        setTimeout(() => {
            this.typingUsers.delete(data.user_id);
            this.updateTypingIndicator();
        }, 3000);
    }

    /**
     * Mettre à jour l'UI de l'indicateur de frappe
     */
    updateTypingIndicator() {
        if (!this.typingIndicator) return;

        const typingNames = Array.from(this.typingUsers.values()).map(u => u.name);

        if (typingNames.length === 0) {
            this.typingIndicator.style.display = 'none';
        } else if (typingNames.length === 1) {
            this.typingIndicator.innerHTML = `<span>${typingNames[0]} est en train d'écrire...</span>`;
            this.typingIndicator.style.display = 'flex';
        } else if (typingNames.length === 2) {
            this.typingIndicator.innerHTML = `<span>${typingNames.join(' et ')} sont en train d'écrire...</span>`;
            this.typingIndicator.style.display = 'flex';
        } else {
            this.typingIndicator.innerHTML = `<span>Plusieurs personnes sont en train d'écrire...</span>`;
            this.typingIndicator.style.display = 'flex';
        }
    }

    /**
     * Envoyer indicateur de frappe
     */
    sendTypingIndicator() {
        if (!this.echoChannel) return;

        // Throttle - envoyer max toutes les 2 secondes
        if (this._lastTypingSent && Date.now() - this._lastTypingSent < 2000) return;
        this._lastTypingSent = Date.now();

        fetch(`/api/channels/${this.channelId}/typing`, {
            method: 'POST',
            headers: {
                ...this.getHeaders(),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ is_typing: true })
        }).catch(() => {});
    }

    /**
     * === VOICE RECORDING ===
     */
    setupVoiceRecorder() {
        // Vérifier le support
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.warn('⚠️ Enregistrement vocal non supporté');
            if (this.voiceButton) this.voiceButton.style.display = 'none';
        }
    }

    async toggleVoiceRecording() {
        if (this.isRecording) {
            this.stopVoiceRecording();
        } else {
            await this.startVoiceRecording();
        }
    }

    async startVoiceRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            this.mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm;codecs=opus' });
            this.audioChunks = [];

            this.mediaRecorder.ondataavailable = (e) => {
                if (e.data.size > 0) this.audioChunks.push(e.data);
            };

            this.mediaRecorder.onstop = () => {
                const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                this.sendVoiceMessage(audioBlob);
                stream.getTracks().forEach(track => track.stop());
            };

            this.mediaRecorder.start();
            this.isRecording = true;
            this.recordingStartTime = Date.now();
            this.updateVoiceUI(true);
            this.startRecordingTimer();

            console.log('🎤 Enregistrement démarré');
        } catch (error) {
            console.error('❌ Erreur accès micro:', error);
            this.showToast('Impossible d\'accéder au microphone', 'error');
        }
    }

    stopVoiceRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.isRecording = false;
            this.updateVoiceUI(false);
            this.stopRecordingTimer();
            console.log('🎤 Enregistrement arrêté');
        }
    }

    cancelVoiceRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.audioChunks = [];
            this.isRecording = false;
            this.updateVoiceUI(false);
            this.stopRecordingTimer();
        }
    }

    updateVoiceUI(recording) {
        if (!this.voiceButton) return;

        if (recording) {
            this.voiceButton.classList.add('recording');
            this.voiceButton.innerHTML = '⏹️';
            this.showRecordingOverlay();
        } else {
            this.voiceButton.classList.remove('recording');
            this.voiceButton.innerHTML = '🎤';
            this.hideRecordingOverlay();
        }
    }

    showRecordingOverlay() {
        let overlay = document.getElementById('channelRecordingOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'channelRecordingOverlay';
            overlay.innerHTML = `
                <div class="recording-content">
                    <div class="recording-pulse"></div>
                    <span class="recording-time">0:00</span>
                    <button class="recording-cancel" onclick="channelApp.cancelVoiceRecording()">✕ Annuler</button>
                </div>
            `;
            document.querySelector('.channel-input-container')?.appendChild(overlay);
        }
        overlay.style.display = 'flex';
    }

    hideRecordingOverlay() {
        const overlay = document.getElementById('channelRecordingOverlay');
        if (overlay) overlay.style.display = 'none';
    }

    startRecordingTimer() {
        this.recordingTimer = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.recordingStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            const timeEl = document.querySelector('.recording-time');
            if (timeEl) timeEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    stopRecordingTimer() {
        if (this.recordingTimer) {
            clearInterval(this.recordingTimer);
            this.recordingTimer = null;
        }
    }

    async sendVoiceMessage(audioBlob) {
        const formData = new FormData();
        formData.append('audio', audioBlob, 'voice.webm');
        formData.append('type', 'voice');

        try {
            const response = await fetch(`/api/channels/${this.channelId}/messages/voice`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) throw new Error('Erreur d\'envoi');

            const data = await response.json();
            this.messages.push(data.data);
            this.renderMessages();
            this.scrollToBottom();
            this.showToast('Message vocal envoyé', 'success');
        } catch (error) {
            console.error('❌ Erreur envoi vocal:', error);
            this.showToast('Erreur d\'envoi du message vocal', 'error');
        }
    }

    /**
     * === EMOJI & REACTIONS ===
     */
    toggleEmojiPicker() {
        if (!this.emojiPicker) return;

        if (this.emojiPickerVisible) {
            this.closeEmojiPicker();
        } else {
            this.openEmojiPicker();
        }
    }

    openEmojiPicker() {
        if (!this.emojiPicker) return;

        // Remplir avec les emojis si pas encore fait
        if (!this.emojiPicker.dataset.loaded) {
            this.populateEmojiPicker();
        }

        this.emojiPicker.style.display = 'block';
        this.emojiPickerVisible = true;
    }

    closeEmojiPicker() {
        if (!this.emojiPicker) return;
        this.emojiPicker.style.display = 'none';
        this.emojiPickerVisible = false;
    }

    populateEmojiPicker() {
        const emojis = [
            '😀', '😃', '😄', '😁', '😅', '😂', '🤣', '😊', '😇', '🙂',
            '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛',
            '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨',
            '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔',
            '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '🥵',
            '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '😎', '🤓', '🧐', '😕',
            '😟', '🙁', '☹️', '😮', '😯', '😲', '😳', '🥺', '😦', '😧',
            '😨', '😰', '😥', '😢', '😭', '😱', '😖', '😣', '😞', '😓',
            '👍', '👎', '👏', '🙌', '🤝', '💪', '❤️', '🔥', '⭐', '✨',
            '🎉', '🎊', '💯', '✅', '❌', '⚠️', '📌', '🔔', '💬', '👀'
        ];

        this.emojiPicker.innerHTML = `
            <div class="emoji-grid">
                ${emojis.map(e => `<button class="emoji-item" onclick="channelApp.insertEmoji('${e}')">${e}</button>`).join('')}
            </div>
        `;
        this.emojiPicker.dataset.loaded = 'true';
    }

    insertEmoji(emoji) {
        if (!this.messageInput) return;

        const start = this.messageInput.selectionStart;
        const end = this.messageInput.selectionEnd;
        const text = this.messageInput.value;

        this.messageInput.value = text.substring(0, start) + emoji + text.substring(end);
        this.messageInput.selectionStart = this.messageInput.selectionEnd = start + emoji.length;
        this.messageInput.focus();
        this.updateSendButtonState();
        this.closeEmojiPicker();
    }

    showReactionPicker(messageId, event) {
        event.stopPropagation();

        const quickReactions = ['👍', '❤️', '😂', '😮', '😢', '🔥'];

        // Créer le picker de réaction
        let picker = document.getElementById('channelReactionPicker');
        if (!picker) {
            picker = document.createElement('div');
            picker.id = 'channelReactionPicker';
            picker.className = 'channel-reaction-picker';
            document.body.appendChild(picker);
        }

        picker.innerHTML = quickReactions.map(e =>
            `<button class="reaction-quick" onclick="channelApp.toggleReaction(${messageId}, '${e}')">${e}</button>`
        ).join('');

        // Positionner près du bouton
        const rect = event.target.getBoundingClientRect();
        picker.style.top = (rect.top - 50) + 'px';
        picker.style.left = rect.left + 'px';
        picker.style.display = 'flex';

        // Fermer après clic ailleurs
        setTimeout(() => {
            document.addEventListener('click', () => {
                picker.style.display = 'none';
            }, { once: true });
        }, 0);
    }

    async toggleReaction(messageId, emoji) {
        try {
            const response = await fetch(`/api/channels/${this.channelId}/messages/${messageId}/react`, {
                method: 'POST',
                headers: {
                    ...this.getHeaders(),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ emoji })
            });

            if (!response.ok) throw new Error('Erreur');

            // Rafraîchir les messages pour voir la réaction
            await this.loadMessages();
        } catch (error) {
            console.error('❌ Erreur réaction:', error);
        }

        // Fermer le picker
        const picker = document.getElementById('channelReactionPicker');
        if (picker) picker.style.display = 'none';
    }

    /**
     * === FILE HANDLING ===
     */
    handleFileSelect(event) {
        const files = Array.from(event.target.files);
        if (files.length === 0) return;

        files.forEach(file => {
            if (file.size > 10 * 1024 * 1024) { // 10MB max
                this.showToast(`${file.name} est trop volumineux (max 10MB)`, 'error');
                return;
            }
            this.selectedFiles.push(file);
        });

        this.showSelectedFiles();
        this.updateSendButtonState();
        event.target.value = '';
    }

    showSelectedFiles() {
        let preview = document.getElementById('channelFilePreview');
        if (!preview) {
            preview = document.createElement('div');
            preview.id = 'channelFilePreview';
            preview.className = 'channel-file-preview';
            document.querySelector('.channel-input-wrapper')?.prepend(preview);
        }

        preview.innerHTML = this.selectedFiles.map((file, i) => `
            <div class="file-preview-item">
                <span class="file-preview-icon">${this.getFileIcon(file.name)}</span>
                <span class="file-preview-name">${this.escapeHtml(file.name)}</span>
                <button class="file-preview-remove" onclick="channelApp.removeFile(${i})">✕</button>
            </div>
        `).join('');

        preview.style.display = this.selectedFiles.length > 0 ? 'flex' : 'none';
    }

    removeFile(index) {
        this.selectedFiles.splice(index, 1);
        this.showSelectedFiles();
        this.updateSendButtonState();
    }

    getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            pdf: '📄', doc: '📝', docx: '📝', xls: '📊', xlsx: '📊',
            ppt: '📊', pptx: '📊', zip: '🗜️', rar: '🗜️',
            jpg: '🖼️', jpeg: '🖼️', png: '🖼️', gif: '🖼️', webp: '🖼️',
            mp3: '🎵', wav: '🎵', mp4: '🎬', mov: '🎬', avi: '🎬'
        };
        return icons[ext] || '📎';
    }

    /**
     * === REPLY ===
     */
    replyToMessage(messageId) {
        const message = this.messages.find(m => m.id === messageId);
        if (!message) return;

        this.replyTo = message;
        this.decryptMessageRecord(message);
        this.showReplyPreview();
        this.messageInput?.focus();
    }

    showReplyPreview() {
        if (!this.replyPreview || !this.replyTo) return;

        if (this.replyPreviewUser) {
            this.replyPreviewUser.textContent = this.replyTo.user_name || 'Utilisateur';
        }

        if (this.replyPreviewText) {
            this.replyPreviewText.textContent = this.truncate(this.getMessagePlainText(this.replyTo), 80);
        }

        this.replyPreview.style.display = 'flex';
    }

    cancelReply() {
        this.replyTo = null;
        if (this.replyPreview) {
            this.replyPreview.style.display = 'none';
        }
        if (this.replyPreviewUser) {
            this.replyPreviewUser.textContent = '';
        }
        if (this.replyPreviewText) {
            this.replyPreviewText.textContent = '';
        }
    }

    /**
     * === PIN & DELETE ===
     */
    async togglePin(messageId) {
        try {
            const response = await fetch(`/api/channels/${this.channelId}/messages/${messageId}/pin`, {
                method: 'POST',
                headers: this.getHeaders()
            });

            if (!response.ok) throw new Error('Erreur');

            const data = await response.json();
            const message = this.messages.find(m => m.id === messageId);
            if (message) {
                message.is_pinned = !!data.is_pinned;
            }

            this.renderMessages();
            this.showToast(data.message, 'success');
        } catch (error) {
            console.error('❌ Erreur pin:', error);
            this.showToast('Erreur', 'error');
        }
    }

    async deleteMessage(messageId) {
        if (!confirm('Supprimer ce message ?')) return;

        try {
            const response = await fetch(`/api/channels/${this.channelId}/messages/${messageId}`, {
                method: 'DELETE',
                headers: this.getHeaders()
            });

            if (!response.ok) throw new Error('Erreur');

            this.messages = this.messages.filter(m => m.id !== messageId);
            this.renderMessages();
            this.showToast('Message supprimé', 'success');
        } catch (error) {
            console.error('❌ Erreur suppression:', error);
            this.showToast('Erreur de suppression', 'error');
        }
    }

    /**
     * === AUDIO PLAYER ===
     */
    toggleAudio(btn) {
        const wrapper = btn.closest('.channel-audio-player');
        const audio = wrapper?.querySelector('audio');
        if (!audio) return;

        if (audio.paused) {
            // Pause tous les autres audios
            document.querySelectorAll('.channel-audio-player audio').forEach(a => {
                if (a !== audio && !a.paused) {
                    a.pause();
                    const player = a.closest('.channel-audio-player');
                    const icon = player?.querySelector('.audio-icon');
                    if (icon) icon.textContent = '▶️';
                }
            });
            audio.play();
            btn.querySelector('.audio-icon').textContent = '⏸️';
        } else {
            audio.pause();
            btn.querySelector('.audio-icon').textContent = '▶️';
        }

        // Progress bar
        if (!audio._listenerAdded) {
            audio.addEventListener('timeupdate', () => {
                const progress = (audio.currentTime / audio.duration) * 100;
                wrapper.querySelector('.audio-progress').style.width = progress + '%';
            });
            audio.addEventListener('ended', () => {
                btn.querySelector('.audio-icon').textContent = '▶️';
                wrapper.querySelector('.audio-progress').style.width = '0%';
            });
            audio._listenerAdded = true;
        }
    }

    /**
     * === UTILITIES ===
     */
    getHeaders() {
        return {
            'Authorization': `Bearer ${this.authToken}`,
            'Accept': 'application/json'
        };
    }

    parseMessageContent(content) {
        if (!content) return '';

        let parsed = this.escapeHtml(content);

        // Liens cliquables
        parsed = parsed.replace(
            /(https?:\/\/[^\s<]+)/g,
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
        );

        // Mentions @user
        parsed = parsed.replace(
            /@(\w+)/g,
            '<span class="mention">@$1</span>'
        );

        // Sauts de ligne
        parsed = parsed.replace(/\n/g, '<br>');

        return parsed;
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    truncate(text, length) {
        if (!text || text.length <= length) return text;
        return text.substring(0, length) + '...';
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    formatDateSeparator(timestamp) {
        const date = new Date(timestamp);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);

        if (date.toDateString() === today.toDateString()) {
            return "Aujourd'hui";
        } else if (date.toDateString() === yesterday.toDateString()) {
            return 'Hier';
        } else {
            return date.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
        }
    }

    getColorForUser(userId) {
        const colors = [
            '#e91e63', '#9c27b0', '#673ab7', '#3f51b5', '#2196f3',
            '#03a9f4', '#00bcd4', '#009688', '#4caf50', '#8bc34a',
            '#cddc39', '#ffeb3b', '#ffc107', '#ff9800', '#ff5722'
        ];
        return colors[userId % colors.length];
    }

    scrollToBottom() {
        if (this.messagesContainer) {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }
    }

    scrollToMessage(messageId) {
        const el = document.querySelector(`[data-message-id="${messageId}"]`);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.classList.add('highlight');
            setTimeout(() => el.classList.remove('highlight'), 2000);
        }
    }

    isAtBottom() {
        if (!this.messagesContainer) return true;
        return this.messagesContainer.scrollHeight - this.messagesContainer.scrollTop - this.messagesContainer.clientHeight < 100;
    }

    showNewMessageIndicator() {
        // TODO: Afficher un badge "Nouveaux messages"
    }

    autoExpandTextarea() {
        if (!this.messageInput) return;
        this.messageInput.style.height = 'auto';
        this.messageInput.style.height = Math.min(this.messageInput.scrollHeight, 120) + 'px';
    }

    updateSendButtonState() {
        if (!this.sendButton) return;
        const hasText = (this.messageInput?.value?.trim().length || 0) > 0;
        const hasFiles = this.selectedFiles.length > 0;
        const isRecording = !!this.isRecording;
        const enabled = hasText || hasFiles || isRecording;
        this.sendButton.disabled = !enabled;
        this.sendButton.classList.toggle('active', enabled);
    }

    normalizeMessage(raw) {
        if (!raw) return null;
        const message = { ...raw };
        message.attachments = Array.isArray(raw.attachments) ? raw.attachments : [];
        message.reactions = Array.isArray(raw.reactions) ? raw.reactions : [];
        message.encrypted = !!raw.encrypted;
        if (!message.user_name && raw.user?.name) {
            message.user_name = raw.user.name;
        }
        if (!message.user_avatar && raw.user?.avatar) {
            message.user_avatar = raw.user.avatar;
        }
        if (!message.user_name && raw.sender?.name) {
            message.user_name = raw.sender.name;
        }
        if (!message.decrypted_content) {
            if (!message.encrypted && typeof message.content === 'string') {
                message.decrypted_content = message.content;
            } else {
                message.decrypted_content = null;
            }
        }
        if (message.reply_to) {
            const reply = message.reply_to;
            message.reply_to = {
                id: reply.id,
                content: reply.content,
                user_name: reply.user_name || reply.user?.name || reply.sender?.name,
                encrypted: !!reply.encrypted,
                iv: reply.iv,
                decrypted_content: reply.decrypted_content || null
            };
        }
        return message;
    }

    getMessagePreviewText(message) {
        if (!message) return '';

        if (message.decrypted_content) {
            return message.decrypted_content;
        }

        if (message.encrypted && message.iv) {
            this.decryptMessageRecord(message);
            return '🔐 Message chiffré';
        }

        if (typeof message.content === 'string' && message.content.trim().length > 0) {
            return message.content;
        }

        if (Array.isArray(message.attachments) && message.attachments.length) {
            const attachment = message.attachments[0] || {};
            const mime = (attachment.mime || attachment.type || '').toLowerCase();
            const name = attachment.name || attachment.original_name || '';

            if (mime.includes('image')) return '📷 Image';
            if (mime.includes('video')) return '🎬 Vidéo';
            if (mime.includes('audio')) return '🎧 Audio';
            if (mime.includes('pdf')) return '📄 PDF';
            if (mime.includes('zip')) return '🗜️ Archive';
            if (name) return `📎 ${name}`;
            return '📎 Pièce jointe';
        }

        if (message.type === 'voice' || message.voice_url) return '🎤 Message vocal';
        if (message.type === 'call') return '📞 Appel';

        return '';
    }

    getMessagePlainText(message) {
        if (!message) return '';
        if (typeof message === 'string') return message;
        if (message.decrypted_content) return message.decrypted_content;
        if (!message.encrypted || !message.iv) {
            message.decrypted_content = message.content || '';
            return message.decrypted_content;
        }
        this.decryptMessageRecord(message);
        return message.decrypted_content || message.content || '';
    }

    async decryptMessageRecord(message) {
        if (!message) return '';
        if (message.decrypted_content) return message.decrypted_content;
        if (!message.encrypted || !message.iv) {
            message.decrypted_content = message.content || '';
            return message.decrypted_content;
        }
        if (message._decryptingPromise) return message._decryptingPromise;

        message._decryptingPromise = (async () => {
            const plain = await this.decryptText(message.content, message.iv);
            message.decrypted_content = plain;
            this.updateReplySnippetsForMessage(message.id, plain);
            if (this.replyTo && this.replyTo.id === message.id) {
                this.showReplyPreview();
            }
            return plain;
        })();

        return message._decryptingPromise;
    }

    updateReplySnippetsForMessage(messageId, plainText) {
        if (!messageId) return;
        const truncated = this.truncate(plainText || '', 50);
        document
            .querySelectorAll(`.channel-message-reply[data-reply-source="${messageId}"] .reply-content`)
            .forEach(el => { el.textContent = truncated; });
    }

    renderReplySnippet(replyInfo, parentId) {
        if (!replyInfo) return '';
        const sourceMessage = this.messages.find(m => m.id === replyInfo.id) || replyInfo;

        const author = sourceMessage.user_name || replyInfo.user_name || 'Utilisateur';
        const sourceId = sourceMessage.id || replyInfo.id || parentId;
        const sourceIdAttr = this.escapeHtml(String(sourceId));
        const sourceIdCall = JSON.stringify(sourceId);
        const plain = this.getMessagePlainText(sourceMessage);
        if (sourceMessage.encrypted && sourceMessage.iv && !sourceMessage.decrypted_content) {
            this.decryptMessageRecord(sourceMessage);
        }

        return `
            <div class="channel-message-reply" data-reply-source="${sourceIdAttr}" onclick="channelApp.scrollToMessage(${sourceIdCall})">
                <span class="reply-author">${this.escapeHtml(author)}</span>
                <span class="reply-content">${this.escapeHtml(this.truncate(plain || '', 50))}</span>
            </div>
        `;
    }

    async fetchChannelKeyFromServer() {
        try {
            const response = await fetch(`/api/channels/${this.channelId}/encryption-key`, {
                headers: this.getHeaders()
            });

            if (!response.ok) {
                throw new Error(`Erreur API clé canal: ${response.status}`);
            }

            const data = await response.json();
            return data?.key || null;
        } catch (error) {
            console.warn('⚠️ Impossible de récupérer la clé du canal:', error);
            return null;
        }
    }

    async saveChannelKeyToServer(keyB64) {
        try {
            const response = await fetch(`/api/channels/${this.channelId}/encryption-key`, {
                method: 'POST',
                headers: {
                    ...this.getHeaders(),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ key: keyB64 })
            });

            if (!response.ok) {
                throw new Error(`Erreur enregistrement clé canal: ${response.status}`);
            }

            return true;
        } catch (error) {
            console.error('❌ Enregistrement clé canal échoué:', error);
            return false;
        }
    }

    getEncryptionStorageKey() {
        return `channel_key_${this.channelId}`;
    }

    getChannelKeyB64() {
        const storageKey = this.getEncryptionStorageKey();
        return localStorage.getItem(storageKey) || null;
    }

    updateKeyPreview() {
        if (!this.keyPreviewEl) return;
        const key = this.getChannelKeyB64();
        if (key) {
            this.keyPreviewEl.textContent = key;
            this.keyPreviewEl.classList.remove('hidden');
        } else {
            this.keyPreviewEl.textContent = '';
            this.keyPreviewEl.classList.add('hidden');
        }
    }

    handleDecryptionFailure(error) {
        if (!this.decryptionWarned) {
            this.decryptionWarned = true;
            this.showToast('Impossible de déchiffrer ce message. Importez la clé du canal via "Importer une clé".', 'warning');
        }

        if (this.keyPreviewEl) {
            this.keyPreviewEl.classList.remove('hidden');
        }

        console.debug('📛 Détail erreur déchiffrement:', error);
    }

    updateMembersUI() {
        if (this.membersCount) {
            this.membersCount.textContent = this.members.length;
        }
        if (this.onlineCount) {
            this.onlineCount.textContent = this.onlineMembers.size;
        }
    }

    showLoadingState() {
        if (this.messagesList) {
            this.messagesList.innerHTML = `
                <div class="channel-loading">
                    <div class="loading-spinner"></div>
                    <p>Chargement des messages...</p>
                </div>
            `;
        }
    }

    showError(message) {
        if (this.messagesList) {
            this.messagesList.innerHTML = `
                <div class="channel-error">
                    <span>❌</span>
                    <p>${message}</p>
                    <button onclick="channelApp.loadMessages()">Réessayer</button>
                </div>
            `;
        }
    }

    showToast(message, type = 'info') {
        // Utiliser le toast global si disponible
        if (typeof showToast === 'function') {
            showToast(message, type);
            return;
        }

        // Fallback simple
        let toast = document.getElementById('channelToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'channelToast';
            toast.className = 'channel-toast';
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.className = `channel-toast ${type} show`;

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    exportChannelKey() {
        const key = this.getChannelKeyB64();
        if (!key) {
            this.showToast('Aucune clé à exporter', 'warning');
            return;
        }

        navigator.clipboard.writeText(key).then(() => {
            this.showToast('Clé du canal copiée dans le presse-papiers', 'success');
        }).catch(() => {
            this.showToast('Impossible de copier la clé, copiez-la manuellement.', 'warning');
            if (this.keyPreviewEl) {
                this.keyPreviewEl.textContent = key;
                this.keyPreviewEl.classList.remove('hidden');
            }
        });
    }

    async importChannelKey() {
        const key = prompt('Collez la clé du canal (base64) :');
        if (!key) return;

        try {
            await this.setupEncryption(key);
            const synced = await this.saveChannelKeyToServer(key.trim());
            this.decryptionWarned = false;
            this.currentPage = 1;
            this.hasMoreMessages = true;
            this.messages = [];
            this.showToast(synced ? 'Clé importée. Déchiffrement en cours…' : 'Clé importée localement (échec de la synchronisation serveur).', synced ? 'success' : 'warning');
            await this.loadMessages();
        } catch (error) {
            console.error('❌ Import key error:', error);
            this.showToast('Clé invalide. Vérifiez et réessayez.', 'error');
        }
    }

    playNotificationSound() {
        if (!this.notificationSoundUrl) return;

        try {
            if (!this.notificationAudio) {
                this.notificationAudio = new Audio(this.notificationSoundUrl);
                this.notificationAudio.volume = 0.3;
            }

            this.notificationAudio.currentTime = 0;
            this.notificationAudio.play().catch(() => {});
        } catch (e) {
            console.warn('🔇 Son de notification indisponible:', e);
        }
    }

    attachMessageListeners() {
        // Double-clic pour répondre
        document.querySelectorAll('.channel-message').forEach(el => {
            el.addEventListener('dblclick', () => {
                const messageId = parseInt(el.dataset.messageId);
                if (messageId) this.replyToMessage(messageId);
            });
        });
    }

    handleContextMenu(event) {
        // TODO: Implémenter menu contextuel
        event.preventDefault();
    }

    viewImage(url) {
        // Ouvrir l'image en grand
        const modal = document.createElement('div');
        modal.className = 'channel-image-modal';
        modal.innerHTML = `
            <div class="modal-backdrop" onclick="this.parentElement.remove()"></div>
            <img src="${url}" alt="Image">
            <button class="modal-close" onclick="this.parentElement.remove()">✕</button>
        `;
        document.body.appendChild(modal);
    }

    /**
     * Destruction propre
     */
    destroy() {
        if (this.echoChannel) {
            Echo.leave(`channel.${this.channelId}`);
        }
        if (this.recordingTimer) {
            clearInterval(this.recordingTimer);
        }
        console.log('🔌 ChannelMessagingApp destroyed');
    }
}

// Export global
window.ChannelMessagingApp = ChannelMessagingApp;
