# Feature Specification: TechBits eCommerce Application

**Feature Branch**: `001-techbits-ecommerce-spec`

**Created**: 2026-05-31

**Status**: Draft

**Source**: Generated directly from `SCOPE.md` and `UI.md` — all requirements preserved exactly as written in those files.

---

## Clarifications

### Session 2026-05-31

- Q: When a customer adds a new address inline during checkout step 1 (S20), is the address permanently saved to their address book (counting toward the 10-address limit), or used only for that single order? → A: Permanently saved to address book, subject to the 10-address limit.
- Q: When a cart line's quantity exceeds currently available stock (stock > 0 but less than the cart quantity), does the cart page show a proactive warning at view time, or is the customer only notified when checkout fails? → A: Cart page proactively shows a warning on the affected line and reduces the quantity selector cap to current stock; checkout is blocked until the quantity is adjusted.

---

## User Scenarios & Testing

User journeys are ordered by role and then by operational priority. Each represents a independently testable slice of the application.

---

### User Story 1 — Guest Browse & Cart (Priority: P1)

A guest (unauthenticated visitor) can browse the home page, navigate by category, search and filter products, view product detail pages, and add items to a session cart — all without registering.

**Why this priority**: Core browsing and cart are the foundation of the storefront. All other customer flows depend on this working first.

**Independent Test**: A visitor can land on the home page, browse a category, open a product detail page, and add an item to cart — the cart badge updates and the cart page shows the item — without any account.

**Acceptance Scenarios**:

1. **Given** a guest lands on the home page, **When** they view the page, **Then** they see a hero banner, up to 8 featured products (most-recently-created featured-flagged first, filled with most-recent active products if fewer than 8 featured exist), and 10 category quick links.
2. **Given** a guest clicks a category link, **When** the category page loads, **Then** they see a product grid (12/page), a filter/sort bar (in-stock-only toggle; sort by Newest/Price↑/Price↓/Name A–Z), and pagination; if the category is empty, they see "No products in this category yet."
3. **Given** a guest enters a keyword ≥2 characters in the search bar, **When** they submit, **Then** they see a results grid with result count, a category-dropdown filter, an in-stock toggle, sort options, and pagination; if no results, they see "No products match your search."
4. **Given** a guest enters fewer than 2 characters, **When** they search, **Then** they see a short-query hint and no results grid.
5. **Given** a guest opens a product detail page, **When** the page loads, **Then** they see: name, large primary image + thumbnail strip, full description, specifications table, LKR price, stock status indicator, category, quantity selector (capped at min(10, stock)), breadcrumb, and an active Add-to-cart button; if stock = 0, the button is disabled and labelled "Out of stock."
6. **Given** a product is inactive, **When** a guest navigates to its URL, **Then** they see the Not Found / S30 page.
7. **Given** a guest adds a product to cart, **When** the action completes, **Then** a toast confirmation appears, the page does not navigate away, and the cart badge in the header increments.
8. **Given** a guest has items in cart and opens the cart page (S5), **When** the page loads, **Then** they see line items (image, name, unit price, quantity selector, line total, remove button), a summary (subtotal + LKR 500 shipping + grand total), and buttons for "Continue shopping" and "Proceed to checkout."
9. **Given** a guest cart is empty, **When** they open the cart, **Then** they see "Your cart is empty. Browse products to get started." with a CTA.
10. **Given** a guest cart holds an inactive product, **When** they view the cart, **Then** a warning is shown and checkout is blocked until the line is removed.

---

### User Story 2 — Registration, OTP Verification, and Login (Priority: P1)

A guest can create an account (full name, email, contact number, password, confirm password), verify it via a 6-digit email OTP, and subsequently log in using email + password + OTP at every login.

**Why this priority**: Account creation and login gate checkout and all customer-specific flows.

**Independent Test**: A new user can register, receive and enter the OTP, reach the Active state, and log back in via the full OTP flow — ending with an authenticated session.

**Acceptance Scenarios**:

