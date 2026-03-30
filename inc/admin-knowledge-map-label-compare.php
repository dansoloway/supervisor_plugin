<?php
/**
 * WP Admin: compare canonical knowledge-map labels vs qa_tags names; optionally sync.
 * Use when direct access to development/compare-knowledge-map-labels.php returns 404 (host rules).
 */

defined('ABSPATH') || exit;

/**
 * @return int Number of terms updated.
 */
function supervisor_knowledge_map_label_apply_canonical_names() {
    if (! function_exists('supervisor_knowledge_map_canonical_leaf_labels')) {
        return 0;
    }
    $canon = supervisor_knowledge_map_canonical_leaf_labels();
    $terms = get_terms([
        'taxonomy'   => 'qa_tags',
        'hide_empty' => false,
    ]);
    if (is_wp_error($terms)) {
        return 0;
    }
    $updates = 0;
    foreach ($terms as $term) {
        $raw  = get_term_meta($term->term_id, 'qa_knowledge_map_category', true);
        $slug = $raw !== '' && $raw !== null ? sanitize_key($raw) : '';
        if ($slug === '' || ! isset($canon[ $slug ])) {
            continue;
        }
        $expected = $canon[ $slug ];
        if ($term->name === $expected) {
            continue;
        }
        $result = wp_update_term($term->term_id, 'qa_tags', ['name' => $expected]);
        if (! is_wp_error($result)) {
            ++$updates;
        }
    }

    return $updates;
}

/**
 * Render המפקחת → מפת הידע: כותרות.
 */
