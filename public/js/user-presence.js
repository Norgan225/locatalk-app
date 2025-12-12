// ====================================
// 🟢 Système de Statut en Temps Réel
// ====================================

class UserPresenceManager {
    constructor() {
        this.pusher = null;
        this.channel = null;
        this.userChannel = null;
        this.pingInterval = null;
        this.currentUserId = null;
        this.onlineUsers = new Map();
    }

    /**
     * Initialiser la connexion WebSocket
     */
    async init(userId, authToken) {
        this.currentUserId = userId;
        this.authToken = authToken;

        try {
            // Essayer d'utiliser Pusher/Laravel Echo
            console.log('🔌 [PRESENCE] Tentative de connexion WebSocket...');

            // Vérifier si Laravel Echo est disponible
            if (typeof Echo !== 'undefined') {
                console.log('🎯 [PRESENCE] Laravel Echo détecté, tentative de connexion...');
                this.setupEchoPresence();
            } else {
                // Fallback vers Pusher direct
                console.log('🔌 [PRESENCE] Laravel Echo non disponible, utilisation de Pusher direct...');
                this.setupPusherPresence();
            }
        } catch (error) {
            console.error('❌ [PRESENCE] Échec de la connexion WebSocket:', error);
            // Fallback vers polling
            this.startPresencePolling();
        }

        this.setupEventListeners();
        this.startHeartbeat();
        await this.setOnline();
    }

    /**
     * Helper to build fetch options with conditional Authorization and credentials
     */
    _fetchOptions(method = 'GET', body = null) {
        const headers = {
            'Content-Type': 'application/json'
        };

        if (this.authToken && this.authToken.length) {
            headers['Authorization'] = `Bearer ${this.authToken}`;
        }

        const opts = {
            method,
            headers,
            credentials: 'same-origin'
        };

        if (body) opts.body = JSON.stringify(body);
        return opts;
    }

    /**
     * Configurer la présence avec Laravel Echo
     */
    setupEchoPresence() {
        try {
            // Utiliser le canal de présence Laravel Echo
            const presenceChannel = Echo.join('presence');
            console.log('🎯 [PRESENCE] Canal presence Echo obtenu');

            presenceChannel
                .here((users) => {
                    console.log('🎉 [PRESENCE] here() appelé avec users:', users.map(u => ({id: u.id, name: u.name})));
                    users.forEach(user => {
                        this.onlineUsers.set(user.id, {
                            status: 'online',
                            last_activity: new Date().toISOString(),
                            device_type: 'unknown'
                        });
                        this.updateUI(user.id, { color: '#10b981', label: 'En ligne' });
                    });
                })
                .joining((user) => {
                    console.log('➕ [PRESENCE] joining() appelé pour user:', user.id, user.name);
                    this.onlineUsers.set(user.id, {
                        status: 'online',
                        last_activity: new Date().toISOString(),
                        device_type: 'unknown'
                    });
                    this.updateUI(user.id, { color: '#10b981', label: 'En ligne' });
                })
                .leaving((user) => {
                    console.log('➖ [PRESENCE] leaving() appelé pour user:', user.id, user.name);
                    this.onlineUsers.delete(user.id);
                    this.updateUI(user.id, { color: '#ef4444', label: 'Hors ligne' });
                })
                .error((error) => {
                    console.error('❌ [PRESENCE] Erreur Echo presence:', error);
                    this.startPresencePolling();
                });

        } catch (error) {
            console.error('❌ [PRESENCE] Impossible de configurer Echo presence:', error);
            this.startPresencePolling();
        }
    }

    /**
     * Configurer la présence avec Pusher direct
     */
    setupPusherPresence() {
        try {
            this.pusher = new Pusher(PUSHER_APP_KEY, {
                cluster: PUSHER_CLUSTER,
                wsHost: window.location.hostname,
                wsPort: 6001,
                forceTLS: false,
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'Authorization': `Bearer ${this.authToken}`
                    }
                }
            });

            // S'abonner au canal global des statuts
            this.channel = this.pusher.subscribe('user-status');

            // S'abonner au canal personnel
            this.userChannel = this.pusher.subscribe(`private-user.${this.currentUserId}`);

