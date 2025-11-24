import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// === Laravel Echo & Pusher ===
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
	broadcaster: 'pusher',
	key: 'VOTRE_PUSHER_APP_KEY', // Remplacer par la vraie clé
	cluster: 'VOTRE_PUSHER_APP_CLUSTER', // Remplacer par le vrai cluster
	wsHost: window.location.hostname,
	wsPort: 6001,
	forceTLS: false,
	disableStats: true,
	authEndpoint: '/broadcasting/auth',
	auth: {
		headers: {
			Authorization: `Bearer ${localStorage.getItem('auth_token')}`
		}
	}
});
