# CSS Consolidation Plan: supervisor-styles.css

**Goal:** Reduce duplication without changing visual output.  
**Constraint:** Preserve current near-final homepage appearance; no blind refactoring.

---

## File Structure Overview

The file has evolved through phases with overlapping sections:
- **Lines 135–1130:** "COMPLETE REDESIGN" / "PHASE 1" / "PHASE 2" – token-based, design system
- **Lines 1132–2085:** "HOMEPAGE REDESIGN" / "LAYOUT GRID" / taxonomy / knowledge-map
- **Lines 2229–2627:** "SUPERVISOR UPDATES PAGE LAYOUT" / "AJAX SEARCH" / "QA UPDATES LIST"
- **Lines 3051–3632:** Older sections (SEARCH BAR, UPDATES BOX, CONTACT, KNOWLEDGE MAP CARD)
- **Lines 3633–3990:** "HEADINGS" / "AJAX SEARCH COMPONENT - NEW DESIGN" (hardcoded) + "Phase 2 overrides" (tokens)
- **Lines 3990+:** SEARCH RESULTS, ORGANIZATIONS, etc.

**Key insight:** The "Phase 2 overrides" block (~3912) re-applies design tokens over the "NEW DESIGN" hardcoded block (~3647). The *last* rule wins; Phase 2 defines the effective visual output.

---

## Priority 1: Homepage-Related Duplicates

### 1.1 `.supervisor-home .ajax-search-component` (6 occurrences)

| Location | Context | Role | Properties |
|----------|---------|------|------------|
| ~582 | PHASE 2: UPDATES PAGE | Canonical (tokens) | background, border, radius, padding, gap, direction |
| ~710 | Phase 2 mobile | Media override | padding |
| ~3647 | AJAX SEARCH - NEW DESIGN | **Redundant base** | Hardcoded #E9F1F7, etc. |
| ~3913 | Phase 2 overrides | **Winning override** | Same as ~582 but with !important |
| ~3948 | Mobile (768px) | Media override | padding |
| ~3971 | Mobile (480px) | Media override | padding, gap |

**Canonical:** Lines ~582 block (token-based).  
**Overrides:** Phase 2 block at ~3913 duplicates ~582 with !important, then wins over ~3647.  
**Effective:** Token values (--sv-blue-050, etc.) – same as ~582.

**Consolidation:** Keep one `.supervisor-home .ajax-search-component` block with token values. Remove ~3647 (NEW DESIGN base) and ~3913 (Phase 2 override). Keep media queries at ~710, ~3948, ~3971 – merge into a single set if they duplicate.

**Risk:** **MEDIUM** – multiple media breakpoints; verify desktop + 768px + 480px after change.

---

### 1.2 `.supervisor-home` (4 occurrences)

| Location | Context | Role | Properties |
|----------|---------|------|------------|
| ~249 | Mobile 768px | Media override | font-size: 18px |
| ~327 | Mobile 480px | Media override | font-size: 19px |
| ~2230 | SUPERVISOR UPDATES PAGE LAYOUT | Base layout | max-width, margin, padding, direction |
| ~2445 | Mobile 768px | Media override | padding: 16px |

**Canonical:** ~2230 (base layout).  
**Overrides:** Media queries are additive; no conflicts.

**Consolidation:** Keep all. No redundant base blocks. Low-value consolidation.

**Risk:** **LOW** – structure is intentional.

---

### 1.3 `.supervisor-home .search-title` (5 occurrences)

| Location | Context | Role | Properties |
|----------|---------|------|------------|
| ~600 | PHASE 2 (inside ajax-search) | Tokens | font-size, weight, color, margin |
| ~775 | Search fields (unified) | Tokens | font-family, size, weight, color |
| ~3667 | NEW DESIGN | **Redundant** | font-size 22px !important, etc. |
| ~3952 | Mobile 768px | Override | font-size: 18px |
| ~3976 | Mobile 480px | Override | font-size: 18px |

