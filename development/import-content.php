<?php
/**
 * Import Supervisor Plugin Content
 * 
 * Imports content from a JSON export file created by export-content.php
 * 
 * USAGE:
 * 1. Access via: /wp-admin/admin.php?page=supervisor-import-content
 * 2. Upload the JSON export file
 * 3. Review and confirm import
 * 
 * WARNING: This will create new content. Duplicates are detected by post slug.
 */

// Security check
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Import results storage
$import_results = [
    'posts_created' => 0,
    'posts_skipped' => 0,
    'taxonomies_created' => 0,
    'terms_created' => 0,
    'terms_skipped' => 0,
    'media_created' => 0,
    'media_skipped' => 0,
    'errors' => []
];

/**
 * Import content from JSON file
 */
function supervisor_import_content($json_file_path, $options = []) {
    global $import_results;
    
    // Default options
    $defaults = [
        'skip_duplicates' => true,
        'skip_existing_media' => true,
        'update_existing' => false
    ];
    $options = wp_parse_args($options, $defaults);
    
    // Read and parse JSON file
    $json_content = file_get_contents($json_file_path);
    $export_data = json_decode($json_content, true);
    
    if (!$export_data || !isset($export_data['posts']) || !isset($export_data['taxonomies'])) {
        $import_results['errors'][] = 'Invalid export file format.';
        return false;
    }
    
    // ID mapping for relationships
    $id_mapping = [
        'posts' => [],
        'terms' => [],
        'media' => []
    ];
    
    // Step 1: Import taxonomies and terms first
    foreach ($export_data['taxonomies'] as $taxonomy => $terms) {
        foreach ($terms as $term_data) {
            $existing_term = get_term_by('slug', $term_data['slug'], $taxonomy);
            
            if ($existing_term && $options['skip_duplicates']) {
                $id_mapping['terms'][$term_data['term_taxonomy_id']] = $existing_term->term_id;
                $import_results['terms_skipped']++;
                continue;
            }
            
            // Prepare term args
            $term_args = [
                'description' => $term_data['description'] ?? '',
                'slug' => $term_data['slug']
            ];
            
            // Handle parent term
            if (!empty($term_data['parent']) && isset($id_mapping['terms'][$term_data['parent']])) {
                $term_args['parent'] = $id_mapping['terms'][$term_data['parent']];
            }
            
            // Insert or update term
            if ($existing_term && $options['update_existing']) {
                $result = wp_update_term($existing_term->term_id, $taxonomy, $term_args);
            } else {
                $result = wp_insert_term($term_data['name'], $taxonomy, $term_args);
            }
            
            if (!is_wp_error($result)) {
                $new_term_id = is_array($result) ? $result['term_id'] : $result;
                $id_mapping['terms'][$term_data['term_taxonomy_id']] = $new_term_id;
                
                // Import term meta
                if (isset($term_data['meta']) && !empty($term_data['meta'])) {
                    foreach ($term_data['meta'] as $meta_key => $meta_value) {
                        update_term_meta($new_term_id, $meta_key, $meta_value);
                    }
                }
                
                $import_results['terms_created']++;
                $import_results['taxonomies_created']++;
            } else {
                $import_results['errors'][] = 'Error creating term: ' . $term_data['name'] . ' - ' . $result->get_error_message();
            }
        }
    }
    
    // Step 2: Import media files
    $media_cache = [];
    foreach ($export_data['media'] as $media_data) {
        if (!$media_data) continue;
        
        // Check if media already exists by filename
        $existing_attachment = get_posts([
            'post_type' => 'attachment',
            'post_status' => 'any',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => '_wp_attached_file',
                    'value' => basename($media_data['file_path']),
                    'compare' => 'LIKE'
                ]
            ]
        ]);
        
        if (!empty($existing_attachment) && $options['skip_existing_media']) {
            $id_mapping['media'][$media_data['ID']] = $existing_attachment[0]->ID;
            $import_results['media_skipped']++;
            continue;
        }
        
        // Import media file
        $attachment_id = supervisor_import_attachment($media_data);
        if ($attachment_id) {
            $id_mapping['media'][$media_data['ID']] = $attachment_id;
            $media_cache[$media_data['ID']] = $attachment_id;
            $import_results['media_created']++;
        } else {
            $import_results['errors'][] = 'Error importing media: ' . ($media_data['filename'] ?? 'Unknown');
        }
    }
    
    // Step 3: Import posts
    foreach ($export_data['posts'] as $post_data) {
        // Check for duplicates by post slug
        $existing_post = get_page_by_path($post_data['post_name'], OBJECT, $post_data['post_type']);
        
        if ($existing_post && $options['skip_duplicates'] && !$options['update_existing']) {
            $id_mapping['posts'][$post_data['ID'] ?? 0] = $existing_post->ID;
            $import_results['posts_skipped']++;
            continue;
        }
        
        // Prepare post data
        $post_args = [
            'post_title' => $post_data['post_title'],
            'post_content' => $post_data['post_content'] ?? '',
            'post_excerpt' => $post_data['post_excerpt'] ?? '',
            'post_status' => $post_data['post_status'] ?? 'publish',
            'post_type' => $post_data['post_type'],
            'post_name' => $post_data['post_name'] ?? sanitize_title($post_data['post_title']),
            'post_date' => $post_data['post_date'] ?? current_time('mysql'),
            'post_date_gmt' => $post_data['post_date_gmt'] ?? get_gmt_from_date($post_data['post_date'] ?? current_time('mysql')),
            'comment_status' => $post_data['comment_status'] ?? 'closed',
            'ping_status' => $post_data['ping_status'] ?? 'closed',
            'menu_order' => $post_data['menu_order'] ?? 0
        ];
        
        // Insert or update post
        if ($existing_post && $options['update_existing']) {
            $post_args['ID'] = $existing_post->ID;
            $post_id = wp_update_post($post_args, true);
        } else {
            $post_id = wp_insert_post($post_args, true);
        }
        
        if (is_wp_error($post_id)) {
            $import_results['errors'][] = 'Error creating post: ' . $post_data['post_title'] . ' - ' . $post_id->get_error_message();
            continue;
        }
        
        $id_mapping['posts'][$post_data['ID'] ?? 0] = $post_id;
        
        // Assign taxonomies
        if (isset($post_data['taxonomies']) && !empty($post_data['taxonomies'])) {
            foreach ($post_data['taxonomies'] as $taxonomy => $terms) {
                $term_ids = [];
                foreach ($terms as $term_info) {
                    $term = get_term_by('slug', $term_info['slug'], $taxonomy);
                    if ($term) {
                        $term_ids[] = $term->term_id;
                    }
                }
                if (!empty($term_ids)) {
                    wp_set_object_terms($post_id, $term_ids, $taxonomy);
                }
            }
        }
        
        // Import ACF fields
        if (isset($post_data['acf_fields']) && !empty($post_data['acf_fields']) && function_exists('update_field')) {
            supervisor_import_acf_fields($post_data['acf_fields'], $post_id, $id_mapping, $media_cache);
        }
        
        // Import featured image
        if (!empty($post_data['featured_image']) && isset($post_data['featured_image']['attachment_data'])) {
            $old_attachment_id = $post_data['featured_image']['attachment_data']['ID'];
            if (isset($id_mapping['media'][$old_attachment_id])) {
                set_post_thumbnail($post_id, $id_mapping['media'][$old_attachment_id]);
            }
        }
        
        // Import post meta
        if (isset($post_data['meta']) && !empty($post_data['meta'])) {
            foreach ($post_data['meta'] as $meta_key => $meta_value) {
                // Skip ACF meta keys (they start with underscore and are handled separately)
                if (strpos($meta_key, '_') === 0 && $meta_key !== '_thumbnail_id') {
                    continue;
                }
                update_post_meta($post_id, $meta_key, $meta_value);
            }
        }
        
        $import_results['posts_created']++;
    }
    
    return true;
}