1. **Given** a guest submits the registration form with valid data, **When** the form is processed, **Then** an Unverified account is created and a 6-digit OTP is emailed; the user is routed to the email OTP screen (S7).
2. **Given** registration fields violate rules (name 2–100 chars; email ≤254, unique; contact: Sri Lankan format; password 8–64 chars with ≥1 letter and ≥1 number; confirm must match), **When** the form is submitted, **Then** inline field errors appear and no account is created.
3. **Given** the email is already in use by an Active or Inactive account, **When** registration is attempted, **Then** it is rejected as "email already in use."
4. **Given** the email belongs to an Unverified account whose OTP has expired, **When** re-registration is attempted, **Then** the Unverified record is replaced.
5. **Given** a user enters the correct OTP within 10 minutes, **When** it is submitted, **Then** the account becomes Active, the user is auto-logged in, and they are routed to the home page (or checkout if mid-purchase).
6. **Given** a user enters an incorrect OTP, **When** submitted, **Then** an "Invalid or expired code" error appears; after 5 wrong attempts the OTP is invalidated and the user must resend.
7. **Given** the OTP expires (>10 minutes), **When** the user attempts to verify, **Then** they see "Invalid or expired code" and can request a resend after the 60-second cooldown.
8. **Given** a user closes the OTP verification screen and later logs in with Unverified credentials, **When** login is attempted, **Then** a fresh OTP is emailed and the user is routed back to the verification screen (J8.1).
9. **Given** a guest enters valid credentials for an Active account on the login screen (S8), **When** they submit, **Then** a 6-digit OTP is emailed and they are routed to the login OTP screen (S9).
10. **Given** the login OTP screen is shown and the user closes it, **When** they attempt to use that OTP, **Then** the in-progress login is invalidated and must restart from the login screen.
11. **Given** a user submits incorrect credentials, **When** login fails, **Then** the generic message "Invalid email or password" is shown; no account existence or status is disclosed.
12. **Given** 5 consecutive failed credential attempts, **When** the account is locked (15 minutes, per-account not per-IP), **Then** even correct credentials return "Invalid email or password" (no lockout disclosure).
13. **Given** an Inactive account's credentials are submitted, **When** login is attempted, **Then** "Invalid email or password" is shown (no status disclosure).
14. **Given** "Remember me" is checked on login, **When** the OTP is verified, **Then** the post-OTP session extends to 30 days inactivity; OTP is never skipped.
15. **Given** a logged-in customer visits the login or register page, **When** the page loads, **Then** they are redirected to the account dashboard.

---

### User Story 3 — Password Reset (Priority: P2)

A guest or customer can request a password reset by email, receive a single-use time-limited tokenized link, and set a new password.

**Why this priority**: Required for account recovery. Does not block browsing but blocks checkout for locked-out customers.

**Independent Test**: A user enters their email on the Forgot Password screen, receives a reset link, clicks it, sets a new password, and can log in with the new password via the normal OTP flow.

**Acceptance Scenarios**:

1. **Given** a user submits their email on S10, **When** processed, **Then** they see the generic confirmation "If this email exists, a reset link has been sent." regardless of whether the email belongs to an Active, Inactive, or Unverified account (or does not exist).
2. **Given** the email belongs to an Active account, **When** the confirmation is shown, **Then** a tokenized reset link (secure random, 1-hour expiry, single-use) is emailed.
3. **Given** the email belongs to an Inactive or Unverified account, **When** the confirmation is shown, **Then** no email is sent.
4. **Given** a user clicks a valid reset link before expiry, **When** S12 loads, **Then** they can set a new password (8–64 chars, ≥1 letter, ≥1 number, confirm must match); on success the token is invalidated and the user is redirected to login.
5. **Given** a user clicks an expired or already-used link, **When** S12 loads, **Then** they see "Invalid or expired reset link." and an option to request a new one.
6. **Given** a user requests a new reset while a token is still valid, **When** the new request is processed, **Then** the old token is invalidated immediately.

---

### User Story 4 — Checkout & Order Placement (Priority: P1)

An authenticated customer can complete the 3-step checkout (shipping address → simulated payment → review), place an order, receive an on-screen confirmation and email, and view the order in their history.

**Why this priority**: Checkout is the core revenue flow; everything else supports it.

**Independent Test**: A logged-in customer with items in cart can complete all 3 checkout steps, place the order, see the confirmation screen with a TB-NNNNNN order number, receive the confirmation email, and view the order in My Orders with a Pending status.

**Acceptance Scenarios**:

1. **Given** a guest clicks "Proceed to checkout", **When** they are not logged in, **Then** they are redirected to login/register and returned to checkout on success.
2. **Given** a customer reaches checkout with an empty cart, **When** any checkout screen is accessed, **Then** they are redirected to the cart with a message.
3. **Given** step 1 (S20) loads, **When** the customer has saved addresses, **Then** their default address is pre-selected; they may select a different saved address or add a new one inline; a new address added inline is permanently saved to the address book (subject to the 10-address limit) and used for the current order.
4. **Given** step 2 (S21) loads, **When** the page is displayed, **Then** a prominent notice states "Simulated payment — do not enter real card details"; the form collects cardholder name (2–100 chars), card number (13–19 digits, spaces stripped, Luhn check), expiry MM/YY (01–12, not in the past), and CVV (3–4 digits); format validation is the only check.
5. **Given** payment format validation passes, **When** step 3 (S22) loads, **Then** the review shows shipping address, payment (masked — last 4 only), all line items (snapshot: name, qty, unit price, line total), subtotal, shipping LKR 500, and grand total.
6. **Given** the customer clicks "Place order" on S22 and all placement preconditions are met (cart not empty, every line is an Active product with sufficient stock, an address is selected, payment form passed validation), **When** placement succeeds, **Then**: order created Pending; stock decremented per line item; cart emptied; confirmation email queued (T4); customer is redirected to S23 showing order number, items, total, address, and "A confirmation email has been sent."
7. **Given** stock changed between cart and placement causing a line to exceed available stock, **When** placement is attempted, **Then** no order is created, no stock is changed, cart is preserved, and the problematic line is flagged.
8. **Given** a cart contains an inactive product at checkout time, **When** the customer reaches checkout, **Then** checkout is blocked and the line must be removed first.
9. **Given** a guest had items in cart before logging in, **When** they log in, **Then** their guest cart merges into the account cart: quantities summed and capped at min(10, stock); excess silently capped with a notice; the merged cart is used for checkout.
10. **Given** an order is placed, **When** the customer views S19, **Then** they see order number, placed date, status badge, line items snapshot (name, SKU, unit price, qty, line total, primary image at time of order), shipping address used, payment card (last 4 only), subtotal, shipping LKR 500, and grand total — all read-only.

