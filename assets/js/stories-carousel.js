/**
 * Stories from the Field - Slider (carousel) arrow navigation
 * Shows arrows when there are more items than can fit; uses item count for visibility.
 */
(function() {
    'use strict';

    function initStoriesCarousel() {
        var wrapper = document.querySelector('.stories-carousel-wrapper');
        if (!wrapper) return;

        var carousel = wrapper.querySelector('.stories-carousel');
        var prevBtn = wrapper.querySelector('.stories-carousel-prev');
        var nextBtn = wrapper.querySelector('.stories-carousel-next');

        if (!carousel || !prevBtn || !nextBtn) return;

        var scrollAmount = 304; /* one card (280px) + gap (24px) */
        var isRTL = getComputedStyle(carousel).direction === 'rtl';
        var cards = carousel.querySelectorAll('.story-card');

        function shouldShowArrows() {
            return cards.length > 1;
        }

        function updateArrowVisibility() {
            wrapper.classList.toggle('no-scroll-needed', !shouldShowArrows());
        }

        function updateButtonStates() {
            if (!shouldShowArrows()) return;

            var maxScroll = carousel.scrollWidth - carousel.clientWidth;
            var atStart, atEnd;

            if (isRTL) {
                atStart = carousel.scrollLeft >= 0;
                atEnd = maxScroll <= 0 || carousel.scrollLeft <= -maxScroll;
            } else {
                atStart = carousel.scrollLeft <= 0;
                atEnd = maxScroll <= 0 || carousel.scrollLeft >= maxScroll - 1;
            }

            prevBtn.disabled = atStart;
            nextBtn.disabled = atEnd;
        }

        prevBtn.addEventListener('click', function() {
            if (prevBtn.disabled) return;
            carousel.scrollBy({ left: isRTL ? scrollAmount : -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function() {
            if (nextBtn.disabled) return;
            carousel.scrollBy({ left: isRTL ? -scrollAmount : scrollAmount, behavior: 'smooth' });
        });

        carousel.addEventListener('scroll', updateButtonStates);
        window.addEventListener('resize', updateButtonStates);

        updateArrowVisibility();
        updateButtonStates();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStoriesCarousel);
    } else {
        initStoriesCarousel();
    }
})();
