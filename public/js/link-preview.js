/**
 * 🔗 Gestionnaire d'Aperçus de Liens
 * Preview automatique des URLs avec métadonnées Open Graph
 */

class LinkPreviewManager {
    constructor(messagingApp) {
        this.app = messagingApp;
        this.cache = new Map();
        this.pending = new Set();
    }

    /**
     * Détecter et prévisualiser les URLs dans un message
     */
    async detectAndPreview(text) {
        const urls = this.detectUrls(text);

        if (urls.length === 0) return [];

        const previews = [];

        for (const url of urls) {
            // Éviter les doublons
            if (this.pending.has(url)) continue;

            // Vérifier le cache
            if (this.cache.has(url)) {
                previews.push(this.cache.get(url));
                continue;
            }

            // Récupérer l'aperçu
            const preview = await this.fetchPreview(url);
            if (preview) {
                this.cache.set(url, preview);
                previews.push(preview);
            }
        }

        return previews;
    }

    /**
     * Détecter les URLs dans un texte
     */
    detectUrls(text) {
        const urlRegex = /(https?:\/\/[^\s<>"{}|\\^`\[\]]+)/gi;
        const matches = text.match(urlRegex);
        return matches || [];
    }

    /**
     * Récupérer l'aperçu d'une URL
     */
    async fetchPreview(url) {
        try {
            this.pending.add(url);

            const response = await fetch('/api/messaging/link-preview', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.app.authToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ url })
            });

            this.pending.delete(url);

            if (!response.ok) return null;

            const data = await response.json();
            return data.preview;

        } catch (error) {
            console.error('Erreur preview:', error);
            this.pending.delete(url);
            return null;
        }
    }

    /**
     * Rendre un aperçu de lien
     */
    renderPreview(preview) {
        const hasImage = preview.image && preview.image.length > 0;
        const hasVideo = preview.video && preview.video.length > 0;

        return `
            <div class="link-preview" data-url="${this.escapeHtml(preview.url)}">
                ${hasImage ? `
                    <div class="link-preview-image">
                        <img src="${this.escapeHtml(preview.image)}" alt="${this.escapeHtml(preview.title)}" loading="lazy">
                    </div>
                ` : ''}
                ${hasVideo ? `
                    <div class="link-preview-video">
                        <iframe src="${this.escapeHtml(preview.video)}" frameborder="0" allowfullscreen></iframe>
                    </div>
                ` : ''}
                <div class="link-preview-content">
                    ${preview.favicon ? `<img src="${this.escapeHtml(preview.favicon)}" class="link-preview-favicon" alt="">` : ''}
                    <div class="link-preview-text">
                        <div class="link-preview-title">${this.escapeHtml(preview.title)}</div>
                        ${preview.description ? `<div class="link-preview-description">${this.escapeHtml(preview.description)}</div>` : ''}
                        <div class="link-preview-url">
                            ${preview.site_name || this.getDomain(preview.url)}
                        </div>
                    </div>
                </div>
                <a href="${this.escapeHtml(preview.url)}" target="_blank" rel="noopener noreferrer" class="link-preview-overlay"></a>
            </div>
        `;
    }

    /**
     * Afficher les aperçus dans l'input
     */
    async showPreviewsInInput(text) {
        const previewArea = document.getElementById('linkPreviewArea');
        if (!previewArea) return;

        const previews = await this.detectAndPreview(text);

        if (previews.length === 0) {
            previewArea.classList.add('hidden');
            previewArea.innerHTML = '';
            return;
        }

        previewArea.classList.remove('hidden');
        previewArea.innerHTML = previews.map(p => `
            <div class="link-preview-mini">
                ${p.image ? `<img src="${this.escapeHtml(p.image)}" class="link-preview-mini-image" alt="">` : '<div class="link-preview-mini-placeholder">🔗</div>'}
                <div class="link-preview-mini-info">
                    <div class="link-preview-mini-title">${this.escapeHtml(p.title)}</div>
                    <div class="link-preview-mini-url">${this.getDomain(p.url)}</div>
                </div>
                <button class="link-preview-remove" onclick="linkPreviewManager.removePreview('${this.escapeHtml(p.url)}')">×</button>
            </div>
        `).join('');
    }

    /**
     * Supprimer un aperçu
     */
    removePreview(url) {
        this.cache.delete(url);
        const previewArea = document.getElementById('linkPreviewArea');
        if (previewArea) {
            const preview = previewArea.querySelector(`[data-url="${url}"]`);
            if (preview) {
                preview.remove();
            }

            // Cacher si vide
            if (previewArea.children.length === 0) {
                previewArea.classList.add('hidden');
            }
        }
    }

    /**
     * Utilitaires
     */

    getDomain(url) {
        try {
            const urlObj = new URL(url);
            return urlObj.hostname.replace('www.', '');
        } catch {
            return url;
        }
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Instance globale
let linkPreviewManager = null;
