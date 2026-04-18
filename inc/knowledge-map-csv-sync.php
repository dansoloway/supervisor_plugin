<?php
/**
 * Sync qa_tags knowledge-map meta from נושאי מפתח - correct.csv (repo root).
 */

defined('ABSPATH') || exit;

/**
 * Default path to the canonical CSV (UTF-8, comma-separated).
 */
function supervisor_knowledge_map_csv_default_path() {
    return PLUGIN_ROOT . 'נושאי מפתח - correct.csv';
}

/**
 * Turn optional CSV icon cell (e.g. glasses, shield-alt) into a Font Awesome class string.
 */
function supervisor_knowledge_map_csv_icon_cell_to_fa_class($raw) {
    $raw = trim((string) $raw);
    if ($raw === '') {
        return '';
    }
    if (preg_match('/\bfa-(solid|regular|brands)\s+fa-/i', $raw) || preg_match('/^fa[srb]?\s+fa-/i', $raw)) {
        return sanitize_text_field($raw);
    }
    $suffix = strtolower(preg_replace('/\s+/u', '-', $raw));
    $suffix = preg_replace('/[^a-z0-9-]/', '', $suffix);
    if ($suffix === '') {
        return '';
    }
    $aliases = [
        'shield-alt' => 'shield-halved',
    ];
    if (isset($aliases[ $suffix ])) {
        $suffix = $aliases[ $suffix ];
    }

    return 'fa-solid fa-' . $suffix;
}

/**
 * @return list<array{0: string, 1: string, 2: string}> [שם נושא המפתח, קטגוריית מפת הידע, optional שם האייקון]
 */
function supervisor_knowledge_map_csv_read_rows($path) {
    if (! is_readable($path)) {
        return [];
    }
    $fh = fopen($path, 'rb');
    if ($fh === false) {
        return [];
    }
    $rows   = [];
    $header = fgetcsv($fh);
    while (($row = fgetcsv($fh)) !== false) {
        if (! isset($row[0], $row[1])) {
            continue;
        }
        $a = supervisor_knowledge_map_automap_normalize_label($row[0]);
        $b = supervisor_knowledge_map_automap_normalize_label($row[1]);
        $c = isset($row[2]) ? trim((string) $row[2]) : '';
        if ($a === '' || $b === '') {
            continue;
        }
        $rows[] = [$a, $b, $c];
    }
    fclose($fh);

    $unique = [];
    $seen    = [];
    foreach ($rows as $triple) {
        $k = $triple[0] . "\t" . $triple[1];
        if (isset($seen[ $k ])) {
            continue;
        }
        $seen[ $k ]   = true;
        $unique[]     = $triple;
    }

    return $unique;
}

/**
 * Expected CSV column "קטגוריית מפת הידע" per leaf (Hebrew, not passed through translate — stable under WP-CLI locale).
 *
 * @return array<string, string>
 */
function supervisor_knowledge_map_csv_parent_column_for_leaf() {
    return [
        'policy_supervision'                 => 'מדיניות',
        'policy_service_quality_standards' => 'מדיניות',
        'control_external'                   => 'בקרה',
        'control_self'                       => 'בקרה',
        'enforcement_corrective_punitive'    => 'אכיפה',
        'knowledge_training_materials'       => 'פיתוח ידע והדרכה',
        'knowledge_research'                 => 'פיתוח ידע והדרכה',
        'wm_risk_management'                 => 'שיטות עבודה',
        'wm_service_user_participation'      => 'שיטות עבודה',
        'wm_transparency_access'             => 'שיטות עבודה',
        'wm_integrated_supervision'          => 'שיטות עבודה',
        'wm_supervisor_supervisee_relations' => 'שיטות עבודה',
        'sp_service_delivery_outsourcing'   => 'רכש חברתי',
        'regulatory_welfare_state'           => 'מדינת הרווחה הרגולטורית',
    ];
}

/**
 * Resolve leaf slug from topic title (column 1) using current hierarchy labels.
 *
 * @return string Empty if no leaf has this exact title.
 */
function supervisor_knowledge_map_csv_leaf_slug_for_topic_title($topic_title) {
    $want = supervisor_knowledge_map_automap_normalize_label($topic_title);
    foreach (supervisor_knowledge_map_category_choices() as $slug => $label) {
        if (supervisor_knowledge_map_automap_normalize_label($label) === $want) {
            return $slug;
        }
    }

    return '';
}

/**
 * @return list<int>
 */
function supervisor_knowledge_map_csv_term_ids_for_exact_name($name) {
    global $wpdb;

    return array_map('intval', $wpdb->get_col(
        $wpdb->prepare(
            "SELECT t.term_id FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id AND tt.taxonomy = %s
            WHERE t.name = %s",
            'qa_tags',
            $name
        )
    ));
}

/**
 * Apply CSV rows to qa_tags: set qa_knowledge_map_category to the matching leaf slug.
 *
 * @param array{dry_run?: bool, path?: string} $args
 * @return array{updated: int, icons_written: int, skipped_parent_mismatch: int, skipped_no_leaf: int, skipped_no_term: int, csv_pairs?: int, rows: list<array<string, mixed>>}
 */