            console.log('🔌 [PRESENCE] Pusher configuré avec succès');

        } catch (error) {
            console.error('❌ [PRESENCE] Impossible de configurer Pusher:', error);
            this.startPresencePolling();
        }
    }

    /**
     * Configurer les listeners d'événements
     */
    setupEventListeners() {
        // Écouter les changements de statut (si canal disponible)
        if (this.channel) {
            this.channel.bind('status.changed', (data) => {
                this.handleStatusChange(data);
            });
        }

        // Détecter les déconnexions
        window.addEventListener('beforeunload', () => {
            this.setOffline();
        });

        // Détecter l'inactivité
        this.setupInactivityDetection();
    }

    /**
     * Démarrer le heartbeat (ping régulier)
     */
    startHeartbeat() {
        // Ping toutes les 30 secondes
        this.pingInterval = setInterval(() => {
            this.ping();
        }, 30000);
    }

    /**
     * Détecter l'inactivité de l'utilisateur
     */
    setupInactivityDetection() {
        let inactivityTimer;
        const INACTIVITY_TIMEOUT = 5 * 60 * 1000; // 5 minutes

        const resetTimer = () => {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                this.setAway();
            }, INACTIVITY_TIMEOUT);
        };

        // Réinitialiser le timer sur toute activité
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, resetTimer, true);
        });

        resetTimer();
    }

    /**
     * Définir l'utilisateur comme en ligne
     */
    async setOnline() {
        try {
            const response = await fetch('/api/status/online', this._fetchOptions('POST', { device_type: this.getDeviceType() }));

            const data = await response.json();
            this.updateUI(this.currentUserId, data.status);
        } catch (error) {
            console.error('Erreur lors de la mise en ligne:', error);
        }
    }

    /**
     * Définir l'utilisateur comme hors ligne
     */
    async setOffline() {
        try {
            await fetch('/api/status/offline', Object.assign(this._fetchOptions('POST', null), { keepalive: true }));
        } catch (error) {
            console.error('Erreur lors de la mise hors ligne:', error);
        }
    }

    /**
     * Définir l'utilisateur comme absent
     */
    async setAway() {
        try {
            const response = await fetch('/api/status/away', this._fetchOptions('POST', null));

            const data = await response.json();
            this.updateUI(this.currentUserId, data.status);
        } catch (error) {
            console.error('Erreur lors du passage en absent:', error);
        }
    }

    /**
     * Définir l'utilisateur comme occupé
     */
    async setBusy(customMessage = null) {
        try {
            const response = await fetch('/api/status/busy', this._fetchOptions('POST', { custom_message: customMessage }));

            const data = await response.json();
            this.updateUI(this.currentUserId, data.status);
        } catch (error) {
            console.error('Erreur lors du passage en occupé:', error);
        }
    }

    /**
     * Activer/désactiver le mode invisible
     */
    async toggleInvisible(invisible = true) {
        try {
            const response = await fetch('/api/status/invisible', this._fetchOptions('POST', { invisible }));

            const data = await response.json();
            console.log(data.message);
        } catch (error) {
            console.error('Erreur lors du changement de visibilité:', error);
        }
    }

    /**
     * Envoyer un ping (heartbeat)
     */
    async ping() {
        try {
            const res = await fetch('/api/status/ping', this._fetchOptions('POST', { device_type: this.getDeviceType() }));
            if (!res.ok) {
                console.warn('⚠️ [PRESENCE] ping non OK:', res.status);
            } else {
                // small info for debugging
                const d = await res.json().catch(() => null);
                if (d && d.last_activity) console.debug('🔔 [PRESENCE] ping ok, last_activity:', d.last_activity);
            }
        } catch (error) {
            console.error('Erreur lors du ping:', error);
        }
    }

    /**
     * Obtenir le statut d'un utilisateur
     */
    async getUserStatus(userId) {
        try {
            const response = await fetch(`/api/status/user/${userId}`, this._fetchOptions('GET'));
            if (!response.ok) return null;
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la récupération du statut:', error);
            return null;
        }
    }

    /**
     * Obtenir les statuts de plusieurs utilisateurs
     */
    async getBulkStatuses(userIds) {
        try {
            const response = await fetch('/api/status/bulk', this._fetchOptions('POST', { user_ids: userIds }));
            if (!response.ok) {
                console.warn('⚠️ [PRESENCE] getBulkStatuses non OK:', response.status);
                return [];
            }

            const data = await response.json();
            return data.statuses || [];
        } catch (error) {
            console.error('Erreur lors de la récupération des statuts:', error);
            return [];
        }
    }

    /**
     * Obtenir tous les utilisateurs en ligne
     */
    async getOnlineUsers() {
        try {
            const response = await fetch('/api/status/online', this._fetchOptions('GET'));
            if (!response.ok) {
                console.warn('⚠️ [PRESENCE] getOnlineUsers non OK:', response.status);
                return [];
            }

            const data = await response.json();
            return data.users || [];
        } catch (error) {
            console.error('Erreur lors de la récupération des utilisateurs en ligne:', error);
            return [];
        }
    }

    /**
     * Gérer un changement de statut
     */
    handleStatusChange(data) {
        console.log('Changement de statut:', data);

        this.onlineUsers.set(data.user_id, {
            status: data.status,
            last_activity: data.last_activity,
            custom_message: data.custom_message,
            device_type: data.device_type
        });

        this.updateUI(data.user_id, data.status_details);
    }

    /**
     * Mettre à jour l'UI avec le nouveau statut
     */
    updateUI(userId, statusDetails) {
        // Déterminer le statut bref en dehors de la boucle
        const inferredStatus = statusDetails.status || (statusDetails.label && statusDetails.label.toLowerCase().includes('en ligne') ? 'online' : 'offline');

        // Trouver tous les éléments de statut pour cet utilisateur
        // (widgets, user lists use data-user-status) et conversation list uses data-user-id on conversation-item
        const statusElements = [
            ...document.querySelectorAll(`[data-user-status="${userId}"]`),
            ...document.querySelectorAll(`.conversation-item[data-user-id="${userId}"]`)
        ];

        statusElements.forEach(element => {
            // Mettre à jour la couleur du badge
            const badge = element.querySelector('.status-badge');
            if (badge) {
                // For widgets we keep backgroundColor for the widget, but also sync class when relevant
                badge.style.backgroundColor = statusDetails.color;
                try {
                    badge.className = 'status-badge';
                    badge.classList.add(`status-${inferredStatus}`);
                } catch (e) {}
            }

            // Mettre à jour le texte du statut
            const statusText = element.querySelector('.status-text');
            if (statusText) {
                statusText.textContent = statusDetails.label;
            }

            // Mettre à jour le message personnalisé
            const customMessage = element.querySelector('.status-message');
            if (customMessage && statusDetails.custom_message) {
                customMessage.textContent = statusDetails.custom_message;
                customMessage.style.display = 'block';
            } else if (customMessage) {
                customMessage.style.display = 'none';
            }

            // Ajouter une classe pour l'animation
            element.classList.add('status-updated');
            setTimeout(() => {
                element.classList.remove('status-updated');
            }, 300);
        });

        // Émettre un événement personnalisé avec le statut inféré pour compatibilité
        window.dispatchEvent(new CustomEvent('user-status-changed', {
            detail: { userId, statusDetails, status: inferredStatus }
        }));
    }

    /**
     * Détecter le type d'appareil
     */
    getDeviceType() {
        const ua = navigator.userAgent;
        if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
            return 'tablet';
        }
        if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Démarrer le polling de présence (fallback quand WebSocket ne fonctionne pas)
     */
    startPresencePolling() {
        console.log('🔄 [PRESENCE] Démarrage du polling de présence (fallback)...');

        // Vérifier les statuts toutes les 30 secondes
        this.pollingInterval = setInterval(async () => {
            try {
                await this.pollUserStatuses();
            } catch (error) {
                console.error('Erreur lors du polling de présence:', error);
            }
        }, 30000);

        // Première vérification immédiate
        this.pollUserStatuses();
    }

    /**
     * Vérifier les statuts des utilisateurs via polling
     */
    async pollUserStatuses() {
        try {
            // Obtenir tous les utilisateurs en ligne
            const onlineUsers = await this.getOnlineUsers();
            console.log('🔄 [PRESENCE] Utilisateurs en ligne via polling:', onlineUsers.length);

            // Créer un Set des utilisateurs actuellement en ligne
            const currentOnlineIds = new Set(onlineUsers.map(u => u.user_id));

            // Identifier les changements
            const previousOnlineIds = new Set(this.onlineUsers.keys());

            // Nouveaux utilisateurs en ligne
            const newlyOnline = [...currentOnlineIds].filter(id => !previousOnlineIds.has(id));

            // Utilisateurs qui se sont déconnectés
            const newlyOffline = [...previousOnlineIds].filter(id => !currentOnlineIds.has(id));

            // Mettre à jour les statuts
            newlyOnline.forEach(userId => {
                const user = onlineUsers.find(u => u.user_id === userId);
                if (user) {
                    this.handleStatusChange({
                        user_id: userId,
                        status: 'online',
                        status_details: user.status_details,
                        last_activity: user.last_activity,
                        device_type: user.device_type
                    });
                }
            });

            newlyOffline.forEach(userId => {
                this.handleStatusChange({
                    user_id: userId,
                    status: 'offline',
                    status_details: { color: '#ef4444', label: 'Hors ligne' },
                    last_activity: new Date().toISOString()
                });
            });

            // Émettre des événements pour tous les utilisateurs actuellement en ligne
            // (nécessaire pour mettre à jour les headers de conversation après chargement)
            currentOnlineIds.forEach(userId => {
                const user = onlineUsers.find(u => u.user_id === userId);
                if (user) {
                    // Émettre l'événement même si l'utilisateur était déjà en ligne
                    this.handleStatusChange({
                        user_id: userId,
                        status: 'online',
                        status_details: user.status_details,
                        last_activity: user.last_activity,
                        device_type: user.device_type
                    });
                }
            });

        } catch (error) {
            console.error('Erreur lors du polling des statuts:', error);
        }
    }

    /**
     * Nettoyer et déconnecter
     */
    destroy() {
        if (this.pingInterval) {
            clearInterval(this.pingInterval);
        }

        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }

        if (this.channel) {
            this.pusher.unsubscribe('user-status');
        }

        if (this.userChannel) {
            this.pusher.unsubscribe(`private-user.${this.currentUserId}`);
        }

        if (this.pusher) {
            this.pusher.disconnect();
        }

        this.setOffline();
    }
}

