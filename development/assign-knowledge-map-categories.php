<?php
/**
 * One-off: auto-assign qa_knowledge_map_category on all qa_tags from Hebrew names + legacy slug fix.
 *
 * Usage (browser): log in as Administrator, then open:
 *   /wp-content/plugins/supervisor_plugin/development/assign-knowledge-map-categories.php
 *
 * Query flags:
 *   ?dry_run=1  — report only, no writes
 *
 * CLI: wp supervisor automap-km-tags [--dry-run] [--fill-all] [--no-fix-legacy]
 */

$wp_load_paths = [
    __DIR__ . '/../../../wp-load.php',
    __DIR__ . '/../../../../wp-load.php',
    __DIR__ . '/../../../../../wp-load.php',
    '/www/brookdalejdcorg_480/public/wp-load.php',
];

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $wp_loaded = true;
        break;
    }
}

if (! $wp_loaded) {
    die('Could not load WordPress. Adjust wp-load paths in this script.');
}

if (! is_user_logged_in() || ! current_user_can('manage_options')) {
    wp_die('You must be logged in as an administrator to run this script.', 'Forbidden', ['response' => 403]);
}

if (! function_exists('supervisor_knowledge_map_autofill_qa_tags_meta')) {
    wp_die('Supervisor plugin is not loaded or is outdated (missing supervisor_knowledge_map_autofill_qa_tags_meta).');
}

$dry_run = ! empty($_GET['dry_run']);

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Assign knowledge map categories</title>';
echo '<style>body{font-family:system-ui,sans-serif;max-width:900px;margin:2rem auto;line-height:1.5;} .ok{color:#065f46;} .muted{color:#555;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ccc;padding:6px 8px;text-align:right;} th{background:#f3f4f6;}</style>';
echo '</head><body dir="rtl">';

echo '<h1>שיוך אוטומטי: קטגוריית מפת הידע לנושאי מפתח</h1>';

if ($dry_run) {
    echo '<p class="muted"><strong>מצב יבש:</strong> לא נשמרו שינויים במסד הנתונים.</p>';
}

$stats = supervisor_knowledge_map_autofill_qa_tags_meta([
    'dry_run'         => $dry_run,
    'only_empty'      => true,
    'fix_legacy_meta' => true,
]);

echo '<ul>';
echo '<li class="ok"><strong>שויך לפי שם:</strong> ' . (int) $stats['assigned'] . '</li>';
echo '<li class="ok"><strong>תוקן מטא ישן (slug):</strong> ' . (int) $stats['fixed_meta'] . '</li>';
echo '<li><strong>דולג — כבר יש שיוך תקף:</strong> ' . (int) $stats['skipped_has_meta'] . '</li>';
echo '<li><strong>דולג — אין התאמה פשוטה לשם:</strong> ' . (int) $stats['skipped_no_match'] . '</li>';
echo '</ul>';

$no_match = array_filter($stats['details'], function ($r) {
    return isset($r['action']) && $r['action'] === 'no_match';
});
if (! empty($no_match)) {
    echo '<h2>נדרש שיוך ידני (לא נמצאה התאמה)</h2>';
    echo '<table><thead><tr><th>מזהה</th><th>שם המונח</th></tr></thead><tbody>';
    foreach ($no_match as $row) {
        echo '<tr><td>' . (int) $row['term_id'] . '</td><td>' . esc_html($row['name']) . '</td></tr>';
    }
    echo '</tbody></table>';
}

$fixed = array_filter($stats['details'], function ($r) {
    return isset($r['action']) && $r['action'] === 'fixed_legacy_meta';
});
if (! empty($fixed)) {
    echo '<h2>תוקן slug ישן</h2><table><thead><tr><th>מזהה</th><th>שם</th><th>leaf</th></tr></thead><tbody>';
    foreach ($fixed as $row) {
        echo '<tr><td>' . (int) $row['term_id'] . '</td><td>' . esc_html($row['name']) . '</td><td>' . esc_html($row['leaf'] ?? '') . '</td></tr>';
    }
    echo '</tbody></table>';
}

$assigned = array_filter($stats['details'], function ($r) {
    return isset($r['action']) && $r['action'] === 'assigned';
});
if (! empty($assigned)) {
    echo '<h2>שויך לפי שם</h2><table><thead><tr><th>מזהה</th><th>שם</th><th>leaf</th></tr></thead><tbody>';
    foreach ($assigned as $row) {
        echo '<tr><td>' . (int) $row['term_id'] . '</td><td>' . esc_html($row['name']) . '</td><td>' . esc_html($row['leaf'] ?? '') . '</td></tr>';
    }
    echo '</tbody></table>';
}

$run_url = plugins_url('development/assign-knowledge-map-categories.php', dirname(__DIR__) . '/supervisor-plugin.php');
echo '<p class="muted">';
echo '<a href="' . esc_url($run_url) . '">הרצה אמיתית (שמירה)</a> · ';
echo '<a href="' . esc_url(add_query_arg('dry_run', '1', $run_url)) . '">הרצת dry-run</a>';
echo '</p>';

echo '</body></html>';
