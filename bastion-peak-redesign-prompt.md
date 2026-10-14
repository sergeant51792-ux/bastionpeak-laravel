# Bastion Peak: Full Frontend Redesign Prompt

Paste everything below the line into Claude Code (or your coding agent) from the project root. Keep `structure.md` and `AGENTS.md` in the repo so the agent can read them.

---

## ROLE

You are the design lead and senior front-end engineer for **Bastion Peak**, a Laravel 11 banking infrastructure platform used by finance and operations teams and their customers. You have the taste of a studio that builds trusted fintech products (think the clarity of Monzo, the calm of Mercury, the precision of Wise) and the discipline of an engineer who never breaks working code.

Your job: **redesign the entire front end so every page is well structured, well positioned, modern, and consistent**, with sleek graphics, content, and pop-ups in a modern banking style. **Keep the existing color palette exactly.**

Read `structure.md`, `AGENTS.md`, `public/css/tokens.css`, `resources/css/app.css`, every file in `resources/views/components/`, and every file in `resources/views/layouts/` before changing anything.

---

## 1. HARD CONSTRAINTS (never violate)

1. **Colors stay.** Use only the existing design tokens. Do not introduce new brand colors. Light and dark values:

   | Token | Light | Dark |
   |---|---|---|
   | `--bg` | `#F1F5F9` | `#0F172A` |
   | `--surface` | `#FFFFFF` | `rgba(30,35,60,0.45)` |
   | `--primary` | `#1E3A8A` | `#3B82F6` |
   | `--primary-dark` | `#0F2557` | `#2563EB` |
   | `--accent` | `#3B82F6` | `#60A5FA` |
   | `--success` | `#059669` | `#4ADE80` |
   | `--danger` | `#DC2626` | `#F87171` |
   | `--warning` | `#D97706` | `#FB923C` |
   | `--text` | `#0F172A` | `#F1F5F9` |
   | `--text-muted` | `#64748B` | `#94A3B8` |

   You may **add derived tokens** (for example `--surface-raised`, `--border`, `--ring`, `--shadow-sm/md/lg`, `--radius-sm/md/lg/xl`, `--space-*`, `--primary-subtle` built with `color-mix()` from existing tokens) in `tokens.css`. Never hardcode hex or rgb values in Blade, JS, or component CSS.
2. **Do not change backend behavior.** No edits to services, models, migrations, policies, events, or routes. Do not rename route names, form field names, `name=` attributes, Livewire properties, or `wire:` bindings.
3. **Filament approval flow stays intact.** `Approvals.php` and its view use Livewire `wire:submit` for approve and reject with no form action fallback. Restyle them; do not restructure the interaction.
4. **Pass `php artisan acceptance:check` at the end.** It enforces: no emojis, CSS variables only (no hex colors), no TODOs, no `console.log`, no `href="#"`, semantic HTML, `tabular-nums` on numbers, accessible labels. Fix every violation you introduce or find.
5. **Icons** come only from the existing `<x-icon>` Lucide wrapper. No emoji, no new icon packs.
6. **Tech stack stays:** Blade, Tailwind, Alpine.js, Livewire 3, Filament 3, Vite, Lottie. No React, no new CSS framework. Add a dependency only if there is no reasonable alternative, and tell me why.
7. Keep the PWA manifest, favicon, and Lottie assets working.

---

## 2. DESIGN DIRECTION

**Subject:** a bank people trust with their money. **Audience:** everyday customers on mobile first, plus finance and ops staff on desktop who process approvals all day. **Primary job:** let people see their money clearly and move it with confidence; let staff review and decide fast without mistakes.

**Personality:** calm, precise, confident. Premium through restraint, not decoration. The blue palette already signals trust, so spend design effort on structure, spacing, type, and motion quality instead of adding color or gradients.

### Before you build, write a short design plan (and show it to me)
- Type scale, spacing scale, radius scale, elevation scale, all expressed as tokens.
- A layout sketch (ASCII is fine) for the customer home, the deposit/transfer flow, and the admin approvals page.
- Review the plan against this brief. If anything reads like a generic fintech template, revise it and say what you changed and why.

### Typography
- Keep **Schibsted Grotesk** for display headings (already in `public/fonts/schibsted.css`).
- Pair it with one clearly distinct body family that has excellent tabular figures (for example IBM Plex Sans). Self-host it like the existing font. Two families maximum.
- A real type scale (display, h1, h2, h3, body, small, caption) with intentional weights and line heights. Body line length under 75 characters.
- **All money, balances, IBANs, card numbers, dates, and table figures use `font-variant-numeric: tabular-nums`** and right-align in tables.
- Balances are the hero of the customer home: large, high-contrast, with the currency symbol set smaller and the decimals visually quieter.
- Sentence case everywhere. Avoid tracked-out all-caps eyebrow labels above every heading, and avoid stray decorative numbering. Use a label only when it carries information.

