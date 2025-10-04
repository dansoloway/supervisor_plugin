<?php
/**
 * ACF Custom Location Rules for Plugin Templates
 * Add this to your main plugin file or include it
 */

// Add custom location rule for supervisor-activities template
add_filter('acf/location/rule_types', 'add_supervisor_template_location_rule');
add_filter('acf/location/rule_values/supervisor_template', 'add_supervisor_template_location_rule_values');
add_filter('acf/location/rule_match/supervisor_template', 'add_supervisor_template_location_rule_match', 10, 3);

function add_supervisor_template_location_rule($choices) {
    $choices['Page']['supervisor_template'] = 'Supervisor Template';
    return $choices;
}

function add_supervisor_template_location_rule_values($choices) {
    $choices['supervisor-activities'] = 'Supervisor Activities';
    $choices['supervisor-knowledge-map'] = 'Supervisor Knowledge Map';
    $choices['supervisor-home'] = 'Supervisor Home';
    return $choices;
}

function add_supervisor_template_location_rule_match($match, $rule, $options) {
    global $post;
    
    if (!$post) {
        return false;
    }
    
    // Check if this page uses a supervisor template
    $template = get_page_template_slug($post->ID);
    
    if ($rule['operator'] == '==') {
        $match = ($template == $rule['value']);
    } elseif ($rule['operator'] == '!=') {
        $match = ($template != $rule['value']);
    }
    
    return $match;
}
