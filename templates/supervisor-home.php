<?php
/* Template Name: Supervisor Home */
get_header('supervisor');


// // Enqueue the plugin CSS
// wp_enqueue_style(
//     'supervisor-styles',
//     plugins_url('assets/css/supervisor-styles.css', dirname(__FILE__)),
//     [],
//     '1.0'
// );
?>

<div class="supervisor-home">

    <!-- Text Search Bar -->
    <div class="supervisor-search">
        <input type="text" placeholder="חפש..." class="supervisor-search-bar" />
    </div>

    <div class="supervisor-content">

        <!-- Updates Box -->
        <div class="updates-box">
            <h2 class="updates-title">עדכונים</h2>

            <div class="updates-list">
                <?php
                $updates_query = new WP_Query([
                    'post_type' => 'qa_updates',
                    'posts_per_page' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ]);

                if ($updates_query->have_posts()) {
                    while ($updates_query->have_posts()) {
                        $updates_query->the_post();

                        $raw_date = get_field('qa_updates_date'); // ACF date field
                        $formatted_date = $raw_date ? date_i18n('F Y', strtotime($raw_date)) : '';

                        echo '<div class="update-item">';
                        echo '<h3 class="update-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
                        if ($formatted_date) {
                            echo '<p class="update-date">' . esc_html($formatted_date) . '</p>';
                        }
                        echo '</div>';
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p class="no-updates">אין עדכונים זמינים</p>';
                }
                ?>
            </div>

            <a href="<?php echo get_post_type_archive_link('qa_updates'); ?>" class="more-updates-link">לעדכונים נוספים</a>
        </div>

        <!-- Vertical Buttons -->
        <div class="vertical-buttons">
            <button class="vertical-button" id="button-1">לחצן 1</button>
            <button class="vertical-button" id="button-2">לחצן 2</button>
            <button class="vertical-button" id="button-3">לחצן 3</button>
        </div>

    </div>

</div>

<?php
get_footer();
?>