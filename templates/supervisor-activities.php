<?php
/**
 * Template for Supervisor Activities Page
 * Displays three main activity areas: Conferences, Distribution, and Workshops
 */

get_header('supervisor');
?>

<div class="supervisor-home">

    <!-- Navigation Menu -->
    <?php
        $nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">
        
        <!-- Main Content Area -->
        <div class="supervisor-content-wrapper supervisor-single-column">
        
        <!-- Activities Grid -->
        <div class="activities-grid">
            <?php
            // Get ACF repeater field data
            if (have_rows('qa_areas_of_activity')):
                while (have_rows('qa_areas_of_activity')): the_row();
                    $title = get_sub_field('qa_areas_of_activity_title');
                    $icon = get_sub_field('qa_areas_of_activity_icon');
                    $content = get_sub_field('qa_areas_of_activity_content');
                    
                    // Ensure we have content to display
                    if ($title && $content):
            ?>
                <div class="activity-card">
                    <div class="activity-icon">
                        <?php if ($icon): ?>
                            <i class="<?php echo esc_attr($icon); ?>"></i>
                        <?php endif; ?>
                        <h2 class="activity-title"><?php echo esc_html($title); ?></h2>
                    </div>
                    <div class="activity-content">
                        <p><?php echo esc_html($content); ?></p>
                    </div>
                </div>
            <?php 
                    endif;
                endwhile;
            else:
                // Fallback content if no ACF data is available
                echo '<p class="no-activities">אין פעילויות להצגה. אנא הוסף פעילויות באמצעות שדות ACF.</p>';
            endif;
            ?>
        </div>

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>


<?php get_footer(); ?>
