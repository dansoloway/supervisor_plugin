jQuery(document).ready(function ($) {
    $('#search-submit').on('click', function (e) {
        e.preventDefault();
        
        // Properly collect checkbox values
        const selectedThemes = [];
        $('input[name="qa_themes[]"]:checked').each(function() {
            selectedThemes.push($(this).val());
        });
        
        const selectedTags = [];
        $('input[name="qa_tags[]"]:checked').each(function() {
            selectedTags.push($(this).val());
        });
        
        const searchData = {
            search_text: $('#search-text').val(),
            qa_themes: selectedThemes,
            qa_tags: selectedTags,
        };

        console.log('Search data:', searchData);

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