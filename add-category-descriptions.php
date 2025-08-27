<?php
/**
 * Add Category Descriptions
 * This script adds Hebrew descriptions to all qa_tags categories
 */

// Load WordPress - try multiple possible paths
$wp_load_paths = [
    '../../../wp-load.php',
    '../../../../wp-load.php',
    '../../../../../wp-load.php',
    '/www/brookdalejdcorg_480/public/wp-load.php'
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
    die('Could not load WordPress. Please check the file paths.');
}

echo '<h1>Adding Category Descriptions</h1>';
echo '<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .step { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
</style>';

// Define Hebrew descriptions for each category
$category_descriptions = [
    'בקרה עצמית' => 'מערכות ומנגנונים המאפשרים לארגונים לפקח על פעילותם הפנימית ולבקר את איכות השירותים שהם מספקים.',
    'בקרה אחר עמידה בסטנדרטים' => 'תהליכי פיקוח ובקרה המוודאים כי שירותים חברתיים עומדים בסטנדרטים המקצועיים והחוקיים הנדרשים.',
    'אספקת שירותים חברתיים ומיקור חוץ' => 'מנגנונים להעברת שירותים חברתיים לגורמים חיצוניים תוך שמירה על איכות ופיקוח מתמשך.',
    'אכיפה' => 'כלים ומנגנונים לאכיפת תקנות, חוקים וסטנדרטים בתחום השירותים החברתיים והרווחה.',
    'ניהול סיכונים' => 'שיטות וכלים לזיהוי, הערכה וניהול סיכונים בתחום השירותים החברתיים והרווחה.',
    'מדינת רווחה רגולטורית' => 'מערכת רגולציה המבטיחה כי שירותי הרווחה ניתנים באיכות גבוהה ובהתאם לעקרונות הצדק החברתי.',
    'מדיניות פיקוח על שירותים חברתיים' => 'עקרונות ומדיניות לפיקוח על איכות השירותים החברתיים והבטחת טובת הציבור.',
    'הפצת מידע וידע' => 'מנגנונים להעברת מידע מקצועי וידע בין ארגונים ומוסדות בתחום הרווחה והשירותים החברתיים.',
    'שיתוף מקבלי השירות בפיקוח' => 'שילוב משתמשי השירות בתהליכי הפיקוח והבקרה על איכות השירותים החברתיים.',
    'פיקוח משולב' => 'גישה לפיקוח המשלבת מספר מנגנוני בקרה ופיקוח ליצירת מערכת פיקוח מקיפה ואפקטיבית.',
    'סטנדרטים לאיכות השירותים' => 'קריטריונים ומדדים להערכת איכות השירותים החברתיים והבטחת רמת שירות גבוהה.'
];

echo '<div class="step">';
echo '<h2>Step 1: Getting current terms</h2>';

// Get all current terms
$current_terms = get_terms([
    'taxonomy' => 'qa_tags',
    'hide_empty' => false,
]);

echo '<p><strong>Total terms found:</strong> ' . count($current_terms) . '</p>';

echo '<div class="step">';
echo '<h2>Step 2: Adding descriptions</h2>';

$updated_count = 0;
$errors = [];

foreach ($current_terms as $term) {
    if (isset($category_descriptions[$term->name])) {
        $description = $category_descriptions[$term->name];
        $result = update_term_meta($term->term_id, 'qa_bib_description', $description);
        
        if ($result !== false) {
            echo '<p class="success">✓ Updated description for "' . esc_html($term->name) . '"</p>';
            $updated_count++;
        } else {
            echo '<p class="error">✗ Failed to update description for "' . esc_html($term->name) . '"</p>';
            $errors[] = $term->name;
        }
    } else {
        echo '<p class="info">- No description defined for "' . esc_html($term->name) . '"</p>';
    }
}

echo '</div>';

echo '<div class="step">';
echo '<h2>Step 3: Summary</h2>';
echo '<p><strong>Total terms processed:</strong> ' . count($current_terms) . '</p>';
echo '<p><strong>Successfully updated:</strong> <span class="success">' . $updated_count . '</span></p>';

if (!empty($errors)) {
    echo '<p><strong>Errors:</strong> <span class="error">' . count($errors) . '</span></p>';
    echo '<ul>';
    foreach ($errors as $error) {
        echo '<li class="error">' . esc_html($error) . '</li>';
    }
    echo '</ul>';
}

echo '<p class="success">✅ Category descriptions have been added successfully!</p>';
echo '</div>';

echo '<div class="step">';
echo '<h2>Step 4: Verification</h2>';
echo '<p>Current terms with descriptions:</p>';
echo '<ul>';

foreach ($current_terms as $term) {
    $description = get_term_meta($term->term_id, 'qa_bib_description', true);
    echo '<li><strong>' . esc_html($term->name) . '</strong>';
    if ($description) {
        echo ': ' . esc_html($description);
    } else {
        echo ': <span class="error">No description</span>';
    }
    echo '</li>';
}

echo '</ul>';
echo '</div>';
?>
