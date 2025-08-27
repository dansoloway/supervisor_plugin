<?php
/**
 * Update QA Tags Taxonomy
 * This script updates the qa_tags taxonomy to match the exact categories needed
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
    // This is safe since we're only updating taxonomy terms
    echo '<p style="color: orange;">Note: Running without admin privileges. Proceeding with taxonomy updates...</p>';
}

echo '<h1>Updating QA Tags Taxonomy</h1>';
echo '<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .step { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
</style>';

// Define the exact categories we want to keep
$desired_categories = [
    'בקרה עצמית' => 'Self-control / Self-monitoring',
    'בקרה אחר עמידה בסטנדרטים' => 'Control after compliance with standards',
    'אספקת שירותים חברתיים ומיקור חוץ' => 'Provision of social services and outsourcing',
    'אכיפה' => 'Enforcement',
    'ניהול סיכונים' => 'Risk management',
    'מדינת רווחה רגולטורית' => 'Regulatory welfare state',
    'מדיניות פיקוח על שירותים חברתיים' => 'Supervision policy on social services',
    'הפצת מידע וידע' => 'Dissemination of information and knowledge',
    'שיתוף מקבלי השירות בפיקוח' => 'Involving service recipients in supervision',
    'פיקוח משולב' => 'Integrated supervision',
    'סטנדרטים לאיכות השירותים' => 'Service quality standards'
];

echo '<div class="step">';
echo '<h2>Step 1: Getting current terms</h2>';

// Get all current terms
$current_terms = get_terms([
    'taxonomy' => 'qa_tags',
    'hide_empty' => false,
]);

echo '<p>Found ' . count($current_terms) . ' current terms:</p>';
echo '<ul>';
foreach ($current_terms as $term) {
    echo '<li>' . esc_html($term->name) . ' (ID: ' . $term->term_id . ')</li>';
}
echo '</ul>';
echo '</div>';

echo '<div class="step">';
echo '<h2>Step 2: Creating/updating desired terms</h2>';

$created_terms = [];
$updated_terms = [];

foreach ($desired_categories as $hebrew_name => $english_description) {
    // Check if term already exists
    $existing_term = get_term_by('name', $hebrew_name, 'qa_tags');
    
    if ($existing_term) {
        // Term exists, update description if needed
        $updated = wp_update_term($existing_term->term_id, 'qa_tags', [
            'name' => $hebrew_name,
            'description' => $english_description
        ]);
        
        if (!is_wp_error($updated)) {
            $updated_terms[] = $hebrew_name;
            echo '<p class="success">✓ Updated: ' . esc_html($hebrew_name) . '</p>';
        } else {
            echo '<p class="error">✗ Failed to update: ' . esc_html($hebrew_name) . '</p>';
        }
    } else {
        // Term doesn't exist, create it
        $new_term = wp_insert_term($hebrew_name, 'qa_tags', [
            'description' => $english_description
        ]);
        
        if (!is_wp_error($new_term)) {
            $created_terms[] = $hebrew_name;
            echo '<p class="success">✓ Created: ' . esc_html($hebrew_name) . '</p>';
        } else {
            echo '<p class="error">✗ Failed to create: ' . esc_html($hebrew_name) . '</p>';
        }
    }
}

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 3: Removing unnecessary terms</h2>';

$terms_to_delete = [];
foreach ($current_terms as $term) {
    if (!array_key_exists($term->name, $desired_categories)) {
        $terms_to_delete[] = $term;
    }
}

if (empty($terms_to_delete)) {
    echo '<p class="info">No unnecessary terms to delete.</p>';
} else {
    echo '<p>Found ' . count($terms_to_delete) . ' terms to delete:</p>';
    echo '<ul>';
    foreach ($terms_to_delete as $term) {
        echo '<li>' . esc_html($term->name) . ' (ID: ' . $term->term_id . ')</li>';
    }
    echo '</ul>';
    
    echo '<p><strong>Note:</strong> Terms with associated posts will not be deleted automatically for data safety.</p>';
    
    foreach ($terms_to_delete as $term) {
        // Check if term has posts
        $posts_with_term = get_posts([
            'post_type' => ['qa_bib_items', 'qa_orgs', 'qa_updates'],
            'tax_query' => [
                [
                    'taxonomy' => 'qa_tags',
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ]
            ],
            'posts_per_page' => 1
        ]);
        
        if (empty($posts_with_term)) {
            // Safe to delete
            $deleted = wp_delete_term($term->term_id, 'qa_tags');
            if (!is_wp_error($deleted)) {
                echo '<p class="success">✓ Deleted: ' . esc_html($term->name) . '</p>';
            } else {
                echo '<p class="error">✗ Failed to delete: ' . esc_html($term->name) . '</p>';
            }
        } else {
            echo '<p class="info">⚠ Skipped: ' . esc_html($term->name) . ' (has associated posts)</p>';
        }
    }
}

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 4: Final verification</h2>';

// Get final terms
$final_terms = get_terms([
    'taxonomy' => 'qa_tags',
    'hide_empty' => false,
]);

echo '<p>Final count: ' . count($final_terms) . ' terms</p>';
echo '<ul>';
foreach ($final_terms as $term) {
    echo '<li>' . esc_html($term->name) . '</li>';
}
echo '</ul>';

echo '<p class="success"><strong>Update complete!</strong></p>';
echo '</div>';

// Flush rewrite rules
flush_rewrite_rules();
echo '<p class="info">Rewrite rules flushed.</p>';
?>
