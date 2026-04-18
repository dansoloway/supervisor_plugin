<?php
/**
 * Export qa_tags assignments + term data; reset taxonomy from CSV and optionally restore links.
 */

defined('ABSPATH') || exit;

/**
 * Post types that use the qa_tags taxonomy.
 *
 * @return list<string>
 */
function supervisor_qa_tags_assigned_post_types() {
    return ['qa_orgs', 'qa_updates', 'qa_bib_items', 'qa_stories'];
}

/**
 * Default JSON path for qa_tags export / reset (plugin root).
 */
function supervisor_qa_tags_export_default_file_path() {
    return PLUGIN_ROOT . 'qa-tags-export.json';
}

/**
 * Build export payload (posts with tag names/slugs + all qa_tags terms with meta).
 *
 * @return array<string, mixed>
 */
function supervisor_qa_tags_export_state() {
    $posts_out = [];

    foreach (supervisor_qa_tags_assigned_post_types() as $post_type) {
        if (! post_type_exists($post_type)) {
            continue;
        }

        $post_ids = get_posts([
            'post_type'              => $post_type,
            'post_status'            => 'any',
            'posts_per_page'         => -1,
            'fields'                 => 'ids',
            'orderby'                => 'ID',
            'order'                  => 'ASC',
            'suppress_filters'       => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);

        foreach ($post_ids as $post_id) {
            $terms = wp_get_post_terms((int) $post_id, 'qa_tags', ['fields' => 'all']);
            if (is_wp_error($terms) || $terms === []) {
                continue;
            }

            $posts_out[] = [
                'id'             => (int) $post_id,
                'post_type'      => $post_type,
                'qa_tag_names'   => array_map(static function ($t) {
                    return $t->name;
                }, $terms),
                'qa_tag_slugs'   => array_map(static function ($t) {
                    return $t->slug;
                }, $terms),
            ];
        }
    }

    $terms_out = [];
    $all_terms = get_terms([
        'taxonomy'   => 'qa_tags',
        'hide_empty' => false,
    ]);

    if (! is_wp_error($all_terms) && is_array($all_terms)) {
        foreach ($all_terms as $t) {
            $raw = get_term_meta($t->term_id);
            $meta = [];
            if (is_array($raw)) {
                foreach ($raw as $key => $values) {
                    if ($key === 'term_order') {
                        continue;
                    }
                    if (! is_array($values)) {
                        continue;
                    }
                    $meta[ $key ] = count($values) === 1
                        ? maybe_unserialize($values[0])
                        : array_map('maybe_unserialize', $values);
                }
            }

            $terms_out[] = [
                'term_id'     => (int) $t->term_id,
                'name'        => $t->name,
                'slug'        => $t->slug,
                'description' => $t->description,
                'parent'      => (int) $t->parent,
                'meta'        => $meta,
            ];
        }
    }

    return [
        'version'     => 1,
        'exported_at' => current_time('mysql'),
        'site_url'    => site_url(),
        'posts'       => $posts_out,
        'terms'       => $terms_out,
    ];
}

/**
 * Map an exported tag name (possibly legacy wording) to a term_id after CSV reset.
 * Tries exact normalized name, then (unless $exact_match_only) knowledge-map automap (name → leaf → canonical topic label).
 *
 * @param array<string, int> $term_ids_by_normalized_name normalized new topic title => term_id
 */
function supervisor_qa_tags_resolve_export_tag_name_to_term_id($export_name, $term_ids_by_normalized_name, $exact_match_only = false) {
    $norm = supervisor_knowledge_map_automap_normalize_label((string) $export_name);
    if ($norm !== '' && isset($term_ids_by_normalized_name[ $norm ])) {
        return (int) $term_ids_by_normalized_name[ $norm ];
    }

    if ($exact_match_only) {
        return 0;
    }

    $leaf = supervisor_knowledge_map_automap_guess_leaf_from_name((string) $export_name);
    if ($leaf === '') {
        return 0;
    }

    $choices = supervisor_knowledge_map_category_choices();
    if (! isset($choices[ $leaf ])) {
        return 0;
    }

    $canon_norm = supervisor_knowledge_map_automap_normalize_label($choices[ $leaf ]);
    if ($canon_norm === '' || ! isset($term_ids_by_normalized_name[ $canon_norm ])) {
        return 0;
    }

    return (int) $term_ids_by_normalized_name[ $canon_norm ];
}

/**
 * normalized term name => term_id for all current qa_tags (after CSV / manual setup).
 *
 * @return array<string, int>
 */
function supervisor_qa_tags_current_term_id_map_by_normalized_name() {
    $map = [];
    $terms = get_terms([
        'taxonomy'   => 'qa_tags',
        'hide_empty' => false,
    ]);
    if (is_wp_error($terms) || ! is_array($terms)) {
        return $map;
    }
    foreach ($terms as $t) {
        $n = supervisor_knowledge_map_automap_normalize_label($t->name);
        if ($n !== '') {
            $map[ $n ] = (int) $t->term_id;
        }
    }

    return $map;
}

/**
 * Delete every qa_tags term (removes post links; does not delete posts).
 *
 * @param array{dry_run?: bool} $args
 * @return array{deleted: int, errors: list<string>}
 */
function supervisor_qa_tags_delete_all_terms($args = []) {
    $dry = ! empty($args['dry_run']);
    $out = ['deleted' => 0, 'errors' => []];

    $terms = get_terms([
        'taxonomy'   => 'qa_tags',
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms) || ! is_array($terms)) {
        return $out;
    }

    $ids = [];
    foreach ($terms as $term) {
        $ids[] = (int) $term->term_id;
    }

    foreach ($ids as $term_id) {
        if ($dry) {
            $out['deleted']++;
            continue;
        }
        $t = get_term($term_id, 'qa_tags');
        if (! $t || is_wp_error($t)) {
            continue;
        }
        $r = wp_delete_term($term_id, 'qa_tags');
        if (is_wp_error($r)) {
            $out['errors'][] = sprintf('%s (%s)', $t->name, $r->get_error_message());
            continue;
        }
        $out['deleted']++;
    }

    return $out;
}

/**
 * Create qa_tags terms from נושאי מפתח - correct.csv and set qa_knowledge_map_category.
 *
 * @param array{dry_run?: bool, path?: string} $args
 * @return array{created: int, errors: list<string>, term_ids_by_normalized_name: array<string, int>}
 */
function supervisor_qa_tags_insert_terms_from_csv($args = []) {
    $dry  = ! empty($args['dry_run']);
    $path = isset($args['path']) && $args['path'] !== ''
        ? $args['path']
        : supervisor_knowledge_map_csv_default_path();

    $out = [
        'created'                      => 0,
        'errors'                       => [],
        'term_ids_by_normalized_name'  => [],
    ];

    $rows = supervisor_knowledge_map_csv_read_rows($path);
    if ($rows === []) {
        $out['errors'][] = 'No rows in CSV or file not readable.';

        return $out;
    }

    $choices   = supervisor_knowledge_map_category_choices();
    $parent_map = supervisor_knowledge_map_csv_parent_column_for_leaf();

    foreach ($rows as $pair) {
        $topic      = $pair[0];
        $parent_csv = $pair[1];
        $icon_cell  = isset($pair[2]) ? (string) $pair[2] : '';
        $leaf       = supervisor_knowledge_map_csv_leaf_slug_for_topic_title($topic);
        if ($leaf === '' || ! isset($choices[ $leaf ])) {
            $out['errors'][] = 'No leaf for topic: ' . $topic;
            continue;
        }
        $expected_parent = $parent_map[ $leaf ] ?? '';
        if ($expected_parent === ''
            || supervisor_knowledge_map_automap_normalize_label($parent_csv)
                !== supervisor_knowledge_map_automap_normalize_label($expected_parent)) {
            $out['errors'][] = 'Parent mismatch for topic: ' . $topic;
            continue;
        }

        $norm = supervisor_knowledge_map_automap_normalize_label($topic);
        if (isset($out['term_ids_by_normalized_name'][ $norm ])) {
            continue;
        }

        if ($dry) {
            $out['created']++;
            $out['term_ids_by_normalized_name'][ $norm ] = 0;

            continue;
        }

        $ins = wp_insert_term(
            $topic,
            'qa_tags',
            [
                'description' => '',
            ]
        );

        if (is_wp_error($ins)) {
            $out['errors'][] = sprintf('%s: %s', $topic, $ins->get_error_message());
            continue;
        }

        $term_id = (int) $ins['term_id'];
        update_term_meta($term_id, 'qa_knowledge_map_category', $leaf);
        $fa_class = $icon_cell !== '' ? supervisor_knowledge_map_csv_icon_cell_to_fa_class($icon_cell) : '';
        if ($fa_class !== '') {
            update_term_meta($term_id, 'fa_icon', $fa_class);
        }
        $out['created']++;
        $out['term_ids_by_normalized_name'][ $norm ] = $term_id;
    }

    return $out;
}

/**
 * Apply exported term meta (icons, etc.) when the term name matches after CSV insert.
 *
 * @param array<string, mixed> $export
 * @param array<string, int>   $term_ids_by_normalized_name normalized topic => term_id
 * @param bool                 $exact_match_only If true, only exact Hebrew name matches (no automap to canonical leaf title).
 */
function supervisor_qa_tags_restore_term_meta_from_export($export, $term_ids_by_normalized_name, $dry_run = false, $exact_match_only = false) {
    $applied     = 0;
    $seen_target = [];

    foreach ($export['terms'] ?? [] as $row) {
        $name = isset($row['name']) ? (string) $row['name'] : '';
        if ($name === '') {
            continue;
        }

        $term_id = supervisor_qa_tags_resolve_export_tag_name_to_term_id($name, $term_ids_by_normalized_name, $exact_match_only);
        if ($term_id < 1) {
            continue;
        }

        $meta = $row['meta'] ?? [];
        if (! is_array($meta) || $meta === []) {
            continue;
        }

        foreach ($meta as $key => $value) {
            if ($key === 'qa_knowledge_map_category') {
                continue;
            }
            if (isset($seen_target[ $term_id ][ $key ])) {
                continue;
            }
            if (! $dry_run) {
                update_term_meta($term_id, $key, $value);
            }
            if (! isset($seen_target[ $term_id ])) {
                $seen_target[ $term_id ] = [];
            }
            $seen_target[ $term_id ][ $key ] = true;
            $applied++;
        }
    }

    return $applied;
}

/**
 * Re-assign qa_tags on posts using exported names (must match new term names).
 *
 * @param array<string, mixed> $export
 * @param array<string, int>   $term_ids_by_normalized_name
 * @param bool                 $exact_match_only If true, only exact Hebrew name matches (no automap).
 * @return array{posts: int, missing_names: list<string>}
 */
function supervisor_qa_tags_restore_post_assignments_from_export($export, $term_ids_by_normalized_name, $dry_run = false, $exact_match_only = false) {
    $missing = [];
    $posts   = 0;

    foreach ($export['posts'] ?? [] as $row) {
        $post_id = isset($row['id']) ? (int) $row['id'] : 0;
        if ($post_id < 1) {
            continue;
        }
        $names = $row['qa_tag_names'] ?? [];
        if (! is_array($names) || $names === []) {
            continue;
        }

        $term_ids = [];
        foreach ($names as $name) {
            if (supervisor_knowledge_map_automap_normalize_label((string) $name) === '') {
                continue;
            }
            $tid = supervisor_qa_tags_resolve_export_tag_name_to_term_id($name, $term_ids_by_normalized_name, $exact_match_only);
            if ($tid < 1) {
                $missing[] = (string) $name;
                continue;
            }
            $term_ids[] = $tid;
        }

        $term_ids = array_values(array_unique(array_filter($term_ids)));

        if ($term_ids === []) {
            continue;
        }

        if (! $dry_run) {
            wp_set_object_terms($post_id, $term_ids, 'qa_tags', false);
        }
        $posts++;
    }

    return [
        'posts'         => $posts,
        'missing_names' => array_values(array_unique($missing)),
    ];
}

if (defined('WP_CLI') && WP_CLI) {
    /**
     * Export qa_tags term list + post assignments (JSON).
     *
     * Optional snapshot before `wp supervisor reset-qa-tags-from-csv`: preserves post↔tag links and term meta for reassignment (unless you use `--no-assignments` on reset).
     *
     * ## OPTIONS
     *
     * [--file=<path>]
     * : Write JSON to this path. Relative paths are under the plugin directory. Default: qa-tags-export.json in the plugin root.
     *
     * [--stdout]
     * : Print JSON to STDOUT instead of writing the default file.
     */
    WP_CLI::add_command('supervisor export-qa-tags-state', function ($__, $assoc_args) {
        $data = supervisor_qa_tags_export_state();
        $json  = wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (! is_string($json)) {
            WP_CLI::error('JSON encode failed.');
        }

        if (WP_CLI\Utils\get_flag_value($assoc_args, 'stdout', false)) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            fwrite(STDOUT, $json . "\n");

            return;
        }

        $file = WP_CLI\Utils\get_flag_value($assoc_args, 'file', '');
        if ($file === '') {
            $file = supervisor_qa_tags_export_default_file_path();
        } elseif (! preg_match('#^/#', $file) && ! preg_match('#^[A-Za-z]:[/\\\\]#', $file)) {
            $file = PLUGIN_ROOT . ltrim($file, '/');
        }

        if (file_put_contents($file, $json) === false) {
            WP_CLI::error(sprintf('Could not write: %s', $file));
        }
        WP_CLI::success(sprintf(
            'Wrote %d posts with tags, %d terms → %s',
            count($data['posts']),
            count($data['terms']),
            $file
        ));
    });

    /**
     * Delete all qa_tags terms, recreate from נושאי מפתח - correct.csv, optionally restore meta and post links from export JSON.
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : Show counts only; no database writes.
     *
     * [--confirm]
     * : Required (with --dry-run omitted) to perform destructive steps.
     *
     * [--csv=<path>]
     * : CSV path (default: plugin נושאי מפתח - correct.csv).
     *
     * [--assignments=<path>]
     * : JSON from export (default if omitted and readable: qa-tags-export.json in the plugin root). Restores term meta and post qa_tags; legacy tag names are mapped via knowledge-map automap unless --exact-match-only.
     *
     * [--no-assignments]
     * : Do not read any JSON; recreate terms from CSV only. Posts will have no qa_tags after term deletion until you assign in admin, run `reapply-qa-tags-from-export`, or import.
     *
     * [--exact-match-only]
     * : When restoring from JSON, map export tag names to new terms by exact Hebrew title only (skip knowledge-map automap fallback).
     */
    WP_CLI::add_command('supervisor reset-qa-tags-from-csv', function ($__, $assoc_args) {
        $dry     = WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
        $confirm = WP_CLI\Utils\get_flag_value($assoc_args, 'confirm', false);
        if (! $dry && ! $confirm) {
            WP_CLI::error('Refusing to run without --confirm (or use --dry-run). This deletes all qa_tags terms.');
        }

        $csv = WP_CLI\Utils\get_flag_value($assoc_args, 'csv', '');
        if ($csv !== '' && ! preg_match('#^/#', $csv) && ! preg_match('#^[A-Za-z]:[/\\\\]#', $csv)) {
            $csv = PLUGIN_ROOT . ltrim($csv, '/');
        }
        $csv_path = $csv !== '' ? $csv : supervisor_knowledge_map_csv_default_path();
        if (! is_readable($csv_path)) {
            WP_CLI::error(sprintf('CSV not readable: %s', $csv_path));
        }

        $no_assignments = WP_CLI\Utils\get_flag_value($assoc_args, 'no-assignments', false);
        $exact_only     = WP_CLI\Utils\get_flag_value($assoc_args, 'exact-match-only', false);

        $export = null;
        if ($no_assignments) {
            WP_CLI::log('Skipping assignments JSON (--no-assignments): terms recreated from CSV only; re-link later with `wp supervisor reapply-qa-tags-from-export` or in admin.');
        } else {
            $assignments_flag_set = array_key_exists('assignments', $assoc_args);
            $assign_path           = $assignments_flag_set
                ? (string) WP_CLI\Utils\get_flag_value($assoc_args, 'assignments', '')
                : supervisor_qa_tags_export_default_file_path();

            if ($assign_path !== '' && ! preg_match('#^/#', $assign_path) && ! preg_match('#^[A-Za-z]:[/\\\\]#', $assign_path)) {
                $assign_path = PLUGIN_ROOT . ltrim($assign_path, '/');
            }

            if ($assign_path === '') {
                WP_CLI::warning('No assignments file (--assignments was empty). Posts will have no qa_tags until you assign manually.');
            } elseif (! is_readable($assign_path)) {
                if ($assignments_flag_set) {
                    WP_CLI::error(sprintf('Assignments JSON not readable: %s', $assign_path));
                }
                WP_CLI::warning(sprintf(
                    'Default export not found or not readable (%s). Run `wp supervisor export-qa-tags-state` first, or pass --assignments=.',
                    $assign_path
                ));
            } else {
                $raw = file_get_contents($assign_path);
                if ($raw === false) {
                    WP_CLI::error('Could not read assignments file.');
                }
                $export = json_decode($raw, true);
                if (! is_array($export) || ! isset($export['posts'], $export['terms'])) {
                    WP_CLI::error('Invalid assignments JSON (expected posts + terms arrays).');
                }
            }
        }

        $terms_before = get_terms(['taxonomy' => 'qa_tags', 'hide_empty' => false]);
        $n_before     = is_wp_error($terms_before) ? 0 : count($terms_before);

        if ($dry) {
            WP_CLI::log(sprintf('(dry run) Would delete %d qa_tags terms, then create from CSV.', $n_before));
        }

        $del = supervisor_qa_tags_delete_all_terms(['dry_run' => $dry]);
        if ($del['errors'] !== []) {
            foreach ($del['errors'] as $e) {
                WP_CLI::warning($e);
            }
        }

        $ins = supervisor_qa_tags_insert_terms_from_csv([
            'dry_run' => $dry,
            'path'    => $csv_path,
        ]);
        foreach ($ins['errors'] as $e) {
            WP_CLI::warning($e);
        }

        $map = $ins['term_ids_by_normalized_name'];

        if (is_array($export) && ! $dry) {
            $meta_writes = supervisor_qa_tags_restore_term_meta_from_export($export, $map, false, $exact_only);
            WP_CLI::log(sprintf('Term meta keys restored (excluding km category): %d', $meta_writes));

            $re = supervisor_qa_tags_restore_post_assignments_from_export($export, $map, false, $exact_only);
            WP_CLI::log(sprintf('Posts reassigned: %d', $re['posts']));
            if ($re['missing_names'] !== []) {
                $hint = $exact_only ? 'exact title match' : 'legacy automap';
                WP_CLI::warning('Tag names in export still unmatched (' . $hint . '): ' . implode(', ', array_slice($re['missing_names'], 0, 30))
                    . (count($re['missing_names']) > 30 ? ' …' : ''));
            }
        } elseif (is_array($export) && $dry) {
            WP_CLI::log('(dry run) Skipping term-meta and post reassignment.');
        }

        if (! $dry) {
            flush_rewrite_rules(false);
        }

        $suffix = $no_assignments ? ' | assignments: skipped (no JSON)' : '';
        WP_CLI::success(sprintf(
            'Terms deleted: %d | terms created: %d | dry-run: %s%s',
            $del['deleted'],
            $ins['created'],
            $dry ? 'yes' : 'no',
            $suffix
        ));
    });

    /**
     * Re-apply export JSON to existing qa_tags (no term delete). Fixes posts after a reset when legacy tag names differ from CSV titles.
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : Report only; no database writes.
     *
     * [--assignments=<path>]
     * : Export JSON (default: qa-tags-export.json in the plugin root).
     *
     * [--exact-match-only]
     * : Map export tag names to current terms by exact Hebrew title only (skip knowledge-map automap fallback).
     */
    WP_CLI::add_command('supervisor reapply-qa-tags-from-export', function ($__, $assoc_args) {
        $dry        = WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
        $exact_only = WP_CLI\Utils\get_flag_value($assoc_args, 'exact-match-only', false);

        $assignments_flag_set = array_key_exists('assignments', $assoc_args);
        $assign_path           = $assignments_flag_set
            ? (string) WP_CLI\Utils\get_flag_value($assoc_args, 'assignments', '')
            : supervisor_qa_tags_export_default_file_path();

        if ($assign_path !== '' && ! preg_match('#^/#', $assign_path) && ! preg_match('#^[A-Za-z]:[/\\\\]#', $assign_path)) {
            $assign_path = PLUGIN_ROOT . ltrim($assign_path, '/');
        }

        if ($assign_path === '' || ! is_readable($assign_path)) {
            WP_CLI::error(sprintf('Export JSON not readable: %s', $assign_path !== '' ? $assign_path : '(empty path)'));
        }

        $raw = file_get_contents($assign_path);
        if ($raw === false) {
            WP_CLI::error('Could not read export file.');
        }

        $export = json_decode($raw, true);
        if (! is_array($export) || ! isset($export['posts'], $export['terms'])) {
            WP_CLI::error('Invalid export JSON (expected posts + terms arrays).');
        }

        $map = supervisor_qa_tags_current_term_id_map_by_normalized_name();
        if ($map === []) {
            WP_CLI::error('No qa_tags terms found. Run reset from CSV or create terms first.');
        }

        if ($dry) {
            WP_CLI::log('(dry run) Would reapply term meta and post assignments from export.');
        }

        $meta_writes = supervisor_qa_tags_restore_term_meta_from_export($export, $map, $dry, $exact_only);
        WP_CLI::log(sprintf('Term meta keys %s: %d', $dry ? 'that would be written' : 'written', $meta_writes));

        $re = supervisor_qa_tags_restore_post_assignments_from_export($export, $map, $dry, $exact_only);
        WP_CLI::log(sprintf('Posts %s: %d', $dry ? 'that would get assignments' : 'updated', $re['posts']));
        if ($re['missing_names'] !== []) {
            WP_CLI::warning('Unmatched export tag names' . ($exact_only ? ' (exact match only)' : '') . ': ' . implode(', ', array_slice($re['missing_names'], 0, 30))
                . (count($re['missing_names']) > 30 ? ' …' : ''));
        }

        WP_CLI::success($dry ? 'Dry run complete.' : 'Reapply complete.');
    });
}
