<?php
/**
 * Frozen snapshot of production page IDs (not loaded by WordPress).
 * The plugin reads `config.php` only (`config.php` is gitignored). To use these
 * IDs on a server: `cp config-prod-backup.php config.php` (or merge into a
 * local config.php). Keep in sync when IDs change.
 *
 * New environments: run `wp supervisor bootstrap-pages` then rely on slugs;
 * you usually do not need numeric defines.
 */

define('SUPERVISOR_HOME', 27886);
define('SUPERVISOR_BIB_CATS', 27899);
define('SUPERVISOR_UPDATES', 27906);
define('SUPERVISOR_ORGS', 27928);
define('SUPERVISOR_ABOUT', 27908);
define('SUPERVISOR_CONTACT', 27912);
define('SUPERVISOR_INTRO_TEXT', 27901);
define('SUPERVISOR_ACTIVITIES', 28029);
define('SUPERVISOR_KNOWLEDGE_MAP', 28040);