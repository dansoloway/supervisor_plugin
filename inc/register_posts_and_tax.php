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
    
    register_taxonomy('qa_themes', ['qa_orgs', 'qa_updates', 'qa_bib_items'], [
        'labels' => $themes_labels,
        'hierarchical' => true, // Enables hierarchical structure (like categories)
        'public' => true, // Allows taxonomy to be publicly queryable
        'show_ui' => true, // Shows taxonomy UI in the admin
        'show_in_nav_menus' => false, // Disable in navigation menus
        'show_in_rest' => true, // Enable for block editor and REST API
        'rewrite' => ['slug' => 'qa-themes'], // Rewrite slug
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

    register_taxonomy('qa_tags', ['qa_orgs', 'qa_updates', 'qa_bib_items'], [
        'labels' => $tags_labels,
        'hierarchical' => true, // Non-hierarchical (like tags)
        'public' => true, // Allows taxonomy to be publicly queryable
        'show_ui' => true, // Shows taxonomy UI in the admin
        'show_in_rest' => true, // Enable for block editor and REST API
        'rewrite' => ['slug' => 'qa-tags'], // Rewrite slug
    ]);
    }
add_action('init', 'register_additional_taxonomies');

function register_qa_bib_cats_taxonomy() {
    register_taxonomy('qa_bib_cats', ['qa_orgs', 'qa_updates', 'qa_bib_items'], [
        'labels' => [
            'name' => __('קטגוריות ביבליוגרפיה', 'text-domain'),
            'singular_name' => __('קטגוריה ביבליוגרפיה', 'text-domain'),
            'search_items' => __('חפש קטגוריות ביבליוגרפיה', 'text-domain'),
            'all_items' => __('כל הקטגוריות', 'text-domain'),
            'parent_item' => __('קטגוריית אב', 'text-domain'),
            'parent_item_colon' => __('קטגוריית אב:', 'text-domain'),
            'edit_item' => __('ערוך קטגוריה', 'text-domain'),
            'update_item' => __('עדכן קטגוריה', 'text-domain'),
            'add_new_item' => __('הוסף קטגוריה חדשה', 'text-domain'),
            'new_item_name' => __('שם קטגוריה חדשה', 'text-domain'),
            'menu_name' => __('קטגוריות ביבליוגרפיה', 'text-domain'),
        ],
        'hierarchical' => false, // Allows parent-child structure
        'show_admin_column' => true, // Displays the taxonomy in the post list view
        'rewrite' => ['slug' => 'qa-bib-cats'], // Friendly URL slug
        'show_in_rest' => true, // Enables REST API and Gutenberg support
    ]);
}
add_action('init', 'register_qa_bib_cats_taxonomy');

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

// Add hooks for qa_bib_cats taxonomy
add_action('qa_bib_cats_edit_form_fields', 'add_taxonomy_icon_field', 10, 1);
add_action('qa_bib_cats_add_form_fields', 'add_taxonomy_icon_field_add');
add_action('edited_qa_bib_cats', 'save_taxonomy_icon_field', 10, 1);
add_action('created_qa_bib_cats', 'save_taxonomy_icon_field', 10, 1);

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
