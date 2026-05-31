# TechBits — UI Specification (UI.md)

> **Purpose of this file.** This is the authoritative visual and screen specification for the TechBits eCommerce application. It is written to be consumed by an AI UI mock-up agent that will generate one mock-up set per screen. It is self-sufficient: UI-relevant business rules (validation limits, character counts, status colors, formats) are restated here so you do not need to read `SCOPE.md`.
>
> **Companion file.** `SCOPE.md` holds the full functional scope. Screen IDs (S1–S30, A1–A11) and terminology are shared between the two files.
>
> **What to produce.** A mock-up for every screen listed in Sections 4–9 below, at the breakpoints defined in the Responsive Matrix (Section 10). Reuse the shared components (Section 3) consistently across all screens.
>
> **Academic note.** TechBits is a coursework project; checkout payment is **simulated** (no real transactions). A small academic disclaimer appears in the footer of every page, and a prominent one on the payment screen.

---

## Table of Contents

1. Design Language
2. UI-Relevant Business Rules (restated)
3. Shared / Reusable Components
4. Storefront Screens (S1–S5)
5. Authentication Screens (S6–S12)
6. Customer Account Screens (S13–S19)
7. Checkout Screens (S20–S23)
8. Static & Utility Screens (S24–S30)
9. Admin Screens (A1–A11)
10. Responsive Mock-up Matrix
11. Mock-up Generation Guidance

---

## 1. Design Language

### 1.1 Philosophy
TechBits should feel **clean, trustworthy, and modern** — a competent contemporary electronics store. Clarity over decoration. A first-time visitor on a mid-range Android phone should instantly understand what the store sells and how to buy.

