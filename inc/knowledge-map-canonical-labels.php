<?php
/**
 * Canonical Hebrew titles for נושאי מפתח (knowledge-map leaf cards) — design screenshot source of truth.
 */

defined('ABSPATH') || exit;

/**
 * Leaf slug => exact card title (14 leaves).
 *
 * @return array<string, string>
 */
function supervisor_knowledge_map_canonical_leaf_labels() {
    static $map = null;
    if ($map !== null) {
        return $map;
    }

    $map = [
        'policy_supervision'              => 'מדיניות פיקוח',
        'policy_service_quality_standards'=> 'סטנדרטים לאיכות השירותים',
        'control_external'                => 'בקרה חיצונית',
        'control_self'                    => 'בקרה עצמית',
        'enforcement_corrective_punitive' => 'אכיפה מתקנת ואכיפה עונשית',
        'knowledge_training_materials'    => 'חומרי הדרכה',
        'knowledge_research'             => 'מחקרים',
        'wm_risk_management'             => 'ניהול סיכונים',
        'wm_service_user_participation'    => 'שיתוף מקבלי השירות בפיקוח',
        'wm_transparency_access'         => 'שקיפות והנגשת מידע',
        'wm_integrated_supervision'      => 'פיקוח משולב',
        'wm_supervisor_supervisee_relations' => 'יחסי מפקחים מפוקחים',
        'sp_service_delivery_outsourcing' => 'רכש חברתי',
        'regulatory_welfare_state'       => 'מדינת הרווחה הרגולטורית',
    ];

    return $map;
}

/**
 * Default Font Awesome class per knowledge-map leaf (team table: שם האייקון → fa-solid).
 * Used when qa_tags has no fa_icon term meta; full classes match Font Awesome 6 naming.
 *
 * @return array<string, string> Leaf slug => CSS classes
 */
function supervisor_knowledge_map_leaf_default_fa_icons() {
    return [
        'policy_supervision'               => 'fa-solid fa-glasses',
        'policy_service_quality_standards' => 'fa-solid fa-award',
        'control_external'                 => 'fa-solid fa-ruler',
        'control_self'                     => 'fa-solid fa-compass',
        'enforcement_corrective_punitive'  => 'fa-solid fa-shield-halved',
        'knowledge_research'               => 'fa-solid fa-copy',
        'knowledge_training_materials'     => 'fa-solid fa-paper-plane',
        'wm_risk_management'               => 'fa-solid fa-traffic-light',
        'wm_service_user_participation'    => 'fa-solid fa-user-friends',
        'wm_transparency_access'           => 'fa-solid fa-code-branch',
        'wm_integrated_supervision'        => 'fa-solid fa-link',
        'wm_supervisor_supervisee_relations' => 'fa-solid fa-hands-helping',
        'sp_service_delivery_outsourcing'  => 'fa-solid fa-square-up-right',
        'regulatory_welfare_state'         => 'fa-solid fa-archway',
    ];
}

/**
 * @param string $slug Leaf slug (qa_knowledge_map_category value).
 */
function supervisor_knowledge_map_leaf_default_fa_icon($slug) {
    $slug = sanitize_key((string) $slug);
    if ($slug === '') {
        return '';
    }
    $slug = function_exists('supervisor_knowledge_map_normalize_slug')
        ? supervisor_knowledge_map_normalize_slug($slug)
        : $slug;
    $map = supervisor_knowledge_map_leaf_default_fa_icons();

    return isset($map[ $slug ]) ? $map[ $slug ] : '';
}

/**
 * @param string $slug Leaf slug.
 *
 * @return array{slug: string, label: string}
 */
function supervisor_knowledge_map_hierarchy_leaf_item($slug) {
    $slug  = sanitize_key($slug);
    $canon = supervisor_knowledge_map_canonical_leaf_labels();

    return [
        'slug'  => $slug,
        'label' => isset($canon[ $slug ]) ? $canon[ $slug ] : $slug,
    ];
}
