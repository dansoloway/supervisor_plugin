<?php
// Load WordPress environment dynamically
$wp_loaded = false;

// Try multiple common WordPress load paths
$possible_paths = [
    // Standard WordPress structure
    dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php',
    dirname(dirname(dirname(__FILE__))) . '/wp-load.php',
    dirname(dirname(__FILE__)) . '/wp-load.php',
    dirname(__FILE__) . '/wp-load.php',
    
    // Kinsta staging specific paths
    '/www/brookdalejdcorg_480/public/wp-load.php',
    '/www/brookdalejdcorg_480/public_html/wp-load.php',
    
    // Common hosting paths
    $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php',
    dirname($_SERVER['DOCUMENT_ROOT']) . '/wp-load.php',
];

// Log file path (create logs directory if it doesn't exist)
$log_dir = dirname(__FILE__) . '/../logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}
$log_file = $log_dir . '/search_log.txt';

// Log the search attempt
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Starting WordPress load attempt\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Current file: " . __FILE__ . "\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'not set') . "\n", FILE_APPEND);

// Try each possible path
foreach ($possible_paths as $path) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Trying path: $path\n", FILE_APPEND);
    
    if (file_exists($path)) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Found WordPress at: $path\n", FILE_APPEND);
        require_once $path;
        $wp_loaded = true;
        break;
    }
}

// If WordPress still not loaded, try to find it by searching up the directory tree
if (!$wp_loaded) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - WordPress not found in common paths, searching directory tree\n", FILE_APPEND);
    
    $current_dir = dirname(__FILE__);
    $max_depth = 10; // Prevent infinite loops
    
    for ($i = 0; $i < $max_depth; $i++) {
        $wp_load_path = $current_dir . '/wp-load.php';
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Searching: $wp_load_path\n", FILE_APPEND);
        
        if (file_exists($wp_load_path)) {
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Found WordPress at: $wp_load_path\n", FILE_APPEND);
            require_once $wp_load_path;
            $wp_loaded = true;
            break;
        }
        
        $current_dir = dirname($current_dir);
        if ($current_dir === '/' || $current_dir === '') {
            break; // Reached root
        }
    }
}

// If WordPress still not loaded, return error
if (!$wp_loaded) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - WordPress not found after all attempts\n", FILE_APPEND);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    echo json_encode(['success' => false, 'message' => 'WordPress not found']);
    exit;
}

// Log successful WordPress load
file_put_contents($log_file, date('Y-m-d H:i:s') . " - WordPress loaded successfully\n", FILE_APPEND);

// Log raw POST data
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Raw POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Decode and sanitize input
$search_text = sanitize_text_field($_POST['search_text'] ?? '');
$qa_themes = $_POST['qa_themes'] ?? [];
$qa_tags = $_POST['qa_tags'] ?? [];
$post_types = $_POST['post_types'] ?? ['qa_orgs', 'qa_updates', 'qa_bibs']; // Default to all types

// Log raw input before processing
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Raw qa_themes before processing: " . print_r($qa_themes, true) . "\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Raw qa_tags before processing: " . print_r($qa_tags, true) . "\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Post types requested: " . print_r($post_types, true) . "\n", FILE_APPEND);

// Ensure arrays are properly formatted
if (!is_array($qa_themes)) {
    $qa_themes = [$qa_themes];
}
if (!is_array($qa_tags)) {
    $qa_tags = [$qa_tags];
}

// Log after array conversion
file_put_contents($log_file, date('Y-m-d H:i:s') . " - qa_themes after array conversion: " . print_r($qa_themes, true) . "\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - qa_tags after array conversion: " . print_r($qa_tags, true) . "\n", FILE_APPEND);

// Sanitize array values and filter out empty ones
$qa_themes = array_map(function($value) {
    $decoded = urldecode($value);
    return sanitize_text_field($decoded);
}, array_filter($qa_themes, function($value) {
    $trimmed = trim($value);
    $is_empty = empty($trimmed);
    return !$is_empty;
}));

$qa_tags = array_map(function($value) {
    $decoded = urldecode($value);
    return sanitize_text_field($decoded);
}, array_filter($qa_tags, function($value) {
    $trimmed = trim($value);
    $is_empty = empty($trimmed);
    return !$is_empty;
}));

// Log after filtering
file_put_contents($log_file, date('Y-m-d H:i:s') . " - qa_themes after filtering: " . print_r($qa_themes, true) . "\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - qa_tags after filtering: " . print_r($qa_tags, true) . "\n", FILE_APPEND);

// Log decoded values
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Search text: $search_text\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Themes: " . print_r($qa_themes, true) . "\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Tags: " . print_r($qa_tags, true) . "\n", FILE_APPEND);

// Build query arguments
$args = [
    'post_type' => $post_types,
    'posts_per_page' => 10,
];

// Add search text if not empty
if (!empty($search_text)) {
    $args['s'] = $search_text;
}

// Add taxonomy filters
$tax_query = [];

// Only add themes if we have valid non-empty values
if (!empty($qa_themes) && count($qa_themes) > 0) {
    // Double-check that we don't have empty values
    $valid_themes = array_filter($qa_themes, function($theme) {
        return !empty(trim($theme));
    });
    
    if (!empty($valid_themes)) {
        $tax_query[] = [
            'taxonomy' => 'qa_themes',
            'field'    => 'slug',
            'terms'    => $valid_themes,
            'operator' => 'IN',
        ];
    }
}

// Only add tags if we have valid non-empty values
if (!empty($qa_tags) && count($qa_tags) > 0) {
    // Double-check that we don't have empty values
    $valid_tags = array_filter($qa_tags, function($tag) {
        return !empty(trim($tag));
    });
    
    if (!empty($valid_tags)) {
        $tax_query[] = [
            'taxonomy' => 'qa_tags',
            'field'    => 'slug',
            'terms'    => $valid_tags,
            'operator' => 'IN',
        ];
    }
}

// Include tax_query only if we have valid filters
if (!empty($tax_query) && count($tax_query) > 0) {
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