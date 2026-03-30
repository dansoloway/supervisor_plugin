# Supervisor plugin documentation

All project docs for this plugin live in **`doc/`** (except vendor readmes under `assets/flag-icons-main/`).

| Document | Purpose |
|----------|---------|
| [GOING_LIVE.md](GOING_LIVE.md) | **Production checklist** — deploy, `config.php`, ACF, content, go-live order |
| [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) | Full step-by-step deployment, pages, ACF field groups, theme header |
| [SUPERVISOR_EDITOR_GUIDE.md](SUPERVISOR_EDITOR_GUIDE.md) | Editor / content workflow |
| [DESIGN_SPEC.md](DESIGN_SPEC.md) | Design / UX spec |
| [FRONTEND_REFERENCE.md](FRONTEND_REFERENCE.md), [FRONTEND_COMPONENT_MAP.md](FRONTEND_COMPONENT_MAP.md) | Frontend structure and component map |
| [REDESIGN_IMPLEMENTATION_MAP.md](REDESIGN_IMPLEMENTATION_MAP.md), [CHANGES_SUMMARY.md](CHANGES_SUMMARY.md) | Redesign notes and change log |
| [CSS_CONSOLIDATION_PLAN.md](CSS_CONSOLIDATION_PLAN.md) | CSS architecture notes |
| [development/](development/) | Dev tools: content management notes, test README |

Compare any ACF field names in **DEPLOYMENT_GUIDE** with live templates (`get_field(…)` / `inc/register_posts_and_tax.php`) before production — names sometimes drift.
