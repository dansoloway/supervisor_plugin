(function () {
    'use strict';

    var cards = document.querySelectorAll('.supervisor-qa_orgs .org-card');
    if (!cards.length) {
        return;
    }

    var viewportPadding = 8;
    var gap = 12;

    function positionTooltip(card, tip) {
        tip.hidden = false;
        tip.style.left = '-9999px';
        tip.style.top = '0';
        tip.classList.add('is-visible');

        var cardRect = card.getBoundingClientRect();
        var tipRect = tip.getBoundingClientRect();
        var left = cardRect.left - tipRect.width - gap;
        var top = cardRect.top + (cardRect.height - tipRect.height) / 2;

        if (left < viewportPadding) {
            left = cardRect.right + gap;
        }

        if (left + tipRect.width > window.innerWidth - viewportPadding) {
            left = Math.max(
                viewportPadding,
                Math.min(left, window.innerWidth - tipRect.width - viewportPadding)
            );
        }

        top = Math.max(
            viewportPadding,
            Math.min(top, window.innerHeight - tipRect.height - viewportPadding)
        );

        tip.style.left = left + 'px';
        tip.style.top = top + 'px';
    }

    function hideTooltip(tip) {
        tip.classList.remove('is-visible');
        tip.hidden = true;
        tip.style.left = '';
        tip.style.top = '';
    }

    cards.forEach(function (card) {
        var tip = card.querySelector('.org-card__tooltip');
        if (!tip) {
            return;
        }

        card.addEventListener('mouseenter', function () {
            positionTooltip(card, tip);
        });

        card.addEventListener('focus', function () {
            positionTooltip(card, tip);
        });

        card.addEventListener('mouseleave', function () {
            hideTooltip(tip);
        });

        card.addEventListener('blur', function () {
            hideTooltip(tip);
        });
    });

    window.addEventListener('scroll', function () {
        document.querySelectorAll('.supervisor-qa_orgs .org-card__tooltip.is-visible').forEach(function (tip) {
            hideTooltip(tip);
        });
    }, { passive: true });

    window.addEventListener('resize', function () {
        document.querySelectorAll('.supervisor-qa_orgs .org-card__tooltip.is-visible').forEach(function (tip) {
            hideTooltip(tip);
        });
    }, { passive: true });
})();
