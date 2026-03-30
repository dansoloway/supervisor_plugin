# Going live: production checklist

Short path from "plugin in repo" to "site works in production." Details live in [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md).

## `config.php`: page IDs (important)

[`config.php`](../config.php) ships with a **commented block** of `define('SUPERVISOR_*', …)` lines.

- **Uncomment and set real post IDs** (from **Pages → Edit**: URL contains `post=####`) when:
  - supervisor pages use **different slugs** than [inc/supervisor-pages.php](../inc/supervisor-pages.php), or
  - the site looks broken after deploy (wrong layout, missing nav, scripts not loading on the right pages).

- **Leave those lines commented** only when every supervisor page slug **exactly matches** the manifest (typical fresh install after `wp supervisor bootstrap-pages`).

The plugin can still pick the **correct PHP template** for some setups when IDs are unset (slug fallback), but **menus, permalinks, and conditional assets** depend on the numeric page IDs. **Pinning IDs in `config.php` is the dependable fix** for staging/production that did not start from the default slugs.

`PLUGIN_ROOT` is **not** set in `config.php`; it comes from the main plugin file.

## Automated path (WP-CLI)

If you have [WP-CLI](https://wp-cli.org/) on the server:

1. Deploy the plugin and activate it (or pass `--activate-plugin` once).
2. From the WordPress root, run:
   - `wp supervisor bootstrap-pages` — creates or updates pages by slug (Hebrew titles, slugs in [inc/supervisor-pages.php](../inc/supervisor-pages.php)).
   - Optional: `wp supervisor bootstrap-pages --dry-run` to preview.
3. After pages exist, either keep **slug-only** mode (commented defines in [`config.php`](../config.php), slugs must match the manifest) **or** uncomment the `SUPERVISOR_*` block and set IDs from the admin (recommended if you will change slugs or already have legacy pages).
4. **ACF field definitions:** ensure [`acf-export/field-groups.php`](../acf-export/field-groups.php) contains your latest **Custom Fields → Tools → Export (PHP)** from staging (see [`acf-export/README.md`](../acf-export/README.md)). Commit before deploy so production does not rely on manual field-group rebuild or third-party migration plugins.
5. Optional meta cleanup: `wp supervisor automap-km-tags` (see [inc/knowledge-map-automap.php](../inc/knowledge-map-automap.php)).
6. Smoke-test URLs, supervisor nav, bib cats filter, orgs, updates.

## Manual path (no CLI)

Create pages with the slugs in the deployment guide. Then:

- If slugs match [inc/supervisor-pages.php](../inc/supervisor-pages.php), you can leave [`config.php`](../config.php) without active `SUPERVISOR_*` defines.
- Otherwise, **uncomment the ID block** in [`config.php`](../config.php) and fill in the correct numbers.

## 1. Server and WordPress

- WordPress 5.x+, PHP 7.4+ (8.x recommended), HTTPS as you prefer.
- Deploy plugin to `wp-content/plugins/` (folder name must match activation; **`supervisor-plugin.php`** is the main file).
- **Activate** the plugin (registers CPTs, taxonomies, rewrites).

## 2. `config.php` (summary)

- **No `PLUGIN_ROOT`** in [`config.php`](../config.php) — it is set in `supervisor-plugin.php`.
- **`SUPERVISOR_*`**: optional only in the strict sense — **use explicit defines** whenever slugs do not match the repo manifest or behavior is flaky after deploy.

## 3. Pages and templates

Prefer `wp supervisor bootstrap-pages`, or create pages manually per [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) §2.1. Slugs should match [inc/supervisor-pages.php](../inc/supervisor-pages.php) unless you rely on numeric overrides in [`config.php`](../config.php).

Minimum set usually includes: home, נושאי מפתח / bib categories, updates, orgs, knowledge map, contact, about, intro, activities (if used).

Set **Settings → Reading** if this supervisor home should be the site front page.

## 4. ACF (Advanced Custom Fields)

**Strategy:** Field **definitions** ship inside the plugin repo; field **values** (what editors save) are separate from this and still need your normal content migration if you want staging copy on production.

- **ACF Pro** is required and must be **activated and licensed** on every environment.
- **Where definitions live**
  - **Most field groups:** paste ACF’s **Export → PHP** output into [`acf-export/field-groups.php`](../acf-export/field-groups.php). [`supervisor-plugin.php`](../supervisor-plugin.php) loads that file when present. Step-by-step: [`acf-export/README.md`](../acf-export/README.md).
  - **Built into PHP already:** e.g. org country select in [`inc/acf-org-country-select.php`](../inc/acf-org-country-select.php) — **exclude** that group from the ACF export (or you will register the same fields twice).
  - **Taxonomy term UI:** e.g. `qa_knowledge_map_category` is registered in code ([`inc/register_posts_and_tax.php`](../inc/register_posts_and_tax.php)); not part of the PHP field-group export.
- **After you change fields on staging:** re-export to PHP, replace `acf-export/field-groups.php`, commit, deploy (same as code).
- **Field groups** must match what templates call (`get_field`, `get_fields`). Cross-check [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) §3.2 with live templates.
- **Field values** (post content, repeaters, etc.) are **not** in `acf-export/` — use DB/export tools, [`development/import-content.php`](../development/import-content.php), or manual entry as appropriate.

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

1. Deploy code + activate supervisor plugin (`wp plugin activate …` or Admin).
2. **`wp supervisor bootstrap-pages`** (or manual pages with correct slugs / [`config.php`](../config.php) IDs).
3. Set **[`config.php`](../config.php)** — uncomment `SUPERVISOR_*` and real IDs when slugs don’t match the manifest or the site misbehaves.
4. **ACF Pro:** activate and license. Ensure **[`acf-export/field-groups.php`](../acf-export/field-groups.php)** reflects your latest staging export (see §4 and [`acf-export/README.md`](../acf-export/README.md)) — no separate “import field groups” step on production if that file is current.
5. **Migrate or enter content** (terms + posts + ACF **values** if needed).
6. Smoke-test; optional `wp supervisor automap-km-tags`.
7. Turn off staging-only plugins/logging; set `WP_DEBUG` appropriately.

For narrative field lists and the manual field-group reference, use **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** §3.
