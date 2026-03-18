/**
 * Stories from the Field - RTL-safe carousel
 * Uses visible-card detection and scrollIntoView. No scrollLeft math.
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

        var cards = Array.prototype.slice.call(carousel.querySelectorAll('.story-card'));

        function hasOverflow() {
            return carousel.scrollWidth > carousel.clientWidth;
        }

        function isCardVisible(card) {
            var cRect = carousel.getBoundingClientRect();
            var cardRect = card.getBoundingClientRect();
            var overlapStart = Math.max(cRect.left, cardRect.left);
            var overlapEnd = Math.min(cRect.right, cardRect.right);
            return overlapEnd > overlapStart;
        }

        function getVisibleIndices() {
            var indices = [];
            for (var i = 0; i < cards.length; i++) {
                if (isCardVisible(cards[i])) indices.push(i);
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
            if (!shouldShowArrows()) return;

            prevBtn.disabled = atStart();
            nextBtn.disabled = atEnd();
        }

        function scrollToCard(index, alignStart) {
            if (index < 0 || index >= cards.length) return;

            var el = cards[index];
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: alignStart ? 'start' : 'end'
            });
        }

        prevBtn.addEventListener('click', function() {
            if (prevBtn.disabled) return;

            var indices = getVisibleIndices();
            var targetIdx = indices.length > 0 ? indices[0] - 1 : 0;
            if (targetIdx < 0) targetIdx = 0;

            scrollToCard(targetIdx, true);
        });

        nextBtn.addEventListener('click', function() {
            if (nextBtn.disabled) return;

            var indices = getVisibleIndices();
            var lastIdx = cards.length - 1;
            var targetIdx = indices.length > 0 ? indices[indices.length - 1] + 1 : 0;
            if (targetIdx > lastIdx) targetIdx = lastIdx;

            scrollToCard(targetIdx, false);
        });

        carousel.addEventListener('scroll', updateButtonStates);
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
