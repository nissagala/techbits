# TechBits — Project Scope Document (SCOPE.md)

> **Purpose of this file.** This is the authoritative functional scope for the TechBits eCommerce application. It is written to be consumed by an AI coding agent. Anything not described here is out of scope. Where a behaviour or value is specified (a limit, a status flow, an edge case), treat it as a requirement, not a suggestion.
>
> **Companion file.** `UI.md` contains the visual/design specification and the detailed screen-by-screen layout for the UI mock-up agent. The two files share screen IDs (S1–S30, A1–A11) and terminology (see Glossary) so they can be used independently.
>
> **Academic context.** TechBits is a coursework project for a Master of Information Security. After development, the application will be scanned for vulnerabilities using security tools. The application is built using conventional, standard implementation — it is **not** deliberately sabotaged with planted vulnerabilities, and it is **not** hardened with extra security layers beyond what a normal build would include. Some intentional gaps are noted in the rules (e.g. no rate limiting on the contact form) to give scanners meaningful findings.

---

## Table of Contents

1. Project Overview & Objectives
2. Target Audience & User Personas
3. Project Scope (In / Out / Deferred)
4. Assumptions, Constraints, Dependencies & Risks
5. User Roles & Permissions
6. Core Feature Breakdown
7. User Journeys / Flows
8. Product Catalog Definition
9. Screen Inventory (Reference)
10. Business Rules
11. Content Requirements
13. Accessibility & Usability
14. Notifications & Communications
15. Deliverables
16. Glossary

---

## 1. Project Overview & Objectives

### 1.1 Project Name
**TechBits** — an online store selling computer accessories (keyboards, mice, RAM, storage, monitors, audio, webcams, cables, laptop accessories, power & charging).

### 1.2 Background
TechBits is a web-based eCommerce platform built as coursework for a Master of Information Security program. It serves customers in Sri Lanka. While functionally a real store, its primary purpose is to be a realistic target for security vulnerability assessment in a later phase of the course.

### 1.3 Purpose
- **Functional purpose** — deliver a working store where customers browse, register, search, manage a cart, check out, and track orders, and where an administrator manages catalog and orders.
- **Academic purpose** — provide sufficient attack surface (authentication, sessions, OTP, forms, file upload, simulated payment, admin functions, database queries) for meaningful security scanning.

### 1.4 Business Objectives
- Give Sri Lankan customers a convenient online channel to buy computer accessories.
- Let guests browse freely; require an account only at checkout.
- Give an administrator a simple back-office for catalog and orders.
- Build trust through clear product info, transparent LKR pricing, and order confirmations.

### 1.5 Success Criteria
- All epic acceptance criteria are demonstrably met for the customer role.
- An administrator can manage products (create, edit, delete) and view/update orders.
- A customer can complete an end-to-end purchase from landing page to confirmation.
- The application is stable under basic interaction so security scans can run.
- `SCOPE.md` and `UI.md` are detailed enough for independent AI coding and UI agents.

### 1.6 Conceptual Boundaries (high-level — see Section 3 for detail)
- No real payment processing — checkout is **simulated**.
- No real shipping integration — shipping is represented by status fields only.
- Web only (responsive); no native mobile apps.
- English only; LKR only; Sri Lanka delivery only.
- No marketing tooling, SEO, or analytics integrations.

---

## 2. Target Audience & User Personas

**Primary audience.** Sri Lankan consumers buying computer accessories online — home users, students, gamers, IT professionals, small office buyers. English-medium, basic computer literacy. Both desktop and mobile, with mobile equally important.

### Persona 1 — "Nimal", The Everyday Buyer (25–40)
Office worker whose keyboard broke. Browses on a mid-range Android phone. Doesn't want an account just to look. Needs easy guest browsing, clear photos and specs, fast checkout, an order confirmation.

### Persona 2 — "Tharushi", The Enthusiast (19–28)
University student and PC gamer. Knows hardware. Browses on desktop, uses search and filters heavily, reads specs carefully. Needs detailed specifications, working search/filters, an account for order history and reordering.

### Persona 3 — "Mr. Perera", The Small Office Buyer (40–55)
Buys mice/keyboards in small quantities for staff. Less tech-savvy, wants a simple uncluttered interface, clear records. Needs a no-frills flow, visible order history, clear confirmations.

### Persona 4 — "Store Admin", The Administrator
TechBits staff member. Adds products, updates prices/stock, processes orders, removes discontinued items. Works from desktop. Needs a functional back-office, not a beautiful one.

### Audience assumptions that shape the build
- Responsive UI for desktop and mobile.
- LKR pricing and Sri Lankan address formats.
- English-only, familiar eCommerce patterns.
- No expectation of reviews, wishlists, live chat, or loyalty points (all out of scope).

---

## 3. Project Scope

### 3.1 In-Scope

**Guest (unauthenticated)**
- Browse home page with featured products.
- Browse products by category.
- Search products by name; filter by category and stock; sort.
- View product detail pages (specs, price, stock, images).
- Add/update/remove cart items without registering.
- Register a new account; log in; (logout applies once logged in).
- Submit the Contact Us form.
- View static pages.
- Request a password reset.

