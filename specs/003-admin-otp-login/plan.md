# Implementation Plan: Admin Login OTP Verification

**Branch**: `003-admin-otp-login` | **Date**: 2026-06-07 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/003-admin-otp-login/spec.md`

## Summary

Add two-step OTP verification to the admin login flow. After submitting valid credentials on the admin login screen, the admin is redirected to a new OTP entry screen. A 6-digit code is emailed to the admin's registered address; the admin enters it to establish their session. Uses the same OTP generation logic (6-digit, SHA-256 hashed, 10-min expiry, 5-attempt invalidation, 60s resend cooldown) and the same `otps` table as customer login OTP.

Technical approach: modify `Admin\AuthController` to dispatch an OTP instead of calling `Auth::login()` directly at the credential step; add three new OTP routes and one new Blade view; add one new Mailable with its email template. No schema changes required.

## Technical Context

**Language/Version**: PHP 8.4, Laravel 11

**Primary Dependencies**: Laravel 11.x framework; Laravel Mail facade (same as customer OTP); `App\Models\Otp` (existing, reused unchanged)

**Storage**: Existing `otps` table (MariaDB); existing `users` table; database-backed sessions

**Testing**: Manual testing against acceptance scenarios in spec.md

**Target Platform**: Linux web server (Nginx + PHP-FPM 8.4); timezone Asia/Colombo

**Project Type**: Server-rendered web application — admin back-office extension

**Performance Goals**: Academic demo — no latency targets; matches existing admin panel behaviour

**Constraints**: No new dependencies; no schema changes; no changes to customer OTP flow; admin lockout gap must be fixed in same change (see research.md Decision 3)

**Scale/Scope**: One new admin screen (OTP entry); 3 new routes; 2 modified files; 3 new files

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-checked after Phase 1 design — all gates still pass.*

| Principle | Status | Notes |
|---|---|---|
| I. Standard Laravel MVC | ✅ PASS | Routes → controller → Otp model → Blade view. No extra layers. Form Request used for OTP input validation. |
| II. Specification Fidelity | ✅ PASS | All FR-001–FR-015 traceable to spec. Admin lockout gap fix is required by constitution §III and spec Assumptions. |
| III. Security by Convention | ✅ PASS | OTP stored as SHA-256 hash (same as customer). Generic error messages throughout. Single-use + 10-min expiry. Lockout added to credential step. Partial auth session MUST NOT grant access. |
| IV. Scope Discipline | ✅ PASS | One new screen (admin OTP entry) added per spec Assumptions (accepted scope extension). No storefront screens affected. |
| V. UI Consistency | ✅ PASS | Admin OTP screen uses standalone full-page card layout — same approach as admin login (pre-auth, no nav). |
| VI. Task Execution Discipline | ✅ PASS | tasks.md will sequence work one task at a time. |

## Project Structure

### Documentation (this feature)

```text
specs/003-admin-otp-login/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── contracts/
│   └── admin-otp.md     # New admin OTP routes contract
├── quickstart.md        # Phase 1 output
└── tasks.md             # Phase 2 output (/speckit-tasks)
```

### Source Code Changes

```text
techbits/
├── app/
│   ├── Http/Controllers/Admin/
│   │   └── AuthController.php          ← MODIFY: add OTP dispatch, showOtp, verifyOtp, resendOtp, lockout
│   └── Mail/
│       └── AdminLoginOtpMail.php        ← CREATE
├── resources/views/
│   ├── admin/auth/
│   │   └── otp.blade.php               ← CREATE (admin OTP entry screen)
│   └── emails/
│       └── admin-login-otp.blade.php   ← CREATE (email body)
└── routes/
    └── admin.php                       ← MODIFY: add 3 OTP routes
```

## Detailed Design

### Session State

Admin OTP flow uses a dedicated session key to avoid collision with customer OTP state:

| Key | Value | Cleared when |
|---|---|---|
| `pending_admin_id` | `int` — admin user's ID | On successful OTP verify, on OTP screen invalidation, on return to login |

No `admin_otp_purpose` needed — admin OTP only ever has one purpose (`admin_login`).

### OTP Flow Sequence

```
GET /tb-backroom-engine/
  → showLogin()
    if pending_admin_id in session → invalidate OTP + clear session (screen-close detection)
    return admin.auth.login view