**Canonical:** ~600 (tokens, inside ajax-search) or ~775 (broader scope). ~775 has `var(--sv-fs-h3)`; ~3667 has `22px`. Phase 2 overrides don’t touch search-title, so ~3667 may affect desktop if it comes later.

**Cascade:** ~775 → ~600 (more specific) → ~3667 (NEW DESIGN, later). ~3667 wins for `.supervisor-home .search-title` on desktop.

**Consolidation:** Decide if 22px or `var(--sv-fs-h3)` is desired. If token-based: remove ~3667, rely on ~600/~775. Merge duplicate mobile overrides (3952, 3976) into one.

**Risk:** **MEDIUM** – font-size affects readability; verify desktop and mobile.

---

### 1.4 `.supervisor-home .search-section` (4 occurrences)

| Location | Context | Role | Properties |
|----------|---------|------|------------|
| ~594 | PHASE 2 (ajax-search) | display, flex, gap | gap: var(--sv-spacing-sm) |
| ~771 | Search fields | margin | margin: 0 0 var(--sv-spacing-md) |
| ~815 | Section spacing | margin-bottom | margin-bottom: var(--sv-section-gap) |
| ~2524 | Mobile | margin-bottom | margin-bottom: 24px |
| ~3660 | NEW DESIGN | **Redundant** | display flex, gap 10px |

**Canonical:** ~594 + ~771 + ~815 define layout and spacing. ~3660 repeats display/flex and uses a different gap (10px vs token).

**Consolidation:** Remove ~3660. Ensure ~594 + ~771 + ~815 together give the correct layout. Merge mobile ~2524 with other mobile rules if possible.

**Risk:** **LOW** – layout is straightforward; check spacing.

---

### 1.5 `.supervisor-home .intro-text-block` (4 occurrences)

| Location | Context | Role | Properties |
|----------|---------|------|------------|
| ~819 | Section spacing | margin | margin: 0 auto var(--sv-block-gap) |
| ~870 | Homepage section spacing | margin | margin: 0 0 var(--sv-spacing-sm) |
| ~1123 | Phase 1 mobile | padding | padding: var(--sv-spacing-md) 0 |
| ~2491 | HOMEPAGE MOBILE | margin-bottom | margin-bottom: 30px |

**Canonical:** ~819 or ~870. ~870 overrides margin (0 0 vs 0 auto).  
**Effective:** ~870 wins (later).

**Consolidation:** Merge ~819 and ~870 into one rule: `margin: 0 0 var(--sv-spacing-sm)`. Keep mobile overrides. Merge duplicate mobile rules if they overlap.

**Risk:** **LOW** – simple margin/padding.

---

### 1.6 `.supervisor-content-wrapper.supervisor-two-column` (4 occurrences)

| Location | Context | Role | Properties |
|----------|---------|------|------------|
| ~170 | STANDARDIZED CONTAINER | Base grid | 4fr 6fr, gap 40px |
| ~242 | Global mobile 768px | Single column | 1fr, display grid |
| ~2455 | Updates page mobile | Flex column | flex, column |
| ~2502 | Homepage mobile | Flex column | flex, column, gap 30px |
| ~2599 | min-width 769px | Desktop reset | grid, 4fr 6fr |
| ~3249 | Contact page | Override | 2fr 9fr |

**Canonical:** ~170 (base). Overrides are page- and breakpoint-specific.

**Consolidation:** Group desktop rules together; group mobile rules by breakpoint. Consider whether ~2455 and ~2502 can share one block with conditional values (e.g. gap).

**Risk:** **MEDIUM** – layout-critical; test desktop, tablet, mobile for updates and contact pages.

---

### 1.7 `.supervisor-contact-page .page-header` (4 occurrences)

