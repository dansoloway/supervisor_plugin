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
                    the_title('<h1 class="page-title">', '</h1>');
                    echo apply_filters('the_content', get_the_content());

                    $about_context = get_queried_object_id();
                    $accordion_context = have_rows('qa_about_accordion', $about_context) ? $about_context : (have_rows('qa_about_accordion', 'option') ? 'option' : null);
                    if ($accordion_context !== null) :
                        $has_about_accordion = true;
                        ?>
                        <section class="about-accordion-section content-card-list" aria-label="<?php esc_attr_e('About accordion', 'supervisor-plugin'); ?>">
                            <?php
                            $row_index = 0;
                            while (have_rows('qa_about_accordion', $accordion_context)) : the_row();
                                $subtitle = get_sub_field('qa_about_subtitle');
                                $text    = get_sub_field('qa_about_text');
                                $accordion_id = 'about-' . $row_index;
                                $row_index++;
                                ?>
                                <div class="qa-update-item content-card">
                                    <div class="light-green-bkg accordion-header" data-accordion="<?php echo esc_attr($accordion_id); ?>">
                                        <div class="qa-update-title">
                                            <h3><?php echo esc_html($subtitle); ?></h3>
                                            <span class="accordion-icon" id="icon-<?php echo esc_attr($accordion_id); ?>">⌄</span>
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

<?php
if ($has_about_accordion) :
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function initializeAccordions() {
            const accordions = document.querySelectorAll('.about-content .accordion-header');
            accordions.forEach(function (header) {
                header.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const accordionId = this.getAttribute('data-accordion');
                    if (!accordionId) return;
                    const content = document.getElementById('accordion-' + accordionId);
                    const icon = document.getElementById('icon-' + accordionId);
                    if (!content || !icon) return;
                    const allHeaders = document.querySelectorAll('.about-content .accordion-header');
                    allHeaders.forEach(function (other) {
                        if (other !== header) {
                            const otherId = other.getAttribute('data-accordion');
                            if (otherId) {
                                const otherContent = document.getElementById('accordion-' + otherId);
                                const otherIcon = document.getElementById('icon-' + otherId);
                                if (otherContent && otherIcon) {
                                    otherContent.classList.remove('is-open');
                                    otherIcon.textContent = '⌄';
                                }
                            }
                        }
                    });
                    if (content.classList.contains('is-open')) {
                        content.classList.remove('is-open');
                        icon.textContent = '⌄';
                    } else {
                        content.classList.add('is-open');
                        icon.textContent = '⌃';
                    }
                });
            });
        }
        initializeAccordions();
    });
</script>
<?php
endif;
get_footer();
?>