function supervisor_admin_knowledge_map_label_compare_page() {
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('אין הרשאה.', 'text-domain'), '', ['response' => 403]);
    }

    if (! function_exists('supervisor_knowledge_map_canonical_leaf_labels')) {
        echo '<div class="wrap"><p>' . esc_html__('הרחבה חסרה: supervisor_knowledge_map_canonical_leaf_labels', 'text-domain') . '</p></div>';

        return;
    }

    $base_url = admin_url('admin.php?page=supervisor-km-label-compare');
    $apply    = isset($_GET['apply'], $_GET['_wpnonce'])
        && sanitize_key(wp_unslash($_GET['apply'])) === '1'
        && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'supervisor_km_labels_apply');

    if ($apply) {
        $n = supervisor_knowledge_map_label_apply_canonical_names();
        wp_safe_redirect(
            add_query_arg(
                [
                    'km_synced' => '1',
                    'updated'   => (string) (int) $n,
                ],
                $base_url
            )
        );
        exit;
    }

    $synced_n = isset($_GET['km_synced'], $_GET['updated'])
        ? (int) $_GET['updated']
        : null;

    $apply_url = wp_nonce_url(add_query_arg('apply', '1', $base_url), 'supervisor_km_labels_apply');

    echo '<div class="wrap" dir="rtl">';
    echo '<h1>' . esc_html__('מפת הידע: השוואת כותרות', 'text-domain') . '</h1>';
    echo '<p class="description">' . esc_html__('מקור אמת: supervisor_knowledge_map_canonical_leaf_labels() (צילום מסך).', 'text-domain') . '</p>';

    if ($synced_n !== null && sanitize_key(wp_unslash($_GET['km_synced'] ?? '')) === '1') {
        if ($synced_n > 0) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo esc_html(
                sprintf(
                    /* translators: %d: number of terms updated */
                    __('עודכנו %d שמות תנאים.', 'text-domain'),
                    $synced_n
                )
            );
            echo '</p></div>';
        } else {
            echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__('לא נדרש עדכון — כל השמות כבר תואמים.', 'text-domain') . '</p></div>';
        }
    }

    echo '<p><strong>' . esc_html__('דוח; להחלת שמות במסד:', 'text-domain') . '</strong> ';
    echo '<a href="' . esc_url($apply_url) . '" class="button button-primary" onclick="return confirm(\'' . esc_js(__('לעדכן שמות של נושאי מפתח במסד הנתונים?', 'text-domain')) . '\');">';
    echo esc_html__('החל שמות קנוניים על נושאי מפתח', 'text-domain');
    echo '</a></p>';

    echo '<style>.sv-km-ok{color:#065f46;font-weight:600;} .sv-km-bad{color:#991b1b;font-weight:600;} .sv-km-table{margin-top:1em;}</style>';

    $canon   = supervisor_knowledge_map_canonical_leaf_labels();
    $choices = function_exists('supervisor_knowledge_map_category_choices') ? supervisor_knowledge_map_category_choices() : [];

    echo '<h2>' . esc_html__('קוד: hierarchy מול קנוני', 'text-domain') . '</h2>';
    echo '<table class="widefat striped sv-km-table"><thead><tr>';
    echo '<th>slug</th><th>' . esc_html__('קנוני', 'text-domain') . '</th><th>hierarchy</th><th>' . esc_html__('סטטוס', 'text-domain') . '</th>';
    echo '</tr></thead><tbody>';
    foreach ($canon as $slug => $expected) {
        $actual = isset($choices[ $slug ]) ? $choices[ $slug ] : '';
        $match  = ($actual === $expected);
        echo '<tr>';
        echo '<td>' . esc_html($slug) . '</td>';
        echo '<td>' . esc_html($expected) . '</td>';
        echo '<td>' . esc_html($actual !== '' ? $actual : '(חסר)') . '</td>';
        echo '<td class="' . ( $match ? 'sv-km-ok' : 'sv-km-bad' ) . '">' . ( $match ? esc_html__('תואם', 'text-domain') : esc_html__('סטיה', 'text-domain') ) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

    $extra_in_choices = array_diff_key($choices, $canon);
    if ($extra_in_choices !== []) {
        echo '<div class="notice notice-error"><p>' . esc_html(
            sprintf(
                /* translators: %s: comma-separated slugs */
                __('סלאגים ב-hierarchy שלא בקנוני: %s', 'text-domain'),
                implode(', ', array_keys($extra_in_choices))
            )
        ) . '</p></div>';
    }

    echo '<h2>' . esc_html__('מסד: qa_tags לפי qa_knowledge_map_category', 'text-domain') . '</h2>';

    $terms = get_terms([
        'taxonomy'   => 'qa_tags',
        'hide_empty' => false,
    ]);
    if (is_wp_error($terms)) {
        echo '<div class="notice notice-error"><p>' . esc_html__('שגיאה בטעינת תנאים.', 'text-domain') . '</p></div>';
    } else {
        echo '<table class="widefat striped sv-km-table"><thead><tr>';
        echo '<th>term_id</th><th>' . esc_html__('שם נוכחי', 'text-domain') . '</th><th>meta</th><th>' . esc_html__('כותרת קנונית', 'text-domain') . '</th><th>' . esc_html__('סטטוס', 'text-domain') . '</th>';
        echo '</tr></thead><tbody>';
        $db_ok   = 0;
        $db_bad  = 0;
        $db_skip = 0;
        foreach ($terms as $term) {
            $raw  = get_term_meta($term->term_id, 'qa_knowledge_map_category', true);
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
            echo '<td class="' . ( $match ? 'sv-km-ok' : 'sv-km-bad' ) . '">' . ( $match ? esc_html__('תואם', 'text-domain') : esc_html__('סטיה', 'text-domain') ) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        echo '<p class="description">';
        echo esc_html(
            sprintf(
                /* translators: 1: matched map terms, 2: OK count, 3: mismatch count, 4: skipped */
                __('מותאמי מפה: %1$d · תואמים: %2$d · סטיות: %3$d · ללא מטא/מחוץ לקנוני: %4$d', 'text-domain'),
                (int) ($db_ok + $db_bad),
                (int) $db_ok,
                (int) $db_bad,
                (int) $db_skip
            )
        );
        echo '</p>';
    }

    echo '<p><a href="' . esc_url($base_url) . '">' . esc_html__('רענון דוח', 'text-domain') . '</a></p>';
    echo '</div>';
}