**Customer (registered, logged in)**
- Everything a guest can do.
- View/edit profile (name, contact number; email is read-only).
- Change own password.
- Manage multiple shipping addresses (add/edit/delete/set default).
- Persistent cart tied to the account; cart merge on login.
- Checkout (3 steps): shipping address → simulated payment → review & place order.
- On-screen order confirmation + confirmation email.
- View order history and individual order details (read-only).

**Administrator**
- Separate admin login at a non-public URL.
- Dashboard with counters and recent activity.
- Product management: create, edit, delete; multiple images; price/stock; featured & active toggles.
- Category management: create, rename, delete (empty only).
- Order management: list, view, update status, cancel (auto-restores stock).
- Customer management: list; toggle Active/Inactive only.
- Contact message management: list, view, mark read/unread.

**Cross-cutting**
- Responsive layout (desktop + mobile).
- LKR currency formatting throughout (`LKR 12,500`).
- English-only content.
- Simulated payment (collects card details, no real gateway).
- Sri Lankan shipping address format (district + 5-digit postal code).
- Email notifications (registration OTP, login OTP, password reset, order confirmation, order status updates, contact submission to admin).
- Email OTP at registration verification and at **every** login.
- Account lockout after repeated failed logins.

### 3.2 Out-of-Scope

**Features not built**
- Product reviews, ratings, Q&A.
- Wishlist / save for later.
- Product comparison.
- Algorithmic recommendations / "you may also like".
- Discount codes, coupons, promotions, sales, campaigns.
- Loyalty points, referrals, gift cards.
- Multiple payment methods (only one simulated card flow).
- Real payment gateway (PayHere, Stripe, PayPal, etc.).
- Real shipping/courier integration or live tracking.
- Returns, refunds, exchanges.
- Live chat, chatbots, help desk/ticketing.
- Multi-vendor / marketplace (single seller only).
- Inventory reservation, back-orders.
- Tax calculation (prices assumed tax-inclusive).
- Invoice PDF generation.
- Advanced reporting/analytics for admin.
- Bulk product import/export (CSV).
- Product variants (each variant is a separate product).
- Stock reservation while items sit in a cart.
- Customer self-service account deletion.
- SMS notifications.
- Dark mode.

**Audiences / contexts not supported**
- Languages other than English.
- Currencies other than LKR.
- Delivery outside Sri Lanka.
- Native mobile apps.
- Browsers older than current major Chrome/Firefox/Edge/Safari.

**Non-functional areas not addressed**
- SEO, sitemaps, structured data, marketing pixels.
- Third-party analytics.
- Newsletter / email marketing.
- Social login / social media integration.
- GDPR/data-privacy workflows (cookie banners, data export, right-to-be-forgotten).
- Formal WCAG conformance audit.
- Security hardening/pen-testing as part of the build (that is the next course phase).

### 3.3 Deferred ("Could Be Added Later")
Product reviews & ratings; wishlist; discount codes; real payment gateway; order-status email is now in-scope (moved in); SMS OTP.

---

## 4. Assumptions, Constraints, Dependencies & Risks

### 4.1 Assumptions
**Users** — modern browsers; reliable internet; basic web literacy; individuals/small buyers; truthful info (no identity verification); valid, accessible email (used for OTP, resets, confirmations).

**Business** — single seller (not a marketplace); all prices in LKR and tax-inclusive; all orders ship within Sri Lanka; flat shipping fee; displayed stock taken as accurate at display time (no external warehouse sync).

**Technical (high-level only)** — single web server + database adequate for a demo; basic outbound email service; product images stored/served by the app (no CDN); app reachable over the network so scanners can target it.

**Payments** — checkout collects card details for realism but processes nothing; "payment successful" is always simulated unless admin later cancels; no real card data should be entered.

**Project** — this document is the basis for development; changes are treated as scope changes; the security assessment is a separate later phase.

### 4.2 Constraints
- Academic project: must exist to be scanned, not to go to market.
- Built with conventional implementation, no bolt-on hardening (WAF, advanced rate limiting, deep sanitization libraries); not deliberately sabotaged.
- Timeline/team bounded by course schedule.
- English only; LKR only; Sri Lanka delivery only; web only.
- One customer role + one flat admin role.
- One simulated payment method; no real third-party integrations.
- All product/static content is sample/illustrative; not legally reviewed.
- Admin accounts created manually (no self-registration); operated by a single trusted user.
- No SLAs, uptime targets, or support processes.

### 4.3 Dependencies
- A working dev/demo environment for submission.
- An outbound email service for OTP, resets, confirmations, status updates, contact relay.
- Sample product data and images (placeholders acceptable).
- Security scanning tools chosen in the next course phase.

### 4.4 Risks (scope-level, non-technical)
- **Scope creep** — deferred items may be requested mid-build. Mitigation: this document is the contract.
- **Ambiguous acceptance** — plain-English features may be interpreted differently. Mitigation: Section 10 makes behaviour explicit.
- **SCOPE.md / UI.md drift** — the two files must agree on screen names and flows. Mitigation: one shared screen inventory referenced by both.

