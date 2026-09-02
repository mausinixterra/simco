/**
 * Form Initialization Module
 * Initializes the PQRSF form validation and handles translations
 */

const FormInitModule = {
    /**
     * Initialize form with translations
     * @param {Object} translations - Translation strings from PHP
     */
    init(translations) {
        // Initialize form validation
        if (typeof initializeForm === 'function') {
            initializeForm(translations);
        } else {
            console.error('initializeForm function not found. Make sure form-validation.js is loaded first.');
        }
    },

    /**
     * Reset form after successful submission
     */
    resetForm() {
        if (typeof resetFormAfterSuccess === 'function') {
            resetFormAfterSuccess();
        }
    }
};

// Export for use in inline scripts
window.FormInitModule = FormInitModule;
