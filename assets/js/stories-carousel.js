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

        var scrollAmount = 300; // pixels to scroll per click

        function updateButtonStates() {
            prevBtn.disabled = carousel.scrollLeft <= 0;
            nextBtn.disabled = carousel.scrollLeft >= carousel.scrollWidth - carousel.clientWidth - 1;
        }

        prevBtn.addEventListener('click', function() {
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function() {
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        carousel.addEventListener('scroll', updateButtonStates);
        window.addEventListener('resize', updateButtonStates);
        updateButtonStates();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStoriesCarousel);
    } else {
        initStoriesCarousel();
    }
})();
