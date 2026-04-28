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
    
    // Dropdown menu functionality - desktop menu (arrow-only toggle)
    const desktopDropdownGroups = document.querySelectorAll('.nav-wrapper .site-nav.supervisor_header_links.desktop-menu .nav-item-group');

    function closeAllDesktopDropdowns(exceptGroup = null) {
        desktopDropdownGroups.forEach(group => {
            if (exceptGroup && group === exceptGroup) return;
            const menu = group.querySelector('.dropdown-menu');
            const toggle = group.querySelector('.dropdown-toggle');
            if (menu) menu.classList.remove('show');
            group.classList.remove('is-open');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        });
    }

    desktopDropdownGroups.forEach(group => {
        const toggle = group.querySelector('.dropdown-toggle');
        const menu = group.querySelector('.dropdown-menu');
        if (!toggle || !menu) return;

        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const willOpen = !menu.classList.contains('show');
            closeAllDesktopDropdowns(group);

            if (willOpen) {
                menu.classList.add('show');
                group.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            } else {
                menu.classList.remove('show');
                group.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-wrapper .nav-item-group') && !e.target.closest('.nav-wrapper .dropdown-menu')) {
            closeAllDesktopDropdowns();
        }
    });

    // If a dropdown was opened/hover-activated, ensure it doesn't stay highlighted
    // when the user clicks a different top-level nav item.
    document.addEventListener('click', function(e) {
        const clickedTopLevelLink = e.target.closest('.nav-wrapper .site-nav.supervisor_header_links a.nav-item');
        if (!clickedTopLevelLink) return;

        // Don't immediately clear when clicking the dropdown toggle itself or inside its menu.
        if (clickedTopLevelLink.classList.contains('dropdown')) return;
        if (e.target.closest('.dropdown-menu')) return;

        closeAllDesktopDropdowns();
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