<?php
/**
 * Country list from flag-icons country.json — labels for ACF and display.
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @return string Absolute path to country.json bundled with flag-icons.
 */
function supervisor_flag_icons_country_json_path() {
    return dirname(__DIR__) . '/assets/flag-icons-main/country.json';
}

/**
 * @return list<array{code?: string, name?: string, iso?: bool}>
 */
function supervisor_flag_country_json_records() {
    static $records = null;
    if ($records !== null) {
        return $records;
    }

    $path = supervisor_flag_icons_country_json_path();
    if (! is_readable($path)) {
        $records = [];
        return $records;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    if (! is_array($decoded)) {
        $records = [];
        return $records;
    }

    $records = $decoded;
    return $records;
}

/**
 * Map lowercase code => English country name (from JSON).
 */
function supervisor_flag_country_name_lookup() {
    static $map = null;
    if ($map !== null) {
        return $map;
    }

    $map = [];
    foreach (supervisor_flag_country_json_records() as $row) {
        if (empty($row['code']) || empty($row['name'])) {
            continue;
        }
        if (isset($row['iso']) && $row['iso'] === false) {
            continue;
        }
        $code = strtolower((string) $row['code']);
        $map[$code] = (string) $row['name'];
    }

    return $map;
}

/**
 * English display name for an ISO / flag-icons code, or empty string.
 */
function supervisor_flag_country_name_for_code($code) {
    $code = strtolower(preg_replace('/[^a-z0-9\-]/', '', (string) $code));
    if ($code === '') {
        return '';
    }

    $lookup = supervisor_flag_country_name_lookup();

    return $lookup[$code] ?? '';
}

/**
 * Choices for ACF select: value => label (sorted by label).
 */
function supervisor_flag_country_acf_choices() {
    $lookup = supervisor_flag_country_name_lookup();
    natcasesort($lookup);

    $out = [];
    foreach ($lookup as $code => $name) {
        $out[$code] = $name;
    }

    return $out;
}
