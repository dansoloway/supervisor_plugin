<?php
/**
 * Supervisor page slugs → SUPERVISOR_* constants (optional config overrides).
 */

defined('ABSPATH') || exit;

/**
 * Registry: constant name => [ 'slug' => string, 'title' => string ].
 * Used for slug-based IDs and `wp supervisor bootstrap-pages`.
 *
 * @return array<string, array{slug: string, title: string}>
 */
function supervisor_supervisor_pages_registry() {
    return [
        'SUPERVISOR_HOME'           => [
            'slug'  => 'supervisor-home',
            'title' => 'המפקחת - דף הבית',
        ],
        'SUPERVISOR_BIB_CATS'      => [
            'slug'  => 'supervisor-bib-cats',
            'title' => 'קטגוריות ביבליוגרפיה',
        ],
        'SUPERVISOR_UPDATES'       => [
            'slug'  => 'supervisor-updates',
            'title' => 'עדכונים',
        ],
        'SUPERVISOR_ORGS'          => [
            'slug'  => 'supervisor-orgs',
            'title' => 'ארגונים',
        ],
        'SUPERVISOR_ABOUT'         => [
            'slug'  => 'supervisor-about',
            'title' => 'אודות',
        ],
        'SUPERVISOR_CONTACT'       => [
            'slug'  => 'supervisor-contact',
            'title' => 'יצירת קשר',
        ],
        'SUPERVISOR_INTRO_TEXT'    => [
            'slug'  => 'supervisor-intro',
            'title' => 'טקסט פתיחה',
        ],
        'SUPERVISOR_ACTIVITIES'    => [
            'slug'  => 'supervisor-activities',
            'title' => 'תחומי פעילות',
        ],
        'SUPERVISOR_KNOWLEDGE_MAP' => [
            'slug'  => 'supervisor-knowledge-map',
            'title' => 'מפת הידע',
        ],
    ];
}

/**
 * @return array<string, string> Constant name => slug
 */
function supervisor_page_id_slug_map() {
    $map = [];
    foreach (supervisor_supervisor_pages_registry() as $constant => $row) {
        $map[ $constant ] = $row['slug'];
    }

    return $map;
}

/**
 * Define SUPERVISOR_* from config.php if present; otherwise resolve published page by slug.
 * Missing pages become ID 0 (callers treat as unset).
 */
function supervisor_define_page_id_constants() {
    foreach (supervisor_page_id_slug_map() as $constant => $slug) {
        if (defined($constant)) {
            continue;
        }

        $page = get_page_by_path($slug);
        if ($page instanceof WP_Post && $page->post_status !== 'trash') {
            define($constant, (int) $page->ID);
        } else {
            define($constant, 0);
        }
    }
}

add_action('plugins_loaded', 'supervisor_define_page_id_constants', 0);