/**
 * Import an attachment/media file
 */
function supervisor_import_attachment($attachment_data) {
    if (empty($attachment_data['file_base64']) || empty($attachment_data['filename'])) {
        return false;
    }
    
    // Decode base64 file
    $file_contents = base64_decode($attachment_data['file_base64']);
    if (!$file_contents) {
        return false;
    }
    
    // Get upload directory
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['path'] . '/' . $attachment_data['filename'];
    $file_url = $upload_dir['url'] . '/' . $attachment_data['filename'];
    
    // Write file
    if (file_put_contents($file_path, $file_contents) === false) {
        return false;
    }
    
    // Create attachment post
    $attachment_args = [
        'post_mime_type' => $attachment_data['post_mime_type'] ?? 'application/octet-stream',
        'post_title' => $attachment_data['post_title'] ?? sanitize_file_name($attachment_data['filename']),
        'post_content' => $attachment_data['post_content'] ?? '',
        'post_excerpt' => $attachment_data['post_excerpt'] ?? '',
        'post_status' => 'inherit',
        'guid' => $file_url
    ];
    
    $attachment_id = wp_insert_attachment($attachment_args, $file_path);
    
    if (is_wp_error($attachment_id)) {
        return false;
    }
    
    // Generate attachment metadata
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
    
    // Merge with original metadata if available
    if (isset($attachment_data['meta']) && is_array($attachment_data['meta'])) {
        $attach_data = array_merge($attach_data, $attachment_data['meta']);
    }
    
    wp_update_attachment_metadata($attachment_id, $attach_data);
    
    return $attachment_id;
}

