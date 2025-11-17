/**
 * 🔔 Gestionnaire de Notifications
 * Popups pour nouveaux messages et événements avec sons
 */

class NotificationManager {
    constructor() {
        this.isSupported = 'Notification' in window;
        this.isGranted = false;
        this.isEnabled = false;
        this.currentConversationId = null;
        this.soundEnabled = true;
        this.selectedSound = 'bell';

        // Initialiser le gestionnaire de sons
        this.soundManager = new SoundManager();

        console.log('🔔 NotificationManager initialisé, support:', this.isSupported);
    }

    /**
     * Initialisation du gestionnaire
     */
    async init() {
        if (!this.isSupported) {
            console.warn('🔔 Notifications non supportées par ce navigateur');
            return;
        }

        // Vérifier l'état actuel des permissions
        this.checkPermission();

        // Initialiser le gestionnaire de sons
        await this.soundManager.init();

        // Écouter les changements de focus pour activer/désactiver les notifications
        window.addEventListener('focus', () => {
            this.isEnabled = false; // Désactiver quand la fenêtre est focus
        });

        window.addEventListener('blur', () => {
            this.isEnabled = true; // Activer quand la fenêtre perd le focus
        });

        // Écouter les nouveaux messages
        this.setupMessageListeners();
    }

    /**
     * Vérifier l'état des permissions
     */
    checkPermission() {
        this.isGranted = Notification.permission === 'granted';
        console.log('🔔 Permission notifications:', Notification.permission);
    }

    /**
     * Demander la permission pour les notifications
     */
    async requestPermission() {
        if (!this.isSupported) {
            alert('Votre navigateur ne supporte pas les notifications.');
            return false;
        }

        try {
            const permission = await Notification.requestPermission();
            this.isGranted = permission === 'granted';

            if (this.isGranted) {
                console.log('✅ Permission notifications accordée');
                this.showTestNotification();
            } else {
                console.log('❌ Permission notifications refusée');
                alert('Les notifications sont désactivées. Vous ne recevrez pas de notifications pour les nouveaux messages.');
            }

            return this.isGranted;
        } catch (error) {
            console.error('Erreur lors de la demande de permission:', error);
            return false;
        }
    }

    /**
     * Afficher une notification de test
     */
    showTestNotification() {
        this.showNotification(
            'Notifications activées !',
            'Vous recevrez maintenant des notifications pour les nouveaux messages.',
            '🔔'
        );
    }

    /**
     * Afficher une notification
     */
    showNotification(title, body, icon = '💬', tag = null) {
        if (!this.isSupported || !this.isGranted) {
            return;
        }

        // Ne pas afficher si la fenêtre est focus et que c'est pour la conversation actuelle
        if (!this.isEnabled && tag && tag.includes(`conv-${this.currentConversationId}`)) {
            return;
        }

        const options = {
            body: body,
            icon: '/favicon.ico', // Ou une icône personnalisée
            badge: '/favicon.ico',
            tag: tag || 'locatalk-message',
            requireInteraction: false,
            silent: false,
            timestamp: Date.now()
        };

        try {
            const notification = new Notification(title, options);

            // Jouer le son de notification
            if (this.soundEnabled) {
                this.soundManager.play(this.selectedSound);
            }

            // Animer le bouton de notification
            this.animateNotificationButton();

            // Auto-fermer après 5 secondes
            setTimeout(() => {
                notification.close();
            }, 5000);

            // Gérer le clic sur la notification
            notification.onclick = () => {
                window.focus();
                notification.close();
            };

            return notification;
        } catch (error) {
            console.error('Erreur lors de l\'affichage de la notification:', error);
        }
    }

