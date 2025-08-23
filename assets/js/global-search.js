jQuery(document).ready(function ($) {
    // Ensure jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not available');
        return;
    }
    
    $('#global-search-submit').on('click', function (e) {
        e.preventDefault();
        
        try {
            // Properly collect checkbox values
            const selectedThemes = [];
            $('input[name="qa_themes[]"]:checked').each(function() {
                const value = $(this).val();
                if (value && value.trim() !== '') {
                    selectedThemes.push(value.trim());
                }
            });
            
            const selectedTags = [];
            $('input[name="qa_tags[]"]:checked').each(function() {
                const value = $(this).val();
                if (value && value.trim() !== '') {
                    selectedTags.push(value.trim());
                }
            });
            
            const searchText = $('#global-search-text').val().trim();
            
            console.log('Global search text:', searchText);
            console.log('Selected themes:', selectedThemes);
            console.log('Selected tags:', selectedTags);
            
            // Only search if there's a search term or selected filters
            if (!searchText && selectedThemes.length === 0 && selectedTags.length === 0) {
                console.log('No search criteria provided');
                $('.global-search-results').html('<p class="no-results">אנא הכנס טקסט לחיפוש או בחר קטגוריות</p>');
                return;
            }
            
            const searchData = {
                search_text: searchText,
                qa_themes: selectedThemes,
                qa_tags: selectedTags,
                post_types: ['qa_orgs', 'qa_updates', 'qa_bibs'], // Search all post types
            };

            console.log('Global search data being sent:', searchData);

            // Use dynamic URL based on current site
            const ajaxUrl = window.location.origin + '/wp-content/plugins/supervisor-plugin/ajax/search_handler.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: searchData,
                dataType: 'json',
                success: function (response) {
                    console.log('Global AJAX response:', response);
                    
                    if (response && response.success) {
                        const results = response.data || [];
                        let output = '';

                        if (results.length > 0) {
                            output += '<ul class="global-search-results-list">';
                            results.forEach((item) => {
                                if (item && item.title && item.link) {
                                    const postTypeLabel = getPostTypeLabel(item.type);
                                    output += `
                                        <li class="global-search-result-item">
                                            <a href="${item.link}" class="global-search-result-link">
                                                ${item.title}
                                            </a>
                                            <span class="post-type-label">${postTypeLabel}</span>
                                        </li>
                                    `;
                                }
                            });
                            output += '</ul>';
                        } else {
                            output = '<p class="no-results">לא נמצאו תוצאות.</p>';
                        }

                        $('.global-search-results').html(output);
                    } else {
                        console.error('Error in response:', response);
                        $('.global-search-results').html('<p class="error">שגיאה בחיפוש.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Global AJAX request failed:', status, error);
                    console.error('Response text:', xhr.responseText);
                    $('.global-search-results').html('<p class="error">שגיאה בחיבור לשרת.</p>');
                },
            });
        } catch (error) {
            console.error('Error in global search function:', error);
            $('.global-search-results').html('<p class="error">שגיאה בלתי צפויה.</p>');
        }
    });
    
    // Helper function to get Hebrew labels for post types
    function getPostTypeLabel(postType) {
        const labels = {
            'qa_orgs': 'ארגונים',
            'qa_updates': 'עדכונים',
            'qa_bibs': 'ביבליוגרפיה'
        };
        return labels[postType] || postType;
    }
});