### Layout and structure
- Mobile-first, then enhance for tablet and desktop. Test at 360, 390, 768, 1024, 1280, 1536.
- A consistent 4/8 spacing rhythm, a max content width, and a clear grid. Every page uses the same page header pattern: title, short supporting line, primary action on the right (or below on mobile).
- Visual hierarchy through size, weight, spacing, and surface level, not through borders on everything. Vary radius and elevation by role (inputs, cards, sheets, modals) instead of one radius and one shadow everywhere.
- Let the primary content (balance, pending approvals) be visibly larger than the rest.

### NO CARD CLUTTER (strict rule)
The most common flaw in generated UI is wrapping everything in boxes. Do **not** do that.

- **Default to no container.** Content sits directly on the page background, separated by whitespace, headings, and at most a thin divider line. A box is the exception, not the default.
- **Never nest containers.** No card inside a card, no bordered box inside a panel, no pill inside a box inside a section.
- **No grids of identical cards.** If three or more items are similar, show them as a clean list or table with row dividers, not as three matching boxes.
- **A surface is allowed only when it earns its place**, meaning one of these: (a) a real physical object (the bank card visual), (b) the single hero element of a page (the main balance), (c) an overlay (modal, bottom sheet, toast, dropdown), (d) an input or a form control, (e) a table that needs a scroll boundary on mobile.
- **Maximum one raised surface per screen section**, and at most two on any full screen. Everything else is flat.
- **No decorative borders, shadows, or background tints** on groups of content. Use spacing and type weight to group instead. Use a divider only when it separates distinct items in a list.
- **Icons do not need tinted circles or squares behind them.** Use a plain icon next to its label. Use a tinted shape only to carry status meaning.
- **Stats are plain figures**: a number, a small label, and a trend, set in a row with whitespace or vertical dividers between them. No box per stat.
- **One radius family and a calm shadow scale.** Most things have no shadow at all; shadows are reserved for overlays and the hero element.
- Before finishing each page, do a **container audit**: count the boxes on the screen, then remove or flatten every one that does not meet the allowed list above.
- Customer layout: sidebar on desktop, **bottom nav on mobile** with a clear active state, a clean header (greeting, notifications, avatar, hide-balance toggle). Safe-area padding for mobile browsers and the PWA.
- Admin layout: dense but breathable, sticky table headers, persistent filters, keyboard-friendly.

### Motion
- One orchestrated moment per key screen (for example balances counting in on the home page). No fade-and-slide-up on every section, no hover animation on every card.
- Motion that responds to user action is welcome: sheet open and close, toggle, approve and reject confirmation, copy-to-clipboard feedback, success states.
- Respect `prefers-reduced-motion` everywhere, including Lottie (swap to a static frame).

### Accessibility and quality floor
- WCAG AA contrast in **both** light and dark mode (check `--text-muted` on `--surface` in dark mode, which has a translucent surface).
- Visible focus rings on every interactive element, using a token.
- Touch targets at least 44px on mobile.
- Real `<button>`, `<a>`, `<nav>`, `<main>`, `<table>`, `<label>`. Form fields have associated labels, helper text, and error text linked with `aria-describedby`.
- Pop-ups trap focus, close on Escape, restore focus on close, and have `role="dialog"` with `aria-modal` and a labelled title.
- Status never relies on color alone: pair color with icon and text.

---

## 3. SHARED COMPONENTS (do these first, everything else depends on them)

Rebuild or upgrade these in `resources/views/components/` so every page inherits the improvement. Keep their current prop names working; add new props with sensible defaults.

| Component | Redesign goals |
|---|---|
| `account-card` | Use only for the single hero balance or the bank card visual. Elsewhere, accounts appear as flat list rows: name, masked IBAN with copy action, balance in tabular numerals, no box around them. Respect the hide-balance preference (blurred or dotted, never layout-shifting). |
| `txn-row` | Flat two-line row with a thin divider below, no card or background: merchant or description plus date on the left, signed amount on the right (credit in `--success`, debit in `--text`), small status text below the amount. Plain category icon, no tinted circle. |
| `status-pill` | Icon plus text plus tint, for pending review, approved, rejected, completed, frozen. Small, consistent height. |
| `data-table` | Sticky header, right-aligned numeric columns, row hover, empty state, loading skeleton, and a mobile fallback that collapses rows into stacked cards. |
| `bottom-sheet` | Smooth drag handle, backdrop blur, rounded top corners, max height with internal scroll, focus trap. On desktop it becomes a centered side panel or modal. |
| `confirm-dialog` | Clear title that names the action ("Approve this deposit?"), a summary of what will happen (amount, account, recipient), a destructive variant in `--danger`, and buttons labelled with the exact action ("Approve deposit", not "OK"). |
| `toast` | Non-blocking, stacked, auto-dismiss with pause on hover, icon plus message plus optional action, announced with `role="status"`. Message wording mirrors the action label ("Deposit submitted"). |
| `custom-select` | Keyboard-navigable listbox, search for long lists, matches input styling. |
| `proof-uploader` | Drag-and-drop zone, image preview thumbnail, file name and size, replace and remove actions, upload progress, and clear error text for wrong type or size. |
| `limit-meter` | Thin progress bar with used and remaining amounts in tabular numerals; shifts to `--warning` then `--danger` as it fills, with text as well as color. |
| `request-stepper` | Clean horizontal stepper on desktop, compact "Step 2 of 4" on mobile. Completed steps show a check. |
| `empty-state` | Small illustration or icon, one-line explanation, one clear action. Never mood copy. |
| `quick-action` | Large tap target (44px minimum), plain icon plus short label, no box or tinted background. Laid out as a simple inline row on the home page. |
| `avatar`, `bp-logo`, `icon` | Consistent sizes, correct dark mode treatment. |

