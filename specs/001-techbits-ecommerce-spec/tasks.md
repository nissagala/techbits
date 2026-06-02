---

description: "Task list for TechBits eCommerce Application"
---

# Tasks: TechBits eCommerce Application

**Input**: Design documents from `specs/001-techbits-ecommerce-spec/`

**Prerequisites**: plan.md ✅ | spec.md ✅ | research.md ✅ | data-model.md ✅ | contracts/ ✅

**Tests**: Not requested — manual testing against acceptance scenarios in spec.md is sufficient.

**Organization**: Phases 3–13 map to user stories; each story is independently testable after
the Phase 2 foundation is complete.

## Format: `[ID] [P?] [Story?] Description`

- **[P]**: Can run in parallel (different files, no dependencies on incomplete sibling tasks)
- **[Story]**: Which user story this task belongs to (US1–US11 per spec.md)
- Include exact file paths in descriptions

## Path Conventions

All paths are relative to the Laravel project root created in T001.

---

## Phase 1: Setup (Project Initialization)

**Purpose**: Create the Laravel project and configure the base environment.

- [x] T001 Install Laravel 11 project: run `composer create-project laravel/laravel techbits` then `cd techbits`
- [x] T002 Configure `.env`: set `APP_NAME=TechBits`, `APP_TIMEZONE=Asia/Colombo`, `DB_CONNECTION=mysql`, `DB_DATABASE=techbits`, `SESSION_DRIVER=database`, `SESSION_LIFETIME=30`, `MAIL_MAILER=log`, `MAIL_FROM_ADDRESS=noreply@techbits.lk`, `MAIL_FROM_NAME=TechBits`, `MAIL_ADMIN_ADDRESS=admin@techbits.lk`; add `'admin_address' => env('MAIL_ADMIN_ADDRESS', 'admin@techbits.lk')` to the `config/mail.php` array
- [x] T003 Create MariaDB database `techbits` with `utf8mb4_unicode_ci` collation and run `php artisan key:generate`
- [x] T004 Create `routes/admin.php` and register it with `/admin` prefix and `EnsureAdmin` middleware group in `bootstrap/app.php`
- [x] T005 Run `php artisan storage:link` to symlink `public/storage` → `storage/app/public`

**Checkpoint**: `php artisan serve` starts without errors; `storage/public` symlink exists.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Database schema, seed data, layouts, components, helpers, and middleware that ALL
user stories depend on. No user story work begins until this phase is complete.

**⚠️ CRITICAL**: Complete all Phase 2 tasks before any Phase 3+ work.

- [x] T006 Create `database/migrations/0001_create_users_table.php`: id, name, email (unique), phone, password, role enum(customer|admin), status enum(unverified|active|inactive), failed_login_attempts (tinyint default 0), locked_until (timestamp nullable), remember_token, timestamps
- [x] T007 [P] Create `database/migrations/0002_create_sessions_table.php` (run `php artisan session:table` or write manually: id varchar PK, user_id bigint nullable, ip_address, user_agent, payload longtext, last_activity int)
- [x] T008 [P] Create `database/migrations/0003_create_categories_table.php`: id, name varchar(50) unique, timestamps
- [x] T009 [P] Create `database/migrations/0004_create_products_table.php`: id, category_id FK, name varchar(200), sku varchar(50) unique, short_description varchar(200), description text, price int unsigned, stock int unsigned default 0, is_featured tinyint default 0, is_active tinyint default 1, deleted_at timestamp nullable (SoftDeletes), timestamps
- [x] T010 [P] Create `database/migrations/0005_create_product_images_table.php`: id, product_id FK cascade, path varchar(255), is_primary tinyint default 0, sort_order tinyint default 0, timestamps; and `0006_create_product_specs_table.php`: id, product_id FK cascade, spec_key varchar(50), spec_value varchar(200), sort_order tinyint default 0
- [x] T011 [P] Create `database/migrations/0007_create_cart_items_table.php`: id, user_id FK cascade, product_id FK cascade, quantity smallint unsigned, timestamps; unique index on (user_id, product_id)
- [x] T012 [P] Create `database/migrations/0008_create_addresses_table.php`: id, user_id FK cascade, label varchar(30) nullable, recipient varchar(100), line1 varchar(200), line2 varchar(200) nullable, city varchar(50), district varchar(50), postal_code varchar(5), phone varchar(20), is_default tinyint default 0, timestamps
- [x] T013 [P] Create `database/migrations/0009_create_orders_table.php`: id, user_id FK (no cascade), order_number varchar(12) unique, status enum(pending|processing|shipped|delivered|cancelled) default pending, subtotal int unsigned, shipping_fee int unsigned default 500, total int unsigned, shipping_address json, payment_cardholder varchar(100), payment_last4 varchar(4), payment_expiry varchar(7), placed_at timestamp, timestamps
- [x] T014 [P] Create `database/migrations/0010_create_order_items_table.php`: id, order_id FK cascade, product_id bigint nullable, product_name varchar(200), product_sku varchar(50), unit_price int unsigned, quantity smallint unsigned, line_total int unsigned, product_image_path varchar(255) nullable
- [x] T015 [P] Create `database/migrations/0011_create_order_status_logs_table.php`: id, order_id FK cascade, from_status varchar(20) nullable, to_status varchar(20), created_at timestamp; and `0012_create_otps_table.php`: id, user_id FK cascade, code varchar(64), purpose enum(registration|login), expires_at timestamp, attempts tinyint unsigned default 0, invalidated_at timestamp nullable, created_at timestamp
- [x] T016 [P] Create `database/migrations/0013_create_password_reset_tokens_table.php`: email varchar(254) PK, token varchar(255), created_at timestamp nullable; and `0014_create_contact_messages_table.php`: id, sender_name varchar(100), sender_email varchar(254), subject varchar(150), message text, is_read tinyint default 0, timestamps
- [x] T017 Run `php artisan migrate` — verify all 14 tables created without error
- [x] T018 Create `database/seeders/CategorySeeder.php` inserting the 10 categories from SCOPE.md §8.1: Keyboards, Mice, RAM, Storage, Monitors, Headsets & Audio, Webcams, Cables & Adapters, Laptop Accessories, Power & Charging
- [x] T019 [P] Create `database/seeders/AdminSeeder.php` inserting 1 user with role=admin, status=active, email=admin@techbits.lk, password=bcrypt(document in handover — do not commit plaintext)
- [x] T020 Create `database/seeders/ProductSeeder.php` with ~40 products across 10 categories (3–5 each): ≥5 is_featured=1; ≥3 stock=0; ≥3 stock between 1–5; ≥1 is_active=0; ≥5 with 2–3 images; price spread LKR 800–250,000; include matching `product_images` and `product_specs` rows
- [x] T021 Wire `database/seeders/DatabaseSeeder.php` to call CategorySeeder → AdminSeeder → ProductSeeder in order; run `php artisan db:seed`
- [x] T022 Create `resources/views/layouts/storefront.blade.php`: full-width centered container (max ~1200px); persistent header (logo → S1, search bar, category nav, cart badge, account area — use `Auth::check() && Auth::user()->role === 'customer'` to determine logged-in state: show "Login / Register" for guests AND for logged-in admins (admin is treated as guest on the storefront per constitution §5.4), show account-name dropdown with Account/My Orders/Logout only for role=customer); footer (store blurb, links to S24–S29, copyright); academic disclaimer "TechBits is an academic project. No real transactions are processed."; `@yield('content')` slot; include `resources/css/app.css`
- [x] T023 Create `resources/views/layouts/admin.blade.php`: left side nav (Dashboard, Products, Categories, Orders, Customers, Messages, Logout links); top bar ("Logged in as: [admin name]" + Logout); `@yield('content')` main content area; no public footer
- [x] T024 [P] Create `resources/views/components/product-card.blade.php`: square primary image, name (2-line truncate), LKR price (formatted), stock indicator badge, Featured amber badge when applicable; entire card wrapped in `<a>` → product detail URL
- [x] T025 [P] Create `resources/views/components/status-badge.blade.php`: pill-shaped span, colour-coded per UI.md §2.5 (Order: pending=grey, processing=blue, shipped=amber, delivered=green, cancelled=red; Account: unverified=amber, active=green, inactive=grey; Product: featured=amber, active=green, inactive=grey, out-of-stock=red), always includes text
- [x] T026 [P] Create `resources/views/components/qty-selector.blade.php`: − / numeric input / + buttons; min=1, max=`min(10, $stock)` prop; disabled state styling when stock=0
- [x] T027 [P] Create `resources/views/components/toast.blade.php`: top-right fixed position, auto-dismiss JS; success (green) and error (red) variants; driven by session flash
- [x] T028 [P] Create `resources/views/components/confirm-modal.blade.php`: centered overlay, title + message props, Cancel button + destructive-action button (red); triggered by JS
- [x] T029 [P] Create `resources/views/components/empty-state.blade.php`: centered icon placeholder + `$message` prop + `$cta` label + `$ctaUrl` prop
- [x] T030 Create `config/districts.php` returning array of 25 official Sri Lankan districts (Colombo, Gampaha, Kalutara, Kandy, Matale, Nuwara Eliya, Galle, Matara, Hambantota, Jaffna, Kilinochchi, Mannar, Vavuniya, Mullaitivu, Batticaloa, Ampara, Trincomalee, Kurunegala, Puttalam, Anuradhapura, Polonnaruwa, Badulla, Monaragala, Ratnapura, Kegalle)
- [x] T031 [P] Create `app/Helpers/LuhnValidator.php` with static `check(string $number): bool` method implementing the Luhn algorithm (double every second digit from right, subtract 9 if >9, sum all, valid if total mod 10 === 0)
- [x] T032 [P] Create global LKR currency formatter: `app/Helpers/CurrencyHelper.php` with `static format(int $amount): string` returning `'LKR ' . number_format($amount, 0)` (e.g. "LKR 12,500"); register as Blade directive `@lkr($amount)` in `AppServiceProvider`
- [x] T033 Create `app/Rules/SriLankanPhone.php` implementing `ValidationRule`: accept `0771234567`, `+94771234567`, `077 123 4567`; normalize to `+94XXXXXXXXX` (strip spaces/dashes, replace leading 0 with +94); fail with "Invalid Sri Lankan contact number format"; then create all four middleware classes and register in `bootstrap/app.php`: `app/Http/Middleware/EnsureCustomer.php` (authenticated + role=customer + status=active, else redirect with appropriate error); `EnsureAdmin.php` (authenticated + role=admin, else redirect to /admin); `EnsureOtpVerified.php` (apply to GET /login/verify and GET /register/verify — redirects to /login if session has no `pending_user_id`); `RedirectIfAuthenticated.php` (redirect customers to /account, admins to /admin/dashboard)
- [x] T034 Create `resources/css/app.css`: CSS custom properties for tech-blue (primary), amber/orange (accent), light-grey surfaces, dark-grey text; base layout styles (container, header, footer); status badge pill styles (.badge-pending, .badge-processing, etc.); LKR price emphasis weight; responsive grid (4-col desktop, 2-col tablet, 1-col mobile); button variants (primary filled blue, secondary outline, destructive red)

