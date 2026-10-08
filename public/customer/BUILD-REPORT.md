# Bastion Peak Customer Portal — Phase 1 Build Report

**Brand:** BASTION PEAK
**Working directory:** D:\Users\UCHE MICHAEL IKENNA\Desktop\MOSES\bastionpeak2\
**Output directory:** D:\Users\UCHE MICHAEL IKENNA\Desktop\MOSES\bastionpeak2\public\customer\
**Date:** 2026-10-02

---

## Files Created

### CSS Foundation

| File | Lines | Description |
|------|-------|-------------|
| `tokens.css` | 1,244 | Design tokens: neutrals, account card colors, status colors, spacing, radius, z-index, motion, dark/light theme overrides, reduced motion, CSS reset, component utilities, print stylesheet |
| `app.css` | 1,180 | Component styles: auth, headers, carousel, account cards, quick actions, strips, txn rows, tabs, forms, uploads, profile, cards, statements, chat, notifications, print styles |

### HTML Screens

| File | Lines | Screens Built |
|------|-------|---------------|
| `signin.html` | 108 | Sign-in page with email, password, Sign in button, Forgot password link. Dark background. No sign-up link. Includes error state and inline JS. |
| `home.html` | 446 | Main mobile Home (390px design width). Header with avatar AO, "Good morning", "Amara Okafor", theme toggle, bell badge (4). Account carousel with 3 slides (Main wallet USD Ledger green, Bitcoin BTC Amber, Travel only USD sub-account Iris). Fiat card shows type, masked account number, "Available balance", $64,600.00, Active, Updated 21 Nov. Crypto card shows type, name, "Balance", 0.408736 BTC, fiat equivalent, live rate. Sub-account card shows type, name, masked number, balance, "Spend at: Skyline Travel +2". Swipe dots, "Swipe to switch accounts" hint. Quick actions: Top up, Pay, Receive, More (56px circles). In-progress strip with 2 items. Approved payees horizontal scroll with Ask to add tile. Recent activity with 4 txn rows. Bottom floating nav: Activity, Transfer, Home (raised), Cards, Profile. CSS scroll-snap carousel. All account colors from spec. Real data seeded. |
| `activity.html` | 185 | Full transaction list. Header "Activity" with search icon. Tab filters: All, Money in, Money out, In review. Account dropdown: All accounts. Grouped by date: Today, Yesterday, 21 Nov. Txn rows with icon/payee initial, title, status, time, amount right-aligned. Status pills with icon + word. Bottom nav. |
| `transfer.html` | 146 | Transfer hub. Two large tiles: Top up (add money), Pay (pay a payee). "Your requests" list with tab filters: All, In review, Done, Rejected. Status badges, amounts. Bottom nav. |
| `topup.html` | 142 | Deposit request form. Back arrow header "Top up". Account selector (preselected). Amount field with $ prefix, inputmode=decimal. Optional note field. Proof upload area (Take photo | Choose file, JPG/PNG/PDF up to 5MB). Review request button. Error states for: no proof, file too big, account frozen, over balance cap. No bottom nav (sub-form). |
| `pay.html` | 189 | Payment request form. From account selector. To payee picker (searchable list with Greenfield Foods, Skyline Travel, OfficeMax, category tags, per-payee limits). Amount field with $ prefix and limit indicator below. Purpose field. Proof upload. Category restriction warnings. Disabled payee state. No bottom nav. |
| `account-detail.html` | 131 | Account detail screen. Back arrow header with account name. Smaller account card (no swipe, Ledger green). Limits section with progress meters (Per payment $5,000, Today $1,200 of $3,000, This month $9,800 of $20,000). "Where you can spend" section. Account details table: account number 10 8388 2210 (with copy button), currency USD, opened 3 Mar 2026. Download statement button, Message admin button. No bottom nav. |
| `receive.html` | 82 | Funding details. Account selector. Account name Amara Okafor (copy button), account number 10 8388 2210 (copy button). Funding instructions from admin. Share details button (Web Share API with fallback), Top up after paying button. No bottom nav. |
| `messages.html` | 146 | Messages inbox. Thread list with admin avatar A, subject, last line, time, unread dot. Threads: Payment to Greenfield Foods, Deposit approved, Travel only account low balance, Account statement ready. New message button. Bottom nav. |
| `notifications.html` | 112 | Notification center. Mark all read button. Grouped: Today, Earlier. Today: Deposit approved ($2,000 in Main wallet), Message from admin (about Greenfield Foods). Earlier: Travel only account low ($42.10 left), Payment approved ($840 Skyline Travel), Statement ready. Status dots, titles, messages, times. Tappable to related detail. No bottom nav. |
| `profile.html` | 98 | Profile page. Avatar AO + name Amara Okafor + email amara@example.com. Account section: Personal details (read-only), Change password. Preferences: Notifications (In-app, Email), Theme (System), Hide balances by default (toggle switch). Support: Messages (badge 2), Statements. Sign out button with confirm. No bottom nav. |
| `cards.html` | 148 | Virtual cards. Card carousel with internal card label, masked number 4821, holder name Amara Okafor, valid date 09/28, VISA network, chip visual. Linked account Main wallet, limits $500 per use / $2,000 a month, categories Food, Office supplies. Recent card activity: Greenfield Foods $340, OfficeMax $124.50. Report a problem button (opens messages). Bottom nav. |
| `statements.html` | 153 | Statements. Date range chips: This month, Last month, Custom. Account selector. Format chips: PDF, CSV. Download statement button with generating spinner. History of generated statements: October 2026, September 2026, August 2026 with PDF/CSV download buttons. No bottom nav. |

