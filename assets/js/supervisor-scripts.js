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
    
    // Mobile hamburger menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    
    // Function to close mobile menu
    function closeMobileMenu() {
        if (mobileMenu) {
            mobileMenu.classList.remove('mobile-active');
            if (mobileMenuToggle) {
                mobileMenuToggle.classList.remove('active');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
            }
            document.body.style.overflow = '';
        }
    }
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle menu visibility
            const isActive = mobileMenu.classList.toggle('mobile-active');
            mobileMenuToggle.classList.toggle('active');
            mobileMenuToggle.setAttribute('aria-expanded', isActive ? 'true' : 'false');
            
            // Prevent body scroll when menu is open
            if (isActive) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        
        // Close button click handler
        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMobileMenu();
            });
        }
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileMenuToggle.contains(e.target) && 
                !mobileMenu.contains(e.target) && 
                (!mobileMenuClose || !mobileMenuClose.contains(e.target))) {
                closeMobileMenu();
            }
        });
        
        // Close menu when a link is clicked (for better UX on mobile)
        const mobileNavLinks = mobileMenu.querySelectorAll('a');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Only close if it's not a dropdown toggle
                if (!this.classList.contains('dropdown')) {
                    mobileMenu.classList.remove('mobile-active');
                    mobileMenuToggle.classList.remove('active');
                    mobileMenuToggle.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                }
            });
        });
    }
    
    // Dropdown menu functionality - desktop menu
    const dropdownItems = document.querySelectorAll('.supervisor-home .desktop-menu a.dropdown');
    
    dropdownItems.forEach(dropdown => {
        // Find the dropdown menu that follows this dropdown item
        const dropdownMenu = dropdown.nextElementSibling;
        
        if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
            let isClickMode = false;
            let hoverTimeout = null;
            
            // Toggle dropdown on click
            dropdown.addEventListener('click', function(e) {
                // On mobile, always use click mode for dropdowns
                const isMobile = window.innerWidth <= 768;
                
                if (isMobile || !this.href || this.href === '#' || this.href.endsWith('#') || this.href.endsWith(window.location.pathname + '#')) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                isClickMode = true;
                
                // Close any other open dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                        menu.previousElementSibling.classList.remove('active');
                    }
                });
                
                // Toggle current dropdown
                dropdownMenu.classList.toggle('show');
                dropdown.classList.toggle('active');
                
                // Reset click mode after a delay
                setTimeout(() => {
                    isClickMode = false;
                }, 100);
            });
            
            // Show dropdown on hover (only if not in click mode)
            dropdown.addEventListener('mouseenter', function() {
                if (isClickMode) return;
                
                // Clear any pending hide timeout
                if (hoverTimeout) {
                    clearTimeout(hoverTimeout);
                    hoverTimeout = null;
                }
                
                // Close any other open dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                        menu.previousElementSibling.classList.remove('active');
                    }
                });
                
                dropdownMenu.classList.add('show');
                dropdown.classList.add('active');
            });
            
            // Hide dropdown when mouse leaves (with delay to prevent flickering)
            dropdown.addEventListener('mouseleave', function() {
                if (isClickMode) return;
                
                hoverTimeout = setTimeout(() => {
                    if (!dropdownMenu.matches(':hover') && !dropdown.matches(':hover')) {
                        dropdownMenu.classList.remove('show');
                        dropdown.classList.remove('active');
                    }
                }, 150);
            });
            
            // Keep dropdown open when hovering over the menu
            dropdownMenu.addEventListener('mouseenter', function() {
                if (hoverTimeout) {
                    clearTimeout(hoverTimeout);
                    hoverTimeout = null;
                }
                dropdownMenu.classList.add('show');
                dropdown.classList.add('active');
            });
            
            dropdownMenu.addEventListener('mouseleave', function() {
                if (isClickMode) return;
                
                hoverTimeout = setTimeout(() => {
                    if (!dropdownMenu.matches(':hover') && !dropdown.matches(':hover')) {
                        dropdownMenu.classList.remove('show');
                        dropdown.classList.remove('active');
                    }
                }, 150);
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown') && !e.target.closest('.dropdown-menu')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
                menu.previousElementSibling.classList.remove('active');
            });
        }
    });
    
    // Mobile menu dropdown functionality (separate from desktop)
    if (mobileMenu) {
        const mobileDropdownItems = mobileMenu.querySelectorAll('a.dropdown');
        
        mobileDropdownItems.forEach(dropdown => {
            const dropdownMenu = dropdown.nextElementSibling;
            
            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                dropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Close any other open dropdowns
                    mobileMenu.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                            menu.previousElementSibling.classList.remove('active');
                        }
                    });
                    
                    // Toggle current dropdown
                    dropdownMenu.classList.toggle('show');
                    dropdown.classList.toggle('active');
                });
            }
        });
    }
});