# Redesign Implementation Map

**Reference:** DESIGN_SPEC.md, FRONTEND_COMPONENT_MAP.md  
**Purpose:** Locate where each redesign page/component is implemented before making changes.

---

## Page / Component Location Table

| Page / Component | Template File | Partials Used | CSS Selectors | JS File | Dynamic Data | Shared Component | Risk Level |
|------------------|---------------|---------------|---------------|---------|--------------|-----------------|------------|
| **1. Homepage** | `templates/supervisor-home.php` | `inc/navigation.php` | `.supervisor-home`, `.supervisor-page-container`, `.intro-text-block`, `.intro-content`, `.knowledge-map-section`, `.knowledge-map-card`, `.knowledge-map-diagram`, `.knowledge-map-image`, `.knowledge-map-button`, `.search-section`, `.supervisor-search`, `.supervisor-search-bar`, `.search-button`, `.updates-box`, `.card`, `.updates-list`, `.update-item`, `.update-title`, `.update-date`, `.more-updates-button` | `home-search.js` (conditional) | `WP_Query` (qa_updates, 5 posts), `get_field('qa_updates_date')`, `get_permalink()`, `plugins_url()` | Cards, Search fields | Medium |
| **2. Updates page** | `templates/supervisor-updates.php` | `inc/navigation.php`, `inc/search.php` | `.supervisor-content-wrapper.supervisor-two-column`, `.qa-updates-list`, `.initial-content`, `.search-results-container`, `.qa-update-item`, `.accordion-header`, `.accordion-content`, `.accordion-icon`, `.light-green-bkg`, `.update-content-text`, `.taxonomy-boxes`, `.source-link`, `.pagination` | `ajax-search.js`, inline accordion script | `WP_Query` (qa_updates), ACF: `qa_updates_date`, `qa_updates_link`, taxonomies: `qa_themes`, `qa_tags`, `$_GET['highlight']`, `paginate_links()` | Cards, Accordion, Filters, Search fields, Pagination | Medium |
| **3. AJAX search results** | None (JS-rendered into `.search-results-container`) | `inc/search.php` (parent) | `.search-results-container`, `.qa-update-item`, `.accordion-header`, `.accordion-content`, `.light-green-bkg`, `.taxonomy-boxes`, `.source-link`, `.no-results` | `ajax-search.js` | AJAX `ajax/search_handler.php`, `post_types`: qa_updates, taxonomy terms from response | Accordion, Cards | Medium |
| **4. Search results page** | `templates/supervisor-search-results.php` | `inc/navigation.php` | `.search-results-header`, `.search-query`, `.results-count`, `.search-results-list`, `.search-results-content`, `.qa-update-item`, `.accordion-header`, `.accordion-content`, `.taxonomy-boxes`, `.taxonomy-term`, `.light-green-bkg`, `.source-link`, `.no-results`, `.pagination` | Inline accordion script | `$_GET['supervisor_search']`, `WP_Query` (qa_updates, qa_orgs, qa_bib_items), title/content/tax/meta search, ACF per type, `paginate_links()` | Accordion, Cards, Pagination | High |
| **5. Study case / article page** | **qa_updates:** Redirected to Updates page (no standalone). **qa_bib_items:** Theme default (`single.php` or `single-qa_bib_items.php` if in theme). Plugin has `single-qa_bibs.php` (orphaned – wrong post type name). | `inc/navigation.php` | `.supervisor-page-container`, `.supervisor-content-wrapper`, content typography | None | **qa_updates:** Not used. **qa_bib_items:** `the_post()`, `get_the_title()`, `the_content()`, ACF `orignial_link` | Cards (if theme uses) | High |
| **6. Knowledge map page** | `templates/supervisor-knowledge-map.php` | `inc/navigation.php` | `.supervisor-knowledge-map`, `.knowledge-layout`, `.col-left`, `.col-right`, `.col-center`, `.arrow-list`, `.group-top`, `.group-bottom`, `.svg-frame`, `.knowledge-map-description`, `.knowledge-map-links`, `.page-title` | None | `get_permalink(SUPERVISOR_BIB_CATS)`, `get_permalink(SUPERVISOR_ORGS)`, hardcoded Hebrew | Cards (links) | High |
| **7. Key topics taxonomy page** | `templates/supervisor-bib_cats.php` | `inc/navigation.php` | `.supervisor-bib-cats`, `.categories-grid`, `.category-card`, `.category-icon`, `.category-title`, `.category-description`, `.category-arrow`, `.page-title`, `.intro-text` | None | `get_terms('qa_tags')`, `get_term_meta('qa_bib_description')`, `get_term_fa_icon()`, `get_term_link()`, `get_post_meta('page_description')` | Icon grid (cards) | Medium |
| **8. Bibliography / research page** | `templates/taxonomy-qa_tags.php` | `inc/navigation.php` | `.supervisor-taxonomy`, `.taxonomy-content`, `.taxonomy-page-title`, `.intro-text`, `.bib-items-list`, `.bib-item`, `.bib-item-header`, `.bib-toggle`, `.bib-content`, `.bib-reference`, `.bib-original-link`, `.bib-description` | Inline bib-toggle script | `get_queried_object()`, `get_term_fa_icon()`, `get_term_meta('qa_bib_description')`, `WP_Query` (qa_bib_items, tax_query, menu_order), ACF `orignial_link` | Accordion (expandable) | High |
| **9. Organization listing page** | `templates/supervisor-qa_orgs.php` | `inc/navigation.php` | `.supervisor-qa_orgs`, `.categories-container`, `.org-card-grid`, `.org-card`, `.org-title`, `.org-description`, `.org-info`, `.page-title` | None | `WP_Query` (qa_orgs), ACF: `qa_subtitle`, `qa_country`, `get_the_terms('qa_themes')` | Cards (org cards) | Medium |
| **10. Organization detail page** | `templates/single-qa_orgs.php` | `inc/navigation.php` | `.supervisor-single-org`, `.org-header`, `.org-main-title`, `.org-info-boxes`, `.info-box`, `.info-item`, `.info-label`, `.info-value`, `.org-content`, `.content-text` | None | `the_post()`, ACF: `qa_yearoffounding`, `qa_services_supervised`, `qa_gov_agency`, `qa_link`, `qa_yearly_report`, `get_the_terms('qa_themes')` | Metadata panel | High |
| **11. Contact page** | `templates/supervisor-contact.php` | `inc/navigation.php`, `contact-form.php` (via `supervisor_display_contact_form()`) | `.supervisor-contact-page`, `.supervisor-two-column`, `.page-header`, `.contact-info-column`, `.contact-info-title`, `.contact-email`, `.contact-form-container`, `.supervisor-contact-form` | None (form submits server-side) | `get_option('supervisor_contact_email')`, contact form fields | Two-column layout | Low |

