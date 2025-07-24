<?php
/* Template Name: Supervisor Home */
get_header('supervisor');
?>

<div class="supervisor-home">

    <?php
        $search_bar_path = PLUGIN_ROOT . 'inc/top_search_bar.php';
        

        if (file_exists($search_bar_path)) {
            require_once $search_bar_path;
        } else {
            error_log('Search bar file not found: ' . $search_bar_path);
        }
    ?>

    <div class="supervisor-even-columns">

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

            <h3 class="updates-title"><a class="white" href="<?php echo get_the_permalink(SUPERVISOR_UPDATES)?>">לעדכונים נוספים</a>
            </h2>
        </div>

        <!-- Vertical Buttons -->
        <div class="vertical-buttons">
            <a class="vertical-button assistant-bold" href="<?php echo get_the_permalink(SUPERVISOR_INTRO_TEXT) ?>">פיקוח על שירותים חברתיים</a>
            <a class="vertical-button assistant-bold" href="<?php echo get_the_permalink(SUPERVISOR_ORGS) ?>">ארגוני פיקוח מן העולם</a>
            <a class="vertical-button assistant-bold" href="<?php echo get_the_permalink(SUPERVISOR_BIB_CATS) ?>">נושאי מפתח בתחום הפיקוח</a>
        </div>

    </div>

</div>

<?php
get_footer();
?>