/**
 * Import ACF fields with proper handling of relationships
 */
function supervisor_import_acf_fields($acf_fields, $post_id, $id_mapping, $media_cache) {
    foreach ($acf_fields as $field_key => $field_value) {
        if (empty($field_value)) {
            update_field($field_key, $field_value, $post_id);
            continue;
        }
        
        // Handle special field types
        if (is_array($field_value) && isset($field_value['type'])) {
            switch ($field_value['type']) {
                case 'image_id':
                    if (isset($field_value['attachment_data']['ID']) && isset($id_mapping['media'][$field_value['attachment_data']['ID']])) {
                        update_field($field_key, $id_mapping['media'][$field_value['attachment_data']['ID']], $post_id);
                    }
                    break;
                    
                case 'file_id':
                    if (isset($field_value['attachment_data']['ID']) && isset($id_mapping['media'][$field_value['attachment_data']['ID']])) {
                        update_field($field_key, $id_mapping['media'][$field_value['attachment_data']['ID']], $post_id);
                    }
                    break;
                    
                case 'gallery':
                    if (isset($field_value['images']) && is_array($field_value['images'])) {
                        $image_ids = [];
                        foreach ($field_value['images'] as $img) {
                            if (isset($img['ID']) && isset($id_mapping['media'][$img['ID']])) {
                                $image_ids[] = $id_mapping['media'][$img['ID']];
                            }
                        }
                        if (!empty($image_ids)) {
                            update_field($field_key, $image_ids, $post_id);
                        }
                    }
                    break;
                    
                case 'relationship':
                case 'post_object':
                    if (isset($field_value['posts']) && is_array($field_value['posts'])) {
                        $post_ids = [];
                        foreach ($field_value['posts'] as $related_post) {
                            if (isset($related_post['ID']) && isset($id_mapping['posts'][$related_post['ID']])) {
                                $post_ids[] = $id_mapping['posts'][$related_post['ID']];
                            }
                        }
                        if (!empty($post_ids)) {
                            update_field($field_key, is_array($field_value['posts']) && count($field_value['posts']) === 1 ? $post_ids[0] : $post_ids, $post_id);
                        }
                    }
                    break;
                    
                case 'taxonomy':
                    // Taxonomies are already handled during post import
                    break;
                    
                default:
                    update_field($field_key, $field_value, $post_id);
            }
        } else {
            // Regular field value
            update_field($field_key, $field_value, $post_id);
        }
    }
}

