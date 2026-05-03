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
    
    // Dropdown menu — desktop: label opens only (never closes on label click); caret toggles open/close.
    const desktopDropdownGroups = document.querySelectorAll('.nav-wrapper .site-nav.supervisor_header_links.desktop-menu .nav-item-group');

    function setDesktopDropdownAria(group, open) {
        const toggles = group.querySelectorAll('.dropdown-toggle, button.nav-item.dropdown');
        toggles.forEach(t => t.setAttribute('aria-expanded', open ? 'true' : 'false'));
    }

    function closeAllDesktopDropdowns(exceptGroup = null) {
        desktopDropdownGroups.forEach(group => {
            if (exceptGroup && group === exceptGroup) return;
            const menu = group.querySelector('.dropdown-menu');
            if (menu) menu.classList.remove('show');
            group.classList.remove('is-open');
            setDesktopDropdownAria(group, false);
        });
    }

    desktopDropdownGroups.forEach(group => {
        const menu = group.querySelector('.dropdown-menu');
        const label = group.querySelector('button.nav-item.dropdown');
        const caret = group.querySelector('.dropdown-toggle');
        if (!menu || (!label && !caret)) return;

        if (caret) {
            caret.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const willOpen = !menu.classList.contains('show');
                closeAllDesktopDropdowns(group);

                if (willOpen) {
                    menu.classList.add('show');
                    group.classList.add('is-open');
                    setDesktopDropdownAria(group, true);
                } else {
                    menu.classList.remove('show');
                    group.classList.remove('is-open');
                    setDesktopDropdownAria(group, false);
                }
            });
        }

        if (label) {
            label.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (menu.classList.contains('show')) {
                    return;
                }

                closeAllDesktopDropdowns(group);
                menu.classList.add('show');
                group.classList.add('is-open');
                setDesktopDropdownAria(group, true);
            });
        }
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
        const clickedTopLevel = e.target.closest('.nav-wrapper .site-nav.supervisor_header_links a.nav-item, .nav-wrapper .site-nav.supervisor_header_links button.nav-item');
        if (!clickedTopLevel) return;

        if (clickedTopLevel.classList.contains('dropdown')) return;
        if (e.target.closest('.dropdown-menu')) return;

        closeAllDesktopDropdowns();
    });
    
    // Mobile menu dropdown: label button + caret both toggle (same DOM as desktop)
    if (mobileMenu) {
        mobileMenu.querySelectorAll('.nav-item-group').forEach(group => {
            const dropdownMenu = group.querySelector('.dropdown-menu');
            const parentTab = group.querySelector('.nav-item.dropdown');
            const triggers = group.querySelectorAll('.dropdown-toggle, button.nav-item.dropdown');
            if (!dropdownMenu || triggers.length === 0) return;

            triggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    mobileMenu.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                            const g = menu.closest('.nav-item-group');
                            const p = g && g.querySelector('.nav-item.dropdown');
                            if (p) p.classList.remove('active');
                        }
                    });

                    dropdownMenu.classList.toggle('show');
                    if (parentTab) parentTab.classList.toggle('active');
                });
            });
        });
    }
});