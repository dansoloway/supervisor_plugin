<?php
/**
 * Compare knowledge-map canonical labels (screenshot) vs hierarchy + qa_tags term names; optionally rename terms.
 *
 * Prefer WP Admin → המפקחת → מפת הידע: כותרות (avoids 404 when hosts block direct plugin PHP).
 *
 * Legacy direct URL (may 404 on Kinsta etc.):
 *   /wp-content/plugins/supervisor_plugin/development/compare-knowledge-map-labels.php
 *
 * Query:
 *   ?apply=1 — wp_update_term names to match canonical
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

if (! function_exists('supervisor_knowledge_map_canonical_leaf_labels')) {
    wp_die('Supervisor plugin missing supervisor_knowledge_map_canonical_leaf_labels().');
}

$dry_run = empty($_GET['apply']);
$apply   = ! empty($_GET['apply']);

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Knowledge map labels</title>';
echo '<style>body{font-family:system-ui,sans-serif;max-width:960px;margin:2rem auto;line-height:1.5;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ccc;padding:6px 8px;text-align:right;} th{background:#f3f4f6;} .ok{color:#065f46;} .bad{color:#991b1b;} .muted{color:#555;}</style>';
echo '</head><body dir="rtl">';

echo '<h1>מפת הידע: השוואת כותרות</h1>';
echo '<p class="muted">מקור אמת: <code>supervisor_knowledge_map_canonical_leaf_labels()</code> (צילום מסך).</p>';

if ($dry_run && ! $apply) {
    echo '<p class="ok"><strong>מצב דוח בלבד.</strong> להחלת שמות במסד: <a href="' . esc_url(add_query_arg('apply', '1')) . '">הרץ עם apply=1</a></p>';
} elseif ($apply) {
    echo '<p class="bad"><strong>מצב יישום:</strong> שמות נושאי מפתח יעודכנו אם יש סטיה.</p>';
}

$canon = supervisor_knowledge_map_canonical_leaf_labels();
$choices = function_exists('supervisor_knowledge_map_category_choices') ? supervisor_knowledge_map_category_choices() : [];

echo '<h2>קוד: hierarchy מול קנוני</h2>';
echo '<table><thead><tr><th>slug</th><th>קנוני</th><th>מתוך hierarchy</th><th>סטטוס</th></tr></thead><tbody>';
$code_ok = 0;
$code_bad = 0;
foreach ($canon as $slug => $expected) {
    $actual = isset($choices[ $slug ]) ? $choices[ $slug ] : '';
    $match  = ($actual === $expected);
    if ($match) {
        ++$code_ok;
    } else {
        ++$code_bad;
    }
    echo '<tr>';
    echo '<td>' . esc_html($slug) . '</td>';
    echo '<td>' . esc_html($expected) . '</td>';
    echo '<td>' . esc_html($actual !== '' ? $actual : '(חסר)') . '</td>';
    echo '<td class="' . ( $match ? 'ok' : 'bad' ) . '">' . ( $match ? 'תואם' : 'סטיה' ) . '</td>';
    echo '</tr>';
}
echo '</tbody></table>';

$extra_in_choices = array_diff_key($choices, $canon);
if ($extra_in_choices !== []) {
    echo '<p class="bad">סלאגים ב-hierarchy שלא בקנוני: ' . esc_html(implode(', ', array_keys($extra_in_choices))) . '</p>';
}

echo '<h2>מסד: qa_tags לפי qa_knowledge_map_category</h2>';
$terms = get_terms([
    'taxonomy'   => 'qa_tags',
    'hide_empty' => false,
]);
if (is_wp_error($terms)) {
    echo '<p class="bad">שגיאה בטעינת תנאים.</p>';
} else {
    echo '<table><thead><tr><th>term_id</th><th>שם נוכחי</th><th>slug מטא</th><th>כותרת קנונית</th><th>סטטוס</th></tr></thead><tbody>';
    $db_ok = 0;
    $db_bad = 0;
    $db_skip = 0;
    $updates = 0;
    foreach ($terms as $term) {
        $raw = get_term_meta($term->term_id, 'qa_knowledge_map_category', true);
        $slug = $raw !== '' && $raw !== null ? sanitize_key($raw) : '';
        if ($slug === '' || ! isset($canon[ $slug ])) {
            ++$db_skip;
            continue;
        }
        $expected = $canon[ $slug ];
        $match    = ($term->name === $expected);
        if ($match) {
            ++$db_ok;
        } else {
            ++$db_bad;
        }
        echo '<tr>';
        echo '<td>' . (int) $term->term_id . '</td>';
        echo '<td>' . esc_html($term->name) . '</td>';
        echo '<td>' . esc_html($slug) . '</td>';
        echo '<td>' . esc_html($expected) . '</td>';
        echo '<td class="' . ( $match ? 'ok' : 'bad' ) . '">' . ( $match ? 'תואם' : 'סטיה' ) . '</td>';
        echo '</tr>';

        if (! $match && $apply) {
            $result = wp_update_term($term->term_id, 'qa_tags', ['name' => $expected]);
            if (! is_wp_error($result)) {
                ++$updates;
            }
        }
    }
    echo '</tbody></table>';
    echo '<p class="muted">מותאמי מפה: ' . (int) ($db_ok + $db_bad) . ' | תואמים: ' . (int) $db_ok . ' | סטיות: ' . (int) $db_bad . ' | ללא מטא/מחוץ לקנוני: ' . (int) $db_skip . '</p>';
    if ($apply && $updates > 0) {
        echo '<p class="ok">עודכנו ' . (int) $updates . ' שמות תנאים.</p>';
    }
}

echo '<p><a href="' . esc_url(remove_query_arg(['apply', 'dry_run'])) . '">רענון דוח</a></p>';
echo '</body></html>';
