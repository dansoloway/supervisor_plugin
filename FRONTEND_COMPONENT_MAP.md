# Frontend Component Map — Redesign Audit

**Purpose:** Implementation-focused reference for matching a PDF mockup redesign. No code changes.

---

## Component Summary

| Component | Exists | Primary Template | Risk |
|-----------|--------|------------------|------|
| Homepage hero / intro block | Yes | supervisor-home.php | Medium |
| Knowledge map (homepage card) | Yes | supervisor-home.php | Low |
| Knowledge map (full page grid) | Yes | supervisor-knowledge-map.php | High |
| Search UI (simple) | Yes | supervisor-home.php | Low |
| Search UI (AJAX / filter) | Yes | inc/search.php | Medium |
| Updates list | Yes | supervisor-updates.php | Medium |
| Updates filter sidebar | Yes | inc/search.php | Medium |
| Stories carousel | **No** | — | N/A |
| Article / study case page | Yes | single-qa_bibs.php, single-qa_updates.php | High |
| Search results page | Yes | supervisor-search-results.php | High |
| Filter sidebar | Yes | inc/search.php | Medium |
| Key topics icon grid | Yes | supervisor-bib_cats.php | Medium |
| Bibliography / research list | Yes | taxonomy-qa_tags.php | High |
| Organization cards | Yes | supervisor-qa_orgs.php | Medium |
| Organization detail page | Yes | single-qa_orgs.php | High |

---

## Component Details

### 1. Homepage Hero / Intro Block

**Component name:** Introductory text block (closest to “hero”)

**Files responsible:**
- **Markup:** `templates/supervisor-home.php` (lines 19–25)

**Markup source:** PHP template — static markup plus optional page meta:
```php
<div class="intro-text-block">
    <div class="intro-content">
        <p>...</p>
        <p>...</p>
    </div>
</div>
```

**CSS:** `assets/css/supervisor-styles.css`
- `.intro-text-block` — ~2510
- `.intro-title` — ~2509
- `.intro-content p` — ~2517
- Mobile: `.supervisor-home .intro-text-block`, `.intro-content p` — ~1479–1487

**JS dependencies:** None

**Dynamic output:** Hardcoded Hebrew paragraphs; no WP content (page meta could be added).

**Risk:** Medium — Shared layout container; edits may affect layout of adjacent blocks.

---

### 2. Knowledge Map — Homepage Card

**Component name:** Knowledge map card (homepage teaser)

**Files responsible:**
- **Markup:** `templates/supervisor-home.php` (lines 28–40)

**Markup source:** PHP template. Uses:
- `get_permalink(SUPERVISOR_KNOWLEDGE_MAP)`
- `plugins_url('assets/img/knowledge_map.svg', dirname(__FILE__))`
- `.knowledge-map-section`, `.knowledge-map-card`, `.knowledge-map-diagram`, `.knowledge-map-image`, `.knowledge-map-button`

**CSS:** `assets/css/supervisor-styles.css`
- `.knowledge-map-section` — ~410
- `.knowledge-map-card` — ~2390
- `.knowledge-map-diagram` — ~2395
- `.knowledge-map-image` — ~2410
- `.knowledge-map-image[src*=".svg"]` — ~2417
- Desktop max-width override — ~2425–2430
- Mobile — ~1503, 1561–1573

**JS dependencies:** None

**Dynamic output:** Permalink constant; SVG path via `plugins_url`.

**Risk:** Low — Self-contained block; safe to restyle.

---

### 3. Knowledge Map — Full Page Grid

**Component name:** Knowledge map diagram with side quadrants (full page)

**Files responsible:**
- **Markup:** `templates/supervisor-knowledge-map.php` (lines 23–62)

**Markup source:** PHP template. Structure:
- `.knowledge-layout` (3 columns: `col-right`, `col-center`, `col-left`)
- `.arrow-list` with `.group-top`, `.group-bottom`
- `.svg-frame` with central SVG
- `.knowledge-map-description`, `.knowledge-map-links`
- Links use `get_permalink(SUPERVISOR_BIB_CATS)`, `get_permalink(SUPERVISOR_ORGS)`

**CSS:** `assets/css/supervisor-styles.css`
- `.supervisor-knowledge-map .knowledge-layout` — ~635
- `.col-center`, `.svg-frame` — ~647–663
- `.col-left`, `.col-right` — ~666–698
- `.arrow-list`, chevrons — ~700–747
- `.knowledge-map-description` — ~999–1020
- `.knowledge-map-links` — ~1022–1037
- Responsive: 900px — ~810; 768px — ~851–994

**JS dependencies:** None

**Dynamic output:** Permalinks; content is hardcoded Hebrew.

**Risk:** High — Absolute positioning, RTL, responsive stacking; easy to break layout.

---

