<?php
/**
 * Supervisor Plugin Admin Menu
 * Consolidates all plugin functionality under a single menu
 */

// Main admin menu function
function supervisor_admin_menu() {
    // Add main menu page
    add_menu_page(
        __('המקפחת - ניהול מערכת', 'text-domain'), // Page title
        __('המקפחת', 'text-domain'), // Menu title
        'manage_options', // Capability
        'supervisor-admin', // Menu slug
        'supervisor_admin_dashboard', // Callback function
        'dashicons-admin-generic', // Icon
        25 // Position
    );

    // Add submenu pages
    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('דשבורד', 'text-domain'), // Page title
        __('דשבורד', 'text-domain'), // Menu title
        'manage_options', // Capability
        'supervisor-admin', // Menu slug (same as parent for first submenu)
        'supervisor_admin_dashboard' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ניהול ביבליוגרפיה', 'text-domain'), // Page title
        __('ניהול ביבליוגרפיה', 'text-domain'), // Menu title
        'manage_options', // Capability
        'supervisor-bibliography', // Menu slug
        'supervisor_bibliography_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ניהול עדכונים', 'text-domain'), // Page title
        __('ניהול עדכונים', 'text-domain'), // Menu title
        'manage_options', // Capability
        'supervisor-updates', // Menu slug
        'supervisor_updates_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ניהול ארגונים', 'text-domain'), // Page title
        __('ניהול ארגונים', 'text-domain'), // Menu title
        'manage_options', // Capability
        'supervisor-organizations', // Menu slug
        'supervisor_organizations_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ניהול קטגוריות', 'text-domain'), // Page title
        __('ניהול קטגוריות', 'text-domain'), // Menu title
        'manage_options', // Capability
        'supervisor-categories', // Menu slug
        'supervisor_categories_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('הגדרות', 'text-domain'), // Page title
        __('הגדרות', 'text-domain'), // Menu title
        'manage_options', // Capability
        'supervisor-settings', // Menu slug
        'supervisor_settings_page' // Callback function
    );
}
add_action('admin_menu', 'supervisor_admin_menu');

// Dashboard page callback
function supervisor_admin_dashboard() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('המקפחת - דשבורד', 'text-domain'); ?></h1>
        
        <div class="supervisor-dashboard-stats">
            <div class="stat-box">
                <h3><?php echo esc_html__('סטטיסטיקות כלליות', 'text-domain'); ?></h3>
                <ul>
                    <li><strong><?php echo esc_html__('פריטי ביבליוגרפיה:', 'text-domain'); ?></strong> <?php echo wp_count_posts('qa_bib_items')->publish; ?></li>
                    <li><strong><?php echo esc_html__('עדכונים:', 'text-domain'); ?></strong> <?php echo wp_count_posts('qa_updates')->publish; ?></li>
                    <li><strong><?php echo esc_html__('ארגונים:', 'text-domain'); ?></strong> <?php echo wp_count_posts('qa_orgs')->publish; ?></li>
                    <li><strong><?php echo esc_html__('קטגוריות:', 'text-domain'); ?></strong> <?php echo count(get_terms(['taxonomy' => 'qa_tags', 'hide_empty' => false])); ?></li>
                </ul>
            </div>
            
            <div class="quick-actions">
                <h3><?php echo esc_html__('פעולות מהירות', 'text-domain'); ?></h3>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=supervisor-bibliography'); ?>" class="button button-primary">
                        <?php echo esc_html__('ניהול ביבליוגרפיה', 'text-domain'); ?>
                    </a>
                    <a href="<?php echo admin_url('post-new.php?post_type=qa_updates'); ?>" class="button button-secondary">
                        <?php echo esc_html__('הוסף עדכון חדש', 'text-domain'); ?>
                    </a>
                    <a href="<?php echo admin_url('post-new.php?post_type=qa_orgs'); ?>" class="button button-secondary">
                        <?php echo esc_html__('הוסף ארגון חדש', 'text-domain'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
    <?php
}

// Bibliography management page callback
function supervisor_bibliography_page() {
    // Use the existing bibliography admin page function
    qa_bib_render_admin_page();
}

