<?php

// 1. Register Custom Post Types
function register_qa_cpts() {
    // CPT: ארגונים
    register_post_type('qa_orgs', [
        'labels' => [
            'name' => __('ארגונים', 'text-domain'),
            'singular_name' => __('ארגון', 'text-domain'),
            'add_new' => __('הוסף חדש', 'text-domain'),
            'add_new_item' => __('הוסף ארגון חדש', 'text-domain'),
            'edit_item' => __('ערוך ארגון', 'text-domain'),
            'new_item' => __('ארגון חדש', 'text-domain'),
            'view_item' => __('הצג ארגון', 'text-domain'),
            'search_items' => __('חפש ארגונים', 'text-domain'),
            'not_found' => __('לא נמצאו ארגונים', 'text-domain'),
            'not_found_in_trash' => __('לא נמצאו ארגונים באשפה', 'text-domain'),
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'qa-orgs'],
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

    // CPT: עדכונים
    register_post_type('qa_updates', [
        'labels' => [
            'name' => __('עדכונים', 'text-domain'),
            'singular_name' => __('עדכון', 'text-domain'),
            'add_new' => __('הוסף חדש', 'text-domain'),
            'add_new_item' => __('הוסף עדכון חדש', 'text-domain'),
            'edit_item' => __('ערוך עדכון', 'text-domain'),
            'new_item' => __('עדכון חדש', 'text-domain'),
            'view_item' => __('הצג עדכון', 'text-domain'),
            'search_items' => __('חפש עדכונים', 'text-domain'),
            'not_found' => __('לא נמצאו עדכונים', 'text-domain'),
            'not_found_in_trash' => __('לא נמצאו עדכונים באשפה', 'text-domain'),
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'qa-updates'],
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

    // CPT: ביבליוגרפיה
    register_post_type('qa_bib_items', [
        'labels' => [
            'name' => __('פריטים ביבליוגרפיים', 'text-domain'),
            'singular_name' => __('פריט ביבליוגרפי', 'text-domain'),
            'add_new' => __('הוסף חדש', 'text-domain'),
            'add_new_item' => __('הוסף פריט ביבליוגרפי חדש', 'text-domain'),
            'edit_item' => __('ערוך פריט ביבליוגרפי', 'text-domain'),
            'new_item' => __('פריט ביבליוגרפי חדש', 'text-domain'),
            'view_item' => __('הצג פריט ביבליוגרפי', 'text-domain'),
            'search_items' => __('חפש פריטים ביבליוגרפיים', 'text-domain'),
            'not_found' => __('לא נמצאו פריטים ביבליוגרפיים', 'text-domain'),
            'not_found_in_trash' => __('לא נמצאו פריטים ביבליוגרפיים באשפה', 'text-domain'),
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'qa-bib-items'],
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

}
add_action('init', 'register_qa_cpts');

add_filter('template_include', 'supervisor_load_templates');

function register_additional_taxonomies() {
    // Register "qa_themes"
    $themes_labels = [
        'name' => __('נושאים', 'text-domain'),
        'singular_name' => __('נושא', 'text-domain'),
        'search_items' => __('חפש נושאים', 'text-domain'),
        'all_items' => __('כל הנושאים', 'text-domain'),
        'parent_item' => __('נושא אב', 'text-domain'),
        'parent_item_colon' => __('נושא אב:', 'text-domain'),
        'edit_item' => __('ערוך נושא', 'text-domain'),
        'update_item' => __('עדכן נושא', 'text-domain'),
        'add_new_item' => __('הוסף נושא חדש', 'text-domain'),
        'new_item_name' => __('שם נושא חדש', 'text-domain'),
        'menu_name' => __('נושאים', 'text-domain'),
    ];

    register_taxonomy('qa_themes', ['qa_orgs', 'qa_updates', 'qa_bib_items'], [
        'labels' => $themes_labels,
        'hierarchical' => true, // Enables hierarchical structure (like categories)
        'public' => true, // Allows taxonomy to be publicly queryable
        'show_ui' => true, // Shows taxonomy UI in the admin
        'show_in_nav_menus' => false, // Disable in navigation menus
        'show_in_rest' => true, // Enable for block editor and REST API
        'rewrite' => ['slug' => 'qa-themes'], // Rewrite slug
    ]);

    // Register "qa_tags"
    $tags_labels = [
        'name' => __('טגים', 'text-domain'),
        'singular_name' => __('טג', 'text-domain'),
        'search_items' => __('חפש טגים', 'text-domain'),
        'all_items' => __('כל הטגים', 'text-domain'),
        'edit_item' => __('ערוך טג', 'text-domain'),
        'update_item' => __('עדכן טג', 'text-domain'),
        'add_new_item' => __('הוסף טג חדש', 'text-domain'),
        'new_item_name' => __('שם טג חדש', 'text-domain'),
        'menu_name' => __('טגים', 'text-domain'),
    ];

    register_taxonomy('qa_tags', ['qa_orgs', 'qa_updates', 'qa_bib_items'], [
        'labels' => $tags_labels,
        'hierarchical' => false, // Non-hierarchical (like tags)
        'public' => true, // Allows taxonomy to be publicly queryable
        'show_ui' => true, // Shows taxonomy UI in the admin
        'show_in_rest' => true, // Enable for block editor and REST API
        'rewrite' => ['slug' => 'qa-tags'], // Rewrite slug
    ]);
}
add_action('init', 'register_additional_taxonomies');

function register_qa_bib_cats_taxonomy() {
    register_taxonomy('qa_bib_cats', 'qa_bib_items', [
        'labels' => [
            'name' => __('קטגוריות ביבליוגרפיה', 'text-domain'),
            'singular_name' => __('קטגוריה ביבליוגרפיה', 'text-domain'),
            'search_items' => __('חפש קטגוריות ביבליוגרפיה', 'text-domain'),
            'all_items' => __('כל הקטגוריות', 'text-domain'),
            'parent_item' => __('קטגוריית אב', 'text-domain'),
            'parent_item_colon' => __('קטגוריית אב:', 'text-domain'),
            'edit_item' => __('ערוך קטגוריה', 'text-domain'),
            'update_item' => __('עדכן קטגוריה', 'text-domain'),
            'add_new_item' => __('הוסף קטגוריה חדשה', 'text-domain'),
            'new_item_name' => __('שם קטגוריה חדשה', 'text-domain'),
            'menu_name' => __('קטגוריות ביבליוגרפיה', 'text-domain'),
        ],
        'hierarchical' => false, // Allows parent-child structure
        'show_admin_column' => true, // Displays the taxonomy in the post list view
        'rewrite' => ['slug' => 'qa-bib-cats'], // Friendly URL slug
        'show_in_rest' => true, // Enables REST API and Gutenberg support
    ]);
}
add_action('init', 'register_qa_bib_cats_taxonomy');

function add_default_themes_terms() {
    $terms = ['חינוך', 'רווחה', 'בריאות']; // Replace with your terms
    foreach ($terms as $term) {
        if (!term_exists($term, 'qa_themes')) {
            wp_insert_term($term, 'qa_themes');
        }
    }
}
register_activation_hook(__FILE__, 'add_default_themes_terms');

function display_taxonomies($post_id, $taxonomy_labels) {
    foreach ($taxonomy_labels as $taxonomy_slug => $label) {
        $terms = get_the_terms($post_id, $taxonomy_slug);

        echo '<p><strong>' . esc_html($label) . ':</strong> ';
        if ($terms && !is_wp_error($terms)) {
            $term_names = array_map(function($term) {
                return $term->name;
            }, $terms);
            echo implode(', ', array_map('esc_html', $term_names));
        } else {
            echo __('ללא', 'text-domain');
        }
        echo '</p>';
    }
}
