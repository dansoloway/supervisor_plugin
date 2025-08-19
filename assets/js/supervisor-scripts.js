/**
 * Supervisor Plugin JavaScript - Basic functionality only
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Remove any inline styles that might interfere
    const elementsWithInlineStyles = document.querySelectorAll('[style*="opacity"], [style*="transform"], [style*="transition"]');
    elementsWithInlineStyles.forEach(element => {
        element.removeAttribute('style');
    });
    
    // Basic search functionality
    const searchForm = document.getElementById('ajax-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Basic search handling - can be expanded later
            console.log('Search submitted');
        });
    }
    
    // Basic accordion functionality for updates (if needed)
    const accordionToggles = document.querySelectorAll('.accordion-toggle');
    accordionToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            if (content) {
                content.style.display = content.style.display === 'none' ? 'block' : 'none';
            }
        });
    });
    
    // Basic mobile navigation toggle (if needed)
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    if (mobileNavToggle) {
        mobileNavToggle.addEventListener('click', function() {
            const nav = document.querySelector('.supervisor_header_links');
            if (nav) {
                nav.classList.toggle('mobile-active');
            }
        });
    }
});