<?php
/**
 * Export Supervisor Plugin Content
 * 
 * Exports all plugin-related content (posts, taxonomies, ACF fields, media)
 * to a JSON file that can be imported into production.
 * 
 * USAGE:
 * 1. Access via: /wp-admin/admin.php?page=supervisor-export-content
 * 2. Downloads a JSON export file
 */

// Security check
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Custom post types to export
$post_types_to_export = ['qa_updates', 'qa_orgs', 'qa_bib_items'];
$taxonomies_to_export = ['qa_tags', 'qa_themes'];

/**
 * Export all plugin content to JSON
 */
function supervisor_export_content() {
    // Define post types and taxonomies to export
    $post_types_to_export = ['qa_updates', 'qa_orgs', 'qa_bib_items'];
    $taxonomies_to_export = ['qa_tags', 'qa_themes'];
    
    $export_data = [
        'version' => '1.0',
        'export_date' => current_time('mysql'),
        'site_url' => site_url(),
        'posts' => [],
        'taxonomies' => [],
        'media' => []
    ];
    
    // Export posts for each custom post type
    foreach ($post_types_to_export as $post_type) {
        // Check if post type exists
        if (!post_type_exists($post_type)) {
            error_log("Export: Post type '$post_type' does not exist");
            continue;
        }
        
        // Try multiple approaches to get posts
        $posts = [];
        
        // Approach 1: Use get_posts with 'any' status
        $posts = get_posts([
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'any',
            'orderby' => 'date',
            'order' => 'ASC',
            'suppress_filters' => false // Don't suppress filters
        ]);
        
        // Approach 2: If empty, try with explicit statuses
        if (empty($posts)) {
            $all_statuses = get_post_stati();
            $posts = get_posts([
                'post_type' => $post_type,
                'posts_per_page' => -1,
                'post_status' => $all_statuses,
                'orderby' => 'date',
                'order' => 'ASC'
            ]);
        }
        
        // Approach 3: If still empty, use direct database query as last resort
        if (empty($posts)) {
            global $wpdb;
            $post_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
                $post_type
            ));
            
            if (!empty($post_ids)) {
                $posts = array_map('get_post', $post_ids);
                $posts = array_filter($posts); // Remove any nulls
            }
        }
        
        // Debug: log how many posts found
        error_log("Export: Found " . count($posts) . " posts of type '$post_type'");
        
        if (empty($posts)) {
            error_log("Export: No posts found for '$post_type'. Post type exists: " . (post_type_exists($post_type) ? 'yes' : 'no'));
            continue; // Skip to next post type if no posts found
        }
        
        foreach ($posts as $post) {
            $post_data = [
                'ID' => $post->ID, // Store original ID for mapping
                'post_type' => $post->post_type,
                'post_title' => $post->post_title,
                'post_content' => $post->post_content,
                'post_excerpt' => $post->post_excerpt,
                'post_status' => $post->post_status,
                'post_date' => $post->post_date,
                'post_date_gmt' => $post->post_date_gmt,
                'post_modified' => $post->post_modified,
                'post_modified_gmt' => $post->post_modified_gmt,
                'post_name' => $post->post_name,
                'post_author' => $post->post_author,
                'comment_status' => $post->comment_status,
                'ping_status' => $post->ping_status,
                'menu_order' => $post->menu_order,
                'taxonomies' => [],
                'acf_fields' => [],
                'featured_image' => null,
                'meta' => []
            ];
            
            // Get taxonomies for this post
            foreach ($taxonomies_to_export as $taxonomy) {
                $terms = wp_get_post_terms($post->ID, $taxonomy, ['fields' => 'all']);
                if (!is_wp_error($terms) && !empty($terms)) {
                    $post_data['taxonomies'][$taxonomy] = array_map(function($term) use ($taxonomy) {
                        return [
                            'slug' => $term->slug,
                            'name' => $term->name,
                            'description' => $term->description
                        ];
                    }, $terms);
                }
            }
            
            // Get ACF fields
            if (function_exists('get_fields')) {
                $acf_fields = get_fields($post->ID);
                if ($acf_fields) {
                    // Process ACF fields (handle images, relationships, etc.)
                    $post_data['acf_fields'] = supervisor_process_acf_fields($acf_fields, $post->ID);
                }
            }
            
            // Get featured image
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            if ($thumbnail_id) {
                $thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'full');
                $thumbnail_path = get_attached_file($thumbnail_id);
                $post_data['featured_image'] = [
                    'url' => $thumbnail_url,
                    'filename' => basename($thumbnail_path),
                    'attachment_data' => supervisor_get_attachment_data($thumbnail_id)
                ];
                $export_data['media'][] = supervisor_get_attachment_data($thumbnail_id);
            }
            
            // Get custom post meta (excluding ACF fields which are already exported)
            $all_meta = get_post_meta($post->ID);
            foreach ($all_meta as $key => $values) {
                // Skip ACF fields (they start with underscore or are in ACF format)
                if (strpos($key, '_') === 0 && $key !== '_thumbnail_id') {
                    continue;
                }
                // Skip if it's an ACF field (single value)
                if (!is_array($values) || count($values) === 1) {
                    $post_data['meta'][$key] = maybe_unserialize($values[0]);
                } else {
                    $post_data['meta'][$key] = array_map('maybe_unserialize', $values);
                }
            }
            
            $export_data['posts'][] = $post_data;
        }
        
        // Clean up after processing this post type
        wp_reset_postdata();
        unset($posts); // Free memory
    }
    
    // Export taxonomies with all terms
    foreach ($taxonomies_to_export as $taxonomy) {
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false
        ]);
        
        if (!is_wp_error($terms) && !empty($terms)) {
            $export_data['taxonomies'][$taxonomy] = [];
            
            foreach ($terms as $term) {
                $term_data = [
                    'slug' => $term->slug,
                    'name' => $term->name,
                    'description' => $term->description,
                    'parent' => $term->parent,
                    'term_taxonomy_id' => $term->term_taxonomy_id,
                    'count' => $term->count
                ];
                
                // Get term meta (like FA icons)
                $term_meta = get_term_meta($term->term_id);
                if (!empty($term_meta)) {
                    $term_data['meta'] = [];
                    foreach ($term_meta as $key => $values) {
                        if (count($values) === 1) {
                            $term_data['meta'][$key] = maybe_unserialize($values[0]);
                        } else {
                            $term_data['meta'][$key] = array_map('maybe_unserialize', $values);
                        }
                    }
                }
                
                $export_data['taxonomies'][$taxonomy][] = $term_data;
            }
        }
    }
    
    return $export_data;
}