---

## 5. User Roles & Permissions

### 5.1 Roles
- **Guest** — not logged in; can browse and shop up to (not including) checkout.
- **Customer** — registered + logged in; everything a guest can do plus account-bound features.
- **Admin** — staff with back-office access; manages catalog/orders/customers/messages; cannot shop.

A user has exactly one role; no overlap. Admin is not also a customer.

### 5.2 Account States (Customer)
- **Unverified** — created at registration; email OTP not yet confirmed. Cannot log in, cannot check out.
- **Active** — verified and usable (default after OTP).
- **Inactive** — deactivated by admin. Cannot log in. Existing orders preserved and visible to admin.

### 5.3 Permissions Matrix

| Capability | Guest | Customer | Admin |
|---|---|---|---|
| View home/category/listing pages | ✅ | ✅ | ❌ |
| Search and filter products | ✅ | ✅ | ❌ |
| View product detail pages | ✅ | ✅ | ❌ |
| View static pages | ✅ | ✅ | ✅ |
| Submit Contact Us form | ✅ | ✅ | ❌ |
| Add/update/remove cart items | ✅ | ✅ | ❌ |
| Register a new account | ✅ | ❌ | ❌ |
| Log in | ✅ | ❌ | ✅ (separate login) |
| Log out | ❌ | ✅ | ✅ |
| Request password reset | ✅ | ✅ | ❌ (admin managed manually) |
| Proceed to checkout / place order | ❌ | ✅ | ❌ |
| View own order history & details | ❌ | ✅ | ❌ |
| Manage own profile (name, contact) | ❌ | ✅ | ❌ |
| Change own password | ❌ | ✅ | ❌ |
| Manage own shipping addresses | ❌ | ✅ | ❌ |
| View all orders / any order detail | ❌ | ❌ | ✅ |
| Update order status / cancel order | ❌ | ❌ | ✅ |
| Create/edit/delete products | ❌ | ❌ | ✅ |
| Upload/replace product images | ❌ | ❌ | ✅ |
| Set/update price & stock | ❌ | ❌ | ✅ |
| Create/rename/delete categories | ❌ | ❌ | ✅ |
| View registered customers (read-only) | ❌ | ❌ | ✅ |
| Toggle customer Active/Inactive | ❌ | ❌ | ✅ |
| View/mark contact messages | ❌ | ❌ | ✅ |
| Access admin panel | ❌ | ❌ | ✅ |

### 5.4 Role Behaviour Rules
- **Guest → Customer:** guest cart held by browser session; on register/login it **merges** into the account cart (duplicate products: quantities summed, capped — see 10.5). A guest attempting checkout is redirected to login/register and returned to checkout afterward.
- **Customer session:** stays logged in until logout or expiry; visiting login/register while logged in redirects to the account dashboard; cart persists across devices.
- **Admin separation:** admin area at a separate path (`/tb-backroom-engine`), separate login, not linked from public header/footer; reachable only by direct URL. A logged-in admin is treated as a guest by the storefront; to test purchasing they use a separate customer account.
- **Password rules:** customers self-serve change and reset; admins have no self-service reset (managed manually).

---

## 6. Core Feature Breakdown

### 6.1 Storefront & Browsing
- **F1. Home page** — hero/banner, featured products row, category quick links, footer with static links. Featured logic per 10.13.
- **F2. Category browsing** — grid of products per category (image, name, LKR price, stock indicator); pagination (12/page).
- **F3. Product search** — header search bar on all storefront pages; matches product **name** only; results grid; clear "no results" state.
- **F4. Filtering & sorting** — filter by category (on search results) and in-stock-only; sort by newest (default), price asc, price desc, name A–Z.
- **F5. Product detail page** — name, large image + thumbnails, full description, specifications table, LKR price, stock status, category, quantity selector, Add-to-cart (disabled when out of stock), breadcrumb.

### 6.2 Account Management
- **F6. Registration (+ email OTP)** — fields: full name, email, contact number, password, confirm password. Creates an **Unverified** account, sends a 6-digit OTP, routes to the verification screen. On success → Active + auto-login.
- **F7. Login (+ email OTP)** — fields: email, password, remember-me, forgot-password link. Valid credentials → OTP emailed → verification screen → session on success. Every login requires OTP (Flavor A).
- **F8. Logout** — ends session, returns to home.
- **F9. Forgot password & reset** — email entry → generic confirmation → tokenized reset link (Active accounts only) → set-new-password screen → redirect to login. Token expiry per 10.1.
- **F10. Profile management** — view/edit name & contact number; email read-only.
- **F11. Change password** — current, new, confirm; stays logged in on success.
- **F12. Address book** — list addresses with default marker; add/edit/delete/set-default; first address auto-default; deleting default auto-promotes the most recently added remaining address.

