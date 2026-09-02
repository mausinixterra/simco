/**
 * Navbar Module - Theme Toggle & Language Menu
 * Manages dark mode theme switching and language menu interactions
 */

const NavbarModule = {
    /**
     * Initialize all navbar functionalities
     */
    init() {
        this.initTheme();
        this.initLanguageMenu();
    },

    /**
     * Initialize theme system (dark/light mode)
     */
    initTheme() {
        // Apply saved theme immediately on page load
        this.applyStoredTheme();
        
        // Wait for DOM to be ready before attaching event listeners
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.attachThemeListeners();
            });
        } else {
            this.attachThemeListeners();
        }
    },

    /**
     * Apply theme from localStorage immediately (prevents flash)
     */
    applyStoredTheme() {
        const theme = localStorage.getItem('color-theme') || 'light';
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    /**
     * Attach event listeners for theme toggle button
     */
    attachThemeListeners() {
        const themeToggle = document.getElementById('theme-toggle');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        const lightIcon = document.getElementById('theme-toggle-light-icon');
        
        if (!themeToggle || !darkIcon || !lightIcon) {
            console.warn('Theme toggle elements not found');
            return;
        }

        // Update icons based on current theme
        this.updateThemeIcons();
        
        // Toggle theme on button click
        themeToggle.addEventListener('click', () => {
            this.toggleTheme();
        });
    },

    /**
     * Toggle between dark and light themes
     */
    toggleTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        
        if (isDark) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }
        
        this.updateThemeIcons();
    },

    /**
     * Update theme toggle icons visibility
     */
    updateThemeIcons() {
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        const lightIcon = document.getElementById('theme-toggle-light-icon');
        
        if (!darkIcon || !lightIcon) return;

        if (document.documentElement.classList.contains('dark')) {
            // Dark mode active: show light/sun icon
            lightIcon.classList.remove('hidden');
            darkIcon.classList.add('hidden');
        } else {
            // Light mode active: show dark/moon icon
            darkIcon.classList.remove('hidden');
            lightIcon.classList.add('hidden');
        }
    },

    /**
     * Initialize language menu dropdown
     */
    initLanguageMenu() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.attachLanguageMenuListeners();
            });
        } else {
            this.attachLanguageMenuListeners();
        }
    },

    /**
     * Attach event listeners for language menu
     */
    attachLanguageMenuListeners() {
        const langMenuButton = document.getElementById('lang-menu-button');
        const langMenu = document.getElementById('lang-menu');
        
        if (!langMenuButton || !langMenu) {
            console.warn('Language menu elements not found');
            return;
        }

        // Toggle menu on button click
        langMenuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            langMenu.classList.toggle('hidden');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!langMenuButton.contains(e.target) && !langMenu.contains(e.target)) {
                langMenu.classList.add('hidden');
            }
        });
    }
};

// Initialize immediately (theme must be applied ASAP to prevent flash)
NavbarModule.init();
