---
name: ui-ux
description: DevAdy UI/UX conventions. Invoke before any HTML/CSS/view work in this repo — landing, admin shell, invoice screen, email templates, settings forms. Codifies the dark brand theme, token system, typography stack, RTL awareness, and the "paper on dark" exception for printable documents.
---

# DevAdy UI/UX

DevAdy is a dark-themed, accent-blue brand. Everything user-facing flows from one tiny token system. **Do not introduce a second design language or local color palette** — extend the tokens instead.

## Where the design system lives

| File | Role |
| --- | --- |
| `public/css/brand.css` | Tokens (`:root`) + primitives: `.btn`, `.chip`, `.label-chip`, `.surface`, `.alert`, `.status`, form inputs. Load this first on every page. |
| `public/css/admin.css` | Admin shell only — `.admin-bar`, `.admin-main`, `.card`, `.stat`, tables, `.share-box`. Depends on `brand.css`. |
| `public/css/landing.css` | Public landing-page-only styling. |
| `resources/views/invoice/print.css.php` | Invoice screen + PDF. Special — see "Paper on dark" below. |
| `src/Support/Brand.php` | PHP-side mirror of the tokens (`Brand::DARK`) and the wordmark SVG (`Brand::logo()`). Use for `<meta theme-color>` and inline brand marks. |

## Tokens — never hardcode

Always reference, never duplicate:

```
--bg #0a0e1a  --bg-2 #0f1424  --surface #131a2b  --surface-2 #1a2238
--border #1f2a44  --border-2 #2a3656
--text #e2e8f0  --text-dim #cbd5e1  --muted #94a3b8
--accent #38bdf8  --accent-2 #818cf8  --accent-3 #4ade80
--danger #f87171  --warn #f59e0b
--radius 10px  --radius-lg 18px
```

If you reach for a new color, first ask whether one of these covers it. New tokens go in `brand.css` `:root`, not inline.

## Typography

- Body sans: **Inter** (Latin) / **Cairo** (Arabic, auto-applied via `html[lang="ar"] body`).
- Monospace: **JetBrains Mono** — used for `.chip`, `.label-chip`, table headers, stat labels, language pills, status pills. Anywhere "system / metadata / numeric" voice.
- Display: no separate display face — heavy weight Inter (700–800) with tight letter-spacing (`-0.02em`) handles headings.
- Always preload the three font families together when introducing a new top-level template — see `resources/views/layouts/admin.php` for the canonical `<link>` block.

## Primitives — use them, don't recreate them

- **Buttons**: `.btn.primary` (gradient pill, hero/confirm), `.btn.secondary` (surface pill), `.btn.ghost` (transparent), `.btn.danger`. Modifiers: `.lg`, `.sm`. Plain `.btn` has no fill — always add a variant.
- **Cards**: `.card` (admin) or `.surface` / `.surface-2` (generic).
- **Pills**: `.chip` (neutral metadata), `.label-chip` (uppercase section label), `.status.draft|sent|paid|void` (invoice/lead state).
- **Alerts**: `.alert.ok|err|info|warn`.
- **Forms**: bare `input`/`select`/`textarea` are already styled by `brand.css`. `<label>` is styled to render as uppercase mono. Wrap pairs in `.row` (2-col) or `.row.three` (3-col) for the admin form grid.

## RTL & i18n

Three languages: `en`, `fr`, `ar`. Arabic switches `dir="rtl"` and the Cairo font. **Every new component must be tested mentally in RTL.**

Rules already in the codebase, follow them:
- For mono-typed UI labels (chips, table headers, form labels, stat labels), drop the uppercase + letter-spacing in Arabic — Arabic doesn't have casing and tracked spacing breaks ligatures. Pattern:
  ```css
  .label-chip { letter-spacing: 0.08em; text-transform: uppercase; font-family: var(--mono); }
  html[lang="ar"] .label-chip { font-family: var(--sans-ar); letter-spacing: 0; text-transform: none; }
  ```
- Arrow glyphs in buttons mirror under RTL: `html[dir="rtl"] .btn svg { transform: scaleX(-1); }`.
- Use logical sides (`margin-inline-start`) when adding new spacing. Don't add raw `margin-left`/`right` unless the design is truly direction-locked.
- Numeric, code, and URL strings stay LTR inside RTL pages — add `.ltr-force` (already defined in `brand.css`).

User-facing strings live in `src/Support/i18n/{en,fr,ar}.php` and are read via `t('key')`. **Never hardcode user-visible text in a view** — add a key in all three files.

## Layouts

- Admin pages: render through `resources/views/layouts/admin.php` (sets the sticky `.admin-bar`, brand mark, nav, language switch, logout). Pass `$current` so the active nav item highlights.
- Landing: standalone — does not use the admin layout.
- Invoice screen: standalone — see below.
- Always set `<meta name="theme-color" content="<?= Brand::DARK['bg'] ?>">` and the SVG favicon (`Brand::faviconDataUri()`) on any new top-level template.

## The "paper on dark" exception (printable documents)

Invoices break the dark-theme rule **by design** — they're documents meant to be printed and emailed as PDF.

- The invoice `.page` is always white with dark text and hardcoded slate hex colors. Do **not** swap these for `brand.css` tokens — `pdf.php` does not load `brand.css`, and the PDF renderer would lose them.
- The screen chrome around the page (body backdrop, sticky toolbar, share-box) **is** dark themed and **does** use tokens. It's scoped to `body.invoice-screen` so the PDF view (`pdf.php`, no class) doesn't pick it up.
- `@media print` must reset the body back to white and strip the page's shadow/border so the printed output looks like the PDF.

If you build another printable document (delivery note, quote, receipt), follow the same split: paper `.page` with hardcoded values, optional dark screen chrome under a unique `body` class, `@media print` reset.

## What to do when starting any UI task

1. Read `brand.css` first if you don't already remember the tokens.
2. Reach for an existing primitive (`.btn.primary`, `.card`, `.surface`, `.alert`, `.status`) before inventing new CSS.
3. If a token doesn't exist for the color/spacing you need, add it to `:root` in `brand.css` — don't inline a hex.
4. Add the i18n key in all three language files for any new copy.
5. Visualize RTL: where does an arrow point, where does a label sit, does the mono text need its tracking dropped?
6. For top-level templates, link the fonts + `brand.css` (+ `admin.css` if it's an admin page), set `theme-color`, set the favicon.
