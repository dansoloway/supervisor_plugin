<?php
/**
 * Simple Seed Content Creator for Bibliography Items
 */

// Basic WordPress loading
if (!defined('ABSPATH')) {
    // Try to find wp-load.php
    $wp_load_paths = [
        '../../../wp-load.php',
        '../../wp-load.php',
        '../wp-load.php',
        'wp-load.php'
    ];
    
    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die('Could not load WordPress. Please run this script from the plugin directory.');
    }
}

echo '<!DOCTYPE html>';
echo '<html dir="rtl" lang="he">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<title>יצירת תוכן זרע לביבליוגרפיה</title>';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background: #f9f9f9; border-radius: 8px; direction: rtl; }';
echo 'h1 { color: #333; text-align: center; }';
echo 'h3 { color: #0073aa; margin-top: 20px; }';
echo '.success { color: green; }';
echo '.error { color: red; }';
echo '.warning { color: orange; }';
echo '.summary { font-size: 18px; font-weight: bold; color: #0073aa; margin-top: 30px; }';
echo '</style>';
echo '</head>';
echo '<body>';

echo '<h1>יצירת תוכן זרע לביבליוגרפיה</h1>';

// Check if user is admin
if (!current_user_can('manage_options')) {
    echo '<p class="error">אין לך הרשאות לפעולה זו</p>';
    echo '</body></html>';
    exit;
}

// Get all qa_tags terms
$tags = get_terms([
    'taxonomy' => 'qa_tags',
    'hide_empty' => false,
]);

if (empty($tags)) {
    echo '<p class="error">לא נמצאו קטגוריות qa_tags</p>';
    echo '</body></html>';
    exit;
}

echo '<p>נמצאו ' . count($tags) . ' קטגוריות</p>';