**Checkpoint**: `php artisan migrate:fresh --seed` completes without error; layouts render with header/footer in browser.

---

## Phase 3: User Story 1 — Guest Browse & Cart (Priority: P1) 🎯 MVP Start

**Goal**: A guest can browse the home page, navigate categories, search products, view product
detail, and manage a session-based shopping cart — all without an account.

**Independent Test**: Open the browser as a guest → home page shows featured products and
category links → click a category → product grid with filters appears → open a product →
stock indicator correct → Add to Cart → cart badge increments → cart page shows item with
LKR 500 shipping → adjust qty → qty capped at min(10, stock).

### Implementation for User Story 1

- [x] T035 [P] [US1] Create `app/Models/Category.php`: `$fillable = ['name']`; `hasMany(Product::class)`; scope `orderBy('name')`
- [x] T036 [P] [US1] Create `app/Models/Product.php`: `use SoftDeletes`; `$fillable` for all columns; `belongsTo(Category::class)`; `hasMany(ProductImage::class)`, `hasMany(ProductSpec::class)`, `hasMany(CartItem::class)`, `hasMany(OrderItem::class)`; scope `active()` (is_active=1, deleted_at null); scope `featured()` (is_featured=1)
- [x] T037 [P] [US1] Create `app/Models/ProductImage.php`, `app/Models/ProductSpec.php`, and `app/Models/CartItem.php`: ProductImage and ProductSpec each with `belongsTo(Product::class)` and appropriate `$fillable`; CartItem with `belongsTo(User::class)`, `belongsTo(Product::class)->withTrashed()` (to detect soft-deleted products in cart warnings), `$fillable = ['user_id', 'product_id', 'quantity']`
- [x] T038 [US1] Create `app/Http/Controllers/StorefrontController.php` with `home()`: query featured active products (most-recently-created featured first, cap at 8; fill remaining with most-recently-created active excluding already shown per §10.13); load all 10 categories; pass to `resources/views/storefront/home.blade.php`
- [x] T039 [US1] Create `app/Http/Controllers/ProductController.php` with `category(Category $category)` (active products in category, filter in_stock, sort newest|price_asc|price_desc|name_az, paginate 12), `search(Request $request)` (min 2 chars else return short-query view; partial case-insensitive name match; category+in_stock filters; sort; paginate 12; active products only), `show(Product $product)` (abort 404 if inactive or soft-deleted)
- [x] T040 [US1] Create `app/Http/Controllers/CartController.php`: `show()` (load session cart for guest / DB cart for customer; for each line: check current stock — flag zero-stock or inactive as blocked, flag partial-mismatch as warning + cap qty; compute subtotal + LKR 500 shipping + total); `add(Request $request)` (validate product_id active+in-stock, qty ≥1; upsert session or DB; cap at min(10, stock); return JSON {success, cart_count, message}); `update(Request $request, $item)` (validate qty; cap at min(10, stock); update); `remove($item)` (delete line from session or DB)
- [x] T041 [US1] Register storefront and cart routes in `routes/web.php`: `GET /` → StorefrontController@home; `GET /category/{category}` → ProductController@category; `GET /search` → ProductController@search; `GET /product/{product}` → ProductController@show; `GET /cart` → CartController@show; `POST /cart` → CartController@add; `PATCH /cart/{item}` → CartController@update (auth check for ownership); `DELETE /cart/{item}` → CartController@remove
- [x] T042 [P] [US1] Create `resources/views/storefront/home.blade.php` (S1): extends storefront layout; hero banner with tagline "Computer accessories, delivered across Sri Lanka." + CTA button; "Featured Products" section with `<x-product-card>` grid (up to 8); "Shop by Category" section with 10 category links
- [x] T043 [P] [US1] Create `resources/views/storefront/category.blade.php` (S2): extends storefront layout; breadcrumb (Home › Category Name); category name heading; filter bar (in-stock toggle, sort select); product grid (12/page) using `<x-product-card>`; `{{ $products->links() }}` pagination; `<x-empty-state>` when no products
- [x] T044 [P] [US1] Create `resources/views/storefront/search.blade.php` (S3): extends storefront layout; search bar pre-filled with query; "Showing results for: [query]" + result count; filter bar (category dropdown + in-stock toggle) + sort; product grid; pagination; short-query hint div (shown when q < 2 chars); `<x-empty-state>` for no results
- [x] T045 [P] [US1] Create `resources/views/storefront/product.blade.php` (S4): extends storefront layout; breadcrumb; two-column desktop layout — left: large primary image + thumbnail strip (click to swap main); right: name, LKR price (`@lkr`), stock indicator (`<x-status-badge>`), short description, `<x-qty-selector>`, Add to Cart button (disabled + "Out of stock" label when stock=0); below: Full Description section (plain text, preserve paragraph breaks); Specifications two-column table; `<script>` for thumbnail swap only
- [x] T046 [US1] Create `resources/views/storefront/cart.blade.php` (S5): extends storefront layout; line-item table (image, name, unit price, `<x-qty-selector>`, line total, remove button); per-line warning for zero-stock/inactive (red) and partial-stock mismatch (amber, qty capped to current stock); order summary (subtotal, "Shipping: LKR 500", grand total); "Continue shopping" link + "Proceed to checkout" primary button (disabled if any blocked lines); `<x-empty-state>` when cart empty

