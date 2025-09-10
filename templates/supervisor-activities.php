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

    <!-- Main Content Area -->
    <div class="supervisor-main-content">
        
        <!-- Activities Grid: 5 Cards -->
        <div class="activities-grid">
            
            <!-- Card 1: Conferences -->
            <div class="activity-card">
                <div class="activity-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h2 class="activity-title">כנסים</h2>
                <div class="activity-content">
                    <p>פיקוח חוץ הוא תהליך שבו מועבר ביצוע של פעילות פנימית של ארגון ציבורי או ממשלתי לגורם חוץ-ממשלתי על בסיס הסכם המעוגן על פי רוב בחוזה ומשקף את המשך אחריות המדינה לאספקת השירות. בעקבות תהליך מיקור החוץ התפתחו מגוון דרכים לאספקת שירותים חברתיים לאזרחים. יש שירותים שמסופקים על ידי גופים פרטיים – כאלה שפועלים למטרות רווח, ויש כאלה שמסופקים על ידי ארגונים ללא מטרות רווח.</p>
                </div>
            </div>

            <!-- Card 2: Distribution -->
            <div class="activity-card">
                <div class="activity-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h2 class="activity-title">הפצה</h2>
                <div class="activity-content">
                    <p>פיקוח על שירותים חברתיים הוא מערך פעולות שמבצע גוף פיקוח מטעם המדינה, במטרה להבטיח את איכות השירותים המסופקים, בטיחותם ונגישותם. זאת, כדי להגן על שלומם ועל רווחתם של מקבלי השירות ולעודד חתירה לשיפור מתמיד של איכות השירות. המונח "פיקוח" רווח בעברית בהקשר של שירותים חברתיים, ומשמש לרוב כמקבילה למונח הלועזי "רגולציה".</p>
                </div>
            </div>

            <!-- Card 3: Workshops -->
            <div class="activity-card">
                <div class="activity-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h2 class="activity-title">סדנאות</h2>
                <div class="activity-content">
                    <p>הפיקוח על שירותים חברתיים היה מאז ומתמיד חלק בלתי נפרד ממדינת הרווחה, אך בעשורים האחרונים קיבל משמעות חדשה בעקבות השינויים שחלו באופן אספקת השירותים החברתיים. משנות השמונים ועד היום בישראל, כמו במדינות רבות בעולם, חל תהליך אינטנסיבי של מעבר מאספקה ישירה של שירותים חברתיים על ידי המדינה לאספקה על ידי מפעילים חיצוניים במיקור חוץ.</p>
                </div>
            </div>

            <!-- Card 4: Conferences (Duplicate) -->
            <div class="activity-card">
                <div class="activity-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h2 class="activity-title">כנסים</h2>
                <div class="activity-content">
                    <p>פיקוח חוץ הוא תהליך שבו מועבר ביצוע של פעילות פנימית של ארגון ציבורי או ממשלתי לגורם חוץ-ממשלתי על בסיס הסכם המעוגן על פי רוב בחוזה ומשקף את המשך אחריות המדינה לאספקת השירות. בעקבות תהליך מיקור החוץ התפתחו מגוון דרכים לאספקת שירותים חברתיים לאזרחים. יש שירותים שמסופקים על ידי גופים פרטיים – כאלה שפועלים למטרות רווח, ויש כאלה שמסופקים על ידי ארגונים ללא מטרות רווח.</p>
                </div>
            </div>

            <!-- Card 5: Distribution (Duplicate) -->
            <div class="activity-card">
                <div class="activity-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h2 class="activity-title">הפצה</h2>
                <div class="activity-content">
                    <p>פיקוח על שירותים חברתיים הוא מערך פעולות שמבצע גוף פיקוח מטעם המדינה, במטרה להבטיח את איכות השירותים המסופקים, בטיחותם ונגישותם. זאת, כדי להגן על שלומם ועל רווחתם של מקבלי השירות ולעודד חתירה לשיפור מתמיד של איכות השירות. המונח "פיקוח" רווח בעברית בהקשר של שירותים חברתיים, ומשמש לרוב כמקבילה למונח הלועזי "רגולציה".</p>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
/* Activities Page Specific Styles - moved to main CSS file */

.activity-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 40px 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.activity-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.activity-icon {
    width: 100px;
    height: 100px;
    background: var(--sv-blue-050);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.activity-icon i {
    font-size: 36px;
    color: var(--sv-blue);
}

.activity-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--sv-text);
    margin-bottom: 16px;
    font-family: var(--sv-font-primary);
}

.activity-content p {
    font-size: 16px;
    line-height: 1.6;
    color: var(--sv-text-2);
    margin: 0;
    font-family: var(--sv-font-body);
    text-align: right;
}

/* Override main content layout for activities page */
.supervisor-home .supervisor-main-content {
    grid-template-columns: 1fr;
    max-width: 1140px;
    margin: 0 auto;
    padding: 0 16px 48px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .activities-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .activity-card:nth-child(5) {
        grid-column: 1;
        justify-self: stretch;
        max-width: 100%;
    }
    
    .activity-card {
        padding: 32px 24px;
    }
    
    .activity-icon {
        width: 80px;
        height: 80px;
        margin-bottom: 20px;
    }
    
    .activity-icon i {
        font-size: 28px;
    }
    
    .activity-title {
        font-size: 22px;
    }
    
    .activity-content p {
        font-size: 15px;
    }
}
</style>

<?php get_footer(); ?>
