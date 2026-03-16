# Supervisor Plugin — Changes Summary

**Branch:** `shared-design-layer`  
**Purpose:** Share with ChatGPT or another AI for context when continuing work.

---

## What We Did

### 1. Created a Shared Design Layer

Added a reusable styling foundation to `assets/css/supervisor-styles.css` so components share common design patterns before page-specific rules.

**Design tokens added to `:root`:**
- `--sv-link-external` — #0000EE (traditional link blue for external/source links)
- `--sv-blue-050`, `--sv-text-muted`, `--sv-border-input`, `--sv-focus-ring`, `--sv-input-bg`
- Typography: `--sv-fs-body`, `--sv-fs-body-lg`, `--sv-fs-h1` through `h4`, `--sv-lh-body`, `--sv-fw-*`
- Spacing: `--sv-section-gap`, `--sv-block-gap`, `--sv-inline-gap`
- `--sv-radius-none`, `--sv-shadow-input`

**Shared design layer rules include:**
- Typography (h1–h4, body, paragraphs)
- Card base (`.card`)
- Button base (search, filter, knowledge-map, more-updates buttons)
- Input base (text/search fields, placeholders)
- Search fields (`.supervisor-search`, `.search-input-container`)
- Filter groups (`.filter-section`, `.taxonomy-filters`, `.filter-group-title`)
- Section spacing (intro, knowledge map, search)
- Focus states

### 2. Moved Inline CSS from Templates into the Stylesheet

Removed all `style=` attributes and `onmouseover`/`onmouseout` handlers from PHP templates and replaced them with CSS classes.

**Template-derived styles block in CSS:**
- `.search-results-container` — `display: none` (hidden until AJAX populates)
- `.initial-content--visible` — forces display when coming from homepage with highlight
- `.accordion-content` — `display: none` by default; `.accordion-content.is-open` — `display: block`
- `.bib-content` — same pattern for bibliography expandable items
- `.supervisor-knowledge-map .page-title` — centered, margin-bottom 40px
- `.supervisor-knowledge-map .knowledge-map-links a` — traditional link blue, no underline (overrides global link colors)
- `.supervisor-bib-cats` and `.supervisor-taxonomy .intro-text` — text-align right
- `.supervisor-contact-page .page-header` — `grid-column: 1 / -1`
- `.supervisor-single-org .info-value a` — ltr, link blue for URLs
- `.supervisor-taxonomy .bib-original-link a` — link styling
- `.source-link` — traditional link blue, underline (used in updates, search results)
- `.pagination > span` — `margin-inline-end` for spacing

### 3. Switched Accordion/Bibliography Toggles from Inline `display` to Classes

Replaced inline `style="display: none"` / `style="display: block"` with class-based visibility:
- Default: `.accordion-content` and `.bib-content` are `display: none` via CSS
- Open state: `.is-open` toggled by JavaScript

**JavaScript changes:**
- `supervisor-updates.php` (inline script) — accordion logic uses `classList.add/remove('is-open')` instead of `element.style.display`
- `supervisor-search-results.php` (inline script) — same accordion change
- `taxonomy-qa_tags.php` (inline script) — bibliography toggle uses `classList` for `.bib-content`
- `assets/js/ajax-search.js` — generated accordion HTML no longer has inline style; toggle logic uses `classList`

### 4. Removed Link Injection via `preg_replace`

The Updates page used a `preg_replace` to inject `style="color: #0000EE !important; text-decoration: underline !important;"` into every `<a>` in post content. That was removed. Link appearance is now controlled by `.update-content-text a` rules in the stylesheet.

### 5. Reference Docs Created Earlier (in same branch)

- **FRONTEND_REFERENCE.md** — Architecture, templates, UI components, styling system
- **FRONTEND_COMPONENT_MAP.md** — Redesign-focused map: component name, file, markup source, CSS, JS, dynamic output, risk level

---

## Files Modified

| File | Changes |
|------|---------|
| `assets/css/supervisor-styles.css` | New tokens, shared design layer, template-derived styles; removed duplicate `.source-link` |
| `assets/js/ajax-search.js` | Accordion HTML and toggle logic use classes instead of inline styles |
| `templates/supervisor-knowledge-map.php` | Removed inline styles and event handlers from title and links |
| `templates/supervisor-bib_cats.php` | Removed inline styles from page-title and intro-text |
| `templates/supervisor-contact.php` | Removed inline style from page-header |
| `templates/supervisor-updates.php` | Removed inline styles; use `initial-content--visible`, `is-open`; removed `preg_replace`; accordion JS uses `classList` |
| `templates/supervisor-search-results.php` | Same pattern for source links, pagination, accordion |
| `templates/single-qa_orgs.php` | Replaced inline link styles with `info-value-link` class |
| `templates/taxonomy-qa_tags.php` | Removed inline styles; bib toggle uses `classList` |
| `FRONTEND_REFERENCE.md` | New |
| `FRONTEND_COMPONENT_MAP.md` | New |

---

## Behavior Preserved

- PHP logic unchanged (queries, ACF, taxonomies)
- RTL support kept
- Existing selectors retained (no renames for JS targets)
- Accordion open/close still works
- Bibliography expand/collapse still works
- AJAX search results still work
- Homepage → Updates highlight flow still works

---

## What to Test

1. **Updates page** — Accordion expand/collapse; links in update content; homepage highlight flow
2. **AJAX search (Updates page)** — Search, filter, result accordions
3. **Search results page** — Accordions; always-visible bibliography items
4. **Taxonomy (Key Topics)** — Bibliography item expand/collapse
5. **Knowledge map page** — Link styling without event handlers
6. **Organization detail page** — External links (website, report) in info boxes
7. **Mobile** — Layout and touch targets at 768px and 480px

---

## Notes for ChatGPT

- The plugin is WordPress, RTL (Hebrew), and uses custom post types (`qa_updates`, `qa_orgs`, `qa_bib_items`).
- Main stylesheet: `assets/css/supervisor-styles.css` (~3700 lines). Shared layer is near the top; template-derived styles follow.
- Accordion elements use `data-accordion="id"` and `id="accordion-id"`. The `.is-open` class controls visibility.
- `get_header('supervisor')` loads a theme header; the plugin does not define it.