**Checkpoint**: US1 fully functional — guest can browse, search, filter, view products, add to cart, and see cart with correct LKR totals and stock warnings.

---

## Phase 4: User Story 2 — Registration, OTP Verification, and Login (Priority: P1)

**Goal**: A guest can register an account (OTP-verified → Active), then log in using
email + password + OTP at every subsequent login. Account lockout enforces after 5 wrong
credential attempts.

**Independent Test**: Register → receive OTP in log file → verify → auto-logged in. Log out → log back in → OTP required again. Enter wrong password 5 times → account locked → generic error shown.

### Implementation for User Story 2

- [x] T047 [US2] Create `app/Models/User.php`: extends `Authenticatable`; `$fillable` all columns; `$hidden = ['password', 'remember_token']`; `$casts = ['role' => 'string', 'status' => 'string', 'locked_until' => 'datetime']`; `hasMany(Otp::class)`, `hasMany(CartItem::class)`, `hasMany(Address::class)`, `hasMany(Order::class)`, `isLocked()` method (checks `locked_until > now()`); `isActive()` helper
- [x] T048 [US2] Create `app/Models/Otp.php`: `$fillable = ['user_id', 'code', 'purpose', 'expires_at', 'attempts', 'invalidated_at']`; `belongsTo(User::class)`; `isValid()` (not invalidated, not expired); `incrementAttempts()` (increment + invalidate at 5)
- [x] T049 [P] [US2] Create `app/Mail/RegistrationOtpMail.php` (T1) and `resources/views/emails/registration-otp.blade.php`: subject "TechBits — Verify your email"; greeting + 6-digit OTP in large text + "This code expires in 10 minutes" + "If you didn't create an account, ignore this email"; LKR/Colombo formatting not applicable; TechBits sign-off
- [x] T050 [P] [US2] Create `app/Mail/LoginOtpMail.php` (T2) and `resources/views/emails/login-otp.blade.php`: subject "TechBits — Your login code"; login-framed message + 6-digit code + "If this wasn't you, change your password immediately"
- [x] T051 [US2] Create `app/Http/Requests/Auth/RegisterRequest.php`: name (required, min:2, max:100, regex letters/spaces/dots/hyphens), email (required, email:rfc, max:254, unique:users,email case-insensitively), phone (required, custom SriLankanPhone rule normalizing to +94XXXXXXXXX), password (required, min:8, max:64, regex has letter, regex has digit), password_confirmation (required, same:password)
- [x] T052 [P] [US2] Create `app/Http/Requests/Auth/LoginRequest.php` (email required, password required) and `app/Http/Requests/Auth/OtpVerifyRequest.php` (otp required, digits:6)
- [x] T053 [US2] Create `app/Http/Controllers/Auth/RegisterController.php`: `show()` → S6 view; `submit(RegisterRequest $request)`: check if email belongs to Unverified+expired-OTP account (replace it), else reject if active/inactive; create Unverified User; generate 6-digit random code; store hashed OTP in `otps` (purpose=registration, expires_at=+10min); send `RegistrationOtpMail`; store `pending_user_id` in session; redirect → GET /register/verify
- [x] T054 [US2] Create `app/Http/Controllers/Auth/OtpController.php`: `showRegistration()` / `verifyRegistration(OtpVerifyRequest)` (load OTP for pending_user_id+registration, compare hash, increment attempts, invalidate at 5, on success: mark user Active + Auth::login + clear session + merge guest cart + redirect); `resendRegistration()` (enforce 60s cooldown, invalidate old, create new, send T1); `showLogin()` / `verifyLogin(OtpVerifyRequest)` (same logic for login purpose; on success: Auth::login with $remember flag from session, clear session, cart merge, redirect to stored URL or /); `resendLogin()` (60s cooldown, invalidate old, create new, send T2)
- [x] T055 [US2] Create `app/Http/Controllers/Auth/LoginController.php`: `show()` → S8 view; `submit(LoginRequest)`: if account locked → generic error; attempt credentials (Auth::attempt with false for remember); if wrong → increment failed_login_attempts, lock at 5 (locked_until=+15min) → generic error; if Unverified with correct credentials → J8.1: invalidate any old registration OTPs, create new, send T1, store pending_user_id in session, redirect → /register/verify; if correct + Active: reset failed_login_attempts to 0, generate login OTP, send T2, store pending_user_id + otp_remember + redirect_after_login in session, redirect → /login/verify
- [x] T056 [P] [US2] Create `app/Http/Controllers/Auth/LogoutController.php`: `logout()` → Auth::logout(), session invalidate, regenerate token, redirect to /
- [x] T057 [US2] Register auth routes in `routes/web.php` in two separate groups: (1) guest-only under `RedirectIfAuthenticated` middleware: `GET /register`, `POST /register`, `GET /register/verify` (+ `EnsureOtpVerified`), `POST /register/verify`, `POST /register/resend`, `GET /login`, `POST /login`, `GET /login/verify` (+ `EnsureOtpVerified`), `POST /login/verify`, `POST /login/resend`; (2) authenticated-only under `EnsureCustomer` middleware: `POST /logout` — do NOT place `/logout` in the guest-only group or RedirectIfAuthenticated will prevent logged-in users from reaching it
- [x] T058 [P] [US2] Create `resources/views/auth/register.blade.php` (S6): centered card; all 5 fields with limit helper text; per-field inline errors; primary "Create account" button; "Already have an account? Log in" link
- [x] T059 [P] [US2] Create `resources/views/auth/otp-registration.blade.php` (S7): centered card; "We sent a code to j•••@example.com" (masked email); 6-digit OTP input; Verify button; "Resend code" link (disabled during 60s countdown — JS countdown timer); "Code expires in 10 minutes" note; error states (wrong code, too many attempts, expired)
- [x] T060 [P] [US2] Create `resources/views/auth/login.blade.php` (S8): centered card; email + password fields; "Remember me" checkbox; Log in button; "Forgot password?" link; "Don't have an account? Register" link; generic error display
- [x] T061 [US2] Create `resources/views/auth/otp-login.blade.php` (S9): as S7 but framed "Confirm it's you"; same states; note text does not imply session persistence (closing this screen means restarting login)

