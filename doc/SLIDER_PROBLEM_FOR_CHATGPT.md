# Stories Slider / Carousel – Problem Description for ChatGPT

## Context

- **Page**: Hebrew site, RTL (right-to-left) layout. The parent container `.supervisor-home` has `direction: rtl`.
- **Section**: "סיפורים מהשטח" (Stories from the Field) – a horizontal slider of story cards.
- **Current behavior**: Slider is not behaving correctly. Arrows sometimes don’t appear when they should (e.g. with 4 items). Scroll/slide and RTL handling may be wrong.

## Expected Behavior

1. **Arrows**: Shown when there are 2+ story cards; hidden when there is 0 or 1.
2. **Prev/next**:
   - Clicking **Next** should show the next card(s) (in RTL, that’s scrolling toward the logical “end” / visual left).
   - Clicking **Prev** should show the previous card(s).
3. **At ends**: Prev disabled at start, Next disabled at end.
4. **Layout**: Arrows on the sides. On the visual right in RTL, users should see the arrow that advances to the next items.

## HTML (simplified structure)

The slider lives inside a full-width section. Each `.story-card` is a link with image, title, and excerpt.

```html
<div class="supervisor-home" style="direction: rtl;">
  <!-- ... -->
  <section class="stories-from-field-section">
    <div class="stories-section-header">
      <h2 class="stories-section-title-with-lines">סיפורים מהשטח</h2>
    </div>
    <div class="stories-carousel-wrapper">
      <button type="button" class="stories-carousel-prev" aria-label="הקודם">
        <svg><!-- left chevron --></svg>
      </button>
      <div class="stories-carousel" role="region">
        <a href="..." class="story-card">
          <div class="story-card-image">...</div>
          <h3 class="story-card-title">...</h3>
          <p class="story-card-excerpt">...</p>
        </a>
        <!-- more .story-card items... -->
      </div>
      <button type="button" class="stories-carousel-next" aria-label="הבא">
        <svg><!-- right chevron --></svg>
      </button>
    </div>
  </section>
</div>
```

## CSS

```css
.stories-carousel-wrapper {
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
}

.stories-carousel-prev,
.stories-carousel-next {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid var(--sv-border, #E5EAF0);
    background: var(--sv-card, #fff);
    color: var(--sv-text, #1f2937);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.stories-carousel-wrapper.no-scroll-needed .stories-carousel-prev,
.stories-carousel-wrapper.no-scroll-needed .stories-carousel-next {
    display: none;
}

.stories-carousel-prev:disabled,
.stories-carousel-next:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.stories-carousel {
    flex: 1;
    min-width: 0;
    display: flex;
    gap: 24px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    scrollbar-width: thin;
}

.story-card {
    flex: 0 0 280px;
    scroll-snap-align: start;
    /* ... card content styles ... */
}
```

## JavaScript

```javascript
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
```

## Problems

1. **Arrows sometimes invisible**: With 4 cards, the right-hand arrow (which should advance to the next items) may not appear. Logic uses `cards.length > 1` and `no-scroll-needed`; something may be wrong with element selection, timing, or parent context.

2. **RTL scroll behavior**:
   - Page and carousel are RTL.
   - `scrollLeft` behavior differs in RTL (e.g. 0 at right edge, negative when scrolled).
   - Prev/next directions and `atStart` / `atEnd` may be inverted or incorrect for RTL.

3. **Layout / overflow**:
   - Carousel uses `flex: 1` and `min-width: 0`; in some viewports `scrollWidth` might equal `clientWidth`, so overflow detection fails.
   - Possibly need a more robust way to decide when to show arrows (e.g. based on item count vs. visible width).

## Task

Fix the slider so that:

1. Arrows appear when there are 2 or more story cards.
2. Prev/next click correctly in RTL (Prev = back toward start, Next = forward toward end).
3. Prev is disabled at start, Next at end.
4. The arrow on the visual right advances to the next items and behaves as expected.

Please provide corrected HTML (if needed), CSS (if needed), and especially the JavaScript, handling RTL scrolling correctly.