/**
 * Process ACF fields and handle special types (images, files, relationships)
 */
function supervisor_process_acf_fields($fields, $post_id) {
    $processed = [];
    
    foreach ($fields as $key => $value) {
        if (empty($value)) {
            $processed[$key] = $value;
            continue;
        }
        
        // Get field object to determine type
        $field_object = get_field_object($key, $post_id);
        $field_type = $field_object['type'] ?? 'text';
        
        switch ($field_type) {
            case 'image':
                if (is_numeric($value)) {
                    // Image ID
                    $image_data = supervisor_get_attachment_data($value);
                    $processed[$key] = [
                        'type' => 'image_id',
                        'attachment_data' => $image_data
                    ];
                } elseif (is_array($value) && isset($value['ID'])) {
                    // Image array
                    $image_data = supervisor_get_attachment_data($value['ID']);
                    $processed[$key] = [
                        'type' => 'image_array',
                        'attachment_data' => $image_data,
                        'url' => $value['url'] ?? null,
                        'alt' => $value['alt'] ?? null,
                        'title' => $value['title'] ?? null
                    ];
                } else {
                    $processed[$key] = $value;
                }
                break;
                
            case 'file':
                if (is_numeric($value)) {
                    $file_data = supervisor_get_attachment_data($value);
                    $processed[$key] = [
                        'type' => 'file_id',
                        'attachment_data' => $file_data
                    ];
                } elseif (is_array($value) && isset($value['ID'])) {
                    $file_data = supervisor_get_attachment_data($value['ID']);
                    $processed[$key] = [
                        'type' => 'file_array',
                        'attachment_data' => $file_data,
                        'url' => $value['url'] ?? null,
                        'filename' => $value['filename'] ?? null
                    ];
                } else {
                    $processed[$key] = $value;
                }
                break;
                
            case 'gallery':
                if (is_array($value)) {
                    $processed[$key] = [
                        'type' => 'gallery',
                        'images' => array_map(function($img) {
                            if (is_numeric($img)) {
                                return supervisor_get_attachment_data($img);
                            } elseif (is_array($img) && isset($img['ID'])) {
                                return supervisor_get_attachment_data($img['ID']);
                            }
                            return $img;
                        }, $value)
                    ];
                } else {
                    $processed[$key] = $value;
                }
                break;
                
            case 'relationship':
            case 'post_object':
                // Handle relationship fields (export post IDs and titles)
                if (is_array($value)) {
                    $processed[$key] = [
                        'type' => $field_type,
                        'posts' => array_map(function($post) {
                            if (is_numeric($post)) {
                                $p = get_post($post);
                                return $p ? ['ID' => $post, 'post_type' => $p->post_type, 'title' => $p->post_title] : null;
                            } elseif (is_object($post) && isset($post->ID)) {
                                return ['ID' => $post->ID, 'post_type' => $post->post_type, 'title' => $post->post_title];
                            }
                            return $post;
                        }, is_array($value) ? $value : [$value])
                    ];
                } else {
                    $processed[$key] = $value;
                }
                break;
                
            case 'taxonomy':
                // Taxonomy terms - already exported separately
                if (is_array($value)) {
                    $processed[$key] = [
                        'type' => 'taxonomy',
                        'terms' => array_map(function($term) {
                            if (is_object($term)) {
                                return ['term_id' => $term->term_id, 'slug' => $term->slug, 'name' => $term->name, 'taxonomy' => $term->taxonomy];
                            }
                            return $term;
                        }, is_array($value) ? $value : [$value])
                    ];
                } else {
                    $processed[$key] = $value;
                }
                break;
                
            default:
                $processed[$key] = $value;
        }
    }
    
    return $processed;
}