### 6.3 Cart & Checkout
- **F13. Shopping cart** — add from product page; header cart badge; cart page with line items (image, name, unit price, qty selector, line total, remove), subtotal + shipping + grand total; empty state; persists by session (guest) / account (customer); merge on login.
- **F14. Checkout flow (3 steps)** — (1) shipping address (pick saved/add new; default pre-selected); (2) simulated payment (card number, cardholder, expiry, CVV; format checks only); (3) review & place order (summary + masked card last-4). Guests are redirected to login/register first.
- **F15. Order placement & confirmation** — on placement: stock decremented, order created **Pending**, cart emptied, on-screen confirmation with order number, confirmation email sent.

### 6.4 Orders
- **F16. Order history (customer)** — list of own orders (number, date, total, status), newest first.
- **F17. Order detail (customer)** — read-only: number, date, status, line items (snapshot), shipping address used, payment last-4, subtotal/shipping/grand total.

### 6.5 Static Content & Communications
- **F18. Static pages** — About Us, Terms & Conditions, Privacy Policy, Shipping & Delivery, FAQ. Footer-linked, content-only.
- **F19. Contact Us** — form (name, email, subject, message). On submit: stored in DB **and** emailed to admin; on-screen confirmation. Logged-in users get name/email pre-filled (read-only).

### 6.6 Admin Module
- **F20. Admin login** — separate non-public URL; email + password; no forgot-password.
- **F21. Admin dashboard** — counters (total products, total orders, orders-by-status, total customers, unread messages) + recent orders and recent messages mini-lists.
- **F22. Product management** — list/search/filter; create/edit/delete; specs as key-value rows; multiple images with primary selection; featured & active toggles. Inactive products hidden from storefront but preserved for historical orders.
- **F23. Category management** — list with product counts; create, rename, delete (only when empty).
- **F24. Order management** — list/filter/search; admin order detail (more than customer sees); status updates (one step forward); cancel from any state except Delivered. **On cancellation, all line-item stock is automatically restored**, regardless of prior status.
- **F25. Customer management** — list/search; toggle Active/Inactive only (no other edits, no password access).
- **F26. Contact message management** — list (unread highlighted); view (auto-marks read); manual mark-unread; no in-app reply (admin replies via external email).

---

## 7. User Journeys / Flows

### 7.1 Guest
- **J1. Browse & leave** — home → category → product detail → leave; nothing persists beyond session.
- **J2. Search** — keyword → results → filter/sort → product detail.
- **J3. Add to cart → register → checkout** — product → add to cart (toast, stay on page) → continue shopping → cart → proceed to checkout → redirected to login/register → register → auto-login + **cart merge** → returned to checkout → complete (J7).
- **J4. Add to cart → login → checkout** — as J3 but logs into an existing account; **cart merge** (quantities summed, capped) → checkout.
- **J5. Contact Us** — footer → form → submit → confirmation; stored in DB + emailed to admin.
- **J6. Static pages** — footer link → page → read.

### 7.2 Customer
- **J7. Complete a purchase** — cart → checkout step 1 (address; default pre-selected; add new inline) → step 2 (simulated payment, format checks) → step 3 (review: items, address, masked card, totals) → Place order → order created **Pending**, stock decremented, cart emptied → confirmation page (order number) → confirmation email.
- **J8. Register (+ OTP)** — Register form → submit → **Unverified** account + OTP emailed → "Verify your email" screen → enter OTP (10 min) → success → **Active** + auto-login → home (or checkout if mid-purchase). Wrong OTP: re-enter; after 5 wrong, OTP invalidated → resend required. Expired OTP: resend (60s cooldown).
- **J8.1. Resume unverified registration** — user closes verification screen → later logs in with same credentials → system detects Unverified → fresh OTP emailed → routed to verification → success → proceeds as J8.
- **J9. Login (+ OTP, every login)** — login form → valid credentials + Active → OTP emailed → "Verify login" screen → enter OTP → session → redirect to origin/home. Unverified credentials → divert to J8.1. Inactive → generic "Login failed" (no status disclosure). Remember-me extends the post-OTP session, never skips OTP. Closing the OTP screen invalidates the in-progress login.
- **J10. Logout** — header → logout → session ends → home.
- **J11. Forgot password** — "Forgot password?" → email → generic confirmation → (Active only) tokenized link emailed → "Set new password" → success → login (then a normal J9 login incl. OTP). Expired link → error + request-new option. Unverified/Inactive accounts receive no email but see the same generic confirmation.
- **J12. Change password** — Profile → Change password → current/new/confirm → success, stays logged in.
- **J13. Manage profile** — Profile → edit name/contact (email read-only) → save.
- **J14. Manage addresses** — list → Add (first becomes default) / Edit / Set default / Delete (deleting default auto-promotes most recently added).
- **J15. Order history** — My Orders → list (newest first) → open order → read-only detail.

