# Going live: production checklist

Short path from “plugin in repo” to “site works in production.” Details live in [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md).

## 1. Server and WordPress

- WordPress 5.x+, PHP 7.4+ (8.x recommended), HTTPS as you prefer.
- Deploy plugin to `wp-content/plugins/` (folder name must match how you reference it; **`supervisor-plugin.php`** is the main file).
- **Activate** the plugin (registers CPTs, taxonomies, rewrites).

## 2. `config.php` (critical)

[`config.php`](../config.php) is environment-specific and **must be set on production** (often **not** committed with real IDs/paths):

- **`PLUGIN_ROOT`** — absolute filesystem path to the plugin directory on the **production** server (staging paths will break includes).
- **`SUPERVISOR_*`** — numeric **page IDs** for every supervisor page (home, bib cats, updates, orgs, knowledge map, contact, about, intro, activities, etc.). Get IDs from **Pages → Edit** URL: `post.php?post=####&action=edit`.

Wrong IDs = wrong templates, broken menus, and localized scripts that never enqueue.

## 3. Pages and templates

Create (or migrate) WordPress **pages** and assign the **Supervisor …** page templates listed in [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) §2.1.

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
- Flush permalinks: **Settings → Permalinks → Save** after deploy.

## 7. Plugin-specific features (sanity checks)

- **Bibliography categories** sidebar: AJAX uses `admin-ajax.php`; no extra server path config.
- **Search**: supervisor search URL / rewrite must work on prod (see plugin rewrite rules).
- **Flag icons** (orgs): `assets/flag-icons-main/` bundled; `qa_country_code` select is registered by the plugin when ACF runs.

## 8. Go-live order (recommended)

1. Deploy code + activate plugin.  
2. Fix **`config.php`** (paths + all page IDs).  
3. Create/fix **pages** + templates.  
4. Install/configure **ACF** + import or recreate field groups.  
5. **Migrate or enter content** (terms + posts).  
6. **Flush permalinks**, smoke-test key URLs, supervisor nav, bib cats filter, orgs, updates.  
7. Turn off staging-only plugins/logging; set `WP_DEBUG` appropriately.

For narrative instructions and sample ACF field lists, use **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** end-to-end.
