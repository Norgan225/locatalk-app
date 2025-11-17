/**
 * 🎤 Gestionnaire de Messages Vocaux
 * Enregistrement, upload et lecture audio
 */
/**
 * 🎤 Gestionnaire de Messages Vocaux
 * Enregistrement, upload et lecture audio
 */

class VoiceRecorder {
    constructor(messagingApp) {
        this.app = messagingApp;
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.isRecording = false;
        this.isPaused = false;
        this.recordingStartTime = null;
        this.timerInterval = null;
        this.stream = null;
        this.pauseStartTime = null;
        this.accumulatedPausedDuration = 0;
        this.pendingAudioBlob = null;
        this.pendingAudioDuration = null;
        this._stopResolve = null;
        this.previewFromPause = false;
        this._discardOnStop = false;
        this._previewChunks = null; // 🔑 Chunks séparés pour preview de pause

        // visualizer
        this.audioContext = null;
        this.analyser = null;
        this.sourceNode = null;
        this.visualizerRAF = null;
        this.visualizerBars = [];

        this._previewAudioEl = null;

        this.init();
    }

    init() {
        // Ne PAS attacher de listener ici - déjà géré par messaging-app.js
        // qui appelle toggleVoiceRecording() → voiceRecorder.toggleRecording()
        console.log('🎤 VoiceRecorder initialisé (listener géré par MessagingApp)');
    }

    setupUI() {
        // Méthode conservée pour compatibilité mais ne fait plus rien
        // Le listener est déjà attaché dans messaging-app.js
    }

    async toggleRecording() {
        console.log('🎤 toggleRecording - isRecording:', this.isRecording, 'pending:', this._togglePending);

        // Debounce: empêcher les appels rapides successifs
        if (this._togglePending) {
            console.warn('🎤 Toggle déjà en cours, ignorer');
            return;
        }

        if (!this.app || !this.app.currentConversation) {
            console.log('🎤 Aucune conversation sélectionnée');
            if (this.app && this.app.showNotification) this.app.showNotification('Sélectionnez une conversation d\'abord', 'warning');
            return;
        }

        this._togglePending = true;

        try {
            if (!this.isRecording) {
                // Démarrer l'enregistrement
                await this.startRecording();
            } else {
                // Arrêter l'enregistrement (le bouton ⏹️ arrête ET affiche preview)
                console.log('🎤 Arrêt enregistrement (stop definitif)');
                this.stopRecording();
            }
        } finally {
            // Débloquer après un court délai pour éviter double-clic
            setTimeout(() => { this._togglePending = false; }, 300);
        }
    }