// Define seed content for each category
$seed_content = [
    'בקרה עצמית' => [
        [
            'title' => 'מדריך לבקרה עצמית בארגוני רווחה',
            'description' => 'מדריך מקיף המפרט את השלבים הנדרשים ליישם מערכת בקרה עצמית אפקטיבית בארגוני רווחה. כולל כלים מעשיים, דוגמאות מהשטח וטיפים ליישום מוצלח.',
            'link' => 'https://example.com/self-control-guide'
        ],
        [
            'title' => 'מודל בקרה עצמית מבוסס תוצאות',
            'description' => 'מחקר המציג מודל חדשני לבקרה עצמית המבוסס על מדידת תוצאות ותפוקות. המודל הותאם במיוחד לצרכים של ארגוני שירותים חברתיים.',
            'link' => 'https://example.com/results-based-model'
        ],
        [
            'title' => 'כלים דיגיטליים לבקרה עצמית',
            'description' => 'סקירה של כלים דיגיטליים מתקדמים המאפשרים אוטומציה של תהליכי בקרה עצמית. כולל השוואה בין פלטפורמות שונות והמלצות ליישום.',
            'link' => 'https://example.com/digital-tools'
        ]
    ],
    'אכיפה' => [
        [
            'title' => 'מנגנוני אכיפה בתקנות רווחה',
            'description' => 'ניתוח מקיף של מנגנוני האכיפה הקיימים בתקנות הרווחה בישראל. כולל דוגמאות מעשיות וניתוח אפקטיביות של כל מנגנון.',
            'link' => 'https://example.com/enforcement-mechanisms'
        ],
        [
            'title' => 'מדיניות אכיפה של CQC באנגליה',
            'description' => 'סקירה של מדיניות האכיפה של Care Quality Commission באנגליה. כולל לקחים רלוונטיים ליישום בישראל וניתוח השוואתי.',
            'link' => 'https://example.com/cqc-policy'
        ]
    ],
    'פיקוח' => [
        [
            'title' => 'מערכות פיקוח על רשויות מקומיות',
            'description' => 'מחקר על מערכות הפיקוח הקיימות על רשויות מקומיות בתחום השירותים החברתיים. כולל המלצות לשיפור המערכת הקיימת.',
            'link' => 'https://example.com/local-authorities'
        ],
        [
            'title' => 'מודל פיקוח מבוסס סיכונים',
            'description' => 'פיתוח מודל פיקוח חדשני המבוסס על הערכת סיכונים. המודל מאפשר הקצאת משאבים יעילה ופיקוח ממוקד.',
            'link' => 'https://example.com/risk-based-supervision'
        ],
        [
            'title' => 'כלים טכנולוגיים לפיקוח מרחוק',
            'description' => 'סקירה של כלים טכנולוגיים המאפשרים פיקוח מרחוק על ארגוני רווחה. כולל יתרונות, חסרונות והמלצות ליישום.',
            'link' => 'https://example.com/remote-supervision'
        ]
    ],
    'תקנים' => [
        [
            'title' => 'תקני איכות בשירותי רווחה',
            'description' => 'מסמך המגדיר תקני איכות מקיפים לשירותי רווחה. כולל מדדים כמותיים ואיכותיים למדידת איכות השירות.',
            'link' => 'https://example.com/quality-standards'
        ],
        [
            'title' => 'תהליך פיתוח תקנים מקצועיים',
            'description' => 'מדריך לתהליך פיתוח תקנים מקצועיים בתחום הרווחה. כולל שלבים, שיטות עבודה ודוגמאות מהשטח.',
            'link' => 'https://example.com/standards-development'
        ]
    ],
    'מדיניות' => [
        [
            'title' => 'מדיניות פיקוח על ארגוני רווחה',
            'description' => 'מסמך מדיניות מקיף המגדיר את עקרונות הפיקוח על ארגוני רווחה בישראל. כולל מטרות, יעדים ואמצעים.',
            'link' => 'https://example.com/supervision-policy'
        ],
        [
            'title' => 'מדיניות בקרת איכות בשירותים חברתיים',
            'description' => 'מדיניות לבקרת איכות בשירותים חברתיים המבוססת על עקרונות של שקיפות, אחריות ומעורבות משתמשים.',
            'link' => 'https://example.com/quality-control-policy'
        ],
        [
            'title' => 'מדיניות אכיפה מבוססת שיתוף פעולה',
            'description' => 'גישה חדשנית למדיניות אכיפה המבוססת על שיתוף פעולה עם ארגוני רווחה במקום גישה ענישתית בלבד.',
            'link' => 'https://example.com/collaborative-enforcement'
        ]
    ],
    'איכות' => [
        [
            'title' => 'מערכת מדידת איכות בשירותי רווחה',
            'description' => 'פיתוח מערכת מקיפה למדידת איכות בשירותי רווחה. כולל מדדים, כלי מדידה ותהליכי הערכה.',
            'link' => 'https://example.com/quality-measurement'
        ],
        [
            'title' => 'תהליכי שיפור איכות מתמשכים',
            'description' => 'מדריך לתהליכי שיפור איכות מתמשכים בארגוני רווחה. כולל מתודולוגיות, כלים ודוגמאות מהשטח.',
            'link' => 'https://example.com/continuous-improvement'
        ]
    ],
    'מקצועיות' => [
        [
            'title' => 'סטנדרטים מקצועיים לעובדי רווחה',
            'description' => 'הגדרת סטנדרטים מקצועיים לעובדי רווחה המבוססים על ידע, כישורים וערכים מקצועיים.',
            'link' => 'https://example.com/professional-standards'
        ],
        [
            'title' => 'תוכניות הכשרה והתפתחות מקצועית',
            'description' => 'סקירה של תוכניות הכשרה והתפתחות מקצועית לעובדי רווחה. כולל המלצות לשיפור והרחבה.',
            'link' => 'https://example.com/professional-development'
        ],
        [
            'title' => 'כלי הערכה מקצועית',
            'description' => 'פיתוח כלים להערכה מקצועית של עובדי רווחה המבוססים על סטנדרטים מקצועיים מוגדרים.',
            'link' => 'https://example.com/professional-assessment'
        ]
    ],
    'שקיפות' => [
        [
            'title' => 'מדיניות שקיפות בארגוני רווחה',
            'description' => 'פיתוח מדיניות שקיפות מקיפה לארגוני רווחה המאזנת בין שקיפות לצורך בשמירה על פרטיות.',
            'link' => 'https://example.com/transparency-policy'
        ],
        [
            'title' => 'כלים דיגיטליים לשקיפות',
            'description' => 'סקירה של כלים דיגיטליים המאפשרים שקיפות בארגוני רווחה. כולל פלטפורמות, אפליקציות ומערכות ניהול.',
            'link' => 'https://example.com/digital-transparency'
        ]
    ],
    'אחריותיות' => [
        [
            'title' => 'מערכת אחריותיות בארגוני רווחה',
            'description' => 'פיתוח מערכת אחריותיות מקיפה לארגוני רווחה המגדירה תפקידים, אחריות ומנגנוני דיווח.',
            'link' => 'https://example.com/accountability-system'
        ],
        [
            'title' => 'כלי מדידה לאחריותיות',
            'description' => 'פיתוח כלי מדידה לאחריותיות בארגוני רווחה המאפשרים מעקב ובקרה על ביצועים ותוצאות.',
            'link' => 'https://example.com/accountability-measurement'
        ],
        [
            'title' => 'מנגנוני דיווח ופיקוח',
            'description' => 'סקירה של מנגנוני דיווח ופיקוח בארגוני רווחה המבטיחים אחריותיות ושקיפות.',
            'link' => 'https://example.com/reporting-mechanisms'
        ]
    ],
    'מעורבות' => [
        [
            'title' => 'מעורבות משתמשים בתהליכי פיקוח',
            'description' => 'פיתוח מודלים למעורבות משתמשי שירותים בתהליכי פיקוח ובקרה. כולל כלים ושיטות עבודה.',
            'link' => 'https://example.com/user-involvement'
        ],
        [
            'title' => 'מעורבות קהילתית בבקרת איכות',
            'description' => 'מודלים למעורבות קהילתית בבקרת איכות שירותי רווחה. כולל דוגמאות מהשטח והמלצות ליישום.',
            'link' => 'https://example.com/community-involvement'
        ]
    ],
    'חדשנות' => [
        [
            'title' => 'חדשנות בפיקוח ובקרה',
            'description' => 'סקירה של חידושים טכנולוגיים ומתודולוגיים בתחום הפיקוח והבקרה על שירותי רווחה.',
            'link' => 'https://example.com/innovation-supervision'
        ],
        [
            'title' => 'כלים דיגיטליים לפיקוח חכם',
            'description' => 'פיתוח כלים דיגיטליים לפיקוח חכם המבוססים על בינה מלאכותית ולמידת מכונה.',
            'link' => 'https://example.com/smart-supervision'
        ],
        [
            'title' => 'מודלים חדשניים לבקרת איכות',
            'description' => 'פיתוח מודלים חדשניים לבקרת איכות המשלבים טכנולוגיה מתקדמת עם עקרונות מקצועיים.',
            'link' => 'https://example.com/innovative-quality-control'
        ]
    ]
];

