<?php
/* Template Name: Supervisor Home */
get_header('supervisor');
?>

<div class="supervisor-home">

    <!-- Navigation Menu -->
    <?php
        $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container supervisor-home-page">
        
        <!-- Hero / Welcome Section -->
        <header class="home-hero">
            <h1 class="home-hero-title"><?php echo esc_html(get_the_title() ?: 'המפקחת'); ?></h1>
            <div class="intro-text-block">
                <div class="intro-content">
                    <p>מערכת בקרת האיכות בשירותים חברתיים נועדה להבטיח שהשירותים הניתנים לאזרחים עומדים בסטנדרטים הגבוהים ביותר של איכות, מקצועיות ואפקטיביות.</p>
                    <p>המערכת כוללת כלים מתקדמים לניטור, הערכה ושיפור מתמיד של השירותים החברתיים בישראל.</p>
                </div>
            </div>
        </header>

        <!-- Knowledge Map Section -->
        <section class="knowledge-map-section" aria-labelledby="knowledge-map-heading">
            <h2 id="knowledge-map-heading" class="home-section-title">מפת הידע</h2>
            <div class="knowledge-map-card card">
                <div class="knowledge-map-diagram">
                    <a href="<?php echo get_permalink(SUPERVISOR_KNOWLEDGE_MAP); ?>" class="knowledge-map-image-link">
                        <img src="<?php echo plugins_url('assets/img/knowledge_map.svg', dirname(__FILE__)); ?>" alt="מפת ידע - בקרת איכות" class="knowledge-map-image">
                    </a>
                </div>
                <div class="knowledge-map-footer">
                    <a href="<?php echo get_permalink(SUPERVISOR_KNOWLEDGE_MAP); ?>" class="knowledge-map-button">למידע נוסף על מפת הידע</a>
                </div>
            </div>
        </section>

        <!-- Main Content Area - Search and Updates -->
        <div class="supervisor-content-wrapper supervisor-single-column home-main-content">
            <!-- Search Section -->
            <section class="home-search-section" aria-labelledby="home-search-heading">
                <h2 id="home-search-heading" class="home-section-title visually-hidden">חיפוש</h2>
                <div class="search-section">
                    <form class="supervisor-search home-search-form" role="search" action="<?php echo home_url('/supervisor-search/'); ?>">
                        <span class="search-input-wrapper">
                            <input type="search" name="supervisor_search" placeholder="חיפוש" class="supervisor-search-bar" aria-label="שדה חיפוש">
                            <button type="submit" class="search-button" aria-label="חיפוש">
                                <svg class="search-icon" width="20" height="20" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="13" cy="13" r="9" stroke="white" stroke-width="4"></circle>
                                    <line x1="19" y1="19" x2="28" y2="28" stroke="white" stroke-width="4"></line>
                                </svg>
                            </button>
                        </span>
                    </form>
                </div>
            </section>

            <!-- Updates Section -->
            <section class="home-updates-section" aria-labelledby="home-updates-heading">
                <h2 id="home-updates-heading" class="home-section-title">עדכונים</h2>
                <div class="updates-box card home-updates-card">
                    <div class="updates-list">
                        <?php
                        $updates_query = new WP_Query([
                            'post_type' => 'qa_updates',
                            'posts_per_page' => 5,
                            'orderby' => 'date',
                            'order' => 'DESC',
                            'post_status' => 'publish',
                        ]);

                        if ($updates_query->have_posts()) {
                            while ($updates_query->have_posts()) {
                                $updates_query->the_post();

                                $raw_date = get_field('qa_updates_date'); // ACF date field
                                if ($raw_date) {
                                    $formatted_date = date_i18n('F Y', strtotime($raw_date));
                                } else {
                                    $formatted_date = get_the_date('F Y');
                                }

                                $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20);

                                echo '<article class="update-item home-update-card">';
                                echo '<div class="update-content">';
                                echo '<div class="update-text">';
                                echo '<h3 class="update-title"><a href="' . esc_url(get_permalink(SUPERVISOR_UPDATES) . '?highlight=' . get_the_ID()) . '">' . get_the_title() . '</a></h3>';
                                echo '<p class="update-date">' . esc_html($formatted_date) . '</p>';
                                if ($excerpt) {
                                    echo '<p class="update-excerpt">' . esc_html($excerpt) . '</p>';
                                }
                                echo '</div>';
                                echo '</div>';
                                echo '</article>';
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<p class="no-updates">אין עדכונים זמינים</p>';
                        }
                        ?>
                    </div>

                    <div class="updates-footer-link">
                        <a class="more-updates-button" href="<?php echo esc_url(get_the_permalink(SUPERVISOR_UPDATES)); ?>">לעדכונים נוספים</a>
                    </div>
                </div>
            </section>
        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->

</div>

<?php
get_footer();
?>