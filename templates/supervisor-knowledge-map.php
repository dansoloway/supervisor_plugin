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

    <!-- Main Content -->
    <div class="knowledge-map-content">
        <h1 class="page-title" style="text-align: center; margin-bottom: 40px;">מפת הידע</h1>
        
        <!-- Knowledge Map Diagram -->
        <section class="knowledge-layout" dir="rtl" aria-label="מפת הידע">
            <!-- LEFT column -->
            <aside class="col-left">
                <ul class="arrow-list">
                    <li>מדריכים ופרקטיקות מיטביות</li>
                    <li>מחקרים</li>
                    <li>אכיפה מתקנת</li>
                    <li>אכיפה עונשית</li>
                </ul>
            </aside>

            <!-- CENTER column -->
            <div class="col-center">
                <div class="svg-frame">
                    <img src="<?php echo plugin_dir_url(__FILE__) . '../assets/img/knowledge_map.png'; ?>" alt="מפת הידע - תרשים מרכזי"/>
                </div>
            </div>

            <!-- RIGHT column -->
            <aside class="col-right">
                <ul class="arrow-list big">
                    <li>סטנדרטים לאיכות השירות</li>
                    <li>סטנדרטים לעבודת הפיקוח</li>
                    <li>
                        שיטות עבודה
                        <div class="sub">
                            שקיפות, ניהול סיכונים, שיתוף מקבלי שירות, רספונסיביות, פיקוח משולב
                        </div>
                    </li>
                    <li>בקרה עצמית</li>
                    <li>בקרה חיצונית</li>
                </ul>
            </aside>
        </section>
        
        <!-- Explanatory Text -->
        <div class="knowledge-map-description">
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
            <a href="<?php echo home_url('/supervisor-bib-cats/'); ?>" class="map-link">
                <i class="fas fa-tags"></i>
                <span>נושאי מפתח</span>
            </a>
            <a href="<?php echo home_url('/supervisor-qa-orgs/'); ?>" class="map-link">
                <i class="fas fa-building"></i>
                <span>ארגוני פיקוח</span>
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