/**
 * Get attachment data for export
 */
function supervisor_get_attachment_data($attachment_id) {
    $attachment = get_post($attachment_id);
    if (!$attachment || $attachment->post_type !== 'attachment') {
        return null;
    }
    
    $file_path = get_attached_file($attachment_id);
    $file_url = wp_get_attachment_url($attachment_id);
    
    // Read file contents as base64 for export
    $file_base64 = null;
    if ($file_path && file_exists($file_path)) {
        $file_contents = file_get_contents($file_path);
        $file_base64 = base64_encode($file_contents);
    }
    
    return [
        'ID' => $attachment->ID,
        'post_title' => $attachment->post_title,
        'post_content' => $attachment->post_content,
        'post_excerpt' => $attachment->post_excerpt,
        'post_mime_type' => $attachment->post_mime_type,
        'guid' => $attachment->guid,
        'file_url' => $file_url,
        'file_path' => $file_path,
        'filename' => basename($file_path),
        'file_base64' => $file_base64,
        'meta' => wp_get_attachment_metadata($attachment_id)
    ];
}

// Handle export download before any output (must run early)
function supervisor_handle_export_download() {
    // Check if this is the export download request
    if (!isset($_GET['page']) || $_GET['page'] !== 'supervisor-export-content') {
        return;
    }
    
    if (!isset($_GET['download_export']) || !isset($_GET['_wpnonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_GET['_wpnonce'], 'supervisor_export_content')) {
        wp_die(__('Security check failed.', 'text-domain'));
    }
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to access this page.', 'text-domain'));
    }
    
    // Increase memory and execution time for large exports
    @ini_set('memory_limit', '512M');
    @set_time_limit(600);
    
    // Clean any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $export_data = supervisor_export_content();
    
    // Add debug info if no posts found
    if (empty($export_data['posts'])) {
        // Try to get post counts for debugging
        $debug_info = [];
        $post_types = ['qa_updates', 'qa_orgs', 'qa_bib_items'];
        foreach ($post_types as $pt) {
            $count = wp_count_posts($pt);
            $debug_info[$pt] = (array) $count;
        }
        $export_data['debug'] = [
            'post_counts' => $debug_info,
            'post_types_registered' => array_map('post_type_exists', $post_types),
            'taxonomies_registered' => array_map('taxonomy_exists', ['qa_tags', 'qa_themes'])
        ];
    }
    
    $filename = 'supervisor-export-' . date('Y-m-d-His') . '.json';
    $json_output = json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    // Set headers for file download
    nocache_headers(); // Prevent caching
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($json_output));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Output JSON and exit
    echo $json_output;
    exit;
}
add_action('admin_init', 'supervisor_handle_export_download', 1); // Run early, before any output