---

## Shared UI Components — Location & Reuse

| Shared Component | Used By | Template / Source | CSS Selectors | JS |
|------------------|---------|-------------------|---------------|-----|
| **Cards** | Homepage (knowledge map, updates), Updates page, Search results, Key topics, Organization listing | `.card`, `.knowledge-map-card`, `.updates-box`, `.category-card`, `.org-card`, `.qa-update-item` | `.card`, `.knowledge-map-card`, `.updates-box`, `.category-card`, `.org-card` | — |
| **Accordion blocks** | Updates page, Search results page, AJAX search results | `supervisor-updates.php`, `supervisor-search-results.php`, `ajax-search.js` | `.accordion-header`, `.accordion-content`, `.accordion-content.is-open`, `.accordion-icon`, `[data-accordion]` | Inline scripts, ajax-search.js |
| **Filters** | Updates page only | `inc/search.php` | `.filter-section`, `.filter-header`, `.filter-toggle`, `.filter-content`, `.taxonomy-filters`, `.filter-group-title`, `.filter-listbox`, `.checkbox-label` | ajax-search.js (filter toggle) |
| **Search fields** | Homepage, Updates page (via inc/search.php) | `supervisor-home.php`, `inc/search.php` | `.search-section`, `.supervisor-search`, `.supervisor-search-bar`, `.search-input-container`, `.search-input-field`, `.search-button`, `.ajax-search-component` | home-search.js, ajax-search.js |
| **Pagination** | Updates page, Search results page | `supervisor-updates.php`, `supervisor-search-results.php` | `.pagination`, `.pagination a`, `.pagination span.current`, `.pagination > span` | — |
| **Icon grids** | Key topics page | `supervisor-bib_cats.php` | `.categories-grid`, `.category-card`, `.category-icon`, `.category-arrow` | — |
| **Metadata panels** | Organization detail, Search results (taxonomy boxes) | `single-qa_orgs.php`, `supervisor-updates.php`, `supervisor-search-results.php` | `.org-info-boxes`, `.info-box`, `.info-item`, `.info-label`, `.info-value`, `.taxonomy-boxes` | — |
| **Navigation** | All pages | `inc/navigation.php` | `.desktop-menu`, `.mobile-menu`, `.nav-item`, `.dropdown`, `.dropdown-menu`, `.submenu-item` | supervisor-scripts.js |

