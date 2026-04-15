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