**Checkpoint**: US2 fully functional — registration → OTP → Active → auto-login; logout; login → OTP → session; lockout after 5 wrong passwords.

---

## Phase 5: User Story 11 — Admin Dashboard & Login (Priority: P1 for Admin)

**Goal**: Admin accesses `/admin`, logs in with email + password (no OTP), and sees the
dashboard with live counters.

**Independent Test**: Navigate to /admin → login form appears → enter admin@techbits.lk + password → dashboard shows correct product/order/customer/message counts.

### Implementation for User Story 11

- [x] T062 [US11] Create `app/Http/Controllers/Admin/AuthController.php`: `showLogin()` → A1 view (no `RedirectIfAuthenticated` for customers — admin has separate session guard or role check); `login(AdminLoginRequest $request)`: find user by email with role=admin; verify password; if valid: Auth::login($user, false) (no remember-me), redirect → /admin/dashboard; else: "Invalid email or password"; `logout()`: Auth::logout(), redirect → /admin
- [x] T063 [P] [US11] Create `app/Http/Requests/Admin/AdminLoginRequest.php` (email required, password required)
- [x] T064 [US11] Create `app/Http/Controllers/Admin/DashboardController.php` `show()`: count active products; count all orders; count orders grouped by status; count users with role=customer; count contact_messages where is_read=0; latest 5 orders (number, customer name, date, status); latest 5 messages (sender, subject, date, is_read); pass to A2 view
- [x] T065 [US11] Register in `routes/admin.php`: `GET /admin` → Admin\AuthController@showLogin; `POST /admin/login` → Admin\AuthController@login; `POST /admin/logout` → Admin\AuthController@logout; `GET /admin/dashboard` → Admin\DashboardController@show (EnsureAdmin)
- [x] T066 [P] [US11] Create `resources/views/admin/auth/login.blade.php` (A1): admin layout without nav (or minimal layout); centered card; email + password only; Log in button; no Forgot Password, no Register links
- [x] T067 [US11] Create `resources/views/admin/dashboard.blade.php` (A2): uses admin layout; 5 counter cards (total products, total orders, orders by status breakdown, total customers, unread messages); recent orders mini-table (number, customer, date, status badge); recent messages mini-table (sender, subject, date, read indicator)

**Checkpoint**: Admin can log in, see live counters, and log out.

---

## Phase 6: User Story 8 — Admin: Product & Category Management (Priority: P1 for Admin)

**Goal**: Admin can create, edit, delete products (with images, specs, toggles) and manage categories. Products appear/disappear from storefront based on active flag.

**Independent Test**: Admin creates a product with 2 images, sets is_featured=true, is_active=true → appears on storefront home page → admin deactivates it → disappears from storefront → admin deletes it → SKU cannot be reused in a new product.

### Implementation for User Story 8

- [x] T068 [US8] Create `app/Http/Controllers/Admin/ProductController.php`: `index()` (search by name, filter by category, paginate 20; use `withTrashed` scoped to show only non-deleted to admin); `create()` → A4 form; `store(ProductRequest)` (validate, create product, save images to `storage/app/public/products/{id}/`, create ProductImage rows setting primary, save spec rows, redirect with success); `edit(Product $product)` → A5 form pre-filled; `update(ProductRequest, Product)` (update, handle image additions/removals/primary change, replace all spec rows); `destroy(Product)` (soft delete, redirect)
- [x] T069 [US8] Create `app/Http/Controllers/Admin/CategoryController.php`: `index()` → A6 view with product counts; `store(CategoryRequest)` (case-insensitive unique check, create); `update(CategoryRequest, Category)` (case-insensitive unique ignore-self check, update name); `destroy(Category)` (abort with 422 if `$category->products()->exists()`, else delete)
- [x] T070 [US8] Create `app/Http/Requests/Admin/ProductRequest.php`: all product field rules from `contracts/admin.md`; custom SKU uniqueness rule (for create: check non-deleted rows; for update: ignore current product ID among non-deleted); custom image rules (mimes, max:2048); specs array max 30 pairs; `is_active` true requires at least 1 existing or new image
- [x] T071 [P] [US8] Create `app/Http/Requests/Admin/CategoryRequest.php` (name 2–50, case-insensitive unique via `Rule::unique('categories', 'name')->ignore($this->category)->whereNull('deleted_at')`)
- [x] T072 [US8] Register admin product and category routes in `routes/admin.php` (all under EnsureAdmin): `GET/POST /admin/products`, `GET /admin/products/create`, `GET/PUT/DELETE /admin/products/{product}`, `GET /admin/products/{product}/edit`, `DELETE /admin/products/{product}/images/{image}`; `GET/POST /admin/categories`, `PUT/DELETE /admin/categories/{category}`
- [x] T073 [US8] Create `resources/views/admin/products/index.blade.php` (A3): admin layout; "Add product" button (→ /admin/products/create); filter bar (category dropdown, name search input); data table (image thumb, name, SKU, category, LKR price via `@lkr`, stock, Featured badge, Active toggle form, Edit link, Delete button + `<x-confirm-modal>`); `{{ $products->links() }}` pagination; empty state
- [x] T074 [US8] Create `resources/views/admin/products/form.blade.php` (A4/A5 shared): admin layout; name, SKU, category dropdown, short description, full description textarea; specifications section (repeating key-value rows with "Add spec row" button + JS to append/remove rows); price (LKR integer), stock (integer); Featured + Active toggle checkboxes; image upload area (multi-file input + preview list with "Set as primary" radio + remove button + existing image thumbnails on edit); Save button + Cancel link; Delete product button (only on edit, triggers `<x-confirm-modal>`)
- [x] T075 [US8] Create `resources/views/admin/categories/index.blade.php` (A6): admin layout; "Add category" inline form (name input + Add button); categories list (name, product count, inline rename form, Delete button — disabled with tooltip when products > 0, else triggers `<x-confirm-modal>`)

