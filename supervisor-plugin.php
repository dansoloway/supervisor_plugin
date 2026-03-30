<?php
/*
Plugin Name: Supervisor Plugin
Description: Custom functionality for Supervisor pages.
Version: 1.0
Author: Daniel Soloway
*/

// Paths (config may override SUPERVISOR_* IDs only; not PLUGIN_ROOT)
if (! defined('PLUGIN_ROOT')) {
    define('PLUGIN_ROOT', plugin_dir_path(__FILE__));
}
if (! defined('SUPERVISOR_PLUGIN_BASENAME')) {
    define('SUPERVISOR_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

// Optional SUPERVISOR_* overrides; otherwise IDs resolve from page slugs (see inc/supervisor-pages.php)
require_once plugin_dir_path(__FILE__) . 'config.php';
require_once plugin_dir_path(__FILE__) . 'inc/supervisor-pages.php';
require_once plugin_dir_path(__FILE__) . 'inc/supervisor-bootstrap-cli.php';

// Knowledge-map card titles (canonical Hebrew)
require_once plugin_dir_path(__FILE__) . 'inc/knowledge-map-canonical-labels.php';

// Include post type and taxonomy registration
require_once plugin_dir_path(__FILE__) . 'inc/register_posts_and_tax.php';

// Country → flag SVG + ACF country select (reads flag-icons country.json via inc/flag-countries-data.php)
require_once plugin_dir_path(__FILE__) . 'inc/org-country-flag.php';
require_once plugin_dir_path(__FILE__) . 'inc/acf-org-country-select.php';

// Knowledge-map category automap (qa_tags meta) + optional WP-CLI command
require_once plugin_dir_path(__FILE__) . 'inc/knowledge-map-automap.php';

// AJAX: bibliography categories (נושאי מפתח) sidebar filter
require_once plugin_dir_path(__FILE__) . 'inc/ajax-bib-cats-terms.php';

// Include bibliography admin functionality
require_once plugin_dir_path(__FILE__) . 'inc/bib_admin_page.php';

// Include consolidated admin menu
require_once plugin_dir_path(__FILE__) . 'inc/admin-menu.php';

// Include ACF custom location rules
require_once plugin_dir_path(__FILE__) . 'acf-location-rules.php';

// One-time / versioned ACF field groups: drop ACF → Tools → Export (PHP) into acf-export/field-groups.php
$supervisor_acf_export = plugin_dir_path(__FILE__) . 'acf-export/field-groups.php';
if (is_readable($supervisor_acf_export)) {
    require_once $supervisor_acf_export;
}


// Include custom user role for supervisor editor
require_once plugin_dir_path(__FILE__) . 'create-plugin-user-role.php';

// Include contact form functionality
require_once plugin_dir_path(__FILE__) . 'contact-form.php';

// Include development tools (after WordPress is loaded)
function supervisor_load_development_tools() {
    // Content management tools - safe to load, they only register admin menu hooks
    require_once plugin_dir_path(__FILE__) . 'development/cleanup-test-content.php';
    require_once plugin_dir_path(__FILE__) . 'development/export-content.php';
    require_once plugin_dir_path(__FILE__) . 'development/import-content.php';
}
add_action('init', 'supervisor_load_development_tools', 1); // Load early so admin_menu hooks work

// Enqueue Google Fonts
function enqueue_supervisor_google_fonts() {
    // Add preconnect links for better performance
    add_action('wp_head', function() {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    }, 1);
    
    // Enqueue Secular One font with all weights
    wp_enqueue_style(
        'supervisor-google-fonts',
        'https://fonts.googleapis.com/css2?family=Secular+One:wght@400;700&display=swap',
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
        'supervisor-knowledge-map.php', // Add knowledge map template
        'taxonomy-qa_tags.php', // Updated to use qa_tags taxonomy template
        'supervisor-search-results.php', // Add search results template
        'supervisor-activities.php', // Add activities template
    ];

    $singles = ['qa_orgs', 'qa_updates', 'qa_bib_items', 'qa_stories'];
    $post_type_archives = ['qa_updates', 'qa_bib_items'];
    $is_taxonomy = is_tax('qa_tags'); // Check if it's a taxonomy archive

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
        '1.0.4', // Version bump to force cache refresh (Phase 1)
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

    if (is_page(SUPERVISOR_BIB_CATS)) {
        $bib_base_km_slugs = [];
        $km_cat_raw          = isset($_GET['km_cat']) ? sanitize_key(wp_unslash($_GET['km_cat'])) : '';
        if ($km_cat_raw !== '' && function_exists('supervisor_knowledge_map_resolve_to_leaf_slugs')) {
            $bib_base_km_slugs = supervisor_knowledge_map_resolve_to_leaf_slugs($km_cat_raw);
            if (! is_array($bib_base_km_slugs)) {
                $bib_base_km_slugs = [];
            }
        }

        wp_enqueue_script(
            'bib-cats-filter',
            plugins_url('/assets/js/bib-cats-filter.js', __FILE__),
            ['jquery'],
            '1.0.0',
            true
        );
        wp_localize_script(
            'bib-cats-filter',
            'bibCatsFilter',
            [
                'ajaxUrl'     => admin_url('admin-ajax.php'),
                'nonce'       => wp_create_nonce('supervisor_bib_cats_filter'),
                'action'      => 'supervisor_bib_cats_terms',
                'baseKmSlugs' => array_values($bib_base_km_slugs),
                'strings'     => [
                    'error' => __('שגיאה בטעינת הנתונים. נסו שוב.', 'text-domain'),
                ],
            ]
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
    
    // Dequeue conflicting styles if they exist
    wp_dequeue_style('styles-orig');
    wp_deregister_style('styles-orig');
}
add_action('wp_enqueue_scripts', 'enqueue_alternate_header_assets', 999); // High priority

// Load custom templates
function supervisor_load_template($template) {
    if (! is_page()) {
        return $template;
    }

    $page_id = get_queried_object_id();
    $base    = function_exists('supervisor_resolve_page_template_basename')
        ? supervisor_resolve_page_template_basename($page_id)
        : null;

    if ($base && file_exists(plugin_dir_path(__FILE__) . 'templates/' . $base)) {
        return plugin_dir_path(__FILE__) . 'templates/' . $base;
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

// Force flush rewrite rules on plugin update
function supervisor_force_rewrite_flush() {
    flush_rewrite_rules();
}
add_action('init', 'supervisor_force_rewrite_flush', 999);

// Cleanup on deactivation
function supervisor_plugin_deactivation() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'supervisor_plugin_deactivation');

// Redirect single update pages to main updates page
function supervisor_redirect_single_updates() {
    if (is_singular('qa_updates')) {
        $updates_page_id = defined('SUPERVISOR_UPDATES') ? SUPERVISOR_UPDATES : null;
        if ($updates_page_id) {
            $updates_url = get_permalink($updates_page_id);
            if ($updates_url) {
                wp_redirect($updates_url, 301); // 301 permanent redirect
                exit;
            }
        }
        // Fallback: redirect to home if updates page not found
        wp_redirect(home_url(), 301);
        exit;
    }
}
add_action('template_redirect', 'supervisor_redirect_single_updates', 1); // Run early

function supervisor_load_templates($template) {
    global $post;

    if (is_singular('qa_orgs') && file_exists(plugin_dir_path(__FILE__) . 'templates/single-qa_orgs.php')) {
        return plugin_dir_path(__FILE__) . 'templates/single-qa_orgs.php';
    }

    if (is_singular('qa_stories') && file_exists(plugin_dir_path(__FILE__) . 'templates/single-qa_stories.php')) {
        return plugin_dir_path(__FILE__) . 'templates/single-qa_stories.php';
    }

    // Single qa_updates pages are now redirected, so we don't load the template
    // Removed: single-qa_updates.php template loading

    // Old qa_bibs post type removed - using qa_bib_items instead
    // Template check removed as qa_bibs no longer exists

 

    return $template;
}
add_filter('template_include', 'supervisor_load_templates');

function supervisor_force_taxonomy_template($template) {
    if (is_tax('qa_tags') && file_exists(plugin_dir_path(__FILE__) . 'templates/taxonomy-qa_tags.php')) {
        return plugin_dir_path(__FILE__) . 'templates/taxonomy-qa_tags.php';
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