| Location | Context | Role | Properties |
|----------|---------|------|------------|
| ~1068 | Contact page design | margin-bottom | var(--sv-spacing-xl) |
| ~1369 | Layout grid | grid-column | 1 / -1 |
| ~3236 | CONTACT PAGE | text-align, margin | right, 40px |
| ~3394 | Mobile 768px | grid-column | 1 |

**Canonical:** ~1068 + ~1369 define spacing and layout. ~3236 overrides margin (40px vs token) and adds text-align.

**Effective:** ~3236 wins for margin and text-align (later). ~1369 and ~3394 control grid.

**Consolidation:** Merge ~1068 and ~3236: keep grid-column from ~1369, use one margin/text-align rule (prefer tokens if possible). Keep mobile grid override.

**Risk:** **LOW** – contact page only.

---

## Priority 2: Knowledge-Map-Related Duplicates

### 2.1 `.supervisor-knowledge-map .col-right` (5 occurrences)

| Location | Context | Role | Properties |
|----------|---------|------|------------|
| ~1679 | col-left,col-right | Shared base | position, width, height, z-index |
| ~1692 | Right side | Positioning | left: 20px |
| ~1843 | Tablet 900px | Stack | position static, width 100%, etc. |
| ~1872 | Mobile 768px | Hide | display: none |
| ~1916 | Mobile 768px (later) | Show again | position static, order 3, etc. |

**Note:** ~1872 hides col-right; ~1916 re-enables it with different layout. They are in the same 768px block but different parts – one hides, the next re-enables. This is intentional for the mobile layout.

**Canonical:** ~1679 + ~1692 (desktop). ~1843 (tablet). Mobile has hide + show logic.

**Consolidation:** No removal. Optionally group all `.col-right` rules together for readability. Confirm that hide/show order is correct.

**Risk:** **LOW** – structure is intentional; only organizational cleanup.

---

### 2.2 `.supervisor-knowledge-map .col-left` (2 occurrences)

| Location | Context | Role |
|----------|---------|------|
| ~1687 | Right side quadrants | right: 20px |
| ~1842–1872, 1915 | Tablet / mobile | Same pattern as col-right |

**Consolidation:** Same as col-right – organizational only.

**Risk:** **LOW**

---

### 2.3 `.supervisor-knowledge-map .knowledge-map-description` (3 occurrences)

| Location | Context | Role |
|----------|---------|------|
| ~1857 | Tablet 900px | display: none |
| ~2011+ | Page styling | Base styles (separate block) |

**Consolidation:** Verify no redundancy; keep as-is if structure is clear.

**Risk:** **LOW**

---

### 2.4 `.supervisor-knowledge-map .col-center` (3 occurrences)

Base + tablet + mobile overrides. Same pattern as col-left/col-right.

**Risk:** **LOW**

---

### 2.5 `.supervisor-knowledge-map .arrow-list`, `.col-right .arrow-list`, etc. (3× each)

Spread across base, tablet, and mobile. Role-based, not redundant.

**Consolidation:** Organizational grouping only.

**Risk:** **LOW**

---

## Priority 3: Ajax-Search / Updates-Related Duplicates

### 3.1 `.supervisor-home .ajax-search-component .search-button` (3 occurrences)

| Location | Role | Properties |
|----------|------|------------|
| ~619 | Tokens | background: var(--sv-blue), hover |
| ~3709 | NEW DESIGN | Position, size, #AFC6E8 |
| ~3925 | Phase 2 override | background: var(--sv-blue) !important |

**Effective:** Phase 2 wins – token blue.

**Consolidation:** Remove ~3709 and ~3925. Keep ~619 plus any layout (position, size) that is not overridden. Ensure layout comes from one place.

**Risk:** **MEDIUM** – button appearance and layout; verify desktop and mobile.

---

### 3.2 `.qa-updates-list` (4 occurrences)

