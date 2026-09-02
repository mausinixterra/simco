/**
 * Success Page Module
 * Handles auto-redirect countdown and visual counter for PQRSF success page
 */

const SuccessPageModule = {
    // Configuration
    config: {
        redirectUrl: '/',
        redirectDelay: 8000, // 8 seconds in milliseconds
        countdownInterval: 1000 // 1 second
    },

    // State
    state: {
        secondsLeft: 8,
        countdownIntervalId: null,
        redirectTimeoutId: null
    },

    /**
     * Initialize the success page functionality
     */
    init() {
        // Get redirect URL from the return button's href
        const returnButton = document.getElementById('return-home-btn');
        if (returnButton && returnButton.href) {
            this.config.redirectUrl = returnButton.href;
        }
        
        this.state.secondsLeft = this.config.redirectDelay / 1000;
        this.startCountdown();
        this.scheduleRedirect();
    },

    /**
     * Start the visual countdown
     */
    startCountdown() {
        const counterElement = document.getElementById('countdown');
        
        if (!counterElement) {
            console.warn('Countdown element not found');
            return;
        }

        this.state.countdownIntervalId = setInterval(() => {
            this.state.secondsLeft--;
            counterElement.textContent = this.state.secondsLeft;

            if (this.state.secondsLeft <= 0) {
                this.stopCountdown();
            }
        }, this.config.countdownInterval);
    },

    /**
     * Schedule the automatic redirect
     */
    scheduleRedirect() {
        this.state.redirectTimeoutId = setTimeout(() => {
            this.redirect();
        }, this.config.redirectDelay);
    },

    /**
     * Stop the countdown timer
     */
    stopCountdown() {
        if (this.state.countdownIntervalId) {
            clearInterval(this.state.countdownIntervalId);
            this.state.countdownIntervalId = null;
        }
    },

    /**
     * Cancel the scheduled redirect
     */
    cancelRedirect() {
        if (this.state.redirectTimeoutId) {
            clearTimeout(this.state.redirectTimeoutId);
            this.state.redirectTimeoutId = null;
        }
    },

    /**
     * Perform the redirect
     */
    redirect() {
        window.location.href = this.config.redirectUrl;
    },

    /**
     * Clean up timers (useful if page needs to be destroyed)
     */
    destroy() {
        this.stopCountdown();
        this.cancelRedirect();
    }
};

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        SuccessPageModule.init();
    });
} else {
    SuccessPageModule.init();
}
