<?php
/**
 * Frozen snapshot of production page IDs (not loaded by WordPress).
 * The plugin reads `config.php` only (`config.php` is gitignored). To use these
 * IDs on a server: `cp config-prod-backup.php config.php` (or merge into a
 * local config.php). Keep in sync when IDs change.
 *
 * Soft launch: also define SUPERVISOR_PREVIEW_MODE true until public launch.
 */

define('SUPERVISOR_PREVIEW_MODE', true);

define('SUPERVISOR_HOME', 30630);
define('SUPERVISOR_BIB_CATS', 30631);
define('SUPERVISOR_UPDATES', 30632);
define('SUPERVISOR_ORGS', 30633);
define('SUPERVISOR_ABOUT', 30634);
define('SUPERVISOR_CONTACT', 30635);
define('SUPERVISOR_INTRO_TEXT', 30636);
define('SUPERVISOR_ACTIVITIES', 30637);
define('SUPERVISOR_KNOWLEDGE_MAP', 30638);
