# Supervisor plugin documentation

All project docs for this plugin live in **`doc/`** (except vendor readmes under `assets/flag-icons-main/`).

| Document | Purpose |
|----------|---------|
| [GOING_LIVE.md](GOING_LIVE.md) | **Production checklist** — deploy, `config.php`, **ACF via [`acf-export/field-groups.php`](../acf-export/field-groups.php)**, content, go-live order |
| [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) | Full deployment: pages, **ACF §3.0 (PHP export into repo)**, §3.2 manual reference, theme header |
| [`acf-export/README.md`](../acf-export/README.md) | **One-time ACF field-group export** — staging → `field-groups.php` → git → production (no migration plugins) |
| [SUPERVISOR_EDITOR_GUIDE.md](SUPERVISOR_EDITOR_GUIDE.md) | Editor / content workflow |
| [DESIGN_SPEC.md](DESIGN_SPEC.md) | Design / UX spec |
| [FRONTEND_REFERENCE.md](FRONTEND_REFERENCE.md), [FRONTEND_COMPONENT_MAP.md](FRONTEND_COMPONENT_MAP.md) | Frontend structure and component map |
| [REDESIGN_IMPLEMENTATION_MAP.md](REDESIGN_IMPLEMENTATION_MAP.md), [CHANGES_SUMMARY.md](CHANGES_SUMMARY.md) | Redesign notes and change log |
| [CSS_CONSOLIDATION_PLAN.md](CSS_CONSOLIDATION_PLAN.md) | CSS architecture notes |
| [development/](development/) | Dev tools: content management notes, test README |

Compare any ACF field names in **DEPLOYMENT_GUIDE** §3.2 with live templates (`get_field(…)` / `inc/register_posts_and_tax.php`) before production — names sometimes drift. The **authoritative definitions** on production should match what is in **`acf-export/field-groups.php`** plus [`inc/acf-org-country-select.php`](../inc/acf-org-country-select.php) (and other code-registered fields).