### 4. Search UI — Simple (Homepage)

**Component name:** Simple search bar

**Files responsible:**
- **Markup:** `templates/supervisor-home.php` (lines 45–55)

**Markup source:** PHP template. Form posts to `home_url('/supervisor-search/')` with `name="supervisor_search"`.

**CSS:** `assets/css/supervisor-styles.css`
- `.search-section` — ~1989
- `.supervisor-search`, `.supervisor-search-bar`, `.search-button` — ~1993–2048
- Mobile — ~1510–1524

**JS dependencies:** `assets/js/home-search.js` (loaded only on home page)
- Form submit; Enter key handling
- Depends on: jQuery

**Dynamic output:** `home_url('/supervisor-search/')`

**Risk:** Low — Simple form; JS minimal.

---

### 5. Search UI — AJAX / Filter Sidebar

**Component name:** AJAX search with taxonomy filters

**Files responsible:**
- **Markup:** `inc/search.php` (full file)
- **Used by:** `templates/supervisor-updates.php` (lines 56–58)

**Markup source:** PHP partial. Key elements:
- `.ajax-search-component`
- `#ajax-search-form`
- `.search-section`, `.search-title`, `.search-input-container`, `.search-input-field`, `.search-button`
- `.filter-section`, `.filter-header`, `.filter-toggle`, `.filter-content`
- `.taxonomy-filters`, `.taxonomy-filter`, `.filter-group-title`, `.filter-listbox`
- `.checkbox-label`, `input[name="qa_tags[]"]`, `input[name="qa_themes[]"]`
- `.ajax-search-results`

**CSS:** `assets/css/supervisor-styles.css`
- `.ajax-search-component` — ~1235, 2588
- `.search-section`, `.search-title` — ~2594, 2600
- `.search-input-container`, `.search-input-field` — ~2608, 2620
- `.filter-section`, `.filter-header`, `.filter-listbox` — ~2670–2782
- `.checkbox-label` — ~2784
- `.filter-button` — ~2813
- Mobile — ~2847, 2871

**JS dependencies:**
- `assets/js/ajax-search.js` (jQuery)
- Endpoint: `ajax/search_handler.php`
- Debounce; taxonomy filters; accordion results

**Dynamic output:**
- `get_terms()` for `qa_tags`, `qa_themes` (checkboxes)
- Results rendered via JS from AJAX response

**Risk:** Medium — AJAX, filters, and result rendering are tightly coupled; UI changes need JS updates.

---

### 6. Updates List / Sidebar

**Component name:** Updates list (accordion) + two-column layout

**Files responsible:**
- **Markup:** `templates/supervisor-updates.php` (lines 52–206)

**Markup source:** PHP template. Two-column layout:
- Left: `inc/search.php` (filter sidebar)
- Right: `.qa-updates-list` with `.initial-content`, `.search-results-container` (hidden; filled by AJAX)
- `.qa-update-item`, `.accordion-header`, `.accordion-content`
- `.qa-update-title`, `.title-date-container`, `.update-date`
- `.taxonomy-boxes`, `.update-content-text`

**CSS:** `assets/css/supervisor-styles.css`
- `.supervisor-content-wrapper.supervisor-two-column` — ~190–204, 1584–1607
- `.qa-updates-list` — ~1379
- `.qa-update-item` — ~1272, 1347
- `.qa-update-title`, `.accordion-icon` — ~1383–1429
- `.update-content-text` links — ~1288–1315
- Pagination — ~62–110
- Mobile layout — ~1443–1458

**JS dependencies:**
- `assets/js/ajax-search.js` (results)
- Inline script in template (accordion toggle) — ~215–288

**Dynamic output:**
- `WP_Query` for `qa_updates`
- ACF: `qa_updates_date`, `qa_updates_link`
- Taxonomies: `qa_themes`, `qa_tags`
- `$_GET['highlight']` for pre-opened item
- Link regex for inline styles in content
- `paginate_links()`

**Risk:** Medium — Complex query, ACF, inline JS, content processing; logic changes need care.

---

### 7. Stories Carousel

**Component name:** N/A

**Status:** Not present anywhere in plugin.

**Risk:** N/A — Would be a new component.

---

### 8. Article / Study Case Page

**Component name:** Single bibliography item, single update

**Files responsible:**
- **Markup:** 
  - `templates/single-qa_bibs.php` ( bibliography)
  - `templates/single-qa_updates.php` (updates)

**Markup source:** PHP templates.  
`single-qa_bibs.php`: title + content only.  
`single-qa_updates.php`: title + content, taxonomies, links (ACF).

**Note:** `supervisor_load_templates()` in `supervisor-plugin.php` only wires `single-qa_orgs.php`. `qa_bib_items` and `qa_updates` singles may use theme or default WP templates unless theme overrides.

