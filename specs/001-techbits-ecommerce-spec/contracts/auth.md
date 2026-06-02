# Form Contracts: Authentication (S6–S12)

## POST /register (S6)

| Field | Rules | Error message |
|---|---|---|
| name | required, string, min:2, max:100, regex:/^[a-zA-Z\s.\-]+$/ | Full name is required / 2–100 letters, spaces, dots, hyphens only |
| email | required, email:rfc, max:254, unique:users,email | Email is required / Invalid email / Email already in use |
| phone | required, string, custom:SriLankanPhone | Contact number is required / Invalid Sri Lankan phone number |
| password | required, string, min:8, max:64, regex:/[a-zA-Z]/, regex:/[0-9]/ | Password required / 8–64 chars with at least one letter and one number |
| password_confirmation | required, same:password | Passwords do not match |

**On success**: Create Unverified user, generate+email OTP (T1), store `pending_user_id` in session, redirect → GET /register/verify.

---

## POST /register/verify (S7 — OTP)

| Field | Rules |
|---|---|
| otp | required, string, digits:6 |

**On success**: Mark user Active, clear session pending state, `Auth::login($user)`, redirect → / or saved checkout URL.
**On wrong code**: Increment attempts; if attempts ≥ 5 → invalidate OTP, show "Too many attempts. Please request a new code."
**On expired**: Show "Invalid or expired code."

## POST /register/resend (S7)

Guard: 60s cooldown (`otp.created_at < now() - 60s`). Invalidate old OTP, generate+email new one (T1).

---

## POST /login (S8)

| Field | Rules |
|---|---|
| email | required, email:rfc, max:254 |
| password | required, string |
| remember | boolean, optional |

**On success** (credentials match, account Active, not locked): Generate+email OTP (T2), store `pending_user_id`, `otp_remember`, `redirect_after_login` in session, redirect → GET /login/verify.
**On any failure**: Return "Invalid email or password." (generic; covers wrong password, Inactive, locked, non-existent).
**Lockout**: After 5 consecutive wrong-password attempts on a valid account → set `locked_until = now() + 15 min`; continue returning same generic error.
**Unverified account with correct credentials**: Treat as J8.1 — generate+email new registration OTP (T1), redirect → GET /register/verify.

---

## POST /login/verify (S9 — OTP)

| Field | Rules |
|---|---|
| otp | required, string, digits:6 |

**On success**: `Auth::login($user, $remember)`, clear session pending state, redirect to `redirect_after_login` or /.
**On wrong/expired/max-attempts**: Same behavior as registration OTP. Closing this screen: cleared by browser navigation; next visit to any protected route re-checks session and invalidates the pending OTP.

---

## POST /forgot-password (S10)

| Field | Rules |
|---|---|
| email | required, email:rfc, max:254 |

**Always**: Redirect → GET /forgot-password/sent (S11) with generic message. If email belongs to Active account: generate+email reset link (T3), upsert `password_reset_tokens`. No response difference for non-existent/Inactive/Unverified.

---

## POST /password/reset (S12)

| Field | Rules |
|---|---|
| token | required (from URL) |
| password | required, min:8, max:64, regex:/[a-zA-Z]/, regex:/[0-9]/ |
| password_confirmation | required, same:password |

**Validation**: Token must exist in `password_reset_tokens`, email must match Active account, `created_at + 1 hour > now()`.
**On success**: Update password, delete token, redirect → /login with success message.
**On invalid/expired token**: Show "Invalid or expired reset link." with option to request new one.
