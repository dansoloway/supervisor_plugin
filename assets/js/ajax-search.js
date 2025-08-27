jQuery(document).ready(function ($) {
    // Ensure jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not available');
        return;
    }
    
    // Debounce function to prevent too many requests
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
                            results.forEach((item, index) => {
                                if (item && item.title && item.link) {
                                    const accordionId = 'search-result-' + index;
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
                                                    ${item.source_link ? `<p><strong>לקישור:</strong> <a href="${item.source_link}" target="_blank" class="source-link">${item.source_link}</a></p>` : ''}
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

                        // Hide initial content and show search results
                        $('.initial-content').hide();
                        $('.search-results-container').html(output).show();
                        
                        // Initialize accordion functionality for search results
                        initializeSearchAccordions();
                    } else {
                        console.error('Search failed:', response);
                        $('.initial-content').hide();
                        $('.search-results-container').html('<p class="no-results">שגיאה בחיפוש. אנא נסה שוב.</p>').show();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    $('.initial-content').hide();
                    $('.search-results-container').html('<p class="no-results">שגיאה בחיפוש. אנא נסה שוב.</p>').show();
                }
            });
        } catch (error) {
            console.error('Error in search function:', error);
            $('.initial-content').hide();
            $('.search-results-container').html('<p class="no-results">שגיאה בחיפוש. אנא נסה שוב.</p>').show();
        }
    }
    
    // Debounced version of performSearch for automatic filtering
    const debouncedSearch = debounce(performSearch, 300);
    
    // Event listeners for search buttons (only for AJAX search component)
    $('#search-submit').on('click', function (e) {
        e.preventDefault();
        performSearch();
    });
    
    // Only target search buttons within the AJAX search component
    $('.ajax-search-component .search-button').on('click', function (e) {
        e.preventDefault();
        performSearch();
    });
    
    // Also allow Enter key in search input (only for AJAX search component)
    $('#search-text').on('keypress', function (e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            performSearch();
        }
    });
    
    // Automatic filtering for checkboxes
    $('input[name="qa_themes[]"], input[name="qa_tags[]"]').on('change', function() {
        // Only trigger automatic search if there are any checkboxes checked or if there's search text
        const hasCheckedBoxes = $('input[name="qa_themes[]"]:checked, input[name="qa_tags[]"]:checked').length > 0;
        const hasSearchText = $('#search-text').val().trim() !== '';
        
        if (hasCheckedBoxes || hasSearchText) {
            debouncedSearch();
        } else {
            // If no filters are selected and no search text, show initial content
            $('.search-results-container').hide();
            $('.initial-content').show();
        }
    });
    
    // Initialize accordion functionality for search results
    function initializeSearchAccordions() {
        // Only target accordions within search results
        const searchResultsContainer = document.querySelector('.search-results-container');
        if (!searchResultsContainer) return;
        
        const searchAccordions = searchResultsContainer.querySelectorAll('.accordion-header');
        
        searchAccordions.forEach(header => {
            // Remove any existing click listeners by cloning
            const newHeader = header.cloneNode(true);
            header.parentNode.replaceChild(newHeader, header);
            
            // Add new click listener
            newHeader.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                const accordionId = this.getAttribute('data-accordion');
                if (!accordionId) return;
                
                const content = document.getElementById('accordion-' + accordionId);
                const icon = document.getElementById('icon-' + accordionId);
                
                if (!content || !icon) return;
                
                // Close all other accordions first (both search and main)
                const allAccordions = document.querySelectorAll('.accordion-header');
                allAccordions.forEach(otherHeader => {
                    if (otherHeader !== this) {
                        const otherAccordionId = otherHeader.getAttribute('data-accordion');
                        if (otherAccordionId) {
                            const otherContent = document.getElementById('accordion-' + otherAccordionId);
                            const otherIcon = document.getElementById('icon-' + otherAccordionId);
                            
                            if (otherContent && otherIcon) {
                                otherContent.style.display = 'none';
                                otherIcon.innerHTML = '⌄';
                            }
                        }
                    }
                });
                
                // Toggle the clicked accordion
                if (content.style.display === 'none' || content.style.display === '') {
                    content.style.display = 'block';
                    icon.innerHTML = '⌃';
                } else {
                    content.style.display = 'none';
                    icon.innerHTML = '⌄';
                }
            });
        });
    }
});