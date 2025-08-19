<?php
/* Template Name: Supervisor Home */
get_header('supervisor');
?>

<style>
/* Force styles directly in template */
.supervisor-home {
    border: 5px solid red !important;
    background: yellow !important;
}

/* Navigation - pill buttons with proper border radius */
.supervisor-home .site-nav {
    direction: rtl !important;
    display: flex !important;
    justify-content: center !important;
    gap: 0 !important;
    flex-wrap: wrap !important;
    background: #EEF4FA !important;
    border: 1px solid #D8E4EF !important;
    border-radius: 28px !important;
    padding: 6px !important;
    margin: 8px auto 14px !important;
    max-width: 980px !important;
}

.supervisor-home .site-nav a {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 10px 18px !important;
    border-radius: 9999px !important;
    color: #1F2937 !important;
    text-decoration: none !important;
    font-weight: 700 !important;
    transition: background .2s, color .2s !important;
}

.supervisor-home .site-nav a + a {
    margin-inline-start: 6px !important;
}

.supervisor-home .site-nav a:hover,
.supervisor-home .site-nav a:focus-visible {
    background: #B3CBE7 !important;
    color: #FFFFFF !important;
    outline: 0 !important;
}

/* Search bar - one cohesive pill with proper border radius */
.supervisor-search {
    direction: rtl !important;
    display: flex !important;
    align-items: center !important;
    gap: 0 !important;
    background: #FFFFFF !important;
    border: 1px solid #E5EAF0 !important;
    border-radius: 9999px !important;
    padding: 3px !important;
    height: 44px !important;
}

.supervisor-search-bar {
    height: 38px !important;
    background: transparent !important;
    flex: 1 !important;
    padding: 0 16px !important;
    border: none !important;
    border-radius: 9999px !important;
    outline: 0 !important;
    font-size: 15px !important;
    color: #1F2937 !important;
    margin: 0 !important;
}

.search-button {
    height: 38px !important;
    width: 38px !important;
    min-width: 38px !important;
    display: inline-grid !important;
    place-items: center !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: #B3CBE7 !important;
    color: #FFFFFF !important;
    cursor: pointer !important;
    transition: background .2s !important;
}

/* Updates box - sharp corners on ribbons */
.updates-header {
    background: #B3CBE7 !important;
    color: #000000 !important;
    font-weight: 800 !important;
    font-size: 16px !important;
    margin: -20px -20px 0 !important;
    padding: 12px 16px !important;
    border-radius: 0 !important;
    text-align: center !important;
}

.more-updates-button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 42px !important;
    padding: 0 20px !important;
    border-radius: 9999px !important;
    background: #B3CBE7 !important;
    color: #000000 !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    transition: background .2s !important;
    text-align: center !important;
}

/* Knowledge map card - sharp corners on ribbons */
.knowledge-map-header {
    background: #B3CBE7 !important;
    color: #000000 !important;
    font-weight: 800 !important;
    font-size: 16px !important;
    margin: -20px -20px 0 !important;
    padding: 12px 16px !important;
    border-radius: 0 !important;
    text-align: center !important;
}

.knowledge-map-button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 42px !important;
    padding: 0 20px !important;
    border-radius: 9999px !important;
    background: #B3CBE7 !important;
    color: #000000 !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    transition: background .2s !important;
    text-align: center !important;
}
</style>

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

        <!-- Right Column: Search and Updates -->
        <div class="center-content-section">
            <!-- Search Bar -->
            <div class="search-section">
                <form class="supervisor-search" role="search" action="/חיפוש-גלובלי/">
                    <button class="search-button" aria-label="חיפוש">
                        <svg class="search-icon" width="20" height="20" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="13" cy="13" r="9" stroke="white" stroke-width="4"></circle>
                            <line x1="19" y1="19" x2="28" y2="28" stroke="white" stroke-width="4"></line>
                        </svg>
                    </button>
                    <input type="search" name="text" placeholder="חיפוש" class="supervisor-search-bar" aria-label="שדה חיפוש">
                    <input type="hidden" name="q" value="1">
                </form>
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
                            echo '<div class="update-content">';
                            echo '<div class="update-bullet"></div>';
                            echo '<div class="update-text">';
                            echo '<h3 class="update-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
                            if ($formatted_date) {
                                echo '<p class="update-date">' . esc_html($formatted_date) . '</p>';
                            }
                            echo '</div>';
                            echo '</div>';
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
        
     <!-- Left Column: Knowledge Map Diagram -->
     <div class="knowledge-map-section">
            <div class="knowledge-map-card card">
                <div class="knowledge-map-header">מפת ידע</div>
                <div class="knowledge-map-diagram">
                    <img src="<?php echo plugins_url('assets/img/knowledge_map.png', dirname(__FILE__)); ?>" alt="מפת ידע - בקרת איכות" class="knowledge-map-image">
                </div>
                <div class="knowledge-map-footer">
                    <a href="#" class="knowledge-map-button">למידע נוסף על מפת הידע</a>
                </div>
            </div>
        </div>



       

    </div>

</div>

<?php
get_footer();
?>