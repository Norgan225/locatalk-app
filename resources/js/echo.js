import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Activer le debug Pusher
Pusher.logToConsole = true;

// Fonction d'initialisation d'Echo appelée depuis la vue
window.initializeEcho = function(appKey, authToken) {
    console.log('🔌 [ECHO] Initialisation Echo avec key:', appKey);
    console.log('🔌 [ECHO] Auth token présent:', !!authToken);

    if (window.Echo) {
        console.log('🔌 [ECHO] Echo déjà initialisé');
        return window.Echo;
    }

    // Récupérer le CSRF token depuis la meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    console.log('🔌 [ECHO] CSRF token présent:', !!csrfToken);

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: appKey,
        wsHost: window.location.hostname,
        wsPort: 8080,
        wssPort: 8080,
        forceTLS: false,
        encrypted: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        }
    });

    console.log('✅ [ECHO] Echo initialisé avec succès');

    // Écouter les événements de connexion
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ [ECHO] WebSocket connecté!');
    });

    window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('❌ [ECHO] Erreur WebSocket:', err);
    });

    return window.Echo;
};

// Exporter la clé Reverb pour que la vue puisse l'utiliser
window.REVERB_APP_KEY = import.meta.env.VITE_REVERB_APP_KEY || 'ctpuhe1pkav5slox0g5v';
console.log('🔑 [ECHO] REVERB_APP_KEY:', window.REVERB_APP_KEY);