**Checkpoint**: Admin can create/edit/delete products and categories; storefront reflects active/inactive state; SKU reuse blocked after soft-delete.

---

## Phase 7: User Story 4 — Checkout & Order Placement (Priority: P1)

**Goal**: Authenticated customer completes 3-step checkout (address → simulated payment →
review → place order), receives confirmation screen and email, stock decremented.

**Independent Test**: Log in → add product to cart → checkout → select default address → fill card form (any valid Luhn number) → review page shows correct totals → Place Order → TB-NNNNNN order created, stock decremented, cart empty, S23 confirmation shown, T4 email in log.

### Implementation for User Story 4

- [x] T076 [US4] Create `app/Models/Address.php`: `$fillable` all columns; `belongsTo(User::class)`; `formattedDistrict()` helper
- [x] T077 [P] [US4] Create `app/Models/Order.php`: `$fillable` all columns; `$casts = ['shipping_address' => 'array', 'placed_at' => 'datetime']`; `belongsTo(User::class)`; `hasMany(OrderItem::class)`, `hasMany(OrderStatusLog::class)`; `setOrderNumber()` method (called post-creation: `'TB-' . str_pad($this->id, 6, '0', STR_PAD_LEFT)`)
- [x] T078 [P] [US4] Create `app/Models/OrderItem.php` (`belongsTo(Order::class)`, `belongsTo(Product::class)->withTrashed()`) and `app/Models/OrderStatusLog.php` (`belongsTo(Order::class)`)
- [x] T079 [US4] Create `app/Mail/OrderConfirmationMail.php` (T4) and `resources/views/emails/order-confirmation.blade.php`: subject "TechBits — Order confirmation (TB-NNNNNN)"; order number, items table (name/qty/unit/line totals in LKR), subtotal/shipping LKR 500/grand total, shipping address, card ending in last 4, "3–7 working days" delivery window, "View in your account" link
- [x] T080 [US4] Create `app/Http/Requests/Checkout/PaymentRequest.php`: cardholder (required, min:2, max:100); card_number (required, custom rule: strip spaces, digits 13–19, `LuhnValidator::check()`); expiry (required, regex MM/YY, custom not-in-past rule); cvv (required, digits_between:3,4)
- [x] T081 [US4] Create `app/Http/Requests/Account/AddressRequest.php`: all address fields per `contracts/account.md`; district must be in `config('districts')`; postal_code must be exactly 5 digits; phone custom SriLankanPhone rule
- [x] T082 [US4] Create `app/Http/Controllers/CheckoutController.php`: `shipping()` (S20: load saved addresses, pre-select default; redirect to /cart if cart empty); `saveShipping(Request)` (validate address_id OR new_address fields per `contracts/checkout.md`; if new_address: check 10-limit, save to `addresses` table, use new ID; store `checkout.address_id` in session; redirect → /checkout/payment); `payment()` (S21); `savePayment(PaymentRequest)` (store `checkout.payment = {last4, cardholder, expiry}` in session — no CVV; redirect → /checkout/review); `review()` (S22: load cart, address from session, payment from session; compute totals); `place(Request)` (precondition checks: cart not empty, all lines active+sufficient-stock, address valid, payment in session; DB transaction: decrement stock, create order, set order_number, create order_items with snapshots, create order_status_log from=null to=pending, snapshot shipping_address as JSON, clear cart, clear checkout session, queue Mail::to(user)->send(new OrderConfirmationMail); redirect → /checkout/confirmation/{order}; on stock failure: flash error, redirect → /cart); `confirmation(Order $order)` (authorize user owns order; S23 view)
- [x] T083 [US4] Register checkout routes in `routes/web.php` (EnsureCustomer): `GET /checkout/shipping`, `POST /checkout/shipping`, `GET /checkout/payment`, `POST /checkout/payment`, `GET /checkout/review`, `POST /checkout/place`, `GET /checkout/confirmation/{order}`
- [x] T084 [P] [US4] Create `resources/views/checkout/shipping.blade.php` (S20): step indicator (1 of 3); selectable saved-address radio cards (default pre-selected); "Add new address" accordion/inline section with full AddressRequest form fields; mini order summary (item count, subtotal); Continue to payment + Back to cart buttons
- [x] T085 [P] [US4] Create `resources/views/checkout/payment.blade.php` (S21): step indicator (2 of 3); prominent amber/red notice box "Simulated payment — do not enter real card details. No real payment is processed."; cardholder name, card number, expiry MM/YY, CVV fields; format-validation error display; mini summary; Continue to review + Back buttons
- [x] T086 [P] [US4] Create `resources/views/checkout/review.blade.php` (S22): step indicator (3 of 3); summary panels — shipping address (from session), payment (card ending in last 4 only), line items table (name/qty/unit price/line total), totals (subtotal, "Shipping: LKR 500", grand total in LKR); primary "Place order" button + Back link
- [x] T087 [US4] Create `resources/views/checkout/confirmation.blade.php` (S23): large success indicator; order number in prominent text; ordered items summary; total paid in LKR; shipping address; "A confirmation email has been sent to [email]."; "View order details" button (→ /account/orders/{order}) + "Continue shopping" link (→ /)

**Checkpoint**: US4 complete — full 3-step checkout works, order number TB-NNNNNN created, stock decremented, cart emptied, T4 email in log, S23 shown.

---

## Phase 8: User Story 9 — Admin: Order Management (Priority: P1 for Admin)

**Goal**: Admin can view all orders, advance status one step, and cancel orders with automatic stock restoration.

**Independent Test**: Place an order as a customer → admin opens it (A8) → advance to Processing → status update email (T5) in log → cancel it → stock restored → customer sees Cancelled in My Orders.

### Implementation for User Story 9

