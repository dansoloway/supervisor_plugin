<?php
/*
Plugin Name: Supervisor Plugin
Description: Custom functionality for Supervisor pages.
Version: 1.0
Author: Daniel Soloway
*/

// Include configuration file
require_once plugin_dir_path(__FILE__) . 'config.php';

// Include post type and taxonomy registration
require_once plugin_dir_path(__FILE__) . 'inc/register_posts_and_tax.php';

// Enqueue Google Fonts
function enqueue_supervisor_google_fonts() {
    // Add preconnect links for better performance
    add_action('wp_head', function() {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    }, 1);
    
    // Enqueue Heebo font with all weights
    wp_enqueue_style(
        'supervisor-google-fonts',
        'https://fonts.googleapis.com/css2?family=Heebo:wght@100;200;300;400;500;600;700;800;900&display=swap',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'enqueue_supervisor_google_fonts', 1); // Load early

// Enqueue styles and scripts
function enqueue_alternate_header_assets() {
    global $template; // Get the currently loaded template file

    $plugin_templates = [
        'supervisor-home.php',
        'supervisor-updates.php',
        'supervisor-content.php',
        'supervisor-bib_cats.php',
        'supervisor-qa_orgs.php',
        'taxonomy-qa_bib_cats.php', // Ensure taxonomy template is included
        'supervisor-search-results.php', // Add search results template
        'supervisor-activities.php', // Add activities template
    ];

    $singles = ['qa_bibs', 'qa_orgs', 'qa_updates', 'qa_bib_items'];
    $post_type_archives = ['qa_updates', 'qa_bib_items'];
    $is_taxonomy = is_tax('qa_bib_cats'); // Check if it's a taxonomy archive

    $is_plugin_template = in_array(basename($template), $plugin_templates);
    $is_archive = is_post_type_archive($post_type_archives);
    $is_single = is_singular($singles);

    // Debug logging
    error_log('=== SUPERVISOR CSS DEBUG ===');
    error_log('Template: ' . $template);
    error_log('Is plugin template? ' . ($is_plugin_template ? 'Yes' : 'No'));
    error_log('Is taxonomy archive? ' . ($is_taxonomy ? 'Yes' : 'No'));
    error_log('Is archive? ' . ($is_archive ? 'Yes' : 'No'));
    error_log('Is single? ' . ($is_single ? 'Yes' : 'No'));

    // Load CSS on ALL pages for now to ensure it works
    error_log('Enqueueing CSS & JS for ALL pages');
    
    // Enqueue CSS with cache busting and high priority
    wp_enqueue_style(
        'supervisor-styles',
        plugins_url('/assets/css/supervisor-styles.css', __FILE__),
        ['supervisor-google-fonts'], // Make sure CSS loads after fonts
        time(), // Force cache refresh
        'all' // Media type
    );

    // Enqueue Custom JavaScript
    wp_enqueue_script(
        'supervisor-scripts',
        plugins_url('/assets/js/supervisor-scripts.js', __FILE__), // Path to JS file
        [], // Dependencies
        time(), // Force cache refresh
        true // Load in the footer
    );

    // Enqueue AJAX Search JavaScript
    wp_enqueue_script(
        'ajax-search',
        plugins_url('/assets/js/ajax-search.js', __FILE__),
        ['jquery'], // Dependencies
        time(), // Force cache refresh
        true // Load in the footer
    );

    // Enqueue Global Search JavaScript (for global search pages)
    wp_enqueue_script(
        'global-search',
        plugins_url('/assets/js/global-search.js', __FILE__),
        ['jquery'], // Dependencies
        time(), // Force cache refresh
        true // Load in the footer
    );

    // Enqueue Home Search JavaScript (only on home page)
    if (is_page(SUPERVISOR_HOME)) {
        wp_enqueue_script(
            'home-search',
            plugins_url('/assets/js/home-search.js', __FILE__),
            ['jquery'], // Dependencies
            time(), // Force cache refresh
            true // Load in the footer
        );
    }

    // Enqueue Font Awesome
    wp_enqueue_script(
        'font-awesome',
        'https://kit.fontawesome.com/c1b1058543.js',
        [],
        null, // No version needed
        false // Load in the header (FontAwesome should load early)
    );
}
add_action('wp_enqueue_scripts', 'enqueue_alternate_header_assets', 999); // High priority

// Load custom templates
function supervisor_load_template($template) {
    $page_id = get_queried_object_id();

    $custom_templates_by_id = [
        SUPERVISOR_BIB_CATS => 'supervisor-bib_cats.php',
        SUPERVISOR_ORGS => 'supervisor-qa_orgs.php',
        SUPERVISOR_HOME => 'supervisor-home.php',
        SUPERVISOR_UPDATES => 'supervisor-updates.php',
        SUPERVISOR_INTRO_TEXT => 'supervisor-content.php',
        SUPERVISOR_ABOUT => 'supervisor-content.php',
        SUPERVISOR_CONTACT => 'supervisor-content.php',
        SUPERVISOR_ACTIVITIES => 'supervisor-activities.php',
    ];

    if (isset($custom_templates_by_id[$page_id]) && file_exists(plugin_dir_path(__FILE__) . 'templates/' . $custom_templates_by_id[$page_id])) {
        return plugin_dir_path(__FILE__) . 'templates/' . $custom_templates_by_id[$page_id];
    }

    return $template;
}
add_filter('template_include', 'supervisor_load_template');

// Register AJAX endpoint
function register_custom_ajax_endpoint() {
    add_rewrite_rule('^custom-ajax-endpoint/?$', 'index.php?custom_ajax=1', 'top');
}
add_action('init', 'register_custom_ajax_endpoint');

function add_custom_query_vars($vars) {
    $vars[] = 'custom_ajax';
    return $vars;
}
add_filter('query_vars', 'add_custom_query_vars');

function handle_custom_ajax_request() {
    if (get_query_var('custom_ajax') == 1) {
        echo json_encode(['status' => 'success', 'message' => 'AJAX request handled!']);
        exit;
    }
}
add_action('template_redirect', 'handle_custom_ajax_request');

// Flush rewrite rules on activation
function flush_supervisor_rewrites() {
    register_custom_ajax_endpoint();
    supervisor_add_search_endpoint();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'flush_supervisor_rewrites');

// Cleanup on deactivation
function supervisor_plugin_deactivation() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'supervisor_plugin_deactivation');

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