$created_count = 0;
$errors = [];

foreach ($tags as $tag) {
    if (!isset($seed_content[$tag->name])) {
        echo '<p class="warning">לא נמצא תוכן זרע לקטגוריה: ' . esc_html($tag->name) . '</p>';
        continue;
    }
    
    echo '<h3>קטגוריה: ' . esc_html($tag->name) . '</h3>';
    
    foreach ($seed_content[$tag->name] as $item) {
        // Create post
        $post_data = [
            'post_title' => $item['title'],
            'post_content' => $item['description'],
            'post_status' => 'publish',
            'post_type' => 'qa_bib_items',
            'post_author' => 1,
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            $errors[] = 'שגיאה ביצירת פריט: ' . $item['title'] . ' - ' . $post_id->get_error_message();
            echo '<p class="error">✗ שגיאה: ' . esc_html($item['title']) . '</p>';
            continue;
        }
        
        // Set taxonomy term
        $term_result = wp_set_object_terms($post_id, $tag->term_id, 'qa_tags');
        
        if (is_wp_error($term_result)) {
            $errors[] = 'שגיאה בהגדרת קטגוריה לפריט: ' . $item['title'] . ' - ' . $term_result->get_error_message();
        }
        
        // Set ACF field for original link
        update_field('orignial_link', $item['link'], $post_id);
        
        echo '<p class="success">✓ נוצר פריט: ' . esc_html($item['title']) . '</p>';
        $created_count++;
    }
}

echo '<h2>סיכום</h2>';
echo '<p class="summary">נוצרו ' . $created_count . ' פריטי ביבליוגרפיה</p>';

if (!empty($errors)) {
    echo '<h3>שגיאות:</h3>';
    foreach ($errors as $error) {
        echo '<p class="error">• ' . esc_html($error) . '</p>';
    }
}

echo '<p style="margin-top: 30px;"><a href="' . admin_url() . '" style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">חזרה לניהול</a></p>';
echo '</body></html>';
?>
