jQuery(document).ready(function ($) {
    $('#search-submit').on('click', function (e) {
        e.preventDefault();
        
        // Properly collect checkbox values
        const selectedThemes = [];
        $('input[name="qa_themes[]"]:checked').each(function() {
            const value = $(this).val();
            console.log('Theme checkbox value:', value, 'length:', value.length);
            selectedThemes.push(value);
        });
        
        const selectedTags = [];
        $('input[name="qa_tags[]"]:checked').each(function() {
            const value = $(this).val();
            console.log('Tag checkbox value:', value, 'length:', value.length);
            selectedTags.push(value);
        });
        
        const searchText = $('#search-text').val().trim();
        console.log('Search text:', searchText, 'length:', searchText.length);
        console.log('Selected themes:', selectedThemes);
        console.log('Selected tags:', selectedTags);
        
        // Filter out empty values
        const filteredThemes = selectedThemes.filter(theme => theme && theme.trim() !== '');
        const filteredTags = selectedTags.filter(tag => tag && tag.trim() !== '');
        
        console.log('Filtered themes:', filteredThemes);
        console.log('Filtered tags:', filteredTags);
        
        // Only search if there's a search term or selected filters
        if (!searchText && filteredThemes.length === 0 && filteredTags.length === 0) {
            console.log('No search criteria provided, showing validation message');
            $('.ajax-search-results').html('<p class="no-results">אנא הכנס טקסט לחיפוש או בחר קטגוריות</p>');
            return;
        }
        
        const searchData = {
            search_text: searchText,
            qa_themes: filteredThemes,
            qa_tags: filteredTags,
        };

        console.log('Search data being sent:', searchData);

        // Use dynamic URL based on current site
        const ajaxUrl = window.location.origin + '/wp-content/plugins/supervisor-plugin/ajax/search_handler.php';

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: searchData,
            success: function (response) {
                console.log('AJAX response:', response);
                
                if (response.success) {
                    const results = response.data;
                    let output = '';

                    if (results.length > 0) {
                        output += '<ul class="search-results-list">';
                        results.forEach((item) => {
                            output += `
                                <li class="search-result-item">
                                    <a href="${item.link}" class="search-result-link">
                                        ${item.title}
                                    </a>
                                </li>
                            `;
                        });
                        output += '</ul>';
                    } else {
                        output = '<p class="no-results">לא נמצאו תוצאות.</p>';
                    }

                    // Display the results on the page
                    $('.ajax-search-results').html(output);
                } else {
                    console.error('Error:', response.message);
                    $('.ajax-search-results').html('<p class="error">שגיאה בחיפוש.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error('Request failed:', status, error);
                $('.ajax-search-results').html('<p class="error">שגיאה בחיבור לשרת.</p>');
            },
        });
    });
});