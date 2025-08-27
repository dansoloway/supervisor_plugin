<?php
/**
 * Debug QA Tags Taxonomy
 * This script shows the current state of qa_tags taxonomy
 */

// Load WordPress - try multiple possible paths
$wp_load_paths = [
    '../../../wp-load.php',
    '../../../../wp-load.php',
    '../../../../../wp-load.php',
    '/www/brookdalejdcorg_480/public/wp-load.php'
];

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('Could not load WordPress. Please check the file paths.');
}

echo '<h1>Debug QA Tags Taxonomy</h1>';
echo '<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .warning { color: orange; }
    .step { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
    th { background-color: #f2f2f2; }
</style>';

echo '<div class="step">';
echo '<h2>Step 1: Check if qa_tags taxonomy exists</h2>';

$taxonomy_exists = taxonomy_exists('qa_tags');
echo '<p><strong>qa_tags taxonomy exists:</strong> ' . ($taxonomy_exists ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>') . '</p>';

if ($taxonomy_exists) {
    $taxonomy_object = get_taxonomy('qa_tags');
    echo '<p><strong>Taxonomy object:</strong></p>';
    echo '<pre>' . print_r($taxonomy_object, true) . '</pre>';
}

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 2: Get all current terms</h2>';

// Get all current terms
$current_terms = get_terms([
    'taxonomy' => 'qa_tags',
    'hide_empty' => false,
]);

echo '<p><strong>Total terms found:</strong> ' . count($current_terms) . '</p>';

if (!empty($current_terms)) {
    echo '<table>';
    echo '<tr><th>Term ID</th><th>Name</th><th>Slug</th><th>Description</th><th>fa_icon Meta</th><th>qa_bib_description Meta</th><th>Post Count</th></tr>';
    
    foreach ($current_terms as $term) {
        $fa_icon = get_term_meta($term->term_id, 'fa_icon', true);
        $qa_bib_description = get_term_meta($term->term_id, 'qa_bib_description', true);
        $post_count = $term->count;
        
        echo '<tr>';
        echo '<td>' . $term->term_id . '</td>';
        echo '<td>' . esc_html($term->name) . '</td>';
        echo '<td>' . esc_html($term->slug) . '</td>';
        echo '<td>' . esc_html($term->description) . '</td>';
        echo '<td>' . esc_html($fa_icon) . '</td>';
        echo '<td>' . esc_html($qa_bib_description) . '</td>';
        echo '<td>' . $post_count . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p class="warning">No terms found in qa_tags taxonomy.</p>';
}

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 3: Check for posts using qa_tags</h2>';

$post_types = ['qa_bib_items', 'qa_orgs', 'qa_updates'];
foreach ($post_types as $post_type) {
    $posts_with_tags = get_posts([
        'post_type' => $post_type,
        'tax_query' => [
            [
                'taxonomy' => 'qa_tags',
                'operator' => 'EXISTS',
            ]
        ],
        'posts_per_page' => -1,
    ]);
    
    echo '<p><strong>' . $post_type . ' posts with qa_tags:</strong> ' . count($posts_with_tags) . '</p>';
    
    if (!empty($posts_with_tags)) {
        echo '<ul>';
        foreach ($posts_with_tags as $post) {
            $terms = get_the_terms($post->ID, 'qa_tags');
            $term_names = [];
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
            }
            echo '<li>' . esc_html($post->post_title) . ' (ID: ' . $post->ID . ') - Tags: ' . implode(', ', $term_names) . '</li>';
        }
        echo '</ul>';
    }
}

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 4: Check if get_term_fa_icon function exists</h2>';

if (function_exists('get_term_fa_icon')) {
    echo '<p class="success">✓ get_term_fa_icon function exists</p>';
    
    // Test the function with the first term
    if (!empty($current_terms)) {
        $first_term = $current_terms[0];
        $test_icon = get_term_fa_icon($first_term->term_id, 'fas fa-folder');
        echo '<p><strong>Test get_term_fa_icon for "' . esc_html($first_term->name) . '":</strong> ' . esc_html($test_icon) . '</p>';
    }
} else {
    echo '<p class="error">✗ get_term_fa_icon function does not exist</p>';
}

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 5: Check WordPress cache</h2>';

echo '<p><strong>WordPress cache status:</strong></p>';
echo '<ul>';
echo '<li>Object cache: ' . (wp_cache_get('test') !== false ? 'Enabled' : 'Disabled') . '</li>';
echo '<li>Transients: ' . (get_transient('test') !== false ? 'Enabled' : 'Disabled') . '</li>';
echo '</ul>';

echo '<p><strong>Recommendation:</strong> Try clearing any caching plugins or CDN cache if you have them.</p>';

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 6: Manual cleanup options</h2>';

echo '<p>If you want to manually clean up the taxonomy:</p>';
echo '<ol>';
echo '<li>Run the update script again: <code>php update-qa_tags.php</code></li>';
echo '<li>Clear any caching plugins</li>';
echo '<li>Check if there are any terms with associated posts that couldn\'t be deleted</li>';
echo '<li>Manually delete unwanted terms from WordPress admin</li>';
echo '</ol>';

echo '</div>';
?>