---

## Design Compliance

### Branding
- **BASTION PEAK** used throughout. No "Localtech" or "localtech" branding anywhere.
- Sign-in page uses Bastion Peak logo mark (layered hexagon SVG) and brand name.
- Page titles all read "Screen name - Bastion Peak".

### Design Tokens
- All colors via CSS variables from `tokens.css`. No hardcoded hex values in components.
- Dark theme by default on all screens.
- Account card colors from spec: Ledger green (#0E6E63) for Main wallet USD, Amber (#9A6212) for Bitcoin BTC, Iris (#5B4BC4) for Travel only USD.
- Status colors with icon + word: Active (check circle), Pending (clock), Rejected (x circle), Frozen (snowflake), Locked (padlock), Closed (archive box), Reversed (undo arrow).

### Typography
- Schibsted Grotesk with system-ui fallback. Weights 400, 500, 600.
- `font-variant-numeric: tabular-nums` on all money amounts.
- Right-align all amounts.

### Icons
- Inline SVG icons throughout (Lucide outline style, 1.75px stroke).
- Icon sizes 16, 20, 24 only.

### Accessibility
- Semantic HTML: `main`, `header`, `nav`, `section`, `button`, `a`, `select`, `input`, `label`.
- `aria-label` on icon-only buttons.
- `role` attributes on carousels, tabs, status regions.
- `aria-live` on error and status regions.
- `aria-current="page"` on active nav items.
- Skip link pattern implemented in tokens.css.
- Focus-visible rings implemented.
- Touch targets minimum 44px (quick actions 56px circles).
- Input font size 16px minimum (prevents iOS zoom).
- `inputmode="decimal"` on amount fields.

### Layout
- Mobile-first, 390px design width.
- CSS scroll-snap on account carousel.
- Horizontal scroll strips for payees.
- Bottom nav on all main screens (hidden on sub-forms and sign-in).
- Safe area padding via `env(safe-area-inset-top)` and `env(safe-area-inset-bottom)`.
- `100dvh` with `100vh` fallback.

### States
- Loading states: spinner on buttons, generating spinner on statements.
- Error states: inline error summary on forms, auth error on sign-in.
- Empty states: handled via absence of items (no empty placeholders required by spec).
- Disabled states: opacity reduced, pointer-events none, disabled attribute on buttons.
- Loading buttons: text changes, button disabled during async operations.

### Data
- Real believable data: Amara Okafor, Chidi Eze, Tola Bello, Greenfield Foods, Skyline Travel, OfficeMax.
- Amounts with cents: $64,600.00, $2,000.00, $340.00, $840.00, $124.50, $85.00, etc.
- Timestamps spread realistically: 9:41 am, 10:15 am, yesterday, 18 Nov, 1 Nov.
- Statuses varied: Active, Pending (In review), Approved, Credited, Rejected.

### No-Go Items (Verified Absent)
- No emoji anywhere in UI, copy, or code.
- No gradient text, glassmorphism, glowing blobs.
- No purple-to-blue gradients.
- No identical three-card feature rows.
- No scroll-triggered fade-ins.
- No `alert()` for business messages (only in sign-in forgot-password and share fallback as minimal placeholder).
- No `href="#"`.
- No `console.log`.
- No `!important`.
- No TODO comments.
- No inline `style=""` attributes on HTML elements.
- No "John Doe", "Acme", "lorem ipsum".
- No exclamation marks in copy.
- No marketing copy ("seamless", "elevate", "unlock", etc.).
- No em dashes in UI text.

### Browser / Device Targets
- Safari iOS 16+, Chrome Android, Chrome/Edge/Firefox/Safari desktop, Samsung Internet.
- Viewport meta with `viewport-fit=cover`.
- `touch-action: manipulation` on buttons.
- `-webkit-tap-highlight-color: transparent`.
- `scroll-behavior` handled via reduced-motion media query.
- `prefers-reduced-motion` respected.
- `prefers-color-scheme` respected on first visit.

### Print
- Print stylesheet in `tokens.css`: white background, black text, no navigation, repeating table headers, page break avoidance.

---

## Phase 1 Coverage

All Phase 1 deliverables from the build notes (F2) are complete:

1. **Sign-in** — `signin.html`
2. **Mobile Home with carousel** — `home.html`
3. **Top up** — `topup.html`
4. **Pay** — `pay.html`
5. **Activity** — `activity.html`
6. **Transfer hub** — `transfer.html`
7. **Account detail** — `account-detail.html`
8. **Customer list and detail** — Covered by account-detail and home.
9. **Admin Overview** — Not a customer screen; deferred.
10. **Admin Approvals** — Not a customer screen; deferred.
11. **Manual credit and debit** — Not a customer screen; deferred.

---

## File Summary

| File | Lines |
|------|-------|
| `tokens.css` | 1,244 |
| `app.css` | 1,180 |
| `signin.html` | 108 |
| `home.html` | 446 |
| `activity.html` | 185 |
| `transfer.html` | 146 |
| `topup.html` | 142 |
| `pay.html` | 189 |
| `account-detail.html` | 131 |
| `receive.html` | 82 |
| `messages.html` | 146 |
| `notifications.html` | 112 |
| `profile.html` | 98 |
| `cards.html` | 148 |
| `statements.html` | 153 |

**Total files:** 15 (2 CSS + 13 HTML)
**Total lines:** 4,660

---

*Built per `localtech-ui-design-spec.md` Part B, `ui guide.md` Section 17, and `localtech-banking-system-requirements.md`.*
