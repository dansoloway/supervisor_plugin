# ACF field groups — deploy with the plugin (no migration plugins)

Field definitions created in **Custom Fields → Field Groups** live in the database. To make them follow **git deploys** with no Duplicator-style tools:

## One-time on staging

1. Open **Custom Fields → Tools → Export**.
2. Under **Select Field Groups**, choose every group that should exist in production **except** any you already register in PHP (this repo registers the org country field in [`inc/acf-org-country-select.php`](../inc/acf-org-country-select.php) — **do not export a duplicate** of that group, or remove it from one place).
3. Choose **Export to PHP**.
4. Copy the **full** file ACF shows (starts with `<?php`, uses `acf_add_local_field_group` or `add_action( 'acf/include_fields', … )`).
5. Replace the contents of [`field-groups.php`](field-groups.php) with that output (one `<?php` at the top only).
6. Commit and push. Deploy the plugin to production as usual.

The main plugin loads [`field-groups.php`](field-groups.php) automatically when ACF Pro is active. Field definitions work **without** re-importing in the admin.

## After you change fields

Repeat the export from staging (or edit `field-groups.php` by hand if you dare), commit, deploy.

## Content (values), not definitions

This only moves **field group definitions**. Post meta / content still needs your normal content migration or manual entry.