Add these if they do not exist: `skeleton` (loading placeholders), `page-header`, `form-field` (label, input, hint, error), `money` (formatted amount with tabular nums and sign handling), `modal` (desktop dialog sharing logic with `bottom-sheet`), `copy-button`.

**Success and result pop-ups** (deposit submitted, payment approved, transfer sent): a centered or sheet-style result with the existing Lottie success check, the amount and recipient, a reference number with copy action, and two actions ("View activity", "Done"). Keep it short and calm; no confetti.

---

## 4. PAGE-BY-PAGE REQUIREMENTS

For every page: fix alignment and spacing, apply the shared components, add loading, empty, and error states, and verify both themes and all breakpoints.

### Layouts (`resources/views/layouts/`)
- `customer.blade.php`: sidebar nav with grouped sections and active state, mobile bottom nav (Home, Activity, Pay or Transfer, Cards, Profile), header with greeting, notification bell with unread dot, avatar menu.
- `admin.blade.php`: collapsible sidebar, breadcrumb, global search, pending approvals count badge in the nav.
- `public.blade.php`: modern marketing header and footer with consistent link structure and a clear sign-in call to action.
- `app.blade.php` and `app-dark.blade.php`: make them thin wrappers over the same token system. Dark mode must come from `[data-theme]` or `prefers-color-scheme` tokens, not a duplicated layout. If `app-dark` is redundant, tell me before removing it.

### Public pages (landing `/`, support, learn, policies, terms, privacy, recommendations)
- **Landing:** open with the most characteristic thing about the product, which is a clear view of money moving safely. A focused hero with one headline, one supporting line, one primary action, and a product visual (card plus balance UI built from real components, not stock gradients). Follow with a concise section on what customers can do (transfer, deposit, pay, cards) written as simple text blocks with whitespace, not a grid of cards, a security and trust section, and a closing call to action. Use the bank card Lottie as the single motion moment.
- **Support, learn, policies, terms, privacy:** readable long-form layout with a sticky table of contents on desktop, comfortable line length, strong heading hierarchy, and anchor links. Support gets a searchable FAQ with accordions and a clear contact path.
- **Recommendations:** clean list of recommendation items with an action each.

### Auth (`resources/views/auth/`)
- A split layout on desktop (brand panel plus form) and a single focused column on mobile.
- `signin`, `register`, `forgot-password`, `reset-password`, `change-password`, `first-signin`: clear labels, show-password toggle, inline validation messages that say what to fix, password strength hint on create and change.
- `verify-otp` (6 digits) and `verify-pin` / `verify-login-pin` (4 digits): segmented inputs with auto-advance, paste support, backspace navigation, `inputmode="numeric"`, `autocomplete="one-time-code"` on OTP, resend with a visible countdown, masked entry for PIN.
- Errors never blame the user and always say what to do next.

