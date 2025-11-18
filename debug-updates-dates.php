<?php
/**
 * Debug script to check update dates in database
 * 
 * Access via: /wp-admin/admin.php?page=debug-updates-dates
 * Or run from WP-CLI: wp eval-file wp-content/plugins/supervisor_plugin/debug-updates-dates.php
 */

// If accessed directly, try to load WordPress
if (!defined('ABSPATH')) {
    // Try multiple possible paths
    $paths = [
        dirname(dirname(dirname(__FILE__))) . '/wp-load.php',
        dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php',
        '../../../wp-load.php',
    ];
    
    $loaded = false;
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            $loaded = true;
            break;
        }
    }
    
    if (!$loaded) {
        die('Could not load WordPress. Access via: /wp-admin/admin.php?page=debug-updates-dates');
    }
}

// Function to display the debug info
function supervisor_debug_updates_dates() {
    echo '<div class="wrap">';
    echo "<h1>QA Updates Date Debug</h1>\n";
echo "<style>
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
    th { background-color: #f2f2f2; }
    .has-acf { background-color: #d4edda; }
    .no-acf { background-color: #f8d7da; }
    .same-date { background-color: #fff3cd; }
</style>\n";

$args = [
    'post_type' => 'qa_updates',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
];

$query = new WP_Query($args);

echo "<h2>Found {$query->post_count} updates</h2>\n";
echo "<table>\n";
echo "<tr><th>ID</th><th>Title</th><th>Post Date</th><th>ACF Date (raw)</th><th>ACF Date (formatted)</th><th>Homepage Would Show</th><th>Updates Page Would Show</th></tr>\n";

$dates_seen = [];
$acf_dates_count = 0;

while ($query->have_posts()) {
    $query->the_post();
    $post_id = get_the_ID();
    
    // Get post date
    $post_date = get_the_date('Y-m-d');
    $post_date_formatted = get_the_date('F Y');
    
    // Get ACF date
    $acf_date_raw = get_field('qa_updates_date');
    $acf_date_formatted = $acf_date_raw ? date_i18n('F Y', strtotime($acf_date_raw)) : '';
    
    // What would each page show?
    $homepage_date = $acf_date_raw ? date_i18n('F Y', strtotime($acf_date_raw)) : $post_date_formatted;
    $updates_page_date = $acf_date_formatted ?: '';
    
    // Track dates
    if ($acf_date_raw) {
        $acf_dates_count++;
        $dates_seen[$acf_date_raw] = ($dates_seen[$acf_date_raw] ?? 0) + 1;
    }
    
    $row_class = '';
    if ($acf_date_raw) {
        $row_class = 'has-acf';
    } else {
        $row_class = 'no-acf';
    }
    
    // Check if same as others
    if (isset($dates_seen[$acf_date_raw]) && $dates_seen[$acf_date_raw] > 1) {
        $row_class .= ' same-date';
    }
    
    echo "<tr class='{$row_class}'>\n";
    echo "<td>{$post_id}</td>\n";
    echo "<td>" . esc_html(get_the_title()) . "</td>\n";
    echo "<td>{$post_date}<br><small>({$post_date_formatted})</small></td>\n";
    echo "<td>" . ($acf_date_raw ? esc_html($acf_date_raw) : '<strong>NO ACF DATE</strong>') . "</td>\n";
    echo "<td>" . ($acf_date_formatted ? esc_html($acf_date_formatted) : '-') . "</td>\n";
    echo "<td><strong>" . esc_html($homepage_date) . "</strong></td>\n";
    echo "<td>" . ($updates_page_date ? '<strong>' . esc_html($updates_page_date) . '</strong>' : '<em>No date shown</em>') . "</td>\n";
    echo "</tr>\n";
}

wp_reset_postdata();

echo "</table>\n";

echo "<h2>Summary</h2>\n";
echo "<ul>\n";
echo "<li><strong>Total updates:</strong> {$query->post_count}</li>\n";
echo "<li><strong>Updates with ACF date:</strong> {$acf_dates_count}</li>\n";
echo "<li><strong>Updates without ACF date:</strong> " . ($query->post_count - $acf_dates_count) . "</li>\n";
echo "</ul>\n";

if (!empty($dates_seen)) {
    echo "<h2>ACF Date Distribution</h2>\n";
    echo "<ul>\n";
    foreach ($dates_seen as $date => $count) {
        echo "<li><strong>" . esc_html($date) . "</strong> (" . date_i18n('F Y', strtotime($date)) . "): {$count} update(s)</li>\n";
    }
    echo "</ul>\n";
}

// Check if all homepage dates are the same
$homepage_dates = [];
$query = new WP_Query($args);
while ($query->have_posts()) {
    $query->the_post();
    $acf_date = get_field('qa_updates_date');
    $homepage_date = $acf_date ? date_i18n('F Y', strtotime($acf_date)) : get_the_date('F Y');
    $homepage_dates[] = $homepage_date;
}
wp_reset_postdata();

$unique_homepage_dates = array_unique($homepage_dates);
echo "<h2>Homepage Date Analysis</h2>\n";
echo "<p><strong>Unique dates that would appear on homepage:</strong> " . count($unique_homepage_dates) . "</p>\n";
if (count($unique_homepage_dates) === 1) {
    echo "<p class='same-date'><strong>⚠️ All updates show the same date on homepage: " . esc_html(reset($unique_homepage_dates)) . "</strong></p>\n";
}
echo "<ul>\n";
foreach ($unique_homepage_dates as $date) {
    $count = array_count_values($homepage_dates)[$date];
    echo "<li>" . esc_html($date) . ": {$count} update(s)</li>\n";
}
echo "</ul>\n";
    echo '</div>';
}

// Add admin menu item for debug page
function supervisor_add_debug_menu() {
    add_submenu_page(
        'supervisor-admin',
        'Debug: Update Dates',
        'Debug Dates',
        'manage_options', // Only admins can see this
        'debug-updates-dates',
        'supervisor_debug_updates_dates'
    );
}
add_action('admin_menu', 'supervisor_add_debug_menu', 99);

// If accessed directly and WordPress is loaded, show output
if (defined('ABSPATH') && !is_admin()) {
    // Direct access - show output
    supervisor_debug_updates_dates();
}

