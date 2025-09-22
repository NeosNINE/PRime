/**
 * Rules Page Interactive Functionality
 * Handles tab switching and smooth animations
 */

document.addEventListener('DOMContentLoaded', function() {
    initScrollAnimation();
});

/**
 * Handle keyboard navigation for tabs
 */
function handleTabKeyNavigation(e, tabButtons) {
    const currentIndex = Array.from(tabButtons).indexOf(e.target);
    let nextIndex;

    switch(e.key) {
        case 'ArrowLeft':
        case 'ArrowUp':
            e.preventDefault();
            nextIndex = currentIndex > 0 ? currentIndex - 1 : tabButtons.length - 1;
            tabButtons[nextIndex].focus();
            tabButtons[nextIndex].click();
            break;

        case 'ArrowRight':
        case 'ArrowDown':
            e.preventDefault();
            nextIndex = currentIndex < tabButtons.length - 1 ? currentIndex + 1 : 0;
            tabButtons[nextIndex].focus();
            tabButtons[nextIndex].click();
            break;

        case 'Home':
            e.preventDefault();
            tabButtons[0].focus();
            tabButtons[0].click();
            break;

        case 'End':
            e.preventDefault();
            tabButtons[tabButtons.length - 1].focus();
            tabButtons[tabButtons.length - 1].click();
            break;
    }
}



/**
 * Initialize scroll-based animations
 */
function initScrollAnimation() {
    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe sections for animation
    const sections = document.querySelectorAll('.rules-section');
    sections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        observer.observe(section);
    });
}



/**
 * Utility function to debounce events
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Handle responsive behavior
 */
function initResponsiveBehavior() {
    const tabsContainer = document.querySelector('.rules-tabs');

    // Check if we're on mobile
    const isMobile = () => window.innerWidth <= 768;

    // Handle resize events
    const handleResize = debounce(() => {
        if (isMobile()) {
            // Mobile-specific adjustments
            tabsContainer?.classList.add('mobile-tabs');
        } else {
            // Desktop-specific adjustments
            tabsContainer?.classList.remove('mobile-tabs');
        }
    }, 250);

    window.addEventListener('resize', handleResize);
    handleResize(); // Initial call
}

// Initialize responsive behavior
document.addEventListener('DOMContentLoaded', function() {
    initResponsiveBehavior();
});



// Export functions for potential external use
window.RulesPage = {
    switchTab,
    initScrollAnimation
};
