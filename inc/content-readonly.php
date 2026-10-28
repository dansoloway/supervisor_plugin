<?php
/**
 * Content read-only mode: block admin edits to supervisor content.
 *
 * Enable per environment in config.php (typically staging after go-live):
 *     define('SUPERVISOR_CONTENT_READONLY', true);
 *
 * WP-CLI is not blocked, so content ports and repairs still work.
 */

defined('ABSPATH') || exit;

function supervisor_content_readonly_enabled() {
    return defined('SUPERVISOR_CONTENT_READONLY') && SUPERVISOR_CONTENT_READONLY;
}

/**
 * Whether the current request should be denied write access.
 * CLI is exempt so `wp supervisor …` and `wp post update` still work.
 */
function supervisor_content_readonly_blocks_writes() {
    if (! supervisor_content_readonly_enabled()) {
        return false;
    }
    if (defined('WP_CLI') && WP_CLI) {
        return false;
    }

    return true;
}

/**
 * @return list<string>
 */
function supervisor_content_readonly_post_types() {
    return ['qa_orgs', 'qa_updates', 'qa_bib_items', 'qa_stories'];
}

/**
 * @return list<string>
 */
function supervisor_content_readonly_taxonomies() {
    return ['qa_tags', 'qa_themes'];
}

/**
 * @return list<int>
 */
function supervisor_content_readonly_page_ids() {
    $ids = [];
    if (! function_exists('supervisor_supervisor_pages_registry')) {
        return $ids;
    }
    foreach (array_keys(supervisor_supervisor_pages_registry()) as $constant) {
        if (defined($constant) && (int) constant($constant) > 0) {
            $ids[] = (int) constant($constant);
        }
    }

    return $ids;
}

function supervisor_content_readonly_is_supervisor_post($post_id) {
    $post_id = (int) $post_id;
    if ($post_id <= 0) {
        return false;
    }
    $type = get_post_type($post_id);
    if (in_array($type, supervisor_content_readonly_post_types(), true)) {
        return true;
    }
    if ($type === 'page' && in_array($post_id, supervisor_content_readonly_page_ids(), true)) {
        return true;
    }

    return false;
}

function supervisor_content_readonly_die() {
    wp_die(
        esc_html__('סביבת Staging לקריאה בלבד. אין לשמור שינויים בתוכן המפקחת כאן — ערכו באתר הייצור.', 'text-domain'),
        esc_html__('תוכן המפקחת נעול', 'text-domain'),
        ['response' => 403]
    );
}

function supervisor_content_readonly_admin_notice() {
    if (! supervisor_content_readonly_enabled() || ! is_admin()) {
        return;
    }
    echo '<div class="notice notice-warning"><p><strong>'
        . esc_html__('המפקחת — Staging לקריאה בלבד:', 'text-domain')
        . '</strong> '
        . esc_html__('לא ניתן לשמור שינויים בתוכן המפקחת בסביבה זו. ערכו באתר הייצור; שינויים שכבר בוצעו כאן יועברו בנפרד.', 'text-domain')
        . '</p></div>';
}
add_action('admin_notices', 'supervisor_content_readonly_admin_notice');

/**
 * Strip edit/delete/publish caps for supervisor CPTs and related taxonomies.
 */
function supervisor_content_readonly_user_has_cap($allcaps, $caps, $args, $user) {
    if (! supervisor_content_readonly_blocks_writes()) {
        return $allcaps;
    }

    $cap = isset($args[0]) ? $args[0] : '';
    if ($cap === '') {
        return $allcaps;
    }

    $blocked_exact = [
        'edit_qa_updates', 'edit_others_qa_updates', 'edit_published_qa_updates', 'publish_qa_updates',
        'delete_qa_updates', 'delete_others_qa_updates', 'delete_published_qa_updates',
        'edit_qa_orgs', 'edit_others_qa_orgs', 'edit_published_qa_orgs', 'publish_qa_orgs',
        'delete_qa_orgs', 'delete_others_qa_orgs', 'delete_published_qa_orgs',
        'edit_qa_bib_items', 'edit_others_qa_bib_items', 'edit_published_qa_bib_items', 'publish_qa_bib_items',
        'delete_qa_bib_items', 'delete_others_qa_bib_items', 'delete_published_qa_bib_items',
        'edit_qa_stories', 'edit_others_qa_stories', 'edit_published_qa_stories', 'publish_qa_stories',
        'delete_qa_stories', 'delete_others_qa_stories', 'delete_published_qa_stories',
        'manage_qa_tags', 'edit_qa_tags', 'delete_qa_tags', 'assign_qa_tags',
        'manage_qa_themes', 'edit_qa_themes', 'delete_qa_themes', 'assign_qa_themes',
        'supervisor_editor',
    ];
    if (in_array($cap, $blocked_exact, true)) {
        $allcaps[ $cap ] = false;
        return $allcaps;
    }

    // Core caps when targeting a supervisor post/page.
    $meta_caps = ['edit_post', 'delete_post', 'publish_post', 'edit_page', 'delete_page'];
    if (in_array($cap, $meta_caps, true) && ! empty($args[2]) && supervisor_content_readonly_is_supervisor_post((int) $args[2])) {
        foreach ((array) $caps as $required) {
            $allcaps[ $required ] = false;
        }
    }

    return $allcaps;
}
add_filter('user_has_cap', 'supervisor_content_readonly_user_has_cap', 20, 4);