---

### User Story 5 — Customer Account & Address Management (Priority: P2)

An authenticated customer can view and edit their profile (name, contact number; email read-only), change their password, and manage up to 10 shipping addresses (add, edit, delete, set default).

**Why this priority**: Required to support checkout (saved addresses) and account maintenance.

**Independent Test**: A customer can update their name on the profile screen, add a new address, set it as default, and see it pre-selected on the checkout shipping step.

**Acceptance Scenarios**:

1. **Given** a customer opens My Profile (S14), **When** the page loads, **Then** they see their full name (editable), email (read-only, with note "Email cannot be changed"), and contact number (editable); saving valid values updates the profile.
2. **Given** a customer opens Change Password (S15), **When** they submit the current, new, and confirm-new password (8–64 chars, ≥1 letter, ≥1 number, confirm must match), **Then** on success their password is changed and they remain logged in.
3. **Given** a customer opens My Addresses (S16), **When** they have saved addresses, **Then** each address card shows label, recipient, full formatted address, contact, and a "Default" badge on the default; per-card actions are Edit, Delete, and Set as Default.
4. **Given** a customer has no addresses, **When** S16 loads, **Then** they see "You don't have any saved addresses yet." with a CTA.
5. **Given** a customer adds a first address, **When** saved, **Then** it is automatically set as default.
6. **Given** a customer deletes the current default address, **When** deletion is confirmed, **Then** the most recently added remaining address is automatically promoted to default.
7. **Given** an address form (S17) is submitted, **When** validation runs, **Then** label (optional, 1–30), recipient (2–100), line 1 (3–200), line 2 (optional, 0–200), city (2–50), district (dropdown of 25 Sri Lankan districts), postal code (5 digits), and contact number (Sri Lankan format) are all validated.
8. **Given** a customer already has 10 addresses, **When** they attempt to add an 11th, **Then** the action is blocked.
9. **Given** a customer views My Orders (S18), **When** the page loads, **Then** orders appear newest-first in a list (order number, date, LKR total, status badge) with 10-per-page pagination; if no orders, "You haven't placed any orders yet." is shown.

---

### User Story 6 — Order History (Priority: P2)

An authenticated customer can view a list of all their orders and open the read-only detail view for any order.

**Why this priority**: Customer-facing order visibility and trust.

**Independent Test**: A customer who has placed at least one order can view it in My Orders list and open the detail to see all line-item snapshot data, address, and payment last 4.

**Acceptance Scenarios**:

1. **Given** a customer opens My Orders (S18), **When** orders exist, **Then** they appear newest-first with order number, date, LKR total, and status badge; pagination is 10/page.
2. **Given** a customer clicks an order row, **When** S19 loads, **Then** it shows order number, placed date, status badge, line items (snapshot of name, SKU, unit price, qty, line total, primary image at time of order), shipping address, payment card (last 4 only), subtotal, shipping LKR 500, and grand total — all read-only, no cancel or edit action.

---

### User Story 7 — Static Pages & Contact Form (Priority: P3)

All users (guest, customer, admin) can read static pages (About Us, T&C, Privacy Policy, Shipping & Delivery, FAQ). Any user can submit the Contact Us form; the message is stored in the database and emailed to the admin.

**Why this priority**: Informational and support utility; low risk, no dependencies.

**Independent Test**: A guest can navigate to Contact Us (S29), fill in the form, submit it, and see "Message sent. We'll get back to you soon." with the form cleared.

**Acceptance Scenarios**:

1. **Given** any user opens a static page (S24–S28), **When** it loads, **Then** they see the page title, placeholder content, an academic-content disclaimer line, and the storefront footer.
2. **Given** a logged-in user opens Contact Us (S29), **When** the form loads, **Then** name and email are pre-filled and read-only.
3. **Given** the contact form is submitted with valid data (name 2–100, email, subject 3–150, message 10–2000), **When** submission succeeds, **Then** the message is stored in the database, an email is sent to the admin (T6), the form clears, and "Message sent. We'll get back to you soon." is shown.
4. **Given** the contact form, **When** evaluated, **Then** there is no rate limiting in application logic (intentional).
5. **Given** the FAQ page (S28) loads, **When** rendered, **Then** questions appear as an expandable Q&A list.

---

### User Story 8 — Admin: Product & Category Management (Priority: P1 for Admin)

