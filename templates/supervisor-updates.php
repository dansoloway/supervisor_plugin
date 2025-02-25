<?php
/* Template Name: Supervisor QA Updates */
get_header('supervisor');

?>

<div class="supervisor-two-column">

    <!-- Right: AJAX Search Component -->
    <?php 
    require_once(WP_PLUGIN_DIR . '/supervisor-plugin/inc/search.php');
    ?>

    <!-- Middle: QA Updates List -->
    <div class="qa-updates-list">
        <?php
        $updates_query = new WP_Query([
            'post_type' => 'qa_updates',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        if ($updates_query->have_posts()) {
            while ($updates_query->have_posts()) {
                $updates_query->the_post();
                $post_id = get_the_ID();

                echo '<div class="qa-update-item">';
                

                // Taxonomy Items
                $themes = get_the_terms($post_id, 'qa_themes');
                $tags = get_the_terms($post_id, 'qa_tags');

                echo '<div class="taxonomy-boxes light-green-bkg">';
                if ($themes) {
                    foreach ($themes as $theme) {
                        echo '<span class="taxonomy-box theme-box box-color-'.esc_html($theme->term_taxonomy_id).'">' . esc_html($theme->name) . '</span>';
                    }
                }
                if ($tags) {
                    foreach ($tags as $tag) {
                        echo '<span class="taxonomy-box tag-box box-color-'.esc_html($tag->term_taxonomy_id).'">' . esc_html($tag->name) . '</span>';
                    }
                }
                echo '</div>'; // End of taxonomy-boxes

                echo '<div class="light-green-bkg">';
                    echo '<div class="qa-update-title">';
                    echo '<h3>' . get_the_title() . '</h3>';

                    // Accordion Toggle
                    echo '<button class="accordion-toggle" data-accordion="' . $post_id . '" data-state="collapsed">↓</button>';
                    echo '</div>'; // End of qa-update-title

                    // Accordion Content
                    echo '<div class="" id="accordion-' . $post_id . '" style="display: none;">';
                    echo '<p>' . get_the_content() . '</p>';

                    $link = get_field('external_link');
                    if ($link) {
                        echo '<a href="' . esc_url($link) . '" target="_blank">קרא עוד</a>';
                    }
                    echo '</div>'; // End of accordion-content

                    echo '</div>'; // End of qa-update-item
                echo '</div>'; 
            }
            wp_reset_postdata();
        } else {
            echo '<p>אין עדכונים זמינים</p>';
        }
        ?>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggles = document.querySelectorAll('.accordion-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function () {
                const accordionId = this.getAttribute('data-accordion');
                const accordionContent = document.getElementById('accordion-' + accordionId);
                if (accordionContent.style.display === 'none') {
                    accordionContent.style.display = 'block';
                } else {
                    accordionContent.style.display = 'none';
                }
            });
        });
    });
</script>

<?php
get_footer();
?>