// ====================================
// 🎨 Composant UI de Sélection de Statut
// ====================================

class StatusSelector {
    constructor(presenceManager) {
        this.presenceManager = presenceManager;
        this.currentStatus = 'online';
    }

    render(containerId) {
        const container = document.getElementById(containerId);

        container.innerHTML = `
            <div class="status-selector">
                <button class="current-status" id="statusToggle">
                    <span class="status-badge" style="background-color: #10b981;"></span>
                    <span class="status-text">En ligne</span>
                    <svg class="chevron" width="16" height="16" fill="currentColor">
                        <path d="M4 6l4 4 4-4"/>
                    </svg>
                </button>

                <div class="status-dropdown" id="statusDropdown" style="display: none;">
                    <button data-status="online">
                        <span class="status-badge" style="background-color: #10b981;"></span>
                        En ligne
                    </button>
                    <button data-status="away">
                        <span class="status-badge" style="background-color: #f59e0b;"></span>
                        Absent
                    </button>
                    <button data-status="busy">
                        <span class="status-badge" style="background-color: #ef4444;"></span>
                        Occupé
                    </button>
                    <button data-status="do_not_disturb">
                        <span class="status-badge" style="background-color: #8b5cf6;"></span>
                        Ne pas déranger
                    </button>
                    <hr>
                    <button data-status="invisible">
                        <span class="status-badge" style="background-color: #6b7280;"></span>
                        Mode invisible
                    </button>
                </div>
            </div>
        `;

        this.attachEvents();
    }