// Admin page callback (only renders the page, not the download)
function supervisor_export_content_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to access this page.', 'text-domain'));
    }
    
    // Don't render if this is a download request (should be handled above)
    if (isset($_GET['download_export'])) {
        return;
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ייצוא תוכן המקפחת', 'text-domain'); ?></h1>
        
        <p><?php echo esc_html__('ייצא את כל התוכן של התוסף (פוסטים, טקסונומיות, שדות ACF, מדיה) לקובץ JSON שניתן לייבא לפרודקשן.', 'text-domain'); ?></p>
        
        <div class="export-info">
            <h2><?php echo esc_html__('מה יוצא:', 'text-domain'); ?></h2>
            <ul>
                <li><?php echo esc_html__('כל הפוסטים מסוגים: qa_updates, qa_orgs, qa_bib_items', 'text-domain'); ?></li>
                <li><?php echo esc_html__('כל הטקסונומיות: qa_tags, qa_themes', 'text-domain'); ?></li>
                <li><?php echo esc_html__('כל שדות ACF (כולל תמונות וקבצים)', 'text-domain'); ?></li>
                <li><?php echo esc_html__('כל קבצי המדיה (תמונות, קבצים מצורפים)', 'text-domain'); ?></li>
                <li><?php echo esc_html__('מטא-דאטה של טקסונומיות (איקונים וכו\')', 'text-domain'); ?></li>
            </ul>
            
            <h2><?php echo esc_html__('תצוגה מקדימה:', 'text-domain'); ?></h2>
            <ul>
                <?php
                $post_types = ['qa_updates', 'qa_orgs', 'qa_bib_items'];
                foreach ($post_types as $post_type) {
                    $count = wp_count_posts($post_type);
                    $total = isset($count->publish) ? intval($count->publish) : 0;
                    $total += isset($count->private) ? intval($count->private) : 0;
                    $total += isset($count->draft) ? intval($count->draft) : 0;
                    $total += isset($count->pending) ? intval($count->pending) : 0;
                    echo '<li><strong>' . esc_html($post_type) . ':</strong> ' . esc_html($total) . ' ' . esc_html__('פוסטים', 'text-domain') . '</li>';
                }
                
                $tags = get_terms(['taxonomy' => 'qa_tags', 'hide_empty' => false]);
                $themes = get_terms(['taxonomy' => 'qa_themes', 'hide_empty' => false]);
                echo '<li><strong>qa_tags:</strong> ' . (is_array($tags) ? count($tags) : 0) . ' ' . esc_html__('מונחים', 'text-domain') . '</li>';
                echo '<li><strong>qa_themes:</strong> ' . (is_array($themes) ? count($themes) : 0) . ' ' . esc_html__('מונחים', 'text-domain') . '</li>';
                ?>
            </ul>
        </div>
        
        <p>
            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=supervisor-export-content&download_export=1'), 'supervisor_export_content')); ?>" class="button button-primary button-large">
                <?php echo esc_html__('הורד קובץ ייצוא', 'text-domain'); ?>
            </a>
        </p>
        
        <div class="notice notice-info">
            <p><strong><?php echo esc_html__('הערה:', 'text-domain'); ?></strong> <?php echo esc_html__('קובץ הייצוא יכול להיות גדול אם יש הרבה תמונות. הורדת הקובץ עשויה לקחת זמן.', 'text-domain'); ?></p>
        </div>
    </div>
    <?php
}

// Add to admin menu
function supervisor_add_export_menu() {
    add_submenu_page(
        'supervisor-admin',
        __('ייצוא תוכן', 'text-domain'),
        __('ייצוא תוכן', 'text-domain'),
        'manage_options',
        'supervisor-export-content',
        'supervisor_export_content_page'
    );
}
add_action('admin_menu', 'supervisor_add_export_menu', 99);

