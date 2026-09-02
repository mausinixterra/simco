/**
 * Database Error Page Scripts
 * Handles retry button functionality with loading animation
 */

/**
 * Retry connection functionality with visual feedback
 */
function retryConnection() {
    const button = document.getElementById('retry-button');
    const retryIcon = document.getElementById('retry-icon');
    const retrySpinner = document.getElementById('retry-spinner');
    const retryText = document.getElementById('retry-text');
    
    // Disable button and show loading state
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
    button.classList.remove('cursor-pointer');
    
    // Hide retry icon, show spinner
    retryIcon.classList.add('hidden');
    retrySpinner.classList.remove('hidden');
    
    // Change text to show loading state
    const retryingText = retryText.getAttribute('data-retrying-text') || 'Reintentando...';
    retryText.textContent = retryingText;
    
    // Wait a moment before reloading to show the animation
    setTimeout(() => {
        location.reload();
    }, 800);
}

/**
 * Auto-retry functionality (optional)
 * Uncomment to enable automatic retry after a delay
 */
// setTimeout(() => {
//     console.log('Auto-retrying connection...');
//     location.reload();
// }, 30000); // 30 seconds
