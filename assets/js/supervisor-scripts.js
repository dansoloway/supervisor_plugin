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
    const dropdownItems = document.querySelectorAll('.supervisor-home .site-nav .nav-item.dropdown');
    dropdownItems.forEach(dropdown => {
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        
        if (dropdownMenu) {
            // Toggle dropdown on click
            dropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Close any other open dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                        menu.parentElement.classList.remove('active');
                    }
                });
                
                // Toggle current dropdown
                dropdownMenu.classList.toggle('show');
                dropdown.classList.toggle('active');
            });
            
            // Show dropdown on hover (optional)
            dropdown.addEventListener('mouseenter', function() {
                // Close any other open dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                        menu.parentElement.classList.remove('active');
                    }
                });
                
                dropdownMenu.classList.add('show');
                dropdown.classList.add('active');
            });
            
            // Hide dropdown when mouse leaves (but with delay to allow clicking)
            let hideTimeout;
            dropdown.addEventListener('mouseleave', function() {
                hideTimeout = setTimeout(() => {
                    dropdownMenu.classList.remove('show');
                    dropdown.classList.remove('active');
                }, 150); // Small delay to allow clicking
            });
            
            // Keep dropdown open when hovering over the menu
            dropdownMenu.addEventListener('mouseenter', function() {
                clearTimeout(hideTimeout);
                dropdownMenu.classList.add('show');
                dropdown.classList.add('active');
            });
            
            dropdownMenu.addEventListener('mouseleave', function() {
                hideTimeout = setTimeout(() => {
                    dropdownMenu.classList.remove('show');
                    dropdown.classList.remove('active');
                }, 150);
            });
            
            // Allow clicking on submenu items
            dropdownMenu.addEventListener('click', function(e) {
                if (e.target.classList.contains('submenu-item')) {
                    // Allow the link to work normally
                    e.stopPropagation();
                    console.log('Submenu item clicked:', e.target.href);
                    // Don't prevent default - let the link navigate
                }
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
                menu.parentElement.classList.remove('active');
            });
        }
    });
});