The admin can create, edit, and delete products (with multiple images, specifications, featured/active toggles, price, and stock) and manage categories (create, rename, delete empty only).

**Why this priority**: The product catalog must exist before the storefront can function.

**Independent Test**: The admin can add a new product with at least one image, set it as Featured and Active, verify it appears on the storefront home page, then deactivate it and verify it disappears from the storefront.

**Acceptance Scenarios**:

1. **Given** the admin opens the product list (A3), **When** it loads, **Then** they see a table (image thumbnail, name, SKU, category, LKR price, stock, Featured badge, Active toggle, Edit/Delete actions), a filter bar (category dropdown, search by name), and pagination (20/page); they can click "Add product" to go to A4.
2. **Given** the admin submits the add/edit product form (A4/A5) with valid data, **When** saved, **Then** the product appears in the list and, if Active, on the storefront.
3. **Given** product field rules: name 3–200, SKU 3–50 (alphanumeric + dash/underscore, unique including inactive/deleted), category required, short description 10–200, full description 10–5000 (plain text, no HTML rendering), specifications 0–30 key-value pairs (key 1–50, value 1–200), price integer LKR 1–9,999,999, stock 0–99,999, images 1–5 (JPG/JPEG/PNG/WebP, ≤2 MB each), **When** any rule is violated, **Then** a validation error is shown.
4. **Given** a product is set to Active, **When** it has zero images, **Then** activation is blocked with an error (≥1 image is required to be active).
5. **Given** the admin sets the Featured flag, **When** the product is Active, **Then** it appears in the Featured Products slot on the home page (up to 8 featured products, most-recently-created first; remaining slots filled with most-recently-created Active products).
6. **Given** the admin marks a product Inactive, **When** the change is saved, **Then** the product disappears from all storefront listings, search, and category pages; its URL returns the Not Found page; carts holding it show a warning and block checkout until removed.
7. **Given** the admin deletes a product, **When** the action is confirmed, **Then** the product is hard-deleted; the SKU is never reusable; past orders are unaffected (line-item snapshot preserved per 8.5).
8. **Given** the admin uploads multiple images for a product, **When** managing them, **Then** they can reorder images, set any one as primary, and add/remove images; the primary image is the one used in product cards and order snapshots.
9. **Given** the admin views the specifications section (A4/A5), **When** adding specs, **Then** they can add, remove, and fill key-value rows; up to 30 pairs; key 1–50 chars, value 1–200 chars; the spec renders as a two-column table on the storefront (S4).
10. **Given** the admin opens category management (A6), **When** it loads, **Then** they see a list of categories with product counts, an "Add category" input, inline rename, and a Delete action per category.
11. **Given** the admin tries to delete a category that contains products, **When** deletion is attempted, **Then** it is blocked with an explanatory message.
12. **Given** the admin renames a category, **When** saved, **Then** the new name propagates to all products, listings, and breadcrumbs; past order line-item snapshots are unaffected.
13. **Given** category name rules: 2–50 chars, unique case-insensitively, **When** violated, **Then** a validation error is shown.

---

### User Story 9 — Admin: Order Management (Priority: P1 for Admin)

The admin can view all orders, advance order status one step at a time (Pending→Processing→Shipped→Delivered), and cancel any order not in Delivered state (with automatic stock restoration).

**Why this priority**: Core operations flow for the back-office.

**Independent Test**: The admin can find a Pending order, advance it to Processing (customer receives a status update email), then cancel it (customer receives a cancellation email, stock for all line items is restored).

**Acceptance Scenarios**:

1. **Given** the admin opens the order list (A7), **When** it loads, **Then** they see a table with order number, customer name, date, LKR total, status badge, and a View action; filter by status (default: Pending), search by order number or customer name, optional date range; pagination 20/page.
2. **Given** the admin opens an order detail (A8), **When** it loads, **Then** they see: order header (number, date, customer name/email/contact, status badge), line items snapshot, shipping address, payment (masked last 4), totals, a status-update control (next-status action, one step forward), a Cancel order action, and a status-change timeline with timestamps.
3. **Given** the current status allows a forward transition (Pending→Processing, Processing→Shipped, Shipped→Delivered), **When** the admin updates status, **Then** the status advances one step only; an order-status email is sent to the customer (T5); the timeline records the change with a timestamp.
4. **Given** the order is in Delivered or Cancelled state, **When** the admin views A8, **Then** the forward status-update control is disabled.
5. **Given** the admin clicks Cancel on an order in Pending, Processing, or Shipped state, **When** the action is confirmed ("Stock will be restored"), **Then** the order becomes Cancelled; all line-item stock quantities are automatically restored; a Cancelled status email is sent to the customer (T5).
6. **Given** the order is in Delivered state, **When** the admin views A8, **Then** the Cancel action is disabled.
7. **Given** the order number format rule is TB-NNNNNN (zero-padded, monotonically increasing, never resets), **When** an order is created, **Then** it receives the next sequential number (e.g., TB-000042).

