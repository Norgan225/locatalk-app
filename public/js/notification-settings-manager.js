/**
 * 🔔 Gestionnaire des Paramètres de Notifications
 * Gère les permissions navigateur, les sons et les préférences
 */
class NotificationSettingsManager {
    constructor() {
        this.soundManager = null;
        this.soundEnabled = true;
        this.selectedSound = 'gentle';
        this.browserNotificationsEnabled = false;

        // Charger les préférences sauvegardées
        this.loadPreferences();

        console.log('🔔 NotificationSettingsManager initialisé');
    }

    /**
     * Initialiser le gestionnaire
     */
    async init() {
        try {
            // Initialiser le SoundManager
            this.soundManager = new SoundManager();
            await this.soundManager.init();

            // Mettre à jour l'état des permissions
            this.updateBrowserPermissionStatus();

            console.log('✅ NotificationSettingsManager prêt');
        } catch (error) {
            console.error('❌ Erreur init NotificationSettingsManager:', error);
        }
    }

    /**
     * Vérifier l'état des permissions navigateur
     */
    updateBrowserPermissionStatus() {
        if ('Notification' in window) {
            this.browserNotificationsEnabled = Notification.permission === 'granted';
            return Notification.permission;
        }
        return 'unsupported';
    }

    /**
     * Demander la permission pour les notifications navigateur
     */
    async requestPermission() {
        if (!('Notification' in window)) {
            throw new Error('Les notifications ne sont pas supportées par ce navigateur');
        }

        if (Notification.permission === 'granted') {
            this.browserNotificationsEnabled = true;
            return 'granted';
        }

        if (Notification.permission !== 'denied') {
            const permission = await Notification.requestPermission();
            this.browserNotificationsEnabled = permission === 'granted';
            this.savePreferences();
            return permission;
        }

        throw new Error('Les notifications ont été bloquées. Veuillez les réactiver dans les paramètres du navigateur.');
    }

    /**
     * Afficher une notification navigateur
     */
    showBrowserNotification(title, options = {}) {
        if (!this.browserNotificationsEnabled) {
            console.warn('🔔 Notifications navigateur désactivées');
            return;
        }

        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification(title, {
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                ...options
            });

            // Fermer après 5 secondes
            setTimeout(() => notification.close(), 5000);

            return notification;
        }
    }

    /**
     * Jouer un son de notification
     */
    playSound(soundName = null) {
        if (!this.soundEnabled) {
            console.log('🔇 Sons désactivés');
            return;
        }

        const sound = soundName || this.selectedSound;
        if (this.soundManager) {
            this.soundManager.play(sound);
        }
    }

    /**
     * Tester un son
     */
    testSound(soundName) {
        if (this.soundManager) {
            console.log('🎵 Test du son:', soundName);
            this.soundManager.play(soundName);
        }
    }

    /**
     * Changer le son sélectionné
     */
    setSound(soundName) {
        this.selectedSound = soundName;
        if (this.soundManager) {
            this.soundManager.setSound(soundName);
        }
        this.savePreferences();
        console.log('✅ Son changé:', soundName);
    }

    /**
     * Activer/désactiver les sons
     */
    setSoundEnabled(enabled) {
        this.soundEnabled = enabled;
        if (this.soundManager) {
            this.soundManager.setEnabled(enabled);
        }
        this.savePreferences();
        console.log('🔊 Sons', enabled ? 'activés' : 'désactivés');
    }

    /**
     * Obtenir l'état de la permission
     */
    getPermissionStatus() {
        if (!('Notification' in window)) {
            return 'unsupported';
        }
        return Notification.permission;
    }

    /**
     * Obtenir un texte lisible du statut
     */
    getPermissionStatusText() {
        const status = this.getPermissionStatus();
        const texts = {
            'granted': 'Activé',
            'denied': 'Bloqué',
            'default': 'Désactivé',
            'unsupported': 'Non supporté'
        };
        return texts[status] || 'Inconnu';
    }

    /**
     * Sauvegarder les préférences dans localStorage
     */
    savePreferences() {
        const prefs = {
            soundEnabled: this.soundEnabled,
            selectedSound: this.selectedSound,
            browserNotificationsEnabled: this.browserNotificationsEnabled
        };
        localStorage.setItem('notificationPreferences', JSON.stringify(prefs));
        console.log('💾 Préférences sauvegardées:', prefs);
    }

    /**
     * Charger les préférences depuis localStorage
     */
    loadPreferences() {
        try {
            const saved = localStorage.getItem('notificationPreferences');
            if (saved) {
                const prefs = JSON.parse(saved);
                this.soundEnabled = prefs.soundEnabled ?? true;
                this.selectedSound = prefs.selectedSound ?? 'gentle';
                console.log('📂 Préférences chargées:', prefs);
            }
        } catch (error) {
            console.warn('⚠️ Erreur chargement préférences:', error);
        }
    }
}

// Export global
window.NotificationSettingsManager = NotificationSettingsManager;