- [x] T088 [US9] Create `app/Mail/OrderStatusUpdateMail.php` (T5) and `resources/views/emails/order-status.blade.php`: subject "TechBits — Your order TB-NNNNNN is now [Status]"; order number, new status, status-specific context message (Processing: "We're preparing your order"; Shipped: "Your order is on its way"; Delivered: "Your order has been delivered"; Cancelled: "Your order has been cancelled and stock restored")
- [x] T089 [US9] Create `app/Http/Controllers/Admin/OrderController.php`: `index()` (A7: filter by status defaulting to pending, search by order_number or customer name, date range optional, paginate 20); `show(Order $order)` (A8: full detail with timeline); `advance(Order $order)` (validate allowed transition per status flow; update status; create OrderStatusLog entry; send T5 email; redirect back with success); `cancel(Order $order)` (guard: not in delivered state; DB transaction: set status=cancelled, restore each order_item stock via `Product::withTrashed()->find($item->product_id)?->increment('stock', $item->quantity)`, create log entry, send T5 Cancelled email; redirect)
- [x] T090 [US9] Register admin order routes in `routes/admin.php` (EnsureAdmin): `GET /admin/orders`, `GET /admin/orders/{order}`, `POST /admin/orders/{order}/advance`, `POST /admin/orders/{order}/cancel`
- [x] T091 [P] [US9] Create `resources/views/admin/orders/index.blade.php` (A7): admin layout; filter bar (status dropdown defaulting to "Pending", search input, optional date range); orders table (number, customer name, date, LKR total, status badge, View link); pagination (20/page); empty state
- [x] T092 [US9] Create `resources/views/admin/orders/show.blade.php` (A8): admin layout; order header (number, date, customer name+email+contact, status badge); line items table (snapshot data — name, SKU, unit price, qty, line total); shipping address; payment (card ending in last 4); totals (subtotal, LKR 500 shipping, grand total); status update control (next-status action button — disabled in delivered/cancelled state); Cancel order button (disabled in delivered state) + `<x-confirm-modal>` with "Stock will be restored" message; status timeline (ordered list of OrderStatusLog entries with from→to status and timestamp in "27 May 2026, 14:35" format)

**Checkpoint**: Admin order lifecycle fully functional — Pending→Processing→Shipped→Delivered and cancellation with stock restore all work.

---

## Phase 9: User Story 5 — Customer Account & Address Management (Priority: P2)

**Goal**: Authenticated customer can view/edit their profile, change password, and manage up to 10 shipping addresses with automatic default management.

**Independent Test**: Log in → My Profile → update name → saved; Change Password → update → stays logged in; Add Address → becomes default; Add second address → set as default → first loses default; Delete default → remaining address auto-promoted.

### Implementation for User Story 5