Principles: clarity first (one obvious primary action per screen) · familiar eCommerce patterns (cart top-right, product grid, breadcrumbs — don't innovate on navigation) · mobile-equal (storefront designed mobile-first, scales up) · trust signals (clear pricing, stock status, confirmation steps, disclaimer) · low cognitive load (short forms, clear labels, visible validation).

### 1.2 Brand
- **Name:** TechBits.
- **Tagline:** "Computer accessories, delivered across Sri Lanka."
- **Logo:** clean sans-serif wordmark "TechBits", optionally with a small icon (stylized chip / bracket / cursor). Must read clearly at small sizes.
- **Personality:** approachable, reliable, no-nonsense — "helpful local tech shop that happens to be online," not luxury, not bargain-bin.

### 1.3 Color Direction
Light theme only (no dark mode).
- **Primary:** a strong, trustworthy **tech-blue** — primary buttons, links, active states, header accent.
- **Accent:** a warm **amber/orange**, used sparingly for highlights (Featured badge, standout CTAs).
- **Neutrals:** white and light-grey surfaces, dark-grey/near-black text, generous whitespace.
- **Semantic:** success = green; warning = amber; error = red; info = blue.
- **Status badge colors:**
  - Order — Pending: grey · Processing: blue · Shipped: amber · Delivered: green · Cancelled: red.
  - Account — Unverified: amber · Active: green · Inactive: grey.
  - Product — Featured: accent/amber · Active: green · Inactive: grey · Out of stock: red.

The store should feel light, not dark or aggressive.

### 1.4 Typography
A single clean sans-serif family throughout (e.g. Inter / Roboto / Open Sans — UI agent's choice). Clear hierarchy: large page titles, medium section headings, comfortable body. Body text comfortable on mobile (~16px). Prices rendered with slight weight emphasis so they scan in grids.

### 1.5 Layout & Grid
**Storefront** — centered container (~1200px max) with comfortable margins. Product grid: 4 cols desktop, 2–3 tablet, 1–2 mobile. Persistent top header; footer on every page; breadcrumbs on category/product pages.
**Admin** — left side navigation (collapsible on small screens), main content right. Data-dense tables, clear headers, row actions on the right. Desktop-first.

### 1.6 Component Conventions
Buttons: primary (filled blue), secondary (outline), destructive (red); consistent sizing, rounded corners. Forms: labels above inputs, inline validation below the field, required indicators. Product card: square image, name (max 2 lines), price, stock indicator, whole card clickable. Cart icon: top-right with numeric badge. Toasts: top-right or bottom-center, auto-dismiss. Modals: confirmations only (not primary forms). Status badges: pill-shaped, color-coded. Pagination: numbered + prev/next at list bottom. Empty states: centered icon + message + CTA.

### 1.7 Interaction & Feedback
Every action gives feedback (loading on submit, success toast, inline errors). Destructive actions require a confirmation modal. Validation on submit minimum (inline-on-blur is a nice-to-have). Disabled states visually distinct (greyed Add-to-cart when out of stock). Cart badge updates immediately on add/remove.

### 1.8 Accessibility Baseline (reasonable, not certified)
Sufficient contrast · all inputs labelled · product images have alt text (product name) · keyboard-navigable primary flows · visible focus states · status not by color alone (badges carry text).

### 1.9 Imagery & Iconography
Product imagery: clean shots on white/neutral backgrounds, square aspect ratio. Icons: one consistent open-source set (cart, search, account, nav, status, actions). Hero: simple banner (product montage / flat background with tagline / light lifestyle image), uncluttered. No heavy illustration style.

### 1.10 Microcopy Tone
Friendly and clear ("Your cart is empty," not "Error: no items"). Action-oriented buttons (verbs: "Add to cart," "Place order," "Save address"). Customer errors helpful and non-technical; security-relevant errors generic and non-disclosing (Section 2).

---

## 2. UI-Relevant Business Rules (restated)

These affect what screens display and validate. Restated here so this file is self-sufficient.

### 2.1 Currency, Date, Locale
- Price display: **`LKR 12,500`** — "LKR" + space + comma thousand-separators, no decimals.
- Dates (customer-facing): **`27 May 2026`**. Date+time (order/admin): **`27 May 2026, 14:35`** (24-hour, Asia/Colombo).
- Language: English only.

### 2.2 Field Validation Limits (show as helper text / enforce in forms)
- **Full name:** 2–100 chars.
- **Email:** standard format, ≤254 chars; read-only after registration (on profile).
- **Contact number:** Sri Lankan; accepts `0771234567`, `+94771234567`, `077 123 4567`.
- **Password:** 8–64 chars, at least one letter and one number; confirm must match.
- **OTP:** 6-digit numeric; 10-min expiry; resend after 60s cooldown; invalidated after 5 wrong attempts.
- **Product name:** 3–200 · **SKU:** 3–50 (alphanumeric + dash/underscore) · **short desc:** 10–200 · **full desc:** 10–5000 · **specs:** up to 30 key-value pairs (key 1–50, value 1–200) · **price:** LKR 1–9,999,999 · **stock:** 0–99,999 · **images:** 1–5 (JPG/PNG/WebP, ≤2 MB, ~800×800 square).
- **Category name:** 2–50, unique.
- **Address:** label 1–30 (optional), recipient 2–100, line 1 3–200, line 2 0–200 (optional), city 2–50, district (dropdown of 25 SL districts), postal code 5 digits, contact number as above. Max 10 addresses/customer.
- **Payment (simulated):** cardholder 2–100, card number 13–19 digits, expiry MM/YY (not past), CVV 3–4 digits.
- **Contact form:** name 2–100, email, subject 3–150, message 10–2000.
- **Search:** min 2 chars, max 100.

### 2.3 Stock Display
`> 5` → "In stock" (green) · `1–5` → "Only X left" (amber) · `0` → "Out of stock" (red, Add-to-cart disabled).

### 2.4 Cart / Checkout
Flat shipping **LKR 500** shown at cart and review. Max qty per line = `min(10, stock)`. Cart totals = subtotal + shipping = grand total. Empty cart → friendly empty state. Checkout is **3 separate screens** (address → payment → review). Order number format **`TB-NNNNNN`** (e.g. `TB-000042`). Payment screen shows a prominent "simulated payment — no real card data" notice; review/confirmation/order-detail show only the **last 4 digits**.

### 2.5 Status Badges
Order: Pending (grey), Processing (blue), Shipped (amber), Delivered (green), Cancelled (red). Account: Unverified (amber), Active (green), Inactive (grey). Product: Featured (amber), Active (green), Inactive (grey), Out of stock (red).

### 2.6 Security-Relevant Microcopy (non-disclosing)
Login failure: "Invalid email or password." OTP failure: "Invalid or expired code." Reset link failure: "Invalid or expired reset link." Forgot-password confirmation is generic regardless of whether the email exists: "If this email exists, a reset link has been sent."

### 2.7 Pagination Sizes
Storefront grid 12/page · admin products 20 · admin orders 20 · customer orders 10 · admin customers 20 · admin messages 20.

### 2.8 Academic Disclaimer
Footer on every page: "TechBits is an academic project. No real transactions are processed." Prominent variant on S21 (payment).

---

## 3. Shared / Reusable Components

Design these once; reuse across all screens.

- **Storefront header** — logo (→ S1), search bar (search by product name), categories nav, cart icon with item-count badge, account area. Logged-out: "Login / Register". Logged-in: account name → menu (Account, My Orders, Logout).
- **Storefront footer** — store blurb, links to S24–S29, copyright, academic disclaimer line.
- **Admin layout** — left side nav (Dashboard, Products, Categories, Orders, Customers, Messages, Logout) + top bar ("Logged in as: [admin name]" + Logout). No public footer.
- **Product card** (S1/S2/S3) — square primary image, name (2-line truncate), `LKR` price, stock indicator, Featured badge when applicable; entire card clickable → S4.
- **Status badge** — pill, color-coded (Section 2.5), always includes text.
- **Quantity selector** — − / value / +, capped at `min(10, stock)`.
- **Toast** — transient confirmation/error, auto-dismiss.
- **Confirmation modal** — title, message, Cancel + destructive-action buttons.
- **Empty state** — centered icon + message + CTA button.
- **Pagination** — numbered + prev/next.
- **Breadcrumb** — Home › Category › Product (storefront).
- **Form field** — label above, input, helper/limit text, inline error below.

---

## 4. Storefront Screens (S1–S5)

### S1 — Home page
**Access:** Guest, Customer. **Breakpoints:** mobile + desktop.
**Layout:** header → hero banner (tagline + CTA into catalog) → "Featured Products" grid (up to 8 cards; falls back to most-recent active products) → category quick links (10 categories) → footer.
**States:** standard. Cards show Featured/stock badges where relevant.

### S2 — Category listing
**Access:** Guest, Customer. **Breakpoints:** mobile + desktop.
**Layout:** header → breadcrumb + category name → filter/sort bar (in-stock-only toggle; sort: Newest / Price ↑ / Price ↓ / Name A–Z) → product grid (12/page) → pagination → footer.
**States:** populated; empty ("No products in this category yet.").

### S3 — Search results
**Access:** Guest, Customer. **Breakpoints:** mobile + desktop.
**Layout:** header (search pre-filled) → "Showing results for: [query]" + result count → filter bar (category dropdown + in-stock toggle) + sort → grid (12/page) → pagination → footer.
**States:** results; empty ("No products match your search."); short-query hint (<2 chars).

### S4 — Product detail
**Access:** Guest, Customer. **Breakpoints:** mobile + desktop.
**Layout:** header → breadcrumb → two-column (desktop): left = large primary image + thumbnail strip; right = name, `LKR` price, stock indicator, short description, quantity selector, **Add to cart** (disabled + "Out of stock" label when stock 0). Below: sections/tabs for Full Description and Specifications (two-column table). Footer.
**States:** in stock; low stock ("Only X left"); out of stock (disabled CTA); multiple vs single image.

### S5 — Shopping cart
**Access:** Guest, Customer. **Breakpoints:** mobile + desktop.
**Layout:** header → title → line-item list (image, name, unit price, quantity selector, line total, remove) → summary (subtotal, shipping LKR 500, grand total) → "Continue shopping" + **Proceed to checkout**. Footer.
**States:** populated; empty ("Your cart is empty. Browse products to get started." + CTA); warning state for inactive/out-of-stock lines ("Some items in your cart are unavailable. Please review.").

---

## 5. Authentication Screens (S6–S12)

### S6 — Register
**Access:** Guest. **Breakpoints:** mobile + desktop.
**Layout:** header → centered card → fields: full name, email, contact number, password, confirm password (all with limit helper text) → **Create account** → "Already have an account? Log in." Footer.
**States:** default; per-field validation errors; "email already in use".

### S7 — Email OTP (registration)
**Access:** Unverified user (post-registration). **Breakpoints:** mobile + desktop.
**Layout:** header → centered card → "We sent a code to j•••@example.com" (masked) → 6-digit OTP input → **Verify** → "Resend code" (disabled during 60s countdown) → 10-min expiry note. Footer.
**States:** default; wrong code; expired code; too many attempts (must resend); resend cooldown countdown.

### S8 — Login
**Access:** Guest. **Breakpoints:** mobile + desktop.
**Layout:** header → centered card → email, password, "Remember me" checkbox → **Log in** → "Forgot password?" + "Don't have an account? Register." Footer.
**States:** default; generic failure ("Invalid email or password"); lockout shows the same generic message (no disclosure).

### S9 — Email OTP (login)
**Access:** user who passed credentials at S8. **Breakpoints:** mobile + desktop.
**Layout:** as S7 but framed "Confirm it's you" → masked email → OTP input → **Verify** → resend (60s cooldown) → expiry note. Footer.
**States:** default; wrong/expired code; too many attempts. Note for the agent: closing this screen invalidates the in-progress login (no special UI, just don't imply persistence).

### S10 — Forgot password
**Access:** Guest. **Breakpoints:** mobile + desktop.
**Layout:** header → centered card → email input → **Send reset link** → "Back to login." Footer.

### S11 — Forgot-password confirmation
**Access:** Guest. **Breakpoints:** mobile + desktop.
**Layout:** header → centered card → generic success message ("If this email exists, a reset link has been sent.") → "Back to login." Footer.

### S12 — Set new password
**Access:** anyone with a valid reset link. **Breakpoints:** mobile + desktop.
**Layout:** header → centered card → new password + confirm new password (limit helper) → **Set password** → success → link to login. Footer.
**States:** default; mismatch error; invalid/expired token ("Invalid or expired reset link." + request-new option).

---

## 6. Customer Account Screens (S13–S19)

### S13 — Account dashboard
**Access:** Customer. **Breakpoints:** mobile + desktop.
**Layout:** header (account menu) → "Welcome, [name]" → cards/links: My Profile, My Addresses, My Orders, Change Password, Logout. Footer.

### S14 — My Profile
**Access:** Customer. **Breakpoints:** mobile + desktop.
**Layout:** header → form: full name (editable), email (read-only, with note "Email cannot be changed"), contact number (editable) → **Save changes** → success/error banner. Footer.

### S15 — Change password
**Access:** Customer. **Breakpoints:** mobile + desktop.
**Layout:** header → form: current password, new password, confirm new password (limit helper) → **Update password** → success/error. Footer.

### S16 — My Addresses
**Access:** Customer. **Breakpoints:** mobile + desktop.
**Layout:** header → "Add new address" button → list of address cards (label, recipient, full address, contact, "Default" badge on default) with per-card actions: Edit · Delete · Set as Default. Footer.
**States:** populated; empty ("You don't have any saved addresses yet." + CTA). Delete uses a confirmation modal.

### S17 — Add / Edit Address (full page)
**Access:** Customer. **Breakpoints:** mobile + desktop.
**Layout:** header → form: label (optional), recipient name, street line 1, street line 2 (optional), city, district (dropdown — 25 SL districts), postal code (5 digits), contact number, "Set as default" checkbox → **Save address** + Cancel. Footer.
**States:** add vs edit (pre-filled); validation errors.

### S18 — My Orders
**Access:** Customer. **Breakpoints:** mobile + desktop.
**Layout:** header → list/table: order number, date, total (LKR), status badge — newest first, row click → S19; pagination (10/page). Footer.
**States:** populated; empty ("You haven't placed any orders yet." + CTA).

### S19 — Order detail (customer view)
**Access:** Customer (own orders). **Breakpoints:** mobile + desktop.
**Layout:** header → order number + placed date + status badge → line items (snapshot: image, name, SKU, unit price, qty, line total) → shipping address used → payment (card ending in last 4) → totals (subtotal, shipping, grand total). Read-only — no edit/cancel. Footer.

---

## 7. Checkout Screens (S20–S23)

All checkout screens show a step indicator (1/2/3) and a mini order summary (item count, subtotal; full totals on review). Customer access only, non-empty cart; reaching with an empty cart redirects to S5.

### S20 — Checkout · Shipping address (step 1 of 3)
**Breakpoints:** mobile + desktop.
**Layout:** header → step indicator → selectable saved-address cards (default pre-selected) + inline "Add new address" → mini summary → **Continue to payment** + "Back to cart". Footer.

### S21 — Checkout · Payment (step 2 of 3)
**Breakpoints:** mobile + desktop.
**Layout:** header → step indicator → **prominent academic notice** ("Simulated payment — do not enter real card details") → form: cardholder name, card number, expiry MM/YY, CVV → mini summary → **Continue to review** + Back. Footer.
**States:** default; format-validation errors (Luhn, expiry not past, CVV length).

### S22 — Checkout · Review & place order (step 3 of 3)
**Breakpoints:** mobile + desktop.
**Layout:** header → step indicator → summary panels: shipping address, payment (masked, last 4), line items, totals (subtotal, shipping LKR 500, grand total) → **Place order** + Back. Footer.

### S23 — Order confirmation
**Breakpoints:** mobile + desktop.
**Layout:** header → large success indicator → order number → ordered-items summary → total paid → shipping address → "A confirmation email has been sent." → **View order details** (→ S19) + **Continue shopping** (→ S1). Footer.

---

## 8. Static & Utility Screens (S24–S30)

### S24–S28 — About Us / Terms & Conditions / Privacy Policy / Shipping & Delivery / FAQ
**Access:** all. **Breakpoints:** mobile + desktop.
**Layout:** header → page title → body content (placeholder text; FAQ uses an expandable Q&A list) → academic-content disclaimer line → footer.

### S29 — Contact Us
**Access:** all (logged-in users get name/email pre-filled, read-only). **Breakpoints:** mobile + desktop.
**Layout:** header → form: name, email, subject, message (limit helpers) → **Send message** → on-screen success ("Message sent. We'll get back to you soon.") + cleared form. Footer.

### S30 — Error / Not found
**Access:** any. **Breakpoints:** mobile + desktop.
**Layout:** header → friendly message (used for unknown URLs and removed/inactive products: "Product not available") → **Back to home**. Footer.

---

## 9. Admin Screens (A1–A11)

Admin screens use the admin layout (left side nav + top bar), **desktop only**, no public footer.

### A1 — Admin login
**Layout:** minimal centered card → email, password → **Log in**. No forgot-password, no register links. (Reached by direct URL only.)

### A2 — Admin dashboard
**Layout:** side nav → counter cards (total products, total orders, orders-by-status breakdown, total registered customers, unread contact messages) → recent orders mini-list + recent messages mini-list.

### A3 — Product list
**Layout:** side nav → top bar with **Add product** → filter bar (category dropdown, search by name) → table (image thumb, name, SKU, category, `LKR` price, stock, Featured badge, Active toggle, actions: Edit · Delete) → pagination (20/page).
**States:** populated; empty; delete confirmation modal.

### A4 — Add product
**Layout:** side nav → form: name, SKU, category dropdown, short description, full description, **specifications (repeating key-value rows with add/remove)**, price (LKR), stock, Featured toggle, Active toggle, **multi-image upload** area (reorder + "set as primary") → **Save** + Cancel.
**States:** default; validation errors; ≥1 image required to set Active.

### A5 — Edit product
**Layout:** identical to A4, pre-populated, plus a **Delete product** action (confirmation modal).

### A6 — Category management
**Layout:** side nav → "Add category" input → list of categories with product counts, inline rename, Delete (disabled/blocked with message when the category contains products).

### A7 — Order list
**Layout:** side nav → filter bar (status dropdown — default Pending; search by order number or customer name; optional date range) → table (order number, customer name, date, total LKR, status badge, action: View) → pagination (20/page).

### A8 — Order detail (admin view)
**Layout:** side nav → order header (number, date, customer name + email + contact, current status badge) → line items (snapshot) → shipping address → payment (masked last 4) → totals → **status update control** (next-status action; one step forward) → **Cancel order** (confirmation modal; "Stock will be restored") → **status-change timeline** with timestamps.
**States:** each status; Delivered/Cancelled disable further forward transitions.

### A9 — Customer list
**Layout:** side nav → search (name/email) + status filter (Unverified / Active / Inactive) → table (name, email, contact number, registration date, status badge, action: Activate/Deactivate toggle — the **only** edit available) → pagination (20/page).
**States:** confirmation modal on deactivate.

### A10 — Contact messages list
**Layout:** side nav → table (sender name, email, subject, date, read/unread indicator, action: View) with unread rows highlighted → pagination (20/page).

### A11 — Contact message detail
**Layout:** side nav → sender info (name, email) → subject → date → message body → "Mark as unread" toggle → back to list. No in-app reply (admin replies via external email).

---

## 10. Responsive Mock-up Matrix

| Screen group | IDs | Mobile | Desktop |
|---|---|---|---|
| Storefront | S1–S5 | ✅ | ✅ |
| Authentication | S6–S12 | ✅ | ✅ |
| Customer account | S13–S19 | ✅ | ✅ |
| Checkout | S20–S23 | ✅ | ✅ |
| Static & utility | S24–S30 | ✅ | ✅ |
| Admin | A1–A11 | ❌ | ✅ |

Storefront, auth, account, and checkout screens (S1–S30) each get **both mobile and desktop** mock-ups. Admin screens (A1–A11) get **desktop only** (usable on mobile but not a design priority).

---

## 11. Mock-up Generation Guidance

- **Consistency over novelty.** Use the shared components (Section 3) identically across screens. The header, footer, product card, badges, and form fields should look the same everywhere they appear.
- **One primary action per screen,** visually dominant (filled blue). Secondary actions are outlined; destructive actions are red.
- **Show realistic content.** Populate grids with believable computer-accessory products, `LKR` prices with thousand-separators, and Sri Lankan addresses/districts. Avoid lorem ipsum in product names and prices.
- **Show key states,** not just the happy path. For relevant screens include: empty states, validation errors, low/out-of-stock, OTP error/cooldown, lockout (generic message), and order status badges across the lifecycle.
- **Light theme only.** No dark mode.
- **Mask sensitive data** in payment-related screens (card shown as last 4 only on review/confirmation/order detail); the payment entry screen (S21) carries the prominent simulated-payment notice.
- **Academic disclaimer** appears in the footer of every storefront/account/checkout/static screen.
- **Mobile-first for S1–S30;** ensure the product grid, header (collapsing nav / search), and forms reflow cleanly. **Desktop-first for A1–A11** with data-dense tables and a persistent left nav.
- **Accessibility cues** should be visible in the mock-ups: labelled fields, visible focus styling on at least one example per screen, and text inside every status badge.
