<?php
/**
 * Staging field groups — one-time export into git
 *
 * WordPress admin → Custom Fields → Tools → Export.
 * Select every field group you want in production (see acf-export/README.md).
 * Set output to **PHP** and copy the **entire** generated file, replacing this one.
 *
 * Do not paste the opening `<?php` twice; ACF’s export usually starts with <?php and add_action( … ).
 *
 * Leave this file as-is (comment only) until you run the export; the main plugin
 * only requires this path if the file exists and is readable.
 */

