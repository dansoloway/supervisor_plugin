<?php
/**
 * Plugin Name: Supervisor Plugin
 * Description: A plugin to manage custom post types for ארגונים, עדכונים, and ביבליוגרפיה.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly
require_once plugin_dir_path(__FILE__) . 'config.php';

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

// 2. Flush Rewrite Rules on Activation/Deactivation
function supervisor_rewrite_flush() {
    register_qa_cpts();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'supervisor_rewrite_flush');
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');

// 3. Load Templates for Custom Post Types
function supervisor_load_templates($template) {
    global $post;

    if (is_singular('qa_orgs') && file_exists(plugin_dir_path(__FILE__) . 'templates/single-qa_orgs.php')) {
        return plugin_dir_path(__FILE__) . 'templates/single-qa_orgs.php';
    }

    if (is_singular('qa_updates') && file_exists(plugin_dir_path(__FILE__) . 'templates/single-qa_updates.php')) {
        return plugin_dir_path(__FILE__) . 'templates/single-qa_updates.php';
    }

    if (is_singular('qa_bibs') && file_exists(plugin_dir_path(__FILE__) . 'templates/single-qa_bibs.php')) {
        return plugin_dir_path(__FILE__) . 'templates/single-qa_bibs.php';
    }

 

    return $template;
}
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

function enqueue_alternate_header_assets() {
    // Define templates and post type archives requiring alternate assets
    $templates = ['supervisor-home.php', 'supervisor-content.php', 'supervisor-bib_cats.php'];
    $singles = ['qa_bibs', 'qa_orgs', 'qa_updates']; // Post type slugs
    $post_type_archives = ['qa_updates'];

    // Check if the current request matches the conditions
    $is_template = is_page_template($templates);
    $is_archive = is_post_type_archive($post_type_archives);
    $is_single = is_singular($singles);

    if ($is_template || $is_archive || $is_single) {
        // Enqueue the alternate header styles
        wp_enqueue_style(
            'alternate-header-styles',
            plugins_url('supervisor-plugin/assets/css/supervisor-styles.css', dirname(__FILE__)),
            [],
            '1.0'
        );

        // Enqueue the alternate header scripts
        wp_enqueue_script(
            'alternate-header-scripts',
            plugins_url('supervisor-plugin/assets/js/supervisor-scripts.js', dirname(__FILE__)),
            ['jquery'], // Dependencies
            '1.0', // Version
            true // Load in the footer
        );

        // Enqueue the AJAX search script
        wp_enqueue_script(
            'ajax-search-script',
            plugins_url('assets/js/ajax-search.js', __FILE__), // Dynamically generate the JS URL
            ['jquery'], // Dependencies
            '1.0', // Version
            true // Load in the footer
        );  
    
       
    }
}
add_action('wp_enqueue_scripts', 'enqueue_alternate_header_assets');

// Register the custom page templates
function supervisor_add_templates($templates) {
    $templates['supervisor-home.php'] = 'Supervisor Home';
    $templates['supervisor-content.php'] = 'Supervisor Content';
    return $templates;
}
add_filter('theme_page_templates', 'supervisor_add_templates');

// // Add the custom Supervisor Content
// function supervisor_add_template($templates) {
    
//     return $templates;
// }
// add_filter('theme_page_templates', 'supervisor_add_template');

   // intro text
//    if (is_page(SUPERVISOR_INTRO_TEXT) && file_exists(plugin_dir_path(__FILE__) . 'templates/page-qa_content.php')) {
//     return plugin_dir_path(__FILE__) . 'templates/page-qa_content.php';


function supervisor_load_template($template) {
    // Check for Supervisor Home page template
    if (get_page_template_slug() === 'supervisor-home.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/supervisor-home.php';
    }

    // Check for qa_updates archive
    if (is_post_type_archive('qa_updates')) {
        $archive_template = plugin_dir_path(__FILE__) . 'templates/archive-qa_updates.php';
        if (file_exists($archive_template)) {
            $template = $archive_template;
        }
    }

    return $template;
}
add_filter('template_include', 'supervisor_load_template');

add_action('init', 'register_custom_ajax_endpoint');
function register_custom_ajax_endpoint() {
    add_rewrite_rule('^ajax-handler/?$', 'index.php?custom_ajax=1', 'top');
    flush_rewrite_rules(false); // Use this only once to regenerate rewrite rules
}

add_action('query_vars', 'add_custom_ajax_query_var');
function add_custom_ajax_query_var($query_vars) {
    $query_vars[] = 'custom_ajax';
    return $query_vars;
}

add_action('template_redirect', 'handle_custom_ajax_requests');
function handle_custom_ajax_requests() {
    if (get_query_var('custom_ajax') == 1) {
        // Handle your AJAX logic here
        header('Content-Type: application/json');

        // Collect and process data
        $response = [
            'success' => true,
            'message' => 'Custom AJAX handler working!',
            'data' => $_POST, // Example
        ];

        echo json_encode($response);
        exit;
    }
}


include_once plugin_dir_path(__FILE__) . 'inc/bib_admin_page.php';


function qa_bib_custom_template($template) {
    global $post;

    // Define an array of custom templates mapped to page IDs
    $custom_templates_by_id = [
        SUPERVISOR_BIB_CATS => 'supervisor-bib_cats.php',  // Example: Page ID 101 loads page-bib_cats.php
        SUPERVISOR_HOME => 'supervisor-home.php',
        SUPERVISOR_INTRO_TEXT => 'supervisor-content.php' 
    ];

 

    // Check by Page ID
    if (isset($custom_templates_by_id[$post->ID]) && file_exists(plugin_dir_path(__FILE__) . 'templates/' . $custom_templates_by_id[$post->ID])) {
        return plugin_dir_path(__FILE__) . 'templates/' . $custom_templates_by_id[$post->ID];
    }

    return $template;
}
add_filter('template_include', 'qa_bib_custom_template');