POST /tb-backroom-engine/login
  → login()
    validate email + password (Form Request: AdminLoginRequest — existing)
    find user where role=admin
    if not found OR password wrong → increment failed_login_attempts, lockout at 5 → generic error
    if isLocked() → generic error
    clear pending admin OTP (invalidate any existing admin_login OTP for this user)
    generate 6-digit code, hash with SHA-256, store Otp (purpose='admin_login', expires_at=+10min)
    send AdminLoginOtpMail
    session(['pending_admin_id' => $user->id])
    redirect to admin.login.otp (GET /otp)

GET /tb-backroom-engine/otp
  → showOtp()
    if no pending_admin_id in session → redirect to admin.login
    return admin.auth.otp view (generic "code sent to registered email" message)

POST /tb-backroom-engine/otp
  → verifyOtp()
    validate ['otp' => 'required|digits:6']
    load user from pending_admin_id
    if no user or no pending_admin_id → redirect to admin.login
    load latest admin_login OTP for user (whereNull invalidated_at)
    if not found or !isValid() → back with generic error
    if hash('sha256', input) !== stored code:
      otp->incrementAttempts() (auto-invalidates at 5)
      if !isValid() → clear session, redirect admin.login with generic error
      back with generic error
    otp->invalidate()
    clear pending_admin_id from session
    Auth::login($user, false)
    session()->regenerate()
    redirect to admin.dashboard

POST /tb-backroom-engine/otp/resend
  → resendOtp()
    load user from pending_admin_id
    if no user → redirect to admin.login
    lastOtp = latest admin_login OTP
    if lastOtp and !canResend() → back with error ("Please wait before requesting a new code.")
    invalidate all pending admin_login OTPs for user
    generate new code, store new Otp
    send AdminLoginOtpMail
    back with success
```

### New Routes (admin.php additions)

```php
// Admin OTP (public — no EnsureAdmin, but session-gated in controller)
Route::get('/otp', [AuthController::class, 'showOtp'])->name('admin.login.otp');
Route::post('/otp', [AuthController::class, 'verifyOtp'])->name('admin.login.otp.submit');
Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('admin.login.otp.resend');
```

All three routes are placed outside the `ensure.admin` middleware group — the OTP screen is pre-authentication.

### Credential Lockout Fix

The existing `AuthController::login()` has no lockout logic. Add the same pattern as `LoginController::submit()`:

```php
if ($user->isLocked()) {
    return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
}

if (! Hash::check($request->password, $user->password)) {
    $user->increment('failed_login_attempts');
    if ($user->failed_login_attempts >= 5) {
        $user->update(['locked_until' => now()->addMinutes(15)]);
    }
    return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
}

// Credentials correct — reset lockout counter
$user->update(['failed_login_attempts' => 0, 'locked_until' => null]);
```

### AdminLoginOtpMail

Same pattern as `LoginOtpMail`:

```php
class AdminLoginOtpMail extends Mailable {
    public function __construct(public string $code, public string $email) {}
    public function build() {
        return $this->subject('TechBits — Admin Panel Login Verification')
            ->view('emails.admin-login-otp');
    }
}
```

### Admin OTP Blade View (admin/auth/otp.blade.php)

Standalone full-page card (same pattern as admin login — no admin layout with nav):

- Heading: "Admin Panel — Verification Required"
- Generic message: "We've sent a verification code to your registered email address."
- "If you close this page your login attempt will be cancelled."
- Error/success flash alerts
- OTP input (6-digit numeric, `otp-input` CSS class)
- "Verify" submit button (full-width primary)
- Resend form (separate POST form, `btn-secondary btn-sm`)
- "Code expires in 10 minutes."

### Email Template (emails/admin-login-otp.blade.php)

Same structure as `emails/login-otp` with subject-appropriate header text ("Admin Panel Login Verification" instead of "Login Code").

## Complexity Tracking

> **No constitution violations — this section is intentionally empty.**