---

## Template Routing & Reuse

| Template | Routing | Reused Elsewhere |
|----------|---------|------------------|
| `supervisor-home.php` | Page template (SUPERVISOR_HOME) | No |
| `supervisor-updates.php` | Page template (SUPERVISOR_UPDATES) | No |
| `supervisor-search-results.php` | `$_GET['supervisor_search']` or rewrite `supervisor-search` | No |
| `supervisor-knowledge-map.php` | Page template (SUPERVISOR_KNOWLEDGE_MAP) | No |
| `supervisor-bib_cats.php` | Page template (SUPERVISOR_BIB_CATS) | No |
| `supervisor-qa_orgs.php` | Page template (SUPERVISOR_ORGS) | No |
| `supervisor-contact.php` | Page template (SUPERVISOR_CONTACT) | No |
| `taxonomy-qa_tags.php` | `is_tax('qa_tags')` | No |
| `single-qa_orgs.php` | `supervisor_load_templates()` for `is_singular('qa_orgs')` | No |
| `single-qa_updates.php` | **Never loaded** — `template_redirect` 301 to Updates page | No |
| `single-qa_bibs.php` | **Orphaned** — post type is `qa_bib_items`; WP expects `single-qa_bib_items.php` | No |
| `inc/navigation.php` | Required by all supervisor templates | Yes — all pages |
| `inc/search.php` | Required by `supervisor-updates.php` only | Yes — Updates page |

---

## Study Case / Article Page — Clarification

- **qa_updates:** Single update URLs redirect (301) to the Updates page. There is no standalone article view for updates.
- **qa_bib_items:** Single bibliography items would use WordPress template hierarchy. The plugin does not load a custom single template for `qa_bib_items`. `single-qa_bibs.php` exists but targets the old `qa_bibs` post type (removed). Bibliography entries are mainly shown as expandable items in `taxonomy-qa_tags.php` and in search results.
- **Design spec “Study case”:** Most likely maps to the expanded bibliography item content in taxonomy/search contexts, or to a future `single-qa_bib_items.php` template.

---

## Risk Level Legend

| Level | Meaning |
|-------|---------|
| Low | Self-contained, little logic; safe to restyle |
| Medium | Shared layout, taxonomy/ACF; test layout and data |
| High | Complex layout, queries, inline JS, ACF; change with care |

---

## Related Templates (Not in Main Pages List)

| Template | Used For | Notes |
|----------|----------|-------|
| `supervisor-content.php` | SUPERVISOR_ABOUT, SUPERVISOR_INTRO_TEXT, SUPERVISOR_ACTIVITIES | Generic single-column content pages (title + body). Wired via `supervisor-plugin.php` page ID → template mapping. |
| `supervisor-activities.php` | Activities page | Custom layout; may use supervisor-content for simpler content. |
| `taxonomy-qa_bib_cats.php` | Legacy `qa_bib_cats` taxonomy | Replaced by qa_tags; may still exist in codebase. |

---

## Files Reference

| Purpose | Path |
|---------|------|
| Main stylesheet | `assets/css/supervisor-styles.css` |
| Navigation | `inc/navigation.php` |
| AJAX search form | `inc/search.php` |
| Contact form | `contact-form.php` |
| Config (page IDs) | `config.php` |
| Template loader | `supervisor-plugin.php` (template_include filters) |

---

# Recommended Implementation Order

**Goal:** Maximize reuse, minimize breakage, avoid orphaned templates, prioritize biggest visual impact.

---

## 1. Implementation Phases (in order)

