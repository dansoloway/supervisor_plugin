<?php
/**
 * WP-CLI: idempotent supervisor pages + optional plugin activation.
 */

defined('ABSPATH') || exit;

if (! defined('WP_CLI') || ! WP_CLI) {
    return;
}

/**
 * Print define( 'SUPERVISOR_*', ID ); lines for config.php from current pages by registry slug.
 *
 * @return bool False if any slug was missing.
 */
function supervisor_wp_cli_print_config_defines() {
    $ok = true;
    foreach (supervisor_supervisor_pages_registry() as $constant => $row) {
        $page = get_page_by_path($row['slug']);
        if (! $page instanceof WP_Post || $page->post_type !== 'page' || $page->post_status === 'trash') {
            WP_CLI::warning(sprintf('%s: no published page for slug "%s"', $constant, $row['slug']));
            $ok = false;
            continue;
        }
        WP_CLI::log(sprintf("define('%s', %d);", $constant, (int) $page->ID));
    }

    return $ok;
}

WP_CLI::add_command(
    'supervisor bootstrap-pages',
    function ($__, $assoc_args) {
        require_once ABSPATH . 'wp-admin/includes/post.php';

        $dry_run              = WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
        $activate             = WP_CLI\Utils\get_flag_value($assoc_args, 'activate-plugin', false);
        $flush_rewrite        = ! WP_CLI\Utils\get_flag_value($assoc_args, 'no-flush-rewrites', false);
        $print_config_defines = WP_CLI\Utils\get_flag_value($assoc_args, 'print-config-defines', false);

        if ($activate) {
            if (! defined('SUPERVISOR_PLUGIN_BASENAME')) {
                WP_CLI::error('SUPERVISOR_PLUGIN_BASENAME is not defined; cannot activate plugin.');
            }
            if ($dry_run) {
                WP_CLI::log(sprintf('(dry-run) would run: wp plugin activate %s', SUPERVISOR_PLUGIN_BASENAME));
            } else {
                \WP_CLI::run_command([ 'plugin', 'activate', SUPERVISOR_PLUGIN_BASENAME ]);
            }
        }

        $rows = [];
        foreach (supervisor_supervisor_pages_registry() as $constant => $row) {
            $slug  = $row['slug'];
            $title = $row['title'];

            $existing = get_page_by_path($slug);
            if ($existing instanceof WP_Post && $existing->post_type === 'page') {
                if (! $dry_run && $existing->post_status !== 'trash' && $existing->post_title !== $title) {
                    wp_update_post(
                        [
                            'ID'         => $existing->ID,
                            'post_title' => $title,
                        ]
                    );
                }
                $rows[] = [ 'slug' => $slug, 'id' => (int) $existing->ID, 'action' => 'exists' ];
                continue;
            }

            if ($dry_run) {
                $rows[] = [ 'slug' => $slug, 'id' => null, 'action' => 'would-create' ];
                continue;
            }

            $post_id = wp_insert_post(
                [
                    'post_title'   => $title,
                    'post_name'    => $slug,
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_content' => '',
                ],
                true
            );

            if (is_wp_error($post_id)) {
                WP_CLI::warning(sprintf('%s: %s', $slug, $post_id->get_error_message()));
                $rows[] = [ 'slug' => $slug, 'id' => null, 'action' => 'error' ];
                continue;
            }

            $rows[] = [ 'slug' => $slug, 'id' => (int) $post_id, 'action' => 'created' ];
        }

        if ($flush_rewrite) {
            if ($dry_run) {
                WP_CLI::log('(dry-run) would flush rewrite rules');
            } else {
                flush_rewrite_rules(false);
            }
        }

        WP_CLI\Utils\format_items(
            'table',
            array_map(
                static function ($r) {
                    return [
                        'slug'   => $r['slug'],
                        'id'     => $r['id'] === null ? '-' : (string) $r['id'],
                        'action' => $r['action'],
                    ];
                },
                $rows
            ),
            [ 'slug', 'id', 'action' ]
        );

        if ($print_config_defines && ! $dry_run) {
            WP_CLI::log('');
            WP_CLI::log('// Paste into plugin config.php (uncomment or replace the template block):');
            supervisor_wp_cli_print_config_defines();
        } elseif ($print_config_defines && $dry_run) {
            WP_CLI::log('');
            WP_CLI::log('(dry-run) re-run without --dry-run to print define(...) lines after pages exist.');
        }

        WP_CLI::success($dry_run ? 'Dry run complete' : 'Bootstrap complete');
    },
    [
        'shortdesc' => 'Create or update supervisor pages by slug (idempotent).',
        'synopsis'  => [
            [
                'type'        => 'flag',
                'name'        => 'dry-run',
                'description' => 'Only print what would run.',
            ],
            [
                'type'        => 'flag',
                'name'        => 'activate-plugin',
                'description' => 'Run wp plugin activate for this plugin (requires SUPERVISOR_PLUGIN_BASENAME).',
            ],
            [
                'type'        => 'flag',
                'name'        => 'no-flush-rewrites',
                'description' => 'Skip flush_rewrite_rules after changes.',
            ],
            [
                'type'        => 'flag',
                'name'        => 'print-config-defines',
                'description' => 'After bootstrap, print define(...) lines for config.php to STDOUT.',
            ],
        ],
    ]
);

WP_CLI::add_command(
    'supervisor print-config-defines',
    function () {
        WP_CLI::log('// Paste into config.php:');
        if (! supervisor_wp_cli_print_config_defines()) {
            WP_CLI::error('One or more slugs missing; create pages or run wp supervisor bootstrap-pages first.');
        }
        WP_CLI::success('Done');
    },
    [
        'shortdesc' => 'Print define( \'SUPERVISOR_*\', … ) lines from current pages (by registry slug).',
    ]
);