**CSS:** `assets/css/supervisor-styles.css`
- Shared layout: `.supervisor-page-container`, `.supervisor-content-wrapper`
- Content typography and links (multiple sections)

**JS dependencies:** None (or theme scripts)

**Dynamic output:**
- `the_post()`, `get_the_title()`, `the_content()`
- ACF fields (e.g. on updates)

**Risk:** High — May be served by theme or default template; redesign must account for routing and structure.

---

### 9. Search Results Page

**Component name:** Search results with accordion items

**Files responsible:**
- **Markup:** `templates/supervisor-search-results.php` (full file)

**Markup source:** PHP template. Logic:
- `$_GET['supervisor_search']` for query
- `WP_Query` with title/content, taxonomy, meta search
- `.search-results-header`, `.search-query`, `.results-count`
- `.search-results-list`, `.qa-update-item`
- Accordion for `qa_updates`, `qa_orgs`; expanded by default for `qa_bib_items`
- `.taxonomy-boxes`, `.taxonomy-term`
- Pagination
- `.no-results`

**CSS:** `assets/css/supervisor-styles.css`
- `.new-search-section` — ~2894 (commented out in template)
- `.search-results-header` — ~2971
- `.search-results-content`, `.search-results-list` — ~3000
- `.qa-update-item` — ~1272, 1347
- `.no-results` — ~3009
- `.light-green-bkg` — referenced in template

**JS dependencies:**
- Inline script in template (accordion) — ~364–307

**Dynamic output:**
- Search term from `$_GET`
- `WP_Query` (post types, taxonomies, meta)
- Per-post taxonomies and ACF
- `paginate_links()`

**Risk:** High — Complex search logic; result structure differs by post type; inline JS.

---

### 10. Filter Sidebar

**Component name:** Same as AJAX search filter sidebar

**Files:** `inc/search.php` — see **5. Search UI — AJAX / Filter Sidebar**.

**Risk:** Medium.

---

### 11. Key Topics Icon Grid

**Component name:** Bibliography categories grid

**Files responsible:**
- **Markup:** `templates/supervisor-bib_cats.php` (lines 35–64)

**Markup source:** PHP template. Structure:
- `.categories-grid`
- `.category-card` (links to taxonomy archive)
- `.category-icon`, `.category-title`, `.category-description`, `.category-arrow`
- `get_terms('qa_tags')`
- `get_term_meta($category->term_id, 'qa_bib_description')`
- `get_term_fa_icon()`, `get_term_link()`

**CSS:** `assets/css/supervisor-styles.css`
- `.supervisor-bib-cats .categories-grid` — ~3423
- `.category-card` — ~3433
- `.category-icon` — ~3457
- `.category-title` — ~3481
- `.category-description` — ~3496
- `.category-arrow` — ~3512
- Responsive — ~3568–3613

**JS dependencies:** None

**Dynamic output:**
- `qa_tags` terms
- Term meta: `qa_bib_description`
- Icons via `get_term_fa_icon()`
- `get_term_link()`

**Risk:** Medium — Grid layout and taxonomy-driven content; icon helper is central.

---

### 12. Bibliography / Research List

**Component name:** Taxonomy archive list (expandable bibliography items)

**Files responsible:**
- **Markup:** `templates/taxonomy-qa_tags.php` (lines 49–100)

**Markup source:** PHP template. Structure:
- `.taxonomy-content`, `.page-title`, `.intro-text`
- `.bib-items-list`
- `.bib-item`, `.bib-item-header`, `.bib-toggle-container`, `.bib-toggle`, `.bib-item-content`
- `.bib-reference`, `.bib-original-link`, `.bib-content`, `.bib-description`
- `WP_Query` for `qa_bib_items` in current `qa_tags` term
- ACF: `orignial_link`

**CSS:** `assets/css/supervisor-styles.css`
- `.taxonomy-content` — ~427
- `.bib-items-list`, `.bib-item` — ~473, 477
- `.bib-reference`, `.bib-original-link` — ~489, 498
- `.bib-item-header`, `.bib-toggle` — ~562, 578
- `.bib-description` — ~544
- Link overrides — ~521–537
- Mobile — ~1468

**JS dependencies:**
- Inline script in template — ~109–129 (toggle `.bib-content`)

**Dynamic output:**
- Current term from `get_queried_object()`
- `get_term_fa_icon()`, `get_term_meta()` for description
- `WP_Query` with `tax_query`, `menu_order`
- `get_field('orignial_link')`

**Risk:** High — Toggle logic, taxonomy queries, ACF, link overrides.

---

### 13. Organization Cards

**Component name:** Organization card grid

**Files responsible:**
- **Markup:** `templates/supervisor-qa_orgs.php` (lines 29–75)

