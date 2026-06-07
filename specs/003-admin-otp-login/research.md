# Research: Admin Login OTP Verification

**Branch**: `003-admin-otp-login` | **Date**: 2026-06-07

## Decision 1 — OTP purpose value for admin login

**Decision**: Use `'admin_login'` as the `purpose` field value in the `otps` table for admin login OTPs.

**Rationale**: The existing `otps` table has a `purpose` VARCHAR column already used for `'registration'` and `'login'` (customer). Adding `'admin_login'` as a third purpose reuses the schema without any migration. The `Otp` model queries are always scoped by `purpose`, so there is no cross-contamination.

**Alternatives considered**: A separate `admin_otps` table — rejected because it duplicates schema and the Otp model logic for no benefit.

---

## Decision 2 — Session keys for admin OTP state

**Decision**: Use `pending_admin_id` as the session key for admin OTP flow (instead of `pending_user_id`).

**Rationale**: Customer login already uses `pending_user_id` in the session. If an admin navigates to the storefront mid-flow (unlikely, but possible), the two sessions would collide if both use the same key. Using a distinct key (`pending_admin_id`) ensures the admin OTP state is isolated from the customer OTP state.

**Alternatives considered**: Reuse `pending_user_id` + distinguish via `otp_purpose` — rejected because `otp_purpose` is already set to `'login'` by the customer flow; using `'admin_login'` for admin's otp_purpose would work but adds indirection. A dedicated key is cleaner.

---

## Decision 3 — Credential-step account lockout for admin (pre-existing gap)

**Decision**: Add account lockout at the credential step to `AuthController::login()` as part of this feature.

**Rationale**: The current admin `AuthController::login()` does not track `failed_login_attempts` or enforce `locked_until`, even though the `User` model has those fields and the constitution (§III) mandates 5-attempt / 15-minute lockout. Since this feature modifies `AuthController::login()` to add the OTP dispatch, fixing this gap in the same change costs nothing and brings admin lockout into compliance.

**Alternatives considered**: Leave it for a separate fix — rejected because the spec Assumptions explicitly say "Account lockout rules from existing specification apply to the credential step," and the constitution gate requires compliance.

---

## Decision 4 — No new Mail template view needed beyond a minimal variant

**Decision**: Create `AdminLoginOtpMail` as a new Mailable using a new dedicated email view `emails.admin-login-otp`.

**Rationale**: The customer `LoginOtpMail` uses `emails.login-otp`. A separate admin mailable and view allows the subject line ("Admin Panel Login Verification") and body text to differ appropriately while still following the exact same class structure. No changes to customer mail flow.

**Alternatives considered**: Reuse `LoginOtpMail` with a flag — rejected because mixing admin and customer concerns in one class violates single-responsibility and makes the subject line conditional.

---

## Decision 5 — Admin OTP screen layout

**Decision**: Admin OTP screen (`admin.auth.otp`) uses a standalone HTML page (same approach as admin login — full-page centered card, no left nav or top bar).

**Rationale**: The admin OTP screen is part of the pre-auth flow. The admin is not yet authenticated, so rendering the admin layout with sidebar/nav (which assumes an active admin session) is not appropriate. The standalone card layout matches the existing admin login screen.

**Alternatives considered**: Use the full `admin.blade.php` layout — rejected because that layout is meant for authenticated admin views only.

---

## Decision 6 — Schema change required for `otps.purpose` ENUM

**Decision**: Add a migration to extend the `purpose` ENUM column from `['registration', 'login']` to `['registration', 'login', 'admin_login']`.

**Rationale**: The initial research incorrectly concluded no schema change was needed. The `purpose` column is a MariaDB ENUM (not a VARCHAR), so inserting `'admin_login'` without altering the column causes a data truncation error (SQLSTATE 01000). A migration is required.

**Alternatives considered**: Using an existing purpose value (e.g., `'login'`) with a different discriminator — rejected because it conflates admin and customer OTP records in queries and invalidation logic.

---

## Change Inventory (complete)

Files to **create** (4):

| File | Purpose |
|---|---|
| `app/Mail/AdminLoginOtpMail.php` | Mailable for admin OTP email |
| `resources/views/emails/admin-login-otp.blade.php` | Email body template |
| `resources/views/admin/auth/otp.blade.php` | Admin OTP entry screen |
| `database/migrations/2026_06_07_135243_add_admin_login_to_otps_purpose.php` | Extend `otps.purpose` ENUM |

Files to **modify** (2):

| File | Change |
|---|---|
| `app/Http/Controllers/Admin/AuthController.php` | Add OTP dispatch in `login()`, add lockout at credential step, add `showOtp()` / `verifyOtp()` / `resendOtp()` methods, fix `showLogin()` to invalidate dangling admin OTP |
| `routes/admin.php` | Add 3 routes: `GET /otp`, `POST /otp`, `POST /otp/resend` |
