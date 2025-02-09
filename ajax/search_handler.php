<?php
// Load WordPress environment
require_once '/www/brookdalejdcorg_480/public/wp-load.php';

// Log file path
$log_file = '/www/brookdalejdcorg_480/public/wp-content/plugins/supervisor-plugin/logs/search_log.txt';

// Log raw POST data
file_put_contents($log_file, "Raw POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Decode and sanitize input
$search_text = sanitize_text_field($_POST['search_text'] ?? ''); // Handle search_text
$qa_theme = sanitize_text_field(urldecode($_POST['qa_themes'] ?? ''));
$qa_tag = sanitize_text_field(urldecode($_POST['qa_tags'] ?? ''));

// Log decoded values
file_put_contents($log_file, "Decoded search_text: $search_text\n", FILE_APPEND);
file_put_contents($log_file, "Decoded qa_themes: $qa_theme\n", FILE_APPEND);
file_put_contents($log_file, "Decoded qa_tags: $qa_tag\n", FILE_APPEND);

// Build query arguments
$args = [
    'post_type' => ['qa_orgs', 'qa_updates', 'qa_bibs'], // Adjust post types as needed
    'posts_per_page' => 10, // Limit results to 10
];

// Add search text if not empty
if (!empty($search_text)) {
    $args['s'] = $search_text;
}

// Add taxonomy filters
$tax_query = [];
if (!empty($qa_theme)) {
    $tax_query[] = [
        'taxonomy' => 'qa_themes',
        'field'    => 'slug',
        'terms'    => $qa_theme,
    ];
}

if (!empty($qa_tag)) {
    $tax_query[] = [
        'taxonomy' => 'qa_tags',
        'field'    => 'slug',
        'terms'    => $qa_tag,
    ];
}

// Include tax_query only if filters are provided
if (!empty($tax_query)) {
    $args['tax_query'] = $tax_query;
}

// Log query arguments
file_put_contents($log_file, "Query Arguments: " . print_r($args, true) . "\n", FILE_APPEND);

// Perform query
$query = new WP_Query($args);
$results = [];

// Collect results
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $results[] = [
            'title' => get_the_title(),
            'link'  => get_permalink(),
        ];
    }
}
wp_reset_postdata();

// Return results as JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => $results]);
exit;