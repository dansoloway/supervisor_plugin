<?php
/**
 * Supervisor page slugs → SUPERVISOR_* constants (optional config overrides).
 */

defined('ABSPATH') || exit;

/**
 * Registry: constant name => [ 'slug' => string, 'title' => string, 'template' => string ].
 * Used for slug-based IDs, `wp supervisor bootstrap-pages`, and template_include fallback by slug.
 *
 * @return array<string, array{slug: string, title: string, template: string}>
 */
function supervisor_supervisor_pages_registry() {
    return [
        'SUPERVISOR_HOME'           => [
            'slug'     => 'supervisor-home',
            'title'    => 'המפקחת - דף הבית',
            'template' => 'supervisor-home.php',
        ],
        'SUPERVISOR_BIB_CATS'      => [
            'slug'     => 'supervisor-bib-cats',
            'title'    => 'קטגוריות ביבליוגרפיה',
            'template' => 'supervisor-bib_cats.php',
        ],
        'SUPERVISOR_UPDATES'       => [
            'slug'     => 'supervisor-updates',
            'title'    => 'עדכונים',
            'template' => 'supervisor-updates.php',
        ],
        'SUPERVISOR_ORGS'          => [
            'slug'     => 'supervisor-orgs',
            'title'    => 'ארגונים',
            'template' => 'supervisor-qa_orgs.php',
        ],
        'SUPERVISOR_ABOUT'         => [
            'slug'     => 'supervisor-about',
            'title'    => 'אודות',
            'template' => 'supervisor-about.php',
        ],
        'SUPERVISOR_CONTACT'       => [
            'slug'     => 'supervisor-contact',
            'title'    => 'יצירת קשר',
            'template' => 'supervisor-contact.php',
        ],
        'SUPERVISOR_INTRO_TEXT'    => [
            'slug'     => 'supervisor-intro',
            'title'    => 'טקסט פתיחה',
            'template' => 'supervisor-content.php',
        ],
        'SUPERVISOR_ACTIVITIES'    => [
            'slug'     => 'supervisor-activities',
            'title'    => 'תחומי פעילות',
            'template' => 'supervisor-activities.php',
        ],
        'SUPERVISOR_KNOWLEDGE_MAP' => [
            'slug'     => 'supervisor-knowledge-map',
            'title'    => 'מפת הידע',
            'template' => 'supervisor-knowledge-map.php',
        ],
    ];
}

/**
 * Map post ID => plugin template basename (skips unresolved ID 0).
 *
 * @return array<int, string>
 */
function supervisor_page_templates_map_by_post_id() {
    $out = [];
    foreach (supervisor_supervisor_pages_registry() as $constant => $row) {
        if (! defined($constant)) {
            continue;
        }
        $id = (int) constant($constant);
        if ($id > 0) {
            $out[ $id ] = $row['template'];
        }
    }

    return $out;
}

/**
 * Map page slug => plugin template basename.
 *
 * @return array<string, string>
 */
function supervisor_page_templates_map_by_slug() {
    $out = [];
    foreach (supervisor_supervisor_pages_registry() as $row) {
        $out[ $row['slug'] ] = $row['template'];
    }

    return $out;
}

/**
 * Resolve plugin template file for a page ID (by ID constants, then by slug in registry).
 *
 * @return string|null Basename under templates/ or null
 */
function supervisor_resolve_page_template_basename($page_id) {
    $page_id = (int) $page_id;
    if ($page_id <= 0) {
        return null;
    }

    $by_id = supervisor_page_templates_map_by_post_id();
    if (isset($by_id[ $page_id ])) {
        return $by_id[ $page_id ];
    }

    $slug = get_post_field('post_name', $page_id);
    if (! is_string($slug) || $slug === '') {
        return null;
    }

    $by_slug = supervisor_page_templates_map_by_slug();

    return isset($by_slug[ $slug ]) ? $by_slug[ $slug ] : null;
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
