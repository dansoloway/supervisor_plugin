<?php
/**
 * Migration Script: qa_bib_cats to qa_tags
 * 
 * This script migrates all functionality from qa_bib_cats to qa_tags.
 * Run this after updating the code to migrate the data.
 */

// Load WordPress
if (!defined('ABSPATH')) {
    $wp_load_paths = [
        '../../../wp-load.php',
        '../../wp-load.php',
        '../wp-load.php',
        'wp-load.php'
    ];
    
    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die("Could not load WordPress. Please run this from the plugin directory.\n");
    }
}

class SupervisorMigration {
    
    private $migration_log = [];
    
    public function run_migration() {
        echo "<h1>Supervisor Plugin Migration: qa_bib_cats → qa_tags</h1>\n";
        echo "<style>
            .migration-step { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
            .success { background-color: #d4edda; border-color: #c3e6cb; }
            .error { background-color: #f8d7da; border-color: #f5c6cb; }
            .warning { background-color: #fff3cd; border-color: #ffeaa7; }
        </style>\n";
        
        $this->log("Starting migration...");
        
        // Step 1: Migrate terms
        $this->migrate_terms();
        
        // Step 2: Migrate term meta
        $this->migrate_term_meta();
        
        // Step 3: Migrate post relationships
        $this->migrate_post_relationships();
        
        // Step 4: Clean up old taxonomy
        $this->cleanup_old_taxonomy();
        
        // Step 5: Flush rewrite rules
        $this->flush_rewrite_rules();
        
        $this->print_summary();
    }
    
    private function migrate_terms() {
        echo "<div class='migration-step'>\n";
        echo "<h3>Step 1: Migrating Terms</h3>\n";
        
        // Get all qa_bib_cats terms
        $old_terms = get_terms([
            'taxonomy' => 'qa_bib_cats',
            'hide_empty' => false,
        ]);
        
        if (empty($old_terms)) {
            echo "<p class='warning'>No qa_bib_cats terms found to migrate.</p>\n";
            return;
        }
        
        $migrated_count = 0;
        foreach ($old_terms as $old_term) {
            // Handle both object and array formats
            $term_name = is_object($old_term) ? $old_term->name : $old_term['name'];
            $term_description = is_object($old_term) ? $old_term->description : $old_term['description'];
            $term_slug = is_object($old_term) ? $old_term->slug : $old_term['slug'];
            $term_id = is_object($old_term) ? $old_term->term_id : $old_term['term_id'];
            
            // Check if term already exists in qa_tags
            $existing_term = get_term_by('name', $term_name, 'qa_tags');
            
            if ($existing_term) {
                echo "<p>Term '{$term_name}' already exists in qa_tags (ID: {$existing_term->term_id})</p>\n";
                $this->log("Term '{$term_name}' already exists in qa_tags");
            } else {
                // Create new term in qa_tags
                $result = wp_insert_term($term_name, 'qa_tags', [
                    'description' => $term_description,
                    'slug' => $term_slug,
                ]);
                
                if (is_wp_error($result)) {
                    echo "<p class='error'>Failed to migrate term '{$term_name}': " . $result->get_error_message() . "</p>\n";
                    $this->log("ERROR: Failed to migrate term '{$term_name}'");
                } else {
                    echo "<p class='success'>Migrated term '{$term_name}' (ID: {$term_id} → {$result['term_id']})</p>\n";
                    $this->log("Migrated term '{$term_name}' (ID: {$term_id} → {$result['term_id']})");
                    $migrated_count++;
                }
            }
        }
        
        echo "<p><strong>Migrated {$migrated_count} terms</strong></p>\n";
        echo "</div>\n";
    }
    
    private function migrate_term_meta() {
        echo "<div class='migration-step'>\n";
        echo "<h3>Step 2: Migrating Term Meta</h3>\n";
        
        $old_terms = get_terms([
            'taxonomy' => 'qa_bib_cats',
            'hide_empty' => false,
        ]);
        
        if (empty($old_terms) || is_wp_error($old_terms)) {
            echo "<p class='warning'>No qa_bib_cats terms found for meta migration.</p>\n";
            return;
        }
        
        $migrated_meta = 0;
        foreach ($old_terms as $old_term) {
            // Handle both object and array formats
            $term_name = is_object($old_term) ? $old_term->name : $old_term['name'];
            $term_id = is_object($old_term) ? $old_term->term_id : $old_term['term_id'];
            
            // Find corresponding term in qa_tags
            $new_term = get_term_by('name', $term_name, 'qa_tags');
            
            if (!$new_term) {
                echo "<p class='warning'>No corresponding term found in qa_tags for '{$term_name}'</p>\n";
                continue;
            }
            
            // Migrate custom fields
            $icon = get_term_meta($term_id, 'qa_bib_icon', true);
            $description = get_term_meta($term_id, 'qa_bib_description', true);
            $fa_icon = get_term_meta($term_id, 'fa_icon', true);
            
            if (!empty($icon)) {
                update_term_meta($new_term->term_id, 'qa_bib_icon', $icon);
                echo "<p>Migrated icon for '{$term_name}': {$icon}</p>\n";
                $migrated_meta++;
            }
            
            if (!empty($description)) {
                update_term_meta($new_term->term_id, 'qa_bib_description', $description);
                echo "<p>Migrated description for '{$term_name}'</p>\n";
                $migrated_meta++;
            }
            
            if (!empty($fa_icon)) {
                update_term_meta($new_term->term_id, 'fa_icon', $fa_icon);
                echo "<p>Migrated Font Awesome icon for '{$term_name}': {$fa_icon}</p>\n";
                $migrated_meta++;
            }
        }
        
        echo "<p><strong>Migrated {$migrated_meta} meta fields</strong></p>\n";
        echo "</div>\n";
    }
    
    private function migrate_post_relationships() {
        echo "<div class='migration-step'>\n";
        echo "<h3>Step 3: Migrating Post Relationships</h3>\n";
        
        // Get all posts that have qa_bib_cats terms
        $posts = get_posts([
            'post_type' => ['qa_bib_items', 'qa_orgs', 'qa_updates'],
            'posts_per_page' => -1,
            'tax_query' => [
                [
                    'taxonomy' => 'qa_bib_cats',
                    'operator' => 'EXISTS',
                ],
            ],
        ]);
        
        $migrated_posts = 0;
        foreach ($posts as $post) {
            // Get qa_bib_cats terms for this post
            $old_terms = get_the_terms($post->ID, 'qa_bib_cats');
            
            if (!$old_terms || is_wp_error($old_terms)) {
                continue;
            }
            
            $new_term_ids = [];
            foreach ($old_terms as $old_term) {
                // Find corresponding term in qa_tags
                $new_term = get_term_by('name', $old_term->name, 'qa_tags');
                if ($new_term) {
                    $new_term_ids[] = $new_term->term_id;
                }
            }
            
            if (!empty($new_term_ids)) {
                // Set the new terms
                wp_set_object_terms($post->ID, $new_term_ids, 'qa_tags', true); // true = append
                echo "<p>Migrated terms for post '{$post->post_title}' (ID: {$post->ID})</p>\n";
                $migrated_posts++;
            }
        }
        
        echo "<p><strong>Migrated relationships for {$migrated_posts} posts</strong></p>\n";
        echo "</div>\n";
    }
    
    private function cleanup_old_taxonomy() {
        echo "<div class='migration-step'>\n";
        echo "<h3>Step 4: Cleaning Up Old Taxonomy</h3>\n";
        
        // Note: We can't actually delete the taxonomy here because it's registered in the code
        // This will be handled when the code is updated to remove qa_bib_cats registration
        
        echo "<p class='warning'>Note: qa_bib_cats taxonomy cleanup will happen when the code is updated.</p>\n";
        echo "<p>For now, the old taxonomy remains but is no longer used.</p>\n";
        
        echo "</div>\n";
    }
    
    private function flush_rewrite_rules() {
        echo "<div class='migration-step'>\n";
        echo "<h3>Step 5: Flushing Rewrite Rules</h3>\n";
        
        flush_rewrite_rules();
        echo "<p class='success'>Rewrite rules flushed successfully.</p>\n";
        
        echo "</div>\n";
    }
    
    private function log($message) {
        $this->migration_log[] = date('Y-m-d H:i:s') . ' - ' . $message;
    }
    
    private function print_summary() {
        echo "<div class='migration-step'>\n";
        echo "<h2>Migration Summary</h2>\n";
        
        echo "<h3>Migration Log:</h3>\n";
        echo "<ul>\n";
        foreach ($this->migration_log as $log_entry) {
            echo "<li>" . esc_html($log_entry) . "</li>\n";
        }
        echo "</ul>\n";
        
        echo "<h3>Next Steps:</h3>\n";
        echo "<ol>\n";
        echo "<li>Update the code to remove qa_bib_cats references</li>\n";
        echo "<li>Run the test suite to verify functionality</li>\n";
        echo "<li>Test the admin interface</li>\n";
        echo "<li>Test the frontend templates</li>\n";
        echo "</ol>\n";
        
        echo "<p class='success'><strong>Migration completed!</strong></p>\n";
        echo "</div>\n";
    }
}

// Run migration if this file is accessed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $migration = new SupervisorMigration();
    $migration->run_migration();
}
