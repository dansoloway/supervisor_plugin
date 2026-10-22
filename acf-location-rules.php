<?php
/**
 * ACF Custom Location Rules for Plugin Templates
 * Uses plugin page ID mapping for templates loaded via template_include.
 */

add_filter('acf/location/rule_types', 'add_supervisor_template_location_rule');
add_filter('acf/location/rule_values/supervisor_template', 'add_supervisor_template_location_rule_values');
add_filter('acf/location/rule_match/supervisor_template', 'add_supervisor_template_location_rule_match', 10, 3);

function add_supervisor_template_location_rule($choices) {
    $choices['Page']['supervisor_template'] = 'Supervisor Template';
    return $choices;
}

function add_supervisor_template_location_rule_values($choices) {
    $choices['supervisor-home'] = 'Supervisor Home';
    $choices['supervisor-about'] = 'Supervisor About';
    $choices['supervisor-activities'] = 'Supervisor Activities';
    $choices['supervisor-knowledge-map'] = 'Supervisor Knowledge Map';
    return $choices;
}

/**
 * Resolves the plugin template the same way supervisor_load_template() does
 * (SUPERVISOR_* page ID, then registry slug), so field groups follow the page
 * across environments where IDs differ.
 */
function add_supervisor_template_location_rule_match($match, $rule, $options) {
    $post_id = isset($options['post_id']) ? (int) $options['post_id'] : 0;
    if ($post_id <= 0) {
        global $post;
        $post_id = $post ? (int) $post->ID : 0;
    }
    if ($post_id <= 0 || get_post_type($post_id) !== 'page') {
        return false;
    }

    $effective_template = get_page_template_slug($post_id);
    if (! $effective_template) {
        $basename = supervisor_resolve_page_template_basename($post_id);
        $effective_template = $basename ? basename($basename, '.php') : '';
    }

    if ($rule['operator'] == '==') {
        $match = ($effective_template === $rule['value']);
    } elseif ($rule['operator'] == '!=') {
        $match = ($effective_template !== $rule['value']);
    } else {
        $match = false;
    }

    return $match;
}