    /**
     * Configurer les écouteurs pour les nouveaux messages
     */
    setupMessageListeners() {
        // Écouter les nouveaux messages via WebSocket ou polling
        window.addEventListener('new-message-received', (event) => {
            const { message, conversationId, senderName } = event.detail;

            // Ne pas notifier pour nos propres messages
            if (message.is_sent_by_me || message.sender_id === window.messagingApp?.userId) {
                return;
            }

            // Ne pas notifier si on est dans la conversation
            if (this.currentConversationId === conversationId && !document.hidden) {
                return;
            }

            this.notifyNewMessage(message, senderName, conversationId);
        });

        // Écouter les changements de conversation
        if (window.messagingApp) {
            // Sauvegarder l'ancienne méthode pour l'encapsuler
            const originalSelectConversation = window.messagingApp.selectConversation;
            window.messagingApp.selectConversation = async (userId) => {
                this.currentConversationId = userId;
                return originalSelectConversation.call(window.messagingApp, userId);
            };
        }
    }

    /**
     * Notifier d'un nouveau message
     */
    notifyNewMessage(message, senderName, conversationId) {
        const title = senderName || 'Nouveau message';
        const body = this.formatMessagePreview(message);
        const tag = `conv-${conversationId}`;

        this.showNotification(title, body, '💬', tag);
    }

    /**
     * Formater l'aperçu du message pour la notification
     */
    formatMessagePreview(message) {
        if (!message.content) {
            return 'Nouveau message';
        }

        // Tronquer le message comme dans la prévisualisation
        const content = message.content.substring(0, 50);
        return content.length < message.content.length ? content + '...' : content;
    }

    /**
     * Vérifier si les notifications sont disponibles
     */
    isAvailable() {
        return this.isSupported && this.isGranted;
    }

    /**
     * Obtenir le statut des notifications
     */
    getStatus() {
        if (!this.isSupported) {
            return 'unsupported';
        }

        return Notification.permission;
    }

    /**
     * Ouvrir les paramètres de notifications du navigateur
     */
    openSettings() {
        // Cette méthode dépend du navigateur
        alert('Pour modifier les paramètres de notifications, allez dans les paramètres de votre navigateur.');
    }

    /**
     * Activer/désactiver les sons
     */
    setSoundEnabled(enabled) {
        this.soundEnabled = enabled;
        this.soundManager.setEnabled(enabled);
        console.log('🔔 Sons', enabled ? 'activés' : 'désactivés');
    }

    /**
     * Changer le son de notification
     */
    setSound(soundName) {
        if (this.soundManager.getAvailableSounds().includes(soundName)) {
            this.selectedSound = soundName;
            this.soundManager.setSound(soundName);
            console.log('🔔 Son changé:', soundName);
        }
    }

    /**
     * Tester un son
     */
    async testSound(soundName = null) {
        // S'assurer que le gestionnaire est initialisé
        if (!this.soundManager.audioContext) {
            await this.init();
        }
        this.soundManager.play(soundName || this.selectedSound);
    }

    /**
     * Obtenir la liste des sons disponibles
     */
    getAvailableSounds() {
        return this.soundManager.getAvailableSounds();
    }

    /**
     * Animer le bouton de notification
     */
    animateNotificationButton() {
        const button = document.getElementById('notificationToggleBtn');
        if (button) {
            button.classList.add('notification-alert');
            setTimeout(() => {
                button.classList.remove('notification-alert');
            }, 600);
        }
    }
}

// Initialisation globale
if (typeof window !== 'undefined') {
    window.notificationManager = new NotificationManager();
    // Initialiser après une interaction utilisateur pour l'AudioContext
    let initialized = false;

    const initNotifications = async () => {
        if (!initialized) {
            console.log('🔔 Initialisation du système de notifications...');
            await window.notificationManager.init();
            initialized = true;
            console.log('✅ Système de notifications initialisé');

            // Fonction de test globale
            window.testNotificationSound = async (sound = 'bell') => {
                console.log('🔔 Test du son:', sound);
                await window.notificationManager.testSound(sound);
            };
        }
    };

    // Initialiser dès que possible (au premier clic ou interaction)
    document.addEventListener('click', initNotifications, { once: true });
    document.addEventListener('keydown', initNotifications, { once: true });
    document.addEventListener('touchstart', initNotifications, { once: true });

    // Essayer d'initialiser immédiatement (marchera si l'utilisateur a déjà interagi)
    initNotifications();
}
