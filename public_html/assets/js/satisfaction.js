/**
 * Satisfaction Form JavaScript
 * Handles form interactivity and validation
 */

document.addEventListener('DOMContentLoaded', function() {
    const yesRadio = document.getElementById('si');
    const noRadio = document.getElementById('no');
    const reasonField = document.getElementById('campo-motivo');
    const reasonTextarea = document.getElementById('motivo');
    const submitButton = document.getElementById('boton-enviar');
    const charactersRemaining = document.getElementById('caracteres-restantes');

    // Enable submit button when "Yes" is selected
    function handleYesSelection() {
        submitButton.disabled = false;
        reasonField.classList.add('hidden');
        reasonTextarea.removeAttribute('required');
        reasonTextarea.value = '';
    }

    // Show reason field when "No" is selected
    function handleNoSelection() {
        reasonField.classList.remove('hidden');
        submitButton.disabled = true;
        reasonTextarea.setAttribute('required', 'required');
        
        // Add character counter listener
        reasonTextarea.addEventListener('input', handleTextareaInput);
    }

    // Handle textarea input
    function handleTextareaInput() {
        const currentLength = reasonTextarea.value.length;
        const remaining = 240 - currentLength;
        
        charactersRemaining.textContent = remaining;
        
        // Change color when approaching limit
        if (remaining <= 15) {
            charactersRemaining.classList.add('text-red-600', 'dark:text-red-400');
            charactersRemaining.classList.remove('text-gray-600', 'dark:text-gray-400');
        } else {
            charactersRemaining.classList.add('text-gray-600', 'dark:text-gray-400');
            charactersRemaining.classList.remove('text-red-600', 'dark:text-red-400');
        }
        
        // Enable/disable submit button based on minimum length
        if (currentLength >= 10) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }

    // Prevent paste in textarea
    if (reasonTextarea) {
        reasonTextarea.addEventListener('paste', function(event) {
            event.preventDefault();
        });
    }

    // Attach event listeners to radio buttons
    if (yesRadio) {
        yesRadio.addEventListener('change', handleYesSelection);
    }

    if (noRadio) {
        noRadio.addEventListener('change', handleNoSelection);
    }

    // Disable submit button on form submission to prevent double submission
    const form = document.getElementById('formulario-conformidad');
    if (form) {
        form.addEventListener('submit', function() {
            submitButton.disabled = true;
            return true;
        });
    }
});