---

### User Story 10 — Admin: Customer & Message Management (Priority: P2 for Admin)

The admin can view and search registered customers and toggle their Active/Inactive status; can view, read, and mark contact messages as read/unread.

**Why this priority**: Back-office oversight and support functions.

**Independent Test**: The admin can find a customer, deactivate them (they can no longer log in), reactivate them (they can log in again); the admin can open an unread contact message (it auto-marks as read) and mark it unread again.

**Acceptance Scenarios**:

1. **Given** the admin opens the customer list (A9), **When** it loads, **Then** they see a table (name, email, contact number, registration date, status badge, Activate/Deactivate toggle); search by name/email; status filter (Unverified/Active/Inactive); pagination 20/page.
2. **Given** the admin toggles a customer to Inactive, **When** confirmed, **Then** the customer can no longer log in; their orders remain visible in admin views; their account data is preserved.
3. **Given** the admin toggles a customer back to Active, **When** confirmed, **Then** the customer can log in again.
4. **Given** a deactivated customer attempts login, **When** credentials are submitted, **Then** "Invalid email or password" is shown (no status disclosure).
5. **Given** the admin opens the contact messages list (A10), **When** it loads, **Then** they see sender name, email, subject, date, read/unread indicator; unread rows are highlighted; pagination 20/page; clicking View opens A11.
6. **Given** the admin opens a message (A11), **When** it loads, **Then** the message auto-marks as read; they see sender name, email, subject, date, and message body; a "Mark as unread" toggle is available; there is no in-app reply (admin replies via external email).

---

### User Story 11 — Admin Dashboard & Login (Priority: P1 for Admin)

The admin can access the admin panel via a direct non-public URL, log in with email and password (no OTP, no forgot-password), and see a dashboard with counters and recent activity.

**Why this priority**: The admin must be able to log in before any admin function can be used.

**Independent Test**: Navigating to the admin URL shows the login screen; entering valid admin credentials shows the dashboard with counters and recent orders/messages lists.

**Acceptance Scenarios**:

1. **Given** the admin navigates to the `/admin` path, **When** not logged in, **Then** they see the admin login screen (A1) — a minimal centered card with email, password, and Log in button; no Forgot password or Register links.
2. **Given** valid admin credentials are submitted, **When** login succeeds, **Then** the admin is redirected to the dashboard (A2).
3. **Given** the admin dashboard (A2) loads, **When** displayed, **Then** it shows counter cards (total products, total orders, orders-by-status breakdown, total registered customers, unread contact messages) and a recent orders mini-list + recent messages mini-list.
4. **Given** the admin is logged in and the public storefront is visited, **When** the storefront is accessed as that session, **Then** they are treated as a guest (cannot shop, no customer cart, no customer account features).
5. **Given** the admin session, **When** inactive for more than 30 minutes, **Then** the session expires; there is no remember-me option for admin.
6. **Given** admin accounts, **When** considered, **Then** they are created manually (DB seed/insert only); there is no self-registration screen; one flat privilege level applies.

---

### Edge Cases

- What happens when a cart line's product becomes inactive while items are in the cart? → The line shows a warning; checkout is blocked until the line is removed.
- What happens when stock drops to zero between adding to cart and checkout? → The cart page proactively shows a warning (zero-stock, same as inactive line treatment); checkout is blocked until the line is removed.
- What happens when stock drops below the cart line quantity but is still greater than zero (partial stock reduction)? → The cart page proactively shows a warning on the affected line, the quantity selector cap is reduced to the current stock level, and checkout is blocked until the customer adjusts the quantity to ≤ current stock.
- What happens when the cart holds more than 50 unique line items? → Max 50 unique line items; the selector is capped.
- What happens when a guest's cart session is idle for more than 7 days? → The guest cart is discarded.
- What happens when the admin cancels an order that was already Delivered? → Cancellation is not allowed from Delivered state; the Cancel action is disabled.
- What happens when the admin tries to delete a category with products? → Deletion is blocked with an explanatory message.
- What happens when a reset token is used and then the link is clicked again? → "Invalid or expired reset link." is shown.
- What happens when the OTP screen is closed mid-login? → The in-progress login is invalidated; the user must restart from the login screen.
- How does the system handle an order whose product is later edited or deleted? → Line items snapshot product name, SKU, unit price, and primary image at placement time; later edits or deletions do not alter past orders.
- What happens if checkout is reached with an empty cart? → The user is redirected to the cart with a message.
- What happens during session expiry mid-checkout? → Cart is preserved; checkout step progress is lost; user must re-log in (including OTP).

---

## Requirements

### Functional Requirements

