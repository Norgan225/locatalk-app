/**
 * 🎵 Générateur de Sons pour Notifications
 * Sons synthétiques pour les notifications
 */

class SoundManager {
    constructor() {
        this.audioContext = null;
        this.isEnabled = true;
        this.volume = 0.3;
        this.currentSound = 'bell';

        // Sons disponibles
        this.sounds = {
            bell: this.createBellSound.bind(this),
            chime: this.createChimeSound.bind(this),
            notification: this.createNotificationSound.bind(this),
            gentle: this.createGentleSound.bind(this)
        };

        console.log('🎵 SoundManager initialisé');
    }

    /**
     * Initialiser l'AudioContext
     */
    async init() {
        try {
            // Créer AudioContext seulement après une interaction utilisateur
            if (!this.audioContext) {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }

            // Résumer si suspendu
            if (this.audioContext.state === 'suspended') {
                await this.audioContext.resume();
            }

            console.log('🎵 AudioContext initialisé');
        } catch (error) {
            console.warn('🎵 Erreur AudioContext:', error);
        }
    }

    /**
     * Jouer un son (alias pour compatibilité)
     */
    async playSound(soundName = null) {
        return this.play(soundName);
    }

    /**
     * Jouer un son
     */
    async play(soundName = null) {
        if (!this.isEnabled) {
            console.log('🎵 Sons désactivés');
            return;
        }

        const sound = soundName || this.currentSound;
        if (!this.sounds[sound]) {
            console.warn('🎵 Son non trouvé:', sound);
            return;
        }

        try {
            await this.init();

            if (this.audioContext && this.audioContext.state === 'running') {
                console.log('🎵 Joue son:', sound);
                this.sounds[sound]();
            } else {
                console.warn('🎵 AudioContext pas prêt, état:', this.audioContext?.state);
            }
        } catch (error) {
            console.error('🎵 Erreur lors de la lecture du son:', error);
        }
    }

    /**
     * Son de cloche (par défaut)
     */
    createBellSound() {
        const oscillator = this.audioContext.createOscillator();
        const gainNode = this.audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(this.audioContext.destination);

        // Fréquence de cloche
        oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime);
        oscillator.frequency.exponentialRampToValueAtTime(600, this.audioContext.currentTime + 0.1);

        // Volume
        gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
        gainNode.gain.linearRampToValueAtTime(this.volume, this.audioContext.currentTime + 0.01);
        gainNode.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + 0.3);

        oscillator.start();
        oscillator.stop(this.audioContext.currentTime + 0.3);
    }

    /**
     * Son de carillon
     */
    createChimeSound() {
        const notes = [523.25, 659.25, 783.99]; // Do, Mi, Sol

        notes.forEach((freq, index) => {
            setTimeout(() => {
                const oscillator = this.audioContext.createOscillator();
                const gainNode = this.audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(this.audioContext.destination);

                oscillator.frequency.setValueAtTime(freq, this.audioContext.currentTime);
                oscillator.type = 'sine';

                gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
                gainNode.gain.linearRampToValueAtTime(this.volume * 0.8, this.audioContext.currentTime + 0.01);
                gainNode.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + 0.2);

                oscillator.start();
                oscillator.stop(this.audioContext.currentTime + 0.2);
            }, index * 100);
        });
    }

    /**
     * Son de notification moderne
     */
    createNotificationSound() {
        const oscillator1 = this.audioContext.createOscillator();
        const oscillator2 = this.audioContext.createOscillator();
        const gainNode = this.audioContext.createGain();

        oscillator1.connect(gainNode);
        oscillator2.connect(gainNode);
        gainNode.connect(this.audioContext.destination);

        // Deux oscillateurs pour un son plus riche
        oscillator1.frequency.setValueAtTime(1000, this.audioContext.currentTime);
        oscillator2.frequency.setValueAtTime(1200, this.audioContext.currentTime);

        oscillator1.type = 'square';
        oscillator2.type = 'sawtooth';

        gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
        gainNode.gain.linearRampToValueAtTime(this.volume * 0.6, this.audioContext.currentTime + 0.01);
        gainNode.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + 0.15);

        oscillator1.start();
        oscillator2.start();
        oscillator1.stop(this.audioContext.currentTime + 0.15);
        oscillator2.stop(this.audioContext.currentTime + 0.15);
    }

    /**
     * Son doux et discret
     */
    createGentleSound() {
        const oscillator = this.audioContext.createOscillator();
        const gainNode = this.audioContext.createGain();
        const filter = this.audioContext.createBiquadFilter();

        oscillator.connect(filter);
        filter.connect(gainNode);
        gainNode.connect(this.audioContext.destination);

        // Filtre passe-bas pour un son doux
        filter.type = 'lowpass';
        filter.frequency.setValueAtTime(800, this.audioContext.currentTime);

        oscillator.frequency.setValueAtTime(440, this.audioContext.currentTime);
        oscillator.type = 'sine';

        gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
        gainNode.gain.linearRampToValueAtTime(this.volume * 0.4, this.audioContext.currentTime + 0.05);
        gainNode.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + 1.0);

        oscillator.start();
        oscillator.stop(this.audioContext.currentTime + 1.0);
    }

    /**
     * Changer le son actuel
     */
    setSound(soundName) {
        if (this.sounds[soundName]) {
            this.currentSound = soundName;
            console.log('🎵 Son changé:', soundName);
        }
    }

    /**
     * Activer/désactiver les sons
     */
    setEnabled(enabled) {
        this.isEnabled = enabled;
        console.log('🎵 Sons', enabled ? 'activés' : 'désactivés');
    }

    /**
     * Changer le volume
     */
    setVolume(volume) {
        this.volume = Math.max(0, Math.min(1, volume));
        console.log('🎵 Volume changé:', Math.round(this.volume * 100) + '%');
    }

    /**
     * Obtenir la liste des sons disponibles
     */
    getAvailableSounds() {
        return Object.keys(this.sounds);
    }
}

// Export global
window.SoundManager = SoundManager;
