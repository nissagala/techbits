# Form Contracts: Admin OTP Login (Feature 003)

New routes added under the `/tb-backroom-engine/*` prefix. All three are outside `EnsureAdmin` middleware (pre-authentication). Session-gated in controller — no `pending_admin_id` in session → redirect to admin login.

---

## POST /tb-backroom-engine/login (A1 — modified)

Existing route. Behaviour changed: no longer calls `Auth::login()` directly. Now dispatches OTP and redirects to OTP screen.

**Added behaviour**:
- Lockout check added (`user->isLocked()` → generic error)
- Failed attempt tracking added (`failed_login_attempts` increment, lockout at 5)
- On success: OTP dispatched, redirect to `GET /tb-backroom-engine/otp`

| Field | Rules |
|---|---|
| email | required, email:rfc, max:254 |
| password | required, string |

**On credential failure**: Generic "Invalid email or password." (increment failed attempts; lock after 5).
**On credential success**: OTP sent, redirect to admin OTP screen. Admin session NOT established yet.

---

## GET /tb-backroom-engine/otp (new — A1-OTP show)

Session guard: `pending_admin_id` must exist → else redirect to `/tb-backroom-engine/`.

**Response**: Renders `admin.auth.otp` view. No form fields on GET — display only.

---

## POST /tb-backroom-engine/otp (new — A1-OTP verify)

Session guard: `pending_admin_id` must exist → else redirect to `/tb-backroom-engine/`.

| Field | Rules |
|---|---|
| otp | required, digits:6 |

**On invalid/expired OTP**: Generic error, back to OTP form. After 5 wrong attempts: OTP invalidated, session cleared, redirect to admin login with generic error.
**On success**: OTP invalidated, session cleared, `Auth::login($user, false)`, `session()->regenerate()`, redirect to `admin.dashboard`.

---

## POST /tb-backroom-engine/otp/resend (new — A1-OTP resend)

Session guard: `pending_admin_id` must exist → else redirect to `/tb-backroom-engine/`.

No body fields.

**Within 60-second cooldown**: Generic error "Please wait before requesting a new code."
**After cooldown**: Previous OTP invalidated, new OTP generated and sent, back with success flash.
