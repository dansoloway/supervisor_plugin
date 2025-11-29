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
            // Basic WordPress capabilities - SIMPLIFIED APPROACH
            'read' => true,
            'edit_posts' => true, // Allow editing posts (needed for plugin content)
            'edit_published_posts' => true,
            'publish_posts' => true,
            'delete_posts' => true,
            'delete_published_posts' => true,
            'upload_files' => true,
            'unfiltered_html' => true,
            
            // Allow page editing (will be restricted to supervisor pages only via map_meta_cap)
            'edit_pages' => true,
            'edit_others_pages' => true,
            'edit_published_pages' => true,
            'publish_pages' => true,
            'delete_pages' => false, // Don't allow deleting pages
            'delete_others_pages' => false,
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
            
            // WordPress sometimes requires manage_categories for taxonomy editing UI
            // We'll map this to our custom taxonomy capabilities via map_meta_cap filter
            'manage_categories' => true,
            
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

// Update existing supervisor_editor users with new capabilities
function update_supervisor_editor_capabilities() {
    $role = get_role('supervisor_editor');
    if ($role) {
        // Add the missing capabilities
        $role->add_cap('read_qa_updates');
        $role->add_cap('read_qa_orgs');
        $role->add_cap('read_qa_bib_items');
    }
}
add_action('init', 'update_supervisor_editor_capabilities');

// Force update existing supervisor_editor users with ALL capabilities
function fix_supervisor_editor_permissions() {
    // Get all users with supervisor_editor role
    $users = get_users(array('role' => 'supervisor_editor'));
    
    foreach ($users as $user) {
        // Add ALL the capabilities they need
        $user->add_cap('edit_posts');
        $user->add_cap('edit_published_posts');
        $user->add_cap('publish_posts');
        $user->add_cap('delete_posts');
        $user->add_cap('delete_published_posts');
        $user->add_cap('upload_files');
        $user->add_cap('unfiltered_html');
        
        // Plugin custom post types
        $user->add_cap('edit_qa_updates');
        $user->add_cap('edit_others_qa_updates');
        $user->add_cap('edit_published_qa_updates');
        $user->add_cap('publish_qa_updates');
        $user->add_cap('delete_qa_updates');
        $user->add_cap('delete_others_qa_updates');
        $user->add_cap('delete_published_qa_updates');
        $user->add_cap('read_private_qa_updates');
        $user->add_cap('edit_private_qa_updates');
        $user->add_cap('delete_private_qa_updates');
        $user->add_cap('read_qa_updates');
        
        $user->add_cap('edit_qa_orgs');
        $user->add_cap('edit_others_qa_orgs');
        $user->add_cap('edit_published_qa_orgs');
        $user->add_cap('publish_qa_orgs');
        $user->add_cap('delete_qa_orgs');
        $user->add_cap('delete_others_qa_orgs');
        $user->add_cap('delete_published_qa_orgs');
        $user->add_cap('read_private_qa_orgs');
        $user->add_cap('edit_private_qa_orgs');
        $user->add_cap('delete_private_qa_orgs');
        $user->add_cap('read_qa_orgs');
        
        $user->add_cap('edit_qa_bib_items');
        $user->add_cap('edit_others_qa_bib_items');
        $user->add_cap('edit_published_qa_bib_items');
        $user->add_cap('publish_qa_bib_items');
        $user->add_cap('delete_qa_bib_items');
        $user->add_cap('delete_others_qa_bib_items');
        $user->add_cap('delete_published_qa_bib_items');
        $user->add_cap('read_private_qa_bib_items');
        $user->add_cap('edit_private_qa_bib_items');
        $user->add_cap('delete_private_qa_bib_items');
        $user->add_cap('read_qa_bib_items');
        
        // Taxonomy capabilities
        $user->add_cap('manage_qa_tags');
        $user->add_cap('edit_qa_tags');
        $user->add_cap('delete_qa_tags');
        $user->add_cap('assign_qa_tags');
        $user->add_cap('manage_qa_themes');
        $user->add_cap('edit_qa_themes');
        $user->add_cap('delete_qa_themes');
        $user->add_cap('assign_qa_themes');
        $user->add_cap('manage_categories'); // Needed for taxonomy edit UI
        
        // Add page editing capabilities (will be restricted to supervisor pages only)
        $user->add_cap('edit_pages');
        $user->add_cap('edit_others_pages');
        $user->add_cap('edit_published_pages');
        $user->add_cap('publish_pages');
    }
}
add_action('admin_init', 'fix_supervisor_editor_permissions', 1); // Run early, before menu registration

