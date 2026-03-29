<?php
/**
 * Map ACF qa_country free text to flag-icons ISO codes (assets/flag-icons-main/flags/1x1/).
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @return array<string, string> Normalized lowercase label => ISO 3166-1 alpha-2 (or flag-icons code).
 */
function supervisor_org_country_alias_map() {
    static $map = null;
    if ($map !== null) {
        return $map;
    }

    $map = [
        // ISO-ish / short
        'il' => 'il', 'israel' => 'il',
        'us' => 'us', 'usa' => 'us', 'u.s.a' => 'us', 'u.s.a.' => 'us', 'america' => 'us',
        'united states' => 'us', 'united states of america' => 'us', 'u.s.' => 'us', 'u.s' => 'us',
        'gb' => 'gb', 'uk' => 'gb', 'u.k.' => 'gb', 'great britain' => 'gb',
        'united kingdom' => 'gb', 'england' => 'gb', 'britain' => 'gb',
        'fr' => 'fr', 'france' => 'fr',
        'de' => 'de', 'germany' => 'de', 'deutschland' => 'de',
        'it' => 'it', 'italy' => 'it', 'italia' => 'it',
        'es' => 'es', 'spain' => 'es', 'españa' => 'es', 'espana' => 'es',
        'nl' => 'nl', 'netherlands' => 'nl', 'holland' => 'nl',
        'be' => 'be', 'belgium' => 'be',
        'ch' => 'ch', 'switzerland' => 'ch', 'schweiz' => 'ch',
        'at' => 'at', 'austria' => 'at', 'österreich' => 'at', 'osterreich' => 'at',
        'ca' => 'ca', 'canada' => 'ca',
        'au' => 'au', 'australia' => 'au',
        'nz' => 'nz', 'new zealand' => 'nz',
        'se' => 'se', 'sweden' => 'se', 'sverige' => 'se',
        'no' => 'no', 'norway' => 'no', 'norge' => 'no',
        'dk' => 'dk', 'denmark' => 'dk', 'danmark' => 'dk',
        'fi' => 'fi', 'finland' => 'fi', 'suomi' => 'fi',
        'ie' => 'ie', 'ireland' => 'ie', 'éire' => 'ie', 'eire' => 'ie',
        'pt' => 'pt', 'portugal' => 'pt',
        'gr' => 'gr', 'greece' => 'gr', 'ελλάδα' => 'gr',
        'pl' => 'pl', 'poland' => 'pl', 'polska' => 'pl',
        'cz' => 'cz', 'czech republic' => 'cz', 'czechia' => 'cz',
        'hu' => 'hu', 'hungary' => 'hu', 'magyarország' => 'hu', 'magyarorszag' => 'hu',
        'ro' => 'ro', 'romania' => 'ro', 'românia' => 'ro',
        'bg' => 'bg', 'bulgaria' => 'bg',
        'hr' => 'hr', 'croatia' => 'hr', 'hrvatska' => 'hr',
        'si' => 'si', 'slovenia' => 'si',
        'sk' => 'sk', 'slovakia' => 'sk',
        'lt' => 'lt', 'lithuania' => 'lt', 'lietuva' => 'lt',
        'lv' => 'lv', 'latvia' => 'lv',
        'ee' => 'ee', 'estonia' => 'ee', 'eesti' => 'ee',
        'lu' => 'lu', 'luxembourg' => 'lu',
        'mt' => 'mt', 'malta' => 'mt',
        'cy' => 'cy', 'cyprus' => 'cy',
        'is' => 'is', 'iceland' => 'is', 'ísland' => 'is', 'island' => 'is',
        'ru' => 'ru', 'russia' => 'ru', 'russian federation' => 'ru',
        'ua' => 'ua', 'ukraine' => 'ua', 'україна' => 'ua',
        'tr' => 'tr', 'turkey' => 'tr', 'türkiye' => 'tr', 'turkiye' => 'tr',
        'eg' => 'eg', 'egypt' => 'eg', 'מצרים' => 'eg',
        'jo' => 'jo', 'jordan' => 'jo', 'ירדן' => 'jo',
        'lb' => 'lb', 'lebanon' => 'lb', 'לבנון' => 'lb',
        'sy' => 'sy', 'syria' => 'sy', 'סוריה' => 'sy',
        'ps' => 'ps', 'palestine' => 'ps', 'palestinian territories' => 'ps',
        'jp' => 'jp', 'japan' => 'jp', '日本' => 'jp',
        'cn' => 'cn', 'china' => 'cn', 'p.r.c.' => 'cn', 'prc' => 'cn',
        'in' => 'in', 'india' => 'in',
        'br' => 'br', 'brazil' => 'br', 'brasil' => 'br',
        'ar' => 'ar', 'argentina' => 'ar',
        'mx' => 'mx', 'mexico' => 'mx', 'méxico' => 'mx',
        'za' => 'za', 'south africa' => 'za',
        'co' => 'co', 'colombia' => 'co',
        'kr' => 'kr', 'south korea' => 'kr', 'korea, republic of' => 'kr', 'korea (south)' => 'kr',
        'sg' => 'sg', 'singapore' => 'sg',
        'cl' => 'cl', 'chile' => 'cl',

        // Hebrew (common site labels)
        'ישראל' => 'il',
        'ארצות הברית' => 'us',
        'ארה"ב' => 'us', 'ארה״ב' => 'us',
        'בריטניה' => 'gb', 'הממלכה המאוחדת' => 'gb',
        'צרפת' => 'fr',
        'גרמניה' => 'de',
        'איטליה' => 'it',
        'ספרד' => 'es',
        'הולנד' => 'nl',
        'בלגיה' => 'be',
        'שוויץ' => 'ch', 'שוויצריה' => 'ch',
        'אוסטריה' => 'at',
        'קנדה' => 'ca',
        'אוסטרליה' => 'au',
        'ניו זילנד' => 'nz',
        'שוודיה' => 'se',
        'נורבגיה' => 'no',
        'דנמרק' => 'dk',
        'פינלנד' => 'fi',
        'אירלנד' => 'ie',
        'פורטוגל' => 'pt',
        'יוון' => 'gr',
        'פולין' => 'pl',
        'צכיה' => 'cz', 'צ׳כיה' => 'cz',
        'הונגריה' => 'hu',
        'רומניה' => 'ro',
        'בולגריה' => 'bg',
        'קרואטיה' => 'hr',
        'סלובניה' => 'si',
        'סלובקיה' => 'sk',
        'ליטא' => 'lt',
        'לטביה' => 'lv',
        'אסטוניה' => 'ee',
        'לוקסמבורג' => 'lu',
        'מלטה' => 'mt',
        'קפריסין' => 'cy',
        'איסלנד' => 'is',
        'רוסיה' => 'ru',
        'אוקראינה' => 'ua',
        'טורקיה' => 'tr',
        'יפן' => 'jp',
        'סין' => 'cn',
        'הודו' => 'in',
        'ברזיל' => 'br',
        'ארגנטינה' => 'ar',
        'מקסיקו' => 'mx',
        'דרום אפריקה' => 'za',
        'קולומביה' => 'co',
        'קוריאה הדרומית' => 'kr', 'דרום קוריאה' => 'kr',
        'סינגפור' => 'sg', 'סינגפור' => 'sg',
        'צ׳ילה' => 'cl', 'צילה' => 'cl',
    ];

    return $map;
}

