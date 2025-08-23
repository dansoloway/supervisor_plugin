<?php
// Load WordPress environment dynamically
$wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
if (file_exists($wp_load_path)) {
    require_once $wp_load_path;
} else {
    // Fallback for different server configurations
    $wp_load_path = dirname(dirname(dirname(__FILE__))) . '/wp-load.php';
    if (file_exists($wp_load_path)) {
        require_once $wp_load_path;
    } else {
        // Try to find wp-load.php in common locations
        $possible_paths = [
            dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php',
            dirname(dirname(dirname(__FILE__))) . '/wp-load.php',
            dirname(dirname(__FILE__)) . '/wp-load.php',
            dirname(__FILE__) . '/wp-load.php',
        ];
        
        $wp_loaded = false;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                $wp_loaded = true;
                break;
            }
        }
        
        if (!$wp_loaded) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'WordPress not found']);
            exit;
        }
    }
}

// Log file path (create logs directory if it doesn't exist)
$log_dir = dirname(__FILE__) . '/../logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}
$log_file = $log_dir . '/search_log.txt';

// Log raw POST data
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Raw POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Decode and sanitize input
$search_text = sanitize_text_field($_POST['search_text'] ?? '');
$qa_themes = $_POST['qa_themes'] ?? [];
$qa_tags = $_POST['qa_tags'] ?? [];

// Ensure arrays are properly formatted
if (!is_array($qa_themes)) {
    $qa_themes = [$qa_themes];
}
if (!is_array($qa_tags)) {
    $qa_tags = [$qa_tags];
}

// Sanitize array values
$qa_themes = array_map('sanitize_text_field', array_filter($qa_themes));
$qa_tags = array_map('sanitize_text_field', array_filter($qa_tags));

// Log decoded values
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Search text: $search_text\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Themes: " . print_r($qa_themes, true) . "\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Tags: " . print_r($qa_tags, true) . "\n", FILE_APPEND);

// Build query arguments
$args = [
    'post_type' => ['qa_orgs', 'qa_updates', 'qa_bibs'],
    'posts_per_page' => 10,
];

// Add search text if not empty
if (!empty($search_text)) {
    $args['s'] = $search_text;
}

// Add taxonomy filters
$tax_query = [];
if (!empty($qa_themes)) {
    $tax_query[] = [
        'taxonomy' => 'qa_themes',
        'field'    => 'slug',
        'terms'    => $qa_themes,
        'operator' => 'IN',
    ];
}

if (!empty($qa_tags)) {
    $tax_query[] = [
        'taxonomy' => 'qa_tags',
        'field'    => 'slug',
        'terms'    => $qa_tags,
        'operator' => 'IN',
    ];
}

// Include tax_query only if filters are provided
if (!empty($tax_query)) {
    if (count($tax_query) > 1) {
        $args['tax_query'] = [
            'relation' => 'AND',
            $tax_query
        ];
    } else {
        $args['tax_query'] = $tax_query;
    }
}

// Log query arguments
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Query Arguments: " . print_r($args, true) . "\n", FILE_APPEND);

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
            'type'  => get_post_type(),
        ];
    }
}
wp_reset_postdata();

// Log results count
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Found " . count($results) . " results\n", FILE_APPEND);

// Return results as JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

echo json_encode(['success' => true, 'data' => $results]);
exit;