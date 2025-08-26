jQuery(document).ready(function($) {
    // Home page search functionality
    function handleHomeSearch() {
        const searchInput = $('.supervisor-search .supervisor-search-bar');
        const searchTerm = searchInput.val().trim();
        
        if (searchTerm) {
            // Submit the form normally
            $('.supervisor-search').submit();
        }
    }
    
    // Handle Enter key press
    $('.supervisor-search .supervisor-search-bar').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            handleHomeSearch();
        }
    });
    
    // Handle search button click
    $('.supervisor-search .search-button').on('click', function(e) {
        e.preventDefault();
        handleHomeSearch();
    });
});
