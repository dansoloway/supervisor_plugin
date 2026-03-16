# Supervisor Plugin Frontend Structure Reference

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Template Structure](#template-structure)
3. [UI Components](#ui-components)
4. [Styling System](#styling-system)
5. [JavaScript Functionality](#javascript-functionality)
6. [Component Patterns](#component-patterns)
7. [File Structure](#file-structure)

---

## Architecture Overview

### Plugin Entry Point
- **Main File**: `supervisor-plugin.php`
- **Asset Loading**: `enqueue_alternate_header_assets()` function (lines 58-150)
- **Template Detection**: Checks for specific template files and post types to conditionally load assets

### Core Principles
- **RTL-First Design**: All templates use `direction: rtl` for Hebrew content
- **Component-Based**: Reusable PHP components in `inc/` directory
- **Responsive Design**: Mobile-first approach with breakpoints at 480px, 768px, 992px, 1200px
- **WordPress Integration**: Uses WordPress template hierarchy, ACF fields, and custom post types

---

## Template Structure

### Template Hierarchy

```
templates/
├── Main Page Templates (supervisor-*.php)
│   ├── supervisor-home.php              # Homepage
│   ├── supervisor-knowledge-map.php     # Knowledge map detail page
│   ├── supervisor-search-results.php    # Search results page
│   ├── supervisor-updates.php           # Updates listing (2-column)
│   ├── supervisor-qa_orgs.php          # Organizations listing
│   ├── supervisor-bib_cats.php         # Bibliography categories
│   ├── supervisor-activities.php        # Activities page
│   ├── supervisor-contact.php           # Contact page (2-column)
│   └── supervisor-content.php          # Generic content page
│
├── Single Post Templates (single-*.php)
│   ├── single-qa_bibs.php              # Single bibliography item
│   ├── single-qa_orgs.php              # Single organization detail
│   └── single-qa_updates.php           # Single update detail
│
└── Taxonomy Templates (taxonomy-*.php)
    ├── taxonomy-qa_tags.php            # QA Tags archive
    └── taxonomy-qa_bib_cats.php       # Legacy bibliography categories
```

### Standard Template Structure

All templates follow this consistent pattern:

```php
<?php
/* Template Name: [Template Name] */
get_header('supervisor');
?>

<div class="supervisor-home [optional-class]">
    <!-- Navigation Menu -->
    <?php
        $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
        // OR: plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">
        <div class="supervisor-content-wrapper [layout-class]">
            <!-- Page-specific content -->
        </div>
    </div>
</div>

<?php get_footer(); ?>
```

### Layout Classes

- **Container**: `.supervisor-page-container` (max-width: 1140px, centered)
- **Content Wrapper**: `.supervisor-content-wrapper` with modifiers:
  - `.supervisor-single-column` - Full width single column
  - `.supervisor-two-column` - Two-column grid (4fr 6fr default, 3fr 7fr for updates)
  - `.supervisor-three-column` - Three-column grid
  - `.supervisor-four-column` - Four-column grid

### Post Types & Taxonomies

**Custom Post Types:**
- `qa_updates` - Quality assurance updates
- `qa_orgs` - Organizations
- `qa_bib_items` - Bibliography items

**Taxonomies:**
- `qa_tags` - Key topics/tags (primary taxonomy)
- `qa_themes` - Areas/themes
- `qa_bib_cats` - Bibliography categories (legacy, appears unused)

---

## UI Components

### 1. Navigation Component
**File**: `inc/navigation.php`

**Purpose**: Main site navigation menu (desktop + mobile)

**HTML Structure**:
```html
<nav class="site-nav supervisor_header_links desktop-menu">
    <a href="..." class="nav-item [active] [dropdown]">
        <span class="nav-text">Title</span>
        <svg class="dropdown-icon">...</svg>
    </a>
    <div class="dropdown-menu">
        <a href="..." class="submenu-item">Submenu Item</a>
    </div>
</nav>

<!-- Mobile Menu -->
<button class="mobile-menu-toggle">
    <div class="hamburger-icon">...</div>
</button>
<nav class="site-nav supervisor_header_links mobile-menu">
    <!-- Mobile menu items -->
</nav>
```

**Key Function**: `render_nav_item($item)` - Renders individual menu items with active states and dropdowns

**Menu Structure**: Defined in `$supervisor_menu` array with:
- `title` - Menu item text
- `url` - Link URL
- `is_active` - Active state detection
- `has_dropdown` - Boolean for dropdown menu
- `submenu` - Array of submenu items

**CSS Classes**:
- `.desktop-menu` - Desktop pill-style navigation
- `.mobile-menu` - Mobile full-screen overlay
- `.nav-item` - Individual menu items
- `.dropdown` - Items with dropdown menus
- `.submenu-item` - Dropdown submenu items

**Usage**: Included in all templates at the top of the content area

---

### 2. AJAX Search Component
**File**: `inc/search.php`

**Purpose**: Advanced search form with taxonomy filters

**HTML Structure**:
```html
<div class="ajax-search-component">
    <form id="ajax-search-form" dir="rtl">
        <div class="search-section">
            <h2 class="search-title">חיפוש חופשי בעדכונים</h2>
            <div class="search-input-container">
                <input type="text" id="search-text" class="search-input-field">
                <button type="button" class="search-button">...</button>
            </div>
        </div>
        <div class="filter-section">
            <div class="filter-header">
                <h3 class="filter-title">סינון לפי:</h3>
                <button class="filter-toggle">⌃</button>
            </div>
            <div class="filter-content">
                <div class="taxonomy-filters">
                    <!-- Key Topics (qa_tags) -->
                    <!-- Areas (qa_themes) -->
                </div>
            </div>
        </div>
    </form>
    <div class="ajax-search-results"></div>
</div>
```

**Features**:
- Collapsible filter section
- Taxonomy checkboxes (`qa_tags`, `qa_themes`)
- AJAX search results rendering
- Debounced search (300ms)

**Usage**: Included in `supervisor-updates.php` template

**JavaScript**: `assets/js/ajax-search.js` handles AJAX functionality

---

### 3. Knowledge Map Card
**Location**: Rendered inline in `supervisor-home.php`

**HTML Structure**:
```html
<div class="knowledge-map-section">
    <div class="knowledge-map-card card">
        <div class="knowledge-map-header">מפת הידע</div>
        <div class="knowledge-map-diagram">
            <a href="..." class="knowledge-map-image-link">
                <img src="..." alt="..." class="knowledge-map-image">
            </a>
        </div>
        <div class="knowledge-map-footer">
            <a href="..." class="knowledge-map-button">למידע נוסף</a>
        </div>
    </div>
</div>
```

**CSS Classes**:
- `.knowledge-map-section` - Section wrapper
- `.knowledge-map-card` - Card container
- `.knowledge-map-image` - SVG image (max-width: 600px on desktop, 70% on mobile)
- `.knowledge-map-button` - CTA button

---

### 4. Updates Box
**Location**: Rendered inline in `supervisor-home.php`

**HTML Structure**:
```html
<div class="updates-box card">
    <div class="updates-header">עדכונים</div>
    <div class="updates-list">
        <div class="update-item">
            <div class="update-content">
                <h3 class="update-title">
                    <a href="...">Title</a>
                </h3>
                <p class="update-date">Date</p>
            </div>
        </div>
    </div>
    <div class="updates-header">
        <a class="more-updates-button" href="...">לעדכונים נוספים</a>
    </div>
</div>
```

**CSS Classes**:
- `.updates-box` - Main container
- `.updates-header` - Blue header strip
- `.update-item` - Individual update item
- `.update-title` - Update title link
- `.update-date` - Formatted date

---

### 5. Accordion Component
**Pattern**: Used across multiple templates

**HTML Structure**:
```html
<div class="accordion-header" data-accordion="ID">
    <!-- Header content -->
    <span class="accordion-icon">⌄</span>
</div>
<div class="accordion-content" id="accordion-ID" style="display: none;">
    <!-- Collapsible content -->
</div>
```

**Usage**: 
- `supervisor-updates.php` - Update details
- `supervisor-search-results.php` - Search result items
- `taxonomy-qa_tags.php` - Bibliography items

**JavaScript**: Handled by `supervisor-scripts.js` (accordion toggle functionality)

---

### 6. Organization Cards
**Location**: `supervisor-qa_orgs.php`

**HTML Structure**:
```html
<div class="org-card-grid">
    <a href="..." class="org-card">
        <h3 class="org-title">Organization Name</h3>
        <p class="org-description">Description</p>
        <div class="org-info">
            <i class="..."></i>
            <span>Info text</span>
        </div>
    </a>
</div>
```

**CSS Classes**:
- `.org-card-grid` - 3-column grid (responsive)
- `.org-card` - Individual card
- `.org-title` - Card title
- `.org-description` - Card description
- `.org-info` - Info items with icons

---

### 7. Category Cards (Bibliography)
**Location**: `supervisor-bib_cats.php`

**HTML Structure**:
```html
<div class="categories-grid">
    <a href="..." class="category-card">
        <div class="category-icon">
            <i class="..."></i>
        </div>
        <h3 class="category-title">Category Name</h3>
        <p class="category-description">Description</p>
        <div class="category-arrow">
            <i class="..."></i>
        </div>
    </a>
</div>
```

**CSS Classes**:
- `.categories-grid` - 4-column grid (responsive)
- `.category-card` - Individual card
- `.category-icon` - Icon container
- `.category-title` - Card title
- `.category-description` - Card description

---

## Styling System

### Main Stylesheet
**File**: `assets/css/supervisor-styles.css` (3,675 lines)

### Design Tokens (CSS Custom Properties)

Located in `:root` selector (lines 356-392):

**Colors**:
- `--sv-blue`: `#B3CBE7` (primary blue)
- `--sv-blue-600`: `#9BB8D9` (hover state)
- `--sv-text`: `#1F2937` (primary text)
- `--sv-text-2`: `#4B5563` (secondary text)
- `--sv-card`: `#FFFFFF` (card background)
- `--sv-border`: `#E5EAF0` (borders)
- `--sv-divider`: `#D7DEE7` (dividers)
- `--sv-bar`: `#EEF4FA` (nav bar background)
- `--sv-bar-border`: `#A0B4C8` (nav bar border)

**Typography**:
- `--sv-font-primary`: `'Assistant', sans-serif`
- `--sv-font-secondary`: `'Assistant', sans-serif`

**Spacing**:
- `--sv-spacing-xs`: `4px`
- `--sv-spacing-sm`: `8px`
- `--sv-spacing-md`: `16px`
- `--sv-spacing-lg`: `24px`
- `--sv-spacing-xl`: `32px`
- `--sv-spacing-xxl`: `48px`

**Border Radius**:
- `--sv-radius-sm`: `4px`
- `--sv-radius-md`: `8px`
- `--sv-radius-lg`: `12px`
- `--sv-radius-xl`: `16px`

**Shadows**:
- `--sv-shadow-sm`: `0 2px 4px rgba(0, 0, 0, 0.1)`
- `--sv-shadow-md`: `0 4px 8px rgba(0, 0, 0, 0.15)`
- `--sv-shadow-lg`: `0 8px 16px rgba(0, 0, 0, 0.2)`

### CSS Organization

1. **Global Overrides** (Lines 3-143)
   - Link color overrides
   - Pagination styling
   - Foundation CSS framework overrides

2. **Layout System** (Lines 152-355)
   - Container system
   - Grid layouts (single, two, three, four columns)
   - Responsive breakpoints

3. **Component Styles** (Grouped by page/component)
   - Navigation (Lines 1610-1984)
   - Search Components (Lines 1234-1266, 2587-2896)
   - Cards (Lines 2054-2159, 2385-2470)
   - Forms (Lines 2230-2327)
   - Updates/QA List (Lines 1378-1608)
   - Knowledge Map Page (Lines 615-1072)
   - Taxonomy/Bibliography (Lines 426-600, 3400-3642)
   - Organizations (Lines 3035-3398)
   - Activities (Lines 1074-1215)
   - Contact Page (Lines 2172-2383)

### Responsive Breakpoints

- **1200px**: Four-column → three-column
- **992px**: Three-column → two-column, four-column → two-column
- **900px**: Knowledge map tablet adjustments
- **768px**: Main mobile breakpoint
  - Single column layouts
  - Larger font sizes (18px base)
  - Mobile menu activation
  - Stacked components
- **480px**: Very small screens with adjusted font sizes

### CSS Architecture Patterns

1. **BEM-like Naming**: `supervisor-*` prefix for component classes
2. **CSS Custom Properties**: Token-based theming system
3. **Component-Based Organization**: Styles grouped by page/component
4. **RTL Support**: `direction: rtl` throughout
5. **Specificity Management**: Uses `!important` for overrides where needed
6. **Mobile-First**: Base styles for desktop, mobile overrides in media queries

---

## JavaScript Functionality

### JavaScript Files

#### 1. `supervisor-scripts.js` (237 lines)
**Purpose**: Core UI functionality

**Features**:
- Mobile menu toggle (hamburger)
- Desktop dropdown menus (hover + click)
- Mobile dropdown menus (click only)
- Accordion toggles
- Click-outside handlers
- Body scroll lock when mobile menu is open

**UI Components**:
- `.mobile-menu-toggle` / `.mobile-menu`
- `.dropdown` / `.dropdown-menu`
- `.accordion-toggle`

**Usage**: Loaded globally on all supervisor pages

---

#### 2. `ajax-search.js` (269 lines)
**Purpose**: AJAX search for QA updates with filters

**Features**:
- Debounced search (300ms)
- Taxonomy filter checkboxes (`qa_themes[]`, `qa_tags[]`)
- Loading spinner with Hebrew text
- Results rendered as accordion items

**AJAX Endpoint**: `/wp-content/plugins/supervisor-plugin/ajax/search_handler.php`

**UI Components**:
- `#search-text` input
- `#search-submit` button
- `.filter-toggle` (collapsible filters)
- `.ajax-search-results` container

**Dependencies**: jQuery

**Usage**: Loaded globally, used with `inc/search.php` component

---

#### 3. `global-search.js` (114 lines)
**Purpose**: Global search across multiple post types

**Features**:
- Searches `qa_orgs`, `qa_updates`, `qa_bib_items`
- Taxonomy filters
- Simple list of links with post type labels (Hebrew)

**AJAX Endpoint**: `/wp-content/plugins/supervisor-plugin/ajax/search_handler.php`

**UI Components**:
- `#global-search-submit` button
- `#global-search-text` input
- `.global-search-results` container

**Dependencies**: jQuery

**Usage**: Used on global search pages (`supervisor-search-results.php`)

---

#### 4. `home-search.js` (27 lines)
**Purpose**: Simple form submission for home page search

**Features**:
- No AJAX - submits form normally
- Handles Enter key (keycode 13)
- Redirects to search results page

**UI Components**:
- `.supervisor-search .supervisor-search-bar` input
- `.supervisor-search .search-button` button

**Dependencies**: jQuery

**Usage**: Loaded conditionally on home page only (`is_page(SUPERVISOR_HOME)`)

---

#### 5. `admin.js` (33 lines)
**Purpose**: Admin drag-and-drop sorting for bibliography items

**Features**:
- SortableJS integration
- AJAX save of item order
- Security nonce validation

**AJAX Endpoint**: `qaBibAjax.ajaxurl` (localized from PHP)

**UI Components**:
- `.sortable` containers
- `.sortable-item` elements

**Dependencies**: SortableJS (CDN)

**Usage**: Admin only, enqueued in `inc/bib_admin_page.php`

---

### JavaScript Dependencies

**External Libraries**:
- **jQuery**: Used by `ajax-search.js`, `global-search.js`, `home-search.js`
- **SortableJS**: Used by `admin.js` (loaded via CDN)
- **Font Awesome**: Loaded globally (not used directly by JS)

**WordPress Localization**:
- `qaBibAjax` object (for `admin.js`) - contains `ajaxurl` and `nonce`

---

## Component Patterns

### 1. Page Container Pattern

All templates use this wrapper structure:

```php
<div class="supervisor-home [page-specific-class]">
    <?php require_once $nav_path; ?>
    
    <div class="supervisor-page-container">
        <div class="supervisor-content-wrapper [layout-class]">
            <!-- Content -->
        </div>
    </div>
</div>
```

### 2. Navigation Include Pattern

Two path resolution methods (inconsistent):

```php
// Method 1: Using PLUGIN_ROOT constant
$nav_path = PLUGIN_ROOT . 'inc/navigation.php';

// Method 2: Using plugin_dir_path()
$nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';

if (file_exists($nav_path)) {
    require_once $nav_path;
}
```

### 3. Taxonomy Display Pattern

Common pattern for showing taxonomy terms:

```php
$tags = get_the_terms($post_id, 'qa_tags');
if ($tags) {
    echo '<p><strong>נושאי מפתח:</strong> ';
    foreach ($tags as $tag) {
        echo '<span class="taxonomy-term">';
        echo '<i class="' . get_term_fa_icon($tag->term_id, 'fas fa-tag') . '"></i>';
        echo esc_html($tag->name);
        echo '</span>';
    }
    echo '</p>';
}
```

### 4. Date Formatting Pattern

Consistent Hebrew date formatting:

```php
$raw_date = get_field('qa_updates_date'); // ACF date field
if ($raw_date) {
    $formatted_date = date_i18n('F Y', strtotime($raw_date));
} else {
    $formatted_date = get_the_date('F Y');
}
```

### 5. Link Styling Pattern

Consistent blue link styling:

```php
style="color: #0000EE !important; text-decoration: underline !important;"
```

### 6. Helper Functions

**From `inc/register_posts_and_tax.php`**:
- `get_term_fa_icon($term_id, $default_icon)` - Retrieves Font Awesome icon for taxonomy terms
- `display_taxonomies($post_id, $taxonomy_labels)` - Displays taxonomy terms for a post

**From `contact-form.php`**:
- `supervisor_display_contact_form()` - Generates contact form HTML (can be called directly or via shortcode)

---

## File Structure

### Complete Directory Structure

```
supervisor_plugin/
├── supervisor-plugin.php          # Main plugin file (asset loading)
├── config.php                     # Configuration constants
│
├── templates/                     # Page templates
│   ├── supervisor-home.php
│   ├── supervisor-knowledge-map.php
│   ├── supervisor-search-results.php
│   ├── supervisor-updates.php
│   ├── supervisor-qa_orgs.php
│   ├── supervisor-bib_cats.php
│   ├── supervisor-activities.php
│   ├── supervisor-contact.php
│   ├── supervisor-content.php
│   ├── single-qa_bibs.php
│   ├── single-qa_orgs.php
│   ├── single-qa_updates.php
│   ├── taxonomy-qa_tags.php
│   └── taxonomy-qa_bib_cats.php
│
├── inc/                           # Reusable components
│   ├── navigation.php            # Main navigation menu
│   ├── search.php                # AJAX search component
│   ├── sidebar.php               # Sidebar (unused)
│   ├── top_search_bar.php        # Simple search bar (unused)
│   ├── admin-menu.php            # Admin menu registration
│   ├── register_posts_and_tax.php # Post types & taxonomies
│   └── bib_admin_page.php        # Bibliography admin
│
├── assets/
│   ├── css/
│   │   ├── supervisor-styles.css  # Main stylesheet (3,675 lines)
│   │   └── styles-orig.css        # Legacy stylesheet (640 lines)
│   │
│   ├── js/
│   │   ├── supervisor-scripts.js  # Core UI functionality
│   │   ├── ajax-search.js         # AJAX search for updates
│   │   ├── global-search.js       # Global search
│   │   ├── home-search.js         # Home page search
│   │   └── admin.js               # Admin drag-and-drop
│   │
│   └── img/
│       ├── knowledge_map.svg
│       ├── knowledge_map.png
│       └── logo.svg
│
├── ajax/                          # AJAX handlers
│   ├── search_handler.php
│   ├── bib_save_item_order.php
│   └── index.php
│
├── contact-form.php               # Contact form functionality
├── acf-location-rules.php         # ACF field location rules
└── create-plugin-user-role.php   # Custom user role
```

### Asset Loading Flow

```
supervisor-plugin.php
  └── enqueue_alternate_header_assets()
      ├── Enqueue CSS: supervisor-styles.css
      ├── Enqueue JS: supervisor-scripts.js (global)
      ├── Enqueue JS: ajax-search.js (global)
      ├── Enqueue JS: global-search.js (global)
      ├── Enqueue JS: home-search.js (home page only)
      └── Enqueue Font Awesome (global)
```

### Template → Component Flow

```
Template (e.g., supervisor-home.php)
  ├── Includes: inc/navigation.php
  ├── Renders: Knowledge map card (inline)
  ├── Renders: Search bar (inline)
  └── Renders: Updates box (inline)
```

---

## Key Observations

### Strengths
1. **Consistent Structure**: All templates follow the same container pattern
2. **Component Reusability**: Navigation component used across all templates
3. **Design System**: CSS custom properties provide consistent theming
4. **RTL Support**: Comprehensive RTL support throughout
5. **Responsive Design**: Well-defined breakpoints and mobile-first approach

### Areas for Improvement
1. **Path Resolution**: Inconsistent use of `PLUGIN_ROOT` vs `plugin_dir_path()`
2. **Unused Components**: `sidebar.php` and `top_search_bar.php` exist but aren't used
3. **Accordion Pattern**: Repeated markup across templates - could be extracted to function
4. **Search Components**: Two search components (`top_search_bar.php` and `search.php`) - consider consolidation
5. **Legacy Template**: `taxonomy-qa_bib_cats.php` appears unused (uses different taxonomy)

### Common Issues
1. **Syntax Errors**: `single-qa_bibs.php` and `single-qa_updates.php` have missing `<?php` tags on line 20
2. **Inline Styles**: Some templates use inline styles that could be moved to CSS
3. **Hardcoded Colors**: Some templates use hardcoded `#0000EE` instead of CSS variables

---

## Quick Reference

### Finding Component Styles

| Component | CSS Location | Lines |
|-----------|--------------|-------|
| Navigation | supervisor-styles.css | 1610-1984 |
| Search Components | supervisor-styles.css | 1234-1266, 2587-2896 |
| Cards | supervisor-styles.css | 2054-2159, 2385-2470 |
| Knowledge Map | supervisor-styles.css | 615-1072 |
| Updates Box | supervisor-styles.css | 1378-1608 |
| Organizations | supervisor-styles.css | 3035-3398 |
| Bibliography | supervisor-styles.css | 3400-3642 |

### Finding Component Markup

| Component | PHP Location |
|-----------|--------------|
| Navigation | `inc/navigation.php` |
| AJAX Search | `inc/search.php` |
| Knowledge Map Card | `templates/supervisor-home.php` (lines 28-40) |
| Updates Box | `templates/supervisor-home.php` (lines 58-102) |
| Accordion | Multiple templates (pattern) |

### Finding JavaScript Handlers

| Functionality | JS File | Key Functions |
|----------------|--------|---------------|
| Mobile Menu | supervisor-scripts.js | Toggle handlers |
| Dropdown Menus | supervisor-scripts.js | Click/hover handlers |
| Accordion | supervisor-scripts.js | Toggle functionality |
| AJAX Search | ajax-search.js | Search, filter, render |
| Global Search | global-search.js | Search, render |
| Admin Sortable | admin.js | SortableJS integration |

---

## Version Information

- **Document Created**: 2024
- **Plugin Version**: 1.0
- **CSS Version**: 1.0.3 (as of last update)
- **WordPress Version**: Compatible with modern WordPress versions
- **PHP Version**: Requires PHP 7.4+

---

## Notes

- This document reflects the current state of the codebase
- CSS line numbers may shift as the stylesheet is updated
- Some components may have additional functionality not documented here
- For specific implementation details, refer to the actual source files
