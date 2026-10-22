<?php
/**
 * WP-CLI: move supervisor page ACF values between environments and repair ACF field references.
 */

defined('ABSPATH') || exit;

if (! defined('WP_CLI') || ! WP_CLI) {
    return;
}

/**
 * @return array<string, int> Registry constant => page ID (resolved pages only).
 */
function supervisor_cli_resolved_page_ids() {
    $out = [];
    foreach (array_keys(supervisor_supervisor_pages_registry()) as $constant) {
        $id = defined($constant) ? (int) constant($constant) : 0;
        if ($id > 0 && get_post_type($id) === 'page') {
            $out[ $constant ] = $id;
        }
    }

    return $out;
}

WP_CLI::add_command(
    'supervisor export-page-fields',
    function ($args, $assoc_args) {
        if (! function_exists('get_field_objects')) {
            WP_CLI::error('ACF is not active.');
        }

        $export = [];
        foreach (supervisor_cli_resolved_page_ids() as $constant => $page_id) {
            $fields = get_field_objects($page_id, false);
            if (empty($fields)) {
                continue;
            }
            foreach ($fields as $field) {
                $value = get_field($field['key'], $page_id, false);
                if ($value === null || $value === '' || $value === []) {
                    continue;
                }
                $export[ $constant ][ $field['key'] ] = [
                    'name'  => $field['name'],
                    'value' => $value,
                ];
            }
            if (! empty($export[ $constant ])) {
                WP_CLI::log(sprintf('%s (page %d): %d field(s)', $constant, $page_id, count($export[ $constant ])));
            }
        }

        $json = wp_json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $file = WP_CLI\Utils\get_flag_value($assoc_args, 'file', '');
        if ($file === '') {
            WP_CLI::line($json);
            return;
        }
        if (file_put_contents($file, $json) === false) {
            WP_CLI::error("Could not write $file");
        }
        WP_CLI::success("Wrote $file");
    },
    [
        'shortdesc' => 'Export ACF values of supervisor pages, keyed by SUPERVISOR_* constant (not page ID).',
        'synopsis'  => [
            [
                'type'        => 'assoc',
                'name'        => 'file',
                'optional'    => true,
                'description' => 'Write JSON here instead of STDOUT.',
            ],
        ],
    ]
);

WP_CLI::add_command(
    'supervisor import-page-fields',
    function ($args, $assoc_args) {
        if (! function_exists('update_field')) {
            WP_CLI::error('ACF is not active.');
        }

        $data = json_decode((string) file_get_contents($args[0]), true);
        if (! is_array($data)) {
            WP_CLI::error('Could not read JSON from ' . $args[0]);
        }

        $dry_run   = WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
        $overwrite = WP_CLI\Utils\get_flag_value($assoc_args, 'overwrite', false);
        $pages     = supervisor_cli_resolved_page_ids();

        foreach ($data as $constant => $fields) {
            if (! isset($pages[ $constant ])) {
                WP_CLI::warning("$constant: no page on this site, skipped");
                continue;
            }
            $page_id = $pages[ $constant ];

            foreach ($fields as $field_key => $row) {
                if (! acf_get_field($field_key)) {
                    WP_CLI::warning("$constant: field {$row['name']} ($field_key) is not registered here, skipped");
                    continue;
                }
                $current = get_field($field_key, $page_id, false);
                if (! empty($current) && ! $overwrite) {
                    WP_CLI::log("$constant: {$row['name']} already has a value, skipped (use --overwrite)");
                    continue;
                }
                if ($dry_run) {
                    WP_CLI::log("(dry-run) $constant: would set {$row['name']} on page $page_id");
                    continue;
                }
                update_field($field_key, $row['value'], $page_id);
                WP_CLI::log("$constant: set {$row['name']} on page $page_id");
            }
        }

        WP_CLI::success($dry_run ? 'Dry run complete' : 'Import complete');
    },
    [
        'shortdesc' => 'Import JSON from export-page-fields into this site\'s supervisor pages.',
        'synopsis'  => [
            [
                'type'        => 'positional',
                'name'        => 'file',
                'description' => 'JSON file produced by wp supervisor export-page-fields.',
            ],
            [
                'type'        => 'flag',
                'name'        => 'dry-run',
                'description' => 'Only print what would change.',
            ],
            [
                'type'        => 'flag',
                'name'        => 'overwrite',
                'description' => 'Replace fields that already have a value.',
            ],
        ],
    ]
);

WP_CLI::add_command(
    'supervisor repair-acf-references',
    function ($args, $assoc_args) {
        if (! function_exists('acf_get_field_groups')) {
            WP_CLI::error('ACF is not active.');
        }

        $dry_run = WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
        $fixed   = 0;

        foreach (acf_get_field_groups() as $group) {
            $post_types = [];
            foreach ((array) $group['location'] as $and_rules) {
                foreach ($and_rules as $rule) {
                    if ($rule['param'] === 'post_type' && $rule['operator'] === '==' && strpos($rule['value'], 'qa_') === 0) {
                        $post_types[] = $rule['value'];
                    }
                }
            }
            if (! $post_types) {
                continue;
            }

            $post_ids = get_posts([
                'post_type'      => $post_types,
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ]);

            foreach (acf_get_fields($group) as $field) {
                foreach ($post_ids as $post_id) {
                    if (! metadata_exists('post', $post_id, $field['name'])) {
                        continue;
                    }
                    if (get_post_meta($post_id, '_' . $field['name'], true) === $field['key']) {
                        continue;
                    }
                    $fixed++;
                    if (! $dry_run) {
                        update_post_meta($post_id, '_' . $field['name'], $field['key']);
                    }
                }
            }
        }

        WP_CLI::success(sprintf('%s %d reference(s)', $dry_run ? '(dry-run) Would fix' : 'Fixed', $fixed));
    },
    [
        'shortdesc' => 'Point _<field_name> meta on qa_* posts at the registered ACF field keys.',
        'synopsis'  => [
            [
                'type'        => 'flag',
                'name'        => 'dry-run',
                'description' => 'Only count references that would change.',
            ],
        ],
    ]
);