function supervisor_content_readonly_block_post_save($data, $postarr) {
    if (! supervisor_content_readonly_blocks_writes()) {
        return $data;
    }

    $type = isset($data['post_type']) ? $data['post_type'] : '';
    if (in_array($type, supervisor_content_readonly_post_types(), true)) {
        supervisor_content_readonly_die();
    }

    $id = isset($postarr['ID']) ? (int) $postarr['ID'] : 0;
    if ($type === 'page' && $id > 0 && in_array($id, supervisor_content_readonly_page_ids(), true)) {
        supervisor_content_readonly_die();
    }

    // New page with a supervisor slug.
    if ($type === 'page' && function_exists('supervisor_supervisor_pages_registry')) {
        $name = isset($data['post_name']) ? $data['post_name'] : '';
        foreach (supervisor_supervisor_pages_registry() as $row) {
            if ($name !== '' && $name === $row['slug']) {
                supervisor_content_readonly_die();
            }
        }
    }

    return $data;
}
add_filter('wp_insert_post_data', 'supervisor_content_readonly_block_post_save', 1, 2);

function supervisor_content_readonly_block_before_delete($post_id) {
    if (supervisor_content_readonly_blocks_writes() && supervisor_content_readonly_is_supervisor_post($post_id)) {
        supervisor_content_readonly_die();
    }
}
add_action('before_delete_post', 'supervisor_content_readonly_block_before_delete', 1);
add_action('wp_trash_post', 'supervisor_content_readonly_block_before_delete', 1);

function supervisor_content_readonly_block_term($term, $taxonomy) {
    if (supervisor_content_readonly_blocks_writes() && in_array($taxonomy, supervisor_content_readonly_taxonomies(), true)) {
        return new WP_Error('supervisor_readonly', __('סביבת Staging לקריאה בלבד. אין לשנות מונחי המפקחת כאן.', 'text-domain'));
    }

    return $term;
}
add_filter('pre_insert_term', 'supervisor_content_readonly_block_term', 1, 2);

function supervisor_content_readonly_block_delete_term($term, $tt_id, $taxonomy) {
    if (supervisor_content_readonly_blocks_writes() && in_array($taxonomy, supervisor_content_readonly_taxonomies(), true)) {
        supervisor_content_readonly_die();
    }
}
add_action('pre_delete_term', 'supervisor_content_readonly_block_delete_term', 1, 3);

function supervisor_content_readonly_block_update_term($data, $term_id, $taxonomy) {
    if (supervisor_content_readonly_blocks_writes() && in_array($taxonomy, supervisor_content_readonly_taxonomies(), true)) {
        supervisor_content_readonly_die();
    }

    return $data;
}
add_filter('wp_update_term_data', 'supervisor_content_readonly_block_update_term', 1, 3);

/**
 * Block ACF option / field updates when they belong to supervisor settings keys
 * or are saved against a supervisor post.
 */
function supervisor_content_readonly_block_acf_update($value, $post_id, $field) {
    if (! supervisor_content_readonly_blocks_writes()) {
        return $value;
    }

    $supervisor_options = [
        'supervisor_contact_name',
        'supervisor_contact_email',
        'supervisor_km_text_before',
        'supervisor_km_text_after',
    ];
    if (isset($field['name']) && in_array($field['name'], $supervisor_options, true)) {
        supervisor_content_readonly_die();
    }

    if (is_numeric($post_id) && supervisor_content_readonly_is_supervisor_post((int) $post_id)) {
        supervisor_content_readonly_die();
    }

    return $value;
}
add_filter('acf/update_value', 'supervisor_content_readonly_block_acf_update', 1, 3);
