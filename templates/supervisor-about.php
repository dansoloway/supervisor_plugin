<?php
/**
 * Template for Supervisor About Page
 * Renders page content and an ACF-driven accordion (qa_about_accordion repeater).
 */
get_header('supervisor');
?>

<div class="supervisor-home supervisor-about-page">
    <!-- Navigation Menu -->
    <?php
        $nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">

        <div class="supervisor-content-wrapper supervisor-single-column">
            <div class="content-main about-content">
                <?php
                $has_about_accordion = false;
                while (have_posts()) : the_post();

                    // Render the page body text before the accordion (if any).
                    $page_content = trim((string) get_the_content());
                    if ($page_content !== '') :
                        ?>
                        <div class="about-page-content entry-content">
                            <?php the_content(); ?>
                        </div>
                        <?php
                    endif;

                    $about_context = get_queried_object_id();
                    $accordion_context = have_rows('qa_about_accordion', $about_context) ? $about_context : (have_rows('qa_about_accordion', 'option') ? 'option' : null);
                    if ($accordion_context !== null) :
                        $has_about_accordion = true;
                        ?>
                        <section class="about-accordion-section" aria-label="<?php esc_attr_e('About accordion', 'supervisor-plugin'); ?>">
                            <?php
                            $row_index = 0;
                            while (have_rows('qa_about_accordion', $accordion_context)) : the_row();
                                $subtitle = get_sub_field('qa_about_subtitle');
                                $text    = get_sub_field('qa_about_text');
                                $accordion_id = 'about-' . $row_index;
                                $row_index++;
                                ?>
                                <div class="qa-update-item about-accordion-item">
                                    <div class="accordion-header" data-accordion="<?php echo esc_attr($accordion_id); ?>">
                                        <div class="qa-update-title">
                                            <h2><?php echo esc_html($subtitle); ?></h2>
                                            <?php echo supervisor_accordion_icon_markup($accordion_id, false); ?>
                                        </div>
                                    </div>
                                    <div class="accordion-content" id="accordion-<?php echo esc_attr($accordion_id); ?>">
                                        <div class="update-content-text">
                                            <?php echo wp_kses_post($text); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </section>
                    <?php endif;
                endwhile;
                ?>
            </div>
        </div>

    </div> <!-- End supervisor-page-container -->

</div>

<?php get_footer(); ?>