**Markup source:** PHP template. Structure:
- `.categories-container`, `.org-card-grid`
- `.org-card` (links to single)
- `.org-title`, `.org-description`, `.org-info`
- ACF: `qa_subtitle`, `qa_country`
- Taxonomy: `qa_themes`

**CSS:** `assets/css/supervisor-styles.css`
- `.org-card-grid` — ~3062
- `.org-card` — ~3069
- `.org-title`, `.org-description` — ~3095, 3105
- `.org-info` — ~3115
- Responsive — ~3158–3202

**JS dependencies:** None

**Dynamic output:**
- `WP_Query` for `qa_orgs`
- ACF: `qa_subtitle`, `qa_country`
- `get_the_terms(..., 'qa_themes')`

**Risk:** Medium — Standard card grid; ACF and taxonomy used.

---

### 14. Organization Detail Page

**Component name:** Single organization detail

**Files responsible:**
- **Markup:** `templates/single-qa_orgs.php` (full file)

**Markup source:** PHP template. Structure:
- `.org-header`, `.org-main-title`
- `.org-info-boxes` (`.info-box`, `.info-item`, `.info-label`, `.info-value`)
- `.org-content`, `.content-text`
- ACF: `qa_yearoffounding`, `qa_services_supervised`, `qa_gov_agency`, `qa_link`, `qa_yearly_report`
- Taxonomy: `qa_themes`

**CSS:** `assets/css/supervisor-styles.css`
- `.supervisor-single-org .org-header` — ~3213
- `.org-info-boxes`, `.info-box`, `.info-item` — ~3229–3268
- `.org-content`, `.content-text` — ~3289–3320
- Responsive — ~3323–3391

**JS dependencies:** None

**Dynamic output:**
- `the_post()`, `get_the_title()`
- ACF link fields (array vs string handling)
- `get_the_terms()`, `apply_filters('the_content', ...)`

**Risk:** High — ACF link handling, info layout, RTL; layout changes need testing.

---

## Shared Components

### Navigation

**Files:** `inc/navigation.php`  
**Used by:** All supervisor templates  
**CSS:** ~1610–1984 (`supervisor-styles.css`)  
**JS:** `supervisor-scripts.js` (dropdown, mobile menu)

### Page Container

**Files:** All templates  
**Classes:** `.supervisor-page-container`, `.supervisor-content-wrapper`, `.supervisor-single-column`, `.supervisor-two-column`  
**CSS:** ~152–220 (`supervisor-styles.css`)

### Header / Footer

**Files:** Theme — `header-supervisor.php`, `footer-supervisor.php`  
**Note:** Not in plugin; provided by theme. DEPLOYMENT_GUIDE.md describes expected structure.

---

## Asset Loading

**Entry:** `supervisor-plugin.php` → `enqueue_alternate_header_assets()`

| Asset | Condition | Dependencies |
|-------|-----------|--------------|
| supervisor-styles.css | All supervisor pages | supervisor-google-fonts |
| supervisor-scripts.js | All supervisor pages | — |
| ajax-search.js | All supervisor pages | jQuery |
| global-search.js | All supervisor pages | jQuery |
| home-search.js | `is_page(SUPERVISOR_HOME)` | jQuery |
| Font Awesome | All supervisor pages | — |

---

## Components Not Present

- **Stories carousel** — Add as new component.
- **Homepage hero** in common sense — Intro block only; no large hero.
- **Dedicated new-search bar on search results** — Markup present but commented out (~155–169 in supervisor-search-results.php).

---

## Risk Levels Summary

| Risk | Meaning |
|------|---------|
| Low | Self-contained, minimal logic; safe to restyle. |
| Medium | Shared layout or taxonomy/ACF; test layout and data. |
| High | Complex layout, queries, inline JS, or ACF; change carefully. |

---

## Quick Lookup

| Design element | Component | Primary file |
|----------------|-----------|--------------|
| Top intro text | Homepage hero / intro | supervisor-home.php |
| Knowledge map image + CTA | Knowledge map card | supervisor-home.php |
| Full diagram + quadrants | Knowledge map grid | supervisor-knowledge-map.php |
| Homepage search input | Simple search | supervisor-home.php |
| Updates page search + filters | AJAX search / filter sidebar | inc/search.php |
| List of updates | Updates list | supervisor-updates.php |
| Carousel of stories | — | Not implemented |
| Single bibliography item | Article / study case | single-qa_bibs.php |
| Single update | Article / study case | single-qa_updates.php |
| Search results list | Search results page | supervisor-search-results.php |
| Category icon grid | Key topics grid | supervisor-bib_cats.php |
| Expandable bibliography list | Bibliography list | taxonomy-qa_tags.php |
| Organization grid | Organization cards | supervisor-qa_orgs.php |
| Single organization | Organization detail | single-qa_orgs.php |