- [x] T093 [US5] Create `app/Http/Controllers/Account/DashboardController.php` `show()` → S13 view (welcome name, nav cards to profile/addresses/orders/change-password + logout link)
- [x] T094 [P] [US5] Create `app/Http/Controllers/Account/ProfileController.php`: `show()` → S14 view; `update(UpdateProfileRequest)` → update name+phone, flash success, redirect back
- [x] T095 [P] [US5] Create `app/Http/Controllers/Account/PasswordController.php`: `show()` → S15 view; `update(ChangePasswordRequest)` → `Hash::check` current password, update password hash, flash success, redirect back (stay logged in)
- [x] T096 [US5] Create `app/Http/Controllers/Account/AddressController.php`: `index()` → S16 (user's addresses, newest first); `create()` → S17 form; `store(AddressRequest)` (check ≤10 limit; if first address: is_default=true; if is_default requested: clear others; save; redirect S16 with success); `edit(Address $address)` → S17 form pre-filled (authorize owns); `update(AddressRequest, Address)` (authorize; update; if is_default: clear others then set; redirect); `destroy(Address $address)` (authorize; if is_default: promote most-recently-added remaining; delete; redirect); `setDefault(Address $address)` (authorize; clear all user defaults; set this one; redirect)
- [x] T097 [P] [US5] Create `app/Http/Requests/Account/UpdateProfileRequest.php` (name 2–100, regex letters/spaces/dots/hyphens; phone custom SriLankanPhone) and `app/Http/Requests/Account/ChangePasswordRequest.php` (current_password with `Hash::check`; password min:8, max:64, regex letter+digit; password_confirmation same)
- [x] T098 [US5] Register customer account routes in `routes/web.php` (EnsureCustomer): `GET /account`, `GET/POST /account/profile`, `GET/POST /account/password`, `GET /account/addresses`, `GET /account/addresses/create`, `POST /account/addresses`, `GET /account/addresses/{address}/edit`, `PUT /account/addresses/{address}`, `DELETE /account/addresses/{address}`, `POST /account/addresses/{address}/default`
- [x] T099 [P] [US5] Create `resources/views/account/dashboard.blade.php` (S13): storefront layout; "Welcome, [name]" heading; nav cards linking to My Profile, My Addresses, My Orders, Change Password; Logout button
- [x] T100 [P] [US5] Create `resources/views/account/profile.blade.php` (S14): name (editable), email (read-only input with "Email cannot be changed" note), phone (editable); Save changes button; success/error banner
- [x] T101 [P] [US5] Create `resources/views/account/password.blade.php` (S15): current password, new password (8–64 limit helper), confirm new password; Update password button; success/error
- [x] T102 [US5] Create `resources/views/account/addresses/index.blade.php` (S16): address cards (label, recipient, full address, contact number, "Default" badge); per-card Edit / Delete (`<x-confirm-modal>`) / Set as Default buttons; "Add new address" button; `<x-empty-state>` when none
- [x] T103 [US5] Create `resources/views/account/addresses/form.blade.php` (S17): all address fields, district dropdown (from `config('districts')`), "Set as default" checkbox; Save address + Cancel buttons; pre-populated on edit

**Checkpoint**: Customer profile, password change, and full address CRUD with default management all work correctly.

---

## Phase 10: User Story 3 — Password Reset (Priority: P2)

**Goal**: A guest or customer can request a password reset email and set a new password via a single-use time-limited link. Generic response regardless of email validity.

**Independent Test**: Submit any email → generic confirmation shown; submit Active account email → reset link appears in log → click link → set new password → redirected to login → log in with new password.

### Implementation for User Story 3

- [x] T104 [US3] Create `app/Mail/PasswordResetMail.php` (T3) and `resources/views/emails/password-reset.blade.php`: subject "TechBits — Password reset request"; reset link button + "This link expires in 1 hour" + "If you didn't request this, ignore this email"; TechBits sign-off
- [x] T105 [US3] Create `app/Http/Controllers/Auth/ForgotPasswordController.php`: `show()` → S10; `submit(ForgotPasswordRequest)`: always redirect → /forgot-password/sent; if email belongs to Active user: generate secure random token (Str::random(64)), hash it, upsert `password_reset_tokens` (replaces old token for same email), send `PasswordResetMail` with signed URL `/password/reset/{token}?email={email}`
- [x] T106 [US3] Create `app/Http/Controllers/Auth/ResetPasswordController.php`: `show(Request $request, string $token)` → S12 view passing token+email; `submit(ResetPasswordRequest, string $token)`: look up token by email in `password_reset_tokens`; verify `created_at + 3600s > now()`; verify `Hash::check($token, $record->token)`; verify user is Active; update user password (bcrypt); delete token record; redirect → /login with "Password reset successfully. Please log in." flash
- [x] T107 [P] [US3] Create `app/Http/Requests/Auth/ForgotPasswordRequest.php` (email required, email:rfc) and `app/Http/Requests/Auth/ResetPasswordRequest.php` (password min:8, max:64, regex letter+digit; password_confirmation same)
- [x] T108 [US3] Register forgot-password routes in `routes/web.php` (guest-only under RedirectIfAuthenticated): `GET /forgot-password`, `POST /forgot-password`, `GET /forgot-password/sent`, `GET /password/reset/{token}`, `POST /password/reset`
- [x] T109 [P] [US3] Create `resources/views/auth/forgot-password.blade.php` (S10): centered card; email input; Send reset link button; Back to login link
- [x] T110 [P] [US3] Create `resources/views/auth/forgot-password-sent.blade.php` (S11): centered card; "If this email exists, a reset link has been sent." message; Back to login link
- [x] T111 [US3] Create `resources/views/auth/reset-password.blade.php` (S12): centered card; new password + confirm fields (8–64 limit helper); Set password button; invalid/expired token error state with "Request a new reset link" option

**Checkpoint**: Full password reset flow works — generic confirmation, reset email in log, set-new-password form, redirect to login.

---

## Phase 11: User Story 6 — Order History (Priority: P2)

**Goal**: Authenticated customer can view their order history (newest first, paginated) and open the read-only detail view for any order.

**Independent Test**: Place 2+ orders → My Orders shows them newest-first → open one → snapshot data correct (name/SKU/unit price unchanged even if product was since edited) → no cancel/edit action present.

### Implementation for User Story 6

- [x] T112 [US6] Create `app/Http/Controllers/Account/OrderController.php`: `index()` (Auth::user()->orders()->latest('placed_at')->paginate(10) → S18); `show(Order $order)` (authorize `$order->user_id === Auth::id()`; load order_items, shipping_address JSON, payment info → S19 read-only)
- [x] T113 [US6] Register customer order routes in `routes/web.php` (EnsureCustomer): `GET /account/orders`, `GET /account/orders/{order}`
- [x] T114 [P] [US6] Create `resources/views/account/orders/index.blade.php` (S18): table (order number, date formatted "27 May 2026", LKR total, status badge); row clickable → S19; `{{ $orders->links() }}` (10/page); `<x-empty-state>` "You haven't placed any orders yet."
- [x] T115 [US6] Create `resources/views/account/orders/show.blade.php` (S19): order number + placed date + status badge; line items table (snapshot: image, name, SKU, unit price `@lkr`, qty, line total `@lkr`); "Shipped to" address block (from shipping_address JSON snapshot); "Payment" block (card ending in [last4], [cardholder], exp [expiry]); totals (subtotal, "Shipping: LKR 500", grand total); no edit/cancel controls

**Checkpoint**: Customer sees all their orders; snapshot data is frozen and reflects time-of-order values.

---

## Phase 12: User Story 10 — Admin: Customer & Message Management (Priority: P2 for Admin)

**Goal**: Admin can view/search customers, toggle Active↔Inactive status, and read/manage contact messages.

**Independent Test**: Deactivate a customer → they cannot log in (generic error) → reactivate → they log in normally. Submit contact form as guest → unread indicator in admin message list → open → auto-marked read → mark unread.

### Implementation for User Story 10

- [x] T116 [US10] Create `app/Http/Controllers/Admin/CustomerController.php`: `index()` (search by name/email, filter by status unverified|active|inactive, paginate 20; only role=customer users); `toggle(User $user)` (authorize role=customer; toggle active↔inactive; flash success; redirect back)
- [x] T117 [US10] Create `app/Http/Controllers/Admin/MessageController.php`: `index()` (paginate 20, unread first within sort); `show(ContactMessage $message)` (set is_read=true; render A11 view); `markUnread(ContactMessage $message)` (set is_read=false; redirect back)
- [x] T118 [US10] Register admin customer and message routes in `routes/admin.php` (EnsureAdmin): `GET /admin/customers`, `POST /admin/customers/{user}/toggle`, `GET /admin/messages`, `GET /admin/messages/{message}`, `POST /admin/messages/{message}/unread`
- [x] T119 [P] [US10] Create `resources/views/admin/customers/index.blade.php` (A9): admin layout; search input + status filter dropdown; table (name, email, contact, registration date formatted, status badge, Activate/Deactivate toggle button); deactivate uses `<x-confirm-modal>`; pagination (20/page); empty state
- [x] T120 [P] [US10] Create `resources/views/admin/messages/index.blade.php` (A10): admin layout; table (sender name, email, subject, date formatted, unread indicator); unread rows have distinct background highlight; each row View link → A11; pagination (20/page)
- [x] T121 [US10] Create `resources/views/admin/messages/show.blade.php` (A11): admin layout; sender name + email; subject; submitted date+time in "27 May 2026, 14:35" format; message body in pre-formatted block; "Mark as unread" button (POST /admin/messages/{id}/unread); "Back to messages" link; note "Reply externally via email" (no in-app reply)

**Checkpoint**: Admin customer toggle works; deactivated customer cannot log in; contact message read/unread state correct.

---

## Phase 13: User Story 7 — Static Pages & Contact Form (Priority: P3)

**Goal**: All users can read 5 static pages and submit the Contact Us form (stored in DB, emailed to admin, no rate limiting).

**Independent Test**: Guest navigates to /contact → fills form → submits → "Message sent" shown, form cleared, T6 in admin email log, message stored in DB → admin sees it in A10 → opens A11 → auto-marked read.

### Implementation for User Story 7

- [x] T122 [US7] Add static page methods to `app/Http/Controllers/StorefrontController.php`: `about()`, `terms()`, `privacy()`, `shippingInfo()`, `faq()` — each returns corresponding view with placeholder content from SCOPE.md §11.5
- [x] T123 [US7] Create `app/Models/ContactMessage.php` with `$fillable = ['sender_name', 'sender_email', 'subject', 'message', 'is_read']`; then create `app/Http/Controllers/ContactController.php`: `show()` → S29 view (if Auth::check(): pass user name+email as read-only pre-fill); `submit(ContactRequest $request)`: create `ContactMessage`; send `Mail::to(config('mail.admin_address'))->send(new ContactSubmissionMail($message))`; redirect back with success flash + form-cleared flag
- [x] T124 [US7] Create `app/Mail/ContactSubmissionMail.php` (T6) and `resources/views/emails/contact-submission.blade.php`: to admin; subject "TechBits Contact — [subject]"; sender name + email; subject; message body; submitted timestamp in "27 May 2026, 14:35" Asia/Colombo format
- [x] T125 [P] [US7] Create `app/Http/Requests/ContactRequest.php`: name (required, min:2, max:100), email (required, email:rfc), subject (required, min:3, max:150), message (required, min:10, max:2000); **no rate limiting** (intentional per §10.12)
- [x] T126 [US7] Register static and contact routes in `routes/web.php`: `GET /about`, `GET /terms`, `GET /privacy`, `GET /shipping`, `GET /faq`, `GET /contact`, `POST /contact`
- [x] T127 [P] [US7] Create `resources/views/static/about.blade.php`, `terms.blade.php`, `privacy.blade.php`, `shipping-info.blade.php` (S24–S27): each extends storefront layout; page title; 1–3 placeholder paragraphs per §11.5; "Illustrative academic content" disclaimer line above footer
- [x] T128 [P] [US7] Create `resources/views/static/faq.blade.php` (S28): extends storefront layout; expandable Q&A list (6–10 items per §11.5); `<details>`/`<summary>` or JS accordion; academic content disclaimer
- [x] T129 [US7] Create `resources/views/contact.blade.php` (S29): extends storefront layout; form (name pre-filled+readonly for logged-in, email pre-filled+readonly, subject, message with limit helpers); Submit button; success flash banner with form cleared; character count hint on message field
- [x] T130 [US7] Create `resources/views/error.blade.php` (S30) and register custom 404 handler in `bootstrap/app.php` (or `app/Exceptions/Handler.php`): extends storefront layout; "Page not found" or "Product not available" message; Back to home button; also handle inactive product 404 in `ProductController@show`

**Checkpoint**: All 5 static pages load; contact form submits and stores correctly; T6 email in log; S30 renders for unknown URLs and inactive product URLs.

---

## Phase 14: Polish & Cross-Cutting Concerns

**Purpose**: Final verification pass across all 41 screens and business-rule compliance.

- [x] T131 [P] Verify all 41 screen routes are registered and return correct Blade views: check `php artisan route:list` covers S1–S30 and A1–A11 paths
- [x] T132 [P] Verify LKR formatting throughout all views — every price display uses "LKR 12,500" format (no decimals, comma thousands); fix any `number_format` or raw integer leaks
- [x] T133 [P] Verify academic disclaimer "TechBits is an academic project. No real transactions are processed." appears in every storefront/auth/account/checkout/static page footer
- [x] T134 [P] Verify all status badges are pill-shaped, colour-coded per UI.md §2.5, and always include text — check order, account, and product status badges across all views
- [x] T135 Verify cart merge on login: seed a customer with existing cart item; log in as guest with same product in session cart; verify DB cart qty = summed + capped to min(10, stock); verify merge notice shown
- [x] T136 [P] Verify account lockout: attempt 5 wrong passwords on a test customer account; verify locked_until set; verify 6th attempt with correct password still shows "Invalid email or password"; verify lock clears after 15 min or on success post-expiry
- [x] T137 Verify OTP required at every login: log in with remember-me → OTP still required → after OTP, verify remember-me cookie set (not before); re-open browser → session restored via cookie but no OTP skip (OTP is per-login-attempt, not per-session-persistence)
- [x] T138 [P] Verify admin isolation: confirm /admin routes return 403/redirect for authenticated customer; confirm /account routes return 403/redirect for authenticated admin
- [x] T139 Verify proactive cart stock warning: seed a product with stock=3; add qty=5 to customer cart; reduce stock to 3 via admin; reload cart → warning shown, qty selector capped at 3, checkout blocked; adjust qty to 3 → warning cleared, checkout enabled
- [x] T140 Create `app/Console/Commands/CleanupExpiredData.php` artisan command (`php artisan app:cleanup-expired-data`): delete users where status=unverified AND created_at < now() - 24 hours (per SCOPE.md §10.15); delete otps where expires_at < now() OR invalidated_at IS NOT NULL; delete password_reset_tokens where created_at < now() - 3600 seconds; register the command to run hourly in `routes/console.php` using `Schedule::command('app:cleanup-expired-data')->hourly()`
- [x] T141 Run `php artisan migrate:fresh --seed` to verify clean database setup from scratch; run quickstart.md validation checklist end-to-end

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — start immediately
- **Foundational (Phase 2)**: Depends on Phase 1 — BLOCKS all user stories
- **US1 Browse & Cart (Phase 3)**: After Foundation — no auth needed, purely storefront
- **US2 Auth (Phase 4)**: After Foundation — enables all customer-gated stories
- **US11 Admin Login (Phase 5)**: After Foundation — enables all admin stories
- **US8 Admin Products (Phase 6)**: After US11 (Phase 5)
- **US4 Checkout (Phase 7)**: After US2 (Phase 4) and US1 (Phase 3 — cart)
- **US9 Admin Orders (Phase 8)**: After US11 (Phase 5) and US4 (Phase 7 — orders must exist)
- **US5 Account Management (Phase 9)**: After US2 (Phase 4)
- **US3 Password Reset (Phase 10)**: After US2 (Phase 4) — uses User model and mail
- **US6 Order History (Phase 11)**: After US4 (Phase 7) — orders must exist
- **US10 Admin Customers+Messages (Phase 12)**: After US11 (Phase 5)
- **US7 Static+Contact (Phase 13)**: After Foundation — largely independent
- **Polish (Phase 14)**: After all user story phases

### User Story Dependencies

- **US1 (P1)**: Foundation only — no inter-story dependencies
- **US2 (P1)**: Foundation only
- **US4 (P1)**: Depends on US1 (cart) + US2 (auth)
- **US11 (P1)**: Foundation only
- **US8 (P1)**: Depends on US11
- **US9 (P1)**: Depends on US11 + US4
- **US5 (P2)**: Depends on US2
- **US3 (P2)**: Depends on US2
- **US6 (P2)**: Depends on US4
- **US10 (P2)**: Depends on US11
- **US7 (P3)**: Foundation only

### Within Each Phase

- Tasks marked [P] within the same phase can run in parallel
- Views can be built in parallel once the controller/route exists
- Models can be built in parallel once the migration is run

### Parallel Opportunities

```bash
# Phase 2 parallel examples (run after T017 migrate):
Task T018: CategorySeeder
Task T019: AdminSeeder          # [P] with T018
Task T022: storefront layout
Task T023: admin layout         # [P] with T022
Tasks T024-T029: all components # [P] with each other
Tasks T030-T032: helpers        # [P] with each other

# Phase 3 parallel examples (after Phase 2):
Task T035: Category model
Task T036: Product model        # [P] with T035
Task T037: ProductImage/Spec    # [P] with T035, T036
Task T042: home.blade           # [P] with T043, T044, T045
```

---

## Implementation Strategy

### MVP First (P1 Stories Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL — blocks all stories)
3. Complete Phase 3: US1 Browse & Cart → **Checkpoint**: storefront browsable
4. Complete Phase 4: US2 Auth → **Checkpoint**: register/login/OTP working
5. Complete Phase 5: US11 Admin Login → **Checkpoint**: admin accessible
6. Complete Phase 6: US8 Admin Products → **Checkpoint**: catalog manageable
7. Complete Phase 7: US4 Checkout → **Checkpoint**: end-to-end purchase works
8. Complete Phase 8: US9 Admin Orders → **Checkpoint**: full order lifecycle
9. **STOP and VALIDATE**: End-to-end purchase + admin lifecycle + OTP + lockout all pass quickstart.md

### Incremental Delivery

After MVP (P1), add P2 stories in any order (they are independent of each other):
- US5 (Account Management) → US3 (Password Reset) → US6 (Order History) → US10 (Admin Customers+Messages)
- Then P3: US7 (Static + Contact)
- Then Phase 14: Polish

---

## Notes

- [P] tasks = different files, no incomplete-sibling dependencies
- [US#] label maps each task to its user story for traceability
- Every user story is independently completable and testable after Phase 2 completes
- No automated tests are generated (not requested in spec)
- Commit after each task or logical group
- Stop at any checkpoint to validate the story independently before moving on
- Constitution check: all tasks must follow Standard Laravel MVC (Principle I) — no repositories, no extra service classes unless Eloquent genuinely cannot express the logic