| Phase | Scope | Rationale |
|-------|-------|-----------|
| **0. Foundation** | Design tokens, typography, base card/button/input styles, page container spacing | Pure CSS. No template edits. Establishes shared design language so all later pages benefit. Zero risk of breaking logic. |
| **1. Contact + Homepage (simple)** | Contact page, homepage intro block, homepage search bar, homepage knowledge map card | Low risk, self-contained. Contact tests two-column layout. Homepage blocks are isolated; edits have minimal downstream effect. |
| **2. Updates page (+ AJAX results)** | `inc/search.php`, `supervisor-updates.php`, accordion/card styles | Filters live only here. Accordion + card styles apply to both Updates list and AJAX-rendered results (same markup). Single source of truth. |
| **3. Search results page** | `supervisor-search-results.php` | Uses same accordion/card as Updates. After Phase 2, only needs page-specific tweaks; search logic untouched. |
| **4. Key topics + Organization listing** | `supervisor-bib_cats.php`, `supervisor-qa_orgs.php` | Card-based grids. No accordion, no filters. Reuses shared card styles from Foundation. |
| **5. Bibliography + Organization detail** | `taxonomy-qa_tags.php`, `single-qa_orgs.php` | Expandable bibliography (different accordion pattern). Org detail has metadata panel. Higher complexity. |
| **6. Knowledge map full page** | `supervisor-knowledge-map.php` | Complex layout, absolute positioning, RTL. Do last once other pages are stable. |

---

## 2. Why This Order Is Safest

| Factor | How the order addresses it |
|--------|----------------------------|
| **Shared components** | Foundation defines card, button, input, accordion base styles. Homepage and Contact use them first. Updates/Search reuse them; AJAX results inherit Updates markup/CSS automatically. |
| **Shared CSS** | Many rules are under `.supervisor-home`. Foundation introduces design tokens and base classes used across templates. Later phases add/scoped overrides instead of rewriting. |
| **JS-rendered AJAX results** | AJAX outputs same `.qa-update-item`, `.accordion-header`, `.accordion-content` as Updates. Phase 2 styles both server-rendered and AJAX content in one pass. |
| **Reused navigation** | Navigation markup is shared; Phase 0 typography and spacing improve it. Structural changes deferred until rest of site is stable. |
| **No standalone study case** | `single-qa_bibs.php` and `single-qa_updates.php` skipped. Bibliography “article” content lives in taxonomy expandable items (Phase 5). |
| **Filters isolated to Updates** | Filters are in `inc/search.php` only. Phase 2 covers Updates + filters together; no cross-page filter logic to break. |
| **Orphaned templates** | `single-qa_bibs.php`, `single-qa_updates.php` never edited. |

---

## 3. Files Likely Touched per Phase

| Phase | Files | Type of change |
|-------|------|----------------|
| **0. Foundation** | `assets/css/supervisor-styles.css` | Add/refine design tokens, base typography, `.card`, `.foundation-button`, input base, page container spacing |
| **1. Contact + Homepage** | `assets/css/supervisor-styles.css`, `templates/supervisor-contact.php`, `templates/supervisor-home.php` | CSS for contact two-column, intro block, search bar, knowledge map card; minimal template class tweaks if needed |
| **2. Updates + AJAX** | `assets/css/supervisor-styles.css`, `inc/search.php`, `templates/supervisor-updates.php`, `assets/js/ajax-search.js` | Filter sidebar styles, accordion/card styles, pagination; JS only if markup structure changes |
| **3. Search results** | `assets/css/supervisor-styles.css`, `templates/supervisor-search-results.php` | Accordion/card overrides for search context; header/results-count styling |
| **4. Key topics + Org listing** | `assets/css/supervisor-styles.css`, `templates/supervisor-bib_cats.php`, `templates/supervisor-qa_orgs.php` | `.category-card`, `.org-card` styles; grid layout tuning |
| **5. Bibliography + Org detail** | `assets/css/supervisor-styles.css`, `templates/taxonomy-qa_tags.php`, `templates/single-qa_orgs.php` | `.bib-item`, `.bib-content`, `.org-info-boxes`; bib toggle if behavior changes |
| **6. Knowledge map page** | `assets/css/supervisor-styles.css`, `templates/supervisor-knowledge-map.php` | Layout, columns, arrows, responsive stacking |

**Across all phases:** `inc/navigation.php` and `supervisor-scripts.js` — CSS-only until navigation restructure is needed; then careful edits.

---

## 4. What to Avoid Editing Early

| Item | Reason |
|------|--------|
| `single-qa_bibs.php` | Orphaned; post type is `qa_bib_items`. WP never loads this. |
| `single-qa_updates.php` | Never loaded; single updates redirect to Updates page. |
| `ajax/search_handler.php` | Backend. Changing response structure would break AJAX results. |
| Navigation structure (`inc/navigation.php`) | Shared by all pages. structural edits risk site-wide breakage. |
| `supervisor_load_templates()` / redirect logic | Template routing. Changing can break page resolution. |
| ACF field names / structure | Used across templates. Renaming or removing breaks multiple pages. |
| Search query logic (`WP_Query` args in search results) | Complex; search behavior must stay stable during visual redesign. |