| Location | Context | Role | Properties |
|----------|---------|------|------------|
| ~683 | PHASE 2 | Base | flex column, gap |
| ~2391 | QA UPDATES LIST STYLES | Override | padding: 0 |
| ~2467 | Mobile 768px | order, width | order 1, width 100% |
| ~2622 | min-width 769px | Reset | order initial, width initial |

**Canonical:** ~683 (base). ~2391 adds padding: 0.

**Consolidation:** Merge ~683 and ~2391: one block with flex, gap, and padding: 0. Keep media overrides.

**Risk:** **LOW** – additive properties.

---

### 3.3 `.accordion-content` (2 occurrences)

| Location | Role | Properties |
|----------|------|------------|
| ~1325 | Base | display: none; .is-open: block |
| ~2294 | Override | border: none, padding: 1rem 0 0 0 |

**Canonical:** ~1325. ~2294 adds border/padding.

**Consolidation:** Merge into one block: display logic + border + padding.

**Risk:** **LOW**

---

### 3.4 `.search-results-container` (2 occurrences)

| Location | Role | Properties |
|----------|------|------------|
| ~1315 | Template-derived | display: none |
| ~2280 | Search results | margin-top: 20px |

**Canonical:** ~1315. ~2280 adds margin when visible.

**Consolidation:** Merge: `display: none` plus `margin-top: 20px` (margin applies when used with other display rules). Confirm usage context.

**Risk:** **LOW**

---

### 3.5 `.no-results` (3 occurrences)

| Location | Role | Properties |
|----------|------|------------|
| ~697 | Base (with .search-loading) | padding, text-align, color |
| ~704 | Font size (with .loading-text) | font-size |
| ~4108 | Different block | padding, background, border-radius, margin |

**Effective:** ~4108 wins (later) – different appearance (background, radius).

**Conflict:** Two different designs. First is minimal; second is card-like.

**Consolidation:** Decide which design is desired. If card-like: remove ~697/704 or scope them. If minimal: remove ~4108. Do not merge without a design decision.

**Risk:** **HIGH** – conflicting designs; must choose one.

---

### 3.6 `.supervisor-home .ajax-search-component .search-input-container` (2 occurrences)

| Location | Role |
|----------|------|
| ~606 | PHASE 2 – background, border, radius |
| ~3921 | Phase 2 override – border-radius !important |

**Consolidation:** Merge into one rule with token values. Remove override block.

**Risk:** **LOW**

---

### 3.7 `.supervisor-home .ajax-search-component .filter-listbox` (2 occurrences)

Same pattern: base + Phase 2 override. Merge into one.

**Risk:** **LOW**

---

### 3.8 `.supervisor-home .ajax-search-component .filter-group-title` (2 occurrences)

Same pattern. Merge.

**Risk:** **LOW**

---

### 3.9 `.supervisor-home .ajax-search-component .checkbox-label input` (2 occurrences)

Same pattern. Merge.

**Risk:** **LOW**

---

## Other Notable Duplicates (Lower Priority)

### `.card` (2×)

Shared card shell vs updates/card usage. Check for conflicts before merging.

### `.bib-content` (2×)

Accordion-style display + other styles. Merge if non-conflicting.

### `.pagination span.current` (2×)

Merge into one block.

### `.supervisor_logo_cont` / `.supervisor_logo_cont` (2×)

Typos/duplicates; consolidate spelling and rules.

---

## Phased Cleanup Order

### Phase A: Low-Risk, High-Confidence (Do First)

1. **Merge `.accordion-content`** – Combine ~1325 and ~2294.  
2. **Merge `.search-results-container`** – Combine ~1315 and ~2280.  
3. **Merge `.qa-updates-list`** – Combine ~683 and ~2391.  
4. **Merge contact page header** – Combine ~1068 and ~3236 for `.supervisor-contact-page .page-header`.  
5. **Merge ajax-search sub-selectors** – `.search-input-container`, `.filter-listbox`, `.filter-group-title`, `.checkbox-label input` – merge base and Phase 2 override into single rules.  

