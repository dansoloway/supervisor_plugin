<?php
/**
 * Auto-assign qa_knowledge_map_category term meta from נושא מפתח names (and normalize legacy slugs).
 */

defined('ABSPATH') || exit;

/**
 * Hebrew term names (and common variants) → knowledge-map leaf slug.
 * Only "simple" unambiguous matches; parent-only labels (e.g. מדיניות alone) are omitted.
 *
 * @return array<string, string> normalized label => leaf slug
 */
function supervisor_knowledge_map_automap_name_source_rows() {
    return [
        ['מדיניות פיקוח', 'policy_supervision'],
        ['מדיניות פיקוח על שירותים חברתיים', 'policy_supervision'],
        ['סטנדרטים לאיכות השירותים', 'policy_service_quality_standards'],
        ['סטנדרטים לאיכות השירות', 'policy_service_quality_standards'],
        ['סטנדרטים לעבודת הפיקוח', 'policy_supervision'],

        ['בקרה חיצונית', 'control_external'],
        ['בקרה עצמית', 'control_self'],
        ['בקרה אחר עמידה בסטנדרטים', 'control_external'],

        ['אכיפה', 'enforcement_corrective_punitive'],
        ['אכיפה מתקנת', 'enforcement_corrective_punitive'],
        ['אכיפה עונשית', 'enforcement_corrective_punitive'],
        ['אכיפה מתקנת ואכיפה עונשית', 'enforcement_corrective_punitive'],

        ['חומרי הדרכה', 'knowledge_training_materials'],
        ['מדריכים ופרקטיקות מיטביות', 'knowledge_training_materials'],
        ['מחקרים', 'knowledge_research'],
        ['מחקר', 'knowledge_research'],

        ['ניהול סיכונים', 'wm_risk_management'],
        ['שיתוף מקבלי השירות בפיקוח', 'wm_service_user_participation'],
        ['שקיפות והנגשת מידע', 'wm_transparency_access'],
        ['הפצת מידע וידע', 'wm_transparency_access'],
        ['פיקוח משולב', 'wm_integrated_supervision'],
        ['יחסי מפקחים מפוקחים', 'wm_supervisor_supervisee_relations'],

        ['אספקת שירותים חברתיים ומיקור חוץ', 'sp_service_delivery_outsourcing'],
        ['רכש חברתי', 'sp_service_delivery_outsourcing'],

        ['מדינת הרווחה הרגולטורית', 'regulatory_welfare_state'],
        ['מדינת רווחה רגולטורית', 'regulatory_welfare_state'],
    ];
}

/**
 * Normalize term title for lookup (trim, collapse whitespace, strip HTML / bidi marks).
 */
function supervisor_knowledge_map_automap_normalize_label($label) {
    $label = wp_strip_all_tags((string) $label);
    $label = trim(preg_replace('/[\x{200F}\x{200E}\x{202A}-\x{202E}]/u', '', $label));
    $label = trim(preg_replace('/\s+/u', ' ', $label));

    return $label;
}

/**
 * Build lookup table: normalized Hebrew => leaf slug.
 *
 * @return array<string, string>
 */
function supervisor_knowledge_map_automap_name_lookup() {
    static $lookup = null;
    if ($lookup !== null) {
        return $lookup;
    }
    $lookup = [];
    foreach (supervisor_knowledge_map_automap_name_source_rows() as $row) {
        $key             = supervisor_knowledge_map_automap_normalize_label($row[0]);
        $lookup[ $key ] = $row[1];
    }

    return $lookup;
}

/**
 * Guess leaf slug from term name only.
 *
 * @param string $name Term name.
 * @return string Empty if no simple match.
 */
function supervisor_knowledge_map_automap_guess_leaf_from_name($name) {
    $key = supervisor_knowledge_map_automap_normalize_label($name);
    $map = supervisor_knowledge_map_automap_name_lookup();

    return isset($map[ $key ]) ? $map[ $key ] : '';
}

/**
 * Run automap on all qa_tags terms.
 *
 * @param array{dry_run?: bool, only_empty?: bool, fix_legacy_meta?: bool} $args
 * @return array{assigned: int, skipped_has_meta: int, skipped_no_match: int, fixed_meta: int, details: list<array{term_id: int, name: string, action: string, leaf?: string}>}
 */
