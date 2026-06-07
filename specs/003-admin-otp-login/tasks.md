# Tasks: Admin Login OTP Verification

**Input**: Design documents from `specs/003-admin-otp-login/`

**Organization**: Tasks are grouped by user story to enable independent implementation and testing.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to

---

## Phase 1: Setup

**Purpose**: Confirm the baseline state before any changes.

- [x] T001 Confirm baseline: run `grep -n "Auth::login\|OtpMail\|pending_admin" app/Http/Controllers/Admin/AuthController.php` — expected: `Auth::login` present, no OTP references; confirm `otps` table exists via `php artisan tinker --execute="echo Schema::hasTable('otps') ? 'OK' : 'MISSING';"`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Create the three new files and add the three new routes that all user stories depend on.

**⚠️ CRITICAL**: All of T002–T005 must complete before any user story verification.

- [x] T002 [P] Create `app/Mail/AdminLoginOtpMail.php` — Mailable with subject "TechBits — Admin Panel Login Verification" using view `emails.admin-login-otp`; same constructor signature as `LoginOtpMail` (`public string $code`, `public string $email`)
- [x] T003 [P] Create `resources/views/emails/admin-login-otp.blade.php` — email body template using same structure as `resources/views/emails/login-otp.blade.php`; update heading/subject text to "Admin Panel Login Verification"
- [x] T004 [P] Create `resources/views/admin/auth/otp.blade.php` — standalone full-page card (same approach as `admin/auth/login.blade.php`; NO admin layout nav); heading "TechBits Admin — Verify Login"; generic message "We've sent a verification code to your registered email address."; warning "If you close this page your login attempt will be cancelled."; error/success alert blocks; 6-digit OTP input (`otp-input` CSS class, `inputmode="numeric"`, `maxlength="6"`, `pattern="\d{6}"`); "Verify" submit button (full-width primary) POSTing to `route('admin.login.otp.submit')`; separate resend form POSTing to `route('admin.login.otp.resend')` with "Resend code" secondary button; "Code expires in 10 minutes." note
- [x] T005 Add three OTP routes to `routes/admin.php` in the public section (before the `ensure.admin` group): `GET /otp` → `AuthController::showOtp` named `admin.login.otp`; `POST /otp` → `AuthController::verifyOtp` named `admin.login.otp.submit`; `POST /otp/resend` → `AuthController::resendOtp` named `admin.login.otp.resend`

**Checkpoint**: After T002–T005, run `php artisan route:list | grep otp` — expected: 3 admin OTP routes listed.

---

## Phase 3: User Story 1 — Admin Completes Two-Step Login (Priority: P1) 🎯 MVP

**Goal**: Admin submits valid credentials → OTP emailed → admin enters OTP → dashboard session established.

**Independent Test**: `curl -c /tmp/ac.jar -b /tmp/ac.jar http://techbits.test.local/tb-backroom-engine` → submit credentials → confirm redirect to `/otp` → OTP in log → submit OTP → confirm redirect to dashboard.