### 7.3 Admin
- **J16. Admin login** — direct `/tb-backroom-engine` URL → login → dashboard.
- **J17. Add product** — Add product → fields + specs rows + multi-image upload (set primary) → save → appears in list and (if active) on storefront.
- **J18. Edit product** — list → find (search/filter) → Edit → update/add/remove images, toggle featured/active → save → storefront updates.
- **J19. Delete product** — Delete → confirm → removed; historical orders intact via line-item snapshot.
- **J20. Manage categories** — Categories → create / rename inline / delete (blocked if non-empty, with guidance).
- **J21. Process an order** — Orders (default Pending) → open → update status one step (Pending→Processing→Shipped→Delivered) → customer detail view reflects change + status email sent.
- **J22. Cancel an order** — open order in any non-Delivered state → Cancel → confirm → **Cancelled**, **all stock restored** → customer history reflects it + status email sent.
- **J23. Toggle customer status** — Customers → search/filter → Deactivate/Activate → confirm → status changes; deactivated cannot log in; orders remain visible.
- **J24. Review contact messages** — Messages → unread highlighted → open (auto-read) → optionally mark unread → reply externally.

### 7.4 Cross-cutting Flow Rules
- **Mid-action auth** — unauthenticated actions requiring login redirect to login/register and return to the original action on success.
- **Empty-cart checkout protection** — reaching checkout with an empty cart redirects to the cart with a message.
- **Empty states** — every list (cart, orders, addresses, search) has a defined empty-state message + CTA.
- **Cancel/back** — every multi-step form has a non-saving cancel/back option.
- **Confirmation prompts** — destructive actions (delete product/category/address, cancel order, deactivate customer) require explicit confirmation.
- **Session expiry mid-flow** — cart preserved; checkout step progress lost; user re-logs in (incl. OTP).
- **OTP screens are stateful** — closing a login OTP screen invalidates that login attempt; restart from login.

---

## 8. Product Catalog Definition

### 8.1 Categories (flat; one category per product)
Keyboards · Mice · RAM · Storage · Monitors · Headsets & Audio · Webcams · Cables & Adapters · Laptop Accessories · Power & Charging.
No sub-categories. Admin may add/rename/delete categories (subject to 10.4).

### 8.2 Product Attributes
- **Product name** — text, required.
- **SKU / product code** — free-form text, required, unique across all products (incl. inactive/deleted).
- **Category** — single existing category, required.
- **Short description** — single paragraph, required (used on cards/search snippets).
- **Full description** — multi-paragraph plain text, required (no HTML rendering).
- **Specifications** — 0+ key-value pairs, admin-defined; renders as a two-column table.
- **Price** — integer LKR, required.
- **Stock quantity** — integer ≥ 0, required.
- **Featured flag** — boolean (default false).
- **Active flag** — boolean (default true).
- **Images** — one or more; one marked **primary**; admin can add/remove/reorder and change primary.
- **System fields** — product ID, created date, last-updated date (auto).

### 8.3 Stock & Availability
- Display: `> 5` → "In stock"; `1–5` → "Only X left"; `0` → "Out of stock" (Add-to-cart disabled).
- Out-of-stock products remain visible.
- Inactive products: not listed, not searchable, not openable by URL (return Not Found / S30).
- Stock decremented at order placement; restored automatically on admin cancellation.
- **Cart does not reserve stock** — first to checkout wins; a checkout that would push stock below zero fails and returns to cart with the line flagged.

### 8.4 Pricing & Currency
- All prices in LKR, whole rupees, no decimals.
- Display format: `LKR 12,500` ("LKR" + space + comma thousand-separators).
- Stored as integers; no multi-currency; tax-inclusive (no tax breakdown).

### 8.5 Product History & Order Integrity (snapshot rule)
At order placement, each line item snapshots: product name, SKU, unit price, and primary image reference at time of order. Later edits or deletion of the product do **not** alter past orders. Applies to both customer and admin order detail views.

### 8.6 Catalog Volume (guidance for seed data)
~10 categories; ~40 products (3–5 per category); 1–3 images per product on average.

---

## 9. Screen Inventory (Reference)

Full layouts and per-screen detail live in `UI.md`. This is the canonical ID list (41 screens).

**Storefront (S1–S5):** S1 Home · S2 Category listing · S3 Search results · S4 Product detail · S5 Shopping cart.
**Authentication (S6–S12):** S6 Register · S7 Email OTP (registration) · S8 Login · S9 Email OTP (login) · S10 Forgot password · S11 Forgot-password confirmation · S12 Set new password.
**Customer account (S13–S19):** S13 Account dashboard · S14 My Profile · S15 Change password · S16 My Addresses · S17 Add/Edit Address · S18 My Orders · S19 Order detail (customer).
**Checkout (S20–S23):** S20 Checkout – Shipping · S21 Checkout – Payment · S22 Checkout – Review · S23 Order confirmation.
**Static & utility (S24–S30):** S24 About Us · S25 Terms & Conditions · S26 Privacy Policy · S27 Shipping & Delivery · S28 FAQ · S29 Contact Us · S30 Error / Not found.
**Admin (A1–A11):** A1 Admin login · A2 Admin dashboard · A3 Product list · A4 Add product · A5 Edit product · A6 Category management · A7 Order list · A8 Order detail (admin) · A9 Customer list · A10 Contact messages list · A11 Contact message detail.

---

## 10. Business Rules

### 10.1 Authentication & Session
**Password** — 8–64 chars; at least one letter and one number; confirm must match exactly; same rules at registration/change/reset.