function supervisor_knowledge_map_sync_csv_to_qa_tags($args = []) {
    $dry  = ! empty($args['dry_run']);
    $path = isset($args['path']) && $args['path'] !== ''
        ? $args['path']
        : supervisor_knowledge_map_csv_default_path();

    $stats = [
        'updated'                   => 0,
        'icons_written'             => 0,
        'skipped_parent_mismatch'   => 0,
        'skipped_no_leaf'           => 0,
        'skipped_no_term'           => 0,
        'csv_pairs'                 => 0,
        'rows'                      => [],
    ];

    $choices = supervisor_knowledge_map_category_choices();
    $rows    = supervisor_knowledge_map_csv_read_rows($path);
    if ($rows === []) {
        return $stats;
    }

    $stats['csv_pairs'] = count($rows);

    $parent_map = supervisor_knowledge_map_csv_parent_column_for_leaf();

    foreach ($rows as $triple) {
        $topic       = $triple[0];
        $parent_csv  = $triple[1];
        $icon_cell   = isset($triple[2]) ? (string) $triple[2] : '';
        $leaf        = supervisor_knowledge_map_csv_leaf_slug_for_topic_title($topic);
        if ($leaf === '' || ! isset($choices[ $leaf ])) {
            $stats['skipped_no_leaf']++;
            $stats['rows'][] = ['topic' => $topic, 'action' => 'no_leaf_match'];
            continue;
        }
        $expected_csv_parent = $parent_map[ $leaf ] ?? '';
        if ($expected_csv_parent === ''
            || supervisor_knowledge_map_automap_normalize_label($parent_csv)
                !== supervisor_knowledge_map_automap_normalize_label($expected_csv_parent)) {
            $stats['skipped_parent_mismatch']++;
            $stats['rows'][] = [
                'topic'            => $topic,
                'action'           => 'parent_mismatch',
                'csv_parent'       => $parent_csv,
                'expected_parent'  => $expected_csv_parent,
                'leaf'             => $leaf,
            ];
            continue;
        }

        $term_ids = supervisor_knowledge_map_csv_term_ids_for_exact_name($topic);
        if ($term_ids === []) {
            $stats['skipped_no_term']++;
            $stats['rows'][] = ['topic' => $topic, 'leaf' => $leaf, 'action' => 'no_term'];
            continue;
        }

        $fa_class = $icon_cell !== '' ? supervisor_knowledge_map_csv_icon_cell_to_fa_class($icon_cell) : '';

        foreach ($term_ids as $term_id) {
            $current = get_term_meta($term_id, 'qa_knowledge_map_category', true);
            $current = $current !== '' && $current !== null ? sanitize_key((string) $current) : '';
            if (! $dry && $current !== $leaf) {
                update_term_meta($term_id, 'qa_knowledge_map_category', $leaf);
            }
            if ($current !== $leaf) {
                $stats['updated']++;
            }
            if ($fa_class !== '' && ! $dry) {
                $prev_icon = (string) get_term_meta($term_id, 'fa_icon', true);
                update_term_meta($term_id, 'fa_icon', $fa_class);
                if ($prev_icon !== $fa_class) {
                    $stats['icons_written']++;
                }
            }
        }
    }

    return $stats;
}

if (defined('WP_CLI') && WP_CLI) {
    /**
     * Set qa_knowledge_map_category on qa_tags from the repo CSV (default: נושאי מפתח - correct.csv).
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : Report actions without writing term meta.
     *
     * [--csv=<path>]
     * : Absolute or relative path to a UTF-8 CSV with header: שם נושא המפתח,קטגוריית מפת הידע[,שם האייקון] (optional third column → fa_icon term meta).
     */
    WP_CLI::add_command('supervisor sync-km-csv', function ($__, $assoc_args) {
        $dry  = WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
        $file = WP_CLI\Utils\get_flag_value($assoc_args, 'csv', '');
        if ($file !== '' && ! preg_match('#^/#', $file) && ! preg_match('#^[A-Za-z]:[/\\\\]#', $file)) {
            $file = PLUGIN_ROOT . ltrim($file, '/');
        }

        $path = $file !== '' ? $file : supervisor_knowledge_map_csv_default_path();
        if (! is_readable($path)) {
            WP_CLI::error(sprintf('CSV not readable: %s', $path));
        }

        $stats = supervisor_knowledge_map_sync_csv_to_qa_tags([
            'dry_run' => $dry,
            'path'    => $path,
        ]);

        if (empty($stats['csv_pairs'])) {
            WP_CLI::error('No data rows found in CSV (check header and UTF-8).');
        }

        WP_CLI::log($dry ? '(dry run) no database writes for meta changes' : 'Term meta updated where needed');
        WP_CLI::success(sprintf(
            'km_category writes: %d | fa_icon writes: %d | no qa_tags term for name: %d | topic not a leaf title: %d | parent column mismatch: %d',
            $stats['updated'],
            $stats['icons_written'],
            $stats['skipped_no_term'],
            $stats['skipped_no_leaf'],
            $stats['skipped_parent_mismatch']
        ));

        foreach ($stats['rows'] as $row) {
            if (($row['action'] ?? '') === 'parent_mismatch') {
                WP_CLI::warning(sprintf(
                    'parent mismatch for "%s": CSV "%s" vs expected "%s" (leaf %s)',
                    $row['topic'],
                    $row['csv_parent'],
                    $row['expected_parent'],
                    $row['leaf']
                ));
            }
            if (($row['action'] ?? '') === 'no_term') {
                WP_CLI::log(sprintf('  no qa_tags term with exact name: %s (leaf would be %s)', $row['topic'], $row['leaf']));
            }
            if (($row['action'] ?? '') === 'no_leaf_match') {
                WP_CLI::log(sprintf('  no leaf title matches: %s', $row['topic']));
            }
        }
    });
}
