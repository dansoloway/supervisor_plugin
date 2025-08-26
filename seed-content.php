<?php
/**
 * Seed Content for Supervisor Plugin
 * This script creates sample content for testing search functionality
 * Run this once to populate the database with test data
 */

// WP-CLI automatically loads WordPress, so we don't need to load it manually
if (!defined('ABSPATH')) {
    // If not running via WP-CLI, try to load WordPress
    $wp_loaded = false;
    
    // Try multiple common WordPress load paths
    $possible_paths = [
        dirname(__FILE__) . '/wp-load.php',
        dirname(dirname(__FILE__)) . '/wp-load.php',
        dirname(dirname(dirname(__FILE__))) . '/wp-load.php',
        $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php',
    ];
    
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die('WordPress not found. Please run this script from the WordPress root directory or via WP-CLI.');
    }
}

// Check if we're in admin or can run this safely
if (!defined('WP_CLI') && !current_user_can('manage_options')) {
    die('Insufficient permissions to run this script. Please run via WP-CLI or as an admin user.');
}

// Output function that works with both WP-CLI and browser
function output_message($message) {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log($message);
    } else {
        echo $message . "<br>";
    }
}

if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::log("Creating Seed Content for Supervisor Plugin");
} else {
    echo "<h1>Creating Seed Content for Supervisor Plugin</h1>";
}

// Function to create taxonomy terms if they don't exist
function create_taxonomy_terms() {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log("Creating Taxonomy Terms...");
    } else {
        echo "<h2>Creating Taxonomy Terms...</h2>";
    }
    
    // Themes
    $themes = ['חינוך', 'רווחה', 'בריאות', 'תעסוקה', 'ביטחון', 'תחבורה'];
    foreach ($themes as $theme) {
        if (!term_exists($theme, 'qa_themes')) {
            wp_insert_term($theme, 'qa_themes');
            output_message("Created theme: $theme");
        }
    }
    
    // Tags
    $tags = ['ניהול סיכונים', 'איכות חיים', 'קשישים', 'ילדים', 'נשים', 'עולים', 'עבודה סוציאלית', 'רפואה מונעת'];
    foreach ($tags as $tag) {
        if (!term_exists($tag, 'qa_tags')) {
            wp_insert_term($tag, 'qa_tags');
            output_message("Created tag: $tag");
        }
    }
    
    // Bibliography Categories
    $bib_cats = ['מחקרים אקדמיים', 'דוחות ממשלתיים', 'סקרים', 'מדריכים מקצועיים'];
    foreach ($bib_cats as $cat) {
        if (!term_exists($cat, 'qa_bib_cats')) {
            wp_insert_term($cat, 'qa_bib_cats');
            output_message("Created bibliography category: $cat");
        }
    }
}