    attachEvents() {
        const toggle = document.getElementById('statusToggle');
        const dropdown = document.getElementById('statusDropdown');

        toggle.addEventListener('click', () => {
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });

        const buttons = dropdown.querySelectorAll('button[data-status]');
        buttons.forEach(button => {
            button.addEventListener('click', async () => {
                const status = button.dataset.status;
                await this.changeStatus(status);
                dropdown.style.display = 'none';
            });
        });

        // Fermer le dropdown si on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.status-selector')) {
                dropdown.style.display = 'none';
            }
        });
    }

    async changeStatus(status) {
        switch (status) {
            case 'online':
                await this.presenceManager.setOnline();
                break;
            case 'away':
                await this.presenceManager.setAway();
                break;
            case 'busy':
                await this.presenceManager.setBusy();
                break;
            case 'do_not_disturb':
                await this.presenceManager.setBusy('Ne pas déranger');
                break;
            case 'invisible':
                await this.presenceManager.toggleInvisible(true);
                break;
        }
    }
}

// ====================================
// 📊 Widget Liste d'Utilisateurs En Ligne
// ====================================

async function renderOnlineUsersList(containerId, presenceManager) {
    const container = document.getElementById(containerId);
    const users = await presenceManager.getOnlineUsers();

    container.innerHTML = `
        <div class="online-users-widget">
            <h3>En ligne (${users.length})</h3>
            <div class="users-list">
                ${users.map(user => `
                    <div class="user-item" data-user-status="${user.user_id}">
                        <img src="${user.user_avatar || '/default-avatar.png'}" alt="${user.user_name}" class="avatar">
                        <div class="user-info">
                            <span class="user-name">${user.user_name}</span>
                            <div class="status-container">
                                <span class="status-badge" style="background-color: ${user.status_details.color};"></span>
                                <span class="status-text">${user.status_details.label}</span>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

// ====================================
// 🚀 Initialisation
// ====================================

// Exemple d'utilisation
document.addEventListener('DOMContentLoaded', async () => {
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    const authToken = localStorage.getItem('auth_token');

    if (userId && authToken) {
        // Initialiser le gestionnaire de présence
        const presenceManager = new UserPresenceManager();
        await presenceManager.init(userId, authToken);

        // Initialiser le sélecteur de statut
        const statusSelector = new StatusSelector(presenceManager);
        statusSelector.render('statusSelectorContainer');

        // Afficher la liste des utilisateurs en ligne
        await renderOnlineUsersList('onlineUsersContainer', presenceManager);

        // Écouter les changements de statut globaux
        window.addEventListener('user-status-changed', (event) => {
            console.log('Statut changé:', event.detail);
        });

        // Nettoyer à la fermeture
        window.addEventListener('beforeunload', () => {
            presenceManager.destroy();
        });
    }
});
