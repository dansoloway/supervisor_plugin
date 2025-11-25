<?php
/**
 * Cleanup Test Content
 * 
 * Removes all test content except for one post per custom post type.
 * 
 * USAGE:
 * 1. Access via: /wp-admin/admin.php?page=supervisor-cleanup-test-content
 * 2. Or run directly: wp eval-file cleanup-test-content.php (WP-CLI)
 * 
 * WARNING: This will permanently delete posts. Make sure you have a backup!
 */

// Security check
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Only allow admins to run this
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have permission to access this page.', 'text-domain'));
}

// Custom post types to clean
$post_types = ['qa_updates', 'qa_orgs', 'qa_bib_items'];

// Function to cleanup a specific post type
function cleanup_post_type($post_type) {
    $posts = get_posts([
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'post_status' => 'any',
        'orderby' => 'date',
        'order' => 'DESC' // Keep the newest post
    ]);
    
    if (empty($posts)) {
        return [
            'kept' => 0,
            'deleted' => 0,
            'kept_title' => ''
        ];
    }
    
    // Keep the first (newest) post
    $keep_post = array_shift($posts);
    $kept_title = $keep_post->post_title;
    
    // Delete all others
    $deleted_count = 0;
    foreach ($posts as $post) {
        // Force delete (not just move to trash)
        wp_delete_post($post->ID, true);
        $deleted_count++;
    }
    
    return [
        'kept' => 1,
        'deleted' => $deleted_count,
        'kept_title' => $kept_title
    ];
}

// Admin page callback
function supervisor_cleanup_test_content_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to access this page.', 'text-domain'));
    }
    
    $results = [];
    $has_run = false;
    
    // Process cleanup if requested
    if (isset($_POST['run_cleanup']) && wp_verify_nonce($_POST['cleanup_nonce'], 'supervisor_cleanup_test_content')) {
        $has_run = true;
        $post_types = ['qa_updates', 'qa_orgs', 'qa_bib_items'];
        
        foreach ($post_types as $post_type) {
            $results[$post_type] = cleanup_post_type($post_type);
        }
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('ניקוי תוכן בדיקה', 'text-domain'); ?></h1>
        
        <div class="notice notice-warning">
            <p><strong><?php echo esc_html__('אזהרה:', 'text-domain'); ?></strong> <?php echo esc_html__('פעולה זו תמחק לצמיתות את כל התוכן מלבד פוסט אחד מכל סוג תוכן. ודא שיש לך גיבוי!', 'text-domain'); ?></p>
        </div>
        
        <?php if ($has_run): ?>
            <div class="notice notice-success">
                <h2><?php echo esc_html__('תוצאות ניקוי:', 'text-domain'); ?></h2>
                <?php
                foreach ($results as $post_type => $result) {
                    $post_type_labels = [
                        'qa_updates' => 'עדכונים',
                        'qa_orgs' => 'ארגונים',
                        'qa_bib_items' => 'פריטים ביבליוגרפיים'
                    ];
                    $label = $post_type_labels[$post_type] ?? $post_type;
                    echo '<p>';
                    echo '<strong>' . esc_html($label) . ':</strong> ';
                    echo esc_html__('נשמר', 'text-domain') . ': ' . $result['kept'] . ' (' . esc_html($result['kept_title']) . ') | ';
                    echo esc_html__('נמחק', 'text-domain') . ': ' . $result['deleted'];
                    echo '</p>';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="" onsubmit="return confirm('<?php echo esc_js(__('האם אתה בטוח שברצונך למחוק את כל תוכן הבדיקה? פעולה זו לא הפיכה!', 'text-domain')); ?>');">
            <?php wp_nonce_field('supervisor_cleanup_test_content', 'cleanup_nonce'); ?>
            <p>
                <?php echo esc_html__('פעולה זו תשאיר פוסט אחד (החדש ביותר) מכל סוג תוכן ותמחק את כל השאר:', 'text-domain'); ?>
            </p>
            <ul>
                <li><?php echo esc_html__('עדכונים (qa_updates)', 'text-domain'); ?></li>
                <li><?php echo esc_html__('ארגונים (qa_orgs)', 'text-domain'); ?></li>
                <li><?php echo esc_html__('פריטים ביבליוגרפיים (qa_bib_items)', 'text-domain'); ?></li>
            </ul>
            <p>
                <input type="submit" name="run_cleanup" class="button button-primary" value="<?php echo esc_attr__('הפעל ניקוי', 'text-domain'); ?>">
            </p>
        </form>
    </div>
    <?php
}

// Add to admin menu
function supervisor_add_cleanup_menu() {
    add_submenu_page(
        'supervisor-admin',
        __('ניקוי תוכן בדיקה', 'text-domain'),
        __('ניקוי תוכן בדיקה', 'text-domain'),
        'manage_options',
        'supervisor-cleanup-test-content',
        'supervisor_cleanup_test_content_page'
    );
}
add_action('admin_menu', 'supervisor_add_cleanup_menu', 99);