**Storefront & Browsing**
- **FR-001**: System MUST display a home page with a hero banner, up to 8 featured products (most-recently-created featured-flagged Active products first, remaining slots filled with most-recently-created Active products), and 10 category quick links.
- **FR-002**: System MUST display a product category listing page with a grid of 12 products per page, filter (in-stock-only), and sort (Newest/Price↑/Price↓/Name A–Z).
- **FR-003**: System MUST provide a header search bar on all storefront pages that matches product names only (case-insensitive partial match, minimum 2 characters, maximum 100 characters).
- **FR-004**: System MUST display a product detail page with name, large primary image + thumbnail strip, full plain-text description, specifications table, LKR price, stock status, category, quantity selector (capped at min(10, stock)), breadcrumb, and an Add-to-cart button (disabled when stock = 0).
- **FR-005**: System MUST display stock as: >5 → "In stock" (green), 1–5 → "Only X left" (amber), 0 → "Out of stock" (red, Add-to-cart disabled).
- **FR-006**: System MUST return a Not Found / S30 page when an inactive product URL is accessed.
- **FR-007**: Inactive products MUST be excluded from all storefront listings, category pages, and search results.

**Cart**
- **FR-008**: System MUST maintain a shopping cart for guests (session-based, ≤7 days inactivity) and customers (account-persisted, no auto-expiry).
- **FR-009**: System MUST cap each cart line at min(10, available stock) and cap total unique line items at 50.
- **FR-010**: System MUST display a cart badge in the header with the current item count, updating immediately on add/remove.
- **FR-011**: System MUST show a flat shipping fee of LKR 500 on the cart page and again on checkout review.
- **FR-012**: System MUST merge a guest cart into the account cart on login: quantities summed and capped at min(10, stock); excess silently capped with a notice.
- **FR-013**: System MUST show a warning on the cart for inactive or out-of-stock lines and block checkout until those lines are removed. System MUST also proactively warn on any line whose cart quantity exceeds current available stock (stock > 0 but < cart qty), reduce the quantity selector cap to current stock, and block checkout until the quantity is adjusted to ≤ current stock.

**Authentication & Accounts**
- **FR-014**: System MUST require email + password + 6-digit email OTP at every login (no OTP skip for any session type).
- **FR-015**: System MUST require email OTP verification before a new account becomes Active and can log in.
- **FR-016**: System MUST enforce account lockout after 5 consecutive failed credential attempts; the lockout lasts 15 minutes; it is per-account not per-IP; correct credentials during lockout return "Invalid email or password" with no lockout disclosure.
- **FR-017**: System MUST use generic error messages for all auth failures: "Invalid email or password" for credential failures; "Invalid or expired code" for OTP failures; no account existence or status disclosure.
- **FR-018**: System MUST use bcrypt password hashing; plain-text passwords MUST never be stored.
- **FR-019**: System MUST enforce password rules: 8–64 chars, at least one letter and one number, confirm must match; same rules apply at registration, change, and reset.
- **FR-020**: System MUST enforce email rules: standard format, ≤254 chars, stored and compared case-insensitively, one account per email.
- **FR-021**: System MUST enforce contact number as Sri Lankan format (accepts 0771234567, +94771234567, 077 123 4567); normalized to +94XXXXXXXXX for storage.
- **FR-022**: System MUST enforce OTP rules: 6-digit numeric, 10-minute expiry, resend cooldown 60 seconds, invalidated after 5 wrong attempts, old OTP invalidated immediately on resend, old OTP invalidated immediately on closing the login OTP screen.
- **FR-023**: System MUST enforce password reset token rules: secure random URL token, 1-hour expiry, single-use (invalidated on success), requesting new reset invalidates all old tokens.
- **FR-024**: System MUST allow re-registration with the same email only if the existing account is Unverified and its OTP has expired; the Unverified record is then replaced.
- **FR-025**: System MUST preserve an Unverified account for 24 hours then auto-delete; a user returning to login with Unverified credentials before deletion receives a fresh OTP (J8.1).
- **FR-026**: Sessions MUST expire after 30 minutes inactivity; remember-me extends to 30 days inactivity; explicit logout always ends session; concurrent sessions are allowed.
- **FR-027**: System MUST redirect a logged-in customer who visits login or register to the account dashboard.
- **FR-028**: System MUST redirect a guest attempting checkout to login/register and return them to checkout on success.
- **FR-029**: System MUST redirect any checkout screen access with an empty cart to the cart page with a message.
- **FR-030**: Forgot password MUST show the same generic confirmation page regardless of whether the email exists, belongs to an Active, Inactive, or Unverified account; only Active accounts receive a reset email.

**Profile & Addresses**
- **FR-031**: System MUST allow a customer to edit their full name (2–100 chars) and contact number; email MUST be read-only after registration.
- **FR-032**: System MUST allow a customer to manage up to 10 shipping addresses; address fields per 10.8; first address auto-default; deleting default auto-promotes the most recently added remaining address.

