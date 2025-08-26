<?php
/* Template Name: Supervisor Single Organization */
get_header('supervisor'); 
?>
<div class="supervisor-home supervisor-single-org">
    <!-- Navigation Menu -->
    <?php
        $nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Main Content -->
    <div class="supervisor-main-content">
        <?php 
        while (have_posts()) : the_post();
            $acf_fields = get_fields();
            $org_title = get_the_title();
            $org_subtitle = $acf_fields['qa_subtitle'] ?? '';
            $org_country = $acf_fields['qa_country'] ?? '';
            $org_year = $acf_fields['qa_yearoffounding'] ?? '';
            $org_services = $acf_fields['qa_services_supervised'] ?? '';
            $org_link = $acf_fields['qa_link'] ?? '';
            $org_report = $acf_fields['qa_yearly_report'] ?? '';
            
            // Get taxonomy terms
            $terms = get_the_terms(get_the_ID(), 'qa_themes');
            $org_themes = $terms && !is_wp_error($terms)
                ? implode(', ', wp_list_pluck($terms, 'name'))
                : '';
        ?>
        
        <!-- Organization Title -->
        <div class="org-header">
            <h1 class="org-main-title"><?php echo esc_html($org_title); ?> - <?php echo esc_html($org_subtitle); ?></h1>
        </div>

        <!-- Information Boxes -->
        <div class="org-info-boxes">
            <div class="info-box left-box">
                <div class="info-item">
                    <span class="info-label">סטטוס:</span>
                    <span class="info-value">ארגון פעיל</span>
                </div>
                <div class="info-item">
                    <span class="info-label">אוכלוסיית יעד:</span>
                    <span class="info-value"><?php echo esc_html($org_services); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">מספר קטלוג:</span>
                    <span class="info-value"><?php echo esc_html($org_year); ?></span>
                </div>
            </div>
            
            <div class="info-box right-box">
                <div class="info-item">
                    <span class="info-label">חוקרים:</span>
                    <span class="info-value"><?php echo esc_html($org_themes); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">תחומים:</span>
                    <span class="info-value"><?php echo esc_html($org_services); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">שנת פרסום:</span>
                    <span class="info-value"><?php echo esc_html($org_year); ?></span>
                </div>
            </div>
        </div>

        <!-- General Text Section -->
        <div class="org-content">
            <h2 class="content-title">מלל כללי על הארגון</h2>
            <div class="content-text">
                <?php echo apply_filters('the_content', get_the_content()); ?>
            </div>
        </div>

        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>