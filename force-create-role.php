<?php
/**
 * Force create the Supervisor Editor role
 * Run this once to create the role, then delete this file
 */

// Include WordPress
require_once('../../../wp-config.php');

// Force create the supervisor_editor role
function force_create_supervisor_editor_role() {
    add_role(
        'supervisor_editor',
        'Supervisor Editor',
        array(
            // Basic WordPress capabilities
            'read' => true,
            
            // Plugin-specific capabilities
            'edit_posts' => false,
            'edit_pages' => false,
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
            
            // Media capabilities - ALLOW
            'upload_files' => true,
        )
    );
    
    echo "Supervisor Editor role created successfully!<br>";
    echo "You can now delete this file.<br>";
    echo "<a href='" . admin_url('user-new.php') . "'>Go to Add New User</a>";
}

// Run the function
force_create_supervisor_editor_role();
?>