**Checkout & Orders**
- **FR-033**: System MUST implement a 3-step checkout: Step 1 (S20) shipping address selection; Step 2 (S21) simulated payment; Step 3 (S22) review and place order. A new address added inline on S20 MUST be permanently saved to the customer's address book (subject to the 10-address limit per FR-032) and used for the current order.
- **FR-034**: System MUST validate payment input for format only (cardholder 2–100 chars, card number 13–19 digits with Luhn check, expiry MM/YY not in past, CVV 3–4 digits); no real gateway is contacted; payment is always treated as accepted on valid format.
- **FR-035**: System MUST store only the last 4 digits of the card number, cardholder name, and expiry; CVV MUST never be stored.
- **FR-036**: System MUST display a prominent "Simulated payment — do not enter real card details" notice on S21.
- **FR-037**: On successful order placement, system MUST: decrement stock per line item, create an order in Pending status, empty the cart, queue a confirmation email (T4), redirect to S23.
- **FR-038**: On placement failure (stock conflict), system MUST: create no order, change no stock, preserve the cart, and flag the problematic line.
- **FR-039**: Order numbers MUST follow the format TB-NNNNNN (zero-padded, monotonically increasing, never resets).
- **FR-040**: Order status flow MUST allow: Pending→Processing, Processing→Shipped, Shipped→Delivered, and from Pending/Processing/Shipped→Cancelled only; backward transitions and transitions out of Delivered or Cancelled are forbidden.
- **FR-041**: On admin cancellation of any order not in Delivered state, system MUST restore all line-item stock quantities and send a Cancelled status email (T5) to the customer.
- **FR-042**: Each order line item MUST snapshot: product name, SKU, unit price, and primary image reference at time of placement; later product edits or deletions MUST NOT alter past orders.
- **FR-043**: System MUST display the shipping address used in the order on both customer and admin order detail views; the address is snapshotted at placement.

**Email Notifications**
- **FR-044**: System MUST send 6 email notification types: T1 Registration OTP (at registration and on resend), T2 Login OTP (at each login), T3 Password reset link (Active accounts only), T4 Order confirmation (on placement), T5 Order status update (when admin changes status to Processing/Shipped/Delivered/Cancelled), T6 Contact submission (to admin).
- **FR-045**: System MUST NOT send SMS, marketing, newsletter, or push notifications.

**Admin: Products & Categories**
- **FR-046**: Admin MUST be able to create, edit, and delete products with all attributes per 10.3 and 8.2; deletion is a hard delete; SKU is never reusable after deletion.
- **FR-047**: Admin MUST be able to upload 1–5 images per product (JPG/JPEG/PNG/WebP, ≤2 MB each, ~800×800 recommended), set one as primary, reorder, add, and remove images.
- **FR-048**: Admin MUST be able to set a Featured flag and an Active flag on each product; ≥1 image is required for a product to be set Active.
- **FR-049**: Admin MUST be able to create, rename, and delete categories; deletion is blocked if the category contains products; category name 2–50 chars, unique case-insensitively.

**Admin: Orders**
- **FR-050**: Admin MUST be able to view all orders, filter by status (default: Pending), search by order number or customer name, and view a status-change timeline with timestamps.
- **FR-051**: Admin MUST advance order status one step forward at a time; each advance sends a T5 status email; admin MUST be able to cancel any order not in Delivered state (sends T5 Cancelled email, restores all line-item stock).

**Admin: Customers**
- **FR-052**: Admin MUST be able to view and search registered customers; the only edit available is toggling Active/Inactive status; no password access; no customer deletion.

**Admin: Contact Messages**
- **FR-053**: Admin MUST be able to view contact messages with unread rows highlighted; opening a message auto-marks it as read; admin can manually mark as unread; no in-app reply.

**Admin: Dashboard & Login**
- **FR-054**: Admin login MUST be at a separate non-public URL (`/admin`); no forgot-password or self-registration.
- **FR-055**: Admin dashboard MUST display: total products, total orders, orders-by-status breakdown, total registered customers, unread contact messages count, recent orders mini-list, recent messages mini-list.

**Cross-Cutting**
- **FR-056**: System MUST use CSRF protection on all forms.
- **FR-057**: System MUST use Blade `{{ }}` output escaping for all rendered content; `{!! !!}` MUST only be used for trusted internal content.
- **FR-058**: System MUST format all prices as `LKR 12,500` (no decimals, comma thousand-separators) everywhere in the application.
- **FR-059**: System MUST display customer-facing dates as `27 May 2026`; admin/order date+time as `27 May 2026, 14:35` (24-hour, Asia/Colombo timezone); timestamps stored in UTC.
- **FR-060**: System MUST include the storefront header, footer, and academic disclaimer "TechBits is an academic project. No real transactions are processed." on every storefront, auth, account, checkout, and static page.
- **FR-061**: Admin pages MUST use a separate admin layout (left side nav + top bar); no public footer.
- **FR-062**: Status badges MUST always be pill-shaped, colour-coded per the defined palette, and always include text (never colour alone).
- **FR-063**: Middleware MUST enforce role access on every protected route; admin routes MUST not be linked from public navigation.
- **FR-064**: System MUST implement destructive-action confirmation modals for: delete product, delete category, delete address, cancel order, deactivate customer.
- **FR-065**: System MUST implement empty-state messages with CTAs for: cart, orders, addresses, search results, category listings.
- **FR-066**: Pagination MUST be implemented: storefront grid 12/page, admin products 20/page, admin orders 20/page, customer orders 10/page, admin customers 20/page, admin messages 20/page.

