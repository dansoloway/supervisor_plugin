<?php
/* Template Name: Supervisor Knowledge Map */
get_header('supervisor');
$hierarchy = supervisor_knowledge_map_hierarchy();
?>

<div class="supervisor-home supervisor-knowledge-map">
    <!-- Navigation Menu -->
    <?php
        $nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">
        
        <!-- Main Content -->
        <div class="supervisor-content-wrapper supervisor-single-column">
        <h1 class="page-title">מפת הידע</h1>
        
        <!-- Knowledge Map Diagram (center) -->
        <section class="knowledge-layout knowledge-layout-svg-only" dir="rtl">
            <div class="col-center">
                <div class="svg-frame">
                    <img src="<?php echo plugin_dir_url(__FILE__) . '../assets/img/knowledge_map.svg'; ?>" alt="מפת הידע - תרשים מרכזי"/>
                </div>
            </div>
        </section>

        <!-- Structured index: groups + sub-items → נושאי מפתח filters -->
        <section class="knowledge-map-topic-index" dir="rtl" aria-label="<?php esc_attr_e('מפת הידע לפי נושאים', 'text-domain'); ?>">
            <h2 class="knowledge-map-topic-index-title"><?php echo esc_html__('נושאים במפת הידע', 'text-domain'); ?></h2>
            <div class="knowledge-map-topic-grid">
                <?php foreach ($hierarchy as $index => $group) : ?>
                    <div class="km-topic-group">
                        <h3 class="km-topic-group-title">
                            <a class="km-topic-group-link" href="<?php echo esc_url(supervisor_knowledge_map_topics_url($group['slug'])); ?>"><?php echo esc_html($group['label']); ?></a>
                        </h3>
                        <?php if (count($group['items']) === 1 && $group['items'][0]['slug'] === $group['slug']) : ?>
                            <p class="km-topic-group-note"><?php echo esc_html__('תחום על — שיוך נושאי מפתח ישירות לקטגוריה זו.', 'text-domain'); ?></p>
                        <?php else : ?>
                            <ul class="km-topic-group-list">
                                <?php foreach ($group['items'] as $item) : ?>
                                    <li>
                                        <a href="<?php echo esc_url(supervisor_knowledge_map_topics_url($item['slug'])); ?>"><?php echo esc_html($item['label']); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="knowledge-map-tile-aliases-note">
                <?php echo esc_html__('קישורי טילים במפה:', 'text-domain'); ?>
                <a href="<?php echo esc_url(supervisor_knowledge_map_topics_url('tile_info_dissemination')); ?>"><?php echo esc_html__('הפצת מידע וידע', 'text-domain'); ?></a>
                <?php echo ' · '; ?>
                <a href="<?php echo esc_url(supervisor_knowledge_map_topics_url('tile_policy_social_services')); ?>"><?php echo esc_html__('מדיניות פיקוח על שירותים חברתיים', 'text-domain'); ?></a>
            </p>
        </section>
        
        <!-- Explanatory Text -->
        <div class="knowledge-map-description">
            <h2 class="knowledge-map-description-title">מפת הידע:</h2>
            <p>
                מפת הידע מתארת את המערכת המקיפה לפיקוח ובקרה על שירותים חברתיים. 
                המעבר במדינות רבות מניהול ישיר של שירותים חברתיים על ידי המדינה לרכישה חברתית (social procurement) 
                מאופרטורים חיצוניים - ארגונים פרטיים למטרות רווח ולא למטרות רווח - מציב בפני המדינה אחריות 
                לגיבוש מדיניות ולפיקוח על שירותים אלה.
            </p>
            <p>
                המפה מתארת את ארבעת התחומים המרכזיים של מערכת הפיקוח: הפצת מידע וידע, מדיניות, בקרה ואכיפה. 
                כל תחום כולל כלים ומנגנונים ספציפיים המבטיחים איכות, שקיפות ואחריותיות בשירותים החברתיים.
            </p>
            <p>
                פרויקט זה הוא שיתוף פעולה בין אגף הרכש החברתי במשרד ראש הממשלה, JDC-אלכא ומכון מאיירס-ג'וינט-ברוקדייל 
                לביצוע סקירה השוואתית בינלאומית של שיטות אספקת שירותים חברתיים.
            </p>
        </div>
        
        <!-- Navigation Links -->
        <div class="knowledge-map-links">
            <a href="<?php echo get_permalink(SUPERVISOR_BIB_CATS); ?>">נושאי מפתח</a>
            <a href="<?php echo get_permalink(SUPERVISOR_ORGS); ?>">ארגוני פיקוח</a>
        </div>
        
        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>
