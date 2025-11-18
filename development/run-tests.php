<?php
/**
 * Test Runner for Supervisor Plugin Migration
 * 
 * Usage:
 * - Browser: Visit this file in your browser
 * - Command line: php run-tests.php
 */

// Load WordPress
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
        die("Could not load WordPress. Please run this from the plugin directory.\n");
    }
}

// Include the test file
require_once(__DIR__ . '/tests/test-migration.php');

// Run tests
$tests = new SupervisorMigrationTests();
$tests->run_all_tests();
