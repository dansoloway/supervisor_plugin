jQuery(document).ready(function ($) {
    // Ensure jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not available');
        return;
    }
    
    $('#search-submit').on('click', function (e) {
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
            
            const searchText = $('#search-text').val().trim();
            
            console.log('Search text:', searchText);
            console.log('Selected themes:', selectedThemes);
            console.log('Selected tags:', selectedTags);
            
            // Only search if there's a search term or selected filters
            if (!searchText && selectedThemes.length === 0 && selectedTags.length === 0) {
                console.log('No search criteria provided');
                $('.qa-updates-list').html('<p class="no-results">אנא הכנס טקסט לחיפוש או בחר קטגוריות</p>');
                return;
            }
            
            const searchData = {
                search_text: searchText,
                qa_themes: selectedThemes,
                qa_tags: selectedTags,
                post_types: ['qa_updates'], // Only search updates on this page
            };

            console.log('Search data being sent:', searchData);

            // Use dynamic URL based on current site
            const ajaxUrl = window.location.origin + '/wp-content/plugins/supervisor-plugin/ajax/search_handler.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: searchData,
                dataType: 'json',
                success: function (response) {
                    console.log('AJAX response:', response);
                    
                    if (response && response.success) {
                        const results = response.data || [];
                        let output = '';

                        if (results.length > 0) {
                            output += '<div class="search-results-container">';
                            results.forEach((item) => {
                                if (item && item.title && item.link) {
                                    output += `
                                        <div class="qa-update-item">
                                            <div class="light-green-bkg accordion-header">
                                                <div class="qa-update-title">
                                                    <h3><a href="${item.link}" class="search-result-link">${item.title}</a></h3>
                                                    <span class="post-type-label">${item.type === 'qa_updates' ? 'עדכון' : item.type === 'qa_orgs' ? 'ארגון' : 'ביבליוגרפיה'}</span>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }
                            });
                            output += '</div>';
                        } else {
                            output = '<p class="no-results">לא נמצאו תוצאות.</p>';
                        }

                        // Display the results on the page
                        $('.qa-updates-list').html(output);
                    } else {
                        console.error('Error in response:', response);
                        $('.qa-updates-list').html('<p class="error">שגיאה בחיפוש.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX request failed:', status, error);
                    console.error('Response text:', xhr.responseText);
                    $('.qa-updates-list').html('<p class="error">שגיאה בחיבור לשרת.</p>');
                },
            });
        } catch (error) {
            console.error('Error in search function:', error);
            $('.ajax-search-results').html('<p class="error">שגיאה בלתי צפויה.</p>');
        }
    });
    
    // Show All Updates Button
    $('#show-all-updates').on('click', function (e) {
        e.preventDefault();
        
        // Show the initial content (all updates)
        $('.initial-content').show();
        $('.search-results-container').empty();
        
        // Reinitialize accordion functionality
        initializeAccordions();
    });
    
    // Initialize accordion functionality
    function initializeAccordions() {
        const accordions = document.querySelectorAll('.accordion-header');
        
        accordions.forEach(header => {
            header.addEventListener('click', function () {
                const postId = this.getAttribute('data-accordion');
                const content = document.getElementById('accordion-' + postId);
                const icon = document.getElementById('icon-' + postId);
                
                if (content && icon) {
                    if (content.style.display === 'none') {
                        content.style.display = 'block';
                        icon.innerHTML = '⌃';
                    } else {
                        content.style.display = 'none';
                        icon.innerHTML = '⌄';
                    }
                }
            });
        });
    }
});