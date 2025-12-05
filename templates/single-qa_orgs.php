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
            // Use get_field() directly for Link fields to ensure proper formatting
            $org_link = get_field('qa_link');
            $org_report = get_field('qa_yearly_report');
            $org_ministry = $acf_fields['qa_gov_agency'] ?? '';
            
            // Debug: Display the structure of ACF Link fields (remove after debugging)
            // Temporarily show debug info on page
            if (isset($_GET['asd']) && current_user_can('manage_options')) {
                echo '<!-- DEBUG qa_link: ' . print_r($org_link, true) . ' -->';
                echo '<!-- DEBUG qa_yearly_report: ' . print_r($org_report, true) . ' -->';
                echo '<div style="background: #fff3cd; padding: 10px; margin: 10px 0; border: 1px solid #ffc107;">';
                echo '<strong>DEBUG INFO:</strong><br>';
                echo 'qa_link: <pre>' . print_r($org_link, true) . '</pre>';
                echo 'qa_yearly_report: <pre>' . print_r($org_report, true) . '</pre>';
                echo '</div>';
            }
            
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
                            // ACF Link fields can have 'title' key for the link text
                            // Check multiple possible keys for link text
                            $link_text = '';
                            if (!empty($org_link['title'])) {
                                $link_text = esc_html($org_link['title']);
                            } elseif (!empty($org_link['text'])) {
                                $link_text = esc_html($org_link['text']);
                            } elseif (!empty($org_link['label'])) {
                                $link_text = esc_html($org_link['label']);
                            }
                            
                            // If no title found, show a descriptive version of the URL
                            if (empty($link_text)) {
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
                            // ACF Link fields can have 'title' key for the link text
                            // Check multiple possible keys for link text
                            $report_text = '';
                            if (!empty($org_report['title'])) {
                                $report_text = esc_html($org_report['title']);
                            } elseif (!empty($org_report['text'])) {
                                $report_text = esc_html($org_report['text']);
                            } elseif (!empty($org_report['label'])) {
                                $report_text = esc_html($org_report['label']);
                            }
                            
                            // If no title found, show a descriptive version of the URL
                            if (empty($report_text)) {
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