**Email** — standard format; ≤254 chars; stored/compared case-insensitively; one account per email.

**Contact number** — Sri Lankan format; accepts `0771234567`, `+94771234567`, `077 123 4567`; normalized to `+94XXXXXXXXX`; 9 local / 11 with country code after normalization.

**Session** — 30 min inactivity expiry; remember-me extends to 30 days inactivity; explicit logout always ends session; concurrent sessions allowed. On expiry mid-flow: cart preserved, checkout progress lost.

**OTP**
| Rule | Value |
|---|---|
| Format | 6-digit numeric |
| Delivery | Email only |
| Expiry | 10 minutes |
| Resend cooldown | 60 seconds |
| Max wrong attempts | 5, then OTP invalidated |
| On resend | Old OTP invalidated immediately |
| Required at | Registration verification AND every login |

**Password reset token** — secure random in URL; 1-hour expiry; single-use (invalidates on success); requesting a new reset invalidates old tokens.

**Account lockout** — after **5 consecutive failed login attempts** (wrong password at the credentials step; OTP failures excluded), the account is locked **15 minutes**. Counter resets on success or after lockout expiry. During lockout, even correct credentials return the generic "Invalid email or password" (no lockout disclosure). Per-account, not per-IP.

**Login failure messaging** — generic for all modes ("Invalid email or password"); no disclosure of existence/status. Exception: Unverified accounts proceed to OTP after correct credentials (J8.1).

### 10.2 Registration
All fields required. Full name 2–100 chars (letters/spaces/dots/hyphens). Email unique across all accounts (incl. Inactive/Unverified). New account starts **Unverified**. Re-registration with the same email is allowed only if the existing account is Unverified **and** its OTP has expired (the Unverified record is then replaced); otherwise rejected as "email already in use".

### 10.3 Products
Name 3–200; SKU 3–50 (alphanumeric + dash/underscore, unique incl. inactive/deleted); category required; short desc 10–200; full desc 10–5000 (plain text, paragraph breaks, no HTML render); specs 0–30 pairs (key 1–50, value 1–200); price integer LKR 1–9,999,999; stock 0–99,999; ≥1 image required to be active; ≤5 images; one primary.

**Images** — JPG/JPEG/PNG/WebP; ≤2 MB each; recommended 800×800 px square; no auto-resize.

**Deletion** — hard delete; SKU not reusable after deletion; past orders intact via snapshot (8.5).

**Active/Inactive** — inactive hidden everywhere (Not Found by URL). Carts may still hold a now-inactive product; checkout is blocked until that line is removed.

### 10.4 Categories
Name 2–50, unique case-insensitive. Cannot delete while it contains products. Renaming propagates to products/listings/breadcrumbs; past orders unaffected (line items snapshot product, not category).

### 10.5 Cart
Max 50 unique line items. Max qty per line = `min(10, available stock)`; selector caps at this. Re-adding a product increases the existing line (subject to cap). No stock reservation. Guest cart: browser session, ≤7 days inactivity then discarded. Customer cart: persisted with account, no auto-expiry. Merge on login: quantities summed and capped; excess silently capped with a notice. Inactive/out-of-stock lines show a warning; checkout blocked until resolved.

### 10.6 Checkout & Orders
**Shipping fee** — flat **LKR 500** per order, island-wide; no free-shipping threshold; added at cart and shown again at review.

**Order number** — `TB-NNNNNN`, zero-padded, monotonically increasing across all orders, never resets (e.g. `TB-000042`).

**Placement preconditions** — cart not empty; every line is an Active product with sufficient stock; an address selected; payment form passes format validation.

**On success** — order created **Pending**; stock decremented; cart emptied; confirmation email queued; redirect to S23.

**On failure** (e.g. stock changed) — no order, no stock change, cart preserved, problematic lines flagged.

**Status flow** — allowed: Pending→Processing, Processing→Shipped, Shipped→Delivered, and Pending/Processing/Shipped→Cancelled. Forbidden: anything out of Delivered or Cancelled, and any backward transition. Admin moves forward one step at a time or cancels; each change is timestamped (shown in A8 timeline).

**On cancellation** — all line-item stock restored; customer history shows Cancelled; an order-status email is sent (see 10.11).

### 10.7 Simulated Payment
Collects: cardholder name 2–100; card number 13–19 digits (spaces stripped; **Luhn** check for format only, no issuer/authorization); expiry MM/YY (month 01–12; not in the past); CVV 3–4 digits. On valid format → treated as accepted. Store only **last 4 digits** + cardholder name + expiry; **never store CVV**. No real gateway contacted.

### 10.8 Shipping Addresses
Label 1–30 (optional); recipient 2–100; line 1 3–200; line 2 0–200 (optional); city 2–50; district from the **25 official Sri Lankan districts** (required); postal code 5 digits; contact number per 10.1. Max 10 addresses/customer. First address auto-default; deleting default auto-promotes the most recently added. Address used in an order is snapshotted with it.

### 10.9 Search & Filter
Search matches **product name** only; case-insensitive partial match; min query 2 chars (shorter → empty + hint); max 100 chars. In-stock filter excludes stock = 0. Default sort newest first. Inactive products excluded from all search/filter results.

