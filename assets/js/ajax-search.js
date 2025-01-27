jQuery(document).ready(function ($) {
    $('#search-submit').on('click', function (e) {
        e.preventDefault();
        
        const searchData = {
            search_text: $('#search-text').val(),
            qa_themes: $('#qa_themes').val(),
            qa_tags: $('#qa_tags').val(),
        };

        console.log(searchData);

        $.ajax({
            url: 'https://stg-brookdalejdcorg-brookstaging.kinsta.cloud/wp-content/plugins/supervisor-plugin/ajax/search_handler.php',
            type: 'POST',
            data: searchData,
            success: function (response) {
                if (response.success) {
                    const results = response.data;
                    console.log(results);
                    let output = '';

                    if (results.length > 0) {
                        output += '<ul class="search-results-list">';
                        results.forEach((item) => {
                            output += `
                                <li class="search-result-item">
                                    <a href="${item.link}" target="_blank" class="search-result-link">
                                        ${item.title}
                                    </a>
                                </li>
                            `;
                        });
                        output += '</ul>';
                    } else {
                        output = '<p class="no-results">No results found.</p>';
                    }

                    // Display the results on the page
                    $('.ajax-search-results').html(output);
                } else {
                    console.error('Error:', response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Request failed:', status, error);
            },
        });
    });
});