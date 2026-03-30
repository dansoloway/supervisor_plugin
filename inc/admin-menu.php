<?php
/**
 * Supervisor Plugin Admin Menu
 * Consolidates all plugin functionality under a single menu
 */

// Main admin menu function
function supervisor_admin_menu() {
    // Use 'read' capability so menu shows for all users, then check permissions in callbacks
    $capability = 'read';
    
    // Add main menu page
    add_menu_page(
        __('המפקחת - ניהול מערכת', 'text-domain'), // Page title
        __('המפקחת', 'text-domain'), // Menu title
        $capability, // Capability - allow supervisor editors
        'supervisor-admin', // Menu slug
        'supervisor_admin_dashboard', // Callback function
        'dashicons-book-alt', // Icon - more interesting icon for quality control
        25 // Position
    );

    // Add submenu pages
    // First item: Visit homepage (links to frontend)
    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ביקור באתר', 'text-domain'), // Page title
        __('ביקור באתר', 'text-domain'), // Menu title
        $capability, // Capability
        'supervisor-visit-homepage', // Menu slug
        'supervisor_visit_homepage_redirect' // Callback function
    );
    
    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('דשבורד', 'text-domain'), // Page title
        __('דשבורד', 'text-domain'), // Menu title
        $capability, // Capability - allow supervisor editors
        'supervisor-admin', // Menu slug (same as parent for first submenu)
        'supervisor_admin_dashboard' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ניהול ביבליוגרפיה', 'text-domain'), // Page title
        __('ניהול ביבליוגרפיה', 'text-domain'), // Menu title
        $capability, // Capability - visible to all, but editing restricted
        'supervisor-bibliography', // Menu slug
        'supervisor_bibliography_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ניהול עדכונים', 'text-domain'), // Page title
        __('ניהול עדכונים', 'text-domain'), // Menu title
        $capability, // Capability - visible to all, but editing restricted
        'supervisor-updates', // Menu slug
        'supervisor_updates_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ניהול ארגונים', 'text-domain'), // Page title
        __('ניהול ארגונים', 'text-domain'), // Menu title
        $capability, // Capability - visible to all, but editing restricted
        'supervisor-organizations', // Menu slug
        'supervisor_organizations_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ניהול סיפורים', 'text-domain'), // Page title
        __('ניהול סיפורים', 'text-domain'), // Menu title
        $capability, // Capability - visible to all, but editing restricted
        'supervisor-stories', // Menu slug
        'supervisor_stories_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('ניהול נושאי מפתח', 'text-domain'), // Page title
        __('ניהול נושאי מפתח', 'text-domain'), // Menu title
        $capability, // Capability - visible to all, but editing restricted
        'supervisor-categories', // Menu slug
        'supervisor_categories_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin', // Parent slug
        __('הגדרות', 'text-domain'), // Page title
        __('הגדרות', 'text-domain'), // Menu title
        $capability, // Capability - visible to all, but editing restricted
        'supervisor-settings', // Menu slug
        'supervisor_settings_page' // Callback function
    );

    add_submenu_page(
        'supervisor-admin',
        __('מפת הידע: כותרות (קנוני)', 'text-domain'),
        __('מפת הידע: כותרות', 'text-domain'),
        'manage_options',
        'supervisor-km-label-compare',
        'supervisor_admin_knowledge_map_label_compare_page'
    );
}
add_action('admin_menu', 'supervisor_admin_menu', 20); // Higher priority to ensure capabilities are set

