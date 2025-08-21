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
    
    // Dropdown menu functionality
    const dropdownItems = document.querySelectorAll('.supervisor-home .site-nav a.dropdown');
    dropdownItems.forEach(dropdown => {
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        
        if (dropdownMenu) {
            // Show dropdown on hover
            dropdown.addEventListener('mouseenter', function() {
                // Close any other open dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                    }
                });
                
                dropdownMenu.classList.add('show');
            });
            
            // Hide dropdown when mouse leaves
            dropdown.addEventListener('mouseleave', function() {
                dropdownMenu.classList.remove('show');
            });
            
            // Keep dropdown open when hovering over the menu
            dropdownMenu.addEventListener('mouseenter', function() {
                dropdownMenu.classList.add('show');
            });
            
            dropdownMenu.addEventListener('mouseleave', function() {
                dropdownMenu.classList.remove('show');
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
});