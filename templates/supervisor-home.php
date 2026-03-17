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

        <!-- Two-column layout: main (left) + sidebar (right) - RTL -->
        <div class="supervisor-content-wrapper supervisor-two-column home-two-column">
            <!-- Sidebar (right in RTL): Search + Updates -->
            <aside class="home-sidebar" role="complementary">
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

                <section class="home-updates-section" aria-labelledby="home-updates-heading">
                    <h2 id="home-updates-heading" class="home-section-title">עדכונים</h2>
                    <div class="updates-box card home-updates-card">
                        <div class="updates-list">
                            <?php
                            $updates_query = new WP_Query([
                                'post_type' => 'qa_updates',
                                'posts_per_page' => 2,
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
            </aside>

            <!-- Main content (left in RTL): Hero + Knowledge map -->
            <main class="home-main" role="main">
                <header class="home-hero">
                    <div class="intro-text-block">
                        <h1 class="home-hero-title">ברוכים הבאים לאתר הקהילה המקצועית של הפיקוח</h1>
                        <p class="home-hero-subtitle">ידע וכלים לחיזוק עבודת הפיקוח בישראל בנושאי רווחה, חינוך ובריאות</p>
                    </div>
                </header>

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
            </main>
        </div> <!-- End supervisor-content-wrapper -->

        <!-- Stories from the Field - Carousel -->
        <section class="stories-from-field-section" aria-labelledby="stories-heading">
            <div class="stories-section-header">
                <span class="stories-section-icon" aria-hidden="true"><i class="fas fa-book-open"></i></span>
                <h2 id="stories-heading" class="stories-section-title-with-lines">
                    <span class="title-line"></span>
                    <span class="title-text">סיפורים מהשטח</span>
                    <span class="title-line"></span>
                </h2>
            </div>
            <div class="stories-carousel-wrapper">
                <button type="button" class="stories-carousel-prev" aria-label="<?php esc_attr_e('הקודם', 'text-domain'); ?>">‹</button>
                <div class="stories-carousel" role="region" aria-label="<?php esc_attr_e('סיפורים מהשטח', 'text-domain'); ?>">
                    <?php
                    $stories_query = new WP_Query([
                        'post_type' => 'qa_stories',
                        'posts_per_page' => 3,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_status' => 'publish',
                    ]);
                    if ($stories_query->have_posts()) :
                        while ($stories_query->have_posts()) : $stories_query->the_post();
                            $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 25);
                    ?>
                            <a href="<?php the_permalink(); ?>" class="story-card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="story-card-image"><?php the_post_thumbnail('medium'); ?></div>
                                <?php else : ?>
                                    <div class="story-card-image story-card-placeholder"></div>
                                <?php endif; ?>
                                <h3 class="story-card-title"><?php the_title(); ?></h3>
                                <?php if ($excerpt) : ?>
                                    <p class="story-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                                <?php endif; ?>
                            </a>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
                <button type="button" class="stories-carousel-next" aria-label="<?php esc_attr_e('הבא', 'text-domain'); ?>">›</button>
            </div>
        </section>

    </div> <!-- End supervisor-page-container -->

</div>

<?php
get_footer();
?>