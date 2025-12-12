/**
 * 🚀 Application de Messagerie Moderne
 * Interface temps réel avec WebSocket et E2E encryption
 */

class MessagingApp {
    constructor(userId, userName, authToken) {
        console.log('📦 MessagingApp constructor appelé', { userId, userName });
        this.userId = userId;
        this.userName = userName;
        this.authToken = authToken;
        this.currentConversation = null;
        this.currentConversationUserId = null; // ID de l'autre utilisateur dans la conversation actuelle
        this.messages = {};
        this.conversations = [];
        this.onlineUsers = new Set();
        this.typingTimeout = null;
        this.ws = null;
        this.replyTo = null;
        this.selectedFiles = [];
        this.voiceRecorder = null;
        this.isSending = false; // Flag pour éviter les envois multiples

        // Service de chiffrement E2E
        this.encryptionService = null;

        // Cache pour éviter les changements de statut trop fréquents
        this.statusCache = new Map(); // userId -> {status, timestamp}
        this.statusChangeCooldown = 2000; // 2 secondes minimum entre changements

        console.log('🔧 Appel de init()...');
        this.init();
    }

    /**
     * Initialisation de l'application
     */
    init() {
        console.log('⚙️ Méthode init() démarrée');
        this.setupEventListeners();
        console.log('👂 Event listeners configurés');
        this.loadConversations();
        console.log('📞 loadConversations() appelé');
        this.setupWebSocket();
        this.autoExpandTextarea();

        // Initialiser le voice recorder
        if (typeof VoiceRecorder !== 'undefined') {
            this.voiceRecorder = new VoiceRecorder(this);
            // Assigner à la variable globale pour les événements onclick
            window.voiceRecorder = this.voiceRecorder;
        }

        // Initialiser le service de chiffrement E2E
        this.initEncryptionService();

        // Polling pour actualiser les messages en temps réel
        this.startPolling();

        // Initialiser l'état du bouton d'envoi
        this.updateSendButtonState();
    }

    /**
     * Établir une connexion E2E avec un destinataire
     */
    async establishE2EConnection(recipientId) {
        try {
            console.log(`🔐 Établissement connexion E2E avec ${recipientId}`);

            // Récupérer la clé publique du destinataire depuis l'API
            const response = await fetch(`/api/users/${recipientId}/public-key`, {
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Impossible de récupérer la clé publique du destinataire');
            }

            const data = await response.json();
            const recipientPublicKey = data.public_key;

            // Générer un secret partagé
            await this.encryptionService.generateSharedSecret(recipientId, recipientPublicKey);

            // Envoyer notre clé publique chiffrée au destinataire
            const ourPublicKey = await this.encryptionService.exportPublicKey();
            const encryptedSharedSecret = await this.encryptionService.encryptSharedSecret(recipientId);

            await fetch(`/api/users/${recipientId}/establish-e2e`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    public_key: ourPublicKey,
                    encrypted_secret: encryptedSharedSecret
                })
            });