/**
 * Absolute filesystem path to flag SVG.
 */
function supervisor_org_country_flag_svg_path($code) {
    $code = strtolower(preg_replace('/[^a-z0-9\-]/', '', (string) $code));
    if ($code === '') {
        return '';
    }

    return dirname(__DIR__) . '/assets/flag-icons-main/flags/1x1/' . $code . '.svg';
}

/**
 * Public URL for flag SVG or empty if missing / invalid.
 */
function supervisor_org_country_flag_url($code) {
    $path = supervisor_org_country_flag_svg_path($code);
    if ($path === '' || ! is_readable($path)) {
        return '';
    }

    $code = strtolower(preg_replace('/[^a-z0-9\-]/', '', (string) $code));

    return plugins_url('assets/flag-icons-main/flags/1x1/' . $code . '.svg', dirname(__DIR__) . '/supervisor-plugin.php');
}

/**
 * Resolve ACF country string to flag-icons code.
 */
function supervisor_org_country_flag_code($country_label) {
    if ($country_label === null || $country_label === '') {
        return '';
    }

    $label = trim(wp_strip_all_tags((string) $country_label));
    if ($label === '') {
        return '';
    }

    if (preg_match('/^[a-zA-Z]{2}$/', $label)) {
        $c = strtolower($label);
        if (is_readable(supervisor_org_country_flag_svg_path($c))) {
            return $c;
        }
    }

    $key = mb_strtolower($label, 'UTF-8');
    $map = supervisor_org_country_alias_map();
    if (isset($map[$key])) {
        $c = $map[$key];
        if (is_readable(supervisor_org_country_flag_svg_path($c))) {
            return $c;
        }
    }

    $parts = preg_split('/\s*[\/|]\s*/u', $label);
    if (count($parts) > 1) {
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '' || mb_strtolower($part, 'UTF-8') === $key) {
                continue;
            }
            $code = supervisor_org_country_flag_code($part);
            if ($code !== '') {
                return $code;
            }
        }
    }

    return '';
}
