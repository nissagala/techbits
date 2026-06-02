# Research: TechBits eCommerce Application

**Branch**: `001-techbits-ecommerce-spec` | **Date**: 2026-05-31

All technical choices below are locked by the project constitution (stack is non-negotiable).
Research focuses on the implementation patterns for the non-trivial features.

---

## R1. OTP-at-every-login Pattern in Laravel

**Decision**: Custom `otps` table with a hashed 6-digit code, purpose field, expiry timestamp,
and attempt counter. No third-party OTP package.

**Rationale**: Laravel has no built-in OTP mechanism. A custom table gives full control over
the business rules (5-attempt invalidation, 60s resend cooldown, purpose differentiation between
registration and login, single-use, closing-screen invalidation). Hashing the stored code with
`hash('sha256', $code)` prevents exposure if the DB is compromised.

**Implementation pattern**:
- On credential success (login) or registration: generate random 6-digit number, hash it, insert
  into `otps` table with `purpose=login|registration`, `expires_at=now()+10min`, `attempts=0`.
  Any prior un-expired OTP for that user+purpose is invalidated first (set `invalidated_at=now()`).
- OTP screen stores `pending_user_id` and `otp_purpose` in session (not the code itself).
- On verify: load latest non-invalidated OTP for user+purpose, check expiry, compare
  `hash('sha256', $input) === $otp->code`, increment attempts, invalidate after 5 wrong.
- On screen close (login OTP): the controller detects the session flag and invalidates the OTP;
  the session pending state is cleared. This is enforced by a middleware or the login flow check.
- Resend: if `created_at < now() - 60s`, allow resend (invalidate old, create new).

**Alternatives considered**: Laravel Sanctum (overkill, introduces SPA concerns), third-party
`pragmarx/google2fa` (TOTP not email OTP), Laravel cache-based storage (less auditable, hard to
track attempts across requests).

---

## R2. Cart Architecture: Session (Guest) + Database (Customer)

**Decision**: Guest cart lives in Laravel's session as a plain PHP array
(`session(['cart' => [product_id => qty, ...]])`). Customer cart lives in the `cart_items` DB
table. On login, merge logic sums quantities and caps per `min(10, stock)`.

**Rationale**: Standard Laravel pattern for this use case. Session cart requires no DB writes for
guests (fast), automatically expires with session (≤7 days inactivity via session lifetime
config). DB cart persists across devices and sessions for customers. The merge is a single
operation at login time.

**Cart merge algorithm** (runs in `LoginController` after OTP success):
```
for each item in guest session cart:
    if product_id exists in customer DB cart:
        new_qty = min(customer_line.qty + guest_qty, min(10, product.stock))
        update customer_line.qty = new_qty
    else:
        insert new cart_item(user_id, product_id, qty=min(guest_qty, min(10, product.stock)))
clear session cart
notify user if any qty was capped
```

**Proactive stock warning**: Both session cart (guest) and DB cart (customer) must check current
stock on every cart page load. Lines where `cart_qty > product.stock` (and `product.stock > 0`)
show a warning and reduce the quantity selector cap. Lines where `product.stock = 0` or product
is inactive/soft-deleted show a different warning. Both block checkout.

**Alternatives considered**: Single Redis-backed cart for both roles (overkill for academic demo,
adds Redis dependency), using the DB for guest carts with session key (unnecessary complexity).

---

## R3. Product Soft Deletes for SKU Uniqueness

**Decision**: `products` table uses Laravel's `SoftDeletes` trait (`deleted_at` column). The
unique index on `sku` covers all rows including soft-deleted ones, enforcing "SKU not reusable
after deletion" without a separate tracking table.

