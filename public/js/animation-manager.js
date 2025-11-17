/**
 * ✨ Service d'Animations
 * Gestion des animations et transitions fluides
 */

class AnimationManager {
    constructor() {
        this.animations = new Map();
        this.observers = new Map();
        this.init();
    }

    /**
     * Initialiser le gestionnaire d'animations
     */
    init() {
        // Observer les changements de visibilité pour les animations
        this.setupIntersectionObserver();

        // Écouter les événements de thème pour les transitions
        window.addEventListener('themeChanged', (e) => {
            this.handleThemeChange(e.detail);
        });

        console.log('✨ AnimationManager initialisé');
    }

    /**
     * Configuration de l'observer d'intersection pour les animations au scroll
     */
    setupIntersectionObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        this.intersectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateIn(entry.target);
                }
            });
        }, observerOptions);
    }

    /**
     * Animer un élément entrant dans la vue
     */
    animateIn(element) {
        const animationType = element.dataset.animation || 'fade-in';
        element.classList.add(`animate-${animationType}`);
        element.style.opacity = '1';

        // Supprimer la classe après l'animation
        setTimeout(() => {
            element.classList.remove(`animate-${animationType}`);
        }, 300);
    }

    /**
     * Animer un élément sortant de la vue
     */
    animateOut(element, callback) {
        const animationType = element.dataset.animationOut || 'fade-out';
        element.classList.add(`animate-${animationType}`);

        setTimeout(() => {
            element.style.display = 'none';
            element.classList.remove(`animate-${animationType}`);
            if (callback) callback();
        }, 300);
    }

    /**
     * Animation de rebond pour attirer l'attention
     */
    bounce(element) {
        element.classList.add('animate-bounce');
        setTimeout(() => {
            element.classList.remove('animate-bounce');
        }, 600);
    }

    /**
     * Animation de pulsation
     */
    pulse(element, duration = 2000) {
        element.classList.add('animate-pulse');
        setTimeout(() => {
            element.classList.remove('animate-pulse');
        }, duration);
    }

    /**
     * Animation de chargement
     */
    showLoading(element, text = 'Chargement...') {
        element.innerHTML = `
            <div class="loading-state">
                <div class="spinner"></div>
                <p>${text}</p>
            </div>
        `;
        element.classList.add('animate-fade-in');
    }

    /**
     * Masquer le chargement
     */
    hideLoading(element) {
        element.classList.add('animate-fade-out');
        setTimeout(() => {
            element.innerHTML = '';
            element.classList.remove('animate-fade-out');
        }, 300);
    }

    /**
     * Animation de succès
     */
    showSuccess(element, message = 'Succès !') {
        element.innerHTML = `
            <div class="success-state animate-bounce">
                <div class="success-icon">✅</div>
                <p>${message}</p>
            </div>
        `;

        setTimeout(() => {
            element.innerHTML = '';
        }, 2000);
    }

    /**
     * Animation d'erreur
     */
    showError(element, message = 'Erreur') {
        element.innerHTML = `
            <div class="error-state animate-bounce">
                <div class="error-icon">❌</div>
                <p>${message}</p>
            </div>
        `;

        setTimeout(() => {
            element.innerHTML = '';
        }, 3000);
    }

    /**
     * Animation de message entrant
     */
    animateMessageIn(messageElement) {
        messageElement.classList.add('message-enter');
        messageElement.style.opacity = '0';
        messageElement.style.transform = 'translateY(20px)';

        requestAnimationFrame(() => {
            messageElement.style.transition = 'all 0.3s ease-out';
            messageElement.style.opacity = '1';
            messageElement.style.transform = 'translateY(0)';
        });

        setTimeout(() => {
            messageElement.classList.remove('message-enter');
        }, 300);
    }

    /**
     * Animation de conversation sélectionnée
     */
    animateConversationSelect(conversationElement) {
        // Supprimer la classe selected de tous les éléments
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('selected');
        });

        // Ajouter la classe selected avec animation
        conversationElement.classList.add('selected');
        this.bounce(conversationElement);
    }

    /**
     * Animation de notification
     */
    animateNotification(notificationElement) {
        notificationElement.classList.add('notification-enter');
        this.bounce(notificationElement);

        // Supprimer après 5 secondes
        setTimeout(() => {
            this.animateOut(notificationElement);
        }, 5000);
    }

    /**
     * Animation de modal
     */
    animateModalIn(modalElement) {
        modalElement.style.display = 'flex';
        modalElement.classList.add('animate-fade-in');

        const modalContent = modalElement.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('animate-slide-in-right');
        }
    }

    /**
     * Animation de modal sortant
     */
    animateModalOut(modalElement, callback) {
        const modalContent = modalElement.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('animate-fade-out');
        }

        modalElement.classList.add('animate-fade-out');

        setTimeout(() => {
            modalElement.style.display = 'none';
            modalElement.classList.remove('animate-fade-in', 'animate-fade-out');
            if (modalContent) {
                modalContent.classList.remove('animate-slide-in-right', 'animate-fade-out');
            }
            if (callback) callback();
        }, 300);
    }

    /**
     * Animation de changement de thème
     */
    handleThemeChange(themeData) {
        // Animation de transition pour le changement de thème
        document.body.style.transition = 'background-color 0.5s ease, color 0.5s ease';

        // Animer le bouton de thème
        const themeBtn = document.querySelector('.theme-toggle-btn');
        if (themeBtn) {
            this.bounce(themeBtn);
        }

        setTimeout(() => {
            document.body.style.transition = '';
        }, 500);
    }

    /**
     * Observer un élément pour les animations d'entrée
     */
    observe(element, animationType = 'fade-in') {
        element.dataset.animation = animationType;
        element.style.opacity = '0';
        this.intersectionObserver.observe(element);
    }

    /**
     * Arrêter d'observer un élément
     */
    unobserve(element) {
        this.intersectionObserver.unobserve(element);
    }
}

// Initialiser globalement
window.AnimationManager = AnimationManager;
