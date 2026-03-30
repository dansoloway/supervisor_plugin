<?php
/**
 * AJAX: filter qa_tags (נושאי מפתח) for bibliography categories page.
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @return list<string>
 */
function supervisor_bib_cats_sidebar_area_slugs() {
    return ['policy', 'control', 'enforcement', 'knowledge_development'];
}

add_action('wp_ajax_supervisor_bib_cats_terms', 'supervisor_ajax_bib_cats_terms');
add_action('wp_ajax_nopriv_supervisor_bib_cats_terms', 'supervisor_ajax_bib_cats_terms');

function supervisor_ajax_bib_cats_terms() {
    check_ajax_referer('supervisor_bib_cats_filter', 'nonce');

    $search = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';

    $areas_raw = isset($_POST['km_areas']) ? wp_unslash($_POST['km_areas']) : [];
    if (! is_array($areas_raw)) {
        $areas_raw = [];
    }
    $allowed_area_flip = array_flip(supervisor_bib_cats_sidebar_area_slugs());
    $km_areas          = [];
    foreach ($areas_raw as $v) {
        $k = sanitize_key($v);
        if ($k !== '' && isset($allowed_area_flip[$k])) {
            $km_areas[] = $k;
        }
    }
    $km_areas = array_values(array_unique($km_areas));

    /*
     * Sidebar values are parent slugs (policy, control, …). qa_knowledge_map_category on terms
     * stores *leaf* slugs only (e.g. control_external). ?km_cat=control expands via
     * supervisor_knowledge_map_resolve_to_leaf_slugs(); do the same here so checkbox matches map links.
     */
    $km_leaf_slugs = [];
    foreach ($km_areas as $area_slug) {
        $km_leaf_slugs = array_merge($km_leaf_slugs, supervisor_knowledge_map_resolve_to_leaf_slugs($area_slug));
    }
    $km_leaf_slugs = array_values(array_unique($km_leaf_slugs));

    $valid_choices   = supervisor_knowledge_map_category_choices();
    $valid_slug_flip = array_flip(array_keys($valid_choices));

    $base_raw = isset($_POST['base_km_slugs']) ? wp_unslash($_POST['base_km_slugs']) : [];
    if (! is_array($base_raw)) {
        $base_raw = [];
    }
    $base_slugs = [];
    foreach ($base_raw as $v) {
        $k = sanitize_key($v);
        if ($k !== '' && isset($valid_slug_flip[$k])) {
            $base_slugs[] = $k;
        }
    }
    $base_slugs = array_values(array_unique($base_slugs));

    $meta_slugs = [];
    $impossible = false;
    if ($km_leaf_slugs !== [] && $base_slugs !== []) {
        $meta_slugs = array_values(array_intersect($base_slugs, $km_leaf_slugs));
        if ($meta_slugs === []) {
            $impossible = true;
        }
    } elseif ($km_leaf_slugs !== []) {
        $meta_slugs = $km_leaf_slugs;
    } elseif ($base_slugs !== []) {
        $meta_slugs = $base_slugs;
    }

    $categories = [];
    if (! $impossible) {
        $term_args = [
            'taxonomy'   => 'qa_tags',
            'hide_empty' => false,
        ];
        if ($search !== '') {
            $term_args['search'] = $search;
        }
        if ($meta_slugs !== []) {
            $term_args['meta_query'] = [
                [
                    'key'     => 'qa_knowledge_map_category',
                    'value'   => $meta_slugs,
                    'compare' => 'IN',
                ],
            ];
        }

        $categories = get_terms($term_args);
        if (is_wp_error($categories)) {
            $categories = [];
        }
    }

    $filter_narrow = $impossible || $meta_slugs !== [] || $search !== '';
    $empty_message = $filter_narrow
        ? __('לא נמצאו נושאי מפתח התואמים לחיפוש או לסינון.', 'text-domain')
        : __('No categories found.', 'text-domain');

    ob_start();
    $bib_cats_categories    = $categories;
    $bib_cats_empty_message = $empty_message;
    $partial                = dirname(__DIR__) . '/templates/partials/bib-cats-cards.php';
    if (is_readable($partial)) {
        require $partial;
    }
    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
}
