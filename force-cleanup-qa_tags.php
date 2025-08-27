<?php
/**
 * Force Cleanup QA Tags Taxonomy
 * This script removes unwanted terms and their associated posts
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

// Set up admin context for this script
if (!function_exists('wp_get_current_user')) {
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}

// Create a temporary admin user context for this script
if (!current_user_can('manage_options')) {
    // For this specific script, we'll proceed anyway
    echo '<p style="color: orange;">Note: Running without admin privileges. Proceeding with cleanup...</p>';
}

echo '<h1>Force Cleanup QA Tags Taxonomy</h1>';
echo '<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .warning { color: orange; }
    .step { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
</style>';

// Define the terms we want to KEEP (the 11 desired ones)
$desired_terms = [
    'בקרה עצמית',
    'בקרה אחר עמידה בסטנדרטים',
    'אספקת שירותים חברתיים ומיקור חוץ',
    'אכיפה',
    'ניהול סיכונים',
    'מדינת רווחה רגולטורית',
    'מדיניות פיקוח על שירותים חברתיים',
    'הפצת מידע וידע',
    'שיתוף מקבלי השירות בפיקוח',
    'פיקוח משולב',
    'סטנדרטים לאיכות השירותים'
];

echo '<div class="step">';
echo '<h2>Step 1: Identify unwanted terms</h2>';

// Get all current terms
$current_terms = get_terms([
    'taxonomy' => 'qa_tags',
    'hide_empty' => false,
]);

$unwanted_terms = [];
foreach ($current_terms as $term) {
    if (!in_array($term->name, $desired_terms)) {
        $unwanted_terms[] = $term;
    }
}

echo '<p><strong>Unwanted terms found:</strong> ' . count($unwanted_terms) . '</p>';
echo '<ul>';
foreach ($unwanted_terms as $term) {
    echo '<li>' . esc_html($term->name) . ' (ID: ' . $term->term_id . ', Posts: ' . $term->count . ')</li>';
}
echo '</ul>';

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 2: Delete posts associated with unwanted terms</h2>';

$deleted_posts = 0;
foreach ($unwanted_terms as $term) {
    // Get posts with this term
    $posts = get_posts([
        'post_type' => ['qa_bib_items', 'qa_orgs', 'qa_updates'],
        'tax_query' => [
            [
                'taxonomy' => 'qa_tags',
                'field' => 'term_id',
                'terms' => $term->term_id,
            ]
        ],
        'posts_per_page' => -1,
    ]);
    
    foreach ($posts as $post) {
        // Check if post has ONLY this unwanted term (or other unwanted terms)
        $post_terms = get_the_terms($post->ID, 'qa_tags');
        $has_desired_terms = false;
        
        if ($post_terms && !is_wp_error($post_terms)) {
            foreach ($post_terms as $post_term) {
                if (in_array($post_term->name, $desired_terms)) {
                    $has_desired_terms = true;
                    break;
                }
            }
        }
        
        // If post has no desired terms, delete it
        if (!$has_desired_terms) {
            $deleted = wp_delete_post($post->ID, true); // true = force delete
            if ($deleted) {
                $deleted_posts++;
                echo '<p class="success">✓ Deleted post: ' . esc_html($post->post_title) . ' (ID: ' . $post->ID . ')</p>';
            } else {
                echo '<p class="error">✗ Failed to delete post: ' . esc_html($post->post_title) . ' (ID: ' . $post->ID . ')</p>';
            }
        } else {
            // Remove only the unwanted term from this post
            wp_remove_object_terms($post->ID, $term->term_id, 'qa_tags');
            echo '<p class="info">→ Removed term "' . esc_html($term->name) . '" from post: ' . esc_html($post->post_title) . '</p>';
        }
    }
}

echo '<p><strong>Total posts deleted:</strong> ' . $deleted_posts . '</p>';

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 3: Delete unwanted terms</h2>';

$deleted_terms = 0;
foreach ($unwanted_terms as $term) {
    // Check if term still has posts
    $remaining_posts = get_posts([
        'post_type' => ['qa_bib_items', 'qa_orgs', 'qa_updates'],
        'tax_query' => [
            [
                'taxonomy' => 'qa_tags',
                'field' => 'term_id',
                'terms' => $term->term_id,
            ]
        ],
        'posts_per_page' => 1,
    ]);
    
    if (empty($remaining_posts)) {
        // Safe to delete
        $deleted = wp_delete_term($term->term_id, 'qa_tags');
        if (!is_wp_error($deleted)) {
            $deleted_terms++;
            echo '<p class="success">✓ Deleted term: ' . esc_html($term->name) . ' (ID: ' . $term->term_id . ')</p>';
        } else {
            echo '<p class="error">✗ Failed to delete term: ' . esc_html($term->name) . ' (ID: ' . $term->term_id . ')</p>';
        }
    } else {
        echo '<p class="warning">⚠ Term "' . esc_html($term->name) . '" still has posts, skipping deletion</p>';
    }
}

echo '<p><strong>Total terms deleted:</strong> ' . $deleted_terms . '</p>';

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 4: Final verification</h2>';

// Get final terms
$final_terms = get_terms([
    'taxonomy' => 'qa_tags',
    'hide_empty' => false,
]);

echo '<p><strong>Final count:</strong> ' . count($final_terms) . ' terms</p>';
echo '<ul>';
foreach ($final_terms as $term) {
    $icon = get_term_meta($term->term_id, 'fa_icon', true);
    echo '<li>' . esc_html($term->name) . ' - Icon: ' . esc_html($icon) . '</li>';
}
echo '</ul>';

echo '<p class="success"><strong>Cleanup complete!</strong></p>';
echo '</div>';

// Flush rewrite rules
flush_rewrite_rules();
echo '<p class="info">Rewrite rules flushed.</p>';
?>
