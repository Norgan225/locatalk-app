/**
 * 🎥 WebRTC Call Manager - Appels vidéo peer-to-peer
 * Utilise Reverb pour la signalisation
 */

class WebRTCCallManager {
    constructor(userId, authToken) {
        this.userId = userId;
        this.authToken = authToken;
        this.peerConnection = null;
        this.localStream = null;
        this.remoteStream = null;
        this.currentCall = null;
        this.isInitiator = false;
        this.callType = 'video'; // 'audio' ou 'video'
        this.isMinimized = false;

        // Configuration ICE (STUN servers)
        this.iceConfig = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' },
                { urls: 'stun:stun2.l.google.com:19302' },
            ]
        };

        console.log('🎥 WebRTC Call Manager initialisé');

        // Les listeners seront configurés au premier appel (quand Echo est prêt)
        this.listenersConfigured = false;
    }

    /**
     * Configurer les listeners Reverb pour les appels entrants (appelé automatiquement)
     */
    setupSignalingListeners() {
        // Si déjà configurés, ne rien faire
        if (this.listenersConfigured) {
            return;
        }

        // Vérifier si Echo (variable globale) est disponible
        if (typeof Echo === 'undefined' || !Echo || typeof Echo.private !== 'function') {
            console.warn('⚠️ Laravel Echo non disponible, les appels entrants ne seront pas reçus pour le moment.');
            return;
        }

        console.log('👂 Configuration des listeners de signalisation...');

        try {
            Echo.private(`user.${this.userId}`)
                .listen('.call.signal', (event) => {
                    console.log('📡 Signal reçu:', event);
                    this.handleIncomingSignal(event.type, event.data);
                });

            this.listenersConfigured = true;
            console.log('✅ Listeners de signalisation configurés');
        } catch (error) {
            console.error('❌ Erreur configuration listeners:', error);
        }
    }

    /**
     * Gérer les signaux WebRTC entrants
     */
    async handleIncomingSignal(type, data) {
        console.log(`📥 Traitement signal: ${type}`, data);

        switch (type) {
            case 'call-offer':
                await this.handleIncomingCall(data);
                break;

            case 'call-answer':
                await this.handleCallAnswer(data);
                break;

            case 'ice-candidate':
                await this.handleIceCandidate(data);
                break;

            case 'call-rejected':
                this.handleCallRejected(data);
                break;

            case 'call-ended':
                this.handleCallEnded(data);
                break;

            default:
                console.warn('⚠️ Type de signal inconnu:', type);
        }
    }

    /**
     * Initier un appel vidéo ou audio
     */
    async initiateCall(receiverId, callType = 'video') {
        console.log(`📞 Initiation appel ${callType} vers:`, receiverId);
        this.isInitiator = true;
        this.callType = callType;

        // Configurer les listeners s'ils ne le sont pas déjà
        this.setupSignalingListeners();

        try {
            // Créer l'enregistrement de l'appel dans la DB
            const response = await fetch('/calls', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    receiver_id: receiverId,
                    type: callType
                })
            });

            if (!response.ok) {
                throw new Error('Erreur lors de la création de l\'appel');
            }

            const data = await response.json();
            this.currentCall = data.data;

            // Obtenir l'accès à la caméra et au micro
            await this.getLocalStream();

            // Afficher l'interface d'appel
            this.showCallUI('outgoing');

            // Créer la connexion peer
            await this.createPeerConnection();

            // Créer et envoyer l'offre
            const offer = await this.peerConnection.createOffer({
                offerToReceiveVideo: true,
                offerToReceiveAudio: true
            });
            await this.peerConnection.setLocalDescription(offer);

            // Envoyer l'offre via Reverb
            this.sendSignal('call-offer', {
                callId: this.currentCall.id,
                receiverId: receiverId,
                offer: offer
            });

            console.log('✅ Offre d\'appel envoyée');

        } catch (error) {
            console.error('❌ Erreur initiation appel:', error);
            this.showToast('Impossible de lancer l\'appel: ' + error.message, 'error');
            this.endCall();
        }
    }

    /**
     * Obtenir le flux local (caméra + micro ou audio seul)
     */
    async getLocalStream() {
        try {
            const constraints = {
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true
                }
            };

            // Ajouter vidéo seulement si appel vidéo
            if (this.callType === 'video') {
                constraints.video = {
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                };
            }

            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);

            console.log('✅ Flux local obtenu');
            return this.localStream;

        } catch (error) {
            console.error('❌ Erreur accès média:', error);
            throw new Error('Impossible d\'accéder à la caméra/micro');
        }
    }

    /**
     * Créer la connexion peer-to-peer
     */
    async createPeerConnection() {
        this.peerConnection = new RTCPeerConnection(this.iceConfig);

        // Ajouter les tracks locaux
        this.localStream.getTracks().forEach(track => {
            this.peerConnection.addTrack(track, this.localStream);
        });

        // Gérer les tracks distants
        this.peerConnection.ontrack = (event) => {
            console.log('📺 Track distant reçu');
            this.remoteStream = event.streams[0];
            this.updateRemoteVideo();
        };

        // Gérer les candidats ICE
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                console.log('🧊 Candidat ICE:', event.candidate);
                this.sendSignal('ice-candidate', {
                    callId: this.currentCall.id,
                    candidate: event.candidate
                });
            }
        };

        // Gérer l'état de la connexion
        this.peerConnection.onconnectionstatechange = () => {
            console.log('🔗 État connexion:', this.peerConnection.connectionState);

            if (this.peerConnection.connectionState === 'connected') {
                this.showToast('Appel connecté', 'success');
                this.updateCallUI('connected');
            } else if (this.peerConnection.connectionState === 'failed') {
                this.showToast('Connexion échouée', 'error');
                this.endCall();
            }
        };

        console.log('✅ Peer connection créée');
    }

    /**
     * Envoyer un signal via Reverb
     */
    sendSignal(type, data) {
        if (!window.Echo) {
            console.error('❌ Echo non disponible');
            return;
        }

        // Utiliser l'API pour envoyer le signal
        fetch('/api/call-signal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Authorization': `Bearer ${this.authToken}`
            },
            body: JSON.stringify({
                type: type,
                data: data
            })
        }).catch(err => console.error('Erreur envoi signal:', err));
    }

    /**
     * Afficher l'UI d'appel
     */
    showCallUI(state) {
        // Créer l'overlay d'appel
        const overlay = document.createElement('div');
        overlay.id = 'webrtc-call-overlay';
        overlay.className = this.isMinimized ? 'minimized' : '';
        overlay.innerHTML = `
            <div class="call-container ${this.callType === 'audio' ? 'audio-only' : ''}">
                <div class="call-header">
                    <h3 id="call-status">${state === 'outgoing' ? (this.callType === 'audio' ? 'Appel audio...' : 'Appel vidéo...') : 'Appel entrant'}</h3>
                    <button id="minimize-call" class="minimize-btn" title="Réduire">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                            <path d="M19 13H5v-2h14v2z"/>
                        </svg>
                    </button>
                </div>

                <div class="video-container" style="display: ${this.callType === 'video' ? 'block' : 'none'}">
                    <video id="remote-video" autoplay playsinline></video>
                    <video id="local-video" autoplay playsinline muted></video>
                </div>

                <div class="audio-avatar" style="display: ${this.callType === 'audio' ? 'flex' : 'none'}">
                    <div class="avatar-circle">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="white">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <p class="audio-status">Appel audio en cours</p>
                </div>

                <div class="call-controls">
                    <button id="toggle-mic" class="control-btn" title="Micro">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                            <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/>
                            <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                        </svg>
                    </button>

                    <button id="end-call" class="control-btn danger" title="Raccrocher">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                            <path d="M12 9c-1.6 0-3.15.25-4.6.72v3.1c0 .39-.23.74-.56.9-.98.49-1.87 1.12-2.66 1.85-.18.18-.43.28-.7.28-.28 0-.53-.11-.71-.29L.29 13.08c-.18-.17-.29-.42-.29-.7 0-.28.11-.53.29-.71C3.34 8.78 7.46 7 12 7s8.66 1.78 11.71 4.67c.18.18.29.43.29.71 0 .28-.11.53-.29.71l-2.48 2.48c-.18.18-.43.29-.71.29-.27 0-.52-.11-.7-.28-.79-.74-1.68-1.36-2.66-1.85-.33-.16-.56-.5-.56-.9v-3.1C15.15 9.25 13.6 9 12 9z"/>
                        </svg>
                    </button>

                    <button id="toggle-camera" class="control-btn" title="Caméra" style="display: ${this.callType === 'video' ? 'flex' : 'none'}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                            <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        // Styles
        const style = document.createElement('style');
        style.textContent = `
            #webrtc-call-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: #000;
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            #webrtc-call-overlay.minimized {
                width: 320px;
                height: 240px;
                bottom: 20px;
                right: 20px;
                top: auto;
                left: auto;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            }

            #webrtc-call-overlay.minimized .call-container {
                height: 100%;
            }

            #webrtc-call-overlay.minimized .call-header {
                padding: 10px;
                cursor: pointer;
            }

            #webrtc-call-overlay.minimized .call-header h3 {
                font-size: 14px;
            }

            #webrtc-call-overlay.minimized #local-video {
                width: 100px;
                height: 75px;
                bottom: 10px;
                right: 10px;
            }

            .call-container {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .call-container.audio-only {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .call-header {
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: rgba(0, 0, 0, 0.5);
            }

            .call-header h3 {
                color: white;
                margin: 0;
                font-size: 18px;
                flex: 1;
                text-align: center;
            }

            .minimize-btn {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                border-radius: 8px;
                padding: 8px;
                cursor: pointer;
                transition: all 0.2s;
            }

            .minimize-btn:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            .audio-avatar {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 20px;
            }

            .avatar-circle {
                width: 160px;
                height: 160px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                animation: pulse 2s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% { transform: scale(1); opacity: 0.8; }
                50% { transform: scale(1.05); opacity: 1; }
            }

            .audio-status {
                color: white;
                font-size: 18px;
                font-weight: 500;
            }

            .video-container {
                flex: 1;
                position: relative;
                background: #1a1a1a;
            }

            #remote-video {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            #local-video {
                position: absolute;
                bottom: 20px;
                right: 20px;
                width: 200px;
                height: 150px;
                border-radius: 12px;
                border: 2px solid rgba(255, 255, 255, 0.3);
                object-fit: cover;
            }

            .call-controls {
                padding: 30px;
                display: flex;
                gap: 20px;
                justify-content: center;
                background: rgba(0, 0, 0, 0.7);
            }

            .control-btn {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                border: none;
                background: rgba(255, 255, 255, 0.2);
                cursor: pointer;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .control-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .control-btn.danger {
                background: #ef4444;
            }

            .control-btn.danger:hover {
                background: #dc2626;
            }

            .control-btn.muted {
                background: rgba(239, 68, 68, 0.8);
            }
        `;

        document.head.appendChild(style);
        document.body.appendChild(overlay);

        // Attacher les vidéos
        setTimeout(() => {
            const localVideo = document.getElementById('local-video');
            if (localVideo && this.localStream) {
                localVideo.srcObject = this.localStream;
            }
        }, 100);

        // Événements des boutons
        document.getElementById('end-call')?.addEventListener('click', () => this.endCall());
        document.getElementById('toggle-mic')?.addEventListener('click', () => this.toggleMic());
        document.getElementById('toggle-camera')?.addEventListener('click', () => this.toggleCamera());
        document.getElementById('minimize-call')?.addEventListener('click', () => this.toggleMinimize());

        // Double-clic sur le header pour basculer plein écran/réduit
        document.querySelector('.call-header')?.addEventListener('dblclick', () => this.toggleMinimize());
    }

    /**
     * Basculer entre mode plein écran et réduit
     */
    toggleMinimize() {
        this.isMinimized = !this.isMinimized;
        const overlay = document.getElementById('webrtc-call-overlay');
        if (overlay) {
            overlay.classList.toggle('minimized', this.isMinimized);
            const minimizeBtn = document.getElementById('minimize-call');
            if (minimizeBtn) {
                minimizeBtn.innerHTML = this.isMinimized ?
                    '<svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>' :
                    '<svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M19 13H5v-2h14v2z"/></svg>';
            }
        }
    }

    /**
     * Mettre à jour la vidéo distante
     */
    updateRemoteVideo() {
        const remoteVideo = document.getElementById('remote-video');
        if (remoteVideo && this.remoteStream) {
            remoteVideo.srcObject = this.remoteStream;
            console.log('✅ Vidéo distante attachée');
        }
    }

    /**
     * Mettre à jour l'UI selon l'état
     */
    updateCallUI(state) {
        const statusEl = document.getElementById('call-status');
        if (statusEl) {
            switch (state) {
                case 'connected':
                    statusEl.textContent = 'Appel en cours';
                    break;
                case 'ended':
                    statusEl.textContent = 'Appel terminé';
                    break;
            }
        }
    }

    /**
     * Toggle micro
     */
    toggleMic() {
        if (this.localStream) {
            const audioTrack = this.localStream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                const btn = document.getElementById('toggle-mic');
                if (btn) {
                    btn.classList.toggle('muted', !audioTrack.enabled);
                }
            }
        }
    }

    /**
     * Toggle caméra
     */
    toggleCamera() {
        if (this.localStream) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (videoTrack) {
                videoTrack.enabled = !videoTrack.enabled;
                const btn = document.getElementById('toggle-camera');
                if (btn) {
                    btn.classList.toggle('muted', !videoTrack.enabled);
                }
            }
        }
    }

    /**
     * Gérer un appel entrant (offre reçue)
     */
    async handleIncomingCall(data) {
        console.log('📲 Appel entrant reçu:', data);

        this.currentCall = data.call;
        this.callType = data.call.type;
        this.isInitiator = false;

        // Afficher la popup d'appel entrant
        this.showIncomingCallPopup(data);
    }

    /**
     * Gérer la réponse à notre offre
     */
    async handleCallAnswer(data) {
        console.log('✅ Réponse à l\'appel reçue');

        try {
            const remoteDesc = new RTCSessionDescription(data.answer);
            await this.peerConnection.setRemoteDescription(remoteDesc);
            console.log('✅ Remote description définie');
        } catch (error) {
            console.error('❌ Erreur remote description:', error);
        }
    }

    /**
     * Gérer un candidat ICE reçu
     */
    async handleIceCandidate(data) {
        console.log('🧊 Candidat ICE reçu');

        if (this.peerConnection && data.candidate) {
            try {
                await this.peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
                console.log('✅ Candidat ICE ajouté');
            } catch (error) {
                console.error('❌ Erreur ajout ICE:', error);
            }
        }
    }

    /**
     * Gérer le rejet de l'appel
     */
    handleCallRejected(data) {
        console.log('❌ Appel rejeté');
        this.showToast('Appel refusé', 'error');
        this.endCall();
    }

    /**
     * Gérer la fin de l'appel
     */
    handleCallEnded(data) {
        console.log('📞 Appel terminé par l\'autre partie');
        this.showToast('Appel terminé', 'info');
        this.endCall();
    }

    /**
     * Afficher la popup d'appel entrant
     */
    showIncomingCallPopup(data) {
        const callerName = data.caller?.name || 'Utilisateur inconnu';
        const callType = data.call.type === 'video' ? 'vidéo' : 'audio';
        const callIcon = data.call.type === 'video' ? '📹' : '📞';

        const popup = document.createElement('div');
        popup.id = 'incoming-call-popup';
        popup.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            z-index: 10001;
            min-width: 320px;
            text-align: center;
            animation: slideDown 0.3s ease-out;
        `;

        popup.innerHTML = `
            <div style="font-size: 4rem; margin-bottom: 1rem;">${callIcon}</div>
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.5rem;">Appel ${callType} entrant</h3>
            <p style="margin: 0 0 2rem 0; font-size: 1.1rem; opacity: 0.9;">${callerName}</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button id="accept-call-btn" style="
                    background: #10b981;
                    color: white;
                    border: none;
                    padding: 1rem 2rem;
                    border-radius: 8px;
                    font-size: 1rem;
                    cursor: pointer;
                    font-weight: 600;
                    transition: all 0.2s;
                ">
                    ✅ Accepter
                </button>
                <button id="reject-call-btn" style="
                    background: #ef4444;
                    color: white;
                    border: none;
                    padding: 1rem 2rem;
                    border-radius: 8px;
                    font-size: 1rem;
                    cursor: pointer;
                    font-weight: 600;
                    transition: all 0.2s;
                ">
                    ❌ Refuser
                </button>
            </div>
        `;

        document.body.appendChild(popup);

        // Jouer une sonnerie (optionnel)
        this.playRingtone();

        // Gérer les boutons
        document.getElementById('accept-call-btn').onclick = () => {
            this.acceptIncomingCall(data);
            popup.remove();
            this.stopRingtone();
        };

        document.getElementById('reject-call-btn').onclick = () => {
            this.rejectIncomingCall(data.call.id);
            popup.remove();
            this.stopRingtone();
        };
    }

    /**
     * Accepter un appel entrant
     */
    async acceptIncomingCall(data) {
        console.log('✅ Acceptation de l\'appel');

        try {
            // Obtenir l'accès média
            await this.getLocalStream();

            // Afficher l'UI d'appel
            this.showCallUI('incoming');

            // Créer la connexion peer
            await this.createPeerConnection();

            // Définir la remote description (l'offre)
            const remoteDesc = new RTCSessionDescription(data.offer);
            await this.peerConnection.setRemoteDescription(remoteDesc);

            // Créer la réponse
            const answer = await this.peerConnection.createAnswer();
            await this.peerConnection.setLocalDescription(answer);

            // Notifier le serveur de l'acceptation
            const response = await fetch(`/api/calls/${this.currentCall.id}/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    answer: answer
                })
            });

            if (!response.ok) {
                throw new Error('Erreur lors de l\'acceptation');
            }

            console.log('✅ Appel accepté et réponse envoyée');

        } catch (error) {
            console.error('❌ Erreur acceptation appel:', error);
            this.showToast('Erreur lors de l\'acceptation: ' + error.message, 'error');
            this.endCall();
        }
    }

    /**
     * Refuser un appel entrant
     */
    async rejectIncomingCall(callId) {
        console.log('❌ Rejet de l\'appel');

        try {
            await fetch(`/api/calls/${callId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            this.showToast('Appel refusé', 'info');
        } catch (error) {
            console.error('❌ Erreur rejet appel:', error);
        }
    }

    /**
     * Jouer la sonnerie
     */
    playRingtone() {
        // Créer un son de sonnerie simple avec Web Audio API
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.ringtoneOscillator = audioContext.createOscillator();
        this.ringtoneGain = audioContext.createGain();

        this.ringtoneOscillator.connect(this.ringtoneGain);
        this.ringtoneGain.connect(audioContext.destination);

        this.ringtoneOscillator.frequency.value = 440; // La
        this.ringtoneGain.gain.value = 0.1;

        this.ringtoneOscillator.start();

        // Vibrer si disponible
        if (navigator.vibrate) {
            navigator.vibrate([200, 100, 200, 100, 200]);
        }
    }

    /**
     * Arrêter la sonnerie
     */
    stopRingtone() {
        if (this.ringtoneOscillator) {
            this.ringtoneOscillator.stop();
            this.ringtoneOscillator = null;
        }
    }

    /**
     * Terminer l'appel
     */
    async endCall() {
        console.log('📞 Fin de l\'appel');

        // Arrêter les streams
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
        }

        // Fermer la connexion peer
        if (this.peerConnection) {
            this.peerConnection.close();
        }

        // Notifier l'autre partie
        if (this.currentCall) {
            this.sendSignal('call-ended', {
                callId: this.currentCall.id
            });
        }

        // Supprimer l'UI
        const overlay = document.getElementById('webrtc-call-overlay');
        if (overlay) {
            overlay.remove();
        }

        // Mettre à jour la DB
        if (this.currentCall) {
            await fetch(`/calls/${this.currentCall.id}/end`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).catch(err => console.error('Erreur fin appel:', err));
        }

        // Réinitialiser
        this.peerConnection = null;
        this.localStream = null;
        this.remoteStream = null;
        this.currentCall = null;
    }

    /**
     * Afficher un toast
     */
    showToast(message, type = 'info') {
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
            z-index: 10001;
            animation: slideInRight 0.3s ease;
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
}

// Fonction globale pour initier un appel
window.initWebRTCCall = function(receiverId, callType = 'video') {
    // Vérifier que messagingApp est disponible
    if (!window.messagingApp) {
        console.error('❌ messagingApp non disponible');
        if (typeof showToast === 'function') {
            showToast('Erreur: Application non initialisée', 'error');
        }
        return;
    }

    // Créer le manager si nécessaire
    if (!window.webrtcCallManager) {
        console.log('🎬 Création du WebRTCCallManager');
        window.webrtcCallManager = new WebRTCCallManager(
            window.messagingApp.userId,
            window.messagingApp.authToken
        );
    }

    window.webrtcCallManager.initiateCall(receiverId, callType);
};

console.log('✅ webrtc-call.js chargé');