function supervisor_force_taxonomy_template($template) {
    if (is_tax('qa_bib_cats') && file_exists(plugin_dir_path(__FILE__) . 'templates/taxonomy-qa_bib_cats.php')) {
        return plugin_dir_path(__FILE__) . 'templates/taxonomy-qa_bib_cats.php';
    }
    return $template;
}
add_filter('template_include', 'supervisor_force_taxonomy_template');

// Handle search results page
function supervisor_handle_search_results($template) {
    // Check if this is a supervisor search request
    if (isset($_GET['supervisor_search']) && !empty($_GET['supervisor_search'])) {
        $search_results_template = plugin_dir_path(__FILE__) . 'templates/supervisor-search-results.php';
        if (file_exists($search_results_template)) {
            return $search_results_template;
        }
    }
    return $template;
}
add_filter('template_include', 'supervisor_handle_search_results', 20);

// Register supervisor search endpoint
function supervisor_add_search_endpoint() {
    add_rewrite_rule(
        '^supervisor-search/?$',
        'index.php?supervisor_search=1',
        'top'
    );
}
add_action('init', 'supervisor_add_search_endpoint');

// Add query vars
function supervisor_add_query_vars($vars) {
    $vars[] = 'supervisor_search';
    return $vars;
}
add_filter('query_vars', 'supervisor_add_query_vars');

// Handle supervisor search endpoint
function supervisor_handle_search_endpoint() {
    if (get_query_var('supervisor_search') == '1') {
        $search_results_template = plugin_dir_path(__FILE__) . 'templates/supervisor-search-results.php';
        if (file_exists($search_results_template)) {
            include $search_results_template;
            exit;
        }
    }
}
add_action('template_redirect', 'supervisor_handle_search_endpoint');

// Helper function to get supervisor search URL
function get_supervisor_search_url($search_term = '') {
    if (!empty($search_term)) {
        return home_url('/supervisor-search/?supervisor_search=' . urlencode($search_term));
    }
    return home_url('/supervisor-search/');
}