// Map manage_categories capability to our custom taxonomy capabilities
// This ensures supervisor_editor can edit custom taxonomies but not regular WordPress categories
function supervisor_map_taxonomy_capabilities($caps, $cap, $user_id, $args) {
    // Only apply to supervisor_editor users
    $user = get_userdata($user_id);
    if (!$user || !in_array('supervisor_editor', $user->roles)) {
        return $caps;
    }
    
    // Skip if user is administrator (avoid infinite loop by checking role directly, not capabilities)
    if (in_array('administrator', $user->roles)) {
        return $caps;
    }
    
    // If checking manage_categories for our custom taxonomies, map to taxonomy-specific capabilities
    if ($cap === 'manage_categories' && isset($args[0])) {
        $taxonomy = get_taxonomy($args[0]);
        if ($taxonomy) {
            // For our custom taxonomies, check the taxonomy-specific capability
            if (in_array($taxonomy->name, ['qa_tags', 'qa_themes'])) {
                $caps = [$taxonomy->cap->manage_terms];
            } else {
                // For regular WordPress categories, deny access
                $caps = ['do_not_allow'];
            }
        }
    }
    
    return $caps;
}
add_filter('map_meta_cap', 'supervisor_map_taxonomy_capabilities', 10, 4);

// Get list of supervisor page IDs that supervisor_editor can access
function supervisor_get_allowed_page_ids() {
    $allowed_pages = [];
    
    // Get all supervisor page IDs from config
    $supervisor_pages = [
        'SUPERVISOR_HOME',
        'SUPERVISOR_BIB_CATS',
        'SUPERVISOR_UPDATES',
        'SUPERVISOR_ORGS',
        'SUPERVISOR_ABOUT',
        'SUPERVISOR_CONTACT',
        'SUPERVISOR_INTRO_TEXT',
        'SUPERVISOR_ACTIVITIES',
        'SUPERVISOR_KNOWLEDGE_MAP',
    ];
    
    foreach ($supervisor_pages as $constant) {
        if (defined($constant)) {
            $page_id = constant($constant);
            if ($page_id) {
                $allowed_pages[] = intval($page_id);
            }
        }
    }
    
    return $allowed_pages;
}

// Map page editing capabilities to only allow access to supervisor pages
function supervisor_map_page_capabilities($caps, $cap, $user_id, $args) {
    // Only apply to supervisor_editor users
    $user = get_userdata($user_id);
    if (!$user || !in_array('supervisor_editor', $user->roles)) {
        return $caps;
    }
    
    // Skip if user is administrator (avoid infinite loop by checking role directly, not capabilities)
    if (in_array('administrator', $user->roles)) {
        return $caps;
    }
    
    // Check if this is a page-related capability
    $page_caps = ['edit_page', 'delete_page', 'publish_page'];
    if (!in_array($cap, $page_caps) && !in_array($cap, ['edit_pages', 'edit_others_pages', 'edit_published_pages', 'publish_pages', 'delete_pages', 'delete_others_pages', 'delete_published_pages'])) {
        return $caps;
    }
    
    // Get the page ID from args
    $page_id = isset($args[0]) ? intval($args[0]) : 0;
    
    if ($page_id > 0) {
        $allowed_pages = supervisor_get_allowed_page_ids();
        
        // If this is one of the allowed supervisor pages, grant the capability
        if (in_array($page_id, $allowed_pages)) {
            // Remove 'do_not_allow' and allow the action
            $caps = array_diff($caps, ['do_not_allow']);
            // For edit_page, we need edit_posts capability
            if ($cap === 'edit_page') {
                $caps[] = 'edit_posts';
            }
        } else {
            // Not an allowed page - deny access
            $caps = ['do_not_allow'];
        }
    }
    
    return $caps;
}
add_filter('map_meta_cap', 'supervisor_map_page_capabilities', 10, 4);