### 10.10 Pagination
Storefront grid (S2/S3): 12/page. Admin products (A3): 20/page. Admin orders (A7): 20/page. Customer orders (S18): 10/page. Admin customers (A9): 20/page. Admin messages (A10): 20/page.

### 10.11 Email Notifications
1. **Registration OTP** — at registration and on OTP resend.
2. **Login OTP** — at each login attempt.
3. **Password reset link** — on forgot-password (Active accounts only).
4. **Order confirmation** — on placement (order number, items, totals, address, last-4).
5. **Order status update** — when admin changes status to Processing/Shipped/Delivered/Cancelled (order number, new status, contextual message).
6. **Contact submission** — to admin's configured address (sender info + message).

Not sent: marketing, newsletter, SMS, push.

### 10.12 Contact Form
Name 2–100; email per 10.1; subject 3–150; message 10–2000. Logged-in users: name/email pre-filled and read-only. On submit: stored in DB + emailed to admin + on-screen confirmation + form cleared. **No rate limiting in application logic** (intentional, for scanner findings).

### 10.13 Featured Products (Home, S1)
Up to 8 slots. Featured-flagged shown first (most recently created among them, capped at 8). If fewer than 8 featured, fill remaining slots with most recently created Active products (excluding ones already shown). If fewer than 8 active products exist, show only those.

### 10.14 Admin-Specific
Admin accounts created manually (DB seed/insert); no self-registration screen. One flat privilege level. Admin sessions follow the 30-min inactivity rule with no remember-me. Admin cannot place orders, use a cart, or view the storefront as a logged-in customer.

### 10.15 Data Retention (project lifetime)
Orders: indefinite. Inactive accounts: indefinite (re-activatable). Unverified accounts: 24 hours then auto-deleted (J8.1 resume works until cleanup runs). Contact messages: indefinite. Guest cart: 7 days inactivity. Customer cart: persisted. Expired OTPs/reset tokens: deleted on next cleanup (no functional impact).

### 10.16 Currency & Number Formatting
Display `LKR 12,500`; stored as integers; no decimals anywhere customer-facing.

### 10.17 Date & Time Formatting
Display timezone Asia/Colombo (UTC+5:30). Customer-facing dates: `27 May 2026`. Order/admin date+time: `27 May 2026, 14:35` (24-hour). Store timestamps in UTC; convert for display.

---

## 11. Content Requirements

### 11.1 Seed — Categories
The 10 categories from 8.1 are seeded at install.

### 11.2 Seed — Products
~40 products across 10 categories (3–5 each). Names/descriptions illustrative (may be inspired by real products; specs may be invented). Include: ≥5 Featured; ≥3 with stock 0; ≥3 with stock 1–5; ≥1 Inactive; ≥5 with multiple images (2–3). Placeholder images acceptable. Price spread LKR 800–250,000.

### 11.3 Seed — Admin Account
Single admin seeded at install. Email e.g. `admin@techbits.lk` (project owner confirms). Initial password documented in handover, not committed to source control.

### 11.4 Seed — Customer Accounts
**None.** Database ships with no customer accounts; the demonstrator registers their own.

### 11.5 Static Page Content
Placeholder text (1–3 short paragraphs each), with an "illustrative academic content" disclaimer.
- **About Us** — TechBits as a Sri Lankan online accessories retailer + a mission line.
- **Terms & Conditions** — acceptance, product/pricing accuracy, ordering & payment, shipping, cancellations/returns, IP, liability, governing law (Sri Lanka).
- **Privacy Policy** — data collected (name, email, contact, address, order history), why, how stored, no third-party sharing, contact for data questions.
- **Shipping & Delivery** — flat LKR 500 island-wide, ~3–7 working days, no international, contact for issues.
- **FAQ** — 6–10 Q&A (placing an order, browsing without an account, payment methods, shipping cost, delivery time, cancellation, tracking, damaged product, password reset, who to contact).

### 11.6 Email Templates (basic HTML)
- **T1 Registration OTP** — subject `TechBits — Verify your email`; greeting + 6-digit OTP + 10-min expiry + ignore note.
- **T2 Login OTP** — subject `TechBits — Your login code`; login-framed + "if this wasn't you, change your password".
- **T3 Password reset** — subject `TechBits — Password reset request`; reset link + 1-hour expiry + ignore note.
- **T4 Order confirmation** — subject `TechBits — Order confirmation (TB-NNNNNN)`; thanks, order number, items (name/qty/unit/line), subtotal, shipping, grand total, address, last-4, delivery window, view-in-account note.
- **T5 Order status update** — subject `TechBits — Your order TB-NNNNNN is now [Status]`; order number, new status, status-specific message.
- **T6 Contact submission (to admin)** — subject `TechBits Contact — [subject]`; sender name/email, subject, message, timestamp.

All emails: basic HTML, English, LKR formatting, Asia/Colombo time, TechBits sign-off.

