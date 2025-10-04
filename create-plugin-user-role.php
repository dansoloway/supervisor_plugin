<?php
/**
 * Create Custom User Role for Supervisor Plugin
 * This user can ONLY edit content related to the supervisor plugin
 */

// Add custom user role for supervisor plugin content only
function add_supervisor_editor_role() {
    add_role(
        'supervisor_editor',
        'Supervisor Editor',
        array(
            // Basic WordPress capabilities
            'read' => true,
            
            // Plugin-specific capabilities
            'edit_posts' => false, // Can't edit regular posts
            'edit_pages' => false, // Can't edit pages
            'edit_others_posts' => false,
            'edit_others_pages' => false,
            'edit_published_posts' => false,
            'edit_published_pages' => false,
            'publish_posts' => false,
            'publish_pages' => false,
            'delete_posts' => false,
            'delete_pages' => false,
            'delete_others_posts' => false,
            'delete_others_pages' => false,
            'delete_published_posts' => false,
            'delete_published_pages' => false,
            
            // Plugin custom post types - ALLOW
            'edit_qa_updates' => true,
            'edit_others_qa_updates' => true,
            'edit_published_qa_updates' => true,
            'publish_qa_updates' => true,
            'delete_qa_updates' => true,
            'delete_others_qa_updates' => true,
            'delete_published_qa_updates' => true,
            'read_private_qa_updates' => true,
            'edit_private_qa_updates' => true,
            'delete_private_qa_updates' => true,
            'read_qa_updates' => true,
            
            'edit_qa_orgs' => true,
            'edit_others_qa_orgs' => true,
            'edit_published_qa_orgs' => true,
            'publish_qa_orgs' => true,
            'delete_qa_orgs' => true,
            'delete_others_qa_orgs' => true,
            'delete_published_qa_orgs' => true,
            'read_private_qa_orgs' => true,
            'edit_private_qa_orgs' => true,
            'delete_private_qa_orgs' => true,
            'read_qa_orgs' => true,
            
            'edit_qa_bib_items' => true,
            'edit_others_qa_bib_items' => true,
            'edit_published_qa_bib_items' => true,
            'publish_qa_bib_items' => true,
            'delete_qa_bib_items' => true,
            'delete_others_qa_bib_items' => true,
            'delete_published_qa_bib_items' => true,
            'read_private_qa_bib_items' => true,
            'edit_private_qa_bib_items' => true,
            'delete_private_qa_bib_items' => true,
            'read_qa_bib_items' => true,
            
            // Taxonomy capabilities - ALLOW
            'manage_qa_tags' => true,
            'edit_qa_tags' => true,
            'delete_qa_tags' => true,
            'assign_qa_tags' => true,
            
            'manage_qa_themes' => true,
            'edit_qa_themes' => true,
            'delete_qa_themes' => true,
            'assign_qa_themes' => true,
            
            // Media capabilities - ALLOW (for images in posts)
            'upload_files' => true,
            
            // Admin menu access - RESTRICTED
            'manage_options' => false, // Can't access general settings
            'edit_theme_options' => false, // Can't edit theme
            'switch_themes' => false, // Can't switch themes
            'edit_themes' => false, // Can't edit theme files
            'install_plugins' => false, // Can't install plugins
            'activate_plugins' => false, // Can't activate plugins
            'edit_plugins' => false, // Can't edit plugins
            'delete_plugins' => false, // Can't delete plugins
            'edit_users' => false, // Can't edit users
            'delete_users' => false, // Can't delete users
            'create_users' => false, // Can't create users
            'list_users' => false, // Can't list users
            'promote_users' => false, // Can't promote users
            'edit_dashboard' => false, // Can't edit dashboard
        )
    );
}
add_action('init', 'add_supervisor_editor_role');

