jQuery(function ($) {
    if (typeof bibCatsFilter === 'undefined') {
        return;
    }

    var $inner = $('#bib-cats-grid-inner');
    if (!$inner.length) {
        return;
    }

    function collectKmAreas() {
        var slugs = [];
        $('input[name="bib_km_area[]"]:checked').each(function () {
            var v = ($(this).val() || '').trim();
            if (v) {
                slugs.push(v);
            }
        });
        return slugs;
    }

    function showLoading() {
        $inner.css('opacity', '0.55').attr('aria-busy', 'true');
    }

    function hideLoading() {
        $inner.css('opacity', '').removeAttr('aria-busy');
    }

    function runFilter() {
        var search = ($('#bib-cats-search-text').val() || '').trim();
        var kmAreas = collectKmAreas();
        var baseSlugs = bibCatsFilter.baseKmSlugs || [];

        showLoading();

        $.ajax({
            url: bibCatsFilter.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: bibCatsFilter.action,
                nonce: bibCatsFilter.nonce,
                search: search,
                km_areas: kmAreas,
                base_km_slugs: baseSlugs,
            },
        })
            .done(function (res) {
                if (res && res.success && res.data && typeof res.data.html === 'string') {
                    $inner.html(res.data.html);
                } else {
                    $inner.html(
                        '<p class="no-categories">' +
                            (bibCatsFilter.strings && bibCatsFilter.strings.error ? bibCatsFilter.strings.error : '') +
                            '</p>'
                    );
                }
            })
            .fail(function () {
                $inner.html(
                    '<p class="no-categories">' +
                        (bibCatsFilter.strings && bibCatsFilter.strings.error ? bibCatsFilter.strings.error : '') +
                        '</p>'
                );
            })
            .always(function () {
                hideLoading();
            });
    }

    $('#bib-cats-filter-form').on('submit', function (e) {
        e.preventDefault();
        runFilter();
    });

    $('#bib-cats-search-button').on('click', function (e) {
        e.preventDefault();
        runFilter();
    });

    $('#bib-cats-search-text').on('keydown', function (e) {
        if (e.key === 'Enter' || e.which === 13) {
            e.preventDefault();
            runFilter();
        }
    });
});
