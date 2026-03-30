# Going live: production checklist

Short path from "plugin in repo" to "site works in production." Details live in [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md).

## Automated path (WP-CLI)

If you have [WP-CLI](https://wp-cli.org/) on the server:

1. Deploy the plugin and activate it (or pass `--activate-plugin` once).
2. From the WordPress root, run:
   - `wp supervisor bootstrap-pages` — creates or updates pages by slug (Hebrew titles, slugs in [inc/supervisor-pages.php](../inc/supervisor-pages.php)).
   - Optional: `wp supervisor bootstrap-pages --dry-run` to preview.
3. Page IDs need no manual copy: `SUPERVISOR_*` constants are set on `plugins_loaded` from those slugs (unless you override in [`config.php`](../config.php)).
4. `PLUGIN_ROOT` is set automatically from the main plugin file — do not edit paths per environment.
5. Optional meta cleanup: `wp supervisor automap-km-tags` (see [inc/knowledge-map-automap.php](../inc/knowledge-map-automap.php)).
6. Smoke-test URLs, supervisor nav, bib cats filter, orgs, updates.

## Manual path (no CLI)

Same as before: create pages with the slugs in the deployment guide, then either rely on slug resolution (leave [`config.php`](../config.php) empty of `SUPERVISOR_*` defines) or add numeric overrides there if your slugs differ.

## 1. Server and WordPress

- WordPress 5.x+, PHP 7.4+ (8.x recommended), HTTPS as you prefer.
- Deploy plugin to `wp-content/plugins/` (folder name must match activation; **`supervisor-plugin.php`** is the main file).
- **Activate** the plugin (registers CPTs, taxonomies, rewrites).

## 2. `config.php`

[`config.php`](../config.php) is **optional** for most installs:

- **No `PLUGIN_ROOT`** — defined in `supervisor-plugin.php`.
- **`SUPERVISOR_*`** — only if you must override slug-based resolution (non-standard slugs, duplicates, etc.).

Wrong overrides = wrong templates or broken menus.

## 3. Pages and templates

Prefer `wp supervisor bootstrap-pages`, or create pages manually per [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) §2.1 (slugs must match [inc/supervisor-pages.php](../inc/supervisor-pages.php) unless you use `config.php` overrides).

Minimum set usually includes: home, נושאי מפתח / bib categories, updates, orgs, knowledge map, contact, about, intro, activities (if used).

Set **Settings → Reading** if this supervisor home should be the site front page.

## 4. ACF (Advanced Custom Fields)

- **ACF Pro** is expected for `acf_add_local_field_group` and common field setups.
- **Field groups** in the deployment guide must match what templates actually call (`get_field`, `get_fields`). Verify against:
  - org listing: `templates/supervisor-qa_orgs.php` (e.g. `qa_subtitle`, `qa_country`, `qa_org_acronym`; plugin may register **`qa_country_code`** automatically).
  - updates, bibliography items, stories, etc.
- **Term meta** for `qa_tags`: plugin registers **קטגוריית מפת הידע** (`qa_knowledge_map_category`) in code — ensure editors fill this for map/sidebar filtering.
- Export/import ACF JSON between staging and prod if you use it.

## 5. Content and taxonomies

- **נושאי מפתח** (`qa_tags`): create terms; assign **knowledge map category** meta where relevant; assign icons if your setup uses them.
- **Posts**: `qa_updates`, `qa_orgs`, `qa_bib_items`, `qa_stories` as your site uses them.
- Optional: run dev/migration scripts from `development/` only in a controlled way (see [development/README.md](development/README.md)).

## 6. Theme and assets

- Theme must provide **`header-supervisor.php`** / **`footer`** integration as in the deployment guide (or your established theme equivalent) so `get_header('supervisor')` works.
- **Font Awesome** kit URL in `supervisor-plugin.php` (enqueue) — valid on production.
- After deploy or bootstrap: permalinks are flushed by the CLI command; otherwise **Settings → Permalinks → Save**.

## 7. Plugin-specific features (sanity checks)

- **Bibliography categories** sidebar: AJAX uses `admin-ajax.php`; no extra server path config.
- **Search**: supervisor search URL / rewrite must work on prod (see plugin rewrite rules).
- **Flag icons** (orgs): `assets/flag-icons-main/` bundled; `qa_country_code` select is registered by the plugin when ACF runs.

## 8. Go-live order (summary)

1. Deploy code + activate plugin (`wp plugin activate ...` or Admin).
2. **`wp supervisor bootstrap-pages`** (or manual pages + matching slugs).
3. **`config.php`** overrides only if needed.
4. Install/configure **ACF** + import or recreate field groups.
5. **Migrate or enter content** (terms + posts).
6. Smoke-test; optional `wp supervisor automap-km-tags`.
7. Turn off staging-only plugins/logging; set `WP_DEBUG` appropriately.

For narrative instructions and sample ACF field lists, use **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** end-to-end.
