/**
 * 🎨 Service de Gestion des Thèmes
 * Système de thèmes clair/sombre avec persistance
 */

class ThemeManager {
    constructor() {
        this.currentTheme = 'dark'; // 'light' ou 'dark'
        this.themes = {
            light: {
                name: 'Clair',
                icon: '☀️',
                colors: {
                    primary: '#ffffff',
                    secondary: '#f8f9fa',
                    accent: '#007bff',
                    text: '#212529',
                    textSecondary: '#6c757d',
                    border: '#dee2e6',
                    shadow: 'rgba(0, 0, 0, 0.1)',
                    gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
                }
            },
            dark: {
                name: 'Sombre',
                icon: '🌙',
                colors: {
                    primary: '#1a1a2e',
                    secondary: '#16213e',
                    accent: '#fbbb2a',
                    text: '#ffffff',
                    textSecondary: '#b8c5d6',
                    border: 'rgba(255, 255, 255, 0.1)',
                    shadow: 'rgba(0, 0, 0, 0.3)',
                    gradient: 'linear-gradient(135deg, #1a1a2e 0%, #16213e 100%)'
                }
            }
        };

        this.init();
    }

    /**
     * Initialiser le gestionnaire de thèmes
     */
    init() {
        // Charger le thème sauvegardé
        const savedTheme = localStorage.getItem('messaging-theme') || 'dark';
        this.setTheme(savedTheme);

        // Écouter les changements de préférence système
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('messaging-theme')) {
                    this.setTheme(e.matches ? 'dark' : 'light');
                }
            });
        }

        console.log('🎨 ThemeManager initialisé avec thème:', this.currentTheme);
    }

    /**
     * Définir le thème actif
     */
    setTheme(themeName) {
        if (!this.themes[themeName]) {
            console.warn('Thème inconnu:', themeName);
            return;
        }

        this.currentTheme = themeName;
        const theme = this.themes[themeName];

        // Appliquer les variables CSS
        this.applyThemeVariables(theme);

        // Sauvegarder la préférence
        localStorage.setItem('messaging-theme', themeName);

        // Mettre à jour l'attribut data-theme sur le body
        document.body.setAttribute('data-theme', themeName);

        // Émettre un événement de changement de thème
        window.dispatchEvent(new CustomEvent('themeChanged', {
            detail: { theme: themeName, themeData: theme }
        }));

        console.log('🎨 Thème changé:', themeName);
    }

    /**
     * Appliquer les variables CSS du thème
     */
    applyThemeVariables(theme) {
        const root = document.documentElement;

        Object.entries(theme.colors).forEach(([key, value]) => {
            root.style.setProperty(`--theme-${key}`, value);
        });
    }

    /**
     * Basculer entre les thèmes
     */
    toggleTheme() {
        const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }

    /**
     * Obtenir le thème actuel
     */
    getCurrentTheme() {
        return this.themes[this.currentTheme];
    }

    /**
     * Obtenir tous les thèmes disponibles
     */
    getAvailableThemes() {
        return Object.keys(this.themes);
    }

    /**
     * Créer un bouton de basculement de thème
     */
    createThemeToggle(container) {
        const button = document.createElement('button');
        button.className = 'theme-toggle-btn';
        button.title = 'Changer de thème';
        button.innerHTML = this.themes[this.currentTheme].icon;

        button.addEventListener('click', () => {
            this.toggleTheme();
            button.innerHTML = this.themes[this.currentTheme].icon;
        });

        container.appendChild(button);
        return button;
    }
}

// Initialiser globalement
window.ThemeManager = ThemeManager;
