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

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">
        
        <!-- Main Content -->
        <div class="">
        <?php 
        while (have_posts()) : the_post();
            $acf_fields = get_fields();
            $org_title = get_the_title();
            $org_year = $acf_fields['qa_yearoffounding'] ?? '';
            $org_services = $acf_fields['qa_services_supervised'] ?? '';
            $org_link = $acf_fields['qa_link'] ?? '';
            $org_report = $acf_fields['qa_yearly_report'] ?? '';
            $org_ministry = $acf_fields['qa_gov_agency'] ?? '';
            
            // Normalize link values for comparison (handle both array and string formats)
            $org_link_value = is_array($org_link) && isset($org_link['url']) ? $org_link['url'] : $org_link;
            $org_report_value = is_array($org_report) && isset($org_report['url']) ? $org_report['url'] : $org_report;
            
            // Check if both fields have the same value (potential ACF config issue)
            if ($org_link_value && $org_report_value && $org_link_value === $org_report_value) {
                error_log('WARNING: qa_link and qa_yearly_report have the same value for post ID: ' . get_the_ID() . ' - This suggests an ACF field configuration issue.');
            }
            
            // Get taxonomy terms
            $terms = get_the_terms(get_the_ID(), 'qa_themes');
            $org_themes = $terms && !is_wp_error($terms)
                ? implode(', ', wp_list_pluck($terms, 'name'))
                : '';
        ?>
        
        <!-- Organization Title -->
        <div class="org-header">
            <h1 class="org-main-title"><?php echo esc_html($org_title); ?></h1>
        </div>

        <!-- Information Boxes -->
        <div class="org-info-boxes">
            <div class="info-box left-box">
                <?php if ($org_year): ?>
                <div class="info-item">
                    <span class="info-label">שנת הקמה:</span>
                    <span class="info-value"><?php echo esc_html($org_year); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($org_ministry): ?>
                <div class="info-item">
                    <span class="info-label">משרד ממשלתי אחראי:</span>
                    <span class="info-value"><?php echo esc_html($org_ministry); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($org_link): ?>
                <div class="info-item">
                    <span class="info-label">אתר הארגון:</span>
                    <span class="info-value">
                        <?php
                        // Handle ACF link field (array) or plain URL (string)
                        if (is_array($org_link) && isset($org_link['url'])) {
                            $link_url = esc_url($org_link['url']);
                            // Use title if available, otherwise show a descriptive version of the URL
                            if (!empty($org_link['title'])) {
                                $link_text = esc_html($org_link['title']);
                            } else {
                                // Show full URL or hostname + path for better distinction
                                $parsed_url = parse_url($org_link['url']);
                                $link_text = !empty($parsed_url['host']) 
                                    ? esc_html($parsed_url['host'] . (!empty($parsed_url['path']) && $parsed_url['path'] !== '/' ? $parsed_url['path'] : ''))
                                    : esc_html($org_link['url']);
                            }
                        } else {
                            $link_url = esc_url($org_link);
                            // Extract domain name from URL for readability
                            $parsed_url = parse_url($link_url);
                            $link_text = !empty($parsed_url['host']) 
                                ? esc_html($parsed_url['host'] . (!empty($parsed_url['path']) && $parsed_url['path'] !== '/' ? $parsed_url['path'] : ''))
                                : esc_html($link_url);
                        }
                        ?>
                        <a href="<?php echo $link_url; ?>" target="_blank" rel="noopener noreferrer" style="color: blue !important; text-align: left; direction: ltr;">
                            <?php echo $link_text; ?>
                        </a>
                    </span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="info-box right-box">
                <?php if ($org_services): ?>
                <div class="info-item">
                    <span class="info-label">שירותים מפוקחים:</span>
                    <span class="info-value"><?php echo esc_html($org_services); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($org_report): ?>
                <div class="info-item">
                    <span class="info-label">דוח שנתי:</span>
                    <span class="info-value">
                        <?php
                        // Handle ACF link field (array) or plain URL (string)
                        if (is_array($org_report) && isset($org_report['url'])) {
                            $report_url = esc_url($org_report['url']);
                            // Use title if available, otherwise show a descriptive version of the URL
                            if (!empty($org_report['title'])) {
                                $report_text = esc_html($org_report['title']);
                            } else {
                                // Show full URL or hostname + path for better distinction
                                $parsed_url = parse_url($org_report['url']);
                                $report_text = !empty($parsed_url['host']) 
                                    ? esc_html($parsed_url['host'] . (!empty($parsed_url['path']) && $parsed_url['path'] !== '/' ? $parsed_url['path'] : ''))
                                    : esc_html($org_report['url']);
                            }
                        } else {
                            $report_url = esc_url($org_report);
                            // Extract domain name from URL for readability
                            $parsed_url = parse_url($report_url);
                            $report_text = !empty($parsed_url['host']) 
                                ? esc_html($parsed_url['host'] . (!empty($parsed_url['path']) && $parsed_url['path'] !== '/' ? $parsed_url['path'] : ''))
                                : esc_html($report_url);
                        }
                        ?>
                        <a href="<?php echo $report_url; ?>" target="_blank" rel="noopener noreferrer" style="color: blue !important; text-align: left; direction: ltr;">
                            <?php echo $report_text; ?>
                        </a>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- General Text Section -->
        <div class="org-content">
            <!-- <h2 class="content-title">מלל כללי על הארגון</h2> -->
            <div class="content-text">
                <?php echo apply_filters('the_content', get_the_content()); ?>
            </div>
        </div>

        <?php endwhile; ?>
        
        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>