### Customer app (`resources/views/customer/`)
- **home:** greeting, total balance as the hero with the hide-balance toggle (the only raised surface on the page), a flat list of accounts, quick actions (Transfer, Deposit, Pay, Cards) as a simple icon row, recent transactions using `txn-row` as one continuous divided list, and a single line of text if anything awaits review.
- **account-detail:** account header (balance, IBAN with copy, limits via `limit-meter`), actions, and a filterable transaction list with date grouping ("Today", "Yesterday", dates).
- **transfer / pay:** a guided flow with `request-stepper`: choose source account, recipient or merchant, amount (large numeric input with currency), review, confirm, using a `confirm-dialog` summary. Show limit impact before confirming. Disable the submit button while processing and prevent double submission.
- **deposit / top-up / receive:** these three overlap. Make their roles clear and consistent (deposit methods list, funding the account, share-your-details). Deposit methods appear as selectable rows with icon and short description; selected method reveals instructions with copy buttons for account details; `proof-uploader` for proof; submit leads to the success pop-up with "pending review" status explained in one sentence. Tell me if you think two of these pages should merge, but do not merge routes without asking.
- **cards:** realistic card visuals (virtual and physical distinguished), freeze toggle with immediate feedback, spending limit controls with `limit-meter`, show or hide card number with confirmation via PIN, actions in a `bottom-sheet` (replace card, change PIN).
- **activity:** timeline of account actions grouped by day, filter chips, search.
- **messages:** clean conversation layout, message bubbles with timestamps, transaction reference chips when a message links to a transaction, sticky composer on mobile.
- **notifications:** grouped by day, unread state, mark-all-read, empty state.
- **statements:** period selector with presets (last month, last 3 months, custom), account selector, a generate button with progress state, and a list of recent downloads.
- **profile:** sectioned settings (personal info and picture upload with crop-friendly preview, security: PIN, password, TOTP; preferences: theme, language, hide balance). Each preference saves with immediate visual feedback and a toast.
- **financial-services:** a simple divided list of products, each with a name, one line of description, and a clear action. One featured product may be given more space, but not a box.

### Admin (`resources/views/admin/` and Filament)
- **overview:** KPI figures in one plain row (pending approvals, today's volume, anomalies, active users) with small trend indicators and no box per stat, then pending approvals preview and recent actions as flat lists. Keep Filament widgets (`StatsOverview`, `PendingApprovals`, `RecentActions`, `AnomalyWidget`) styled to match via Filament theme CSS hooks.
- **approvals (Blade and Filament):** the most important admin screen. Tabs for Deposits and Payments with counts; a table with requester, amount, account, method, age of request (with a warning style when old), and status. Row click opens a detail panel showing full transaction context, proof image preview (zoomable), limit check result, and balance impact. Approve and reject buttons are clearly separated, reject requires a reason, both confirm through `confirm-dialog`, and both show a toast on completion. Preserve the Livewire `wire:submit` behavior.
- **transactions, users, accounts:** use `data-table` with filters, search, saved filter chips, pagination, and column alignment.
- **transaction-detail:** a ledger-style layout showing header (status, amount, reference), a timeline of status changes, and a double-entry legs table (debit and credit columns, tabular numerals, totals row that visibly balances).
- **audit-log:** dense, filterable, monospace not needed; use tabular numerals and clear actor and action columns.
- **reports:** period controls, summary figures, and charts that use only the existing tokens (primary, accent, success, danger, warning) with accessible labels and a data table alternative.
- **settings:** grouped form sections with a sticky save bar that appears when there are unsaved changes.

---

## 5. COPY RULES

- Sentence case, plain verbs, active voice, no filler.
- A button names the exact action ("Send money", "Approve deposit", "Save changes"). The same action keeps the same name in the button, the dialog, and the toast.
- Errors say what happened and how to fix it. They do not apologize.
- Empty states invite an action in one line.
- Name things the way customers understand them, not how the system is built ("Money in" over "Deposit ledger entry").
- No emojis anywhere.

---

## 6. PROCESS

Work in phases. After each phase, summarize what changed and wait for my go-ahead only if you hit a question; otherwise continue.

1. **Audit:** list inconsistencies found (spacing, alignment, duplicate components, hardcoded colors, accessibility gaps, mobile problems). Keep it brief.
2. **Design plan:** tokens (type, spacing, radius, elevation, motion), layout sketches, plan review.
3. **Foundation:** update `tokens.css` and `resources/css/app.css`, then layouts.
4. **Components:** the shared component set above.
5. **Pages:** auth, customer app, public, then admin and Filament. Finish each page fully (all states, both themes, all breakpoints) before moving on.
6. **Verification:**
   - `npm run build` succeeds.
   - `php artisan acceptance:check` passes.
   - Existing tests pass (`php artisan test`).
   - Manually check the deposit flow, the transfer flow, and the Filament approve and reject flow still work end to end.
   - Check each page in light and dark mode at 360px and 1280px widths, keyboard-only navigation, and reduced-motion.
7. **Report:** list files changed, new components, any behavior you noticed that looks like a bug (do not silently fix backend issues), and any suggestions that need my decision (for example merging `deposit` and `top-up`, or the `routes/routes/admin.php` path, which looks unusual).

## 7. DEFINITION OF DONE

- Every page shares one visual system and feels like one product.
- Pages feel open and flat: no nested containers, no grids of identical cards, no boxes around things that do not need them.
- Nothing is misaligned, cramped, or overflowing at any breakpoint.
- Balances and figures are tabular, aligned, and respect the hide-balance preference.
- Every pop-up, sheet, dialog, and toast is consistent, accessible, and sleek.
- Colors come only from the existing tokens, in both themes.
- All checks pass and no existing behavior has changed.
