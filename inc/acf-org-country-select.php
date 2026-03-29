<?php
/**
 * ACF: country dropdown for organisations (qa_country_code), backed by flag-icons country.json.
 */

if (! defined('ABSPATH')) {
    exit;
}

add_action('acf/init', 'supervisor_register_acf_org_country_code_field');

function supervisor_register_acf_org_country_code_field() {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'                   => 'group_supervisor_qa_org_country',
        'title'                 => __('מדינה (דגל)', 'text-domain'),
        'fields'                => [
            [
                'key'               => 'field_supervisor_qa_country_code',
                'label'             => __('Country (flag)', 'text-domain'),
                'name'              => 'qa_country_code',
                'type'              => 'select',
                'instructions'      => __('Select the country for the flag on the organisations listing. If empty, the legacy “Country” text field is still used to guess the flag.', 'text-domain'),
                'required'          => 0,
                'choices'           => supervisor_flag_country_acf_choices(),
                'default_value'     => '',
                'allow_null'        => 1,
                'multiple'          => 0,
                'ui'                => 1,
                'ajax'              => 0,
                'return_format'     => 'value',
            ],
        ],
        'location'              => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'qa_orgs',
                ],
            ],
        ],
        'menu_order'            => 5,
        'position'              => 'side',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
    ]);
}
