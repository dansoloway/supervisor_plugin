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
 * Page ID => template slug for plugin-forced templates.
 * Must match supervisor_load_template() in supervisor-plugin.php.
 */
function supervisor_acf_template_page_ids() {
    return [
        'supervisor-home' => defined('SUPERVISOR_HOME') ? SUPERVISOR_HOME : null,
        'supervisor-about' => defined('SUPERVISOR_ABOUT') ? SUPERVISOR_ABOUT : null,
        'supervisor-activities' => defined('SUPERVISOR_ACTIVITIES') ? SUPERVISOR_ACTIVITIES : null,
        'supervisor-knowledge-map' => defined('SUPERVISOR_KNOWLEDGE_MAP') ? SUPERVISOR_KNOWLEDGE_MAP : null,
    ];
}

function add_supervisor_template_location_rule_match($match, $rule, $options) {
    global $post;

    if (!$post) {
        return false;
    }

    $template = get_page_template_slug($post->ID);
    $page_ids = supervisor_acf_template_page_ids();
    $page_id_str = (string) $post->ID;

    // Plugin-forced templates: match by page ID when slug is in our mapping
    $template_from_page_id = null;
    foreach ($page_ids as $slug => $id) {
        if ($id && (string) $id === $page_id_str) {
            $template_from_page_id = $slug;
            break;
        }
    }

    $effective_template = $template ?: $template_from_page_id;

    if ($rule['operator'] == '==') {
        $match = ($effective_template === $rule['value']);
    } elseif ($rule['operator'] == '!=') {
        $match = ($effective_template !== $rule['value']);
    } else {
        $match = false;
    }

    return $match;
}