// Remove unwanted admin menu items for supervisor_editor role
function restrict_supervisor_editor_menu() {
    if (current_user_can('supervisor_editor') && !current_user_can('manage_options')) {
        // Remove all menu items except: Media, Pages, and Supervisor menu
        
        // Remove Dashboard
        remove_menu_page('index.php'); // Dashboard
        
        // Remove Jetpack
        remove_menu_page('jetpack'); // Jetpack
        
        // Remove Posts
        remove_menu_page('edit.php'); // Posts
        
        // Remove custom post types (these are likely from other plugins/themes)
        remove_menu_page('edit.php?post_type=team'); // The Team
        remove_menu_page('edit.php?post_type=project'); // Projects
        remove_menu_page('edit.php?post_type=partner'); // Partners
        remove_menu_page('edit.php?post_type=publication'); // Publications
        remove_menu_page('edit.php?post_type=disability'); // Disabilities
        remove_menu_page('edit.php?post_type=event'); // Conferences and Events
        remove_menu_page('edit.php?post_type=aging_data'); // Aging Data
        remove_menu_page('edit.php?post_type=interactive_report'); // Interactive Reports
        
        // Remove Comments
        remove_menu_page('edit-comments.php'); // Comments
        
        // Remove Contact Us (likely from a contact form plugin)
        remove_menu_page('wpcf7'); // Contact Form 7 (if that's what it is)
        remove_menu_page('contact'); // Generic contact menu
        
        // Remove dangerous/restricted items
        remove_menu_page('themes.php'); // Appearance
        remove_menu_page('plugins.php'); // Plugins
        remove_menu_page('users.php'); // Users
        remove_menu_page('tools.php'); // Tools
        remove_menu_page('options-general.php'); // Settings
        
        // Keep: Media (upload.php), Pages (edit.php?post_type=page), and Supervisor menu
        // The supervisor admin menu will be added by the main admin-menu.php file
    }
}
add_action('admin_menu', 'restrict_supervisor_editor_menu', 999);

// Restrict Pages list to only show supervisor pages
function supervisor_restrict_pages_list($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if (current_user_can('supervisor_editor') && !current_user_can('manage_options')) {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'edit-page') {
            $allowed_pages = supervisor_get_allowed_page_ids();
            if (!empty($allowed_pages)) {
                $query->set('post__in', $allowed_pages);
            } else {
                // If no allowed pages, show nothing
                $query->set('post__in', [0]);
            }
        }
    }
}
add_action('pre_get_posts', 'supervisor_restrict_pages_list');

// Redirect supervisor_editor to plugin admin page on login
function supervisor_editor_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles) && in_array('supervisor_editor', $user->roles)) {
        return admin_url('admin.php?page=supervisor-admin');
    }
    return $redirect_to;
}
add_filter('login_redirect', 'supervisor_editor_login_redirect', 10, 3);

// Redirect supervisor_editor if they try to access restricted areas - SIMPLIFIED
function redirect_supervisor_editor_from_restricted_areas() {
    if (current_user_can('supervisor_editor') && !current_user_can('manage_options')) {
        $current_screen = get_current_screen();
        
        // Only redirect from the most dangerous areas
        $restricted_pages = ['themes', 'plugins', 'users', 'tools', 'options-general'];
        if (in_array($current_screen->id ?? '', $restricted_pages)) {
            wp_redirect(admin_url('admin.php?page=supervisor-admin'));
            exit;
        }
        
        // Check if trying to edit a page that's not in the allowed list
        if ($current_screen && $current_screen->base === 'post' && $current_screen->post_type === 'page') {
            $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
            if ($post_id > 0) {
                $allowed_pages = supervisor_get_allowed_page_ids();
                if (!in_array($post_id, $allowed_pages)) {
                    wp_die(__('אין לך הרשאות לערוך את העמוד הזה.', 'text-domain'), __('גישה נדחתה', 'text-domain'), ['response' => 403]);
                }
            }
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
