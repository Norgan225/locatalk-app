<x-app-layout>
    {{-- CSS pour le chat --}}
    <link rel="stylesheet" href="{{ asset('css/channel-chat.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/user-presence.css') }}?v={{ time() }}">

    {{-- Meta pour mobile --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    @php
        $user = auth()->user();
        $plainToken = $user->createToken('channel-session')->plainTextToken;
        $channelTypeIcons = [
            'public' => 'fas fa-globe',
            'private' => 'fas fa-lock',
            'department' => 'fas fa-building',
            'direct' => 'fas fa-user'
        ];
        $channelTypeColors = [
            'public' => '#10b981',
            'private' => '#ef4444',
            'department' => '#f59e0b',
            'direct' => '#3b82f6'
        ];
    @endphp

    <div class="channel-chat-container" id="channelChatContainer">
        {{-- Header du canal --}}
        <header class="channel-chat-header">
            <div class="channel-header-info">
                <a href="{{ route('channels.index') }}" class="channel-header-back" title="Retour aux canaux">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="channel-avatar" style="background: {{ $channelTypeColors[$channel->type] ?? '#667eea' }}20; color: {{ $channelTypeColors[$channel->type] ?? '#667eea' }};">
                    <i class="{{ $channelTypeIcons[$channel->type] ?? 'fas fa-hashtag' }}"></i>
                </div>
                <div class="channel-header-details">
                    <h1 class="channel-header-name">{{ $channel->name }}</h1>
                    <p class="channel-header-status">
                        <span class="online-count">
                            <span class="online-dot"></span>
                            <span id="onlineCount">{{ $channel->users->count() }}</span> membres
                        </span>
                        @if($channel->description)
                            <span class="separator">•</span>
                            <span class="channel-desc-short">{{ Str::limit($channel->description, 30) }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="channel-header-actions">
                {{-- Bouton appel vidéo --}}
                <button class="channel-header-btn" id="videoCallBtn" title="Appel vidéo">
                    <i class="fas fa-video"></i>
                </button>
                {{-- Bouton appel audio --}}
                <button class="channel-header-btn" id="audioCallBtn" title="Appel audio">
                    <i class="fas fa-phone"></i>
                </button>
                {{-- Bouton recherche --}}
                <button class="channel-header-btn" id="searchBtn" title="Rechercher dans les messages">
                    <i class="fas fa-search"></i>
                </button>
                {{-- Bouton membres --}}
                <button class="channel-header-btn" id="membersBtn" title="Voir les membres">
                    <i class="fas fa-users"></i>
                </button>
                {{-- Bouton paramètres (si admin/owner) --}}
                @if($user->canManageUsers() || $channel->created_by === $user->id)
                <button class="channel-header-btn" id="settingsBtn" title="Paramètres du canal">
                    <i class="fas fa-cog"></i>
                </button>
                @endif
                {{-- Quitter le canal --}}
                @if($channel->is_member && $channel->created_by !== $user->id)
                <button class="channel-header-btn danger" id="leaveChannelBtn" title="Quitter le canal">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
                @endif
            </div>
        </header>

        {{-- Barre des messages épinglés --}}
        <div class="pinned-messages-bar" id="pinnedBar" style="display: none;">
            <i class="fas fa-thumbtack"></i>
            <span id="pinnedCount">0</span> message(s) épinglé(s)
            <span class="view-all">Voir tout <i class="fas fa-chevron-right"></i></span>
        </div>

        {{-- Zone des messages --}}
        <div class="channel-messages-area" id="messagesArea">
            {{-- Les messages seront injectés ici par JavaScript --}}
            <div class="loading-messages" id="loadingMessages">
                <div class="message-skeleton received">
                    <div class="skeleton-avatar"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-name"></div>
                        <div class="skeleton-bubble"></div>
                    </div>
                </div>
                <div class="message-skeleton sent">
                    <div class="skeleton-content">
                        <div class="skeleton-name"></div>
                        <div class="skeleton-bubble"></div>
                    </div>
                </div>
                <div class="message-skeleton received">
                    <div class="skeleton-avatar"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-name"></div>
                        <div class="skeleton-bubble"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Indicateur de frappe --}}
        <div class="typing-indicator" id="typingIndicator" style="display: none;">
            <span class="typing-text" id="typingText">Quelqu'un tape...</span>
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        {{-- Zone de saisie --}}
        @if($channel->is_member)
        <div class="channel-input-area" id="inputArea">
            {{-- Prévisualisation réponse --}}
            <div class="reply-preview" id="replyPreview" style="display: none;">
                <div class="reply-preview-content">
                    <span class="reply-preview-sender" id="replySender"></span>
                    <span class="reply-preview-text" id="replyText"></span>
                </div>
                <button class="reply-preview-close" id="cancelReply">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Prévisualisation fichiers --}}
            <div class="file-preview" id="filePreview" style="display: none;"></div>

            {{-- Ligne de saisie --}}
            <div class="input-row">
                <div class="input-actions-left">
                    {{-- Fichier --}}
                    <button class="input-btn" id="attachFileBtn" title="Joindre un fichier">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <input type="file" id="fileInput" multiple accept="*/*" style="display: none;">

                    {{-- Emoji --}}
                    <button class="input-btn" id="emojiBtn" title="Emojis">
                        <i class="fas fa-smile"></i>
                    </button>
                </div>

                {{-- Zone de texte --}}
                <div class="input-text-wrapper">
                    <textarea
                        class="message-input"
                        id="messageInput"
                        placeholder="Écrire un message..."
                        rows="1"
                    ></textarea>

                    {{-- Emoji Picker --}}
                    <div class="emoji-picker-container" id="emojiPicker" style="display: none;">
                        <div class="emoji-picker-header">
                            <button class="emoji-category-btn active" data-category="smileys">😀</button>
                            <button class="emoji-category-btn" data-category="people">👋</button>
                            <button class="emoji-category-btn" data-category="nature">🌿</button>
                            <button class="emoji-category-btn" data-category="food">🍔</button>
                            <button class="emoji-category-btn" data-category="activities">⚽</button>
                            <button class="emoji-category-btn" data-category="travel">🚗</button>
                            <button class="emoji-category-btn" data-category="objects">💡</button>
                            <button class="emoji-category-btn" data-category="symbols">❤️</button>
                        </div>
                        <input type="text" class="emoji-search" placeholder="Rechercher un emoji...">
                        <div class="emoji-grid" id="emojiGrid"></div>
                    </div>
                </div>

                {{-- Bouton vocal --}}
                <button class="input-btn" id="voiceBtn" title="Message vocal">
                    <i class="fas fa-microphone"></i>
                </button>

                {{-- Bouton envoyer --}}
                <button class="send-btn" id="sendBtn" disabled title="Envoyer">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            {{-- UI Enregistrement vocal --}}
            <div class="voice-recording-ui" id="voiceRecordingUI" style="display: none;">
                <button class="cancel-btn" id="cancelRecording" title="Annuler">
                    <i class="fas fa-times"></i>
                </button>
                <span class="recording-time" id="recordingTime">0:00</span>
                <div class="waveform-preview" id="waveformPreview"></div>
                <button class="confirm-btn" id="confirmRecording" title="Envoyer">
                    <i class="fas fa-check"></i>
                </button>
            </div>
        </div>
        @else
        {{-- Pas membre - Bouton rejoindre --}}
        <div class="channel-input-area" style="text-align: center; padding: 20px;">
            <p style="color: var(--text-secondary); margin-bottom: 15px;">
                Vous n'êtes pas membre de ce canal.
            </p>
            @if($channel->type === 'public' || ($channel->type === 'department' && $user->department_id === $channel->department_id))
            <form action="{{ route('channels.join', $channel->id) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="send-btn" style="width: auto; padding: 12px 24px; border-radius: 10px;">
                    <i class="fas fa-user-plus"></i>
                    Rejoindre le canal
                </button>
            </form>
            @else
            <p style="color: var(--text-muted); font-size: 0.875rem;">
                Ce canal est privé. Demandez à un administrateur de vous ajouter.
            </p>
            @endif
        </div>
        @endif
    </div>

    {{-- Modal Membres --}}
    <div class="members-modal" id="membersModal" style="display: none;">
        <div class="members-modal-content">
            <div class="members-modal-header">
                <h3><i class="fas fa-users"></i> Membres du canal</h3>
                <button class="members-modal-close" id="closeMembersModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="members-search">
                <input type="text" id="memberSearchInput" placeholder="Rechercher un membre...">
            </div>
            <div class="members-list" id="membersList">
                @foreach($channel->users as $member)
                <div class="member-item" data-user-id="{{ $member->id }}">
                    <div class="member-avatar {{ $member->id === $user->id ? '' : '' }}" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        {{ strtoupper(substr($member->name, 0, 2)) }}
                    </div>
                    <div class="member-info">
                        <div class="member-name">
                            {{ $member->name }}
                            @if($member->id === $user->id)
                                (Vous)
                            @endif
                        </div>
                        <div class="member-role">{{ $member->role ?? 'Membre' }}</div>
                    </div>
                    @if($member->id === $channel->created_by)
                    <span class="member-badge">Créateur</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal Paramètres --}}
    @if($user->canManageUsers() || $channel->created_by === $user->id)
    <div class="members-modal" id="settingsModal" style="display: none;">
        <div class="members-modal-content" style="max-width: 500px;">
            <div class="members-modal-header">
                <h3><i class="fas fa-cog"></i> Paramètres du canal</h3>
                <button class="members-modal-close" id="closeSettingsModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="channelSettingsForm" style="padding: 20px;">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">
                        Nom du canal
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ $channel->name }}"
                        class="message-input"
                        style="padding: 12px; border-radius: 8px;"
                    >
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">
                        Description
                    </label>
                    <textarea
                        name="description"
                        class="message-input"
                        rows="3"
                        style="padding: 12px; border-radius: 8px;"
                    >{{ $channel->description }}</textarea>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">
                        Type de canal
                    </label>
                    <select name="type" class="message-input" style="padding: 12px; border-radius: 8px;">
                        <option value="public" {{ $channel->type === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ $channel->type === 'private' ? 'selected' : '' }}>Privé</option>
                        <option value="department" {{ $channel->type === 'department' ? 'selected' : '' }}>Département</option>
                    </select>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="send-btn" style="flex: 1; width: auto; padding: 12px; border-radius: 8px;">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <button type="button" id="deleteChannelBtn" class="send-btn" style="flex: 1; width: auto; padding: 12px; border-radius: 8px; background: var(--danger-color);">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Messages Épinglés --}}
    <div class="members-modal" id="pinnedModal" style="display: none;">
        <div class="members-modal-content" style="max-width: 500px; max-height: 70vh;">
            <div class="members-modal-header">
                <h3><i class="fas fa-thumbtack"></i> Messages épinglés</h3>
                <button class="members-modal-close" id="closePinnedModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="members-list" id="pinnedMessagesList" style="padding: 15px;">
                {{-- Les messages épinglés seront injectés ici --}}
            </div>
        </div>
    </div>

    {{-- Modal Recherche --}}
    <div class="members-modal" id="searchModal" style="display: none;">
        <div class="members-modal-content" style="max-width: 500px;">
            <div class="members-modal-header">
                <h3><i class="fas fa-search"></i> Rechercher</h3>
                <button class="members-modal-close" id="closeSearchModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="members-search">
                <input type="text" id="messageSearchInput" placeholder="Rechercher dans les messages...">
            </div>
            <div class="members-list" id="searchResults" style="padding: 15px;">
                <div style="text-align: center; color: var(--text-muted); padding: 20px;">
                    Entrez votre recherche pour trouver des messages
                </div>
            </div>
        </div>
    </div>

    {{-- Container pour les toasts --}}
    <div class="toast-container" id="toastContainer"></div>

    {{-- Scripts --}}
    <script src="{{ asset('js/voice-recorder.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/channel-messaging-app.js') }}?v={{ time() }}"></script>
    <script>
        // Configuration
        const channelConfig = {
            channelId: {{ $channel->id }},
            userId: {{ $user->id }},
            userName: @json($user->name),
            authToken: @json($plainToken),
            isMember: {{ $channel->is_member ? 'true' : 'false' }},
            isAdmin: {{ ($user->canManageUsers() || $channel->created_by === $user->id) ? 'true' : 'false' }},
            channelName: @json($channel->name),
            channelType: @json($channel->type),
            csrfToken: @json(csrf_token())
        };

        // Initialiser l'application de chat si membre
        document.addEventListener('DOMContentLoaded', function() {
            @if($channel->is_member)
            // Initialiser l'application de messagerie du canal
            window.channelApp = new ChannelMessagingApp({
                channelId: channelConfig.channelId,
                userId: channelConfig.userId,
                userName: channelConfig.userName,
                authToken: channelConfig.authToken,
                channelName: channelConfig.channelName,
                channelType: channelConfig.channelType,
                isAdmin: channelConfig.isAdmin
            });
            // init() est appelé automatiquement dans le constructeur
            @endif

            // Gestion des modales
            setupModals();

            // Gestion du formulaire de paramètres
            setupSettingsForm();
        });

        // Configuration des modales
        function setupModals() {
            // Modal Membres
            const membersBtn = document.getElementById('membersBtn');
            const membersModal = document.getElementById('membersModal');
            const closeMembersModal = document.getElementById('closeMembersModal');

            if (membersBtn && membersModal) {
                membersBtn.addEventListener('click', () => {
                    membersModal.style.display = 'flex';
                });
                closeMembersModal.addEventListener('click', () => {
                    membersModal.style.display = 'none';
                });
                membersModal.addEventListener('click', (e) => {
                    if (e.target === membersModal) {
                        membersModal.style.display = 'none';
                    }
                });
            }

            // Modal Paramètres
            const settingsBtn = document.getElementById('settingsBtn');
            const settingsModal = document.getElementById('settingsModal');
            const closeSettingsModal = document.getElementById('closeSettingsModal');

            if (settingsBtn && settingsModal) {
                settingsBtn.addEventListener('click', () => {
                    settingsModal.style.display = 'flex';
                });
                closeSettingsModal.addEventListener('click', () => {
                    settingsModal.style.display = 'none';
                });
                settingsModal.addEventListener('click', (e) => {
                    if (e.target === settingsModal) {
                        settingsModal.style.display = 'none';
                    }
                });
            }

            // Modal Messages Épinglés
            const pinnedBar = document.getElementById('pinnedBar');
            const pinnedModal = document.getElementById('pinnedModal');
            const closePinnedModal = document.getElementById('closePinnedModal');

            if (pinnedBar && pinnedModal) {
                pinnedBar.addEventListener('click', () => {
                    pinnedModal.style.display = 'flex';
                    loadPinnedMessages();
                });
                closePinnedModal.addEventListener('click', () => {
                    pinnedModal.style.display = 'none';
                });
                pinnedModal.addEventListener('click', (e) => {
                    if (e.target === pinnedModal) {
                        pinnedModal.style.display = 'none';
                    }
                });
            }

            // Modal Recherche
            const searchBtn = document.getElementById('searchBtn');
            const searchModal = document.getElementById('searchModal');
            const closeSearchModal = document.getElementById('closeSearchModal');

            if (searchBtn && searchModal) {
                searchBtn.addEventListener('click', () => {
                    searchModal.style.display = 'flex';
                    document.getElementById('messageSearchInput').focus();
                });
                closeSearchModal.addEventListener('click', () => {
                    searchModal.style.display = 'none';
                });
                searchModal.addEventListener('click', (e) => {
                    if (e.target === searchModal) {
                        searchModal.style.display = 'none';
                    }
                });
            }

            // Recherche de membres
            const memberSearchInput = document.getElementById('memberSearchInput');
            if (memberSearchInput) {
                memberSearchInput.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase();
                    document.querySelectorAll('#membersList .member-item').forEach(item => {
                        const name = item.querySelector('.member-name').textContent.toLowerCase();
                        item.style.display = name.includes(query) ? 'flex' : 'none';
                    });
                });
            }

            // Recherche de messages
            const messageSearchInput = document.getElementById('messageSearchInput');
            if (messageSearchInput) {
                let searchTimeout;
                messageSearchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        searchMessages(e.target.value);
                    }, 300);
                });
            }

            // Quitter le canal
            const leaveChannelBtn = document.getElementById('leaveChannelBtn');
            if (leaveChannelBtn) {
                leaveChannelBtn.addEventListener('click', () => {
                    if (confirm('Êtes-vous sûr de vouloir quitter ce canal ?')) {
                        leaveChannel();
                    }
                });
            }
        }

        // Formulaire de paramètres
        function setupSettingsForm() {
            const form = document.getElementById('channelSettingsForm');
            const deleteBtn = document.getElementById('deleteChannelBtn');

            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(form);

                    try {
                        const response = await fetch(`/api/channels/${channelConfig.channelId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${channelConfig.authToken}`,
                                'X-CSRF-TOKEN': channelConfig.csrfToken
                            },
                            body: JSON.stringify({
                                name: formData.get('name'),
                                description: formData.get('description'),
                                type: formData.get('type')
                            })
                        });

                        if (response.ok) {
                            showToast('success', 'Succès', 'Canal mis à jour avec succès');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            const data = await response.json();
                            showToast('error', 'Erreur', data.message || 'Erreur lors de la mise à jour');
                        }
                    } catch (error) {
                        showToast('error', 'Erreur', 'Erreur de connexion');
                    }
                });
            }

            if (deleteBtn) {
                deleteBtn.addEventListener('click', async () => {
                    if (confirm('Êtes-vous sûr de vouloir supprimer ce canal ? Cette action est irréversible.')) {
                        try {
                            const response = await fetch(`/api/channels/${channelConfig.channelId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Authorization': `Bearer ${channelConfig.authToken}`,
                                    'X-CSRF-TOKEN': channelConfig.csrfToken
                                }
                            });

                            if (response.ok) {
                                showToast('success', 'Succès', 'Canal supprimé');
                                setTimeout(() => {
                                    window.location.href = '/channels';
                                }, 1000);
                            } else {
                                const data = await response.json();
                                showToast('error', 'Erreur', data.message || 'Erreur lors de la suppression');
                            }
                        } catch (error) {
                            showToast('error', 'Erreur', 'Erreur de connexion');
                        }
                    }
                });
            }
        }

        // Charger les messages épinglés
        async function loadPinnedMessages() {
            const container = document.getElementById('pinnedMessagesList');
            if (!container) return;

            try {
                const response = await fetch(`/api/channels/${channelConfig.channelId}/messages/pinned`, {
                    headers: {
                        'Authorization': `Bearer ${channelConfig.authToken}`
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.data && data.data.length > 0) {
                        container.innerHTML = data.data.map(msg => `
                            <div class="message-bubble" style="margin-bottom: 10px; padding: 12px;">
                                <div class="message-sender" style="color: var(--accent-color); margin-bottom: 4px;">
                                    ${escapeHtml(msg.sender?.name || 'Utilisateur')}
                                </div>
                                <div class="message-text">${escapeHtml(msg.content)}</div>
                                <div class="message-footer">
                                    <span class="message-time">${formatTime(msg.created_at)}</span>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 20px;">Aucun message épinglé</div>';
                    }
                }
            } catch (error) {
                container.innerHTML = '<div style="text-align: center; color: var(--danger-color); padding: 20px;">Erreur de chargement</div>';
            }
        }

        // Rechercher des messages
        async function searchMessages(query) {
            const container = document.getElementById('searchResults');
            if (!container || !query.trim()) {
                container.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 20px;">Entrez votre recherche pour trouver des messages</div>';
                return;
            }

            container.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Recherche...</div>';

            try {
                // Recherche locale dans les messages chargés
                if (window.channelApp && window.channelApp.messages) {
                    const results = window.channelApp.messages.filter(msg =>
                        msg.content && msg.content.toLowerCase().includes(query.toLowerCase())
                    );

                    if (results.length > 0) {
                        container.innerHTML = results.slice(0, 20).map(msg => `
                            <div class="message-bubble" style="margin-bottom: 10px; padding: 12px; cursor: pointer;" onclick="scrollToMessage(${msg.id})">
                                <div class="message-sender" style="color: var(--accent-color); margin-bottom: 4px;">
                                    ${escapeHtml(msg.sender?.name || 'Utilisateur')}
                                </div>
                                <div class="message-text">${highlightText(escapeHtml(msg.content), query)}</div>
                                <div class="message-footer">
                                    <span class="message-time">${formatTime(msg.created_at)}</span>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 20px;">Aucun résultat trouvé</div>';
                    }
                }
            } catch (error) {
                container.innerHTML = '<div style="text-align: center; color: var(--danger-color); padding: 20px;">Erreur de recherche</div>';
            }
        }

        // Surligner le texte recherché
        function highlightText(text, query) {
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, '<mark style="background: yellow; padding: 0 2px;">$1</mark>');
        }

        // Scroller vers un message
        function scrollToMessage(messageId) {
            const messageEl = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageEl) {
                document.getElementById('searchModal').style.display = 'none';
                messageEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                messageEl.style.animation = 'highlight 2s ease-out';
            }
        }

        // Quitter le canal
        async function leaveChannel() {
            try {
                const response = await fetch(`/api/channels/${channelConfig.channelId}/leave`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${channelConfig.authToken}`,
                        'X-CSRF-TOKEN': channelConfig.csrfToken
                    }
                });

                if (response.ok) {
                    showToast('success', 'Succès', 'Vous avez quitté le canal');
                    setTimeout(() => {
                        window.location.href = '/channels';
                    }, 1000);
                } else {
                    const data = await response.json();
                    showToast('error', 'Erreur', data.message || 'Erreur lors de la sortie du canal');
                }
            } catch (error) {
                showToast('error', 'Erreur', 'Erreur de connexion');
            }
        }

        // Afficher un toast
        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const icons = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <i class="${icons[type]} toast-icon"></i>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(toast);

            // Auto-remove après 5 secondes
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Utilitaires
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 1) return 'À l\'instant';
            if (minutes < 60) return `Il y a ${minutes}m`;
            if (hours < 24) return `Il y a ${hours}h`;
            if (days === 1) return 'Hier';
            if (days < 7) return `Il y a ${days}j`;

            return date.toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'short'
            });
        }
    </script>

    <style>
        /* Animation pour le highlight */
        @keyframes highlight {
            0% { background: rgba(251, 187, 42, 0.5); }
            100% { background: transparent; }
        }

        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        /* Styles pour les selects */
        select.message-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        /* S'assurer que le container prend toute la hauteur */
        .channel-chat-container {
            height: calc(100vh - 64px); /* Moins le header de l'app */
        }

        /* Masquer l'indicateur de chargement après init */
        .channel-messages-area.loaded .loading-messages {
            display: none;
        }
    </style>
</x-app-layout>