### 11.7 System Messages & Microcopy
**Success** — "Account created. We've sent a code to your email." / "Logged in successfully." / "Password changed successfully." / "Address saved." / "Profile updated." / "Message sent. We'll get back to you soon." / "Order placed successfully."
**Errors (generic, non-disclosing)** — "Invalid email or password." / "Invalid or expired code." / "Invalid or expired reset link." / "Something went wrong. Please try again."
**Stock/cart** — "Out of stock." / "Only X left." / "Your cart is empty." / "Some items in your cart are unavailable. Please review."
**Confirmations** — "Are you sure you want to delete this product?" / "…delete this address?" / "Are you sure you want to cancel this order? Stock will be restored." / "Are you sure you want to deactivate this customer?"
**Empty states** — Cart: "Your cart is empty. Browse products to get started." / Orders: "You haven't placed any orders yet." / Addresses: "You don't have any saved addresses yet." / Search: "No products match your search." / Category: "No products in this category yet."

### 11.8 Branding Content (high level — full UI direction in UI.md)
Name: TechBits. Tagline: "Computer accessories, delivered across Sri Lanka." Logo: clean text wordmark (+ optional small icon), UI-agent generated. Tone: friendly, direct, neutral.

### 11.9 Academic Disclaimer
- Footer of every page: "TechBits is an academic project. No real transactions are processed."
- Payment screen (S21): a more prominent notice that no real payment is processed and no real card data should be entered.

---

## 13. Accessibility & Usability

**Boundary:** reasonable practices, not formal WCAG certification.

### 13.1 Accessibility Baseline
Labelled inputs (no placeholder-only fields); reasonable color contrast; alt text on product images (default to product name); status never by color alone (badges carry text); visible focus states; primary flows keyboard-completable; errors associated with fields where feasible.

### 13.2 Usability Principles
One clear primary action per screen; short forms; immediate specific validation; destructive actions confirmed; consistent placement of recurring elements; every collection has an empty state with a CTA; no dead ends.

### 13.3 Out of Scope (restated)
Formal WCAG audit; screen-reader certification; accessibility statement page.

---

## 14. Notifications & Communications

### 14.1 Email (index — detail in 10.11 / 11.6)
| # | Trigger | Recipient | Template |
|---|---|---|---|
| 1 | Registration / OTP resend | New customer | T1 |
| 2 | Login credentials accepted | Customer | T2 |
| 3 | Forgot-password (Active) | Customer | T3 |
| 4 | Order placed | Customer | T4 |
| 5 | Admin status change (Processing/Shipped/Delivered/Cancelled) | Customer | T5 |
| 6 | Contact submitted | Admin | T6 |

### 14.2 On-Screen
Toasts (transient confirmations); inline messages (field validation, success banners); confirmation modals (destructive actions); generic security messages (non-disclosing); empty states (per 11.7).

### 14.3 NOT Sent
SMS; marketing/newsletter; push; in-app notification center; admin email on new order/registration (admin uses the dashboard).

---

## 15. Deliverables

This scoping exercise produces two files:

- **`SCOPE.md`** (this file) — the functional scope for the AI coding agent: overview, audience, scope boundaries, assumptions/constraints, roles/permissions, features, journeys, catalog, screen reference, business rules, content, accessibility, notifications, glossary.
- **`UI.md`** — the visual specification for the AI UI mock-up agent: design language (brand, color, typography, layout, components, interaction, accessibility, imagery, microcopy), a UI-relevant restatement of the business rules that affect screen content, and a detailed screen-by-screen layout for all 41 screens with a responsive mock-up matrix.

The two files share screen IDs (S1–S30, A1–A11) and the Glossary so each can be used independently. `UI.md` intentionally restates UI-affecting rules (validation limits, character counts, status colors, formats) so the UI agent does not need to cross-reference this file.

---

## 16. Glossary

- **Guest** — an unauthenticated visitor.
- **Customer** — a registered, logged-in shopper. States: Unverified, Active, Inactive.
- **Admin** — a back-office user; single flat privilege level; created manually.
- **Unverified** — a customer account created but not yet email-OTP confirmed; cannot log in or check out.
- **Active** — a verified, usable customer account.
- **Inactive** — an admin-deactivated customer account; cannot log in; orders preserved.
- **OTP** — a 6-digit numeric one-time code emailed for registration verification and every login.
- **Reset token** — a single-use, time-limited token in a password-reset link (distinct from OTP).
- **Cart merge** — combining a guest's session cart into the account cart at login (quantities summed, capped).
- **Snapshot** — product/address data copied onto an order line at placement so later edits/deletions don't alter past orders.
- **Featured** — an admin flag surfacing a product on the home page.
- **Active product** — a product visible/sellable on the storefront (vs. Inactive: hidden but preserved for history).
- **SKU** — a free-form, unique product code.
- **Simulated payment** — a payment form that validates format only and is always treated as accepted; no real gateway; CVV never stored.
- **Order statuses** — Pending → Processing → Shipped → Delivered; Cancelled (from any non-Delivered state).
- **District** — one of the 25 official Sri Lankan districts (address field).
- **Storefront** — the customer-facing site (vs. the Admin panel at `/tb-backroom-engine`).
