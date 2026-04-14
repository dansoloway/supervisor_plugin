<?php

// 1. Register Custom Post Types
function register_qa_cpts() {
    // CPT: ארגונים
    register_post_type('qa_orgs', [
        'labels' => [
            'name' => __('ארגונים', 'text-domain'),
            'singular_name' => __('ארגון', 'text-domain'),
            'add_new' => __('הוסף חדש', 'text-domain'),
            'add_new_item' => __('הוסף ארגון חדש', 'text-domain'),
            'edit_item' => __('ערוך ארגון', 'text-domain'),
            'new_item' => __('ארגון חדש', 'text-domain'),
            'view_item' => __('הצג ארגון', 'text-domain'),
            'search_items' => __('חפש ארגונים', 'text-domain'),
            'not_found' => __('לא נמצאו ארגונים', 'text-domain'),
            'not_found_in_trash' => __('לא נמצאו ארגונים באשפה', 'text-domain'),
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'qa-orgs'],
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

    // CPT: עדכונים
    register_post_type('qa_updates', [
        'labels' => [
            'name' => __('עדכונים', 'text-domain'),
            'singular_name' => __('עדכון', 'text-domain'),
            'add_new' => __('הוסף חדש', 'text-domain'),
            'add_new_item' => __('הוסף עדכון חדש', 'text-domain'),
            'edit_item' => __('ערוך עדכון', 'text-domain'),
            'new_item' => __('עדכון חדש', 'text-domain'),
            'view_item' => __('הצג עדכון', 'text-domain'),
            'search_items' => __('חפש עדכונים', 'text-domain'),
            'not_found' => __('לא נמצאו עדכונים', 'text-domain'),
            'not_found_in_trash' => __('לא נמצאו עדכונים באשפה', 'text-domain'),
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'qa-updates'],
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

    // CPT: ביבליוגרפיה
    register_post_type('qa_bib_items', [
        'labels' => [
            'name' => __('פריטים ביבליוגרפיים', 'text-domain'),
            'singular_name' => __('פריט ביבליוגרפי', 'text-domain'),
            'add_new' => __('הוסף חדש', 'text-domain'),
            'add_new_item' => __('הוסף פריט ביבליוגרפי חדש', 'text-domain'),
            'edit_item' => __('ערוך פריט ביבליוגרפי', 'text-domain'),
            'new_item' => __('פריט ביבליוגרפי חדש', 'text-domain'),
            'view_item' => __('הצג פריט ביבליוגרפי', 'text-domain'),
            'search_items' => __('חפש פריטים ביבליוגרפיים', 'text-domain'),
            'not_found' => __('לא נמצאו פריטים ביבליוגרפיים', 'text-domain'),
            'not_found_in_trash' => __('לא נמצאו פריטים ביבליוגרפיים באשפה', 'text-domain'),
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'qa-bib-items'],
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

    // CPT: סיפורים מהשטח
    register_post_type('qa_stories', [
        'labels' => [
            'name' => __('סיפורים מהשטח', 'text-domain'),
            'singular_name' => __('סיפור מהשטח', 'text-domain'),
            'add_new' => __('הוסף חדש', 'text-domain'),
            'add_new_item' => __('הוסף סיפור חדש', 'text-domain'),
            'edit_item' => __('ערוך סיפור', 'text-domain'),
            'new_item' => __('סיפור חדש', 'text-domain'),
            'view_item' => __('הצג סיפור', 'text-domain'),
            'search_items' => __('חפש סיפורים', 'text-domain'),
            'not_found' => __('לא נמצאו סיפורים', 'text-domain'),
            'not_found_in_trash' => __('לא נמצאו סיפורים באשפה', 'text-domain'),
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'qa-stories'],
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

}
add_action('init', 'register_qa_cpts');

add_filter('template_include', 'supervisor_load_templates');

function register_additional_taxonomies() {
    // Register "qa_themes"
    $themes_labels = [
        'name' => __('תחומים', 'text-domain'),
        'singular_name' => __('תחום', 'text-domain'),
        'search_items' => __('חפש תחומים', 'text-domain'),
        'all_items' => __('כל התחומים', 'text-domain'),
        'parent_item' => __('תחום אב', 'text-domain'),
        'parent_item_colon' => __('תחום אב:', 'text-domain'),
        'edit_item' => __('ערוך תחום', 'text-domain'),
        'update_item' => __('עדכן תחום', 'text-domain'),
        'add_new_item' => __('הוסף תחום חדש', 'text-domain'),
        'new_item_name' => __('שם תחום חדש', 'text-domain'),
        'menu_name' => __('תחומים', 'text-domain'),
    ];
    
    register_taxonomy('qa_themes', ['qa_orgs', 'qa_updates', 'qa_bib_items', 'qa_stories'], [
        'labels' => $themes_labels,
        'hierarchical' => true, // Enables hierarchical structure (like categories)
        'public' => true, // Allows taxonomy to be publicly queryable
        'show_ui' => true, // Shows taxonomy UI in the admin
        'show_in_nav_menus' => false, // Disable in navigation menus
        'show_in_rest' => true, // Enable for block editor and REST API
        'rewrite' => ['slug' => 'qa-themes'], // Rewrite slug
        'capabilities' => [
            'manage_terms' => 'manage_qa_themes',
            'edit_terms' => 'edit_qa_themes',
            'delete_terms' => 'delete_qa_themes',
            'assign_terms' => 'assign_qa_themes',
        ],
    ]);

    // Register "qa_tags"
    $tags_labels = [
        'name' => __('נושאי מפתח', 'text-domain'),
        'singular_name' => __('נושא מפתח', 'text-domain'),
        'search_items' => __('חפש נושאי מפתח', 'text-domain'),
        'all_items' => __('כל נושאי המפתח', 'text-domain'),
        'edit_item' => __('ערוך נושא מפתח', 'text-domain'),
        'update_item' => __('עדכן נושא מפתח', 'text-domain'),
        'add_new_item' => __('הוסף נושא מפתח חדש', 'text-domain'),
        'new_item_name' => __('שם נושא מפתח חדש', 'text-domain'),
        'menu_name' => __('נושאי מפתח', 'text-domain'),
    ];

    register_taxonomy('qa_tags', ['qa_orgs', 'qa_updates', 'qa_bib_items', 'qa_stories'], [
        'labels' => $tags_labels,
        'hierarchical' => true, // Non-hierarchical (like tags)
        'public' => true, // Allows taxonomy to be publicly queryable
        'show_ui' => true, // Shows taxonomy UI in the admin
        'show_in_rest' => true, // Enable for block editor and REST API
        'rewrite' => ['slug' => 'qa-tags'], // Rewrite slug
        'meta_box_cb' => 'supervisor_qa_tags_meta_box', // Use custom metabox with correct title
        'capabilities' => [
            'manage_terms' => 'manage_qa_tags',
            'edit_terms' => 'edit_qa_tags',
            'delete_terms' => 'delete_qa_tags',
            'assign_terms' => 'assign_qa_tags',
        ],
    ]);
    }
add_action('init', 'register_additional_taxonomies');

// Custom metabox callback for qa_tags to ensure correct title "נושאי מפתח"
function supervisor_qa_tags_meta_box($post, $box) {
    $taxonomy = 'qa_tags';
    $tax = get_taxonomy($taxonomy);
    
    // Ensure box title is set correctly
    if (!isset($box['args'])) {
        $box['args'] = ['taxonomy' => $taxonomy];
    }
    $box['args']['taxonomy'] = $taxonomy;
    $box['title'] = __('נושאי מפתח', 'text-domain');
    
    // Call the default categories meta box but with our custom title
    post_categories_meta_box($post, $box);
}

// Change taxonomy metabox title in post editor to show "נושאי מפתח" instead of "קטגוריות"
function supervisor_change_qa_tags_metabox_title() {
    global $wp_taxonomies;
    
    // Modify the taxonomy labels after registration to ensure metabox shows correct title
    if (isset($wp_taxonomies['qa_tags'])) {
        // Update labels to ensure proper display in editor
        $wp_taxonomies['qa_tags']->labels->name = __('נושאי מפתח', 'text-domain');
        $wp_taxonomies['qa_tags']->labels->singular_name = __('נושא מפתח', 'text-domain');
        $wp_taxonomies['qa_tags']->labels->menu_name = __('נושאי מפתח', 'text-domain');
    }
}
add_action('admin_init', 'supervisor_change_qa_tags_metabox_title', 1);

// Override metabox title using JavaScript for both classic and block editor
function supervisor_qa_tags_metabox_js() {
    $screen = get_current_screen();
    if (!$screen) {
        return;
    }
    
    // Only on post edit screens for relevant post types
    $relevant_types = ['qa_bib_items', 'qa_updates', 'qa_orgs', 'qa_stories'];
    if (in_array($screen->post_type, $relevant_types) && ($screen->base === 'post' || $screen->base === 'edit')) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            function updateMetaboxTitle() {
                // Classic Editor: Update metabox titles
                $('#qa_tagsdiv, #tagsdiv-qa_tags').each(function() {
                    var $metabox = $(this);
                    // Update the h2/handle title
                    $metabox.find('h2.hndle, .hndle h2, .postbox-header h2').each(function() {
                        var $title = $(this);
                        var text = $title.text().trim();
                        if (text.includes('קטגוריות') || text.includes('Categories') || text.includes('Category') || text === '') {
                            $title.text('נושאי מפתח');
                        }
                    });
                    
                    // Update any span inside hndle
                    $metabox.find('.hndle span').each(function() {
                        var text = $(this).text().trim();
                        if (text.includes('קטגוריות') || text.includes('Categories')) {
                            $(this).text('נושאי מפתח');
                        }
                    });
                });
                
                // Block Editor: Update sidebar panel titles
                $('[data-wp-block]').each(function() {
                    var $panel = $(this);
                    if ($panel.find('[data-name="qa_tags"]').length > 0 || $panel.attr('data-name') === 'qa_tags') {
                        $panel.find('.components-panel__header h2, .components-panel__body-title').each(function() {
                            var text = $(this).text().trim();
                            if (text.includes('קטגוריות') || text.includes('Categories')) {
                                $(this).text('נושאי מפתח');
                            }
                        });
                    }
                });
                
                // Also update labels
                $('label[for*="qa_tags"], label[for*="taxonomy-qa_tags"], .components-base-control__label').each(function() {
                    var $label = $(this);
                    var text = $label.text().trim();
                    if (text.includes('קטגוריות') || text.includes('Categories')) {
                        $label.text(text.replace(/קטגוריות|Categories/g, 'נושאי מפתח'));
                    }
                });
            }
            
            // Run multiple times to catch dynamically loaded content
            updateMetaboxTitle();
            setTimeout(updateMetaboxTitle, 300);
            setTimeout(updateMetaboxTitle, 1000);
            setTimeout(updateMetaboxTitle, 2000);
            
            // For block editor, observe DOM changes more aggressively
            if (typeof MutationObserver !== 'undefined') {
                var observer = new MutationObserver(function() {
                    updateMetaboxTitle();
                });
                observer.observe(document.body, { 
                    childList: true, 
                    subtree: true,
                    attributes: true,
                    attributeFilter: ['class']
                });
            }
            
            // Also listen for WordPress editor events
            $(document).on('DOMNodeInserted', function() {
                updateMetaboxTitle();
            });
        });
        </script>
        <?php
    }
}
add_action('admin_head', 'supervisor_qa_tags_metabox_js');

// qa_bib_cats taxonomy removed - functionality moved to qa_tags
// Explicitly unregister qa_bib_cats if it still exists (legacy cleanup)
function supervisor_unregister_old_qa_bib_cats() {
    global $wp_taxonomies;
    
    // Remove qa_bib_cats taxonomy if it exists
    if (isset($wp_taxonomies['qa_bib_cats'])) {
        unset($wp_taxonomies['qa_bib_cats']);
    }
}
add_action('init', 'supervisor_unregister_old_qa_bib_cats', 999); // Run late to ensure it removes after any registration

// Remove qa_bib_cats meta boxes from admin (must be in admin area - remove_meta_box only works in admin)
function supervisor_remove_qa_bib_cats_metaboxes() {
    // Only run in admin area where remove_meta_box is available
    if (!is_admin()) {
        return;
    }
    
    $post_types = ['qa_bib_items', 'qa_updates', 'qa_orgs', 'qa_stories'];
    foreach ($post_types as $post_type) {
        remove_meta_box('qa_bib_catsdiv', $post_type, 'side');
        remove_meta_box('tagsdiv-qa_bib_cats', $post_type, 'side');
        remove_meta_box('qa_bib_catsdiv', $post_type, 'normal');
        remove_meta_box('tagsdiv-qa_bib_cats', $post_type, 'normal');
    }
}
// Hook to admin-specific hooks only (remove_meta_box is not available on 'init')
add_action('admin_menu', 'supervisor_remove_qa_bib_cats_metaboxes');
add_action('add_meta_boxes', 'supervisor_remove_qa_bib_cats_metaboxes', 999);

// ===== FONT AWESOME ICON SUPPORT FOR TAXONOMY TERMS =====

// Add custom fields to taxonomy term edit form
function add_taxonomy_icon_field($term) {
    // Get the current icon value
    $icon = get_term_meta($term->term_id, 'fa_icon', true);
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="fa_icon">Font Awesome Icon</label>
        </th>
        <td>
            <input type="text" name="fa_icon" id="fa_icon" value="<?php echo esc_attr($icon); ?>" class="regular-text" />
            <p class="description">
                Enter Font Awesome icon class (e.g., <code>fas fa-book</code>, <code>fas fa-graduation-cap</code>). 
                <br>Leave empty to use default icon.
            </p>
            <div class="icon-preview" style="margin-top: 10px;">
                <?php if (!empty($icon)): ?>
                    <i class="<?php echo esc_attr($icon); ?>" style="font-size: 24px; color: #0073aa;"></i>
                    <span style="margin-left: 10px;"><?php echo esc_html($icon); ?></span>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php
}

// Add custom fields to taxonomy term add form
function add_taxonomy_icon_field_add() {
    ?>
    <div class="form-field">
        <label for="fa_icon">Font Awesome Icon</label>
        <input type="text" name="fa_icon" id="fa_icon" class="regular-text" />
        <p class="description">
            Enter Font Awesome icon class (e.g., <code>fas fa-book</code>, <code>fas fa-graduation-cap</code>). 
            <br>Leave empty to use default icon.
        </p>
    </div>
    <?php
}

// Save the custom field
function save_taxonomy_icon_field($term_id) {
    if (isset($_POST['fa_icon'])) {
        $icon = sanitize_text_field($_POST['fa_icon']);
        update_term_meta($term_id, 'fa_icon', $icon);
    }
}

/**
 * Knowledge-map hierarchy: 7 groups + sub-items (נושאי מפתח assign to leaves only).
 *
 * @return list<array{slug: string, label: string, items: list<array{slug: string, label: string}>}>
 */
function supervisor_knowledge_map_hierarchy() {
    $leaf  = 'supervisor_knowledge_map_hierarchy_leaf_item';
    $canon = supervisor_knowledge_map_canonical_leaf_labels();

    return [
        [
            'slug'  => 'policy',
            'label' => __('מדיניות', 'text-domain'),
            'items' => [
                $leaf('policy_supervision'),
                $leaf('policy_service_quality_standards'),
            ],
        ],
        [
            'slug'  => 'control',
            'label' => __('בקרה', 'text-domain'),
            'items' => [
                $leaf('control_external'),
                $leaf('control_self'),
            ],
        ],
        [
            'slug'  => 'enforcement',
            'label' => __('אכיפה', 'text-domain'),
            'items' => [
                $leaf('enforcement_corrective_punitive'),
            ],
        ],
        [
            'slug'  => 'knowledge_development',
            'label' => __('פיתוח ידע והדרכה', 'text-domain'),
            'items' => [
                $leaf('knowledge_training_materials'),
                $leaf('knowledge_research'),
            ],
        ],
        [
            'slug'  => 'working_methods',
            'label' => __('שיטות עבודה', 'text-domain'),
            'items' => [
                $leaf('wm_risk_management'),
                $leaf('wm_service_user_participation'),
                $leaf('wm_transparency_access'),
                $leaf('wm_integrated_supervision'),
                $leaf('wm_supervisor_supervisee_relations'),
            ],
        ],
        [
            'slug'  => 'social_procurement',
            'label' => __('רכש חברתי', 'text-domain'),
            'items' => [
                $leaf('sp_service_delivery_outsourcing'),
            ],
        ],
        [
            'slug'  => 'regulatory_welfare_state',
            'label' => $canon['regulatory_welfare_state'],
            'items' => [
                $leaf('regulatory_welfare_state'),
            ],
        ],
    ];
}

/**
 * Legacy / tile alias slugs → canonical leaf slug (see product notes: tile vs sub-item).
 *
 * @return array<string, string>
 */
function supervisor_knowledge_map_slug_aliases() {
    return [
        'guides_best_practices'        => 'knowledge_training_materials',
        'research'                     => 'knowledge_research',
        'enforcement_corrective'       => 'enforcement_corrective_punitive',
        'enforcement_punitive'         => 'enforcement_corrective_punitive',
        'standards_service_quality'    => 'policy_service_quality_standards',
        'standards_supervision_work'   => 'policy_supervision',
        'control_self'                 => 'control_self',
        'control_external'             => 'control_external',
        // Tiles / explicit routing (הפצת מידע וידע → שקיפות והנגשת מידע; מדיניות פיקוח על שירותים חברתיים → מדיניות פיקוח)
        'tile_info_dissemination'      => 'wm_transparency_access',
        'tile_policy_social_services'  => 'policy_supervision',
    ];
}

/**
 * @param string $slug Raw slug from URL or storage.
 */
function supervisor_knowledge_map_normalize_slug($slug) {
    $slug = sanitize_key($slug);
    if ($slug === '') {
        return '';
    }
    $aliases = supervisor_knowledge_map_slug_aliases();

    return isset($aliases[ $slug ]) ? $aliases[ $slug ] : $slug;
}

/**
 * @return array<string, string> Leaf slug => label (assignable on נושא מפתח).
 */
function supervisor_knowledge_map_category_choices() {
    $out = [];
    foreach (supervisor_knowledge_map_hierarchy() as $group) {
        foreach ($group['items'] as $item) {
            $out[ $item['slug'] ] = $item['label'];
        }
    }

    return $out;
}

/**
 * @param string $slug
 */
function supervisor_knowledge_map_is_parent_slug($slug) {
    $slug = sanitize_key($slug);
    foreach (supervisor_knowledge_map_hierarchy() as $group) {
        if ($group['slug'] === $slug) {
            return true;
        }
    }

    return false;
}

/**
 * @param string $slug Parent group slug.
 */
function supervisor_knowledge_map_parent_label($slug) {
    $slug = sanitize_key($slug);
    foreach (supervisor_knowledge_map_hierarchy() as $group) {
        if ($group['slug'] === $slug) {
            return $group['label'];
        }
    }

    return '';
}

/**
 * Leaf slugs under a parent group, or single-element array for a leaf slug.
 *
 * @param string $km_raw From query string (parent or leaf, may be legacy).
 *
 * @return list<string>
 */
function supervisor_knowledge_map_resolve_to_leaf_slugs($km_raw) {
    $km_raw = sanitize_key($km_raw);
    if ($km_raw === '') {
        return [];
    }

    if (supervisor_knowledge_map_is_parent_slug($km_raw)) {
        $slugs = [];
        foreach (supervisor_knowledge_map_hierarchy() as $group) {
            if ($group['slug'] === $km_raw) {
                foreach ($group['items'] as $item) {
                    $slugs[] = $item['slug'];
                }
                break;
            }
        }

        return $slugs;
    }

    $leaf = supervisor_knowledge_map_normalize_slug($km_raw);
    $flat = supervisor_knowledge_map_category_choices();
    if (isset($flat[ $leaf ])) {
        return [$leaf];
    }

    return [];
}

/**
 * Label for filter banner (parent group name or sub-item name).
 *
 * @param string $km_raw Query value before normalization.
 */
function supervisor_knowledge_map_filter_banner_label($km_raw) {
    $km_raw = sanitize_key($km_raw);
    if ($km_raw === '') {
        return '';
    }
    if (supervisor_knowledge_map_is_parent_slug($km_raw)) {
        return supervisor_knowledge_map_parent_label($km_raw);
    }
    $leaf = supervisor_knowledge_map_normalize_slug($km_raw);
    $flat = supervisor_knowledge_map_category_choices();
    if (isset($flat[ $leaf ])) {
        return $flat[ $leaf ];
    }

    return '';
}

/**
 * @param string $slug Normalized leaf or parent (for backwards calls).
 */
function supervisor_knowledge_map_category_label($slug) {
    $slug = supervisor_knowledge_map_normalize_slug($slug);
    if (supervisor_knowledge_map_is_parent_slug($slug)) {
        return supervisor_knowledge_map_parent_label($slug);
    }
    $choices = supervisor_knowledge_map_category_choices();

    return isset($choices[ $slug ]) ? $choices[ $slug ] : '';
}

/**
 * Front-end URL for the נושאי מפתח page filtered by knowledge-map group or leaf.
 *
 * @param string $slug Parent group slug OR leaf slug OR tile alias (normalized in resolve).
 */
function supervisor_knowledge_map_topics_url($slug) {
    if (! defined('SUPERVISOR_BIB_CATS') || ! SUPERVISOR_BIB_CATS) {
        return home_url('/');
    }
    $slug = sanitize_key($slug);
    if (! supervisor_knowledge_map_is_parent_slug($slug)) {
        $slug = supervisor_knowledge_map_normalize_slug($slug);
    }

    return add_query_arg('km_cat', $slug, get_permalink(SUPERVISOR_BIB_CATS));
}

/**
 * Resolve a Knowledge Map tile click target.
 *
 * - The 4 main areas link to the Key-Terms page filtered by `?km_cat=<area>`.
 * - "רכש חברתי" and "מדינת הרווחה הרגולטורית" link to their single Key-Term (qa_tags term) pages.
 * - "שיטות עבודה" is intentionally disabled (empty URL) for now.
 *
 * @param string $tile_slug Tile slug from templates (e.g. policy, social_procurement).
 * @return string URL or empty string when disabled.
 */
function supervisor_knowledge_map_tile_url($tile_slug) {
    $tile_slug = sanitize_key((string) $tile_slug);
    if ($tile_slug === '') {
        return '';
    }

    // Disabled for now (product decision: easy to enable later).
    if ($tile_slug === 'working_methods') {
        return '';
    }

    // Special-case tiles route to a single Key-Term (qa_tags term) page.
    if ($tile_slug === 'social_procurement') {
        return supervisor_knowledge_map_find_qa_tag_term_link_by_name_candidates([
            'רכש חברתי',
        ]) ?: supervisor_knowledge_map_topics_url($tile_slug);
    }
    if ($tile_slug === 'regulatory_welfare_state') {
        return supervisor_knowledge_map_find_qa_tag_term_link_by_name_candidates([
            'מדינת הרווחה הרגולטורית',
            'מדינת רווחה רגולטורית',
        ]) ?: supervisor_knowledge_map_topics_url($tile_slug);
    }

    // Default: filter the Key-Terms page (same behavior as before).
    return supervisor_knowledge_map_topics_url($tile_slug);
}

/**
 * Find a qa_tags term link by trying a list of exact Hebrew names.
 *
 * @param list<string> $names
 * @return string Empty when no term is found or link cannot be built.
 */
function supervisor_knowledge_map_find_qa_tag_term_link_by_name_candidates($names) {
    if (! taxonomy_exists('qa_tags')) {
        return '';
    }
    foreach ((array) $names as $name) {
        $name = wp_strip_all_tags((string) $name);
        $name = trim($name);
        if ($name === '') {
            continue;
        }
        $term = get_term_by('name', $name, 'qa_tags');
        if (! $term || is_wp_error($term)) {
            continue;
        }
        $link = get_term_link($term);
        if (! is_wp_error($link) && is_string($link) && $link !== '') {
            return $link;
        }
    }

    return '';
}

function add_qa_tags_knowledge_map_category_field($term) {
    $raw       = get_term_meta($term->term_id, 'qa_knowledge_map_category', true);
    $current   = $raw !== '' && $raw !== null ? sanitize_key($raw) : '';
    $flat      = supervisor_knowledge_map_category_choices();
    if ($current !== '' && ! isset($flat[ $current ])) {
        $current = supervisor_knowledge_map_normalize_slug($current);
    }
    if ($current !== '' && ! isset($flat[ $current ])) {
        $current = '';
    }
    $hierarchy = supervisor_knowledge_map_hierarchy();
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="qa_knowledge_map_category"><?php echo esc_html__('קטגוריית מפת הידע', 'text-domain'); ?></label>
        </th>
        <td>
            <select name="qa_knowledge_map_category" id="qa_knowledge_map_category">
                <option value=""><?php echo esc_html__('ללא (לא מוצג בסינון מהמפה)', 'text-domain'); ?></option>
                <?php foreach ($hierarchy as $group) : ?>
                    <optgroup label="<?php echo esc_attr($group['label']); ?>">
                        <?php foreach ($group['items'] as $item) : ?>
                            <option value="<?php echo esc_attr($item['slug']); ?>" <?php selected($current, $item['slug']); ?>><?php echo esc_html($item['label']); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php echo esc_html__('בחרו תת-נושא במפת הידע. סינון מהמפה או מקישור קבוצה מציג את כל נושאי המפתח באותה קבוצה.', 'text-domain'); ?></p>
        </td>
    </tr>
    <?php
}

function add_qa_tags_knowledge_map_category_field_add() {
    $hierarchy = supervisor_knowledge_map_hierarchy();
    ?>
    <div class="form-field">
        <label for="qa_knowledge_map_category"><?php echo esc_html__('קטגוריית מפת הידע', 'text-domain'); ?></label>
        <select name="qa_knowledge_map_category" id="qa_knowledge_map_category">
            <option value=""><?php echo esc_html__('ללא', 'text-domain'); ?></option>
            <?php foreach ($hierarchy as $group) : ?>
                <optgroup label="<?php echo esc_attr($group['label']); ?>">
                    <?php foreach ($group['items'] as $item) : ?>
                        <option value="<?php echo esc_attr($item['slug']); ?>"><?php echo esc_html($item['label']); ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php echo esc_html__('אופציונלי: שיוך לתת-נושא במפת הידע.', 'text-domain'); ?></p>
    </div>
    <?php
}

function save_qa_tags_knowledge_map_category($term_id) {
    if (! isset($_POST['qa_knowledge_map_category'])) {
        return;
    }
    $raw     = sanitize_key(wp_unslash($_POST['qa_knowledge_map_category']));
    $choices = supervisor_knowledge_map_category_choices();
    if ($raw === '' || array_key_exists($raw, $choices)) {
        if ($raw === '') {
            delete_term_meta($term_id, 'qa_knowledge_map_category');
        } else {
            update_term_meta($term_id, 'qa_knowledge_map_category', $raw);
        }
    }
}

// Add hooks for qa_tags taxonomy
add_action('qa_tags_edit_form_fields', 'add_taxonomy_icon_field', 10, 1);
add_action('qa_tags_edit_form_fields', 'add_qa_tags_knowledge_map_category_field', 15, 1);
add_action('qa_tags_add_form_fields', 'add_taxonomy_icon_field_add');
add_action('qa_tags_add_form_fields', 'add_qa_tags_knowledge_map_category_field_add');
add_action('edited_qa_tags', 'save_taxonomy_icon_field', 10, 1);
add_action('created_qa_tags', 'save_taxonomy_icon_field', 10, 1);
add_action('edited_qa_tags', 'save_qa_tags_knowledge_map_category', 15, 1);
add_action('created_qa_tags', 'save_qa_tags_knowledge_map_category', 15, 1);

// Helper function to get icon for a term
function get_term_fa_icon($term_id, $default_icon = 'fas fa-folder') {
    $icon = get_term_meta($term_id, 'fa_icon', true);
    return !empty($icon) ? $icon : $default_icon;
}

// Helper function to get icon for a term by term object
function get_term_fa_icon_by_term($term, $default_icon = 'fas fa-folder') {
    if (is_object($term) && isset($term->term_id)) {
        return get_term_fa_icon($term->term_id, $default_icon);
    }
    return $default_icon;
}

function add_default_themes_terms() {
    $terms = ['חינוך', 'רווחה', 'בריאות']; // Replace with your terms
    foreach ($terms as $term) {
        if (!term_exists($term, 'qa_themes')) {
            wp_insert_term($term, 'qa_themes');
        }
    }
}
register_activation_hook(__FILE__, 'add_default_themes_terms');

function display_taxonomies($post_id, $taxonomy_labels) {
    foreach ($taxonomy_labels as $taxonomy_slug => $label) {
        $terms = get_the_terms($post_id, $taxonomy_slug);

        echo '<p><strong>' . esc_html($label) . ':</strong> ';
        if ($terms && !is_wp_error($terms)) {
            $term_names = array_map(function($term) {
                return $term->name;
            }, $terms);
            echo implode(', ', array_map('esc_html', $term_names));
        } else {
            echo __('ללא', 'text-domain');
        }
        echo '</p>';
    }
}
