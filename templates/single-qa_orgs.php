<?php
/* Template Name: Supervisor Content */
get_header('supervisor'); 
?>
<div class="supervisor-container">
    <div class="supervisor-two-column">
        <!-- Sidebar Section -->
        <div>
        <?php
            $sidebar_path = trailingslashit(dirname(__FILE__, 2)) . 'inc/sidebar.php';

            if (file_exists($sidebar_path)) {
                require_once $sidebar_path;
            } else {
                error_log('Sidebar file not found: ' . $sidebar_path);
            }
            ?>
            
            </div>
            <div>
                <?php 
                while (have_posts()) : the_post();

                    $acf_fields = get_fields();
                    echo '<h1 class="dir_left" style="margin-top:0px">' . esc_html(get_the_title()) . ' - '.$acf_fields['qa_subtitle'].'</h1>';
                    
                    // Define the ACF fields with their corresponding display names
                    $acf_field_labels = [
                        'qa_yearoffounding' => 'שנת הקמה',
                        'qa_gov_agency' => 'משרד ממשלתי אחראי',
                        'qa_link' => 'אתר הארגון',
                        'qa_services_supervised' => 'שירותים מפוקחים',
                        'qa_yearly_report' => 'דוח שנתי',
                    ];

                    // Get all ACF field values
                    

                    // Check if any fields exist before rendering the div
                    $non_empty_fields = array_filter($acf_fields);

                    if (!empty($non_empty_fields)) : ?>
                        <div class="acf-fields-grid">
                            <?php 
                            // Loop through fields and display only non-empty ones
                            foreach ($acf_field_labels as $key => $label) {
                                if (!empty($acf_fields[$key])) {
                                    echo '<div class="acf-field"><strong>' . esc_html($label) . ':</strong> ';
                                    // Handle links separately
                                    if ($key === 'qa_link' || $key === 'qa_yearly_report') {
                                        echo '<a href="' . esc_url($acf_fields[$key]) . '" target="_blank">' . esc_html($acf_fields[$key]) . '</a>';
                                    } else {
                                        echo esc_html($acf_fields[$key]);
                                    }
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    <?php endif; 
                endwhile;
                ?>

                <?php echo '<div>' . apply_filters('the_content', get_the_content()) . '</div>'; ?>
            </div>
        
    </div>
</div>

<?php get_footer(); ?>