- [x] T006 [US1] Modify `AuthController::showLogin()` in `app/Http/Controllers/Admin/AuthController.php`: add OTP screen-close detection — if `pending_admin_id` exists in session, load the user and invalidate all `admin_login` OTPs (`Otp::where('user_id', $userId)->where('purpose', 'admin_login')->whereNull('invalidated_at')->update(['invalidated_at' => now()])`), then `session()->forget('pending_admin_id')`; add `use App\Models\Otp;` import
- [x] T007 [US1] Modify `AuthController::login()` in `app/Http/Controllers/Admin/AuthController.php`: (a) add credential-step lockout — check `$user->isLocked()` before password check → generic error; increment `failed_login_attempts` on wrong password; lock at 5 attempts (`locked_until = now()->addMinutes(15)`); (b) on correct credentials, reset `failed_login_attempts = 0, locked_until = null`; (c) replace `Auth::login($user, false)` + `redirect()->route('admin.dashboard')` with: invalidate existing `admin_login` OTPs, generate 6-digit code (`str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT)`), store `Otp::create(['user_id'=>$user->id, 'code'=>hash('sha256',$code), 'purpose'=>'admin_login', 'expires_at'=>now()->addMinutes(10), 'created_at'=>now()])`, send `AdminLoginOtpMail`, `session(['pending_admin_id' => $user->id])`, return `redirect()->route('admin.login.otp')`; add imports for `Otp`, `AdminLoginOtpMail`, `Mail`
- [x] T008 [US1] Add `showOtp()` method to `AuthController` in `app/Http/Controllers/Admin/AuthController.php`: check `session('pending_admin_id')` — if missing, `redirect()->route('admin.login')`; return `view('admin.auth.otp')`
- [x] T009 [US1] Add `verifyOtp()` method to `AuthController` in `app/Http/Controllers/Admin/AuthController.php`: validate `['otp' => 'required|digits:6']`; load user from `session('pending_admin_id')` — if missing, redirect to login; load latest `admin_login` OTP (`whereNull('invalidated_at')->latest('created_at')->first()`); if none or `!$otp->isValid()` → `back()->withErrors(['otp' => 'Invalid or expired code.'])`; if `hash('sha256', $request->otp) !== $otp->code`: call `$otp->incrementAttempts()`; if `!$otp->isValid()`: `session()->forget('pending_admin_id')`, `redirect()->route('admin.login')->withErrors(['otp' => 'Invalid or expired code.'])`; else `back()->withErrors(['otp' => 'Invalid or expired code.'])`; on match: `$otp->invalidate()`, `session()->forget('pending_admin_id')`, `Auth::login($user, false)`, `session()->regenerate()`, `redirect()->route('admin.dashboard')`
- [x] T010 [US1] Verify US1 — (a) run `curl -s -o /dev/null -w "%{http_code}" http://techbits.test.local/tb-backroom-engine/otp` with no session — expect 302 redirect to admin login (SC-005); (b) full login flow via curl with cookie jar: GET login page to obtain CSRF token → POST credentials → confirm redirect to `/otp` → grep mail log for "Admin Panel Login Verification" code → POST the code → confirm redirect to `/tb-backroom-engine/dashboard`

**Checkpoint**: User Story 1 complete — two-step admin login works end-to-end.

---

## Phase 4: User Story 2 — OTP Resend and Attempt Limits (Priority: P2)

**Goal**: Resend is rate-limited (60s cooldown); 5 wrong OTPs invalidates session and returns admin to login.

**Independent Test**: Enter wrong OTP 5 times → confirm redirect to login with generic error; resend before 60s → confirm error; wait 60s, resend → confirm new code in log.

- [x] T011 [US2] Add `resendOtp()` method to `AuthController` in `app/Http/Controllers/Admin/AuthController.php`: load user from `session('pending_admin_id')` — if missing, redirect to login; fetch `$lastOtp = $user->otps()->where('purpose','admin_login')->latest('created_at')->first()`; if `$lastOtp && !$lastOtp->canResend()` → `back()->withErrors(['otp' => 'Please wait before requesting a new code.'])`; else: invalidate all pending admin_login OTPs, generate new code, create new Otp, send AdminLoginOtpMail, `back()->with('success', 'A new code has been sent to your email.')`
- [x] T012 [US2] Verify US2 — (a) obtain valid credentials session and reach OTP screen; (b) submit wrong OTP 5 times — confirm after 5th attempt: redirect to admin login with error, `pending_admin_id` cleared from session; (c) in a fresh flow, reach OTP screen and immediately click resend — confirm "Please wait" error; (d) reach OTP screen and use `php artisan tinker` to update `created_at` on the latest `admin_login` OTP to 65 seconds ago, then resend — confirm new code in mail log and old code rejected

**Checkpoint**: User Story 2 complete — attempt limits and resend cooldown enforced.

---

## Phase 5: User Story 3 — Invalid Credentials Do Not Trigger OTP (Priority: P3)

**Goal**: Wrong email or wrong password returns a generic error; no OTP email sent.

