<?php
/**
 * Template for Supervisor Activities Page
 * Displays three main activity areas: Conferences, Distribution, and Workshops
 */

get_header('supervisor');
?>

<div class="supervisor-home">

    <!-- Navigation Menu -->
    <?php
        $nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">
        
        <!-- Main Content Area -->
        <div class="supervisor-content-wrapper supervisor-single-column">
        
        <!-- Activities Grid -->
        <div class="activities-grid">
            <?php
            // Define activities data
            $activities = array(
                array(
                    'icon' => 'fas fa-users',
                    'title' => 'כנסים',
                    'content' => 'פיקוח חוץ הוא תהליך שבו מועבר ביצוע של פעילות פנימית של ארגון ציבורי או ממשלתי לגורם חוץ-ממשלתי על בסיס הסכם המעוגן על פי רוב בחוזה ומשקף את המשך אחריות המדינה לאספקת השירות. בעקבות תהליך מיקור החוץ התפתחו מגוון דרכים לאספקת שירותים חברתיים לאזרחים. יש שירותים שמסופקים על ידי גופים פרטיים – כאלה שפועלים למטרות רווח, ויש כאלה שמסופקים על ידי ארגונים ללא מטרות רווח.'
                ),
                array(
                    'icon' => 'fas fa-paper-plane',
                    'title' => 'הפצה',
                    'content' => 'פיקוח על שירותים חברתיים הוא מערך פעולות שמבצע גוף פיקוח מטעם המדינה, במטרה להבטיח את איכות השירותים המסופקים, בטיחותם ונגישותם. זאת, כדי להגן על שלומם ועל רווחתם של מקבלי השירות ולעודד חתירה לשיפור מתמיד של איכות השירות. המונח "פיקוח" רווח בעברית בהקשר של שירותים חברתיים, ומשמש לרוב כמקבילה למונח הלועזי "רגולציה".'
                ),
                array(
                    'icon' => 'fas fa-cogs',
                    'title' => 'סדנאות',
                    'content' => 'הפיקוח על שירותים חברתיים היה מאז ומתמיד חלק בלתי נפרד ממדינת הרווחה, אך בעשורים האחרונים קיבל משמעות חדשה בעקבות השינויים שחלו באופן אספקת השירותים החברתיים. משנות השמונים ועד היום בישראל, כמו במדינות רבות בעולם, חל תהליך אינטנסיבי של מעבר מאספקה ישירה של שירותים חברתיים על ידי המדינה לאספקה על ידי מפעילים חיצוניים במיקור חוץ.'
                ),
                array(
                    'icon' => 'fas fa-users',
                    'title' => 'כנסים',
                    'content' => 'פיקוח חוץ הוא תהליך שבו מועבר ביצוע של פעילות פנימית של ארגון ציבורי או ממשלתי לגורם חוץ-ממשלתי על בסיס הסכם המעוגן על פי רוב בחוזה ומשקף את המשך אחריות המדינה לאספקת השירות. בעקבות תהליך מיקור החוץ התפתחו מגוון דרכים לאספקת שירותים חברתיים לאזרחים. יש שירותים שמסופקים על ידי גופים פרטיים – כאלה שפועלים למטרות רווח, ויש כאלה שמסופקים על ידי ארגונים ללא מטרות רווח.'
                ),
                array(
                    'icon' => 'fas fa-paper-plane',
                    'title' => 'הפצה',
                    'content' => 'פיקוח על שירותים חברתיים הוא מערך פעולות שמבצע גוף פיקוח מטעם המדינה, במטרה להבטיח את איכות השירותים המסופקים, בטיחותם ונגישותם. זאת, כדי להגן על שלומם ועל רווחתם של מקבלי השירות ולעודד חתירה לשיפור מתמיד של איכות השירות. המונח "פיקוח" רווח בעברית בהקשר של שירותים חברתיים, ומשמש לרוב כמקבילה למונח הלועזי "רגולציה".'
                )
            );

            // Loop through activities and display cards
            foreach ($activities as $index => $activity) :
            ?>
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="<?php echo esc_attr($activity['icon']); ?>"></i>
                        <h2 class="activity-title"><?php echo esc_html($activity['title']); ?></h2>
                    </div>
                    <div class="activity-content">
                        <p><?php echo esc_html($activity['content']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>


<?php get_footer(); ?>
