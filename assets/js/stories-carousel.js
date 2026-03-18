/**
 * Stories from the Field - Carousel arrow navigation
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

        var scrollAmount = 300;
        var isRTL = getComputedStyle(carousel).direction === 'rtl';

        function hasOverflow() {
            return carousel.scrollWidth > carousel.clientWidth;
        }

        function updateCanScroll() {
            wrapper.classList.toggle('can-scroll', hasOverflow());
        }

        function updateButtonStates() {
            if (!hasOverflow()) return;

            var maxScroll = carousel.scrollWidth - carousel.clientWidth;
            var atStart, atEnd;

            if (isRTL) {
                atStart = carousel.scrollLeft >= 0;
                atEnd = carousel.scrollLeft <= -maxScroll;
            } else {
                atStart = carousel.scrollLeft <= 0;
                atEnd = carousel.scrollLeft >= maxScroll - 1;
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
        window.addEventListener('resize', function() {
            updateCanScroll();
            updateButtonStates();
        });

        updateCanScroll();
        updateButtonStates();

        setTimeout(updateCanScroll, 100);
        window.addEventListener('load', updateCanScroll);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStoriesCarousel);
    } else {
        initStoriesCarousel();
    }
})();