**Independent Test**: Submit wrong password on admin login → generic error shown, no `admin_login` OTP row created in DB.

- [x] T013 [US3] Verify US3 — (a) submit wrong password for `admin@techbits.lk` via curl POST — confirm 302 back to login (no redirect to `/otp`) and no new "Admin Panel Login Verification" entry in `storage/logs/laravel.log`; (b) run `php artisan tinker --execute="echo App\Models\Otp::where('purpose','admin_login')->count();"` — confirm count is 0 after wrong-credential attempts; (c) submit correct credentials, confirm count increments to 1 (OTP created only on success)

**Checkpoint**: User Story 3 confirmed — OTP only dispatched on valid credentials.

---

## Phase 6: Polish

**Purpose**: Documentation update and full end-to-end validation.

- [x] T014 [P] Update `specs/001-techbits-ecommerce-spec/contracts/admin.md`: add the three new routes (`GET /tb-backroom-engine/otp`, `POST /tb-backroom-engine/otp`, `POST /tb-backroom-engine/otp/resend`) and note the modified `POST /tb-backroom-engine/login` behaviour (OTP dispatch instead of direct session creation)
- [x] T015 Run final grep: `grep -rn "Auth::login\|pending_user_id" app/Http/Controllers/Admin/ --include="*.php"` — expected: no `Auth::login` in `AuthController::login()` (moved to `verifyOtp()`), no `pending_user_id` (using `pending_admin_id`)
- [x] T016 Run all 8 scenarios from `specs/003-admin-otp-login/quickstart.md` end-to-end and confirm each passes

---

## Dependencies & Execution Order

### Phase Dependencies

- **Phase 1 (Setup)**: No dependencies — start immediately
- **Phase 2 (Foundational)**: Depends on Phase 1 — BLOCKS all user story verification
- **Phase 3 (US1)**: Depends on Phase 2 — T006–T009 all touch `AuthController.php` (sequential); T010 depends on T006–T009
- **Phase 4 (US2)**: Depends on Phase 3 checkpoint — T011 extends `AuthController.php`; T012 depends on T011
- **Phase 5 (US3)**: Depends on Phase 3 checkpoint — verification only; can run after T010
- **Phase 6 (Polish)**: Depends on Phases 3–5 completing

### Parallel Opportunities

```
Phase 2: T002 + T003 + T004 + T005  ← all in parallel (different files)
                  ↓
Phase 3: T006 → T007 → T008 → T009 → T010  ← sequential (same file)
                  ↓
Phase 4 (T011 → T012) + Phase 5 (T013)  ← can run in parallel after Phase 3
                  ↓
Phase 6: T014 (parallel) + T015 + T016
```

### Within AuthController.php

T006–T009 and T011 all modify `AuthController.php` — run them sequentially. Within each task:
- T006: `showLogin()` method only
- T007: `login()` method only
- T008: new `showOtp()` method
- T009: new `verifyOtp()` method
- T011: new `resendOtp()` method

---

## Implementation Strategy

### MVP (US1 only)

1. Phase 1: T001 — confirm baseline
2. Phase 2: T002–T005 — create files + routes
3. Phase 3: T006–T009 — modify AuthController
4. T010 — verify two-step login works
5. **STOP and validate** — admin panel fully secured with OTP

### Full delivery

Complete Phases 1–6 sequentially. Total estimate: ~25 minutes.

---

## Notes

- T002–T005 are all different files — safe to do in parallel
- T006–T011 all touch `AuthController.php` — do them sequentially
- T001 baseline check: `Auth::login` should appear exactly once in current `AuthController.php` (in `login()`) — after T007 it should appear in `verifyOtp()` only
- T003 email view: read `resources/views/emails/login-otp.blade.php` first to match the structure
- T004 OTP blade: use standalone HTML (like `admin/auth/login.blade.php`), NOT `@extends('layouts.admin')`
- Session key is `pending_admin_id` throughout — never use `pending_user_id` in admin OTP flow
