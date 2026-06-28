<?php
/**
 * Shared UI markup helpers (accordions, icons).
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * SVG chevron matching the desktop nav .dropdown-icon (מפת הידע).
 */
function supervisor_accordion_chevron_svg() {
    return '<svg class="accordion-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
        . '<path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round"/>'
        . '</svg>';
}

/**
 * Accordion expand/collapse icon span.
 *
 * @param string $id      Suffix for id="icon-{id}".
 * @param bool   $is_open Whether the row starts expanded.
 */
function supervisor_accordion_icon_markup($id, $is_open = false) {
    $class = 'accordion-icon' . ($is_open ? ' is-open' : '');

    return sprintf(
        '<span class="%1$s" id="icon-%2$s" aria-hidden="true">%3$s</span>',
        esc_attr($class),
        esc_attr((string) $id),
        supervisor_accordion_chevron_svg()
    );
}
