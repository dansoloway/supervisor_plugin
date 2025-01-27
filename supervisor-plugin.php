<?php
/**
 * Plugin Name: Supervisor Plugin
 * Description: A plugin to manage custom post types for ארגונים, עדכונים, and ביבליוגרפיה.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

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
    register_post_type('qa_bibs', [
        'labels' => [
            'name' => __('ביבליוגרפיה', 'text-domain'),
            'singular_name' => __('פריט ביבליוגרפי', 'text-domain'),
            'add_new' => __('הוסף חדש', 'text-domain'),
            'add_new_item' => __('הוסף פריט ביבליוגרפי חדש', 'text-domain'),
            'edit_item' => __('ערוך פריט ביבליוגרפי', 'text-domain'),
            'new_item' => __('פריט ביבליוגרפי חדש', 'text-domain'),
            'view_item' => __('הצג פריט ביבליוגרפי', 'text-domain'),
            'search_items' => __('חפש ביבליוגרפיה', 'text-domain'),
            'not_found' => __('לא נמצאו פריטים ביבליוגרפיים', 'text-domain'),
            'not_found_in_trash' => __('לא נמצאו פריטים ביבליוגרפיים באשפה', 'text-domain'),
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'qa-bibs'],
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

    register_taxonomy('qa_themes', ['qa_orgs', 'qa_updates', 'qa_bibs'], [
        'labels' => $themes_labels,
        'hierarchical' => true, // Enables dropdown behavior
        'public' => false, // Hides the taxonomy UI for creating terms
        'show_ui' => true, // Show taxonomy in the admin post edit screen
        'show_in_nav_menus' => false,
        'show_in_rest' => true, // Enable for Gutenberg and API
        'rewrite' => ['slug' => 'qa-themes'],
    ]);

    // Register "qa_tags" (no changes needed)
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

    register_taxonomy('qa_tags', ['qa_orgs', 'qa_updates', 'qa_bibs'], [
        'labels' => $tags_labels,
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'qa-tags'],
    ]);
}
add_action('init', 'register_additional_taxonomies');

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
    $templates = ['supervisor-home.php'];
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
    
        // Pass the URL to the AJAX handler
        wp_localize_script('ajax-search-script', 'ajaxSearchParams', [
            'ajax_url' => plugins_url('ajax/search_handler.php', __FILE__), // Correct URL
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_alternate_header_assets');

// Add the custom Supervisor Home template
function supervisor_add_template($templates) {
    $templates['supervisor-home.php'] = 'Supervisor Home';
    return $templates;
}
add_filter('theme_page_templates', 'supervisor_add_template');

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