function supervisor_knowledge_map_autofill_qa_tags_meta($args = []) {
    $dry_run        = ! empty($args['dry_run']);
    $only_empty     = array_key_exists('only_empty', $args) ? (bool) $args['only_empty'] : true;
    $fix_legacy_meta = array_key_exists('fix_legacy_meta', $args) ? (bool) $args['fix_legacy_meta'] : true;

    $choices = supervisor_knowledge_map_category_choices();

    $stats = [
        'assigned'           => 0,
        'skipped_has_meta'   => 0,
        'skipped_no_match'   => 0,
        'fixed_meta'         => 0,
        'details'            => [],
    ];

    $terms = get_terms([
        'taxonomy'   => 'qa_tags',
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return $stats;
    }

    foreach ($terms as $term) {
        $term_id   = (int) $term->term_id;
        $name      = $term->name;
        $current   = get_term_meta($term_id, 'qa_knowledge_map_category', true);
        $current   = $current !== '' && $current !== null ? sanitize_key($current) : '';
        $has_valid = $current !== '' && isset($choices[ $current ]);

        // Normalize legacy stored slug to new leaf (e.g. guides_best_practices → knowledge_training_materials).
        if ($fix_legacy_meta && $current !== '' && ! $has_valid) {
            $normalized_store = supervisor_knowledge_map_normalize_slug($current);
            if (isset($choices[ $normalized_store ])) {
                if (! $dry_run && $normalized_store !== $current) {
                    update_term_meta($term_id, 'qa_knowledge_map_category', $normalized_store);
                }
                if ($normalized_store !== $current) {
                    $stats['fixed_meta']++;
                    $stats['details'][] = [
                        'term_id' => $term_id,
                        'name'    => $name,
                        'action'  => 'fixed_legacy_meta',
                        'leaf'    => $normalized_store,
                    ];
                }
                continue;
            }
        }

        if ($only_empty && $has_valid) {
            $stats['skipped_has_meta']++;
            continue;
        }

        $guess = supervisor_knowledge_map_automap_guess_leaf_from_name($name);
        if ($guess === '' || ! isset($choices[ $guess ])) {
            $stats['skipped_no_match']++;
            $stats['details'][] = [
                'term_id' => $term_id,
                'name'    => $name,
                'action'  => 'no_match',
            ];
            continue;
        }

        if (! $dry_run) {
            update_term_meta($term_id, 'qa_knowledge_map_category', $guess);
        }
        $stats['assigned']++;
        $stats['details'][] = [
            'term_id' => $term_id,
            'name'    => $name,
            'action'  => 'assigned',
            'leaf'    => $guess,
        ];
    }

    return $stats;
}

if (defined('WP_CLI') && WP_CLI) {
    /**
     * Auto-assign knowledge-map categories on qa_tags from Hebrew names / legacy meta.
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : Report changes without writing.
     *
     * [--fill-all]
     * : Also set meta where a value already exists (still useful if name map is more specific — use with care).
     *
     * [--no-fix-legacy]
     * : Do not rewrite old stored slug values via supervisor_knowledge_map_normalize_slug().
     */
    WP_CLI::add_command('supervisor automap-km-tags', function ($__, $assoc_args) {
        $dry       = WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
        $fill_all  = WP_CLI\Utils\get_flag_value($assoc_args, 'fill-all', false);
        $fix_meta  = ! WP_CLI\Utils\get_flag_value($assoc_args, 'no-fix-legacy', false);

        $stats = supervisor_knowledge_map_autofill_qa_tags_meta([
            'dry_run'         => $dry,
            'only_empty'      => ! $fill_all,
            'fix_legacy_meta' => $fix_meta,
        ]);

        WP_CLI::log($dry ? '(dry run) no database writes' : 'Updated database');
        WP_CLI::success(sprintf(
            'assigned: %d | fixed legacy meta: %d | skipped (already set): %d | no name match: %d',
            $stats['assigned'],
            $stats['fixed_meta'],
            $stats['skipped_has_meta'],
            $stats['skipped_no_match']
        ));

        foreach ($stats['details'] as $row) {
            if ($row['action'] === 'no_match') {
                WP_CLI::log(sprintf('  [%d] %s → (no simple match)', $row['term_id'], $row['name']));
            }
        }
    });
}