// Function to create QA Updates
function create_qa_updates() {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log("Creating QA Updates...");
    } else {
        echo "<h2>Creating QA Updates...</h2>";
    }
    
    $updates = [
        [
            'title' => 'דוח שנתי 2024: שיפור משמעותי באיכות השירותים הסוציאליים',
            'content' => 'הדוח השנתי של משרד הרווחה והביטחון החברתי מציג שיפור משמעותי באיכות השירותים הסוציאליים הניתנים לאזרחים. בין ההישגים הבולטים: הגדלת מספר העובדים הסוציאליים ב-15%, שיפור זמני המתנה לטיפול, והרחבת השירותים לקשישים. הדוח מדגיש את החשיבות של המשך ההשקעה בתשתיות ובכוח אדם מקצועי.',
            'date' => '2024-03-15',
            'link' => 'https://www.gov.il/he/departments/publications/reports/annual-report-2024',
            'themes' => ['רווחה', 'בריאות'],
            'tags' => ['ניהול סיכונים', 'איכות חיים', 'קשישים']
        ],
        [
            'title' => 'רפורמה במערכת החינוך: שילוב טכנולוגיה מתקדמת',
            'content' => 'משרד החינוך הודיע על רפורמה מקיפה במערכת החינוך שתכלול שילוב טכנולוגיה מתקדמת בכיתות. הרפורמה תכלול התקנת לוחות חכמים, חיבור לאינטרנט מהיר, והכשרת מורים לשימוש בטכנולוגיה חינוכית. הצפי הוא שהרפורמה תושלם בתוך שנתיים ותשפר משמעותית את איכות הלמידה.',
            'date' => '2024-02-28',
            'link' => 'https://www.education.gov.il/he/news/tech-reform-2024',
            'themes' => ['חינוך'],
            'tags' => ['ילדים', 'עבודה סוציאלית']
        ],
        [
            'title' => 'תוכנית לאומית לבריאות מונעת: מניעת מחלות כרוניות',
            'content' => 'משרד הבריאות השיק תוכנית לאומית לבריאות מונעת שמטרתה להפחית את שכיחות המחלות הכרוניות בישראל. התוכנית כוללת בדיקות סקר תקופתיות, חינוך לבריאות בבתי הספר, וקמפיינים ציבוריים להעלאת המודעות. התוכנית תתמקד בקבוצות סיכון ותותאם לצרכים של אוכלוסיות שונות.',
            'date' => '2024-01-20',
            'link' => 'https://www.health.gov.il/he/news/preventive-health-2024',
            'themes' => ['בריאות'],
            'tags' => ['רפואה מונעת', 'איכות חיים', 'קשישים']
        ],
        [
            'title' => 'שיפור התחבורה הציבורית: הוספת קווים חדשים',
            'content' => 'משרד התחבורה הודיע על הוספת קווים חדשים לתחבורה הציבורית במרכז הארץ. הקווים החדשים יחברו בין ערים מרכזיות ויפחיתו את זמני הנסיעה. בנוסף, יותקנו תחנות חדשות עם גישה נוחה לנכים וקשישים. הפרויקט צפוי להסתיים עד סוף השנה.',
            'date' => '2024-04-10',
            'link' => 'https://www.transport.gov.il/he/news/new-bus-lines-2024',
            'themes' => ['תחבורה'],
            'tags' => ['קשישים', 'איכות חיים']
        ]
    ];
    
    foreach ($updates as $update) {
        $post_data = [
            'post_title' => $update['title'],
            'post_content' => $update['content'],
            'post_status' => 'publish',
            'post_type' => 'qa_updates',
            'post_date' => $update['date'] . ' 10:00:00'
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id) {
            // Set custom fields
            update_field('qa_updates_date', $update['date'], $post_id);
            update_field('qa_updates_link', $update['link'], $post_id);
            
            // Set taxonomies
            if (!empty($update['themes'])) {
                wp_set_object_terms($post_id, $update['themes'], 'qa_themes');
            }
            if (!empty($update['tags'])) {
                wp_set_object_terms($post_id, $update['tags'], 'qa_tags');
            }
            
            output_message("Created update: {$update['title']}");
        }
    }
}

