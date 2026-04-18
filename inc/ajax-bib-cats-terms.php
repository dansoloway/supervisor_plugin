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
    return ['policy', 'control', 'enforcement', 'knowledge_development', 'working_methods'];
}

/**
 * Sidebar parent checkboxes to pre-check for the current ?km_cat= deep link (leaf slugs must map onto allowed parents).
 *
 * @param string        $km_cat_raw   Raw query value (parent or leaf).
 * @param list<string>  $filter_slugs Resolved leaf slugs from $km_cat_raw.
 *
 * @return list<string> Parent slugs from supervisor_bib_cats_sidebar_area_slugs() only.
 */
function supervisor_bib_cats_sidebar_checked_areas_from_km($km_cat_raw, $filter_slugs) {
    $km_cat_raw = sanitize_key((string) $km_cat_raw);
    $allowed    = supervisor_bib_cats_sidebar_area_slugs();
    $allowed_f  = array_flip($allowed);

    if ($km_cat_raw !== '' && supervisor_knowledge_map_is_parent_slug($km_cat_raw) && isset($allowed_f[ $km_cat_raw ])) {
        return [$km_cat_raw];
    }

    if ($filter_slugs === []) {
        return [];
    }

    $leaf_flip = array_flip($filter_slugs);
    $found     = [];
    foreach (supervisor_knowledge_map_hierarchy() as $group) {
        $parent = $group['slug'];
        if (! isset($allowed_f[ $parent ])) {
            continue;
        }
        foreach ($group['items'] as $item) {
            if (isset($leaf_flip[ $item['slug'] ])) {
                $found[ $parent ] = true;
                break;
            }
        }
    }

    return array_keys($found);
}

/**
 * Term query args for נושאי מפתח — keep in sync with supervisor_ajax_bib_cats_terms() (same meta_query shape as map filtering).
 *
 * @param string       $search
 * @param list<string> $meta_slugs Leaf slugs for qa_knowledge_map_category; empty means no meta filter.
 *
 * @return array<string, mixed>
 */
function supervisor_bib_cats_term_query_args($search, $meta_slugs) {
    $term_args = [
        'taxonomy'   => 'qa_tags',
        'hide_empty' => false,
    ];
    if ($search !== '') {
        $term_args['search'] = $search;
    }
    $meta_slugs = array_values(array_filter(array_map('sanitize_key', (array) $meta_slugs)));
    if ($meta_slugs !== []) {
        $term_args['meta_query'] = [
            [
                'key'     => 'qa_knowledge_map_category',
                'value'   => $meta_slugs,
                'compare' => 'IN',
            ],
        ];
    }

    return $term_args;
}

/**
 * @param string       $search
 * @param list<string> $meta_slugs
 *
 * @return array<int, WP_Term>
 */
function supervisor_bib_cats_get_qa_tags_terms($search, $meta_slugs) {
    $categories = get_terms(supervisor_bib_cats_term_query_args($search, $meta_slugs));
    if (is_wp_error($categories)) {
        return [];
    }

    return $categories;
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
        $categories = supervisor_bib_cats_get_qa_tags_terms($search, $meta_slugs);
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
