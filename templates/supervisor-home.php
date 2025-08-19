<?php
/* Template Name: Supervisor Home */
get_header('supervisor');
?>

<div class="supervisor-home">

    <!-- Navigation Menu -->
    <nav class="site-nav supervisor_header_links" aria-label="ראשי">
        <?php
            $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
            if (file_exists($nav_path)) {
                ob_start();
                require_once $nav_path;
                $nav_content = ob_get_clean();
                // Extract just the links from the navigation file
                preg_match_all('/<a[^>]*>.*?<\/a>/s', $nav_content, $matches);
                if (!empty($matches[0])) {
                    echo implode('', $matches[0]);
                }
            }
        ?>
    </nav>

    <!-- Introductory Text Block -->
    <div class="intro-text-block">
        <h1 class="intro-title">בקרת איכות בשירותים חברתיים</h1>
        <div class="intro-content">
            <p>מערכת בקרת האיכות בשירותים חברתיים נועדה להבטיח שהשירותים הניתנים לאזרחים עומדים בסטנדרטים הגבוהים ביותר של איכות, מקצועיות ואפקטיביות.</p>
            <p>המערכת כוללת כלים מתקדמים לניטור, הערכה ושיפור מתמיד של השירותים החברתיים בישראל.</p>
        </div>
    </div>

    <!-- Main Content Area - 2 Column Layout -->
    <div class="supervisor-main-content">
        
        <!-- Left Column: Knowledge Map Diagram -->
        <div class="knowledge-map-section">
            <div class="knowledge-map-card card">
                <h2 class="section-title">מפת ידע</h2>
                <div class="knowledge-map-diagram">
                    <img src="<?php echo plugins_url('assets/img/knowledge_map.png', dirname(__FILE__)); ?>" alt="מפת ידע - בקרת איכות" class="knowledge-map-image">
                </div>
            </div>
        </div>

        <!-- Right Column: Search and Updates -->
        <div class="center-content-section">
            <!-- Search Bar -->
            <div class="search-section">
                <?php
                    $search_bar_path = PLUGIN_ROOT . 'inc/top_search_bar.php';
                    
                    if (file_exists($search_bar_path)) {
                        require_once $search_bar_path;
                    } else {
                        error_log('Search bar file not found: ' . $search_bar_path);
                    }
                ?>
            </div>

            <!-- Updates Box -->
            <div class="updates-box card">
                <div class="updates-header">עדכונים</div>

                <div class="updates-list">
                    <?php
                    $updates_query = new WP_Query([
                        'post_type' => 'qa_updates',
                        'posts_per_page' => 5,
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

                <div class="updates-footer">
                    <a class="more-updates-button" href="<?php echo get_the_permalink(SUPERVISOR_UPDATES)?>">לעדכונים נוספים</a>
                </div>
            </div>
        </div>

    </div>

</div>

<?php
get_footer();
?>