// Remove duplicate submenu item and reorder menu items
function supervisor_remove_duplicate_menu_item() {
    global $submenu;
    if (!isset($submenu['supervisor-admin'])) {
        return;
    }
    
    // WordPress auto-creates a submenu item with the same slug and title as parent menu ("המפקחת")
    // We need to remove this duplicate, keeping only our explicit "דשבורד" item
    $items_to_remove = [];
    
    foreach ($submenu['supervisor-admin'] as $key => $item) {
        // Check if this item has the parent slug 'supervisor-admin'
        if (isset($item[2]) && $item[2] === 'supervisor-admin') {
            if (isset($item[0])) {
                $title = strip_tags($item[0]);
                // Remove ALL items with parent slug that are NOT "דשבורד"
                // This removes the auto-generated duplicate while keeping our dashboard
                if (strpos($title, 'דשבורד') === false) {
                    // This is a duplicate (auto-generated) - mark for removal
                    $items_to_remove[] = $key;
                }
            }
        }
    }
    
    // Remove duplicates (remove in reverse order to maintain array keys)
    foreach (array_reverse($items_to_remove) as $key) {
        unset($submenu['supervisor-admin'][$key]);
    }
    
    // Re-index array after removal to fix any gaps (WordPress expects numeric keys)
    if (!empty($items_to_remove)) {
        $submenu['supervisor-admin'] = array_values($submenu['supervisor-admin']);
    }
    
    // Now handle homepage menu item modification and reordering
    $homepage_id = defined('SUPERVISOR_HOME') ? SUPERVISOR_HOME : null;
    if ($homepage_id) {
        $homepage_url = get_permalink($homepage_id);
        if ($homepage_url) {
            // Find homepage menu item and modify its URL
            foreach ($submenu['supervisor-admin'] as $key => $item) {
                if (isset($item[2]) && $item[2] === 'supervisor-visit-homepage') {
                    // Replace callback URL with direct frontend URL
                    $submenu['supervisor-admin'][$key][2] = $homepage_url;
                    
                    // Move to beginning of array to appear first
                    $homepage_item = $submenu['supervisor-admin'][$key];
                    unset($submenu['supervisor-admin'][$key]);
                    array_unshift($submenu['supervisor-admin'], $homepage_item);
                    break;
                }
            }
            
            // Add JavaScript to make homepage link open in new tab
            add_action('admin_footer', function() use ($homepage_url) {
                ?>
                <script>
                jQuery(document).ready(function($) {
                    var homepageUrl = '<?php echo esc_js($homepage_url); ?>';
                    // Make homepage menu link open in new tab
                    $('a[href="' + homepageUrl + '"]').attr('target', '_blank');
                });
                </script>
                <?php
            });
        }
    }
}
add_action('admin_menu', 'supervisor_remove_duplicate_menu_item', 25); // Run after menu is created

// Helper function to check if user can edit supervisor content
function supervisor_can_edit() {
    return current_user_can('manage_options') || current_user_can('edit_qa_updates') || current_user_can('edit_qa_stories') || current_user_can('supervisor_editor');
}

// Handle delete actions for management pages
function supervisor_handle_delete_actions() {
    if (!supervisor_can_edit()) {
        return;
    }
    
    // Handle post deletion (updates, organizations, bibliography items)
    if (isset($_GET['supervisor_delete_post']) && isset($_GET['_wpnonce'])) {
        $post_id = intval($_GET['supervisor_delete_post']);
        
        if (wp_verify_nonce($_GET['_wpnonce'], 'supervisor_delete_post_' . $post_id)) {
            $post = get_post($post_id);
            if ($post && in_array($post->post_type, ['qa_updates', 'qa_orgs', 'qa_bib_items', 'qa_stories'])) {
                wp_delete_post($post_id, true); // Force delete
                
                // Redirect to appropriate page
                $redirect_url = admin_url('admin.php?page=supervisor-updates');
                if ($post->post_type === 'qa_orgs') {
                    $redirect_url = admin_url('admin.php?page=supervisor-organizations');
                } elseif ($post->post_type === 'qa_bib_items') {
                    $redirect_url = admin_url('admin.php?page=supervisor-bibliography');
                } elseif ($post->post_type === 'qa_stories') {
                    $redirect_url = admin_url('admin.php?page=supervisor-stories');
                }
                
                wp_redirect(add_query_arg('deleted', '1', $redirect_url));
                exit;
            }
        }
    }
    
    // Handle term deletion (key topics/categories)
    if (isset($_GET['supervisor_delete_term']) && isset($_GET['_wpnonce'])) {
        $term_id = intval($_GET['supervisor_delete_term']);
        
        if (wp_verify_nonce($_GET['_wpnonce'], 'supervisor_delete_term_' . $term_id)) {
            $term = get_term($term_id, 'qa_tags');
            if ($term && !is_wp_error($term)) {
                wp_delete_term($term_id, 'qa_tags');
                
                wp_redirect(add_query_arg('deleted', '1', admin_url('admin.php?page=supervisor-categories')));
                exit;
            }
        }
    }
}
add_action('admin_init', 'supervisor_handle_delete_actions');