// Admin page callback
function supervisor_import_content_page() {
    global $import_results;
    
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to access this page.', 'text-domain'));
    }
    
    // Handle file upload
    $import_complete = false;
    $upload_error = '';
    
    if (isset($_POST['upload_import']) && wp_verify_nonce($_POST['import_nonce'], 'supervisor_import_content')) {
        if (!empty($_FILES['import_file']['tmp_name'])) {
            $file_path = $_FILES['import_file']['tmp_name'];
            $file_name = $_FILES['import_file']['name'];
            
            // Validate file type
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if ($file_ext !== 'json') {
                $upload_error = 'Invalid file type. Please upload a JSON file.';
            } else {
                // Get import options
                $options = [
                    'skip_duplicates' => isset($_POST['skip_duplicates']),
                    'skip_existing_media' => isset($_POST['skip_existing_media']),
                    'update_existing' => isset($_POST['update_existing'])
                ];
                
                // Run import
                $success = supervisor_import_content($file_path, $options);
                $import_complete = true;
            }
        } else {
            $upload_error = 'No file uploaded.';
        }
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ייבוא תוכן המקפחת', 'text-domain'); ?></h1>
        
        <?php if ($upload_error): ?>
            <div class="notice notice-error">
                <p><?php echo esc_html($upload_error); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($import_complete): ?>
            <div class="notice notice-success">
                <h2><?php echo esc_html__('תוצאות ייבוא:', 'text-domain'); ?></h2>
                <ul>
                    <li><?php echo esc_html__('פוסטים שנוצרו', 'text-domain'); ?>: <?php echo $import_results['posts_created']; ?></li>
                    <li><?php echo esc_html__('פוסטים שנדלגו (כפילויות)', 'text-domain'); ?>: <?php echo $import_results['posts_skipped']; ?></li>
                    <li><?php echo esc_html__('מונחים שנוצרו', 'text-domain'); ?>: <?php echo $import_results['terms_created']; ?></li>
                    <li><?php echo esc_html__('מונחים שנדלגו', 'text-domain'); ?>: <?php echo $import_results['terms_skipped']; ?></li>
                    <li><?php echo esc_html__('קבצי מדיה שנוצרו', 'text-domain'); ?>: <?php echo $import_results['media_created']; ?></li>
                    <li><?php echo esc_html__('קבצי מדיה שנדלגו', 'text-domain'); ?>: <?php echo $import_results['media_skipped']; ?></li>
                </ul>
                <?php if (!empty($import_results['errors'])): ?>
                    <h3><?php echo esc_html__('שגיאות:', 'text-domain'); ?></h3>
                    <ul>
                        <?php foreach ($import_results['errors'] as $error): ?>
                            <li><?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="notice notice-warning">
                <p><strong><?php echo esc_html__('אזהרה:', 'text-domain'); ?></strong> <?php echo esc_html__('פעולה זו תייבא תוכן מהקובץ. כפילויות יזוהו לפי slug של הפוסט ונדלגו.', 'text-domain'); ?></p>
            </div>
            
            <form method="post" enctype="multipart/form-data" action="">
                <?php wp_nonce_field('supervisor_import_content', 'import_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="import_file"><?php echo esc_html__('קובץ ייצוא (JSON)', 'text-domain'); ?></label>
                        </th>
                        <td>
                            <input type="file" name="import_file" id="import_file" accept=".json" required>
                            <p class="description"><?php echo esc_html__('בחר את קובץ הייצוא שנוצר בעמוד ייצוא התוכן.', 'text-domain'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__('אפשרויות', 'text-domain'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="skip_duplicates" value="1" checked>
                                <?php echo esc_html__('דלג על כפילויות (לפי slug)', 'text-domain'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="skip_existing_media" value="1" checked>
                                <?php echo esc_html__('דלג על קבצי מדיה קיימים', 'text-domain'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="update_existing" value="1">
                                <?php echo esc_html__('עדכן פוסטים קיימים במקום לדלג', 'text-domain'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="upload_import" class="button button-primary" value="<?php echo esc_attr__('התחל ייבוא', 'text-domain'); ?>">
                </p>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

// Add to admin menu
function supervisor_add_import_menu() {
    add_submenu_page(
        'supervisor-admin',
        __('ייבוא תוכן', 'text-domain'),
        __('ייבוא תוכן', 'text-domain'),
        'manage_options',
        'supervisor-import-content',
        'supervisor_import_content_page'
    );
}
add_action('admin_menu', 'supervisor_add_import_menu', 99);