**Check:** Homepage, updates page, search results, contact page. No visual changes expected.

---

### Phase B: Ajax-Search Component (Medium Risk)

6. **Remove NEW DESIGN base block** – Delete `.supervisor-home .ajax-search-component` at ~3647 (hardcoded values).  
7. **Remove Phase 2 override block** – Delete ~3913 (duplicate of token block).  
8. **Keep/enhance token block** – Ensure ~582 has all needed properties (including any from Phase 2). Add `!important` only where required to beat other rules.  
9. **Consolidate search-button** – Single `.supervisor-home .ajax-search-component .search-button` rule with layout + token colors.  
10. **Merge duplicate media queries** – Combine ~710, ~3948, ~3971 for ajax-search; merge ~3952 and ~3976 for search-title.  

**Check:** Homepage and updates page – search box, filters, buttons, 768px and 480px breakpoints.

---

### Phase C: Homepage Spacing and Layout (Medium Risk)

11. **Merge `.supervisor-home .intro-text-block`** – Combine ~819 and ~870.  
12. **Merge `.supervisor-home .search-section`** – Remove ~3660; ensure ~594, ~771, ~815 cover layout.  
13. **Resolve `.supervisor-home .search-title`** – Choose token vs 22px; remove redundant block.  
14. **Group `.supervisor-content-wrapper.supervisor-two-column`** – Organize desktop vs mobile rules; avoid duplicate mobile blocks.  

**Check:** Homepage layout, spacing, typography on desktop and mobile.

---

### Phase D: Knowledge-Map (Low Risk, Organizational)

15. **Group knowledge-map rules** – Collect `.col-left`, `.col-right`, `.col-center`, `.arrow-list` rules by breakpoint.  
16. **No removal** – Only reorder for clarity.

**Check:** Knowledge map page at desktop, tablet, and mobile.

---

### Phase E: Design Decision Required (HIGH RISK – Do Last)

17. **`.no-results` design** – Choose minimal (padding/text) vs card (background, radius). Remove or scope the other.  

**Check:** Search results and any “no results” states.

---

## Verification Checklist (Per Phase)

- [ ] Homepage (desktop): hero, search, updates, knowledge map section  
- [ ] Homepage (768px): layout, fonts, spacing  
- [ ] Homepage (480px): layout, fonts, spacing  
- [ ] Updates page: search component, filters, results list  
- [ ] Search results page: layout, no-results state  
- [ ] Contact page: layout, header  
- [ ] Knowledge map page: desktop, tablet, mobile  
- [ ] RTL: correct direction and alignment  

---

## Summary Table

| Selector Group | Occurrences | Canonical | Overrides | Risk | Phase |
|----------------|-------------|-----------|-----------|------|-------|
| `.supervisor-home .ajax-search-component` | 6 | ~582 | ~3647, ~3913, media | Medium | B |
| `.supervisor-home .search-title` | 5 | ~600/~775 | ~3667, mobile | Medium | C |
| `.supervisor-home .search-section` | 4 | ~594, ~771 | ~3660 | Low | C |
| `.supervisor-home .intro-text-block` | 4 | ~819, ~870 | — | Low | C |
| `.supervisor-content-wrapper.supervisor-two-column` | 4+ | ~170 | Page-specific | Medium | C |
| `.supervisor-contact-page .page-header` | 4 | ~1068, ~1369 | ~3236 | Low | A |
| `.supervisor-knowledge-map .col-right` | 5 | ~1679, ~1692 | Media | Low | D |
| `.qa-updates-list` | 4 | ~683 | ~2391 | Low | A |
| `.accordion-content` | 2 | ~1325 | ~2294 | Low | A |
| `.search-results-container` | 2 | ~1315 | ~2280 | Low | A |
| `.no-results` | 3 | Conflicting | — | **High** | E |

---

*Document created: 2025-03-18. No code changes made.*