// Visit homepage redirect callback (fallback if URL modification doesn't work)
function supervisor_visit_homepage_redirect() {
    $homepage_id = defined('SUPERVISOR_HOME') ? SUPERVISOR_HOME : null;
    if ($homepage_id) {
        $homepage_url = get_permalink($homepage_id);
        if ($homepage_url) {
            wp_redirect($homepage_url);
            exit;
        }
    }
    // Fallback: redirect to site home
    wp_redirect(home_url());
    exit;
}

// Dashboard page callback
function supervisor_admin_dashboard() {
    // Check permissions - show read-only view for non-editors
    if (!supervisor_can_edit()) {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('המפקחת - דשבורד', 'text-domain') . '</h1>';
        echo '<div class="notice notice-info"><p>' . esc_html__('אין לך הרשאות לערוך תוכן. אתה יכול לצפות בלבד.', 'text-domain') . '</p></div>';
        echo '</div>';
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('המפקחת - דשבורד', 'text-domain'); ?></h1>
        
        <div class="supervisor-dashboard-stats">
            <div class="stat-box">
                <h3><?php echo esc_html__('סטטיסטיקות כלליות', 'text-domain'); ?></h3>
                <ul>
                    <li><strong><?php echo esc_html__('פריטי ביבליוגרפיה:', 'text-domain'); ?></strong> <?php echo wp_count_posts('qa_bib_items')->publish; ?></li>
                    <li><strong><?php echo esc_html__('עדכונים:', 'text-domain'); ?></strong> <?php echo wp_count_posts('qa_updates')->publish; ?></li>
                    <li><strong><?php echo esc_html__('ארגונים:', 'text-domain'); ?></strong> <?php echo wp_count_posts('qa_orgs')->publish; ?></li>
                    <li><strong><?php echo esc_html__('סיפורים מהשטח:', 'text-domain'); ?></strong> <?php echo wp_count_posts('qa_stories')->publish; ?></li>
                    <li><strong><?php echo esc_html__('נושאי מפתח:', 'text-domain'); ?></strong> <?php echo count(get_terms(['taxonomy' => 'qa_tags', 'hide_empty' => false])); ?></li>
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
                    <a href="<?php echo admin_url('post-new.php?post_type=qa_stories'); ?>" class="button button-secondary">
                        <?php echo esc_html__('הוסף סיפור חדש', 'text-domain'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
    <?php
}

// Bibliography management page callback
function supervisor_bibliography_page() {
    if (!supervisor_can_edit()) {
        wp_die(__('אין לך הרשאות לגשת לעמוד זה.', 'text-domain'));
    }
    // Use the existing bibliography admin page function
    qa_bib_render_admin_page();
}

// Updates management page callback
function supervisor_updates_page() {
    if (!supervisor_can_edit()) {
        wp_die(__('אין לך הרשאות לגשת לעמוד זה.', 'text-domain'));
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ניהול עדכונים', 'text-domain'); ?></h1>
        <p><?php echo esc_html__('ניהול עדכונים במערכת המפקחת.', 'text-domain'); ?></p>
        
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('העדכון נמחק בהצלחה.', 'text-domain'); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="updates-management">
            <h2><?php echo esc_html__('כל העדכונים', 'text-domain'); ?></h2>
            <?php
            $updates = new WP_Query([
                'post_type' => 'qa_updates',
                'posts_per_page' => -1, // Show all updates
                'orderby' => 'date',
                'order' => 'DESC',
                'post_status' => 'publish'
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
                    echo '<a href="' . get_permalink() . '" class="button button-small" target="_blank">' . esc_html__('צפה', 'text-domain') . '</a> ';
                    $delete_url = wp_nonce_url(
                        add_query_arg(['supervisor_delete_post' => get_the_ID()], admin_url('admin.php?page=supervisor-updates')),
                        'supervisor_delete_post_' . get_the_ID(),
                        '_wpnonce'
                    );
                    echo '<a href="' . esc_url($delete_url) . '" class="button button-small button-link-delete" onclick="return confirm(\'' . esc_js(__('האם אתה בטוח שברצונך למחוק את העדכון הזה? פעולה זו לא הפיכה!', 'text-domain')) . '\');">' . esc_html__('מחק', 'text-domain') . '</a>';
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
    if (!supervisor_can_edit()) {
        wp_die(__('אין לך הרשאות לגשת לעמוד זה.', 'text-domain'));
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ניהול ארגונים', 'text-domain'); ?></h1>
        <p><?php echo esc_html__('ניהול ארגוני פיקוח במערכת המפקחת.', 'text-domain'); ?></p>
        
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('הארגון נמחק בהצלחה.', 'text-domain'); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="organizations-management">
            <h2><?php echo esc_html__('כל הארגונים', 'text-domain'); ?></h2>
            <?php
            $organizations = new WP_Query([
                'post_type' => 'qa_orgs',
                'posts_per_page' => -1, // Show all
                'orderby' => 'title',
                'order' => 'ASC',
                'post_status' => 'publish'
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
                    $org_fields = get_fields(get_the_ID());
                    $flag_meta  = function_exists('supervisor_org_resolve_flag_meta')
                        ? supervisor_org_resolve_flag_meta($org_fields ?: [])
                        : ['name' => '', 'url' => ''];
                    $country    = $flag_meta['name'] !== '' ? $flag_meta['name'] : (get_field('qa_country') ?: '');
                    echo '<tr>';
                    echo '<td>' . esc_html(get_the_title()) . '</td>';
                    echo '<td>' . esc_html($country) . '</td>';
                    echo '<td>' . esc_html(get_the_date()) . '</td>';
                    echo '<td>';
                    echo '<a href="' . admin_url('post.php?post=' . get_the_ID() . '&action=edit') . '" class="button button-small">' . esc_html__('ערוך', 'text-domain') . '</a> ';
                    echo '<a href="' . get_permalink() . '" class="button button-small" target="_blank">' . esc_html__('צפה', 'text-domain') . '</a> ';
                    $delete_url = wp_nonce_url(
                        add_query_arg(['supervisor_delete_post' => get_the_ID()], admin_url('admin.php?page=supervisor-organizations')),
                        'supervisor_delete_post_' . get_the_ID(),
                        '_wpnonce'
                    );
                    echo '<a href="' . esc_url($delete_url) . '" class="button button-small button-link-delete" onclick="return confirm(\'' . esc_js(__('האם אתה בטוח שברצונך למחוק את הארגון הזה? פעולה זו לא הפיכה!', 'text-domain')) . '\');">' . esc_html__('מחק', 'text-domain') . '</a>';
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

// Stories management page callback
function supervisor_stories_page() {
    if (!supervisor_can_edit()) {
        wp_die(__('אין לך הרשאות לגשת לעמוד זה.', 'text-domain'));
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ניהול סיפורים מהשטח', 'text-domain'); ?></h1>
        <p><?php echo esc_html__('ניהול סיפורים מהשטח במערכת המפקחת. הסיפורים מוצגים בקרוסלת הבית.', 'text-domain'); ?></p>
        
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('הסיפור נמחק בהצלחה.', 'text-domain'); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="stories-management">
            <h2><?php echo esc_html__('כל הסיפורים', 'text-domain'); ?></h2>
            <?php
            $stories = new WP_Query([
                'post_type' => 'qa_stories',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_status' => 'publish'
            ]);
            
            if ($stories->have_posts()) :
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__('כותרת', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('תאריך', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('פעולות', 'text-domain') . '</th>';
                echo '</tr></thead><tbody>';
                
                while ($stories->have_posts()) : $stories->the_post();
                    echo '<tr>';
                    echo '<td>' . esc_html(get_the_title()) . '</td>';
                    echo '<td>' . esc_html(get_the_date()) . '</td>';
                    echo '<td>';
                    echo '<a href="' . admin_url('post.php?post=' . get_the_ID() . '&action=edit') . '" class="button button-small">' . esc_html__('ערוך', 'text-domain') . '</a> ';
                    echo '<a href="' . get_permalink() . '" class="button button-small" target="_blank">' . esc_html__('צפה', 'text-domain') . '</a> ';
                    $delete_url = wp_nonce_url(
                        add_query_arg(['supervisor_delete_post' => get_the_ID()], admin_url('admin.php?page=supervisor-stories')),
                        'supervisor_delete_post_' . get_the_ID(),
                        '_wpnonce'
                    );
                    echo '<a href="' . esc_url($delete_url) . '" class="button button-small button-link-delete" onclick="return confirm(\'' . esc_js(__('האם אתה בטוח שברצונך למחוק את הסיפור הזה? פעולה זו לא הפיכה!', 'text-domain')) . '\');">' . esc_html__('מחק', 'text-domain') . '</a>';
                    echo '</td>';
                    echo '</tr>';
                endwhile;
                
                echo '</tbody></table>';
                wp_reset_postdata();
            else :
                echo '<p>' . esc_html__('לא נמצאו סיפורים.', 'text-domain') . '</p>';
            endif;
            ?>
            
            <p>
                <a href="<?php echo admin_url('post-new.php?post_type=qa_stories'); ?>" class="button button-primary">
                    <?php echo esc_html__('הוסף סיפור חדש', 'text-domain'); ?>
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=qa_stories'); ?>" class="button button-secondary">
                    <?php echo esc_html__('צפה בכל הסיפורים', 'text-domain'); ?>
                </a>
            </p>
        </div>
    </div>
    <?php
}

// Categories management page callback
function supervisor_categories_page() {
    if (!supervisor_can_edit()) {
        wp_die(__('אין לך הרשאות לגשת לעמוד זה.', 'text-domain'));
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ניהול נושאי מפתח', 'text-domain'); ?></h1>
        <p><?php echo esc_html__('ניהול נושאי מפתח במערכת המפקחת.', 'text-domain'); ?></p>
        
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('נושא המפתח נמחק בהצלחה.', 'text-domain'); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="categories-management">
            <h2><?php echo esc_html__('נושאי מפתח', 'text-domain'); ?></h2>
            <?php
            $tags = get_terms([
                'taxonomy' => 'qa_tags',
                'hide_empty' => false,
            ]);
            
            if (!empty($tags) && !is_wp_error($tags)) :
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__('שם נושא המפתח', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('קטגוריית מפת הידע', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('איקון', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('מספר פריטים', 'text-domain') . '</th>';
                echo '<th>' . esc_html__('פעולות', 'text-domain') . '</th>';
                echo '</tr></thead><tbody>';
                
                foreach ($tags as $tag) :
                    $icon = get_term_meta($tag->term_id, 'fa_icon', true);
                    $map_cat = get_term_meta($tag->term_id, 'qa_knowledge_map_category', true);
                    $map_label = $map_cat ? supervisor_knowledge_map_category_label($map_cat) : '';
                    $count = $tag->count;
                    echo '<tr>';
                    echo '<td>' . esc_html($tag->name) . '</td>';
                    echo '<td>' . ($map_label ? esc_html($map_label) : esc_html__('—', 'text-domain')) . '</td>';
                    echo '<td>';
                    if ($icon) {
                        echo '<i class="' . esc_attr($icon) . '"></i> ' . esc_html($icon);
                    } else {
                        echo esc_html__('ללא איקון', 'text-domain');
                    }
                    echo '</td>';
                    echo '<td>' . esc_html($count) . '</td>';
                    echo '<td>';
                    echo '<a href="' . admin_url('edit-tags.php?action=edit&taxonomy=qa_tags&tag_ID=' . $tag->term_id) . '" class="button button-small">' . esc_html__('ערוך', 'text-domain') . '</a> ';
                    $delete_url = wp_nonce_url(
                        add_query_arg(['supervisor_delete_term' => $tag->term_id], admin_url('admin.php?page=supervisor-categories')),
                        'supervisor_delete_term_' . $tag->term_id,
                        '_wpnonce'
                    );
                    echo '<a href="' . esc_url($delete_url) . '" class="button button-small button-link-delete" onclick="return confirm(\'' . esc_js(__('האם אתה בטוח שברצונך למחוק את נושא המפתח הזה? פעולה זו לא הפיכה!', 'text-domain')) . '\');">' . esc_html__('מחק', 'text-domain') . '</a>';
                    echo '</td>';
                    echo '</tr>';
                endforeach;
                
                echo '</tbody></table>';
            else :
                echo '<p>' . esc_html__('לא נמצאו נושאי מפתח.', 'text-domain') . '</p>';
            endif;
            ?>
            
            <p>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=qa_tags'); ?>" class="button button-primary">
                    <?php echo esc_html__('ניהול נושאי מפתח', 'text-domain'); ?>
                </a>
            </p>
        </div>
    </div>
    <?php
}

// Save settings
function supervisor_save_settings() {
    if (!supervisor_can_edit() || !isset($_POST['supervisor_settings_submit'])) {
        return;
    }
    
    check_admin_referer('supervisor_settings');
    
    // Save contact email
    if (isset($_POST['supervisor_contact_email'])) {
        $email = sanitize_email($_POST['supervisor_contact_email']);
        if (is_email($email) || empty($email)) {
            update_option('supervisor_contact_email', $email);
        }
    }
    
    wp_redirect(add_query_arg('settings-updated', 'true', admin_url('admin.php?page=supervisor-settings')));
    exit;
}
add_action('admin_post_supervisor_save_settings', 'supervisor_save_settings');

// Add RTL styling for all supervisor admin pages
function supervisor_admin_rtl_styles() {
    $screen = get_current_screen();
    $page = isset($_GET['page']) ? $_GET['page'] : '';
    
    // Check if we're on any supervisor admin page
    $supervisor_pages = [
        'supervisor-admin',
        'supervisor-bibliography',
        'supervisor-updates',
        'supervisor-organizations',
        'supervisor-categories',
        'supervisor-settings',
        'supervisor-contact',
        'debug-updates-dates'
    ];
    
    $is_supervisor_page = false;
    if ($screen) {
        foreach ($supervisor_pages as $sp) {
            if (strpos($screen->id, $sp) !== false) {
                $is_supervisor_page = true;
                break;
            }
        }
    }
    if (!$is_supervisor_page && in_array($page, $supervisor_pages)) {
        $is_supervisor_page = true;
    }
    
    if ($is_supervisor_page) {
        ?>
        <style>
        /* RTL styling for all Supervisor admin pages */
        body.wp-admin .wrap h1,
        body.wp-admin .wrap h2,
        body.wp-admin .wrap h3,
        body.wp-admin .wrap p,
        body.wp-admin .wrap div {
            direction: rtl !important;
            text-align: right !important;
        }
        
        /* Form elements RTL */
        body.wp-admin .wrap form,
        body.wp-admin .wrap .form-table,
        body.wp-admin .wrap .form-table th,
        body.wp-admin .wrap .form-table td,
        body.wp-admin .wrap .form-table label,
        body.wp-admin .wrap .form-table .description {
            direction: rtl !important;
            text-align: right !important;
        }
        
        /* Tables RTL */
        body.wp-admin .wrap table,
        body.wp-admin .wrap .wp-list-table,
        body.wp-admin .wrap .wp-list-table thead th,
        body.wp-admin .wrap .wp-list-table tbody td {
            direction: rtl !important;
            text-align: right !important;
        }
        
        /* Lists RTL */
        body.wp-admin .wrap ul,
        body.wp-admin .wrap ol,
        body.wp-admin .wrap li {
            direction: rtl !important;
            text-align: right !important;
        }
        
        /* Input fields - keep email/url inputs LTR */
        body.wp-admin .wrap input[type="email"],
        body.wp-admin .wrap input[type="url"] {
            direction: ltr !important;
            text-align: left !important;
        }
        
        /* Buttons - center text */
        body.wp-admin .wrap .button,
        body.wp-admin .wrap .button-primary,
        body.wp-admin .wrap .button-secondary {
            text-align: center !important;
        }
        
        /* Notices RTL */
        body.wp-admin .wrap .notice,
        body.wp-admin .wrap .notice p {
            direction: rtl !important;
            text-align: right !important;
        }
        
        /* Stats and info boxes */
        body.wp-admin .wrap .supervisor-dashboard-stats,
        body.wp-admin .wrap .quick-actions,
        body.wp-admin .wrap .updates-management,
        body.wp-admin .wrap .organizations-management,
        body.wp-admin .wrap .categories-management,
        body.wp-admin .wrap .contact-form-info {
            direction: rtl !important;
            text-align: right !important;
        }
        </style>
        <?php
    }
}
add_action('admin_head', 'supervisor_admin_rtl_styles');

// Settings page callback
function supervisor_settings_page() {
    if (!supervisor_can_edit()) {
        wp_die(__('אין לך הרשאות לגשת לעמוד זה.', 'text-domain'));
    }
    
    // Handle settings save
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('ההגדרות נשמרו בהצלחה.', 'text-domain') . '</p></div>';
    }
    
    $contact_email = get_option('supervisor_contact_email', '');
    ?>
    <div class="wrap supervisor-settings-page">
        <h1><?php echo esc_html__('הגדרות המפקחת', 'text-domain'); ?></h1>
        <p><?php echo esc_html__('הגדרות כלליות למערכת המפקחת.', 'text-domain'); ?></p>
        
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field('supervisor_settings'); ?>
            <input type="hidden" name="action" value="supervisor_save_settings">
        
        <div class="supervisor-settings">
                <h2><?php echo esc_html__('יצירת קשר', 'text-domain'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="supervisor_contact_email"><?php echo esc_html__('כתובת אימייל ליצירת קשר', 'text-domain'); ?></label>
                        </th>
                        <td>
                            <input type="email" 
                                   id="supervisor_contact_email" 
                                   name="supervisor_contact_email" 
                                   value="<?php echo esc_attr($contact_email); ?>" 
                                   class="regular-text"
                                   placeholder="example@jdc.org">
                            <p class="description"><?php echo esc_html__('כתובת האימייל שתוצג בעמוד יצירת הקשר.', 'text-domain'); ?></p>
                        </td>
                    </tr>
                </table>
                
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
                
                <?php submit_button(__('שמור הגדרות', 'text-domain'), 'primary', 'supervisor_settings_submit'); ?>
            </div>
        </form>
            
            <h2><?php echo esc_html__('פעולות מערכת', 'text-domain'); ?></h2>
            <p>
                <a href="<?php echo admin_url('admin.php?page=supervisor-admin'); ?>" class="button button-primary">
                    <?php echo esc_html__('חזור לדשבורד', 'text-domain'); ?>
                </a>
            </p>
    </div>
    <?php
}

// Redirect old qa_bib_manager page to new supervisor-bibliography page
function supervisor_redirect_old_bib_manager() {
    if (isset($_GET['page']) && $_GET['page'] === 'qa_bib_manager') {
        $redirect_url = admin_url('admin.php?page=supervisor-bibliography');
        if (isset($_GET['updated'])) {
            $redirect_url = add_query_arg('updated', $_GET['updated'], $redirect_url);
        }
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('admin_init', 'supervisor_redirect_old_bib_manager', 1);

// Remove the old standalone bibliography admin menu and any other unwanted menus
function remove_old_bibliography_menu() {
    // Remove old bibliography menu
    remove_menu_page('qa_bib_manager');
    
    // Remove WordPress auto-generated menus for custom post types (duplicates)
    remove_menu_page('edit.php?post_type=qa_updates'); // Remove auto-generated "עדכונים" menu
    remove_menu_page('edit.php?post_type=qa_orgs'); // Remove auto-generated "ארגונים" menu  
    remove_menu_page('edit.php?post_type=qa_bib_items'); // Remove auto-generated "פריטים ביבליוגרפיים" menu
    remove_menu_page('edit.php?post_type=qa_stories'); // Remove auto-generated "סיפורים מהשטח" menu
    
    // Also remove any other potential unwanted menu items
    remove_menu_page('supervisor-category-icons'); // Remove if exists
    remove_menu_page('supervisor-category-manager'); // Remove if exists
    
    // Remove any submenu pages that might be duplicated
    remove_submenu_page('supervisor-admin', 'supervisor-category-icons');
    remove_submenu_page('supervisor-admin', 'supervisor-category-manager');
}
add_action('admin_menu', 'remove_old_bibliography_menu', 999); // High priority to run after the old menu is added

