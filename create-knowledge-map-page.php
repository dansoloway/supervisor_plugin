<?php
/**
 * Create Knowledge Map Page
 * Creates a WordPress page that uses the supervisor-knowledge-map.php template
 */

// Basic WordPress loading
if (!defined('ABSPATH')) {
    // Try to find wp-load.php
    $wp_load_paths = [
        '../../../wp-load.php',
        '../../wp-load.php',
        '../wp-load.php',
        'wp-load.php'
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
        die('Could not load WordPress. Please run this script from the plugin directory.');
    }
}

echo '<!DOCTYPE html>';
echo '<html dir="rtl" lang="he">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<title>יצירת דף מפת הידע</title>';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background: #f9f9f9; border-radius: 8px; direction: rtl; }';
echo 'h1 { color: #333; text-align: center; }';
echo '.success { color: green; }';
echo '.error { color: red; }';
echo '.warning { color: orange; }';
echo '</style>';
echo '</head>';
echo '<body>';

echo '<h1>יצירת דף מפת הידע</h1>';

// Check if page already exists
$existing_page = get_page_by_path('supervisor-knowledge-map');

if ($existing_page) {
    echo '<p class="warning">דף מפת הידע כבר קיים!</p>';
    echo '<p>כתובת הדף: <a href="' . get_permalink($existing_page->ID) . '" target="_blank">' . get_permalink($existing_page->ID) . '</a></p>';
} else {
    // Create the page
    $page_data = [
        'post_title' => 'מפת הידע',
        'post_name' => 'supervisor-knowledge-map',
        'post_content' => 'דף זה מציג את מפת הידע של מערכת הפיקוח והבקרה על שירותים חברתיים.',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'page_template' => 'supervisor-knowledge-map.php'
    ];
    
    $page_id = wp_insert_post($page_data);
    
    if (is_wp_error($page_id)) {
        echo '<p class="error">שגיאה ביצירת הדף: ' . $page_id->get_error_message() . '</p>';
    } else {
        echo '<p class="success">✓ דף מפת הידע נוצר בהצלחה!</p>';
        echo '<p>כתובת הדף: <a href="' . get_permalink($page_id) . '" target="_blank">' . get_permalink($page_id) . '</a></p>';
        
        // Update the page template
        update_post_meta($page_id, '_wp_page_template', 'supervisor-knowledge-map.php');
        echo '<p class="success">✓ תבנית הדף הוגדרה למפת הידע</p>';
    }
}

echo '<p style="margin-top: 30px;"><a href="' . admin_url() . '" style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">חזרה לניהול</a></p>';
echo '</body></html>';
?>
