jQuery(document).ready(function ($) {
    // Ensure jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not available');
        return;
    }
    
    // Function to perform search
    function performSearch() {
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
                                    const accordionId = 'search-' + item.link.split('/').pop();
                                    output += `
                                        <div class="qa-update-item">
                                            <div class="light-green-bkg accordion-header" data-accordion="${accordionId}">
                                                <div class="qa-update-title">
                                                    <div class="title-date-container">
                                                        <h3>${item.title}</h3>
                                                        <span class="update-date">${item.date || ''}</span>
                                                    </div>
                                                    <span class="accordion-icon" id="icon-${accordionId}">⌄</span>
                                                </div>
                                            </div>
                                            <div class="accordion-content" id="accordion-${accordionId}" style="display: none;">
                                                <p>${item.content || ''}</p>
                                                <div class="taxonomy-boxes">
                                                    ${item.tags && item.tags.length > 0 ? `<p><strong>נושאי מפתח:</strong> ${item.tags.join(', ')}</p>` : ''}
                                                    ${item.themes && item.themes.length > 0 ? `<p><strong>תחומים:</strong> ${item.themes.join(', ')}</p>` : ''}
                                                    ${item.source_link ? `<p><strong>לקישור:</strong> <a href="${item.source_link}" target="_blank">${item.source_link}</a></p>` : ''}
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

                        $('.qa-updates-list').html(output);
                        
                        // Initialize accordion functionality for search results
                        initializeSearchAccordions();
                    } else {
                        console.error('Search failed:', response);
                        $('.qa-updates-list').html('<p class="no-results">שגיאה בחיפוש. אנא נסה שוב.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    $('.qa-updates-list').html('<p class="no-results">שגיאה בחיפוש. אנא נסה שוב.</p>');
                }
            });
        } catch (error) {
            console.error('Error in search function:', error);
            $('.qa-updates-list').html('<p class="no-results">שגיאה בחיפוש. אנא נסה שוב.</p>');
        }
    }
    
    // Event listeners for search buttons
    $('#search-submit').on('click', function (e) {
        e.preventDefault();
        performSearch();
    });
    
    $('.search-button').on('click', function (e) {
        e.preventDefault();
        performSearch();
    });
    
    // Also allow Enter key in search input
    $('#search-text').on('keypress', function (e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            performSearch();
        }
    });
    
    // Initialize accordion functionality for search results
    function initializeSearchAccordions() {
        const accordions = document.querySelectorAll('.accordion-header');
        
        accordions.forEach(header => {
            header.addEventListener('click', function (e) {
                const accordionId = this.getAttribute('data-accordion');
                const content = document.getElementById('accordion-' + accordionId);
                const icon = document.getElementById('icon-' + accordionId);
                
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