### Key Entities

- **Guest** — unauthenticated visitor; session cart (≤7 days inactivity); no account.
- **Customer** — registered account with states: Unverified, Active, Inactive; has cart (persisted), order history, profile, and address book.
- **Admin** — back-office user; single flat privilege level; created manually; no customer features.
- **Product** — name, SKU (unique incl. deleted), category (one), short description, full description, specifications (key-value pairs), LKR price (integer), stock (integer ≥0), Featured flag, Active flag, images (1–5, one primary), system timestamps.
- **Category** — name (2–50, unique case-insensitively), product count.
- **Cart** — belongs to guest session or customer account; line items (product reference, quantity); max 50 unique lines.
- **CartLine** — product snapshot reference, quantity (capped at min(10, stock)).
- **Order** — order number (TB-NNNNNN), customer reference, status (Pending/Processing/Shipped/Delivered/Cancelled), line-item snapshots, shipping address snapshot, payment last 4 + cardholder + expiry, subtotal, shipping LKR 500, grand total, placed timestamp, status-change timeline.
- **OrderLine** — product name snapshot, SKU snapshot, unit price snapshot, primary image reference snapshot, quantity, line total.
- **ShippingAddress** — label (optional), recipient, line 1, line 2 (optional), city, district (one of 25 Sri Lankan districts), postal code (5 digits), contact number; belongs to customer; max 10 per customer; one default.
- **OTP** — 6-digit numeric code, associated account, expiry (10 min), attempt count; used for registration verification and every login.
- **PasswordResetToken** — secure random URL token, associated account (Active only), expiry (1 hour), single-use flag.
- **ContactMessage** — sender name, email, subject, message body, submitted timestamp, read flag; stored in DB and emailed to admin.
- **EmailNotification** — type (T1–T6), recipient, queued timestamp; sent via Laravel Mail facade.

---

## Success Criteria

### Measurable Outcomes

- **SC-001**: A guest can complete the full browse-to-cart journey (home → category → product → add to cart) in a single session without creating an account.
- **SC-002**: A new user can register, verify their email OTP, and complete a purchase end-to-end within a single session.
- **SC-003**: All 41 screens (S1–S30 and A1–A11) are implemented and accessible via their defined paths with no missing or extra screens.
- **SC-004**: Every destructive admin action (delete product, cancel order, deactivate customer) requires a confirmation modal before executing.
- **SC-005**: All 6 email notification types (T1–T6) are sent in response to their defined triggers.
- **SC-006**: An admin can manage the full product lifecycle (create, publish, edit, deactivate, delete) and the full order lifecycle (Pending→Processing→Shipped→Delivered; and cancellation with stock restoration) without error.
- **SC-007**: Cart merge on login correctly sums quantities and caps at min(10, stock) for every line, with a notice shown when excess is silently capped.
- **SC-008**: Account lockout activates after exactly 5 consecutive failed credential attempts; the lockout lasts 15 minutes; the generic "Invalid email or password" message is shown throughout.
- **SC-009**: No CVV is stored in the database; only card last 4, cardholder name, and expiry are stored.
- **SC-010**: All prices displayed in the application use the format `LKR 12,500` (no decimals, comma thousand-separators) with no exceptions.
- **SC-011**: The application is stable and reachable over the network so that security scanning tools can execute against it.
- **SC-012**: `SCOPE.md` and `UI.md` are detailed enough for independent AI coding and UI agents (per the project's own success criteria).

---

## Assumptions

- Users have modern browsers (current major Chrome, Firefox, Edge, Safari) and reliable internet connections.
- Users have valid, accessible email addresses (required for OTP, resets, and confirmations).
- All prices are LKR and tax-inclusive; no tax breakdown is displayed.
- All orders ship within Sri Lanka; a flat LKR 500 shipping fee applies to every order.
- A single outbound email service is available for transactional email; no third-party email packages are used unless instructed.
- Product images are stored and served by the application (storage/app/public with symlink); no CDN.
- The admin account is seeded manually at install (email e.g. admin@techbits.lk); the initial password is documented in handover and not committed to source control.
- No customer accounts are seeded; the demonstrator registers their own.
- Sample product data (~40 products across 10 categories) and placeholder images are acceptable for initial seed data.
- The application is an academic project; no real payment gateway is contacted; no real card data should be entered.
- Security scanning is a separate later phase; this build uses conventional standard Laravel implementation — no deliberate vulnerabilities, no extra hardening beyond a normal Laravel build.
- The session and database infrastructure supports database-backed sessions in production configuration.
- Unverified accounts are auto-deleted after 24 hours by a cleanup process; expired OTPs and reset tokens are deleted on next cleanup with no functional impact.
- Stock displayed to users reflects the database state at display time; there is no external warehouse sync.
- Admin accounts have no self-service password reset; they are managed manually.
- The security assessment of this application is a separate course phase and is not part of this build's deliverables.
