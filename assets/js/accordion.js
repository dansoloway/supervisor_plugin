/**
 * Accordion headers — chevron rotates like nav dropdown (is-open on .accordion-icon).
 */
(function () {
    'use strict';

    var CHEVRON_SVG =
        '<svg class="accordion-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
        + '<path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round"/>'
        + '</svg>';

    function accordionScope(header) {
        return (
            header.closest('.about-content')
            || header.closest('.qa-updates-list')
            || header.closest('.search-results-list')
            || header.closest('.search-results-container')
            || header.closest('.supervisor-content-wrapper')
            || document
        );
    }

    function setIconOpen(header, open) {
        var id = header.getAttribute('data-accordion');
        if (!id) {
            return;
        }
        var icon = document.getElementById('icon-' + id);
        if (icon) {
            icon.classList.toggle('is-open', open);
        }
    }

    function closeOthersInScope(scope, currentHeader) {
        scope.querySelectorAll('.accordion-header').forEach(function (header) {
            if (header === currentHeader) {
                return;
            }
            var id = header.getAttribute('data-accordion');
            if (!id) {
                return;
            }
            var content = document.getElementById('accordion-' + id);
            if (content) {
                content.classList.remove('is-open');
            }
            setIconOpen(header, false);
        });
    }

    function syncInitialIcons(root) {
        root.querySelectorAll('.accordion-header').forEach(function (header) {
            var id = header.getAttribute('data-accordion');
            if (!id) {
                return;
            }
            var content = document.getElementById('accordion-' + id);
            if (content && content.classList.contains('is-open')) {
                setIconOpen(header, true);
            }
        });
    }

    function bindAccordionHeader(header) {
        if (header.dataset.supervisorAccordionBound === '1') {
            return;
        }
        header.dataset.supervisorAccordionBound = '1';

        header.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var accordionId = this.getAttribute('data-accordion');
            if (!accordionId) {
                return;
            }

            var content = document.getElementById('accordion-' + accordionId);
            var icon = document.getElementById('icon-' + accordionId);
            if (!content || !icon) {
                return;
            }

            closeOthersInScope(accordionScope(this), this);

            var willOpen = !content.classList.contains('is-open');
            content.classList.toggle('is-open', willOpen);
            icon.classList.toggle('is-open', willOpen);
        });
    }

    function initAccordions(root) {
        root = root || document;
        root.querySelectorAll('.accordion-header').forEach(bindAccordionHeader);
        syncInitialIcons(root);
    }

    window.supervisorAccordionChevronSvg = CHEVRON_SVG;
    window.supervisorInitAccordions = initAccordions;

    document.addEventListener('DOMContentLoaded', function () {
        initAccordions();
    });
})();