// Function to create QA Organizations
function create_qa_orgs() {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log("Creating QA Organizations...");
    } else {
        echo "<h2>Creating QA Organizations...</h2>";
    }
    
    $organizations = [
        [
            'title' => 'מרכז הרווחה העירוני תל אביב',
            'content' => 'מרכז הרווחה העירוני של תל אביב מספק שירותים סוציאליים מקיפים לתושבי העיר. המרכז כולל מחלקות לטיפול במשפחה, קשישים, ילדים ונוער, ושירותי חירום. הצוות כולל עובדים סוציאליים, פסיכולוגים, ומטפלים מקצועיים. המרכז פועל בשיתוף עם ארגונים קהילתיים ומשרדי ממשלה.',
            'subtitle' => 'שירותים סוציאליים מקיפים',
            'year' => '1985',
            'agency' => 'משרד הרווחה והביטחון החברתי',
            'link' => 'https://www.tel-aviv.gov.il/welfare',
            'services' => 'טיפול במשפחה, קשישים, ילדים ונוער, שירותי חירום',
            'report' => 'https://www.tel-aviv.gov.il/welfare/annual-report-2024',
            'themes' => ['רווחה'],
            'tags' => ['עבודה סוציאלית', 'קשישים', 'ילדים']
        ],
        [
            'title' => 'בית החולים שיבא תל השומר',
            'content' => 'בית החולים שיבא תל השומר הוא אחד מבתי החולים הגדולים והמתקדמים בישראל. בית החולים מספק טיפול רפואי מקיף במגוון תחומים: רפואה פנימית, כירורגיה, ילדים, נשים, וטיפול נמרץ. בית החולים משמש גם כמרכז מחקר ופיתוח רפואי ומכשיר סטודנטים לרפואה.',
            'subtitle' => 'מרכז רפואי מתקדם',
            'year' => '1948',
            'agency' => 'משרד הבריאות',
            'link' => 'https://www.sheba.co.il',
            'services' => 'רפואה פנימית, כירורגיה, ילדים, נשים, טיפול נמרץ',
            'report' => 'https://www.sheba.co.il/annual-report-2024',
            'themes' => ['בריאות'],
            'tags' => ['רפואה מונעת', 'איכות חיים']
        ],
        [
            'title' => 'מכון ויצמן למדע',
            'content' => 'מכון ויצמן למדע הוא אחד המוסדות המובילים בעולם למחקר מדעי. המכון מתמחה במחקר בסיסי ויישומי בתחומי הפיזיקה, הכימיה, הביולוגיה והמתמטיקה. המכון מכשיר דוקטורנטים ופוסט-דוקטורנטים ומשתף פעולה עם מוסדות מחקר בינלאומיים. המכון תורם משמעותית לקידום המדע בישראל.',
            'subtitle' => 'מחקר מדעי מתקדם',
            'year' => '1934',
            'agency' => 'משרד המדע והטכנולוגיה',
            'link' => 'https://www.weizmann.ac.il',
            'services' => 'מחקר בסיסי ויישומי, הכשרת חוקרים, שיתופי פעולה בינלאומיים',
            'report' => 'https://www.weizmann.ac.il/annual-report-2024',
            'themes' => ['חינוך'],
            'tags' => ['עבודה סוציאלית']
        ]
    ];
    
    foreach ($organizations as $org) {
        $post_data = [
            'post_title' => $org['title'],
            'post_content' => $org['content'],
            'post_status' => 'publish',
            'post_type' => 'qa_orgs'
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id) {
            // Set custom fields
            update_field('qa_subtitle', $org['subtitle'], $post_id);
            update_field('qa_yearoffounding', $org['year'], $post_id);
            update_field('qa_gov_agency', $org['agency'], $post_id);
            update_field('qa_link', $org['link'], $post_id);
            update_field('qa_services_supervised', $org['services'], $post_id);
            update_field('qa_yearly_report', $org['report'], $post_id);
            
            // Set taxonomies
            if (!empty($org['themes'])) {
                wp_set_object_terms($post_id, $org['themes'], 'qa_themes');
            }
            if (!empty($org['tags'])) {
                wp_set_object_terms($post_id, $org['tags'], 'qa_tags');
            }
            
            output_message("Created organization: {$org['title']}");
        }
    }
}