**Rationale**: Hard-deleting a row removes the unique constraint enforcement for that SKU. Soft
deletes keep the row in the DB (with `deleted_at` set), so the unique index still applies.
From the storefront's perspective, soft-deleted products behave identically to hard-deleted ones
(they are excluded by Eloquent's default query scope). Past order items hold snapshot data and
do not require a live FK to `products`.

**Query scope impact**: All Eloquent queries on `Product` automatically exclude soft-deleted rows
(`deleted_at IS NULL`) unless explicitly using `withTrashed()`. Cart checks that look up
soft-deleted products use `Product::withTrashed()->find($id)` to detect deleted products and
show warnings.

**Alternatives considered**: Separate `deleted_skus` table — more moving parts, requires
additional check in validation; no soft deletes + application-level SKU blocklist — fragile.

---

## R4. Order Number Generation (TB-NNNNNN)

**Decision**: Use the orders table auto-increment `id` as the sequence basis. After inserting
the order row, immediately update `order_number = 'TB-' . str_pad($order->id, 6, '0', STR_PAD_LEFT)`.

**Rationale**: Auto-increment IDs are guaranteed unique and monotonically increasing by the DB.
The two-step (insert then update) avoids needing to know the ID before insertion. For an academic
single-server demo, there is no concurrency concern with this approach.

**Alternatives considered**: Separate `order_sequences` table with a counter — unnecessary
complexity; UUID — not zero-padded and not human-readable as required by TB-NNNNNN format.

---

## R5. Simulated Payment — Luhn Validation

**Decision**: Implement Luhn check as a standalone PHP helper class (`App\Helpers\LuhnValidator`).
Use a Laravel custom validation rule (`Rule::make()`) to invoke it from `PaymentRequest`.

**Luhn algorithm** (standard):
```
1. From the rightmost digit, double every second digit.
2. If doubling results in > 9, subtract 9.
3. Sum all digits (including undoubled).
4. If total mod 10 == 0 → valid.
```

**What is validated** (format only, no real authorization):
- Card number: 13–19 digits (spaces stripped before validation), passes Luhn.
- Expiry: MM/YY format; month 01–12; not in the past (compare against current month/year).
- CVV: 3–4 digits.
- Cardholder name: 2–100 chars.

**Storage**: Only `payment_last4` (last 4 chars of card number after stripping spaces),
`payment_cardholder`, and `payment_expiry` are stored on the order. CVV is never stored.

**Alternatives considered**: Using a real Stripe test-mode integration — out of scope per SCOPE.md.

---

## R6. Email Notifications — Laravel Mail Facade

**Decision**: Six Mailable classes (T1–T6), sent synchronously during the request for the
academic demo (no queue required). Use Laravel's `Mail::to($recipient)->send(new XxxMail(...))`.

**Dev environment**: Mailtrap or Laravel's built-in `log` mail driver (`MAIL_MAILER=log`) so
emails appear in `storage/logs/laravel.log` without a real SMTP server.

**Email templates**: Blade views in `resources/views/emails/` using basic HTML tables for layout.
Each email must include: TechBits branding, LKR formatting, Asia/Colombo timestamps, and a
sign-off.

**Alternatives considered**: Laravel queues for async email — adds Redis/database queue
dependency; the spec doesn't require async delivery and the academic context doesn't need it.

---

## R7. Session Management & Remember Me

**Decision**: `SESSION_DRIVER=database`. Standard Laravel session middleware. 30-minute inactivity
default (`SESSION_LIFETIME=30`). Remember-me extends to 43200 minutes (30 days) using Laravel's
`Auth::attempt(['email'=>..., 'password'=>...], $remember=true)` — but only after OTP verification.

**Key implementation detail**: The remember-me cookie is issued after OTP success, not after
credentials. This means `Auth::attempt($credentials, false)` is called on credential check
(never sets remember cookie), and `Auth::login($user, $remember)` is called after OTP success
with the `$remember` flag from the original login form.

**OTP session state**: `pending_user_id`, `otp_purpose`, `otp_remember`, and `redirect_after_login`
are stored in the session during the credential→OTP gap. These are cleared after OTP success
or failure.

---

## R8. Multi-Image Upload with Primary Flag

**Decision**: `product_images` table with `is_primary` boolean and `sort_order` integer.
Images stored in `storage/app/public/products/{product_id}/`. PHP validation: JPG/JPEG/PNG/WebP,
max 2MB per file. Primary flag enforced in application logic: when setting a new primary, update
all others to `is_primary=false`, then set the chosen one to `is_primary=true`.

**No auto-resize**: The spec explicitly states "no auto-resize" (SCOPE.md §10.3). Images are
stored as uploaded. ~800×800 px is a recommendation to admins, not a server-side enforcement.

**Alternatives considered**: Spatie Media Library — third-party package, adds complexity; would
need to verify it doesn't add hardening behaviors not in the spec.

---

## R9. Account Lockout Implementation

**Decision**: `failed_login_attempts` (tinyint) and `locked_until` (timestamp, nullable) columns
on `users`. Lockout is checked and enforced at the credential step only (not OTP step).

**Logic**:
```
on credential submit:
    if locked_until IS NOT NULL AND locked_until > now():
        return generic "Invalid email or password" (no lockout disclosure)
    verify email + password
    if wrong:
        increment failed_login_attempts
        if failed_login_attempts >= 5:
            set locked_until = now() + 15 minutes
        return "Invalid email or password"
    if correct:
        reset failed_login_attempts = 0, locked_until = null
        proceed to OTP
```

**Per-account, not per-IP**: The counter is on the `users` row, not keyed by IP address.
The spec explicitly requires this (SCOPE.md §10.1).

---

## R10. 25 Sri Lankan Districts

**Decision**: Hard-code the 25 districts as a PHP array constant (in a `DistrictList` class or
`config/districts.php`). Use as a validated enum in address Form Requests and rendered as a
`<select>` dropdown in Blade views.

The 25 official districts: Colombo, Gampaha, Kalutara, Kandy, Matale, Nuwara Eliya, Galle,
Matara, Hambantota, Jaffna, Kilinochchi, Mannar, Vavuniya, Mullaitivu, Batticaloa,
Ampara, Trincomalee, Kurunegala, Puttalam, Anuradhapura, Polonnaruwa, Badulla, Monaragala,
Ratnapura, Kegalle.