// Remove unwanted admin menu items for supervisor_editor role
function restrict_supervisor_editor_menu() {
    if (current_user_can('supervisor_editor') && !current_user_can('manage_options')) {
        // Remove restricted admin menu items only
        remove_menu_page('edit.php'); // Posts
        remove_menu_page('edit.php?post_type=page'); // Pages
        remove_menu_page('edit-comments.php'); // Comments
        remove_menu_page('themes.php'); // Appearance
        remove_menu_page('plugins.php'); // Plugins
        remove_menu_page('users.php'); // Users
        remove_menu_page('tools.php'); // Tools
        remove_menu_page('options-general.php'); // Settings
        
        // Remove auto-generated custom post type menus (they'll be in our custom admin)
        remove_menu_page('edit.php?post_type=qa_updates');
        remove_menu_page('edit.php?post_type=qa_orgs');
        remove_menu_page('edit.php?post_type=qa_bib_items');
        
        // Keep Dashboard (index.php), Media (upload.php), and Profile accessible
        // The supervisor admin menu will be added by the main admin-menu.php file
    }
}
add_action('admin_menu', 'restrict_supervisor_editor_menu', 999);

// Redirect supervisor_editor to plugin admin page on login
function supervisor_editor_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles) && in_array('supervisor_editor', $user->roles)) {
        return admin_url('admin.php?page=supervisor-admin');
    }
    return $redirect_to;
}
add_filter('login_redirect', 'supervisor_editor_login_redirect', 10, 3);

// Redirect supervisor_editor if they try to access restricted areas
function redirect_supervisor_editor_from_restricted_areas() {
    if (current_user_can('supervisor_editor') && !current_user_can('manage_options')) {
        $current_screen = get_current_screen();
        
        // Allow access to supervisor plugin pages
        if (isset($_GET['page']) && strpos($_GET['page'], 'supervisor') !== false) {
            return;
        }
        
        // Allow access to edit pages for plugin post types
        if (in_array($current_screen->post_type ?? '', ['qa_updates', 'qa_orgs', 'qa_bib_items'])) {
            return;
        }
        
        // Allow access to taxonomy pages for plugin taxonomies
        if (in_array($current_screen->taxonomy ?? '', ['qa_tags', 'qa_themes'])) {
            return;
        }
        
        // Allow access to dashboard, media library, and profile page
        $allowed_pages = ['dashboard', 'upload', 'profile'];
        if (in_array($current_screen->id ?? '', $allowed_pages)) {
            return;
        }
        
        // Only redirect if they're trying to access something they really shouldn't
        $restricted_pages = ['posts', 'pages', 'themes', 'plugins', 'users', 'tools', 'options-general'];
        if (in_array($current_screen->id ?? '', $restricted_pages)) {
            wp_redirect(admin_url('admin.php?page=supervisor-admin'));
            exit;
        }
    }
}
add_action('current_screen', 'redirect_supervisor_editor_from_restricted_areas');

// Hide admin bar items for supervisor_editor
function hide_admin_bar_items() {
    if (current_user_can('supervisor_editor') && !current_user_can('manage_options')) {
        global $wp_admin_bar;
        
        // Remove WordPress.org related items
        $wp_admin_bar->remove_node('wp-logo');
        $wp_admin_bar->remove_node('about');
        $wp_admin_bar->remove_node('wporg');
        $wp_admin_bar->remove_node('documentation');
        $wp_admin_bar->remove_node('support-forums');
        $wp_admin_bar->remove_node('feedback');
        
        // Remove site management items
        $wp_admin_bar->remove_node('customize');
        $wp_admin_bar->remove_node('updates');
        $wp_admin_bar->remove_node('comments');
        
        // Remove "New Content" but add back plugin-specific items
        $wp_admin_bar->remove_node('new-content');
        
        // Add back "New Content" but only for plugin post types
        $wp_admin_bar->add_menu(array(
            'id' => 'new-content',
            'title' => __('הוסף תוכן חדש'),
            'href' => '#',
        ));
        
        // Add plugin-specific new content items
        $wp_admin_bar->add_menu(array(
            'parent' => 'new-content',
            'id' => 'new-qa-updates',
            'title' => __('עדכון חדש'),
            'href' => admin_url('post-new.php?post_type=qa_updates'),
        ));
        
        $wp_admin_bar->add_menu(array(
            'parent' => 'new-content',
            'id' => 'new-qa-orgs',
            'title' => __('ארגון חדש'),
            'href' => admin_url('post-new.php?post_type=qa_orgs'),
        ));
        
        $wp_admin_bar->add_menu(array(
            'parent' => 'new-content',
            'id' => 'new-qa-bib-items',
            'title' => __('פריט ביבליוגרפיה חדש'),
            'href' => admin_url('post-new.php?post_type=qa_bib_items'),
        ));
        
        // Keep logout and user account items - don't remove 'my-account'
        // Keep 'site-name' and 'view-site' for navigation
    }
}
add_action('wp_before_admin_bar_render', 'hide_admin_bar_items');

