/**
 * Supervisor Plugin JavaScript
 * Handles interactive elements and dynamic functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Remove inline animation styles that override CSS
    const animatedElements = document.querySelectorAll('.animate-in');
    animatedElements.forEach(element => {
        element.removeAttribute('style');
    });
    
    // Knowledge Map Interaction
    const quadrants = document.querySelectorAll('.quadrant');
    const centerNode = document.querySelector('.center-node');
    
    if (quadrants.length > 0) {
        quadrants.forEach(quadrant => {
            quadrant.addEventListener('click', function() {
                const section = this.getAttribute('data-section');
                handleQuadrantClick(section);
            });
        });
    }
    
    if (centerNode) {
        centerNode.addEventListener('click', function() {
            handleCenterNodeClick();
        });
    }
    
    // Handle quadrant clicks
    function handleQuadrantClick(section) {
        console.log('Quadrant clicked:', section);
        
        // Add visual feedback
        const clickedQuadrant = document.querySelector(`[data-section="${section}"]`);
        if (clickedQuadrant) {
            clickedQuadrant.style.backgroundColor = 'rgba(58,180,222,0.2)';
            setTimeout(() => {
                clickedQuadrant.style.backgroundColor = '';
            }, 500);
        }
        
        // You can add navigation logic here based on the section
        const sectionUrls = {
            'policy': '/policy-section/',
            'control': '/control-section/',
            'characterization': '/characterization-section/',
            'distribution': '/distribution-section/'
        };
        
        // Uncomment the line below to enable navigation
        // if (sectionUrls[section]) window.location.href = sectionUrls[section];
    }
    
    // Handle center node click
    function handleCenterNodeClick() {
        console.log('Center node clicked');
        
        // Add visual feedback
        centerNode.style.backgroundColor = 'var(--accent-color)';
        setTimeout(() => {
            centerNode.style.backgroundColor = 'var(--primary-color)';
        }, 500);
        
        // You can add navigation logic here
        // window.location.href = '/quality-control-overview/';
    }
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('.supervisor_header_links a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add smooth transition effect
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Enhanced hover effects for buttons
    const buttons = document.querySelectorAll('.vertical-button, .more-updates-button, .knowledge-map-button');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Search functionality enhancement
    const searchForm = document.getElementById('ajax-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Add loading state
            const submitButton = this.querySelector('#search-submit');
            if (submitButton) {
                const originalText = submitButton.textContent;
                submitButton.textContent = 'מחפש...';
                submitButton.disabled = true;
                
                // Simulate search delay (replace with actual AJAX call)
                setTimeout(() => {
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }, 1000);
            }
        });
    }
    
    // Accordion functionality for updates
    const accordionToggles = document.querySelectorAll('.accordion-toggle');
    accordionToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const isExpanded = this.getAttribute('data-state') === 'expanded';
            
            if (isExpanded) {
                this.setAttribute('data-state', 'collapsed');
                content.style.display = 'none';
            } else {
                this.setAttribute('data-state', 'expanded');
                content.style.display = 'block';
            }
        });
    });
    
    // Responsive navigation toggle (for mobile)
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    if (mobileNavToggle) {
        mobileNavToggle.addEventListener('click', function() {
            const nav = document.querySelector('.supervisor_header_links');
            nav.classList.toggle('mobile-active');
        });
    }
    
    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close any open modals or dropdowns
            const activeElements = document.querySelectorAll('.mobile-active, .expanded');
            activeElements.forEach(element => {
                element.classList.remove('mobile-active', 'expanded');
            });
        }
    });
});

// Utility functions
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

// Handle window resize
window.addEventListener('resize', debounce(function() {
    // Recalculate any dynamic layouts
    console.log('Window resized');
}, 250));