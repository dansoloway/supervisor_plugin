/**
 * Stories from the Field — horizontal carousel
 * Uses scrollBy on the overflow container. scrollIntoView is unreliable inside
 * overflow-x: auto, especially with RTL + flex.
 */
(function() {
    'use strict';

    function initStoriesCarousel() {
        var wrapper = document.querySelector('.stories-carousel-wrapper');
        if (!wrapper) {
            return;
        }

        var carousel = wrapper.querySelector('.stories-carousel');
        var prevBtn = wrapper.querySelector('.stories-carousel-prev');
        var nextBtn = wrapper.querySelector('.stories-carousel-next');

        if (!carousel || !prevBtn || !nextBtn) {
            return;
        }

        var cards = Array.prototype.slice.call(carousel.querySelectorAll('.story-card'));
        if (cards.length === 0) {
            return;
        }

        function hasOverflow() {
            return carousel.scrollWidth > carousel.clientWidth + 1;
        }

        function isRtl() {
            return getComputedStyle(carousel).direction === 'rtl';
        }

        /** ~one viewport (three cards); fallback if layout not ready */
        function scrollStep() {
            var w = carousel.clientWidth;
            if (w < 80) {
                w = 320;
            }
            return Math.max(120, Math.floor(w * 0.92));
        }

        function isCardVisible(card) {
            var cRect = carousel.getBoundingClientRect();
            var cardRect = card.getBoundingClientRect();
            var overlapStart = Math.max(cRect.left, cardRect.left);
            var overlapEnd = Math.min(cRect.right, cardRect.right);
            return overlapEnd > overlapStart + 2;
        }

        function getVisibleIndices() {
            var indices = [];
            for (var i = 0; i < cards.length; i++) {
                if (isCardVisible(cards[i])) {
                    indices.push(i);
                }
            }
            return indices;
        }

        function shouldShowArrows() {
            return cards.length >= 2 && hasOverflow();
        }

        function atStart() {
            var indices = getVisibleIndices();
            return indices.length === 0 || indices[0] <= 0;
        }

        function atEnd() {
            var indices = getVisibleIndices();
            var lastIdx = cards.length - 1;
            return indices.length === 0 || indices[indices.length - 1] >= lastIdx;
        }

        function updateArrowVisibility() {
            wrapper.classList.toggle('no-scroll-needed', !shouldShowArrows());
        }

        function updateButtonStates() {
            if (!shouldShowArrows()) {
                prevBtn.disabled = false;
                nextBtn.disabled = false;
                return;
            }
            prevBtn.disabled = atStart();
            nextBtn.disabled = atEnd();
        }

        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (prevBtn.disabled) {
                return;
            }
            var step = scrollStep();
            var rtl = isRtl();
            carousel.scrollBy({ left: rtl ? step : -step, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (nextBtn.disabled) {
                return;
            }
            var step = scrollStep();
            var rtl = isRtl();
            carousel.scrollBy({ left: rtl ? -step : step, behavior: 'smooth' });
        });

        carousel.addEventListener('scroll', function() {
            window.requestAnimationFrame(updateButtonStates);
        });
        window.addEventListener('resize', function() {
            updateArrowVisibility();
            updateButtonStates();
        });

        updateArrowVisibility();
        updateButtonStates();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStoriesCarousel);
    } else {
        initStoriesCarousel();
    }
})();