// Updates management page callback
function supervisor_updates_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ניהול עדכונים', 'text-domain'); ?></h1>
        <p><?php echo esc_html__('ניהול עדכונים במערכת המקפחת.', 'text-domain'); ?></p>
        
        <div class="updates-management">
            <h2><?php echo esc_html__('עדכונים אחרונים', 'text-domain'); ?></h2>
            <?php
            $updates = new WP_Query([
                'post_type' => 'qa_updates',
                'posts_per_page' => 10,
                'orderby' => 'date',
                'order' => 'DESC'
            ]);
            
            if ($updates->have_posts()) :
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__('כותרת', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('תאריך', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('פעולות', 'text-domain') . '</th>';
                echo '</tr></thead><tbody>';
                
                while ($updates->have_posts()) : $updates->the_post();
                    echo '<tr>';
                    echo '<td>' . esc_html(get_the_title()) . '</td>';
                    echo '<td>' . esc_html(get_the_date()) . '</td>';
                    echo '<td>';
                    echo '<a href="' . admin_url('post.php?post=' . get_the_ID() . '&action=edit') . '" class="button button-small">' . esc_html__('ערוך', 'text-domain') . '</a> ';
                    echo '<a href="' . get_permalink() . '" class="button button-small" target="_blank">' . esc_html__('צפה', 'text-domain') . '</a>';
                    echo '</td>';
                    echo '</tr>';
                endwhile;
                
                echo '</tbody></table>';
                wp_reset_postdata();
            else :
                echo '<p>' . esc_html__('לא נמצאו עדכונים.', 'text-domain') . '</p>';
            endif;
            ?>
            
            <p>
                <a href="<?php echo admin_url('post-new.php?post_type=qa_updates'); ?>" class="button button-primary">
                    <?php echo esc_html__('הוסף עדכון חדש', 'text-domain'); ?>
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=qa_updates'); ?>" class="button button-secondary">
                    <?php echo esc_html__('צפה בכל העדכונים', 'text-domain'); ?>
                </a>
            </p>
        </div>
    </div>
    <?php
}

// Organizations management page callback
function supervisor_organizations_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ניהול ארגונים', 'text-domain'); ?></h1>
        <p><?php echo esc_html__('ניהול ארגוני פיקוח במערכת המקפחת.', 'text-domain'); ?></p>
        
        <div class="organizations-management">
            <h2><?php echo esc_html__('ארגונים אחרונים', 'text-domain'); ?></h2>
            <?php
            $organizations = new WP_Query([
                'post_type' => 'qa_orgs',
                'posts_per_page' => 10,
                'orderby' => 'title',
                'order' => 'ASC'
            ]);
            
            if ($organizations->have_posts()) :
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__('שם הארגון', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('מדינה', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('תאריך', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('פעולות', 'text-domain') . '</th>';
                echo '</tr></thead><tbody>';
                
                while ($organizations->have_posts()) : $organizations->the_post();
                    $country = get_field('qa_country') ?: '';
                    echo '<tr>';
                    echo '<td>' . esc_html(get_the_title()) . '</td>';
                    echo '<td>' . esc_html($country) . '</td>';
                    echo '<td>' . esc_html(get_the_date()) . '</td>';
                    echo '<td>';
                    echo '<a href="' . admin_url('post.php?post=' . get_the_ID() . '&action=edit') . '" class="button button-small">' . esc_html__('ערוך', 'text-domain') . '</a> ';
                    echo '<a href="' . get_permalink() . '" class="button button-small" target="_blank">' . esc_html__('צפה', 'text-domain') . '</a>';
                    echo '</td>';
                    echo '</tr>';
                endwhile;
                
                echo '</tbody></table>';
                wp_reset_postdata();
            else :
                echo '<p>' . esc_html__('לא נמצאו ארגונים.', 'text-domain') . '</p>';
            endif;
            ?>
            
            <p>
                <a href="<?php echo admin_url('post-new.php?post_type=qa_orgs'); ?>" class="button button-primary">
                    <?php echo esc_html__('הוסף ארגון חדש', 'text-domain'); ?>
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=qa_orgs'); ?>" class="button button-secondary">
                    <?php echo esc_html__('צפה בכל הארגונים', 'text-domain'); ?>
                </a>
            </p>
        </div>
    </div>
    <?php
}

// Categories management page callback
function supervisor_categories_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ניהול קטגוריות', 'text-domain'); ?></h1>
        <p><?php echo esc_html__('ניהול קטגוריות ונושאי מפתח במערכת המקפחת.', 'text-domain'); ?></p>
        
        <div class="categories-management">
            <h2><?php echo esc_html__('נושאי מפתח (qa_tags)', 'text-domain'); ?></h2>
            <?php
            $tags = get_terms([
                'taxonomy' => 'qa_tags',
                'hide_empty' => false,
            ]);
            
            if (!empty($tags) && !is_wp_error($tags)) :
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__('שם הקטגוריה', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('איקון', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('מספר פריטים', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('פעולות', 'text-domain') . '</th>';
                echo '</tr></thead><tbody>';
                
                foreach ($tags as $tag) :
                    $icon = get_term_meta($tag->term_id, 'fa_icon', true);
                    $count = $tag->count;
                    echo '<tr>';
                    echo '<td>' . esc_html($tag->name) . '</td>';
                    echo '<td>';
                    if ($icon) {
                        echo '<i class="' . esc_attr($icon) . '"></i> ' . esc_html($icon);
                    } else {
                        echo esc_html__('ללא איקון', 'text-domain');
                    }
                    echo '</td>';
                    echo '<td>' . esc_html($count) . '</td>';
                    echo '<td>';
                    echo '<a href="' . admin_url('edit-tags.php?action=edit&taxonomy=qa_tags&tag_ID=' . $tag->term_id) . '" class="button button-small">' . esc_html__('ערוך', 'text-domain') . '</a>';
                    echo '</td>';
                    echo '</tr>';
                endforeach;
                
                echo '</tbody></table>';
            else :
                echo '<p>' . esc_html__('לא נמצאו קטגוריות.', 'text-domain') . '</p>';
            endif;
            ?>
            
            <p>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=qa_tags'); ?>" class="button button-primary">
                    <?php echo esc_html__('ניהול קטגוריות', 'text-domain'); ?>
                </a>
            </p>
        </div>
    </div>
    <?php
}

// Settings page callback
function supervisor_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('הגדרות המקפחת', 'text-domain'); ?></h1>
        <p><?php echo esc_html__('הגדרות כלליות למערכת המקפחת.', 'text-domain'); ?></p>
        
        <div class="supervisor-settings">
            <h2><?php echo esc_html__('מידע על המערכת', 'text-domain'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo esc_html__('גרסת המערכת', 'text-domain'); ?></th>
                    <td>1.0</td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('סוגי תוכן', 'text-domain'); ?></th>
                    <td>qa_updates, qa_orgs, qa_bib_items</td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('טקסונומיות', 'text-domain'); ?></th>
                    <td>qa_tags, qa_themes</td>
                </tr>
            </table>
            
            <h2><?php echo esc_html__('פעולות מערכת', 'text-domain'); ?></h2>
            <p>
                <a href="<?php echo admin_url('admin.php?page=supervisor-admin'); ?>" class="button button-primary">
                    <?php echo esc_html__('חזור לדשבורד', 'text-domain'); ?>
                </a>
            </p>
        </div>
    </div>
    <?php
}

// Remove the old standalone bibliography admin menu and any other unwanted menus
function remove_old_bibliography_menu() {
    remove_menu_page('qa_bib_manager');
    
    // Also remove any other potential unwanted menu items
    remove_menu_page('supervisor-category-icons'); // Remove if exists
    remove_menu_page('supervisor-category-manager'); // Remove if exists
    
    // Remove any submenu pages that might be duplicated
    remove_submenu_page('supervisor-admin', 'supervisor-category-icons');
    remove_submenu_page('supervisor-admin', 'supervisor-category-manager');
}
add_action('admin_menu', 'remove_old_bibliography_menu', 999); // High priority to run after the old menu is added
