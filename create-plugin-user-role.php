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
        $wp_admin_bar->remove_menu('wp-logo');
        $wp_admin_bar->remove_menu('site-name');
        $wp_admin_bar->remove_menu('updates');
        $wp_admin_bar->remove_menu('comments');
        $wp_admin_bar->remove_menu('new-content');
        $wp_admin_bar->remove_menu('my-account');
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