    async startRecording() {
        // Guard: ne pas démarrer si déjà en cours ou si preview active
        if (this.isRecording) {
            console.warn('🎤 [START] Déjà en cours d\'enregistrement, ignorer');
            return;
        }
        if (this.pendingAudioBlob) {
            console.warn('🎤 [START] Preview active, annuler d\'abord');
            return;
        }

        console.log('🎤 [START] Démarrage de l\'enregistrement');

        try {
            // 🎯 Test SIMPLE: audio brut sans contraintes pour éviter la répétition
            // Les contraintes créent peut-être une boucle d'écho au niveau du driver
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                console.log('🎤 [START] Stream audio SIMPLE (audio: true uniquement)');
            } catch (err) {
                console.error('🎤 [START] getUserMedia échoué:', err);
                throw err;
            }

            // Setup visualizer
            try {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                this.sourceNode = this.audioContext.createMediaStreamSource(this.stream);
                this.analyser = this.audioContext.createAnalyser();
                this.analyser.fftSize = 256;
                this.sourceNode.connect(this.analyser);
            } catch (e) {
                console.warn('🎤 Visualizer setup échoué:', e);
            }

            // 🎯 Configuration codec + bitrate stable
            const preferred = 'audio/webm;codecs=opus';
            const mimeType = MediaRecorder.isTypeSupported(preferred) ? preferred : this.getSupportedMimeType();
            const recorderOptions = { mimeType };

            // Bitrate plus élevé pour meilleure qualité voix (128 kbps)
            try {
                recorderOptions.audioBitsPerSecond = 128000;
                console.log('🎤 Bitrate: 128 kbps (qualité haute)');
            } catch (e) {
                console.warn('🎤 Bitrate non supporté, utilisation défaut');
            }

            console.log('🎤 MediaRecorder config:', recorderOptions);
            this.mediaRecorder = new MediaRecorder(this.stream, recorderOptions);

            this.audioChunks = [];
            this.mediaRecorder.ondataavailable = (e) => {
                if (e.data && e.data.size) {
                    console.log('🎤 Chunk reçu:', e.data.size, 'bytes');
                    this.audioChunks.push(e.data);
                }
            };
            this.mediaRecorder.onstop = () => this.handleRecordingComplete();

            // 🎯 Chunks plus gros (1000ms) pour stabilité maximale - pas de micro-coupures
            this.mediaRecorder.start(); // Pas d'argument = pas de chunks périodiques
            this.isRecording = true;
            this.isPaused = false;
            this.accumulatedPausedDuration = 0;
            this.recordingStartTime = Date.now();

            // Afficher l'UI D'ABORD (crée le container waveformVisualizer)
            this.showRecordingUI();
            this.startTimer();

            // PUIS démarrer le visualizer (maintenant le container existe)
            try {
                if (this.analyser) {
                    console.log('🎤 Démarrage visualizer...');
                    this.startVisualizer();
                } else {
                    console.warn('🎤 Analyser non disponible, pas de visualizer');
                }
            } catch (e) {
                console.warn('🎤 Erreur visualizer:', e);
            }

            console.log('🎤 ✅ Enregistrement démarré - qualité maximale');
        } catch (error) {
            console.error('❌ Erreur microphone:', error);
            if (error && error.name === 'NotAllowedError') this.app.showNotification('Permission microphone refusée', 'error');
            else if (error && error.name === 'NotFoundError') this.app.showNotification('Aucun microphone trouvé', 'error');
            else this.app.showNotification('Erreur d\'accès au microphone', 'error');
        }
    }

    stopRecording() {
        console.log('🎤 [STOP] Début stopRecording, isRecording:', this.isRecording);

        // Marquer comme non-enregistrement IMMÉDIATEMENT pour éviter tout redémarrage
        this.isRecording = false;
        this.isPaused = false;

        // Arrêter le MediaRecorder
        try {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                console.log('🎤 [STOP] Arrêt du MediaRecorder, state:', this.mediaRecorder.state);
                this.mediaRecorder.stop();
            }
        } catch (e) { console.warn('🎤 [STOP] stop() échoué:', e); }

        // Arrêter et nettoyer le stream micro IMMÉDIATEMENT
        if (this.stream) {
            console.log('🎤 [STOP] Arrêt du stream micro');
            this.stream.getTracks().forEach(t => {
                t.stop();
                console.log('🎤 [STOP] Track stopped:', t.kind, t.label);
            });
            this.stream = null;
        }

        // Nettoyer le contexte audio et visualizer
        try {
            this.stopVisualizer();
            if (this.sourceNode) try { this.sourceNode.disconnect(); } catch (e) {}
            if (this.analyser) try { this.analyser.disconnect(); } catch (e) {}
            if (this.audioContext) try { this.audioContext.close(); } catch (e) {}
            this.sourceNode = this.analyser = this.audioContext = null;
        } catch (e) { console.warn('🎤 [STOP] cleanup failed', e); }

        this.hideRecordingUI();
        this.stopTimer();
        console.log('🎤 [STOP] ✅ Arrêté et nettoyé complètement');
    }

    pauseRecording() {
        if (!(this.mediaRecorder && this.mediaRecorder.state === 'recording')) return;
        try {
            this.mediaRecorder.pause();
            this.isPaused = true;
            this.pauseStartTime = Date.now();
            this.stopTimer();
            this.stopVisualizer();

            const pauseBtn = document.getElementById('pauseResumeBtn'); if (pauseBtn) pauseBtn.textContent = '▶️ Reprendre';
            console.log('🎤 Pause');

            setTimeout(() => {
                try {
                    // Créer la preview avec les chunks ACTUELS
                    const mimeType = (this.mediaRecorder && this.mediaRecorder.mimeType) ? this.mediaRecorder.mimeType : this.getSupportedMimeType();
                    this._previewChunks = [...this.audioChunks]; // 🔑 Copier les chunks pour la preview
                    const previewBlob = new Blob(this._previewChunks, { type: mimeType });
                    const elapsed = Math.floor((Date.now() - this.recordingStartTime - this.accumulatedPausedDuration) / 1000);

                    if (previewBlob.size < 1500) {
                        console.warn('🎤 Aperçu ignoré: trop petit', previewBlob.size);
                        return;
                    }

                    this.showAudioPreview(previewBlob, elapsed);
                    this.previewFromPause = true;
                    console.log('🎤 Aperçu créé:', previewBlob.size, 'bytes');
                } catch (e) { console.warn('🎤 preview failed', e); }
            }, 250);
        } catch (e) { console.error('🎤 Erreur pause:', e); }
    }

    resumeRecording() {
        if (!(this.mediaRecorder && this.mediaRecorder.state === 'paused')) return;
        try {
            this.mediaRecorder.resume();
            this.isPaused = false;
            if (this.pauseStartTime) { this.accumulatedPausedDuration += Date.now() - this.pauseStartTime; this.pauseStartTime = null; }
            this.startTimer();
            const pauseBtn = document.getElementById('pauseResumeBtn'); if (pauseBtn) pauseBtn.textContent = '⏸️ Pause';
            if (this.previewFromPause) { this.cancelAudio(); this.previewFromPause = false; }
            this.startVisualizer();
            console.log('🎤 Reprise');
        } catch (e) { console.error('🎤 Erreur reprise:', e); }
    }

    stopAndGetPendingAudio() {
        const pending = this.getPendingAudio();
        if (pending) return Promise.resolve(pending);
        if (!this.mediaRecorder || this.mediaRecorder.state === 'inactive') return Promise.resolve(null);
        if (this.previewFromPause && this.pendingAudioBlob) {
            const p = { blob: this.pendingAudioBlob, duration: this.pendingAudioDuration };
            try { this.previewFromPause = false; this.stopRecording(); } catch (e) { console.warn('stopAfterPreview', e); }
            return Promise.resolve(p);
        }
        return new Promise((resolve) => { this._stopResolve = resolve; try { this.mediaRecorder.stop(); } catch (e) { console.warn('stop failed', e); resolve(null); } });
    }

    async handleRecordingComplete() {
        console.log('🎤 [COMPLETE] handleRecordingComplete appelé');
        console.log('🎤 [COMPLETE] isRecording:', this.isRecording, 'isPaused:', this.isPaused, '_discardOnStop:', this._discardOnStop);

        const duration = Math.floor((Date.now() - this.recordingStartTime) / 1000);

        // S'assurer que tout est bien arrêté (au cas où)
        this.stopTimer();
        this.isRecording = false;
        this.isPaused = false;
        this.stopVisualizer();

        try {
            if (this.sourceNode) try { this.sourceNode.disconnect(); } catch (e) {}
            if (this.analyser) try { this.analyser.disconnect(); } catch (e) {}
            if (this.audioContext) try { this.audioContext.close(); } catch (e) {}
        } catch (e) { console.warn(e); }

        if (this._discardOnStop) {
            console.log('🎤 [COMPLETE] Annulé par utilisateur');
            this.audioChunks = [];
            this.pendingAudioBlob = null;
            this.pendingAudioDuration = null;
            this._discardOnStop = false;
            try { this.hideRecordingUI(); } catch (e) {}
            if (this._stopResolve) { try { this._stopResolve(null); } catch (e) {} this._stopResolve = null; }
            return;
        }

        if (duration < 1) { this.app.showNotification('Enregistrement trop court (min. 1 sec)', 'error'); return; }
        if (duration > 300) { this.app.showNotification('Enregistrement trop long (max. 5 min)', 'error'); return; }

        const mimeType = (this.mediaRecorder && this.mediaRecorder.mimeType) ? this.mediaRecorder.mimeType : this.getSupportedMimeType();

        // 🔑 Si on vient d'une preview de pause, utiliser UNIQUEMENT les chunks stockés
        // Sinon, créer le blob avec tous les chunks (enregistrement continu ou pas de pause)
        let audioBlob;
        if (this.previewFromPause && this._previewChunks) {
            audioBlob = new Blob(this._previewChunks, { type: mimeType });
            console.log('🎤 ✅ Blob de preview utilisé (Pause):', mimeType, audioBlob.size, 'bytes');
            this._previewChunks = null; // Nettoyer
        } else {
            audioBlob = new Blob(this.audioChunks, { type: mimeType });
            console.log('🎤 ✅ Blob créé (Stop):', mimeType, audioBlob.size, 'bytes');
        }

        try { this.hideRecordingUI(); } catch (e) {}
        this.showAudioPreview(audioBlob, duration);

        if (this._stopResolve) { try { this._stopResolve(this.getPendingAudio()); } catch (e) { this._stopResolve(null); } this._stopResolve = null; }
    }

    // visualizer
    startVisualizer() {
        const container = document.getElementById('waveformVisualizer');
        if (!container) return;
        if (this.visualizerBars.length === 0) {
            container.innerHTML = '';
            for (let i = 0; i < 20; i++) {
                const bar = document.createElement('div');
                bar.className = 'waveform-bar';
                bar.style.height = '8px';
                container.appendChild(bar);
                this.visualizerBars.push(bar);
            }
        }

        const draw = () => {
            if (!this.analyser) return;
            const bufferLength = this.analyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);
            this.analyser.getByteTimeDomainData(dataArray);
            let sum = 0;
            for (let i = 0; i < bufferLength; i++) {
                const v = (dataArray[i] - 128) / 128;
                sum += v * v;
            }
            const rms = Math.sqrt(sum / bufferLength);

            for (let i = 0; i < this.visualizerBars.length; i++) {
                const idx = Math.floor((i / this.visualizerBars.length) * bufferLength);
                const val = Math.abs((dataArray[idx] - 128) / 128);
                const scale = Math.max(rms, val);
                const height = Math.max(6, Math.min(40, Math.round(scale * 200)));
                this.visualizerBars[i].style.height = height + 'px';
                this.visualizerBars[i].style.opacity = 0.5 + (scale * 0.5);
            }

            this.visualizerRAF = requestAnimationFrame(draw);
        };

        if (!this.visualizerRAF) this.visualizerRAF = requestAnimationFrame(draw);
    }

    stopVisualizer() {
        if (this.visualizerRAF) { cancelAnimationFrame(this.visualizerRAF); this.visualizerRAF = null; }
        if (this.visualizerBars && this.visualizerBars.length) this.visualizerBars.forEach(b => { if (b && b.style) { b.style.height = '8px'; b.style.opacity = '0.6'; } });
    }

    showAudioPreview(audioBlob, duration) {
        console.log('🎤 showAudioPreview:', audioBlob.type, audioBlob.size, 'bytes, durée:', duration, 's');
        const previewArea = document.getElementById('filePreviewArea');
        if (!previewArea) {
            console.error('🎤 filePreviewArea introuvable !');
            return;
        }

        // Nettoyer l'ancien audio element si existant
        if (this._previewAudioEl) {
            try {
                this._previewAudioEl.pause();
                this._previewAudioEl.src = '';
                this._previewAudioEl.remove();
            } catch (e) {}
            this._previewAudioEl = null;
        }

        const audioUrl = URL.createObjectURL(audioBlob);
        console.log('🎤 Audio URL:', audioUrl);

        // Modifier le HTML d'abord
        previewArea.classList.remove('hidden');
        previewArea.innerHTML = `
            <div class="whatsapp-preview">
                <button class="preview-delete" title="Supprimer">🗑️</button>
                <button class="preview-play" title="Lire">▶️</button>
                <div class="preview-slider" style="flex:1; margin:0 8px; display:flex; align-items:center; gap:8px;">
                    <input type="range" class="preview-range" min="0" max="100" value="0">
                </div>
                <span class="preview-duration">${this.formatDuration(duration)}</span>
            </div>`;

    // 🔑 Créer et attacher l'audio element IMMÉDIATEMENT APRÈS le innerHTML
    const audioEl = document.createElement('audio');
    // Pré-assigner l'URL initiale (peut être remplacée par une conversion WAV si le navigateur
    // ne supporte pas le codec enregistré, p.ex. Safari qui ne lit pas toujours audio/webm)
    audioEl.src = audioUrl;
    // Conserver l'URL créée pour pouvoir la révoquer proprement plus tard et éviter
    // une course qui provoquerait un "Empty src" MediaError.
    try { audioEl.dataset.blobUrl = audioUrl; } catch (e) { /* silent */ }
        audioEl.preload = 'metadata';
        audioEl.volume = 1.0;
        audioEl.style.display = 'none';
        previewArea.appendChild(audioEl); // ⚠️ ATTACHER AU DOM IMMÉDIATEMENT

        console.log('🎤 Audio element créé et attaché:', audioEl.src);

        // Si le navigateur ne peut pas lire le type MIME du blob (ex: Safari + webm),
        // essayer une conversion client-side en WAV pour la prévisualisation.
        try {
            const mime = audioBlob.type || '';
            const canPlay = audioEl.canPlayType ? audioEl.canPlayType(mime) : '';
            if (!canPlay) {
                console.warn('🎤 Le navigateur ne peut pas lire', mime, '- tentative de conversion en WAV pour la prévisualisation');
                // effectuer la conversion asynchrone et remplacer la source
                this.blobToWav(audioBlob)
                    .then((wavBlob) => {
                        try {
                            const wavUrl = URL.createObjectURL(wavBlob);
                            // Révoquer l'ancienne URL blob si présente pour éviter fuites et races
                            try {
                                const prev = audioEl.dataset && audioEl.dataset.blobUrl;
                                audioEl.src = wavUrl;
                                audioEl.dataset.blobUrl = wavUrl;
                                if (prev && prev !== wavUrl) URL.revokeObjectURL(prev);
                            } catch (innerErr) {
                                // fallback: appliquer la nouvelle src quand même
                                audioEl.src = wavUrl;
                            }
                            console.log('🎤 Prévisualisation convertie en WAV:', wavUrl);
                        } catch (err) {
                            console.warn('🎤 Impossible de définir la source WAV:', err);
                        }
                    })
                    .catch((err) => console.warn('🎤 Échec conversion WAV:', err));
            }
        } catch (e) { console.warn('🎤 Vérification compatibilité audio impossible:', e); }

        const playBtn = previewArea.querySelector('.preview-play');
        const deleteBtn = previewArea.querySelector('.preview-delete');
        const range = previewArea.querySelector('.preview-range');
        const durationEl = previewArea.querySelector('.preview-duration');

        this.pendingAudioBlob = audioBlob;
        this.pendingAudioDuration = duration;
        let seeking = false;

        playBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('🎤 Play/Pause:', audioEl.paused ? 'playing' : 'paused');
            if (audioEl.paused) {
                audioEl.play()
                    .then(() => console.log('🎤 Lecture démarrée'))
                    .catch(err => console.error('🎤 Erreur lecture:', err));
                playBtn.textContent = '⏸️';
            } else {
                audioEl.pause();
                console.log('🎤 Pause');
                playBtn.textContent = '▶️';
            }
        });

        deleteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            try { audioEl.pause(); } catch (x) { /* ignore */ }
            // Révoquer proprement l'URL blob avant de nettoyer pour éviter la course qui
            // provoque un MediaError "Empty src" sur certains navigateurs.
            try {
                const url = audioEl.dataset && audioEl.dataset.blobUrl;
                if (url) URL.revokeObjectURL(url);
            } catch (revErr) { console.warn('🎤 Revoke failed:', revErr); }
            this.cancelAudio();
        });

        audioEl.addEventListener('loadedmetadata', () => {
            const dur = audioEl.duration;
            console.log('🎤 Durée:', dur);
            if (isFinite(dur) && !isNaN(dur) && dur > 0) {
                range.max = Math.max(1, Math.floor(dur));
                durationEl.textContent = this.formatDuration(Math.floor(dur));
            } else {
                const fallback = this.pendingAudioDuration || Math.floor((Date.now() - (this.recordingStartTime || Date.now())) / 1000) || 0;
                console.warn('🎤 Fallback duration:', fallback);
                range.max = Math.max(1, Math.floor(fallback));
                durationEl.textContent = this.formatDuration(Math.floor(fallback));
            }
        });

        audioEl.addEventListener('canplay', () => console.log('🎤 Audio prêt'));
        audioEl.addEventListener('playing', () => console.log('🎤 En lecture'));
        audioEl.addEventListener('ended', () => console.log('🎤 Terminé'));
        // Ignore le MediaError harmless "Empty src attribute" (code 4) qui survient parfois
        // lorsqu'on nettoie/revoke l'URL juste avant que l'élément ne tente de charger.
        audioEl.addEventListener('error', (e) => {
            const err = audioEl.error;
            try {
                // Dans de nombreux navigateurs l'erreur code===4 signifie "Empty src" ou
                // une erreur liée au chargement du média suite à un revoke/cleanup.
                // On l'ignore pour éviter des logs bruyants lors du nettoyage.
                if (err && err.code === 4) {
                    console.debug('🎤 Ignorer MediaError code 4 (Empty src / load failure)');
                    return;
                }
            } catch (x) { /* ignore */ }
            console.error('🎤 Erreur audio:', err, e);
        });

        audioEl.addEventListener('timeupdate', () => { if (!seeking) range.value = Math.floor(audioEl.currentTime); });
        range.addEventListener('input', (e) => { seeking = true; durationEl.textContent = this.formatDuration(Math.floor(e.target.value)); });
        range.addEventListener('change', (e) => { audioEl.currentTime = Number(e.target.value); seeking = false; });

        this._previewAudioEl = audioEl;

        const sendBtn = document.getElementById('sendMessageBtn'); if (sendBtn) sendBtn.disabled = false;
        if (this.app && this.app.updateSendButtonState) this.app.updateSendButtonState();
    }

    cancelAudio() {
        // Nettoyer l'audio element AVANT de vider le HTML
        if (this._previewAudioEl) {
            try {
                this._previewAudioEl.pause();
                // Révoquer l'URL blob si on l'a stockée
                try {
                    const url = this._previewAudioEl.dataset && this._previewAudioEl.dataset.blobUrl;
                    if (url) URL.revokeObjectURL(url);
                } catch (revErr) { /* ignore */ }
                this._previewAudioEl.src = '';
                this._previewAudioEl.remove();
            } catch (e) { console.warn('🎤 Erreur nettoyage audio:', e); }
            this._previewAudioEl = null;
        }

        const previewArea = document.getElementById('filePreviewArea');
        if (previewArea) {
            previewArea.classList.add('hidden');
            previewArea.innerHTML = '';
        }

        this.pendingAudioBlob = null;
        this.pendingAudioDuration = null;
        this.previewFromPause = false;

        if (this.app && this.app.updateSendButtonState) this.app.updateSendButtonState();
    }

    getPendingAudio() { if (!this.pendingAudioBlob) return null; return { blob: this.pendingAudioBlob, duration: this.pendingAudioDuration }; }

    async uploadAudio() {
        const audio = this.getPendingAudio();
        if (!audio) { console.error('🎤 Aucun audio'); return null; }
        try {
            console.log('🎤 Upload audio - conversation:', this.app.currentConversation);
            const formData = new FormData();
            // Si le blob utilise un codec non lisible par le navigateur (ex: webm dans Safari),
            // convertir en WAV avant upload pour éviter les erreurs côté client et assurer compatibilité.
            let uploadBlob = audio.blob;
            const testAudio = document.createElement('audio');
            const mime = uploadBlob.type || '';
            let canPlay = '';
            try { canPlay = testAudio.canPlayType ? testAudio.canPlayType(mime) : ''; } catch (e) { canPlay = ''; }
            if (!canPlay) {
                console.warn('🎤 Codec non supporté par le navigateur pour le blob, tentative de conversion en WAV avant upload:', mime);
                try {
                    uploadBlob = await this.blobToWav(uploadBlob);
                    console.log('🎤 Conversion en WAV réussie pour upload');
                } catch (err) {
                    console.warn('🎤 Conversion WAV échouée, on continue avec le blob original:', err);
                }
            }

            const extension = this.getFileExtension((this.mediaRecorder && this.mediaRecorder.mimeType) ? this.mediaRecorder.mimeType : this.getSupportedMimeType());
            const filename = `voice_${Date.now()}.${extension}`;
            formData.append('file', uploadBlob, filename);
            formData.append('receiver_id', this.app.currentConversation.id);
            formData.append('duration', audio.duration);

            const response = await fetch('/api/messaging/upload', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.app.authToken}`,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('🎤 [UPLOAD_ERROR] Erreur upload (status:', response.status, '):', errorText);
                console.error('🎤 [UPLOAD_ERROR] Headers:', [...response.headers.entries()]);
                throw new Error('Erreur upload audio: ' + errorText);
            }

            const data = await response.json();
            console.log('🎤 [UPLOAD_SUCCESS] Response du serveur:', data);

            // 🔑 Vérifier la structure de la réponse
            if (!data || !data.attachment) {
                console.error('🎤 [UPLOAD_ERROR] Structure réponse invalide:', data);
                throw new Error('Réponse invalide du serveur');
            }

            console.log('🎤 ✅ Upload réussi:', data);
            // Retourner à la fois l'id et l'objet attachment pour permettre
            // d'afficher immédiatement le lecteur dans l'UI sans recharger la conversation
            return {
                id: data.attachment.id,
                attachment: data.attachment
            };
        } catch (error) {
            console.error('❌ Erreur upload:', error);
            if (this.app && this.app.showNotification) this.app.showNotification('Erreur envoi message vocal', 'error');
            return null;
        }
    }

    showRecordingUI() {
        const voiceBtn = document.getElementById('voiceRecordBtn');
        if (voiceBtn) { voiceBtn.innerHTML = '⏹️'; voiceBtn.classList.add('recording'); voiceBtn.title = 'Arrêter l\'enregistrement'; }
        const messageInputArea = document.getElementById('messageInput');
        if (!messageInputArea) return;
        if (messageInputArea.style.display === 'none') messageInputArea.style.display = 'block';

        let recordingIndicator = document.getElementById('recordingIndicator');
        if (!recordingIndicator) {
            recordingIndicator = document.createElement('div');
            recordingIndicator.id = 'recordingIndicator';
            recordingIndicator.className = 'whatsapp-recording';
            // UI simple : suppression + visualizer + timer (pas de pause pendant enregistrement)
            recordingIndicator.innerHTML = `
                <button id="deleteRecordingBtn" class="record-action" title="Annuler">🗑️</button>
                <div id="waveformVisualizer" class="waveform-visualizer" style="flex:1;margin:0 8px;display:flex;align-items:center;gap:8px;"></div>
                <span id="recordingTimer" class="recording-timer">00:00</span>
            `;
            const inputWrapper = messageInputArea.querySelector('.input-wrapper');
            if (inputWrapper) messageInputArea.insertBefore(recordingIndicator, inputWrapper); else messageInputArea.prepend(recordingIndicator);
        }

        const deleteBtn = document.getElementById('deleteRecordingBtn');
        if (deleteBtn && !deleteBtn.dataset.listenerAttached) {
            deleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this._discardOnStop = true;
                try { this.stopRecording(); } catch (err) { console.warn(err); this.cancelAudio(); this.hideRecordingUI(); }
            });
            deleteBtn.dataset.listenerAttached = '1';
        }

        const textarea = document.getElementById('messageTextarea');
        if (textarea) { textarea.disabled = true; textarea.placeholder = 'Enregistrement en cours...'; }
    }

    hideRecordingUI() {
        const voiceBtn = document.getElementById('voiceRecordBtn');
        if (voiceBtn) { voiceBtn.innerHTML = '🎤'; voiceBtn.classList.remove('recording'); voiceBtn.title = 'Message vocal'; }
        const indicator = document.getElementById('recordingIndicator'); if (indicator) indicator.remove();
        const textarea = document.getElementById('messageTextarea'); if (textarea) { textarea.disabled = false; textarea.placeholder = 'Tapez votre message...'; }
    }

    startTimer() {
        if (this.timerInterval) clearInterval(this.timerInterval);
        this.timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.recordingStartTime - this.accumulatedPausedDuration) / 1000);
            const timerEl = document.getElementById('recordingTimer');
            if (timerEl) timerEl.textContent = this.formatDuration(elapsed);
            if (elapsed >= 300) { this.stopRecording(); if (this.app && this.app.showNotification) this.app.showNotification('Durée max atteinte (5 min)', 'info'); }
        }, 1000);
    }

    stopTimer() { if (this.timerInterval) { clearInterval(this.timerInterval); this.timerInterval = null; } }

    getSupportedMimeType() { const types = ['audio/webm;codecs=opus','audio/webm','audio/ogg;codecs=opus','audio/ogg','audio/mp4','audio/mpeg']; for (const t of types) if (MediaRecorder.isTypeSupported(t)) return t; return 'audio/webm'; }
    getFileExtension(mimeType) { const map = {'audio/webm':'webm','audio/ogg':'ogg','audio/mp4':'m4a','audio/mpeg':'mp3','audio/wav':'wav'}; const base = (mimeType||'').split(';')[0]; return map[base]||'webm'; }
    formatDuration(seconds) { const mins = Math.floor(seconds / 60); const secs = seconds % 60; return `${mins.toString().padStart(2,'0')}:${secs.toString().padStart(2,'0')}`; }
    formatFileSize(bytes) { if (bytes < 1024) return bytes + ' B'; if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'; return (bytes / (1024 * 1024)).toFixed(1) + ' MB'; }

    // Convert a recorded Blob (possibly webm/opus) to a WAV Blob for broader browser preview support.
    async blobToWav(blob) {
        try {
            const arrayBuffer = await blob.arrayBuffer();
            const AudioCtx = window.OfflineAudioContext || window.webkitOfflineAudioContext || window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) throw new Error('WebAudio API non disponible');
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            // decodeAudioData works with real-time AudioContext as well
            const audioBuffer = await audioCtx.decodeAudioData(arrayBuffer.slice(0));
            // close the AudioContext if supported
            try { if (audioCtx.close) audioCtx.close(); } catch (e) {}
            const wavArrayBuffer = this.audioBufferToWav(audioBuffer);
            return new Blob([wavArrayBuffer], { type: 'audio/wav' });
        } catch (err) {
            console.warn('🎤 blobToWav error:', err);
            throw err;
        }
    }

    // Convert AudioBuffer -> WAV (ArrayBuffer). Simple 16-bit PCM, interleaved if stereo.
    audioBufferToWav(buffer) {
        const numOfChan = buffer.numberOfChannels;
        const sampleRate = buffer.sampleRate;
        const format = 1; // PCM
        const bitsPerSample = 16;
        const blockAlign = numOfChan * bitsPerSample / 8;
        const bytesPerSample = bitsPerSample / 8;
        const dataLength = buffer.length * numOfChan * bytesPerSample;
        const bufferLength = 44 + dataLength;
        const view = new DataView(new ArrayBuffer(bufferLength));
        let offset = 0;

        function writeString(s) {
            for (let i = 0; i < s.length; i++) view.setUint8(offset + i, s.charCodeAt(i));
            offset += s.length;
        }

        writeString('RIFF');
        view.setUint32(offset, 36 + dataLength, true); offset += 4;
        writeString('WAVE');
        writeString('fmt ');
        view.setUint32(offset, 16, true); offset += 4; // Subchunk1Size
        view.setUint16(offset, format, true); offset += 2; // AudioFormat
        view.setUint16(offset, numOfChan, true); offset += 2;
        view.setUint32(offset, sampleRate, true); offset += 4;
        view.setUint32(offset, sampleRate * blockAlign, true); offset += 4;
        view.setUint16(offset, blockAlign, true); offset += 2;
        view.setUint16(offset, bitsPerSample, true); offset += 2;
        writeString('data');
        view.setUint32(offset, dataLength, true); offset += 4;

        // write interleaved PCM samples
        const interleaved = new Float32Array(buffer.length * numOfChan);
        for (let channel = 0; channel < numOfChan; channel++) {
            const chanData = buffer.getChannelData(channel);
            for (let i = 0; i < chanData.length; i++) {
                interleaved[i * numOfChan + channel] = chanData[i];
            }
        }

        // write samples as 16-bit PCM
        let index = 0;
        for (let i = 0; i < interleaved.length; i++, index += 2) {
            let sample = Math.max(-1, Math.min(1, interleaved[i]));
            sample = sample < 0 ? sample * 0x8000 : sample * 0x7FFF;
            view.setInt16(offset + i * 2, sample, true);
        }

        return view.buffer;
    }
}

window.voiceRecorder = null;
if (window.messagingApp) {
    window.voiceRecorder = new VoiceRecorder(window.messagingApp);
}
