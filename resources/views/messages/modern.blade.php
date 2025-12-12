<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/messaging-modern.css') }}?v={{ time() }}&r={{ rand() }}">
    <!-- Présence utilisateur (pastilles) -->
    <link rel="stylesheet" href="{{ asset('css/user-presence.css') }}?v={{ time() }}&r={{ rand() }}">

    <!-- Viewport meta tag pour mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Styles pour les paramètres de notification -->
    <style>
        /* Styles pour le bouton de notification */
        .notification-container {
            position: relative;
            display: inline-block;
        }

        .notification-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 8px 12px;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .notification-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .notification-toggle.enabled {
            background: rgba(77, 171, 247, 0.2);
            border-color: #4dabf7;
        }

        .notification-icon {
            font-size: 16px;
        }

        .notification-text {
            font-weight: 500;
        }

        .notification-status {
            font-size: 12px;
            opacity: 0.8;
        }

        .notification-arrow {
            font-size: 12px;
            transition: transform 0.2s ease;
        }

        /* Styles pour le menu déroulant */
        .notification-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: rgba(0, 0, 0, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            width: 280px;
            z-index: 10000 !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            margin-top: 4px;
            max-height: 400px;
            overflow-y: auto;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .notification-menu.hidden {
            display: none;
        }

        .menu-section {
            padding: 12px 0;
        }

        .menu-section:not(:last-child) {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .menu-title {
            padding: 0 16px;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 16px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            transition: background 0.2s ease;
            font-size: 14px;
            text-align: left;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .menu-item.active {
            background: rgba(77, 171, 247, 0.2);
            color: #4dabf7;
        }

        .menu-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sound-test {
            margin-left: auto;
            opacity: 0.6;
            transition: opacity 0.2s ease;
        }

        .sound-test:hover {
            opacity: 1;
        }

        /* Styles pour la checkbox */
        .sound-toggle {
            cursor: pointer;
            user-select: none;
        }

        .sound-toggle input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .checkmark {
            position: relative;
            display: inline-block;
            width: 18px;
            height: 18px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 3px;
            margin-right: 12px;
            transition: all 0.2s ease;
        }

        .sound-toggle input:checked ~ .checkmark {
            background: #4dabf7;
            border-color: #4dabf7;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .sound-toggle input:checked ~ .checkmark:after {
            display: block;
        }
    </style>

    <!-- Bibliothèques WebSocket -->
    @vite(['resources/js/echo.js'])

    <!-- Scripts -->
    <script src="{{ asset('js/messaging-app.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <script src="{{ asset('js/voice-recorder.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <script src="{{ asset('js/link-preview.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <script src="{{ asset('js/user-presence.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <script src="{{ asset('js/e2e-encryption.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <script src="{{ asset('js/sound-manager.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <script src="{{ asset('js/notification-settings-manager.js') }}?v={{ time() }}&r={{ rand() }}"></script>
    <!-- WebRTC Call Manager - Chargé après messaging-app.js -->
    <script src="{{ asset('js/webrtc-call.js') }}?v={{ time() }}"></script>

    <!-- Configuration WebSocket - sera initialisé après le chargement du token -->
    <script>
        // Stocker la clé Reverb pour utilisation ultérieure
        window.REVERB_APP_KEY = "{{ config('broadcasting.connections.reverb.key') }}";
        window.REVERB_APP_ID = "{{ config('broadcasting.connections.reverb.app_id') }}";

        console.log('🔑 Reverb App Key:', window.REVERB_APP_KEY ? 'Définie' : 'Non définie');
        console.log('🔑 Reverb App ID:', window.REVERB_APP_ID);
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
        <div style="display: flex; align-items: center; gap: 12px;">
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
    </div>

    <div class="messaging-app">
        <!-- Overlay pour mobile -->
        <div id="mobileOverlay" class="mobile-overlay"></div>

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
                <!-- Bouton Paramètres de Notification -->
                <div class="notification-container" style="margin-top: 16px;">
                    <button id="notificationToggleBtn" class="notification-toggle" type="button" title="Notifications & Sons">
                        <span class="notification-icon">🔔</span>
                        <span class="notification-text">Notifications</span>
                        <span class="notification-status" id="notificationStatus">Désactivé</span>
                        <span class="notification-arrow">▼</span>
                    </button>

                    <!-- Menu déroulant des options -->
                    <div id="notificationMenu" class="notification-menu hidden">
                        <div class="menu-section">
                            <div class="menu-title">Notifications</div>
                            <button id="requestPermissionBtn" class="menu-item">
                                <span class="menu-icon">🔔</span>
                                <span>Activer les notifications</span>
                            </button>
                        </div>

                        <div class="menu-section">
                            <div class="menu-title">Sonneries</div>
                            <button class="menu-item sound-option" data-sound="bell">
                                <span class="menu-icon">🔔</span>
                                <span>Cloche</span>
                                <span class="sound-test">▶️</span>
                            </button>
                            <button class="menu-item sound-option" data-sound="chime">
                                <span class="menu-icon">🎵</span>
                                <span>Carillon</span>
                                <span class="sound-test">▶️</span>
                            </button>
                            <button class="menu-item sound-option" data-sound="notification">
                                <span class="menu-icon">📱</span>
                                <span>Moderne</span>
                                <span class="sound-test">▶️</span>
                            </button>
                            <button class="menu-item sound-option" data-sound="gentle">
                                <span class="menu-icon">😌</span>
                                <span>Doux</span>
                                <span class="sound-test">▶️</span>
                            </button>
                        </div>

                        <div class="menu-section">
                            <label class="menu-item sound-toggle">
                                <input type="checkbox" id="soundEnabledCheckbox">
                                <span class="checkmark"></span>
                                <span>Activer les sons</span>
                            </label>
                        </div>
                    </div>
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
                    <button class="icon-btn mobile-only" id="toggleSidebarBtn" title="Conversations" style="background: linear-gradient(135deg, #fbbb2a, #df5526); border: 2px solid rgba(255, 255, 255, 0.3); box-shadow: 0 4px 12px rgba(251, 187, 42, 0.4); position: relative;">
                        ☰ <span id="conversationsCount" class="badge" style="background: rgba(255, 255, 255, 0.9); color: #df5526; margin-left: 4px;">0</span>
                        <span id="unreadIndicator" class="unread-indicator" style="display: none; position: absolute; top: -2px; right: -2px; width: 8px; height: 8px; background: #df5526; border-radius: 50%; border: 2px solid rgba(255, 255, 255, 0.9);"></span>
                    </button>
                    <button class="icon-btn" id="startAudioCallBtn" title="Appel Audio" style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); transition: all 0.3s ease;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #3b82f6;">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </button>
                    <button class="icon-btn" id="startVideoCallBtn" title="Appel Vidéo" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); transition: all 0.3s ease;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;">
                            <path d="M23 7l-7 5 7 5V7z"/>
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                        </svg>
                    </button>
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

        // Définir le token d'authentification globalement pour Echo
        window.authToken = '{{ $plainToken }}';
        console.log('🔑 Auth token défini globalement');

        // Initialiser Echo avec Reverb maintenant que le token est disponible
        if (typeof window.initializeEcho === 'function' && window.REVERB_APP_KEY) {
            window.initializeEcho(window.REVERB_APP_KEY, window.authToken);
            console.log('✅ Echo initialisé depuis modern.blade.php');
        } else {
            console.warn('⚠️ initializeEcho pas encore disponible ou REVERB_APP_KEY manquante');
            console.log('initializeEcho type:', typeof window.initializeEcho);
            console.log('REVERB_APP_KEY:', window.REVERB_APP_KEY);

            // Fallback: attendre un peu et réessayer
            setTimeout(() => {
                if (typeof window.initializeEcho === 'function' && window.REVERB_APP_KEY) {
                    window.initializeEcho(window.REVERB_APP_KEY, window.authToken);
                    console.log('✅ Echo initialisé après délai');
                }
            }, 500);
        }

        // Utiliser window pour éviter les conflits de scope
        if (!window.messagingApp) {
            console.log('🆕 Création de messagingApp...');
            const authToken = '{{ $plainToken }}';
            console.log('🔑 Auth token length:', authToken ? authToken.length : 'null');

            try {
                window.messagingApp = new MessagingApp(
                    {{ $user->id }},
                    '{{ $user->name }}',
                    authToken
                );
                console.log('✅ messagingApp créé:', typeof window.messagingApp);
                console.log('✅ messagingApp.userId:', window.messagingApp.userId);
                console.log('✅ messagingApp.authToken length:', window.messagingApp.authToken ? window.messagingApp.authToken.length : 'null');
            } catch (error) {
                console.error('❌ Erreur lors de la création de messagingApp:', error);
                // Créer une instance basique pour éviter les erreurs
                window.messagingApp = {
                    selectConversation: function(id) { console.log('selectConversation appelé avec:', id); },
                    showReactionPicker: function() { console.log('showReactionPicker appelé'); },
                    toggleAudioPlayer: function() { console.log('toggleAudioPlayer appelé'); },
                    onReactionClick: function() { console.log('onReactionClick appelé'); },
                    navigateToMessage: function() { console.log('navigateToMessage appelé'); }
                };
            }
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

        // Gestionnaire d'appel audio
        document.getElementById('startAudioCallBtn')?.addEventListener('click', async () => {
            if (!window.messagingApp || (!window.messagingApp.currentConversationUserId && !window.messagingApp.currentConversation)) {
                showToast('Veuillez sélectionner une conversation', 'error');
                return;
            }

            const receiverId = window.messagingApp.currentConversationUserId || window.messagingApp.currentConversation?.id;
            if (!receiverId) {
                showToast('Impossible de récupérer l\'ID du destinataire', 'error');
                return;
            }

            if (typeof window.initWebRTCCall === 'function') {
                window.initWebRTCCall(receiverId, 'audio');
            } else {
                showToast('Module d\'appel en cours de chargement...', 'info');
            }
        });

        // Gestionnaire d'appel vidéo (WebRTC natif)
        document.getElementById('startVideoCallBtn')?.addEventListener('click', async () => {
            if (!window.messagingApp || (!window.messagingApp.currentConversationUserId && !window.messagingApp.currentConversation)) {
                showToast('Veuillez sélectionner une conversation', 'error');
                return;
            }

            const receiverId = window.messagingApp.currentConversationUserId || window.messagingApp.currentConversation?.id;
            if (!receiverId) {
                showToast('Impossible de récupérer l\'ID du destinataire', 'error');
                return;
            }

            if (typeof window.initWebRTCCall === 'function') {
                window.initWebRTCCall(receiverId, 'video');
            } else {
                showToast('Module d\'appel vidéo en cours de chargement...', 'info');
            }
        });

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
            console.log('🚀 DOMContentLoaded - Début initialisation...');
            const userId = {{ auth()->id() }};

            console.log('👤 User ID:', userId);

            if (userId && window.messagingApp) {
                console.log('🚀 Initialisation de la présence utilisateur...');
                window.userPresenceManager = new UserPresenceManager();
                await window.userPresenceManager.init(userId, window.messagingApp.authToken);
                console.log('✅ Présence utilisateur initialisée');
            }

            // Initialiser le système de notifications
            console.log('🚀 Initialisation du système de notifications...');

            // 1. Créer le gestionnaire de paramètres de notification
            window.notificationSettings = new NotificationSettingsManager();
            await window.notificationSettings.init();
            console.log('✅ NotificationSettingsManager initialisé');

            // 2. Créer un alias pour la compatibilité avec messaging-app.js
            window.pushNotificationManager = {
                isEnabled: window.notificationSettings.browserNotificationsEnabled,
                soundEnabled: window.notificationSettings.soundEnabled,
                isGranted: window.notificationSettings.browserNotificationsEnabled,

                showNotification: (title, body) => {
                    console.log('🔔 showNotification appelée:', title, body);
                    window.notificationSettings.showBrowserNotification(title, {
                        body: body,
                        icon: '/favicon.ico',
                        badge: '/favicon.ico'
                    });
                },

                playSound: () => {
                    console.log('🔔 playSound appelée');
                    window.notificationSettings.playSound();
                }
            };

            console.log('🔗 Alias pushNotificationManager créé pour compatibilité');

            // Connecter immédiatement le bouton de notification
            initNotificationButton();
        });

        // Fonction pour initialiser le bouton de notification
       function initNotificationButton() {
            console.log('🎯 Initialisation du bouton de notification...');

            const notificationBtn = document.getElementById('notificationToggleBtn');
            const notificationMenu = document.getElementById('notificationMenu');
            const notificationStatus = document.getElementById('notificationStatus');
            const notificationArrow = notificationBtn?.querySelector('.notification-arrow');

            if (!notificationBtn || !notificationMenu) {
                console.error('❌ Éléments de notification non trouvés');
                return;
            }

            // Mettre à jour le statut initial
            updateNotificationStatus();

            // Toggle du menu
            notificationBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationMenu.classList.toggle('hidden');
                if (notificationArrow) {
                    const isHidden = notificationMenu.classList.contains('hidden');
                    notificationArrow.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            });

            // Fermer le menu en cliquant ailleurs
            document.addEventListener('click', (e) => {
                if (!notificationBtn.contains(e.target) && !notificationMenu.contains(e.target)) {
                    notificationMenu.classList.add('hidden');
                    if (notificationArrow) {
                        notificationArrow.style.transform = 'rotate(0deg)';
                    }
                }
            });

            // Bouton de permission
            const permissionBtn = document.getElementById('requestPermissionBtn');
            if (permissionBtn) {
                permissionBtn.addEventListener('click', async () => {
                    try {
                        await window.notificationSettings.requestPermission();

                        // Mettre à jour l'alias pushNotificationManager
                        window.pushNotificationManager.isEnabled = window.notificationSettings.browserNotificationsEnabled;
                        window.pushNotificationManager.isGranted = window.notificationSettings.browserNotificationsEnabled;

                        updateNotificationStatus();
                        notificationMenu.classList.add('hidden');
                        if (notificationArrow) {
                            notificationArrow.style.transform = 'rotate(0deg)';
                        }
                        showToast('✅ Notifications activées !', 'success');
                        console.log('✅ Permissions accordées - Notifications et sons activés');
                    } catch (error) {
                        console.error('Erreur permission:', error);
                        showToast('❌ ' + error.message, 'error');
                    }
                });
            }

            // Gestion des sonneries
            const soundOptions = document.querySelectorAll('.sound-option');
            soundOptions.forEach(option => {
                option.addEventListener('click', (e) => {
                    const soundTest = e.target.closest('.sound-test');

                    if (soundTest) {
                        // Tester le son
                        const soundName = option.dataset.sound;
                        window.notificationSettings.testSound(soundName);
                    } else {
                        // Changer le son actif
                        const soundName = option.dataset.sound;
                        window.notificationSettings.setSound(soundName);
                        soundOptions.forEach(opt => opt.classList.remove('active'));
                        option.classList.add('active');
                        showToast(`🎵 Son changé : ${getSoundLabel(soundName)}`, 'info');
                    }
                });
            });

            // Activer/désactiver les sons
            const soundCheckbox = document.getElementById('soundEnabledCheckbox');
            if (soundCheckbox) {
                soundCheckbox.checked = window.notificationSettings.soundEnabled;
                soundCheckbox.addEventListener('change', () => {
                    window.notificationSettings.setSoundEnabled(soundCheckbox.checked);

                    // Mettre à jour l'alias pushNotificationManager
                    window.pushNotificationManager.soundEnabled = soundCheckbox.checked;

                    showToast(soundCheckbox.checked ? '🔊 Sons activés' : '🔇 Sons désactivés', 'info');
                });
            }

            // Marquer le son actif
            const activeSound = window.notificationSettings.selectedSound;
            const activeOption = document.querySelector(`.sound-option[data-sound="${activeSound}"]`);
            if (activeOption) {
                activeOption.classList.add('active');
            }

            console.log('🎉 Bouton de notification initialisé !');
        }

        /**
         * Mettre à jour le statut des notifications
         */
        function updateNotificationStatus() {
            const notificationStatus = document.getElementById('notificationStatus');
            const notificationBtn = document.getElementById('notificationToggleBtn');

            if (!notificationStatus || !notificationBtn) return;

            const statusText = window.notificationSettings.getPermissionStatusText();
            const permission = window.notificationSettings.getPermissionStatus();

            notificationStatus.textContent = statusText;

            // Ajouter une classe selon le statut
            notificationBtn.classList.remove('enabled', 'disabled', 'blocked');

            if (permission === 'granted') {
                notificationBtn.classList.add('enabled');
            } else if (permission === 'denied') {
                notificationBtn.classList.add('blocked');
            } else {
                notificationBtn.classList.add('disabled');
            }

            console.log('📊 Statut mis à jour:', statusText, '/', permission);
        }

        /**
         * Obtenir le label d'un son
         */
        function getSoundLabel(soundName) {
            const labels = {
                'bell': 'Cloche',
                'chime': 'Carillon',
                'notification': 'Moderne',
                'gentle': 'Doux'
            };
            return labels[soundName] || soundName;
        }

        /**
         * Afficher un toast (notification temporaire)
         */
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;

            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 10000;
                animation: slideInRight 0.3s ease;
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Animations CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</x-app-layout>