// Function to create QA Bibliography Items
function create_qa_bib_items() {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log("Creating QA Bibliography Items...");
    } else {
        echo "<h2>Creating QA Bibliography Items...</h2>";
    }
    
    $bibliography_items = [
        [
            'title' => 'השפעת התערבות מוקדמת על התפתחות ילדים בסיכון',
            'content' => 'מחקר זה בדק את השפעתן של תוכניות התערבות מוקדמת על התפתחות ילדים בסיכון בגילאי 3-6. המחקר כלל 500 ילדים מ-50 משפחות בישראל. התוצאות הראו שיפור משמעותי ביכולות הקוגניטיביות, הרגשיות והחברתיות של הילדים שהשתתפו בתוכנית לעומת קבוצת הביקורת.',
            'link' => 'https://www.research.gov.il/early-intervention-2024',
            'category' => 'מחקרים אקדמיים',
            'themes' => ['חינוך'],
            'tags' => ['ילדים', 'עבודה סוציאלית']
        ],
        [
            'title' => 'דוח מצב הרווחה בישראל 2024',
            'content' => 'דוח מקיף על מצב הרווחה בישראל לשנת 2024. הדוח כולל נתונים על שירותים סוציאליים, תקציבים, כוח אדם, ומדדי איכות חיים. הדוח מציג גם השוואות בינלאומיות וממליץ על כיווני פעולה עתידיים לשיפור השירותים הסוציאליים בישראל.',
            'link' => 'https://www.welfare.gov.il/annual-report-2024',
            'category' => 'דוחות ממשלתיים',
            'themes' => ['רווחה'],
            'tags' => ['עבודה סוציאלית', 'איכות חיים']
        ],
        [
            'title' => 'סקר בריאות לאומי: הרגלי תזונה ופעילות גופנית',
            'content' => 'סקר לאומי מקיף על הרגלי תזונה ופעילות גופנית בקרב האוכלוסייה הבוגרת בישראל. הסקר כלל 10,000 משתתפים מכל רחבי הארץ. התוצאות מראות על שיפור בהרגלי התזונה אך ירידה בפעילות הגופנית, במיוחד בקרב קבוצות גיל מבוגרות.',
            'link' => 'https://www.health.gov.il/national-health-survey-2024',
            'category' => 'סקרים',
            'themes' => ['בריאות'],
            'tags' => ['רפואה מונעת', 'איכות חיים', 'קשישים']
        ],
        [
            'title' => 'מדריך מקצועי לטיפול בקשישים בקהילה',
            'content' => 'מדריך מקיף למטפלים מקצועיים העובדים עם קשישים בקהילה. המדריך כולל פרקים על הערכה גריאטרית, תכנון טיפול, עבודה עם משפחות, ושיתוף פעולה עם שירותים רפואיים וסוציאליים. המדריך מבוסס על מחקרים עדכניים וניסיון קליני.',
            'link' => 'https://www.geriatrics.org.il/community-care-guide-2024',
            'category' => 'מדריכים מקצועיים',
            'themes' => ['בריאות', 'רווחה'],
            'tags' => ['קשישים', 'עבודה סוציאלית', 'רפואה מונעת']
        ],
        [
            'title' => 'השפעת רפורמת החינוך על הישגי תלמידים',
            'content' => 'מחקר הערכה של רפורמת החינוך שהונהגה בשנת 2022. המחקר בדק את השפעת הרפורמה על הישגי תלמידים במבחני המיצ"ב והבגרות. התוצאות מראות על שיפור משמעותי בהישגים, במיוחד בקרב תלמידים מרקע סוציו-אקונומי נמוך.',
            'link' => 'https://www.education.gov.il/reform-evaluation-2024',
            'category' => 'מחקרים אקדמיים',
            'themes' => ['חינוך'],
            'tags' => ['ילדים', 'עבודה סוציאלית']
        ]
    ];
    
    foreach ($bibliography_items as $item) {
        $post_data = [
            'post_title' => $item['title'],
            'post_content' => $item['content'],
            'post_status' => 'publish',
            'post_type' => 'qa_bib_items'
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id) {
            // Set custom fields
            update_field('orignial_link', $item['link'], $post_id);
            
            // Set taxonomies
            if (!empty($item['category'])) {
                wp_set_object_terms($post_id, $item['category'], 'qa_bib_cats');
            }
            if (!empty($item['themes'])) {
                wp_set_object_terms($post_id, $item['themes'], 'qa_themes');
            }
            if (!empty($item['tags'])) {
                wp_set_object_terms($post_id, $item['tags'], 'qa_tags');
            }
            
            output_message("Created bibliography item: {$item['title']}");
        }
    }
}

// Run the seed functions
try {
    create_taxonomy_terms();
    create_qa_updates();
    create_qa_orgs();
    create_qa_bib_items();
    
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::success("Seed Content Creation Complete!");
        WP_CLI::log("Created sample content for testing search functionality:");
        WP_CLI::log("- 4 QA Updates with themes and tags");
        WP_CLI::log("- 3 QA Organizations with custom fields");
        WP_CLI::log("- 5 QA Bibliography Items with categories");
        WP_CLI::log("- 6 Themes, 8 Tags, and 4 Bibliography Categories");
        WP_CLI::log("You can now test the search functionality with realistic content!");
    } else {
        echo "<h2>Seed Content Creation Complete!</h2>";
        echo "<p>Created sample content for testing search functionality:</p>";
        echo "<ul>";
        echo "<li>4 QA Updates with themes and tags</li>";
        echo "<li>3 QA Organizations with custom fields</li>";
        echo "<li>5 QA Bibliography Items with categories</li>";
        echo "<li>6 Themes, 8 Tags, and 4 Bibliography Categories</li>";
        echo "</ul>";
        echo "<p>You can now test the search functionality with realistic content!</p>";
    }
    
} catch (Exception $e) {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::error("Error Creating Seed Content: " . $e->getMessage());
    } else {
        echo "<h2>Error Creating Seed Content</h2>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>
