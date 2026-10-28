<?php
/**
 * Optional overrides for supervisor page IDs (integers).
 * Copy this file to `config.php` and uncomment defines if slugs do not match
 * inc/supervisor-pages.php on your install.
 *
 * The plugin loads `config.php` only when it exists (see supervisor-plugin.php).
 *
 * New environments: run `wp supervisor bootstrap-pages` then rely on slugs; you
 * usually do not need any defines here.
 */

/*
define('SUPERVISOR_HOME', 0);
define('SUPERVISOR_BIB_CATS', 0);
define('SUPERVISOR_UPDATES', 0);
define('SUPERVISOR_ORGS', 0);
define('SUPERVISOR_ABOUT', 0);
define('SUPERVISOR_CONTACT', 0);
define('SUPERVISOR_INTRO_TEXT', 0);
define('SUPERVISOR_ACTIVITIES', 0);
define('SUPERVISOR_KNOWLEDGE_MAP', 0);
*/

// Pre-launch: restrict all supervisor pages, CPTs, and taxonomies to logged-in users.
// Remove (or set to false) on launch day. See inc/preview-mode.php.
// define('SUPERVISOR_PREVIEW_MODE', true);

// Staging only: block admin saves of supervisor content (pages, CPTs, taxonomies, settings).
// WP-CLI is still allowed. See inc/content-readonly.php.
// define('SUPERVISOR_CONTENT_READONLY', true);
