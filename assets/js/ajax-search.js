jQuery(document).ready(function ($) {
    if (typeof $ === 'undefined') {
        console.error('jQuery is not available');
        return;
    }

    let searchRequestId = 0;
    let activeSearchXhr = null;

    function debounce(func, wait) {
        let timeout;
        const executedFunction = function (...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
        executedFunction.cancel = function () {
            clearTimeout(timeout);
        };
        return executedFunction;
    }

    function resetToInitialList() {
        searchRequestId++;
        if (activeSearchXhr) {
            activeSearchXhr.abort();
            activeSearchXhr = null;
        }
        hideLoading();
        $('.search-results-container').empty().hide();
        $('.initial-content').show();
    }

    function showLoading() {
        const loadingHtml = `
            <div class="search-loading">
                <div class="loading-spinner"></div>
                <p class="loading-text">מחפש...</p>
            </div>
        `;
        $('.initial-content').hide();
        $('.search-results-container').html(loadingHtml).show();
    }

    function hideLoading() {
        $('.search-loading').remove();
    }

    function performSearch() {
        try {
            const selectedThemes = [];
            $('input[name="qa_themes[]"]:checked').each(function () {
                const value = $(this).val();
                if (value && value.trim() !== '') {
                    selectedThemes.push(value.trim());
                }
            });

            const selectedTags = [];
            $('input[name="qa_tags[]"]:checked').each(function () {
                const value = $(this).val();
                if (value && value.trim() !== '') {
                    selectedTags.push(value.trim());
                }
            });

            const searchText = $('#search-text').val().trim();

            if (!searchText && selectedThemes.length === 0 && selectedTags.length === 0) {
                resetToInitialList();
                return;
            }

            const reqId = ++searchRequestId;

            if (activeSearchXhr) {
                activeSearchXhr.abort();
                activeSearchXhr = null;
            }

            showLoading();

            const searchData = {
                search_text: searchText,
                qa_themes: selectedThemes,
                qa_tags: selectedTags,
                post_types: ['qa_updates'],
            };

            const ajaxUrl = window.location.origin + '/wp-content/plugins/supervisor-plugin/ajax/search_handler.php';

            const xhr = $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: searchData,
                dataType: 'json',
                success: function (response) {
                    if (reqId !== searchRequestId) {
                        return;
                    }
                    hideLoading();

                    if (response && response.success) {
                        const results = response.data || [];
                        let output = '';

                        if (results.length > 0) {
                            results.forEach((item, index) => {
                                if (item && item.title && item.link) {
                                    const accordionId = 'search-result-' + index;
                                    output += `
                                        <div class="qa-update-item content-card">
                                            <div class="light-green-bkg accordion-header" data-accordion="${accordionId}">
                                                <div class="qa-update-title">
                                                    <div class="title-date-container">
                                                        <h3>${item.title}</h3>
                                                        <span class="update-date">${item.date || ''}</span>
                                                    </div>
                                                    <span class="accordion-icon" id="icon-${accordionId}">⌄</span>
                                                </div>
                                            </div>
                                            <div class="accordion-content" id="accordion-${accordionId}">
                                                <div class="update-content-text"><p>${item.content || ''}</p></div>
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
                        } else {
                            output = '<p class="no-results">לא נמצאו תוצאות.</p>';
                        }

                        $('.initial-content').hide();
                        $('.search-results-container').html(output).show();
                        initializeSearchAccordions();
                    } else {
                        console.error('Search failed:', response);
                        $('.initial-content').hide();
                        $('.search-results-container').html('<p class="no-results">שגיאה בחיפוש. אנא נסה שוב.</p>').show();
                    }
                },
                error: function (xhr, status) {
                    if (status === 'abort') {
                        return;
                    }
                    if (reqId !== searchRequestId) {
                        return;
                    }
                    console.error('AJAX error:', status, xhr.responseText);
                    hideLoading();
                    $('.initial-content').hide();
                    $('.search-results-container').html('<p class="no-results">שגיאה בחיפוש. אנא נסה שוב.</p>').show();
                },
            });

            activeSearchXhr = xhr;
            xhr.always(function () {
                if (activeSearchXhr === xhr) {
                    activeSearchXhr = null;
                }
            });
        } catch (error) {
            console.error('Error in search function:', error);
            hideLoading();
            $('.initial-content').hide();
            $('.search-results-container').html('<p class="no-results">שגיאה בחיפוש. אנא נסה שוב.</p>').show();
        }
    }

    const debouncedSearch = debounce(performSearch, 300);

    $('#search-submit').on('click', function (e) {
        e.preventDefault();
        performSearch();
    });

    $('.ajax-search-component .search-button').on('click', function (e) {
        e.preventDefault();
        performSearch();
    });

    $('#search-text').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            performSearch();
        }
    });

    function syncSearchStateFromInputs() {
        const hasCheckedBoxes = $('input[name="qa_themes[]"]:checked, input[name="qa_tags[]"]:checked').length > 0;
        const hasSearchText = $('#search-text').val().trim() !== '';

        if (hasCheckedBoxes || hasSearchText) {
            debouncedSearch();
        } else {
            debouncedSearch.cancel();
            resetToInitialList();
        }
    }

    $('input[name="qa_themes[]"], input[name="qa_tags[]"]').on('change', function () {
        syncSearchStateFromInputs();
    });

    $('#search-text').on('input', function () {
        syncSearchStateFromInputs();
    });

    function initializeSearchAccordions() {
        const searchResultsContainer = document.querySelector('.search-results-container');
        if (!searchResultsContainer) return;

        const searchAccordions = searchResultsContainer.querySelectorAll('.accordion-header');

        searchAccordions.forEach((header) => {
            const newHeader = header.cloneNode(true);
            header.parentNode.replaceChild(newHeader, header);

            newHeader.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const accordionId = this.getAttribute('data-accordion');
                if (!accordionId) return;

                const content = document.getElementById('accordion-' + accordionId);
                const icon = document.getElementById('icon-' + accordionId);

                if (!content || !icon) return;

                const allAccordions = document.querySelectorAll('.accordion-header');
                allAccordions.forEach((otherHeader) => {
                    if (otherHeader !== this) {
                        const otherAccordionId = otherHeader.getAttribute('data-accordion');
                        if (otherAccordionId) {
                            const otherContent = document.getElementById('accordion-' + otherAccordionId);
                            const otherIcon = document.getElementById('icon-' + otherAccordionId);

                            if (otherContent && otherIcon) {
                                otherContent.classList.remove('is-open');
                                otherIcon.textContent = '⌄';
                            }
                        }
                    }
                });

                if (content.classList.contains('is-open')) {
                    content.classList.remove('is-open');
                    icon.textContent = '⌄';
                } else {
                    content.classList.add('is-open');
                    icon.textContent = '⌃';
                }
            });
        });
    }

    $('.filter-toggle').on('click', function () {
        const $toggle = $(this);
        const $content = $('.filter-content');
        const isExpanded = $toggle.attr('aria-expanded') === 'true';

        if (isExpanded) {
            $content.slideUp(300);
            $toggle.attr('aria-expanded', 'false');
        } else {
            $content.slideDown(300);
            $toggle.attr('aria-expanded', 'true');
        }
    });
});
