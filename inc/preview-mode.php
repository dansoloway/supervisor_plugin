<?php
/**
 * Preview mode: restrict every supervisor URL to logged-in users until launch.
 *
 * Enable per environment in config.php:
 *     define('SUPERVISOR_PREVIEW_MODE', true);
 */

defined('ABSPATH') || exit;

function supervisor_preview_mode_enabled() {
    return defined('SUPERVISOR_PREVIEW_MODE') && SUPERVISOR_PREVIEW_MODE;
}

function supervisor_preview_mode_blocks_current_user() {
    return supervisor_preview_mode_enabled() && ! is_user_logged_in();
}

/**
 * @return list<int>
 */
function supervisor_preview_mode_page_ids() {
    $ids = [];
    foreach (array_keys(supervisor_supervisor_pages_registry()) as $constant) {
        if (defined($constant) && (int) constant($constant) > 0) {
            $ids[] = (int) constant($constant);
        }
    }

    return $ids;
}

/**
 * @return list<string>
 */
function supervisor_preview_mode_post_types() {
    return ['qa_orgs', 'qa_updates', 'qa_bib_items', 'qa_stories'];
}

/**
 * @return list<string>
 */
function supervisor_preview_mode_taxonomies() {
    return ['qa_tags', 'qa_themes'];
}

function supervisor_preview_mode_is_supervisor_request() {
    $page_ids = supervisor_preview_mode_page_ids();
    if ($page_ids && is_page($page_ids)) {
        return true;
    }

    $post_types = supervisor_preview_mode_post_types();

    return is_singular($post_types)
        || is_post_type_archive($post_types)
        || is_tax(supervisor_preview_mode_taxonomies())
        || get_query_var('supervisor_search')
        || get_query_var('custom_ajax');
}

function supervisor_preview_mode_gate_request() {
    if (! supervisor_preview_mode_blocks_current_user()) {
        return;
    }
    if (supervisor_preview_mode_is_supervisor_request()) {
        nocache_headers();
        auth_redirect();
    }
}
add_action('template_redirect', 'supervisor_preview_mode_gate_request', 0);

function supervisor_preview_mode_noindex($robots) {
    if (supervisor_preview_mode_enabled() && supervisor_preview_mode_is_supervisor_request()) {
        $robots['noindex']  = true;
        $robots['nofollow'] = true;
    }

    return $robots;
}
add_filter('wp_robots', 'supervisor_preview_mode_noindex');

function supervisor_preview_mode_gate_ajax() {
    if (supervisor_preview_mode_blocks_current_user()) {
        wp_send_json_error(['message' => 'login_required'], 403);
    }
}
add_action('wp_ajax_nopriv_supervisor_bib_cats_terms', 'supervisor_preview_mode_gate_ajax', 0);

/**
 * Hide supervisor pages and taxonomy terms from the REST API for logged-out visitors.
 */
function supervisor_preview_mode_gate_rest($result, $server, $request) {
    if (! supervisor_preview_mode_blocks_current_user()) {
        return $result;
    }

    $route = $request->get_route();

    foreach (supervisor_preview_mode_taxonomies() as $taxonomy) {
        if (strpos($route, '/wp/v2/' . $taxonomy) === 0) {
            return new WP_Error('rest_forbidden', 'Login required.', ['status' => 401]);
        }
    }

    if (preg_match('#^/wp/v2/pages/(\d+)#', $route, $m) && in_array((int) $m[1], supervisor_preview_mode_page_ids(), true)) {
        return new WP_Error('rest_forbidden', 'Login required.', ['status' => 401]);
    }

    return $result;
}
add_filter('rest_pre_dispatch', 'supervisor_preview_mode_gate_rest', 10, 3);

function supervisor_preview_mode_exclude_pages_from_rest($args) {
    if (supervisor_preview_mode_blocks_current_user()) {
        $exclude = isset($args['post__not_in']) ? (array) $args['post__not_in'] : [];
        $args['post__not_in'] = array_merge($exclude, supervisor_preview_mode_page_ids());
    }

    return $args;
}
add_filter('rest_page_query', 'supervisor_preview_mode_exclude_pages_from_rest');

function supervisor_preview_mode_admin_notice() {
    if (! supervisor_preview_mode_enabled() || ! current_user_can('manage_options')) {
        return;
    }
    echo '<div class="notice notice-warning"><p>'
        . esc_html__('Supervisor preview mode is ON: supervisor pages are visible to logged-in users only. Remove SUPERVISOR_PREVIEW_MODE from config.php to launch.', 'supervisor-plugin')
        . '</p></div>';
}
add_action('admin_notices', 'supervisor_preview_mode_admin_notice');