// Add custom admin notice for supervisor_editor
function supervisor_editor_admin_notice() {
    if (current_user_can('supervisor_editor') && !current_user_can('manage_options')) {
        echo '<div class="notice notice-info"><p><strong>Supervisor Editor:</strong> You have limited access to edit only supervisor plugin content.</p></div>';
    }
}
add_action('admin_notices', 'supervisor_editor_admin_notice');

// Add RTL styling for Supervisor Editor admin area
function supervisor_editor_admin_styles() {
    if (current_user_can('supervisor_editor') && !current_user_can('manage_options')) {
        ?>
        <style>
        /* RTL styling for Supervisor Editor admin area */
        body.wp-admin {
            direction: rtl !important;
            text-align: right !important;
        }
        
        /* Admin menu RTL */
        #adminmenu,
        #adminmenu .wp-submenu,
        #adminmenu .wp-has-submenu .wp-submenu {
            direction: rtl !important;
            text-align: right !important;
        }
        
        /* Admin menu items */
        #adminmenu .wp-menu-name,
        #adminmenu .wp-submenu .wp-submenu-head {
            text-align: right !important;
            direction: rtl !important;
        }
        
        /* Admin bar RTL */
        #wpadminbar {
            direction: rtl !important;
        }
        
        #wpadminbar .ab-item,
        #wpadminbar .ab-top-menu > li > .ab-item {
            text-align: right !important;
        }
        
        /* Admin content area */
        #wpbody-content .wrap,
        .wp-admin .wrap h1,
        .wp-admin .wrap h2,
        .wp-admin .wrap h3 {
            text-align: right !important;
            direction: rtl !important;
        }
        
        /* Form elements RTL */
        .wp-admin form,
        .wp-admin input,
        .wp-admin textarea,
        .wp-admin select {
            text-align: right !important;
            direction: rtl !important;
        }
        
        /* Tables RTL */
        .wp-admin table,
        .wp-admin .wp-list-table {
            direction: rtl !important;
        }
        
        .wp-admin table th,
        .wp-admin table td {
            text-align: right !important;
        }
        
        /* Meta boxes RTL */
        .wp-admin .postbox,
        .wp-admin .postbox h2,
        .wp-admin .postbox h3 {
            text-align: right !important;
            direction: rtl !important;
        }
        
        /* Buttons RTL */
        .wp-admin .button,
        .wp-admin .button-primary,
        .wp-admin .button-secondary {
            text-align: center !important;
        }
        
        /* Admin notices RTL */
        .wp-admin .notice,
        .wp-admin .notice p {
            text-align: right !important;
            direction: rtl !important;
        }
        
        /* Media library RTL */
        .wp-admin .media-frame,
        .wp-admin .media-frame-title {
            direction: rtl !important;
        }
        
        /* ACF fields RTL */
        .wp-admin .acf-field,
        .wp-admin .acf-field label {
            text-align: right !important;
            direction: rtl !important;
        }
        
        /* WordPress editor RTL */
        .wp-admin .wp-editor-wrap,
        .wp-admin .mce-toolbar {
            direction: rtl !important;
        }
        
        /* Override specific elements that should remain LTR */
        .wp-admin code,
        .wp-admin .code,
        .wp-admin input[type="url"],
        .wp-admin input[type="email"] {
            direction: ltr !important;
            text-align: left !important;
        }
        </style>
        <?php
    }
}
add_action('admin_head', 'supervisor_editor_admin_styles');
