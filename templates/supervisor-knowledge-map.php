<?php
/* Template Name: Supervisor Knowledge Map */
get_header('supervisor');
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

        <div class="supervisor-content-wrapper supervisor-single-column knowledge-map-page-inner">
        <h1 class="page-title"><?php the_title(); ?></h1>

        <div class="knowledge-map-page-stack">
            <div class="knowledge-map-main">
                <?php
                while (have_posts()) :
                    the_post();
                    $has_lead = trim((string) get_post_field('post_content', get_the_ID())) !== '';
                    if ($has_lead) :
                        ?>
                        <div class="knowledge-map-lead entry-content">
                            <?php the_content(); ?>
                        </div>
                        <?php
                    else :
                        ?>
                        <div class="knowledge-map-lead knowledge-map-lead--empty" aria-hidden="true"></div>
                        <?php
                    endif;
                endwhile;
                rewind_posts();
                ?>

                <section class="knowledge-map-section knowledge-map-page-tiles" aria-label="<?php esc_attr_e('מפת הידע', 'text-domain'); ?>">
                    <div class="home-knowledge-map">
                        <div class="knowledge-map-grid">
                            <a class="km-tile km-top-left" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('regulatory_welfare_state')); ?>"><span>מדינת הרווחה הרגולטורית</span></a>
                            <a class="km-tile km-top-right" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('social_procurement')); ?>"><span>רכש חברתי</span></a>
                            <a class="km-tile km-mid-left" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('policy')); ?>"><span>מדיניות</span></a>
                            <a class="km-tile km-mid-right" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('knowledge_development')); ?>"><span>פיתוח ידע והדרכה</span></a>
                            <a class="km-tile km-bottom-left" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('control')); ?>"><span>בקרה</span></a>
                            <a class="km-tile km-bottom-right" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('enforcement')); ?>"><span>אכיפה</span></a>
                            <?php
                            $wm_url = supervisor_knowledge_map_tile_url('working_methods');
                            if ($wm_url) :
                                ?>
                                <a class="km-wide" href="<?php echo esc_url($wm_url); ?>"><span>שיטות עבודה</span></a>
                                <?php
                            else :
                                ?>
                                <div class="km-wide" aria-disabled="true"><span>שיטות עבודה</span></div>
                                <?php
                            endif;
                            ?>
                            <div class="km-center" aria-hidden="true"><span>גוף<br>פיקוח</span></div>
                        </div>
                    </div>
                </section>

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

                <div class="knowledge-map-links">
                    <a href="<?php echo get_permalink(SUPERVISOR_BIB_CATS); ?>">נושאי מפתח</a>
                    <a href="<?php echo get_permalink(SUPERVISOR_ORGS); ?>">ארגוני פיקוח</a>
                </div>
            </div>
        </div>

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>
