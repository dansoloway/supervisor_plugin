<?php
/**
 * Search Debug Tool for Supervisor Plugin
 * This tool helps debug search functionality by analyzing queries and results
 */

// WP-CLI automatically loads WordPress, so we don't need to load it manually
if (!defined('ABSPATH')) {
    // If not running via WP-CLI, try to load WordPress
    $wp_loaded = false;
    
    // Try multiple common WordPress load paths
    $possible_paths = [
        dirname(__FILE__) . '/wp-load.php',
        dirname(dirname(__FILE__)) . '/wp-load.php',
        dirname(dirname(dirname(__FILE__))) . '/wp-load.php',
        $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php',
    ];
    
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die('WordPress not found. Please run this script from the WordPress root directory or via WP-CLI.');
    }
}

// Check if we're in admin or can run this safely
if (!defined('WP_CLI') && !current_user_can('manage_options')) {
    die('Insufficient permissions to run this script. Please run via WP-CLI or as an admin user.');
}

// Output function that works with both WP-CLI and browser
function debug_output($message) {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log($message);
    } else {
        echo $message . "<br>";
    }
}

function debug_search($search_term) {
    debug_output("=== DEBUGGING SEARCH FOR: '$search_term' ===");
    
    // 1. Check total posts by type
    debug_output("\n1. TOTAL POSTS BY TYPE:");
    $post_types = ['qa_updates', 'qa_orgs', 'qa_bib_items'];
    foreach ($post_types as $post_type) {
        $count = wp_count_posts($post_type)->publish;
        debug_output("   $post_type: $count posts");
    }
    
    // 2. Test basic search query
    debug_output("\n2. BASIC SEARCH QUERY TEST:");
    $basic_args = [
        'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
        'posts_per_page' => -1,
        's' => $search_term
    ];
    $basic_query = new WP_Query($basic_args);
    debug_output("   Basic search results: " . $basic_query->found_posts . " posts");
    
    // 3. Test taxonomy search
    debug_output("\n3. TAXONOMY SEARCH TEST:");
    $taxonomies = ['qa_themes', 'qa_tags', 'qa_bib_cats'];
    foreach ($taxonomies as $taxonomy) {
        $tax_query = new WP_Query([
            'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
            'posts_per_page' => -1,
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'name',
                    'terms' => $search_term,
                    'operator' => 'LIKE'
                ]
            ]
        ]);
        debug_output("   $taxonomy search: " . $tax_query->found_posts . " posts");
    }
    
    // 4. Test custom fields search
    debug_output("\n4. CUSTOM FIELDS SEARCH TEST:");
    $custom_fields = [
        'qa_updates_link',
        'qa_updates_date', 
        'qa_orgs_link',
        'orignial_link'
    ];
    foreach ($custom_fields as $field) {
        $meta_query = new WP_Query([
            'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => $field,
                    'value' => $search_term,
                    'compare' => 'LIKE'
                ]
            ]
        ]);
        debug_output("   $field search: " . $meta_query->found_posts . " posts");
    }
    
    // 5. Test the actual search logic from supervisor-search-results.php
    debug_output("\n5. ACTUAL SEARCH LOGIC TEST:");
    
    // First, get posts that match the search term in title/content
    $title_content_posts = get_posts([
        'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
        'posts_per_page' => -1,
        's' => $search_term,
        'fields' => 'ids'
    ]);
    debug_output("   Title/content matches: " . count($title_content_posts) . " posts");
    
    // Get posts that have matching taxonomy terms
    $taxonomy_posts = get_posts([
        'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
        'posts_per_page' => -1,
        'tax_query' => [
            'relation' => 'OR',
            [
                'taxonomy' => 'qa_themes',
                'field' => 'name',
                'terms' => $search_term,
                'operator' => 'LIKE'
            ],
            [
                'taxonomy' => 'qa_tags',
                'field' => 'name',
                'terms' => $search_term,
                'operator' => 'LIKE'
            ],
            [
                'taxonomy' => 'qa_bib_cats',
                'field' => 'name',
                'terms' => $search_term,
                'operator' => 'LIKE'
            ]
        ],
        'fields' => 'ids'
    ]);
    debug_output("   Taxonomy matches: " . count($taxonomy_posts) . " posts");
    
    // Get posts that have matching custom fields
    $meta_posts = get_posts([
        'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'qa_updates_link',
                'value' => $search_term,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'qa_updates_date',
                'value' => $search_term,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'qa_orgs_link',
                'value' => $search_term,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'orignial_link',
                'value' => $search_term,
                'compare' => 'LIKE'
            ]
        ],
        'fields' => 'ids'
    ]);
    debug_output("   Custom field matches: " . count($meta_posts) . " posts");
    
    // Combine all post IDs and remove duplicates
    $all_post_ids = array_unique(array_merge($title_content_posts, $taxonomy_posts, $meta_posts));
    debug_output("   Combined unique matches: " . count($all_post_ids) . " posts");
    
    // 6. Show sample results
    if (!empty($all_post_ids)) {
        debug_output("\n6. SAMPLE RESULTS:");
        $sample_query = new WP_Query([
            'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
            'post__in' => array_slice($all_post_ids, 0, 5), // Show first 5
            'posts_per_page' => 5
        ]);
        
        while ($sample_query->have_posts()) {
            $sample_query->the_post();
            $post_id = get_the_ID();
            $post_type = get_post_type();
            $title = get_the_title();
            debug_output("   - [$post_type] ID:$post_id - $title");
        }
        wp_reset_postdata();
    }
    
    // 7. Check if search term is empty or problematic
    debug_output("\n7. SEARCH TERM ANALYSIS:");
    debug_output("   Search term length: " . strlen($search_term));
    debug_output("   Search term is empty: " . (empty($search_term) ? 'YES' : 'NO'));
    debug_output("   Search term trimmed: '" . trim($search_term) . "'");
    
    // 8. Test with empty search term
    if (empty(trim($search_term))) {
        debug_output("\n8. EMPTY SEARCH TERM DETECTED!");
        debug_output("   This might be causing all posts to be returned.");
        debug_output("   Check if the search form is sending empty values.");
    }
    
    debug_output("\n=== END DEBUG ===");
}

// Main execution
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::log("Search Debug Tool for Supervisor Plugin");
} else {
    echo "<h1>Search Debug Tool for Supervisor Plugin</h1>";
}

// Get search term from command line argument or default
$search_term = '';
if (defined('WP_CLI') && WP_CLI) {
    if (isset($args[0])) {
        $search_term = $args[0];
    } else {
        $search_term = 'chocolate'; // Default test term
    }
} else {
    $search_term = $_GET['term'] ?? 'chocolate'; // Default test term
}

debug_search($search_term);

// Additional tests
debug_output("\n=== ADDITIONAL TESTS ===");

// Test with a known term that should exist
debug_output("\nTesting with 'חינוך' (should find education-related content):");
debug_search('חינוך');

// Test with empty string
debug_output("\nTesting with empty string (should find nothing):");
debug_search('');

// Test with a term that definitely shouldn't exist
debug_output("\nTesting with 'xyz123nonexistent' (should find nothing):");
debug_search('xyz123nonexistent');
?>