            console.log(`✅ Connexion E2E établie avec ${recipientId}`);
        } catch (error) {
            console.error('❌ Erreur établissement connexion E2E:', error);
            throw error;
        }
    }

    /**
     * Initialiser le service de chiffrement E2E
     */
    async initEncryptionService() {
        try {
            if (typeof E2EEncryptionService !== 'undefined') {
                this.encryptionService = new E2EEncryptionService();
                await this.encryptionService.initialize();
                console.log('🔐 Service E2E Encryption initialisé avec succès');
            } else {
                console.warn('⚠️ Service E2E Encryption non disponible');
            }
        } catch (error) {
            console.error('❌ Erreur lors de l\'initialisation du chiffrement E2E:', error);
        }
    }

    _fetchOptions(method = 'GET', body = null) {
        const headers = {
            'Accept': 'application/json'
        };

        if (this.authToken && this.authToken.length) {
            headers['Authorization'] = `Bearer ${this.authToken}`;
        }

        const opts = {
            method,
            headers
        };

        // Only add credentials if no auth token (fallback to session)
        if (!this.authToken || !this.authToken.length) {
            opts.credentials = 'same-origin';
        }

        if (body) {
            if (!headers['Content-Type']) headers['Content-Type'] = 'application/json';
            opts.body = typeof body === 'string' ? body : JSON.stringify(body);
        }

        return opts;
    }

    // Toggle play/pause sécurisé pour les players audio insérés dynamiquement
    toggleAudioPlayer(btn) {
        console.log('🎵 [TOGGLE] toggleAudioPlayer appelé, btn:', btn);
        try {
            const wrapper = btn.closest('.audio-player-wrapper') || btn.parentElement;
            console.log('🎵 [TOGGLE] wrapper:', wrapper);
            const audio = wrapper ? wrapper.querySelector('audio') : null;
            console.log('🎵 [TOGGLE] audio element:', audio);
            if (!audio) {
                console.warn('🎵 [TOGGLE] Aucun élément audio trouvé !');
                return;
            }
            console.log('🎵 [TOGGLE] audio.src:', audio.src, 'paused:', audio.paused, 'duration:', audio.duration);
            // Attacher des listeners une fois pour maintenir l'état du bouton et afficher la progression
            if (!audio._listenersAttached) {
                const iconSpan = btn.querySelector('.audio-icon');
                const durEl = wrapper.querySelector('.audio-duration');

                // Formater le temps en mm:ss
                const fmt = (t) => {
                    const mm = Math.floor(t/60);
                    const ss = Math.floor(t%60);
                    return `${mm}:${ss.toString().padStart(2,'0')}`;
                };

                // Stocker la durée totale originale au premier chargement
                audio.addEventListener('loadedmetadata', () => {
                    try {
                        if (durEl && !durEl.dataset.totalSeconds) {
                            durEl.dataset.totalSeconds = Math.floor(audio.duration);
                            durEl.dataset.originalText = durEl.textContent;
                        }
                    } catch(e){}
                });

                audio.addEventListener('play', () => {
                    try {
                        if (iconSpan) iconSpan.textContent = '⏸️';
                        wrapper.classList.add('playing');
                    } catch(e){}
                });

                audio.addEventListener('pause', () => {
                    try {
                        if (iconSpan) iconSpan.textContent = '▶️';
                        wrapper.classList.remove('playing');
                    } catch(e){}
                });

                audio.addEventListener('ended', () => {
                    try {
                        if (iconSpan) iconSpan.textContent = '▶️';
                        wrapper.classList.remove('playing');
                        audio.currentTime = 0;
                        // Restaurer la durée totale
                        if (durEl && durEl.dataset.originalText) {
                            durEl.textContent = durEl.dataset.originalText;
                        }
                    } catch(e){}
                });

                // Mettre à jour le timer en temps réel pendant la lecture
                audio.addEventListener('timeupdate', () => {
                    try {
                        if (durEl && durEl.dataset.totalSeconds) {
                            const current = Math.floor(audio.currentTime);
                            const total = durEl.dataset.totalSeconds;
                            durEl.textContent = `${fmt(current)} / ${fmt(total)}`;
                        }
                    } catch(e){}
                });

                audio._listenersAttached = true;
            }

            // Prévenir les appels concurrents (play() renvoie une promise qui peut être abortée)
            if (audio._playTogglePending) {
                console.debug('🎵 play/pause pending, ignoring toggle');
                return;
            }

            // 🔑 Si audio.src est vide, le récupérer depuis le <source> enfant
            if (!audio.src || audio.src === '') {
                const sourceEl = audio.querySelector('source');
                if (sourceEl && sourceEl.src) {
                    console.log('🎵 [TOGGLE] Définition de audio.src depuis <source>:', sourceEl.src);
                    audio.src = sourceEl.src;
                    audio.load(); // Forcer le chargement
                } else {
                    console.error('🎵 [TOGGLE] Aucun src trouvé !');
                    return;
                }
            }

            // Si l'audio est à la fin, remettre au début pour relire
            if (audio.ended) audio.currentTime = 0;

            // Pause tous les autres players actifs pour un comportement exclusif comme WhatsApp
            try {
                document.querySelectorAll('audio').forEach(a => { if (a !== audio && !a.paused) try{ a.pause(); }catch(e){} });
            } catch(e) {}

            const iconSpan = btn.querySelector('.audio-icon');
            if (audio.paused) {
                audio._playTogglePending = true;
                console.log('🎵 [TOGGLE] Appel de play()...');
                audio.play()
                    .then(() => {
                        console.log('🎵 [TOGGLE] ✅ Lecture démarrée !');
                        try { if (iconSpan) iconSpan.textContent = '⏸️'; } catch(e){}
                    })
                    .catch(err => {
                        if (err && (err.name === 'AbortError' || err.message && err.message.toLowerCase().includes('interrupted'))) {
                            console.debug('🎵 play aborted/interrupt (ignored)');
                        } else {
                            console.error('🎵 play failed', err);
                        }
                    })
                    .finally(() => { audio._playTogglePending = false; });
            } else {
                audio.pause();
                try { if (iconSpan) iconSpan.textContent = '▶️'; } catch(e){}
            }
        } catch (e) {
            console.error('toggleAudioPlayer error', e);
        }
    }

    /**
     * Mettre à jour la zone des réactions d'un message directement dans le DOM
     */
    updateMessageReactionsInDOM(messageId) {
        try {
            const convId = this.currentConversation?.id;
            if (!convId) return;
            const msg = (this.messages[convId] || []).find(m => m.id === messageId);
            const bubble = document.querySelector(`.message-bubble[data-message-id="${messageId}"]`);
            if (!bubble) return;

            let reactionsContainer = bubble.querySelector('.message-reactions');
            if (!reactionsContainer) {
                // create container under message-content-wrapper
                const wrapper = bubble.querySelector('.message-content-wrapper');
                reactionsContainer = document.createElement('div');
                reactionsContainer.className = 'message-reactions';
                wrapper.appendChild(reactionsContainer);
            }

            // Build inner HTML: existing grouped reactions + add btn
            const reactionsHtml = (msg && msg.reactions && this.renderReactionsDisplay(msg.reactions, messageId)) || '';
            reactionsContainer.innerHTML = reactionsHtml + ` <button class="add-reaction-btn" onclick="window.messagingApp.showReactionPicker(event, ${messageId})">➕</button>`;
        } catch (e) {
            console.error('updateMessageReactionsInDOM error', e);
        }
    }

    /**
     * Démarrer le polling pour les nouveaux messages
     */
    startPolling() {
        // Vérifier les nouveaux messages toutes les 3 secondes
        setInterval(() => {
            if (this.currentConversation && this.currentConversation.id) {
                this.refreshMessages();
            }
        }, 3000);

        // Rafraîchir la liste des conversations toutes les 10 secondes pour mettre à jour les statuts
        setInterval(() => {
            this.refreshConversationStatuses();
        }, 10000);
    }

    /**
     * Rafraîchir uniquement les statuts des conversations sans recharger toute la liste
     */
    async refreshConversationStatuses() {
        try {
            // Utiliser uniquement le presenceManager pour éviter les conflits
            if (window.userPresenceManager) {
                try {
                    // Récupérer la liste en ligne via presenceManager (plus fiable)
                    const onlineList = await window.userPresenceManager.getOnlineUsers();
                    const onlineIds = new Set(onlineList.map(u => u.user_id));

                    // Mettre à jour les conversations en mémoire et dans le DOM
                    this.conversations.forEach(conv => {
                        const newStatus = onlineIds.has(conv.user_id) ? 'online' : 'offline';

                        // Vérifier le cache pour éviter les changements trop fréquents
                        const cached = this.statusCache.get(conv.user_id);
                        const now = Date.now();

                        if (cached && cached.status === newStatus && (now - cached.timestamp) < this.statusChangeCooldown) {
                            return; // Pas de changement nécessaire
                        }

                        // Éviter les changements inutiles pour éviter le clignotement
                        if (conv.user_status !== newStatus) {
                            conv.user_status = newStatus;
                            this.statusCache.set(conv.user_id, { status: newStatus, timestamp: now });

                            const badge = document.querySelector(`.conversation-item[data-user-id="${conv.user_id}"] .status-badge`);
                            if (badge) {
                                badge.className = 'status-badge';
                                badge.classList.add(`status-${newStatus}`);
                            }
                        }
                    });

                    return; // Succès avec presenceManager, pas besoin du fallback
                } catch (e) {
                    console.warn('❗ presenceManager.getOnlineUsers échoué:', e);
                }
            }

            // Fallback uniquement si presenceManager n'est pas disponible
            console.log('🔄 [REFRESH] Utilisation du fallback API conversations');
            const response = await fetch('/api/messaging/conversations', this._fetchOptions('GET'));

            if (!response.ok) return;

            const data = await response.json();
            const conversations = data.conversations || [];

            // Mettre à jour uniquement les badges de statut dans le DOM
            conversations.forEach(conv => {
                const badge = document.querySelector(`.conversation-item[data-user-id="${conv.user_id}"] .status-badge`);
                if (badge) {
                    const newStatus = conv.user_status || 'offline';
                    const currentStatus = badge.classList.contains('status-online') ? 'online' : 'offline';

                    // Éviter les changements inutiles
                    if (currentStatus !== newStatus) {
                        // Retirer les anciennes classes de statut
                        badge.className = 'status-badge';
                        // Ajouter la nouvelle classe de statut
                        badge.classList.add(`status-${newStatus}`);
                    }
                }
            });
        } catch (error) {
            console.error('❌ Erreur refreshConversationStatuses:', error);
        }
    }

    /**
     * Rafraîchir les messages de la conversation actuelle
     */
    async refreshMessages() {
        if (!this.currentConversation) return;

        try {
            const response = await fetch(`/api/messaging/conversation/${this.currentConversation.id}?page=1&per_page=50`, this._fetchOptions('GET'));

            if (!response.ok) return;

            const data = await response.json();
            const newMessages = (data.messages || []).reverse(); // Inverser l'ordre

            // Comparer avec les messages actuels
            const currentCount = this.messages[this.currentConversation.id]?.length || 0;
            if (newMessages.length > currentCount) {
                // Nouveaux messages détectés
                this.messages[this.currentConversation.id] = newMessages;
                await this.renderMessages();
                this.scrollToBottom();
                console.log('🔄 Nouveaux messages reçus:', newMessages.length - currentCount);
            }
        } catch (error) {
            // Ignorer les erreurs de polling
        }
    }

    /**
     * Configuration des écouteurs d'événements
     */
    setupEventListeners() {
        // Bouton d'envoi
        document.getElementById('sendMessageBtn')?.addEventListener('click', () => this.sendMessage());

        // Textarea - Entrée pour envoyer (supporte l'envoi de vocaux en cours/pause)
        document.getElementById('messageTextarea')?.addEventListener('keydown', async (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();

                // Si un enregistrement est en cours ou en pause, arrêter et préparer le blob avant d'envoyer
                if (this.voiceRecorder && (this.voiceRecorder.isRecording || this.voiceRecorder.isPaused)) {
                    console.log('🎤 Entrée détectée : arrêt de l\'enregistrement actif avant envoi');
                    await this.voiceRecorder.stopAndGetPendingAudio();
                }

                this.sendMessage();
            }
        });

        // Textarea - Indicateur de frappe
        document.getElementById('messageTextarea')?.addEventListener('input', (e) => {
            this.updateCharCounter(e.target.value.length);
            this.handleTyping();
            this.updateSendButtonState();
        });

        // Écouter les changements de statut depuis UserPresenceManager
        window.addEventListener('user-status-changed', (event) => {
            const { userId, statusDetails, status: dispatchStatus } = event.detail;
            if (userId && statusDetails) {
                const status = dispatchStatus || statusDetails.status || 'offline';

                // Mettre à jour la mémoire (conversations) pour garder l'état cohérent
                if (this.conversations && this.conversations.length) {
                    const convIndex = this.conversations.findIndex(c => c.user_id === userId);
                    if (convIndex !== -1) {
                        this.conversations[convIndex].user_status = status;
                    }
                }

                // Mettre à jour le badge dans la liste des conversations (DOM)
                const badge = document.querySelector(`.conversation-item[data-user-id="${userId}"] .status-badge`);
                if (badge) {
                    badge.className = 'status-badge';
                    badge.classList.add(`status-${status}`);
                }

                // Mettre à jour la collection locale des utilisateurs online
                if (status === 'online') {
                    this.onlineUsers.add(userId);
                } else {
                    this.onlineUsers.delete(userId);
                }

                // Mettre à jour le header si c'est la conversation actuelle
                if (this.currentConversation && this.currentConversation.id === userId) {
                    this.updateUserStatus(userId, status);
                }
            }
        });

        // Emoji picker
        document.getElementById('emojiPickerBtn')?.addEventListener('click', () => this.toggleEmojiPicker());

        // File upload
        document.getElementById('attachFileBtn')?.addEventListener('click', () => this.triggerFileUpload());
        document.getElementById('fileInput')?.addEventListener('change', (e) => this.handleFileSelect(e));

        // Voice record
        document.getElementById('voiceRecordBtn')?.addEventListener('click', () => this.toggleVoiceRecording());

        // Scroll to bottom
        document.getElementById('scrollToBottom')?.addEventListener('click', () => this.scrollToBottom());

        // Fermer réponse
        document.getElementById('cancelReply')?.addEventListener('click', () => this.cancelReply());

        // Search
        document.getElementById('searchInChatBtn')?.addEventListener('click', () => {
            console.log('🔍 Bouton recherche cliqué');
            this.toggleSearch();
        });
        document.getElementById('closeSearchPanel')?.addEventListener('click', () => {
            console.log('❌ Bouton fermer recherche cliqué');
            this.closeSearch();
        });
        document.getElementById('searchInChat')?.addEventListener('input', (e) => this.searchMessages(e.target.value));

        // Pinned messages
        document.getElementById('showPinnedBtn')?.addEventListener('click', () => this.togglePinned());
        document.getElementById('closePinnedPanel')?.addEventListener('click', () => this.closePinned());

        // Emoji search
        document.getElementById('emojiSearch')?.addEventListener('input', (e) => this.filterEmojis(e.target.value));

        // Click outside to close
        document.addEventListener('click', (e) => this.handleClickOutside(e));

        // Conversations search
        document.getElementById('searchConversations')?.addEventListener('input', (e) => {
            this.filterConversations(e.target.value);
        });
    }

    /**
     * Charger toutes les conversations
     */
    async loadConversations() {
        console.log('🔄 Chargement des conversations...');

        // Afficher le spinner pendant le chargement
        const list = document.getElementById('conversationsList');
        if (list) {
            list.innerHTML = '<div class="loading-state"><div class="spinner"></div><p>Chargement des conversations...</p></div>';
        }

        try {
            const response = await fetch('/api/messaging/conversations', this._fetchOptions('GET'));

            console.log('📡 Réponse API:', response.status, response.ok);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                console.error('❌ Erreur API:', errorData);
                throw new Error('Erreur de chargement des conversations');
            }

            const data = await response.json();
            console.log('✅ Données reçues:', data);
            this.conversations = data.conversations || [];
            console.log('📋 Conversations:', this.conversations.length);
            this.renderConversations();
        } catch (error) {
            console.error('❌ Erreur:', error);
            this.showNotification('Erreur de chargement des conversations', 'error');
            // Afficher un message d'erreur dans la liste
            const list = document.getElementById('conversationsList');
            if (list) {
                list.innerHTML = `
                    <div class="empty-state" style="color: red;">
                        <p>❌ Erreur de chargement</p>
                        <p style="font-size: 12px;">${error.message}</p>
                    </div>
                `;
            }
        }
    }

    /**
     * Afficher les conversations
     */
    renderConversations() {
        const list = document.getElementById('conversationsList');
        if (!list) return;

        if (!this.conversations || this.conversations.length === 0) {
            list.innerHTML = '<div class="empty-state"><p>Aucune conversation</p></div>';
            return;
        }

        let html = '';
        this.conversations.forEach(conv => {
            const avatarHtml = conv.user_avatar
                ? ('<img src="/storage/' + conv.user_avatar + '" alt="' + this.escapeHtml(conv.user_name) + '">')
                : ('<span class="avatar-initial">' + this.escapeHtml((conv.user_name || '').charAt(0).toUpperCase()) + '</span>');
            const fullPreviewText = conv.last_message ? ((conv.last_message.is_sent_by_me ? 'Vous: ' : '') + this.escapeHtml(conv.last_message.content || '')) : '';
            const previewText = this.truncatePreviewText(fullPreviewText, 40);
            html += '<div class="conversation-item ' + (conv.user_id === this.currentConversation?.id ? 'active' : '') + ' ' + (conv.unread_count > 0 ? 'unread' : '') + '" data-user-id="' + conv.user_id + '" onclick="window.messagingApp.selectConversation(' + conv.user_id + ')">'
                + '<div class="conversation-avatar">' + avatarHtml + '<span class="status-badge status-' + (conv.user_status || 'offline') + '"></span></div>'
                + '<div class="conversation-content"><div class="conversation-header"><span class="conversation-name">' + this.escapeHtml(conv.user_name) + '</span><span class="conversation-time">' + this.formatTime(conv.last_message?.created_at || '') + '</span></div>'
                + '<div class="conversation-preview ' + (conv.unread_count > 0 ? 'unread' : '') + '">' + previewText + '</div></div>'
                + (conv.unread_count > 0 ? ('<span class="unread-badge">' + conv.unread_count + '</span>') : '')
                + '</div>';
        });

        list.innerHTML = html;
    }

    /**
     * Sélectionner une conversation
     */
    async selectConversation(userId) {
        console.log('👆 Sélection de la conversation avec user:', userId);

        // Fermer/réinitialiser toutes les actions en cours (recherche, épinglés, reply, picker, enregistrement...)
        try {
            // Search & pinned
            try { this.closeSearch(); } catch (e) {}
            try { this.closePinned(); } catch (e) {}
            // Reply composer
            try { this.cancelReply(); } catch (e) {}

            // Emoji / reaction picker
            try {
                const picker = document.getElementById('emojiPicker');
                if (picker) {
                    picker.style.display = 'none';
                    delete picker.dataset.mode;
                    delete picker.dataset.messageId;
                }
            } catch (e) {}

            // Context menu
            try {
                const cm = document.getElementById('messageContextMenu');
                if (cm) cm.style.display = 'none';
            } catch (e) {}

            // Voice recorder: arrêter ou annuler tout audio en cours / preview
            try {
                if (this.voiceRecorder) {
                    if (this.voiceRecorder.isRecording || this.voiceRecorder.isPaused) {
                        try { this.voiceRecorder.stopRecording(); } catch (e) { console.warn('stopRecording failed', e); }
                    }
                    try { if (this.voiceRecorder.getPendingAudio && this.voiceRecorder.getPendingAudio()) this.voiceRecorder.cancelAudio(); } catch (e) {}
                }
            } catch (e) {}
        } catch (err) {
            console.warn('Erreur lors du nettoyage avant changement de conversation', err);
        }

        // Définir la conversation courante
        this.currentConversation = { id: userId };
        this.currentConversationUserId = userId; // Stocker l'ID de l'autre utilisateur

        // Réinitialiser le compteur de messages non lus pour cette conversation
        const conv = this.conversations.find(c => c.user_id === userId);
        if (conv) {
            conv.unread_count = 0;
        }

        // Afficher la zone de chat
        this.showChatArea();

        // Charger les messages de cette conversation
        await this.loadConversation(userId);

        // Mettre à jour l'état actif dans la liste (sans recharger toute la liste)
        this.updateConversationActiveState(userId);
    }

    /**
     * Mettre à jour l'état actif d'une conversation dans le DOM
     */
    updateConversationActiveState(userId) {
        // Retirer la classe active de toutes les conversations
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('active');
        });

        // Ajouter la classe active à la conversation sélectionnée
        const activeItem = document.querySelector(`.conversation-item[data-user-id="${userId}"]`);
        if (activeItem) {
            activeItem.classList.add('active');

            // Réinitialiser le compteur de messages non lus
            const unreadBadge = activeItem.querySelector('.unread-badge');
            if (unreadBadge) {
                unreadBadge.remove();
            }

            // Retirer la classe unread du texte de prévisualisation
            const previewElement = activeItem.querySelector('.conversation-preview');
            if (previewElement) {
                previewElement.classList.remove('unread');
            }

            // Retirer la classe unread de l'élément de conversation
            activeItem.classList.remove('unread');
        }
    }

    /**
     * Mettre à jour le statut d'un utilisateur dans le header de la conversation
     */
    updateUserStatus(userId, status) {
        console.log('✅ [PRESENCE] Header mis à jour - statusText:', status === 'online' ? 'En ligne (vert)' : 'Hors ligne (rouge)');

        // Mettre à jour le badge de statut dans le header
        const statusEl = document.getElementById('chatUserStatus');
        if (statusEl) {
            statusEl.className = `status-badge status-${status}`;
            console.log('✅ [PRESENCE] Header statusEl classes:', statusEl.className);
        } else {
            console.warn('❌ [PRESENCE] Header statusEl introuvable');
        }

        // Mettre à jour le texte du statut si présent
        const statusTextEl = document.querySelector('#chatHeader .status-text');
        if (statusTextEl) {
            statusTextEl.textContent = status === 'online' ? 'En ligne' : 'Hors ligne';
        }
    }

    /**
     * Charger les messages d'une conversation
     */
    async loadConversation(userId) {
        console.log('📨 [LOAD_CONV_START] Chargement des messages pour conversation:', userId, 'timestamp:', Date.now());

        // Afficher le spinner de chargement
        this.showLoadingState();

        try {
            console.log('🔄 [LOAD_CONV_TRY] Début du try pour userId:', userId);
            console.log('🔑 [LOAD_CONV_AUTH] Auth token présent:', !!this.authToken, 'length:', this.authToken?.length);
            const response = await fetch(`/api/messaging/conversation/${userId}?page=1&per_page=50`, this._fetchOptions('GET'));

            console.log('📡 [LOAD_CONV_RESPONSE] Réponse reçue, status:', response.status, 'ok:', response.ok);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                console.error('❌ [LOAD_CONV_ERROR] Erreur API conversation:', errorData);
                throw new Error('Erreur de chargement des messages');
            }

            const data = await response.json();
            console.log('✅ [LOAD_CONV_DATA] Messages reçus:', data.messages?.length || 0);
            console.log('🔍 [LOAD_CONV_DATA] Exemple de message:', data.messages?.[0]);

            // Stocker les messages
            this.messages[userId] = (data.messages || []).reverse(); // Inverser l'ordre
            // Stocker la pagination pour ce fil
            try {
                this._convPagination = this._convPagination || {};
                this._convPagination[userId] = data.pagination || { current_page: 1, total_pages: 1, total: (data.messages||[]).length };
            } catch (e) { /* noop */ }
            console.log('💾 [LOAD_CONV_STORE] Messages stockés pour userId:', userId, 'count:', this.messages[userId].length);
            console.log('🔍 [LOAD_CONV_STORE] Premier message stocké:', this.messages[userId]?.[0]);

            // Rendre les messages
            console.log('🎨 [LOAD_CONV_RENDER] Appel renderMessages');
            await this.renderMessages();

            // Défiler vers le bas
            this.scrollToBottom();

            // Mettre à jour les informations de l'utilisateur
            if (data.user) {
                const nameEl = document.getElementById('chatUserName');
                const avatarEl = document.getElementById('chatUserAvatar');
                const statusEl = document.getElementById('chatUserStatus');
                if (nameEl) nameEl.textContent = data.user.name;
                else console.warn('chatUserName element introuvable');
                if (avatarEl) {
                    try { avatarEl.src = `/storage/${data.user.avatar}`; } catch(e) { console.warn('Impossible de définir chatUserAvatar.src', e); }
                } else console.warn('chatUserAvatar element introuvable');

                // Initialiser avec un statut temporaire, le vrai statut sera mis à jour par l'événement user-status-changed
                if (statusEl) {
                    statusEl.className = 'status-badge status-offline'; // Temporaire, sera corrigé par l'événement
                    console.log('🔄 [LOAD_CONV_STATUS] Statut temporaire défini, en attente de mise à jour par presenceManager');
                } else {
                    console.warn('chatUserStatus element introuvable');
                }
            }

            console.log('✅ [LOAD_CONV_SUCCESS] Chargement terminé avec succès pour userId:', userId);

            // Déclencher une mise à jour du statut pour le header (le presenceManager va émettre l'événement)
            if (window.userPresenceManager) {
                // Forcer une vérification immédiate du statut de cet utilisateur
                try {
                    const onlineUsers = await window.userPresenceManager.getOnlineUsers();
                    const userOnline = onlineUsers.find(u => u.user_id === userId);
                    if (userOnline) {
                        // Émettre directement l'événement pour mettre à jour le header
                        window.dispatchEvent(new CustomEvent('user-status-changed', {
                            detail: {
                                userId: userId,
                                statusDetails: userOnline.status_details,
                                status: userOnline.status || 'online'
                            }
                        }));
                        console.log('✅ [PRESENCE] Événement user-status-changed émis pour userId:', userId);
                    } else {
                        // Utilisateur hors ligne
                        window.dispatchEvent(new CustomEvent('user-status-changed', {
                            detail: {
                                userId: userId,
                                statusDetails: { color: '#ef4444', label: 'Hors ligne' },
                                status: 'offline'
                            }
                        }));
                        console.log('✅ [PRESENCE] Événement user-status-changed émis (offline) pour userId:', userId);
                    }
                } catch (e) {
                    console.warn('❗ Erreur lors de la vérification du statut:', e);
                }
            }

        } catch (error) {
            console.error('❌ [LOAD_CONV_CATCH] Erreur dans loadConversation:', error.message);
            console.error('❌ [LOAD_CONV_CATCH] Stack:', error.stack);
            this.showNotification('Erreur de chargement des messages', 'error');
        }
    }

    /**
     * Charger une page spécifique (utile pour charger d'anciens messages et les préfixer)
     */
    async loadConversationPage(userId, page = 1, perPage = 50) {
        console.log('📨 [LOAD_CONV_PAGE] Chargement page', page, 'pour conversation', userId);
        try {
            const response = await fetch(`/api/messaging/conversation/${userId}?page=${page}&per_page=${perPage}`, this._fetchOptions('GET'));

            if (!response.ok) {
                console.error('❌ [LOAD_CONV_PAGE] Erreur API page', page);
                return null;
            }

            const data = await response.json();
            const newMsgs = (data.messages || []).reverse();

            // Initialiser structure messages si nécessaire
            this.messages[userId] = this.messages[userId] || [];

            // Si on a déjà des messages, préfixer (anciennes messages avant)
            // Eviter doublons en filtrant les ids déjà présents
            const existingIds = new Set(this.messages[userId].map(m => m.id));
            const toPrefix = newMsgs.filter(m => !existingIds.has(m.id));

            if (toPrefix.length) {
                this.messages[userId] = toPrefix.concat(this.messages[userId]);
                // Rendre et conserver le scroll position raisonnable
                await this.renderMessages();
            }

            // Mettre à jour pagination stockée
            this._convPagination = this._convPagination || {};
            this._convPagination[userId] = data.pagination || { current_page: page, total_pages: 1, total: (this.messages[userId]||[]).length };

            return data.pagination || null;
        } catch (e) {
            console.error('❌ [LOAD_CONV_PAGE] Exception:', e);
            return null;
        }
    }

    /**
     * Afficher les messages
     */
    async renderMessages() {
        const container = document.getElementById('messagesList');
        if (!container || !this.currentConversation) {
            console.error('❌ Container ou conversation manquant');
            return;
        }

        const messages = this.messages[this.currentConversation.id] || [];
        console.log('🎨 Rendu de', messages.length, 'messages pour conversation', this.currentConversation.id);
        console.log('🔍 [RENDER_MSGS] Exemple de message à rendre:', messages[0]);

        if (messages.length === 0) {
            container.innerHTML = '<div class="empty-state"><p>Aucun message</p></div>';
            return;
        }

        // Grouper les messages par date et ajouter des séparateurs
        let html = '';
        let lastDate = null;

        for (const msg of messages) {
            const msgDate = new Date(msg.created_at || msg.timestamp);
            const dateKey = msgDate.toDateString();

            if (dateKey !== lastDate) {
                html += this.renderDateSeparator(msgDate);
                lastDate = dateKey;
            }

            html += await this.renderMessage(msg);
        }

        container.innerHTML = html;
        console.log('✅ Messages rendus dans le DOM');

        // Ajouter les écouteurs d'événements pour chaque message
        container.querySelectorAll('.message-bubble').forEach(bubble => {
            this.attachMessageListeners(bubble);
        });
    }

    /**
     * Rendre un séparateur de date
     */
    renderDateSeparator(date) {
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);

        let label;
        if (date.toDateString() === today.toDateString()) {
            label = "Aujourd'hui";
        } else if (date.toDateString() === yesterday.toDateString()) {
            label = "Hier";
        } else {
            label = date.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        return `
            <div class="date-separator">
                <span>${label}</span>
            </div>
        `;
    }

    /**
     * Rendre un message individuel
     */
    async renderMessage(msg) {
        // Vérifier is_sent_by_me OU comparer sender_id avec userId
        const isSent = msg.is_sent_by_me || (msg.sender_id === this.userId);
        const reactions = msg.reactions || [];
        // groupReactions accepte plusieurs formes (array ou object mapping)
        // et retourne un mapping emoji => {count, userReacted}
        const groupedReactions = this.groupReactions(reactions);
        const hasReactions = Object.keys(groupedReactions).length > 0;

        // Déchiffrer le contenu si nécessaire
        let decryptedContent = msg.content;
        if (this.encryptionService && msg.is_encrypted && !isSent) {
            try {
                const encryptedData = JSON.parse(msg.content);
                decryptedContent = await this.encryptionService.decryptMessage(encryptedData, msg.sender_id);
                console.log('🔓 Message déchiffré');
            } catch (error) {
                console.warn('⚠️ Échec du déchiffrement:', error);
                decryptedContent = '[Message chiffré - déchiffrement impossible]';
            }
        }

        // 🎵 Gestion spéciale pour les messages vocaux et audio
        let messageContent = '';
        console.log('🎵 [RENDER_MSG] Vérification message ID:', msg.id, 'type:', msg.type, 'content:', decryptedContent);
        console.log('🎵 [RENDER_MSG] Attachments:', msg.attachments);

        // Vérifier si c'est un message vocal (type voice/audio) OU si c'est un message avec attachment audio
        const hasVoiceType = msg.type === 'voice' || msg.type === 'audio';
        const hasAudioAttachment = msg.attachments && msg.attachments.some(att => att.file_type === 'audio');

        console.log('🎵 [RENDER_MSG] hasVoiceType:', hasVoiceType, 'hasAudioAttachment:', hasAudioAttachment);

        if (hasVoiceType || hasAudioAttachment) {
            console.log('🎤 [RENDER_MSG] Message vocal/audio détecté:', msg.id, 'type:', msg.type, 'hasAudioAttachment:', hasAudioAttachment);

            // Chercher l'attachment audio
            const audioAtt = msg.attachments?.find(att => att.file_type === 'audio' || att.mime_type?.startsWith('audio/'));

            console.log('🎵 [RENDER_MSG] Audio attachment trouvé:', audioAtt);

            if (audioAtt) {
                console.log('🎵 [RENDER_MSG] Rendu du player audio pour:', audioAtt.file_url, 'duration:', audioAtt.formatted_duration);
                messageContent = `
                    <div class="voice-message-container">
                        <div class="audio-player-wrapper">
                            <button class="audio-play-btn" onclick="window.messagingApp && window.messagingApp.toggleAudioPlayer(this)">
                                <span class="audio-icon">▶️</span>
                            </button>
                            <div class="audio-waveform">
                                <div class="waveform-bars">
                                    <span></span><span></span><span></span><span></span><span></span>
                                    <span></span><span></span><span></span><span></span><span></span>
                                    <span></span><span></span><span></span><span></span><span></span>
                                </div>
                            </div>
                            <div class="audio-duration">${audioAtt.formatted_duration || '0:00'}</div>
                            <audio preload="metadata" class="audio-player">
                                <source src="${audioAtt.file_url}" type="${audioAtt.mime_type}">
                            </audio>
                        </div>
                    </div>
                `;
            } else {
                console.log('⚠️ [RENDER_MSG] Pas d\'attachment audio trouvé pour message vocal/audio');
                // Fallback si pas d'attachment audio trouvé
                messageContent = `<p class="message-text">${this.parseMessageContent(msg.content || 'Message vocal')}</p>`;
            }
        } else {
            console.log('📝 [RENDER_MSG] Message texte normal');
            // Message texte normal
            messageContent = `<p class="message-text">${this.parseMessageContent(msg.content)}</p>`;
            // Ajouter les attachments si présents
            if (msg.attachments && msg.attachments.length > 0) {
                console.log('📎 [RENDER_MSG] Ajout des attachments:', msg.attachments.length);
                messageContent += this.renderAttachments(msg.attachments);
            }
        }

        return `
            <div class="message-bubble ${isSent ? 'sent' : 'received'}" data-message-id="${msg.id}">
                <div class="message-avatar">
                    ${msg.sender.avatar ? `<img src="/storage/${msg.sender.avatar}" alt="${msg.sender.name}">` : msg.sender.name.charAt(0)}
                </div>
                <div class="message-content-wrapper">
                    <div class="message-content ${msg.is_pinned ? 'pinned' : ''}">
                        ${msg.reply_to ? this.renderReplyTo(msg.reply_to) : ''}
                        ${messageContent}
                        <div class="message-footer">
                            <span class="message-time">${this.formatTime(msg.created_at || msg.timestamp)}</span>
                            ${isSent ? this.renderMessageStatus(msg) : ''}
                        </div>
                    </div>
                    ${hasReactions || true ? `
                        <div class="message-reactions">
                            ${hasReactions ? this.renderReactionsDisplay(reactions, msg.id) : ''}
                            <button class="add-reaction-btn" onclick="window.messagingApp.showReactionPicker(event, ${msg.id})">➕</button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    /**
     * Rendre une réponse à un message
     */
    renderReplyTo(replyMsg) {
        const senderName = replyMsg.sender_name || replyMsg.sender?.name || 'Utilisateur';
        return `
            <div class="message-reply-to">
                <strong>↩️ ${this.escapeHtml(senderName)}</strong>
                <p>${this.escapeHtml(replyMsg.content.substring(0, 50))}${replyMsg.content.length > 50 ? '...' : ''}</p>
            </div>
        `;
    }

    /**
     * Rendre les réactions (affichage uniquement)
     */
    renderReactionsDisplay(reactions, messageId) {
        const grouped = this.groupReactions(reactions);
        return Object.entries(grouped).map(([emoji, data]) => `
            <span class="reaction-item ${data.userReacted ? 'reacted-by-me' : ''}"
                  onclick="window.messagingApp.onReactionClick(event, ${messageId}, '${emoji}')">
                ${emoji} <span class="reaction-count">${data.count}</span>
            </span>
        `).join('');
    }

    /**
     * Rendre les réactions
     */
    renderReactions(reactions, messageId) {
        const grouped = this.groupReactions(reactions);
        return `
            <div class="message-reactions">
                ${Object.entries(grouped).map(([emoji, data]) => `
                    <span class="reaction-item ${data.userReacted ? 'reacted-by-me' : ''}"
                          onclick="messagingApp.toggleReaction(${messageId}, '${emoji}')">
                        ${emoji} <span class="reaction-count">${data.count}</span>
                    </span>
                `).join('')}
                <button class="add-reaction-btn" onclick="messagingApp.showReactionPicker(event, ${messageId})">➕</button>
            </div>
        `;
    }

    /**
     * Grouper les réactions par emoji
     */
    groupReactions(reactions) {
        const grouped = {};

        // Accept multiple shapes returned by server or stored locally:
        // 1) Array of reaction objects: [{emoji, user_id, user}, ...]
        // 2) Object mapping emoji => count (from backend getReactionCounts)
        // 3) Object mapping emoji => {count: x, users: [...]}

        if (!reactions) return grouped;

        // If reactions is an object mapping (not an array)
        if (!Array.isArray(reactions) && typeof reactions === 'object') {
            Object.entries(reactions).forEach(([emoji, value]) => {
                let count = 0;
                let userReacted = false;

                if (typeof value === 'number') {
                    count = value;
                } else if (typeof value === 'object') {
                    // Could be {count: x} or {count: x, users: [...]}
                    count = value.count ?? (Array.isArray(value) ? value.length : 0);
                    const users = value.users ?? [];
                    if (Array.isArray(users)) {
                        userReacted = users.some(u => (u?.id ?? u?.user_id) === this.userId);
                    }
                }

                grouped[emoji] = { count, userReacted };
            });

            return grouped;
        }

        // Otherwise expect an array of reaction records
        reactions.forEach(r => {
            const emoji = r.emoji;
            if (!grouped[emoji]) {
                grouped[emoji] = { count: 0, userReacted: false };
            }
            grouped[emoji].count++;
            if (r.user_id === this.userId || r.user?.id === this.userId) {
                grouped[emoji].userReacted = true;
            }
        });

        return grouped;
    }

    /**
     * Normaliser les réactions pour obtenir un tableau d'objets {emoji, user_id, user}
     * Accepte soit :
     *  - un tableau d'objets déjà
     *  - un objet mapping emoji => {count, users: [...]}
     *  - un objet mapping emoji => number
     */
    normalizeReactions(reactions) {
        if (!reactions) return [];

        if (Array.isArray(reactions)) return reactions;

        const out = [];

        // reactions is an object mapping
        Object.entries(reactions).forEach(([emoji, value]) => {
            if (Array.isArray(value)) {
                // array of users? unlikely, but handle
                value.forEach(u => out.push({ emoji, user_id: u?.id ?? u?.user_id, user: u }));
            } else if (typeof value === 'object') {
                const users = value.users ?? [];
                if (Array.isArray(users) && users.length > 0) {
                    users.forEach(u => out.push({ emoji, user_id: u?.id ?? u?.user_id, user: u }));
                } else {
                    // No user list available, but we may have a count; create placeholder entries (without user)
                    const count = value.count ?? 0;
                    for (let i = 0; i < count; i++) {
                        out.push({ emoji, user_id: null, user: null });
                    }
                }
            } else if (typeof value === 'number') {
                for (let i = 0; i < value; i++) {
                    out.push({ emoji, user_id: null, user: null });
                }
            }
        });

        return out;
    }

    /**
     * Rendre les pièces jointes
     */
    renderAttachments(attachments) {
        try {
            if (!attachments || !Array.isArray(attachments) || attachments.length === 0) return '';

            return attachments.map(att => {
                if ((att.mime_type || '').startsWith('image/')) {
                    return `<img src="${att.file_url}" class="attachment-image" alt="${this.escapeHtml(att.file_name)}" onclick="messagingApp.viewImage('${att.file_url}')">`;
                } else if ((att.mime_type || '').startsWith('audio/')) {
                    console.log('🎵 Rendu audio attachment:', att.file_url, att.mime_type, att.formatted_duration);
                    return `
                        <div class="message-attachment audio-attachment voice-message">
                            <div class="audio-player-wrapper">
                                <button class="audio-play-btn" onclick="this.nextElementSibling.play(); this.style.display='none'; this.nextElementSibling.style.display='block';" title="Lire le message vocal">
                                    <span class="audio-icon">�</span>
                                    <span class="play-text">Lire</span>
                                    ${att.formatted_duration ? `<span class="audio-duration-small">${att.formatted_duration}</span>` : ''}
                                </button>
                                <div class="audio-info">
                                    <div class="audio-title">Message vocal</div>
                                    <div class="audio-duration">${att.formatted_duration || '00:00'}</div>
                                </div>
                                <audio controls preload="none" class="audio-player" style="display:none;" onloadeddata="console.log('🎵 Audio loaded:', '${att.file_url}')" onerror="console.error('🎵 Audio error:', '${att.file_url}', event)" onended="this.style.display='none'; this.previousElementSibling.style.display='flex';">
                                    <source src="${att.file_url}" type="${att.mime_type}">
                                    Votre navigateur ne supporte pas la lecture audio.
                                </audio>
                            </div>
                        </div>
                    `;
                } else {
                    return `
                        <div class="message-attachment" onclick="window.open('${att.file_url}', '_blank')">
                            <span class="attachment-icon">${this.getFileIcon(att.mime_type || '')}</span>
                            <div class="attachment-info">
                                <div class="attachment-name">${this.escapeHtml(att.file_name)}</div>
                                <div class="attachment-size">${att.formatted_size || ''}</div>
                            </div>
                        </div>
                    `;
                }
            }).join('');
        } catch (err) {
            console.error('Erreur dans renderAttachments:', err);
            return '';
        }
    }

    /**
     * Rendre le statut du message
     */
    renderMessageStatus(msg) {
        if (msg.is_read) {
            return '<span class="message-status"><span class="status-check read">✓✓</span></span>';
        } else if (msg.is_delivered) {
            return '<span class="message-status"><span class="status-check delivered">✓✓</span></span>';
        } else {
            return '<span class="message-status"><span class="status-check">✓</span></span>';
        }
    }

    /**
     * Envoyer un message
     */
    async sendMessage() {
        if (this.isSending) return; // Empêcher les envois multiples

        const textarea = document.getElementById('messageTextarea');
        const content = textarea?.value.trim();

        // If a voice recording is currently active (recording or paused), stop it and prepare the blob
        if (this.voiceRecorder && (this.voiceRecorder.isRecording || this.voiceRecorder.isPaused)) {
            console.log('🎤 sendMessage: arrêt enregistrement actif avant envoi');
            await this.voiceRecorder.stopAndGetPendingAudio();
        }

        if (!content && this.selectedFiles.length === 0 && (!this.voiceRecorder || !this.voiceRecorder.getPendingAudio())) return;
        if (!this.currentConversation) {
            this.showNotification('Veuillez sélectionner une conversation', 'error');
            return;
        }

        this.isSending = true; // Activer le verrou
        const sendBtn = document.getElementById('sendMessageBtn');
        if (sendBtn) sendBtn.disabled = true; // Désactiver le bouton visuellement

        try {
            // Uploader d'abord les fichiers
            let attachmentIds = [];
            let messageType = 'text';

            if (this.selectedFiles.length > 0) {
                attachmentIds = await this.uploadFiles();
                messageType = 'file';
            }

            // Vérifier si c'est un message vocal
            let voiceUploadResult = null;
            if (this.voiceRecorder && this.voiceRecorder.getPendingAudio()) {
                voiceUploadResult = await this.voiceRecorder.uploadAudio();
                if (voiceUploadResult && voiceUploadResult.id) {
                    attachmentIds.push(voiceUploadResult.id);
                    messageType = 'voice';
                } else {
                    // Upload failed: inform user, cleanup UI and abort sending
                    this.showNotification('Erreur d\'envoi du message vocal', 'error');
                    if (this.voiceRecorder) {
                        try { this.voiceRecorder.cancelAudio(); } catch(e){}
                        try { this.voiceRecorder.stopRecording(); } catch(e){}
                    }
                    return;
                }
            }

            // Chiffrer le contenu si E2E est disponible
            let encryptedContent = content;
            let encryptionData = null;

            if (this.encryptionService && content) {
                try {
                    // Vérifier si on a déjà un secret partagé avec le destinataire
                    const recipientId = this.currentConversation.id;
                    if (!this.encryptionService.keys.has(recipientId)) {
                        // Établir une connexion E2E avec le destinataire
                        await this.establishE2EConnection(recipientId);
                    }

                    // Chiffrer le message
                    const encrypted = await this.encryptionService.encryptMessage(content, recipientId);
                    encryptedContent = JSON.stringify(encrypted);
                    encryptionData = {
                        is_encrypted: true,
                        key_id: encrypted.keyId
                    };
                    console.log('🔒 Message chiffré pour envoi');
                } catch (error) {
                    console.warn('⚠️ Échec du chiffrement E2E, envoi en clair:', error);
                    // Fallback: envoyer en clair
                }
            }

            // Envoyer le message
            const messageData = {
                receiver_id: this.currentConversation.id,
                content: encryptedContent || 'Message vocal',
                type: messageType,
                reply_to: this.replyTo,
                attachment_ids: attachmentIds,
                is_encrypted: !!encryptionData?.is_encrypted,
                key_id: encryptionData?.key_id
            };

            const response = await fetch('/api/messaging/send', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(messageData)
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Send error:', errorText);
                throw new Error('Erreur d\'envoi du message');
            }

            const result = await response.json();
            console.log('✅ Message envoyé:', result);

            // Ajouter le message à la liste (l'API retourne data.data)
            const newMessageData = result.data;

            // Ajouter is_sent_by_me pour l'affichage
            newMessageData.is_sent_by_me = true;
            newMessageData.is_read = false;
            newMessageData.is_delivered = false;
            newMessageData.reactions = [];
            // Si on a uploadé un attachment juste avant l'envoi, l'afficher immédiatement
            newMessageData.attachments = [];
            if (voiceUploadResult && voiceUploadResult.attachment) {
                // L'API d'upload renvoie formatted fields (file_url, formatted_duration...)
                newMessageData.attachments.push(voiceUploadResult.attachment);
            }

            this.addMessageToConversation(newMessageData);

            // Réinitialiser le formulaire
            textarea.value = '';
            this.cancelReply();
            this.clearSelectedFiles();
            this.updateCharCounter(0);
            this.scrollToBottom();

            // Nettoyer l'interface vocal si elle était utilisée
            if (this.voiceRecorder) {
                this.voiceRecorder.cancelAudio();
            }

            // Mettre à jour l'état du bouton d'envoi
            this.updateSendButtonState();

            // Pas de popup - le message s'affiche directement
            console.log('✅ Message envoyé avec succès');

        } catch (error) {
            console.error('❌ Erreur:', error);
            this.showNotification('Erreur d\'envoi du message', 'error');
            // Nettoyer l'interface vocal si quelque chose est resté
            if (this.voiceRecorder) {
                try { this.voiceRecorder.cancelAudio(); } catch(e){}
                try { this.voiceRecorder.stopRecording(); } catch(e){}
            }
        } finally {
            this.isSending = false; // Libérer le verrou
            if (sendBtn) sendBtn.disabled = false; // Réactiver le bouton
            this.updateSendButtonState(); // Mettre à jour l'état correct
        }
    }

    /**
     * Uploader les fichiers
     */
    async uploadFiles() {
        const ids = [];

        for (const file of this.selectedFiles) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('receiver_id', this.currentConversation.id);

            try {
                const response = await fetch('/api/messaging/upload', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${this.authToken}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) throw new Error('Erreur d\'upload');

                const data = await response.json();
                ids.push(data.attachment.id);
            } catch (error) {
                console.error('Erreur upload:', error);
            }
        }

        return ids;
    }

    /**
     * Ajouter un message à la conversation
     */
    async addMessageToConversation(message) {
        console.log('➕ Ajout du message à la conversation', this.currentConversation.id, message);

        if (!this.messages[this.currentConversation.id]) {
            this.messages[this.currentConversation.id] = [];
        }

        this.messages[this.currentConversation.id].push(message);
        console.log('📝 Nombre total de messages:', this.messages[this.currentConversation.id].length);

        await this.renderMessages();
    }

    /**
     * Toggle réaction
     */
    async toggleReaction(messageId, emoji) {
        // Optimistic UI: update locally first, then call API. Revert on error.
        try {
            const convId = this.currentConversation?.id;
            if (!convId) {
                console.warn('No conversation selected for reaction');
            }

            const convMsgs = this.messages[convId] || [];
            const msg = convMsgs.find(m => m.id === messageId);

            // Keep a shallow copy of previous reactions to revert if needed
            const prevReactions = msg ? (Array.isArray(msg.reactions) ? [...msg.reactions] : []) : [];

            if (msg) {
                // Normalize reactions to array shape so we can use findIndex reliably
                if (!Array.isArray(msg.reactions)) {
                    msg.reactions = this.normalizeReactions(msg.reactions);
                }

                const existingIndex = msg.reactions.findIndex(r => r.emoji === emoji && (r.user_id === this.userId || (r.user && r.user.id === this.userId)));
                if (existingIndex > -1) {
                    // User already reacted with this emoji -> remove
                    msg.reactions.splice(existingIndex, 1);
                    console.log('➖ Optimistically removed reaction', emoji, 'from message', messageId);
                } else {
                    // Add reaction optimistically
                    const reactionObj = { emoji, user_id: this.userId, user: { id: this.userId, name: this.userName } };
                    msg.reactions.push(reactionObj);
                    console.log('➕ Optimistically added reaction', emoji, 'to message', messageId);
                }

                // Re-render messages to show change immediately
                // Update only the message reactions in the DOM for a snappier UI
                this.updateMessageReactionsInDOM(messageId);
            }

            // Send API request
            const response = await fetch(`/api/messaging/messages/${messageId}/react`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ emoji })
            });

            if (!response.ok) {
                const errText = await response.text().catch(() => '');
                console.error('❌ Reaction API error', response.status, errText);
                throw new Error(errText || 'Erreur de réaction');
            }

            // If server returns updated reactions, use them
            try {
                const data = await response.json();
                if (data && data.reactions && msg) {
                    // Server returns mapping (emoji => {count, users}) — normalize to array to keep client logic consistent
                    msg.reactions = this.normalizeReactions(data.reactions);
                    this.updateMessageReactionsInDOM(messageId);
                }
            } catch (e) {
                // ignore JSON parse errors
            }

        } catch (error) {
            console.error('Erreur:', error);
            // Afficher le message d'erreur serveur si présent pour faciliter le debug
            const errMsg = (error && error.message) ? error.message : 'Erreur de réaction';
            this.showNotification('Erreur de réaction: ' + errMsg, 'error');

            // Revert optimistic update
            const convId = this.currentConversation?.id;
            const convMsgs = this.messages[convId] || [];
            const msg = convMsgs.find(m => m.id === messageId);
            if (msg) {
                msg.reactions = msg.reactions || [];
                // Try to refresh from server by reloading conversation
                try {
                    await this.loadConversation(convId);
                } catch (e) {
                    // fallback: no-op
                    console.error('Failed to reload conversation after reaction error', e);
                }
            }
        }
    }

    /**
     * Épingler un message
     */
    async pinMessage(messageId) {
        console.log('📌 Attempting to pin message:', messageId);
        try {
            const response = await fetch(`/api/messaging/messages/${messageId}/pin`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Accept': 'application/json'
                }
            });

            console.log('📡 Pin response status:', response.status);
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Pin error:', errorText);
                throw new Error('Erreur d\'épinglage');
            }

            const result = await response.json();
            console.log('✅ Pin successful:', result);
            this.showNotification('Message épinglé', 'success');
            await this.loadPinnedMessages(this.currentConversation.id);
            // Recharger les messages pour afficher l'indicateur épinglé
            await this.loadConversation(this.currentConversation.id);

        } catch (error) {
            console.error('❌ Pin error:', error);
            this.showNotification('Erreur d\'épinglage', 'error');
        }
    }

    /**
     * Désépingler un message
     */
    async unpinMessage(messageId) {
        try {
            const response = await fetch(`/api/messaging/messages/${messageId}/unpin`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Erreur de désépinglage');

            this.showNotification('Message désépinglé', 'success');
            await this.loadPinnedMessages(this.currentConversation.id);
            // Recharger les messages pour retirer l'indicateur épinglé
            await this.loadConversation(this.currentConversation.id);

        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    /**
     * Charger les messages épinglés
     */
    async loadPinnedMessages(userId) {
        try {
            const response = await fetch(`/api/messaging/conversation/${userId}/pinned`, {
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Erreur de chargement');

            const data = await response.json();
            console.log('📌 Messages épinglés reçus:', data);

            // Mettre à jour le badge
            const badge = document.getElementById('pinnedCount');
            if (badge) {
                badge.textContent = data.pinned_messages ? data.pinned_messages.length : 0;
            }

            // Afficher les messages épinglés dans le panneau
            const pinnedList = document.getElementById('pinnedMessagesList');
            if (pinnedList && data.pinned_messages) {
                if (data.pinned_messages.length === 0) {
                    pinnedList.innerHTML = '<div class="empty-state"><p>Aucun message épinglé</p></div>';
                } else {
                    pinnedList.innerHTML = data.pinned_messages.map(msg => `
                                <div class="pinned-message-item" data-message-id="${msg.id}" onclick="window.messagingApp.navigateToMessage(${msg.id})">
                                    <div class="pinned-message-content">
                                        <strong>${msg.sender.name}</strong>
                                        <p>${this.escapeHtml(msg.content)}</p>
                                        <span class="pinned-time">${this.formatTime(msg.created_at)}</span>
                                    </div>
                                    <button class="unpin-btn" onclick="event.stopPropagation(); messagingApp.unpinMessage(${msg.id})" title="Désépingler">✕</button>
                                </div>
                            `).join('');
                }
            }

        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    /**
     * Supprimer un message
     */
    async deleteMessage(messageId) {
        if (!confirm('Supprimer ce message ?')) return;

        // Trouver et supprimer immédiatement le message du DOM
        const messageElement = document.querySelector(`.message-bubble[data-message-id="${messageId}"]`);
        if (!messageElement) {
            console.error('Message element not found for deletion:', messageId);
            return;
        }

        // Sauvegarder le HTML du message pour pouvoir le restaurer en cas d'erreur
        const messageBackup = messageElement.outerHTML;

        // Supprimer immédiatement du DOM
        messageElement.remove();
        console.log('Message supprimé du DOM:', messageId);

        // Supprimer aussi du cache local des messages
        if (this.currentConversation && this.messages[this.currentConversation.id]) {
            const messageIndex = this.messages[this.currentConversation.id].findIndex(m => m.id === messageId);
            if (messageIndex !== -1) {
                this.messages[this.currentConversation.id].splice(messageIndex, 1);
                console.log('Message supprimé du cache local');
            }
        }

        try {
            // Faire l'appel API en arrière-plan
            const response = await fetch(`/api/messaging/messages/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Erreur de suppression');

            this.showNotification('Message supprimé', 'success');
            console.log('Message supprimé du serveur avec succès');

        } catch (error) {
            console.error('Erreur lors de la suppression:', error);

            // Restaurer le message en cas d'erreur
            const messagesContainer = document.getElementById('messagesList');
            if (messagesContainer && messageBackup) {
                // Créer un élément temporaire et l'insérer
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = messageBackup;
                const restoredMessage = tempDiv.firstElementChild;

                // Trouver où l'insérer (approximativement à la bonne position)
                const messages = messagesContainer.querySelectorAll('.message-bubble');
                if (messages.length > 0) {
                    messagesContainer.insertBefore(restoredMessage, messages[0]);
                } else {
                    messagesContainer.appendChild(restoredMessage);
                }

                console.log('Message restauré après erreur de suppression');
            }

            this.showNotification('Erreur lors de la suppression du message', 'error');

            // Restaurer aussi dans le cache local
            if (this.currentConversation && this.messages[this.currentConversation.id]) {
                // Recharger les messages pour être sûr
                await this.loadMessages(this.currentConversation.id);
            }
        }
    }

    /**
     * Indicateur de frappe
     */
    handleTyping() {
        if (!this.currentConversation) return;

        // Annuler le timeout précédent
        clearTimeout(this.typingTimeout);

        // Envoyer l'indicateur
        this.sendTypingIndicator(true);

        // Arrêter après 3 secondes
        this.typingTimeout = setTimeout(() => {
            this.sendTypingIndicator(false);
        }, 3000);
    }

    /**
     * Envoyer l'indicateur de frappe
     */
    async sendTypingIndicator(isTyping) {
        if (!this.currentConversation) return;

        try {
            await fetch('/api/messaging/typing', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    conversation_user_id: this.currentConversation.id,
                    is_typing: isTyping
                })
            });
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    /**
     * Configuration WebSocket
     */
    setupWebSocket() {
        console.log('🔌 [WEBSOCKET] Configuration WebSocket démarrée');
        console.log('🔌 [WEBSOCKET] Echo défini:', typeof Echo);

        if (typeof Echo === 'undefined') {
            console.log('🔌 [WEBSOCKET] Echo instance: non disponible');
            console.error('❌ [WEBSOCKET] Laravel Echo non disponible');
            return;
        }

        // Mettre à jour le token d'authentification Echo si disponible
        if (this.authToken) {
            console.log('🔑 [WEBSOCKET] Mise à jour du token d\'authentification Echo');
            if (Echo.connector && Echo.connector.options && Echo.connector.options.auth) {
                Echo.connector.options.auth.headers['Authorization'] = `Bearer ${this.authToken}`;
            }
        }

        console.log('🔌 [WEBSOCKET] Echo instance:', Echo);

        console.log('✅ [WEBSOCKET] Echo disponible, configuration des écouteurs...');

        // Écouter les nouveaux messages
        const channel = Echo.private(`user.${this.userId}`);
        console.log('🔌 [WEBSOCKET] Canal privé créé:', `user.${this.userId}`);

        channel.listen('.message.sent', (e) => {
                console.log('📨 [WEBSOCKET] Message event received (.message.sent):', e);
                this.handleNewMessage(e);
            })
            .listen('message.sent', (e) => {
                console.log('📨 [WEBSOCKET] Message event received (message.sent):', e);
                this.handleNewMessage(e);
            })
            .listen('MessageSent', (e) => {
                console.log('📨 [WEBSOCKET] Message event received (MessageSent):', e);
                this.handleNewMessage(e);
            })
            .listen('App\\Events\\MessageSent', (e) => {
                console.log('📨 [WEBSOCKET] Message event received (App\\Events\\MessageSent):', e);
                this.handleNewMessage(e);
            })
            .listen('MessageReactionChanged', (e) => {
                this.handleReactionChanged(e);
            })
            .listen('MessageDelivered', (e) => {
                this.handleMessageDelivered(e);
            })
            .listen('MessageDeleted', (e) => {
                this.handleMessageDeleted(e);
            })
            .listen('UserTyping', (e) => {
                this.handleUserTyping(e);
            });

        console.log('✅ [WEBSOCKET] Écouteurs privés configurés pour user.' + this.userId);

        // Test de connexion
        channel.subscribed(() => {
            console.log('🔌 [WEBSOCKET] Connexion WebSocket établie avec succès pour user.' + this.userId);
        }).error((error) => {
            console.error('❌ [WEBSOCKET] Erreur de connexion WebSocket:', error);
        });

        // Écouter les changements de statut
        console.log('🎯 [PRESENCE] Tentative de connexion au canal presence...');
        const presenceChannel = Echo.join('presence');
        console.log('🎯 [PRESENCE] Canal presence obtenu:', presenceChannel);
        Echo.join('presence')
            .here((users) => {
                console.log('🎉 [PRESENCE] here() appelé avec users:', users.map(u => ({id: u.id, name: u.name})));
                try {
                    // users is an array of user objects currently present
                    (users || []).forEach(u => {
                        console.log('👤 [PRESENCE] Utilisateur présent:', u.id, u.name);
                        this.onlineUsers.add(u.id);
                        this.updateUserStatus(u.id, 'online');
                    });
                    console.log('👥 [PRESENCE] Total utilisateurs en ligne après here:', this.onlineUsers.size);
                } catch (e) {
                    console.warn('Erreur traitement presence.here', e);
                }
            })
            .joining((user) => {
                console.log('➕ [PRESENCE] joining() appelé pour user:', user.id, user.name);
                this.onlineUsers.add(user.id);
                this.updateUserStatus(user.id, 'online');
                console.log('👥 [PRESENCE] Total utilisateurs en ligne après joining:', this.onlineUsers.size);
            })
            .leaving((user) => {
                console.log('➖ [PRESENCE] leaving() appelé pour user:', user.id, user.name);
                this.onlineUsers.delete(user.id);
                this.updateUserStatus(user.id, 'offline');
                console.log('👥 [PRESENCE] Total utilisateurs en ligne après leaving:', this.onlineUsers.size);
            });
    }

    /**
     * Gérer un nouveau message WebSocket
     */
    handleNewMessage(message) {
        console.log('📨 [NEW_MESSAGE] Nouveau message reçu:', message);
        console.log('📨 [NEW_MESSAGE] Current conversation:', this.currentConversation);
        console.log('📨 [NEW_MESSAGE] User ID:', this.userId);
        console.log('📨 [NEW_MESSAGE] Conversations loaded:', this.conversations.length);

        // Ajouter à la conversation si c'est la conversation active
        if (this.currentConversation &&
            (message.sender_id === this.currentConversation.id || message.receiver_id === this.currentConversation.id)) {
            this.addMessageToConversation(message);
            this.scrollToBottom();

            // Marquer comme délivré
            if (message.receiver_id === this.userId) {
                this.markAsDelivered(message.id);
            }
        }

        // Mettre à jour la conversation dans la liste (dernier message, compteur non lus)
        this.updateConversationInList(message);

        // 🔔 Déclencher les notifications push et sons pour les nouveaux messages reçus
        if (message.receiver_id === this.userId && message.sender_id !== this.userId) {
            this.triggerMessageNotification(message);
        }
    }

    /**
     * Mettre à jour une conversation dans la liste en temps réel
     */
    updateConversationInList(message) {
        // Déterminer l'ID de l'autre utilisateur dans la conversation
        const otherUserId = message.sender_id === this.userId ? message.receiver_id : message.sender_id;
        const isSentByMe = message.sender_id === this.userId;

        console.log('🔄 [UPDATE_CONV] Updating conversation for user:', otherUserId, 'message:', message.id);

        // Mettre à jour le tableau des conversations en mémoire
        let conversation = this.conversations.find(c => c.user_id === otherUserId);
        if (!conversation) {
            console.log('🔄 [UPDATE_CONV] Conversation not found in memory, creating new one for user:', otherUserId);
            // Créer une nouvelle conversation si elle n'existe pas
            conversation = {
                user_id: otherUserId,
                user_name: message.sender_name || message.receiver_name || `User ${otherUserId}`,
                user_avatar: null,
                user_status: 'offline',
                last_message: {
                    content: message.content,
                    created_at: message.created_at,
                    is_sent_by_me: isSentByMe
                },
                unread_count: 0
            };
            this.conversations.unshift(conversation); // Ajouter au début
            console.log('🔄 [UPDATE_CONV] New conversation created:', conversation);
        } else {
            // Mettre à jour les données de la conversation existante
            conversation.last_message = {
                content: message.content,
                created_at: message.created_at,
                is_sent_by_me: isSentByMe
            };
            console.log('🔄 [UPDATE_CONV] Existing conversation updated');
        }

        // Incrémenter le compteur non lus si nécessaire
        if (message.receiver_id === this.userId && otherUserId !== this.currentConversation?.id) {
            conversation.unread_count = (conversation.unread_count || 0) + 1;
            console.log('🔄 [UPDATE_CONV] Unread count incremented to:', conversation.unread_count);
        }

        // Re-rendre la liste des conversations pour refléter les changements
        this.renderConversations();
        console.log('🔄 [UPDATE_CONV] Conversations re-rendered');
    }

    /**
     * Gérer changement de réaction
     */
    async handleReactionChanged(data) {
        // Rafraîchir le message
        await this.renderMessages();
    }

    /**
     * Déclencher les notifications pour un nouveau message
     */
    triggerMessageNotification(message) {
        try {
            console.log('🔔 triggerMessageNotification appelée pour message:', message.id);

            // Vérifier les permissions de notification
            console.log('🔔 Permission Notification:', Notification.permission);
            console.log('🔔 Notification supportée:', 'Notification' in window);

            // Vérifier si la fenêtre est active
            const isWindowActive = !document.hidden && document.hasFocus();

            console.log('🔔 État de la fenêtre - hidden:', document.hidden, 'hasFocus:', document.hasFocus(), 'isWindowActive:', isWindowActive);
            console.log('🔔 Nouveau message reçu:', {
                sender: message.sender_name,
                content: message.content?.substring(0, 50) + '...',
                isWindowActive: isWindowActive
            });

            // Vérifier si pushNotificationManager existe
            console.log('🔔 window.pushNotificationManager:', window.pushNotificationManager);
            if (window.pushNotificationManager) {
                console.log('🔔 pushNotificationManager.isEnabled:', window.pushNotificationManager.isEnabled);
                console.log('🔔 pushNotificationManager.soundEnabled:', window.pushNotificationManager.soundEnabled);
                console.log('🔔 pushNotificationManager.isGranted:', window.pushNotificationManager.isGranted);
            }

            // Si la fenêtre n'est pas active, déclencher les notifications
            if (!isWindowActive) {
                // Notification push du navigateur
                if (window.pushNotificationManager && window.pushNotificationManager.isEnabled) {
                    console.log('🔔 Déclenchement de la notification push...');
                    const title = `Nouveau message de ${message.sender_name}`;
                    const body = message.content || 'Message vocal';
                    window.pushNotificationManager.showNotification(title, body);
                    console.log('✅ Notification push déclenchée');
                } else {
                    console.log('❌ Notification push non déclenchée - manager non prêt ou désactivé');
                }

                // Son de notification
                if (window.pushNotificationManager && window.pushNotificationManager.soundEnabled) {
                    console.log('🔔 Déclenchement du son...');
                    window.pushNotificationManager.playSound();
                    console.log('✅ Son déclenché');
                } else {
                    console.log('❌ Son non déclenché - manager non prêt ou sons désactivés');
                }

                console.log('✅ Notification complète déclenchée pour le message');
            } else {
                console.log('ℹ️ Fenêtre active, pas de notification');
            }
        } catch (error) {
            console.error('❌ Erreur lors du déclenchement de la notification:', error);
        }
    }

    /**
     * Gérer message délivré
     */
    async handleMessageDelivered(data) {
        // Mettre à jour le statut du message
        const messages = this.messages[this.currentConversation?.id] || [];
        const msg = messages.find(m => m.id === data.message_id);
        if (msg) {
            msg.is_delivered = true;
            msg.delivered_at = data.delivered_at;
            await this.renderMessages();
        }
    }

    /**
     * Gérer message supprimé
     */
    async handleMessageDeleted(data) {
        // Supprimer le message de la liste
        if (this.messages[this.currentConversation?.id]) {
            this.messages[this.currentConversation.id] =
                this.messages[this.currentConversation.id].filter(m => m.id !== data.message_id);
            await this.renderMessages();
        }
    }

    /**
     * Gérer indicateur de frappe
     */
    handleUserTyping(data) {
        if (this.currentConversation && data.user_id === this.currentConversation.id) {
            const indicator = document.getElementById('typingIndicator');
            if (indicator) {
                indicator.style.display = data.is_typing ? 'block' : 'none';
                if (data.is_typing) {
                    indicator.textContent = `${data.user_name} est en train d'écrire...`;
                }
            }
        }
    }

    /**
     * Marquer comme délivré
     */
    async markAsDelivered(messageId) {
        try {
            await fetch(`/api/messaging/messages/${messageId}/delivered`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Accept': 'application/json'
                }
            });
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    /**
     * Utilitaires UI
     */

    showChatArea() {
        const emptyState = document.getElementById('emptyState');
        const chatHeader = document.getElementById('chatHeader');
        const messagesContainer = document.getElementById('messagesContainer');
        const messageInput = document.getElementById('messageInput');

        if (emptyState) emptyState.style.display = 'none';
        if (chatHeader) chatHeader.style.display = 'flex';
        if (messagesContainer) messagesContainer.style.display = 'flex';
        if (messageInput) messageInput.style.display = 'block';

        console.log('👁️ Zone de chat affichée');
    }

    showLoadingState() {
        const container = document.getElementById('messagesList');
        if (container) {
            container.innerHTML = '<div class="loading-state"><div class="spinner"></div><p>Chargement...</p></div>';
        }
    }

    scrollToBottom() {
        // Attendre que le DOM soit mis à jour
        setTimeout(() => {
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
                console.log('⬇️ Scroll vers le bas:', container.scrollHeight);
            }
        }, 100);
    }

    toggleEmojiPicker() {
        const picker = document.getElementById('emojiPicker');
        if (!picker) return;

        const isHidden = picker.style.display === 'none';
        picker.style.display = isHidden ? 'block' : 'none';

        // Initialiser les emojis si première ouverture
        if (isHidden && !picker.dataset.initialized) {
            this.initEmojiPicker();
            picker.dataset.initialized = 'true';
        }
    }

    initEmojiPicker() {
        const emojis = {
            smileys: ['😀','😁','😂','🤣','😃','😄','😅','😆','😉','😊','😋','😎','😍','😘','🥰','😗','😙','😚','☺️','🙂','🤗','🤩','🤔','🤨','😐','😑','😶','🙄','😏','😣','😥','😮','🤐','😯','😪','😫','😴','😌','😛','😜','😝','🤤','😒','😓','😔','😕','🙃','🤑','😲','☹️','🙁','😖','😞','😟','😤','😢','😭','😦','😧','😨','😩','🤯','😬','😰','😱','🥵','🥶','😳','🤪','😵','😡','😠','🤬','😷','🤒','🤕','🤢','🤮','🤧','😇','🤠','🤡','🥳','🥴','🥺','🤥','🤫','🤭','🧐','🤓'],
            gestures: ['👋','🤚','🖐️','✋','🖖','👌','🤌','🤏','✌️','🤞','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','👍','👎','✊','👊','🤛','🤜','👏','🙌','👐','🤲','🤝','🙏','✍️','💅','🤳','💪','🦾','🦿','🦵','🦶','👂','🦻','👃','🧠','🫀','🫁','🦷','🦴','👀','👁️','👅','👄'],
            animals: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐽','🐸','🐵','🙈','🙉','🙊','🐒','🐔','🐧','🐦','🐤','🐣','🐥','🦆','🦅','🦉','🦇','🐺','🐗','🐴','🦄','🐝','🪱','🐛','🦋','🐌','🐞','🐜','🪰','🪲','🪳','🦟','🦗','🕷️','🕸️','🦂','🐢','🐍','🦎','🦖','🦕','🐙','🦑','🦐','🦞','🦀','🐡','🐠','🐟','🐬','🐳','🐋','🦈','🐊','🐅','🐆','🦓','🦍','🦧','🦣','🐘','🦛','🦏','🐪','🐫','🦒','🦘','🦬','🐃','🐂','🐄','🐎','🐖','🐏','🐑','🦙','🐐','🦌','🐕','🐩','🦮','🐕‍🦺','🐈','🐈‍⬛','🪶','🐓','🦃','🦤','🦚','🦜','🦢','🦩','🕊️','🐇','🦝','🦨','🦡','🦫','🦦','🦥','🐁','🐀','🐿️','🦔'],
            food: ['🍕','🍔','🍟','🌭','🍿','🧈','🥓','🥚','🍳','🧇','🥞','🧈','🍞','🥐','🥨','🥯','🥖','🧀','🥗','🥙','🥪','🌮','🌯','🫔','🥫','🍝','🍜','🍲','🍛','🍣','🍱','🥟','🦪','🍤','🍙','🍚','🍘','🍥','🥠','🥮','🍢','🍡','🍧','🍨','🍦','🥧','🧁','🍰','🎂','🍮','🍭','🍬','🍫','🍿','🍩','🍪','🌰','🥜','🍯','🥛','🍼','☕','🍵','🧃','🥤','🧋','🍶','🍺','🍻','🥂','🍷','🥃','🍸','🍹','🧉','🍾','🧊','🥄','🍴','🍽️','🥣','🥡','🥢'],
            travel: ['✈️','🛫','🛬','🪂','💺','🚁','🚟','🚠','🚡','🛰️','🚀','🛸','🚂','🚃','🚄','🚅','🚆','🚇','🚈','🚉','🚊','🚝','🚞','🚋','🚌','🚍','🚎','🚐','🚑','🚒','🚓','🚔','🚕','🚖','🚗','🚘','🚙','🛻','🚚','🚛','🚜','🏎️','🏍️','🛵','🦽','🦼','🛴','🚲','🛹','🛼','🚏','🛣️','🛤️','🛢️','⛽','🚨','🚥','🚦','🛑','🚧','⚓','⛵','🛶','🚤','🛳️','⛴️','🛥️','🚢','🏖️','🏝️','🗺️','🗾','🧳','⛰️','🏔️','🗻','🏕️','🏖️','🏜️','🏝️','🏞️','🏟️','🏛️','🏗️','🧱','🪨','🪵','🛖'],
            objects: ['⚽','🏀','🏈','⚾','🥎','🎾','🏐','🏉','🥏','🎱','🪀','🏓','🏸','🏒','🏑','🥍','🏏','🪃','🥅','⛳','🪁','🏹','🎣','🤿','🥊','🥋','🎽','🛹','🛼','🛷','⛸️','🥌','🎿','⛷️','🏂','🪂','🏋️','🤼','🤸','⛹️','🤺','🤾','🏌️','🏇','🧘','🏄','🏊','🤽','🚣','🧗','🚵','🚴','🏆','🥇','🥈','🥉','🏅','🎖️','🏵️','🎗️','🎫','🎟️','🎪','🤹','🎭','🩰','🎨','🎬','🎤','🎧','🎼','🎹','🥁','🪘','🎷','🎺','🪗','🎸','🪕','🎻','🎲','♟️','🎯','🎳','🎮','🎰','🧩'],
            symbols: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','☮️','✝️','☪️','🕉️','☸️','✡️','🔯','🕎','☯️','☦️','🛐','⛎','♈','♉','♊','♋','♌','♍','♎','♏','♐','♑','♒','♓','🆔','⚛️','🉑','☢️','☣️','📴','📳','🈶','🈚','🈸','🈺','🈷️','✴️','🆚','💮','🉐','㊙️','㊗️','🈴','🈵','🈹','🈲','🅰️','🅱️','🆎','🆑','🅾️','🆘','❌','⭕','🛑','⛔','📛','🚫','💯','💢','♨️','🚷','🚯','🚳','🚱','🔞','📵','🚭','❗','❕','❓','❔','‼️','⁉️','🔅','🔆','〽️','⚠️','🚸','🔱','⚜️','🔰','♻️','✅','🈯','💹','❇️','✳️','❎','🌐','💠']
        };

        const grid = document.getElementById('emojiGrid');
        if (!grid) return;

        // Afficher les emojis smileys par défaut
        this.renderEmojis(emojis.smileys);

        // Gérer les catégories
        document.querySelectorAll('.emoji-categories button').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const category = e.target.dataset.category;
                if (category && emojis[category]) {
                    this.renderEmojis(emojis[category]);
                }
            });
        });

        // Gérer la recherche
        document.getElementById('emojiSearch')?.addEventListener('input', (e) => {
            const search = e.target.value.toLowerCase();
            if (!search) {
                this.renderEmojis(emojis.smileys);
                return;
            }

            const allEmojis = Object.values(emojis).flat();
            this.renderEmojis(allEmojis);
        });
    }

    renderEmojis(emojiList) {
        const grid = document.getElementById('emojiGrid');
        if (!grid) return;

        grid.innerHTML = emojiList.map(emoji =>
            `<button class="emoji-btn" data-emoji="${emoji}">${emoji}</button>`
        ).join('');

        // Ajouter les listeners pour insérer l'emoji
        grid.querySelectorAll('.emoji-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const emoji = e.target.dataset.emoji;
                const picker = document.getElementById('emojiPicker');

                // Véifier le mode du picker
                const mode = picker?.dataset.mode;
                const messageId = picker?.dataset.messageId;

                if (mode === 'reaction' && messageId) {
                    // Mode réaction : soit remplacer la réaction existante, soit toggler
                    console.log('➕ Selected reaction emoji:', emoji, 'for message:', messageId);
                    // Use replaceReaction which will decide to replace or toggle
                    this.replaceReaction(parseInt(messageId), emoji);
                    picker.style.display = 'none';
                    picker.dataset.mode = '';
                    picker.dataset.messageId = '';
                } else {
                    // Mode normal : insérer dans le textarea
                    const textarea = document.getElementById('messageTextarea');
                    if (textarea) {
                        textarea.value += emoji;
                        textarea.focus();
                    }
                    // Fermer le picker
                    picker.style.display = 'none';
                }
            });
        });
    }

    /**
     * Remplacer la réaction existante par une nouvelle (si l'utilisateur a déjà réagi),
     * ou toggler la nouvelle réaction si aucun ancien n'existe.
     */
    async replaceReaction(messageId, newEmoji) {
        try {
            const convId = this.currentConversation?.id;
            const convMsgs = this.messages[convId] || [];
            const msg = convMsgs.find(m => m.id === messageId);

            let existing = null;
            if (msg) {
                if (!Array.isArray(msg.reactions)) msg.reactions = this.normalizeReactions(msg.reactions);
                existing = msg.reactions.find(r => r.user_id === this.userId || (r.user && r.user.id === this.userId));
            }

            if (existing && existing.emoji && existing.emoji !== newEmoji) {
                // Optimistic replace locally
                msg.reactions = msg.reactions.filter(r => !(r.user_id === this.userId || (r.user && r.user.id === this.userId)));
                msg.reactions.push({ emoji: newEmoji, user_id: this.userId, user: { id: this.userId, name: this.userName } });
                this.updateMessageReactionsInDOM(messageId);

                // Ask server to replace (server will delete existing and create new when replace=true)
                const response = await fetch(`/api/messaging/messages/${messageId}/react`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${this.authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ emoji: newEmoji, replace: true })
                });

                if (!response.ok) {
                    const errText = await response.text().catch(() => '');
                    console.error('❌ Replace Reaction API error', response.status, errText);
                    throw new Error(errText || 'Erreur de remplacement de réaction');
                }

                try {
                    const data = await response.json();
                    if (data && data.reactions && msg) {
                        msg.reactions = this.normalizeReactions(data.reactions);
                        this.updateMessageReactionsInDOM(messageId);
                    }
                } catch (e) {
                    // ignore JSON parse errors
                }

            } else {
                // No existing different reaction -> toggle as before
                this.toggleReaction(messageId, newEmoji);
            }

        } catch (error) {
            console.error('Erreur:', error);
            const errMsg = (error && error.message) ? error.message : 'Erreur de réaction';
            this.showNotification('Erreur de réaction: ' + errMsg, 'error');

            // Fallback: reload conversation to sync state
            try {
                await this.loadConversation(this.currentConversation.id);
            } catch (e) {
                console.error('Failed to reload conversation after replace error', e);
            }
        }
    }

    loadEmojisInPicker() {
        const picker = document.getElementById('emojiPicker');
        if (!picker || picker.dataset.initialized === 'true') return;

        this.initEmojiPicker();
        picker.dataset.initialized = 'true';
    }

    toggleSearch() {
        console.log('🔄 toggleSearch appelé');
        const panel = document.getElementById('searchInChatPanel');
        console.log('📋 Panel trouvé:', !!panel);
        if (panel) {
            panel.classList.toggle('hidden');
            console.log('📋 Classe hidden toggled, visible:', !panel.classList.contains('hidden'));
        }
    }

    closeSearch() {
        console.log('🔄 closeSearch appelé');
        const panel = document.getElementById('searchInChatPanel');
        if (panel) {
            panel.classList.add('hidden');
            console.log('📋 Panel masqué');

            // Réinitialiser l'input de recherche
            const input = document.getElementById('searchInChat');
            if (input) {
                input.value = '';
                // déclencher un évènement input pour que tout listener réagisse
                try { input.dispatchEvent(new Event('input', { bubbles: true })); } catch (e) { /* noop */ }
            }

            // Réinitialiser l'état interne de recherche
            this._lastSearchQuery = '';
            this._lastSearchPage = 1;
            this._lastSearchTotal = 0;
            this._lastSearchLastPage = 0;

            // Vider les résultats affichés (header, liste, pager)
            const resultsList = panel.querySelector('.search-results');
            if (resultsList) resultsList.innerHTML = '';
            const header = panel.querySelector('.search-results-header');
            if (header) header.innerHTML = '';
            const pager = panel.querySelector('.search-results-pager');
            if (pager) pager.innerHTML = '';
        }
    }

    togglePinned() {
        const panel = document.getElementById('pinnedMessagesPanel');
        if (panel) {
            panel.style.display = (panel.style.display === 'block') ? 'none' : 'block';
        }
    }

    closePinned() {
        const panel = document.getElementById('pinnedMessagesPanel');
        if (panel) {
            panel.style.display = 'none';
        }
    }

    cancelReply() {
        this.replyTo = null;
        const replyingTo = document.getElementById('replyingTo');
        if (replyingTo) {
            replyingTo.style.display = 'none';
        }
    }

    updateCharCounter(count) {
        const counter = document.getElementById('charCounter');
        if (counter) {
            counter.textContent = `${count}/5000`;
        }
    }

    showNotification(message, type = 'info') {
        // Notification non-bloquante - peut être amélioré avec une bibliothèque
        console.log(`🔔 Notification ${type}:`, message);
        // Pour l'instant, juste un log - peut être remplacé par une vraie notification
    }

    /**
     * Utilitaires de formatage
     */

    formatTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        // Toujours afficher l'heure au format HH:MM (style WhatsApp)
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    getStatusText(status) {
        const statuses = {
            online: 'En ligne',
            away: 'Absent',
            busy: 'Occupé',
            offline: 'Hors ligne'
        };
        return statuses[status] || 'Inconnu';
    }

    parseMessageContent(content) {
        // Échapper HTML
        let parsed = this.escapeHtml(content);

        // Convertir URLs en liens
        parsed = parsed.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');

        // Convertir retours à la ligne
        parsed = parsed.replace(/\n/g, '<br>');

        return parsed;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    getFileIcon(mimeType) {
        if (mimeType.startsWith('image/')) return '🖼️';
        if (mimeType.startsWith('video/')) return '🎥';
        if (mimeType.startsWith('audio/')) return '🎵';
        if (mimeType.includes('pdf')) return '📄';
        if (mimeType.includes('word')) return '📝';
        if (mimeType.includes('excel')) return '📊';
        return '📎';
    }

    autoExpandTextarea() {
        const textarea = document.getElementById('messageTextarea');
        if (!textarea) return;

        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }

    // Placeholder pour fonctions supplémentaires
    showContextMenu(e, messageId) {
        console.log('📋 Showing context menu for message:', messageId);
        e.preventDefault();
        const menu = document.getElementById('messageContextMenu');
        if (!menu) return;

        // Trouver l'élément du message pour positionner le menu près de lui
        const messageElement = document.querySelector(`.message-bubble[data-message-id="${messageId}"]`);
        if (!messageElement) {
            console.error('❌ Message element not found for ID:', messageId);
            return;
        }

        const rect = messageElement.getBoundingClientRect();

        console.log('📍 Message rect:', rect);
        console.log('📐 Viewport:', window.innerWidth, 'x', window.innerHeight);

        // Dimensions approximatives du menu (on peut les ajuster)
        const menuWidth = 180;
        const menuHeight = 200;

        // Déterminer si c'est un message envoyé ou reçu
        const isSent = messageElement.classList.contains('sent');

        // Positionner le menu toujours en dessous du message, bien aligné
        // Utiliser getBoundingClientRect qui donne les coordonnées par rapport au viewport
        let top = rect.bottom + 5; // 5px en dessous du message
        let left = rect.left + (rect.width / 2) - (menuWidth / 2); // Centré sur le message

        // S'assurer que le menu ne sorte pas de l'écran à droite
        const viewportWidth = window.innerWidth;
        if (left + menuWidth > viewportWidth - 10) {
            left = viewportWidth - menuWidth - 10;
        }

        // S'assurer que le menu ne sorte pas de l'écran à gauche
        if (left < 10) {
            left = 10;
        }

        // Si le menu sortirait en bas, le positionner au-dessus
        const viewportHeight = window.innerHeight;
        if (top + menuHeight > viewportHeight - 10) {
            top = rect.top - menuHeight - 5; // Au-dessus du message
        }

        console.log('📨 Message type:', isSent ? 'sent' : 'received', '- final position - top:', top, 'left:', left);

        menu.style.display = 'block';
        menu.style.top = `${top}px`;
        menu.style.left = `${left}px`;
        menu.dataset.messageId = messageId;
        menu.dataset.messageId = messageId;

        // Vérifier si le message est épinglé
        const conversationMessages = this.messages[this.currentConversation.id] || [];
        console.log('📚 Messages in conversation:', conversationMessages.length, '- looking for:', messageId);
        const msg = conversationMessages.find(m => m.id === messageId);
        console.log('📝 Message found for menu:', !!msg, msg?.is_pinned, '- sender_id:', msg?.sender?.id, '- userId:', this.userId);
        const isPinned = msg && msg.is_pinned;

        // Afficher le bon bouton selon l'état du message
        const pinButton = document.getElementById('pinButton');
        const unpinButton = document.getElementById('unpinButton');

        if (isPinned) {
            pinButton.style.display = 'none';
            unpinButton.style.display = 'block';
            console.log('📌 Showing unpin button');
        } else {
            pinButton.style.display = 'block';
            unpinButton.style.display = 'none';
            console.log('📌 Showing pin button');
        }

        // Gestionnaires pour toutes les actions
        const buttons = menu.querySelectorAll('[data-action]');
        buttons.forEach(btn => {
            btn.onclick = () => {
                const action = btn.dataset.action;
                menu.style.display = 'none';

                switch(action) {
                    case 'reply':
                        this.startReply(messageId);
                        break;
                    case 'pin':
                        this.pinMessage(messageId);
                        break;
                    case 'unpin':
                        this.unpinMessage(messageId);
                        break;
                    case 'copy':
                        const msg = this.messages[this.currentConversation.id]?.find(m => m.id === messageId);
                        if (msg) navigator.clipboard.writeText(msg.content);
                        break;
                    case 'delete':
                        this.deleteMessage(messageId);
                        break;
                }
            };
        });
    }

    showReactionPicker(e, messageId) {
        e.stopPropagation();
        console.log('🎭 Opening reaction picker for message:', messageId);

        // Utiliser le picker d'emojis principal avec tous les emojis
        const picker = document.getElementById('emojiPicker');
        if (!picker) {
            console.error('❌ Emoji picker not found');
            return;
        }

        // Marquer que c'est pour une réaction (pas pour le textarea)
        picker.dataset.mode = 'reaction';
        picker.dataset.messageId = messageId;

        // Appliquer styles d'affichage (retour à l'aspect normal)
        picker.style.cssText = `
            display: flex;
            flex-direction: column;
            position: fixed;
            z-index: 99999;
            width: 350px;
            max-height: 400px;
            background: rgba(30, 30, 46, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            visibility: visible;
            opacity: 1;
            pointer-events: auto;
        `;

        // Positionner le picker sous le bouton ➕
        const btnRect = e.target.getBoundingClientRect();
        const pickerWidth = 350;
        const pickerHeight = 400;

        let top = btnRect.bottom + 8;
        let left = btnRect.left - (pickerWidth / 2) + (btnRect.width / 2);

        // Ajustements pour rester dans l'écran
        if (left < 10) left = 10;
        if (left + pickerWidth > window.innerWidth - 10) {
            left = window.innerWidth - pickerWidth - 10;
        }
        if (top + pickerHeight > window.innerHeight - 10) {
            top = btnRect.top - pickerHeight - 8;
        }

        picker.style.top = `${top}px`;
        picker.style.left = `${left}px`;

        console.log('📍 Picker positioned at:', {
            top,
            left,
            btnRect,
            pickerRect: picker.getBoundingClientRect(),
            pickerDisplay: window.getComputedStyle(picker).display,
            pickerVisibility: window.getComputedStyle(picker).visibility
        });

        // Charger les emojis si pas déjà fait
        this.loadEmojisInPicker();

        // Fermer le picker si on clique ailleurs
        const closePickerHandler = (event) => {
            if (!picker.contains(event.target) && event.target !== e.target) {
                picker.style.display = 'none';
                document.removeEventListener('click', closePickerHandler);
            }
        };
        setTimeout(() => {
            document.addEventListener('click', closePickerHandler);
        }, 100);
    }
    triggerFileUpload() { document.getElementById('fileInput')?.click(); }
    handleFileSelect(e) { this.selectedFiles = Array.from(e.target.files); }
    clearSelectedFiles() { this.selectedFiles = []; }
    /**
     * Toggle enregistrement vocal
     */
    toggleVoiceRecording() {
        if (this.voiceRecorder) {
            this.voiceRecorder.toggleRecording();
        } else {
            console.warn('VoiceRecorder non initialisé');
        }
    }
    filterEmojis(query) { console.log('Filter emojis:', query); }
    filterConversations(query) { console.log('Filter conversations:', query); }
    searchMessages(query, page = 1) {
        console.log('🔍 Recherche messages:', query, 'page:', page);
        this._lastSearchQuery = query;
        this._lastSearchPage = page;

        if (!query || !query.trim()) {
            this.displaySearchResults([], { total: 0, current_page: 1, last_page: 0 }, query);
            return;
        }

        if (!this.currentConversation) {
            console.warn('❌ Aucune conversation active pour la recherche');
            return;
        }

        const perPage = 50;
        const url = `/api/messaging/conversation/${this.currentConversation.id}/search?q=${encodeURIComponent(query)}&page=${page}&per_page=${perPage}`;
        console.log('🌐 URL de recherche:', url);

        fetch(url, {
            headers: {
                'Authorization': `Bearer ${this.authToken}`,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('📡 Réponse API:', response.status);
            return response.json();
        })
        .then(data => {
            const results = data.results || [];
            const pagination = data.pagination || { total: results.length, current_page: page, last_page: page };
            console.log('✅ Résultats recherche:', results.length, 'pagination:', pagination);
            this._lastSearchTotal = pagination.total;
            this._lastSearchLastPage = pagination.last_page;
            this.displaySearchResults(results, pagination, query);
        })
        .catch(error => {
            console.error('❌ Erreur recherche:', error);
            this.displaySearchResults([], { total: 0, current_page: 1, last_page: 0 }, query);
        });
    }

    /**
     * Afficher les résultats de recherche dans le panneau
     */
    displaySearchResults(results, pagination = { total: 0, current_page: 1, last_page: 0 }, searchTerm = '') {
        const panel = document.getElementById('searchInChatPanel');
        if (!panel) return;

        // Créer ou mettre à jour la liste des résultats
        let resultsList = panel.querySelector('.search-results');
        if (!resultsList) {
            resultsList = document.createElement('div');
            resultsList.className = 'search-results';
            panel.appendChild(resultsList);
        }

        // Header avec total
        let header = panel.querySelector('.search-results-header');
        if (!header) {
            header = document.createElement('div');
            header.className = 'search-results-header';
            header.style.display = 'flex';
            header.style.justifyContent = 'space-between';
            header.style.alignItems = 'center';
            header.style.marginBottom = '8px';
            panel.insertBefore(header, resultsList);
        }

    // Afficher le nombre total d'occurrences si fourni par le backend, sinon utiliser le nombre de messages
    const totalOccurrences = (pagination && (pagination.total_occurrences || pagination.total_occurrences === 0)) ? pagination.total_occurrences : (pagination.total || 0);
    header.innerHTML = `<div class="search-total">${totalOccurrences} occurrence(s)</div>`;

        if (!results || results.length === 0) {
            resultsList.innerHTML = '<div class="no-results">Aucun résultat trouvé</div>';
            return;
        }

        // Afficher les résultats — n'afficher que des snippets contenant les occurrences
        const term = (typeof searchTerm === 'string' && searchTerm.length) ? searchTerm : (document.getElementById('searchInChat')?.value || '');
        if (!term) {
            resultsList.innerHTML = '<div class="no-results">Aucun résultat trouvé</div>';
            return;
        }

        const entries = [];
        results.forEach(result => {
            // essayer d'extraire des snippets contenant le terme
            const snippets = this.createSearchSnippets(result.content, term, 60, 3);

            // fallback : si aucun snippet, mais le contenu contient le terme, créer un extrait autour du premier index
            if (snippets.length === 0 && result.content && result.content.toLowerCase().includes(term.toLowerCase())) {
                const idx = result.content.toLowerCase().indexOf(term.toLowerCase());
                const s = Math.max(0, idx - 60);
                const e = Math.min(result.content.length, idx + term.length + 60);
                snippets.push(result.content.slice(s, e).trim());
            }

            if (snippets.length > 0) {
                const snippetHtml = snippets.map(s => `<div class="search-snippet">${this.highlightSearchTerm(s, term)}</div>`).join('<div class="ellipsis">…</div>');
                entries.push(`
            <div class="search-result-item" data-message-id="${result.id}" onclick="window.messagingApp.navigateToMessage(${result.id})">
                <div class="search-result-avatar">
                    ${result.sender.avatar ? `<img src="/storage/${result.sender.avatar}" alt="${result.sender.name}">` : result.sender.name.charAt(0)}
                </div>
                <div class="search-result-content">
                    <div class="search-result-sender">${result.sender.name} <small style="color:rgba(255,255,255,0.6);">• ${result.match_count || 1} occurrence(s)</small></div>
                    <div class="search-result-text">${snippetHtml}</div>
                    <div class="search-result-date">${this.formatTime(result.created_at)}</div>
                </div>
            </div>
        `);
            }
        });

        if (entries.length === 0) {
            resultsList.innerHTML = '<div class="no-results">Aucun résultat trouvé</div>';
        } else {
            resultsList.innerHTML = entries.join('');
        }

        // Pagination - bouton charger plus si pages restantes
        let pager = panel.querySelector('.search-results-pager');
        if (!pager) {
            pager = document.createElement('div');
            pager.className = 'search-results-pager';
            pager.style.marginTop = '8px';
            panel.appendChild(pager);
        }

        if (pagination.current_page < pagination.last_page) {
            pager.innerHTML = `<button class="icon-btn" id="loadMoreSearch">Charger plus</button>`;
            pager.querySelector('#loadMoreSearch').addEventListener('click', () => {
                const next = (this._lastSearchPage || 1) + 1;
                this.searchMessages(this._lastSearchQuery || document.getElementById('searchInChat')?.value || '', next);
            });
        } else {
            pager.innerHTML = '';
        }
    }

    /**
     * Mettre en évidence le terme recherché dans le texte
     */
    highlightSearchTerm(text, searchTerm) {
        if (!searchTerm) return text;

        const regex = new RegExp(`(${this.escapeRegex(searchTerm)})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    /**
     * Extraire des snippets autour des occurrences trouvées dans un texte.
     * Retourne un tableau de courtes phrases/segments contenant le terme recherché.
     */
    createSearchSnippets(text, term, radius = 60, maxSnippets = 3) {
        if (!text || !term) return [];
        const lower = String(text).toLowerCase();
        const needle = String(term).toLowerCase();
        const snippets = [];
        let startPos = 0;
        const maxSnippetLen = Math.max(120, radius * 2 + needle.length);

        // Trouver toutes les occurrences
        while (snippets.length < maxSnippets) {
            const idx = lower.indexOf(needle, startPos);
            if (idx === -1) break;

            // Chercher les bornes naturelles (ponctuation) autour de l'occurrence
            const before = text.lastIndexOf('.', idx);
            const beforeExcl = Math.max(before, text.lastIndexOf('!', idx), text.lastIndexOf('?', idx), text.lastIndexOf('\n', idx));
            const after = text.indexOf('.', idx + needle.length);
            const afterExcl = Math.min(after === -1 ? text.length : after, Math.min(
                text.indexOf('!', idx + needle.length) === -1 ? text.length : text.indexOf('!', idx + needle.length),
                text.indexOf('?', idx + needle.length) === -1 ? text.length : text.indexOf('?', idx + needle.length)
            ));

            let from;
            if (beforeExcl !== -1) {
                // partir après la ponctuation
                from = beforeExcl + 1;
            } else {
                from = Math.max(0, idx - radius);
            }

            let to;
            if (afterExcl !== -1 && afterExcl > idx) {
                to = afterExcl + 1;
            } else {
                to = Math.min(text.length, idx + needle.length + radius);
            }

            // Ne pas retourner un snippet trop long (éviter de renvoyer tout le fil)
            if ((to - from) > maxSnippetLen) {
                // recentrer autour de l'occurrence
                from = Math.max(0, idx - Math.floor(maxSnippetLen / 3));
                to = Math.min(text.length, from + maxSnippetLen);
            }

            let snippet = text.slice(from, to).trim();

            // Si le snippet ne contient pas le mot (cas bizarres), extraire un segment autour de l'index
            if (snippet.toLowerCase().indexOf(needle) === -1) {
                const s = Math.max(0, idx - radius);
                const e = Math.min(text.length, idx + needle.length + radius);
                snippet = text.slice(s, e).trim();
            }

            // Eviter les doublons
            if (!snippets.includes(snippet)) snippets.push(snippet);

            startPos = idx + needle.length;
        }

        // Si aucune snippet et que le terme est présent (rare), retourner un extrait central
        if (snippets.length === 0 && lower.indexOf(needle) !== -1) {
            const idx = lower.indexOf(needle);
            const s = Math.max(0, idx - radius);
            const e = Math.min(text.length, idx + needle.length + radius);
            snippets.push(text.slice(s, e).trim());
        }

        return snippets;
    }

    /**
     * Échapper les caractères spéciaux pour RegExp
     */
    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    /**
     * Naviguer vers un message trouvé
     */
    async navigateToMessage(messageId) {
        console.log('🎯 Navigation vers message:', messageId);

        // Fermer le panneau de recherche
        this.closeSearch();
        // Fermer le panneau des messages épinglés si ouvert
        try { this.closePinned(); } catch (e) { /* noop */ }

        // Trouver le message dans la liste actuelle
        const messageElement = document.querySelector(`.message-bubble[data-message-id="${messageId}"]`);

        if (messageElement) {
            // Le message est déjà chargé, faire défiler vers lui
            messageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Ajouter un highlight temporaire
            messageElement.classList.add('search-highlight');
            setTimeout(() => {
                messageElement.classList.remove('search-highlight');
            }, 3000);
        } else {
            // Le message n'est pas chargé, tenter de charger plus de pages plus anciennes jusqu'à le trouver
            console.log('📄 Message pas trouvé dans le DOM, chargement nécessaire');
            try {
                const convId = this.currentConversation?.id;
                if (!convId) return;

                this._convPagination = this._convPagination || {};
                const pagination = this._convPagination[convId] || { current_page: 1, total_pages: 1 };
                let nextPage = (pagination.current_page || 1) + 1;

                while (nextPage <= (pagination.total_pages || 1)) {
                    // afficher un loader minimal
                    this.showLoadingState();
                    const newPagination = await this.loadConversationPage(convId, nextPage, 50);
                    // arrêter le loader
                    this.hideLoadingState && this.hideLoadingState();

                    // mettre à jour pagination pour la prochaine itération
                    if (newPagination) {
                        this._convPagination[convId] = newPagination;
                        // si le message est maintenant dans le DOM, scroller vers lui
                        const el = document.querySelector(`.message-bubble[data-message-id="${messageId}"]`);
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            el.classList.add('search-highlight');
                            setTimeout(() => el.classList.remove('search-highlight'), 3000);
                            return;
                        }
                        nextPage = (this._convPagination[convId].current_page || nextPage) + 1;
                    } else {
                        break;
                    }
                }

                console.log('📄 Message introuvable après chargement des pages disponibles');
            } catch (e) {
                console.error('❌ Erreur lors du chargement automatique des pages:', e);
            }
        }
    }
    viewImage(url) { window.open(url, '_blank'); }
    updateUserStatus(userId, status) {
        if (!userId) return;
        try {
            // Mettre à jour l'ensemble local
            if (status === 'online') this.onlineUsers.add(userId);
            else this.onlineUsers.delete(userId);

            // Mettre à jour la conversation en mémoire si présente
            try {
                const conv = (this.conversations || []).find(c => c.user_id === userId);
                if (conv) conv.user_status = status;
            } catch (e) {}

            // Mettre à jour les éléments DOM correspondants
            // Helper to set classes compat : both `status-online` and `online` (some CSS uses one or the other)
            const applyStatusClasses = (el) => {
                if (!el) return;
                // remove previous status-* and plain status names
                el.classList.remove('status-online','status-offline','status-away','status-busy','online','offline','away','busy');
                // ensure base
                if (!el.classList.contains('status-badge')) el.classList.add('status-badge');
                el.classList.add(`status-${status}`);
                el.classList.add(status);
            };

            // 1) Badge dans la liste de conversations
            const convBadge = document.querySelector(`.conversation-item[data-user-id="${userId}"] .status-badge`);
            if (convBadge) {
                applyStatusClasses(convBadge);
                console.log('✅ [PRESENCE] Badge conversation mis à jour pour userId:', userId, 'classes:', convBadge.className);
            } else {
                console.warn('⚠️ [PRESENCE] Badge conversation non trouvé pour userId:', userId);
            }

            // 2) Badges dans listes d'utilisateurs (.user-item)
            document.querySelectorAll(`.user-item[data-user-id="${userId}"] .status-badge`).forEach(applyStatusClasses);

            // 3) Badge dans l'en-tête de chat si on est en conversation avec cet utilisateur
            const chatUserId = this.currentConversation?.id;
            if (chatUserId && parseInt(chatUserId) === parseInt(userId)) {
                const statusEl = document.getElementById('chatUserStatus');
                const statusTextEl = document.getElementById('chatUserStatusText');
                if (statusEl) {
                    if (status === 'online') {
                        // Online: green badge with "En ligne" text
                        statusEl.innerHTML = '';
                        statusEl.className = 'status-badge status-online online';
                        statusEl.title = 'En ligne';
                        if (statusTextEl) {
                            statusTextEl.textContent = 'En ligne';
                            statusTextEl.style.color = '#10b981';
                            console.log('✅ [PRESENCE] Header mis à jour - statusText: En ligne (vert)');
                        }
                    } else {
                        // Offline: red badge with "Hors ligne" text
                        statusEl.innerHTML = '';
                        statusEl.className = 'status-badge status-offline offline';
                        statusEl.title = 'Hors ligne';
                        if (statusTextEl) {
                            statusTextEl.textContent = 'Hors ligne';
                            statusTextEl.style.color = '#ef4444';
                            console.log('✅ [PRESENCE] Header mis à jour - statusText: Hors ligne (rouge)');
                        }
                    }
                    console.log('✅ [PRESENCE] Header statusEl classes:', statusEl.className);
                } else {
                    console.warn('⚠️ [PRESENCE] Élément chatUserStatus non trouvé');
                }
            }

            // 4) Tout autre badge explicite data-user-id
            document.querySelectorAll(`.status-badge[data-user-id="${userId}"]`).forEach(el => {
                applyStatusClasses(el);
                el.title = this.getStatusText(status);
            });

        } catch (e) {
            console.warn('updateUserStatus error', e);
        }
    }

    handleClickOutside(e) {
        // Fermer le menu contextuel si clic en dehors
        const contextMenu = document.getElementById('messageContextMenu');
        if (contextMenu && !contextMenu.contains(e.target)) {
            contextMenu.style.display = 'none';
        }

        // Fermer le picker d'emojis si clic en dehors
        const emojiPicker = document.getElementById('emojiPicker');
        const emojiBtn = document.getElementById('emojiPickerBtn');
        if (emojiPicker && !emojiPicker.contains(e.target) && e.target !== emojiBtn) {
            emojiPicker.style.display = 'none';
        }
    }

    attachMessageListeners(bubble) {
        // Ajouter les écouteurs d'événements sur chaque message
        const messageId = parseInt(bubble.dataset.messageId);
        const messageContent = bubble.querySelector('.message-content');

        console.log('🔗 Attaching listeners to message:', messageId, '- content found:', !!messageContent);

        if (messageContent) {
            // Clic droit pour ouvrir le menu contextuel
            messageContent.addEventListener('contextmenu', (e) => {
                console.log('🖱️ Right-click detected on message:', messageId);
                e.preventDefault();
                this.showContextMenu(e, messageId);
            });
        }

        // Double-clic pour répondre
        bubble.addEventListener('dblclick', () => {
            this.startReply(messageId);
        });
    }

    /**
     * Handler when user clicks on an existing reaction item.
     * If the user already reacted with this emoji, open the picker to change (replace),
     * otherwise toggle the reaction.
     */
    onReactionClick(e, messageId, emoji) {
        e.stopPropagation();
        const convId = this.currentConversation?.id;
        const msg = (this.messages[convId] || []).find(m => m.id === messageId);

        let userReactedHere = false;
        if (msg) {
            if (!Array.isArray(msg.reactions)) msg.reactions = this.normalizeReactions(msg.reactions);
            userReactedHere = msg.reactions.some(r => (r.emoji === emoji) && (r.user_id === this.userId || (r.user && r.user.id === this.userId)));
        }

        if (userReactedHere) {
            // Open the picker positioned on this element to allow replacing
            this.showReactionPicker(e, messageId);
        } else {
            // Normal toggle
            this.toggleReaction(messageId, emoji);
        }
    }

    /**
     * Commencer à répondre à un message
     */
    startReply(messageId) {
        console.log('🔄 Starting reply to:', messageId);

        // Trouver le message original
        const msg = this.messages[this.currentConversation?.id]?.find(m => m.id === messageId);
        console.log('📝 Message found:', !!msg);

        if (!msg) return;

        this.replyTo = messageId;
        console.log('✅ Reply set to:', this.replyTo);

        // Remplir les détails de la réponse
        const replyToUser = document.getElementById('replyToUser');
        const replyToContent = document.getElementById('replyToContent');
        const replyingTo = document.getElementById('replyingTo');

        if (replyToUser) {
            replyToUser.textContent = `↩️ ${msg.sender.name}`;
            console.log('👤 Reply user set');
        }
        if (replyToContent) {
            replyToContent.textContent = msg.content.length > 100 ? msg.content.substring(0, 100) + '...' : msg.content;
            console.log('💬 Reply content set');
        }
        if (replyingTo) {
            replyingTo.style.display = 'block';
            console.log('📋 Reply zone shown');
        }
    }

    /**
     * Mettre à jour l'état du bouton d'envoi
     */
    updateSendButtonState() {
        const textarea = document.getElementById('messageTextarea');
        const content = textarea?.value.trim() || '';
        const hasContent = content.length > 0;
        const hasFiles = this.selectedFiles.length > 0;
        // Considerer un enregistrement en cours ou en pause comme "hasVoice" afin de permettre
        // à l'utilisateur d'appuyer sur Envoyer pour stopper et envoyer immédiatement.
        const hasVoice = this.voiceRecorder && (
            !!this.voiceRecorder.getPendingAudio() || this.voiceRecorder.isRecording || this.voiceRecorder.isPaused
        );

        const sendBtn = document.getElementById('sendMessageBtn');
        if (sendBtn) {
            sendBtn.disabled = !hasContent && !hasFiles && !hasVoice;
        }
    }

    /**
     * Tronquer le texte de prévisualisation à une longueur maximale
     */
    truncatePreviewText(text, maxLength = 40) {
        if (!text || text.length <= maxLength) {
            return text;
        }

        // Essayer de couper aux mots (maximum 5 mots)
        const words = text.split(' ');
        if (words.length <= 5) {
            return text.length <= maxLength ? text : text.substring(0, maxLength - 3) + '...';
        }

        // Prendre les 5 premiers mots
        const truncatedWords = words.slice(0, 5).join(' ');

        // Si ça dépasse la longueur max, couper aux caractères
        if (truncatedWords.length > maxLength) {
            return text.substring(0, maxLength - 3) + '...';
        }

        return truncatedWords + '...';
    }
}

// Initialisation globale
let messagingApp = null;
