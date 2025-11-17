<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/messaging-modern.css') }}?v={{ time() }}&r={{ rand() }}">
    <!-- Présence utilisateur (pastilles) -->
    <link rel="stylesheet" href="{{ asset('css/user-presence.css') }}?v={{ time() }}&r={{ rand() }}">

    <!-- Scripts -->
    <script src="{{ asset('js/messaging-app.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <script src="{{ asset('js/voice-recorder.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <script src="{{ asset('js/link-preview.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <script src="{{ asset('js/user-presence.js') }}?v={{ time() }}&r={{ rand() }}"></script>

    <!-- Configuration Pusher (même si non utilisé, pour compatibilité) -->
    <script>
        window.PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key', '') }}";
        window.PUSHER_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}";
        window.LARAVEL_ECHO_AVAILABLE = typeof Echo !== 'undefined';

        // Configuration Laravel Echo
        if (window.LARAVEL_ECHO_AVAILABLE && window.PUSHER_APP_KEY) {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: window.PUSHER_APP_KEY,
                cluster: window.PUSHER_CLUSTER,
                forceTLS: false,
                wsHost: window.location.hostname,
                wsPort: 6001,
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'Authorization': `Bearer ${window.authToken || ''}`
                    }
                }
            });
        }
    </script>

    <!-- Header avec info -->
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div>
                <h1 style="color: white; font-size: 24px; font-weight: 700; margin-bottom: 4px;">
                    💬 Messages
                </h1>
                <p style="color: rgba(255, 255, 255, 0.6); font-size: 13px;">
                    Messages vocaux • Réactions • Liens enrichis • Chiffrement E2E
                </p>
            </div>
        </div>
        <a href="{{ route('web.messages.classic') }}"
           style="background: rgba(255, 255, 255, 0.1);
                  color: white;
                  padding: 10px 16px;
                  border-radius: 10px;
                  text-decoration: none;
                  display: inline-flex;
                  align-items: center;
                  gap: 8px;
                  border: 1px solid rgba(255, 255, 255, 0.2);
                  transition: all 0.2s ease;
                  font-size: 14px;"
           onmouseover="this.style.background='rgba(255, 255, 255, 0.15)'"
           onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'">
            📋 Interface Classique
        </a>
    </div>

    <div class="messaging-app">
        <!-- Liste des conversations -->
        <div class="conversations-sidebar">
            <div class="sidebar-header">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 class="sidebar-title" style="margin: 0;">💬 Messages</h2>
                    <button id="newConversationBtn" class="icon-btn" title="Nouvelle conversation"
                            style="background: linear-gradient(135deg, #fbbb2a, #df5526); border: none; padding: 10px 14px;">
                        ✏️
                    </button>
                </div>
                <div class="search-wrapper">
                    <input type="text"
                           id="searchConversations"
                           class="search-box"
                           placeholder="🔍 Rechercher une conversation...">
                </div>
            </div>

            <div id="conversationsList" class="conversations-list">
                <!-- Chargé dynamiquement via JS -->
            </div>
        </div>

        <!-- Zone de chat -->
        <div class="chat-area">
            <!-- État initial: aucune conversation sélectionnée -->
            <div id="emptyState" class="empty-state">
                <div class="empty-state-icon">💬</div>
                <h3>Sélectionnez une conversation</h3>
                <p>Choisissez un contact pour commencer à discuter</p>
            </div>

            <!-- En-tête du chat -->
            <div id="chatHeader" class="chat-header" style="display: none;">
                <div class="chat-user-info">
                    <div class="chat-avatar" id="chatAvatar"></div>
                    <div class="chat-user-details">
                        <h3 id="chatUserName"></h3>
                        <div class="user-status-container">
                            <span class="status-badge" id="chatUserStatus"></span>
                            <span class="status-text" id="chatUserStatusText"></span>
                            <span id="typingIndicator" class="typing-indicator" style="display: none;">
                                est en train d'écrire...
                            </span>
                        </div>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="icon-btn" id="showPinnedBtn" title="Messages épinglés">
                        📌 <span id="pinnedCount" class="badge">0</span>
                    </button>
                    <button class="icon-btn" id="searchInChatBtn" title="Rechercher">
                        🔍
                    </button>
                    <button class="icon-btn" id="chatMenuBtn" title="Options">
                        ⋮
                    </button>
                </div>
            </div>

            <!-- Zone de recherche dans conversation (masquée par défaut) -->
            <div id="searchInChatPanel" class="search-panel hidden">
                <input type="text" id="searchInChat" placeholder="Rechercher dans cette conversation...">
                <button id="closeSearchPanel" class="icon-btn">✕</button>
            </div>

            <!-- Messages épinglés (masqué par défaut) -->
            <div id="pinnedMessagesPanel" class="pinned-panel" style="display: none;">
                <div class="pinned-header">
                    <h4>📌 Messages épinglés</h4>
                    <button id="closePinnedPanel" class="icon-btn">✕</button>
                </div>
                <div id="pinnedMessagesList" class="pinned-messages-list">
                    <!-- Chargé dynamiquement -->
                </div>
            </div>

            <!-- Messages -->
            <div id="messagesContainer" class="messages-container" style="display: none;">
                <div id="messagesList" class="messages-list">
                    <!-- Messages chargés dynamiquement -->
                </div>
                <div id="scrollToBottom" class="scroll-to-bottom" style="display: none;">
                    <button class="scroll-btn">⬇️ Nouveaux messages</button>
                </div>
            </div>

            <!-- Zone de saisie -->
            <div id="messageInput" class="message-input-area" style="display: none;">
                <!-- Zone de réponse (affichée quand on répond à un message) -->
                <div id="replyingTo" class="replying-to" style="display: none;">
                    <div class="reply-content">
                        <strong id="replyToUser"></strong>
                        <p id="replyToContent"></p>
                    </div>
                    <button id="cancelReply" class="icon-btn">✕</button>
                </div>

                <!-- Preview des fichiers uploadés -->
                <div id="filePreviewArea" class="file-preview-area hidden"></div>

                <!-- Preview des liens -->
                <div id="linkPreviewArea" class="link-preview-area hidden"></div>

                <div class="input-wrapper">
                    <button class="icon-btn" id="emojiPickerBtn" title="Émojis">
                        😊
                    </button>
                    <button class="icon-btn" id="attachFileBtn" title="Joindre un fichier">
                        📎
                    </button>
                    <input type="file" id="fileInput" style="display: none;" multiple>

                    <div class="text-input-container">
                        <textarea
                            id="messageTextarea"
                            placeholder="Écrivez un message..."
                            rows="1"
                            maxlength="5000"></textarea>
                    </div>

                    <button class="icon-btn voice-btn" id="voiceRecordBtn" title="Message vocal">
                        🎤
                    </button>
                    <button class="send-btn" id="sendMessageBtn" title="Envoyer" disabled>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Picker d'émojis (popup) -->
    <div id="emojiPicker" class="emoji-picker" style="display: none;">
        <div class="emoji-header">
            <input type="text" id="emojiSearch" placeholder="Rechercher un émoji...">
        </div>
        <div class="emoji-categories">
            <button data-category="recent">⏱️</button>
            <button data-category="smileys">😀</button>
            <button data-category="gestures">👋</button>
            <button data-category="animals">🐶</button>
            <button data-category="food">🍕</button>
            <button data-category="travel">✈️</button>
            <button data-category="objects">⚽</button>
            <button data-category="symbols">❤️</button>
        </div>
        <div id="emojiGrid" class="emoji-grid"></div>
    </div>

    <!-- Menu contextuel pour les messages -->
    <div id="messageContextMenu" class="context-menu" style="display: none;">
        <button data-action="reply">💬 Répondre</button>
        <button id="pinButton" data-action="pin">📌 Épingler</button>
        <button id="unpinButton" data-action="unpin" style="display: none;">📌 Désépingler</button>
        <button data-action="copy">📋 Copier</button>
        <button data-action="delete">🗑️ Supprimer</button>
    </div>

    <!-- Picker de réactions rapides -->
    <div id="reactionPicker" class="reaction-picker" style="display: none;">
        <button class="reaction-btn" data-emoji="👍">👍</button>
        <button class="reaction-btn" data-emoji="❤️">❤️</button>
        <button class="reaction-btn" data-emoji="😂">😂</button>
        <button class="reaction-btn" data-emoji="😮">😮</button>
        <button class="reaction-btn" data-emoji="😢">😢</button>
        <button class="reaction-btn" data-emoji="🎉">🎉</button>
        <button class="reaction-btn" data-emoji="🔥">🔥</button>
        <button class="reaction-btn" data-emoji="➕">➕</button>
    </div>

    <!-- Modal Nouvelle Conversation -->
    <div id="newConversationModal" class="modal" style="display: none;">
        <div class="modal-overlay" onclick="closeNewConversationModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>✏️ Nouvelle Conversation</h3>
                <button onclick="closeNewConversationModal()" class="modal-close">×</button>
            </div>
            <div class="modal-body">
                <input type="text" id="userSearchInput" class="search-box" placeholder="🔍 Rechercher un utilisateur..." style="margin-bottom: 16px;">
                <div id="usersList" class="users-list">
                    <!-- Chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <!-- CSS additionnel -->
    <link rel="stylesheet" href="{{ asset('css/voice-recorder.css') }}">
    <link rel="stylesheet" href="{{ asset('css/link-preview.css') }}">

    <!-- Scripts -->
    <script>
        console.log('🚀 Initialisation de la page Messages...');

        // Initialiser l'app de messagerie
        @php
            $user = auth()->user();
            // Supprimer les anciens tokens et créer un nouveau
            $user->tokens()->where('name', 'messaging-session')->delete();
            $plainToken = $user->createToken('messaging-session')->plainTextToken;
        @endphp

        console.log('👤 User ID:', {{ $user->id }});

        // Utiliser window pour éviter les conflits de scope
        if (!window.messagingApp) {
            console.log('🆕 Création de messagingApp...');
            const authToken = '{{ $plainToken }}';

            window.messagingApp = new MessagingApp(
                {{ $user->id }},
                '{{ $user->name }}',
                authToken
            );
            console.log('✅ messagingApp créé:', window.messagingApp);
        } else {
            console.log('⚠️ messagingApp déjà défini, réutilisation');
        }

        // Initialiser le voice recorder
        if (!window.voiceRecorder && window.messagingApp) {
            window.voiceRecorder = new VoiceRecorder(window.messagingApp);
            console.log('🎤 Voice recorder initialisé');
        }

        // Initialiser le link preview manager
        if (!window.linkPreviewManager && window.messagingApp) {
            window.linkPreviewManager = new LinkPreviewManager(window.messagingApp);
            console.log('🔗 Link preview manager initialisé');
        }

        // Détecter les liens dans le textarea
        const textarea = document.getElementById('messageTextarea');
        if (textarea && window.linkPreviewManager) {
            let linkPreviewDebounce = null;
            textarea.addEventListener('input', (e) => {
                clearTimeout(linkPreviewDebounce);
                linkPreviewDebounce = setTimeout(() => {
                    window.linkPreviewManager.showPreviewsInInput(e.target.value);
                }, 1000);
            });
        }

        // ===== NOUVELLE CONVERSATION =====
        let allUsers = [];

        // Ouvrir la modal nouvelle conversation
        document.getElementById('newConversationBtn')?.addEventListener('click', async () => {
            const modal = document.getElementById('newConversationModal');
            modal.style.display = 'flex';
            await loadUsers();
        });

        // Fermer la modal
        function closeNewConversationModal() {
            document.getElementById('newConversationModal').style.display = 'none';
        }

        // Charger tous les utilisateurs
        async function loadUsers() {
            try {
                if (!window.messagingApp || !window.messagingApp.authToken) {
                    console.error('❌ messagingApp non initialisé');
                    return;
                }

                const response = await fetch('/api/messaging/users', {
                    headers: {
                        'Authorization': `Bearer ${window.messagingApp.authToken}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Erreur de chargement');

                const data = await response.json();
                console.log('👥 Utilisateurs chargés:', data.users.length);
                console.log('👥 Données utilisateurs:', data.users.map(u => ({name: u.name, status: u.status})));

                // Les statuts viennent déjà corrects de /api/messaging/users
                allUsers = data.users;
                renderUsers(allUsers);
            } catch (error) {
                console.error('❌ Erreur chargement utilisateurs:', error);
            }
        }

        // Afficher les utilisateurs
        function renderUsers(users) {
            console.log('🎨 renderUsers appelé avec:', users.length, 'utilisateurs');
            console.log('🎨 Statuts des utilisateurs:', users.map(u => `${u.name}: ${u.status}`));

            const list = document.getElementById('usersList');
            if (users.length === 0) {
                list.innerHTML = '<p style="color: rgba(255,255,255,0.5); text-align: center; padding: 20px;">Aucun utilisateur trouvé</p>';
                return;
            }

            list.innerHTML = users.map(user => `
                <div class="user-item" onclick="startConversation(${user.id})">
                    <div class="user-item-avatar">
                        ${user.avatar ? `<img src="${user.avatar}" alt="${user.name}">` : user.name.charAt(0)}
                        <span class="status-badge status-${user.status || 'offline'}"></span>
                    </div>
                    <div class="user-item-info">
                        <div class="user-item-name">${user.name}</div>
                        <div class="user-item-status">
                            <span class="status-badge status-${user.status || 'offline'}"></span>
                            ${getStatusText(user.status || 'offline')}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Recherche d'utilisateurs
        document.getElementById('userSearchInput')?.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            const filtered = allUsers.filter(u =>
                u.name.toLowerCase().includes(query) ||
                u.email.toLowerCase().includes(query)
            );
            renderUsers(filtered);
        });

        // Démarrer une conversation
        async function startConversation(userId) {
            closeNewConversationModal();
            await window.messagingApp.selectConversation(userId);
        }

        function getStatusText(status) {
            const statuses = {
                online: 'En ligne',
                away: 'Absent',
                busy: 'Occupé',
                offline: 'Hors ligne'
            };
            return statuses[status] || 'Inconnu';
        }

        // Initialiser la présence utilisateur
        document.addEventListener('DOMContentLoaded', async () => {
            const userId = {{ auth()->id() }};

            if (userId && window.messagingApp) {
                console.log('🚀 Initialisation de la présence utilisateur...');
                window.userPresenceManager = new UserPresenceManager();
                await window.userPresenceManager.init(userId, window.messagingApp.authToken);
                console.log('✅ Présence utilisateur initialisée');
            }
        });
    </script>
</x-app-layout>
