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
        <div class="knowledge-map-diagram">
            <!-- Central Image -->
            <div class="central-image">
                <img src="<?php echo plugin_dir_url(__FILE__) . '../assets/img/knowledge_map.png'; ?>" alt="מפת הידע" />
            </div>
            
            <!-- Top: Information & Knowledge Dissemination -->
            <div class="map-section top-section">
                <div class="section-box">
                    <h3>הפצת מידע וידע</h3>
                    <div class="sub-items">
                        <div class="sub-item">
                            <i class="fas fa-chevron-left"></i>
                            <span>מדריכים ופרקטיקות מיטביות</span>
                        </div>
                        <div class="sub-item">
                            <i class="fas fa-chevron-left"></i>
                            <span>מחקרים</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right: Policy -->
            <div class="map-section right-section">
                <div class="section-box">
                    <h3>מדיניות</h3>
                    <div class="sub-items">
                        <div class="sub-item">
                            <i class="fas fa-chevron-left"></i>
                            <span>סטנדרטים לאיכות השירות</span>
                        </div>
                        <div class="sub-item">
                            <i class="fas fa-chevron-left"></i>
                            <span>סטנדרטים לעבודת הפיקוח</span>
                        </div>
                        <div class="sub-item">
                            <i class="fas fa-chevron-left"></i>
                            <span>שיטות עבודה</span>
                        </div>
                        <div class="work-methods-examples">
                            [שקיפות, ניהול סיכונים, שיתוף מקבלי שירות, רספונסיביות, פיקוח משולב]
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom: Control/Oversight -->
            <div class="map-section bottom-section">
                <div class="section-box">
                    <h3>בקרה</h3>
                    <div class="sub-items">
                        <div class="sub-item">
                            <i class="fas fa-chevron-left"></i>
                            <span>בקרה עצמית</span>
                        </div>
                        <div class="sub-item">
                            <i class="fas fa-chevron-left"></i>
                            <span>בקרה חיצונית</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Left: Enforcement -->
            <div class="map-section left-section">
                <div class="section-box">
                    <h3>אכיפה</h3>
                    <div class="sub-items">
                        <div class="sub-item">
                            <i class="fas fa-chevron-left"></i>
                            <span>אכיפה מתקנת</span>
                        </div>
                        <div class="sub-item">
                            <i class="fas fa-chevron-left"></i>
                            <span>אכיפה עונשית</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
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
