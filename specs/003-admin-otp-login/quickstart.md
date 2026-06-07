# Quickstart: Admin OTP Login — Manual Test Scenarios

**Branch**: `003-admin-otp-login` | **Date**: 2026-06-07

Prerequisites: app running at `http://techbits.test.local`, mail log at `storage/logs/laravel.log`, admin account `admin@techbits.lk` / `Admin@1234`.

---

## Scenario 1 — Happy Path (US1)

1. Visit `http://techbits.test.local/tb-backroom-engine`
2. Enter `admin@techbits.lk` and `Admin@1234`, click **Log in**
3. Verify: redirected to `/tb-backroom-engine/otp` (OTP entry screen loads)
4. Verify: screen shows "We've sent a verification code to your registered email address" (no email hint)
5. Check `storage/logs/laravel.log` for a line containing "Admin Panel Login Verification" with a 6-digit code
6. Enter the 6-digit code, click **Verify**
7. Verify: redirected to `/tb-backroom-engine/dashboard` with active admin session

---

## Scenario 2 — Wrong OTP (US1 / FR-007)

1. Complete steps 1–4 from Scenario 1
2. Enter `000000` (wrong code), click **Verify**
3. Verify: generic error message shown, OTP entry form still present
4. Repeat 4 more times (5 total wrong attempts)
5. Verify: after 5th wrong attempt, redirected to admin login screen with generic error
6. Verify: entering the original correct code from the email now fails (OTP invalidated)

---

## Scenario 3 — OTP Expiry (US1 / FR-005)

1. Complete steps 1–3 from Scenario 1
2. Wait 10+ minutes (or manually update `expires_at` in DB to the past)
3. Enter the original code, click **Verify**
4. Verify: generic error shown (OTP expired)

---

## Scenario 4 — Resend (US2 / FR-008, FR-009)

1. Complete steps 1–4 from Scenario 1
2. Click **Resend code** immediately (within 60 seconds)
3. Verify: error shown ("Please wait before requesting a new code")
4. Wait 60 seconds, click **Resend code** again
5. Verify: new 6-digit code appears in the log; previous code is now invalid
6. Enter the new code, verify: dashboard loads

---

## Scenario 5 — Screen Close / Back to Login (US2 / FR-010)

1. Complete steps 1–3 from Scenario 1 (arrive at OTP screen)
2. Navigate back to `http://techbits.test.local/tb-backroom-engine`
3. Verify: admin login screen shown (no OTP screen redirect)
4. Complete full login flow again — verify: new OTP sent, previous code invalid

---

## Scenario 6 — Wrong Credentials (US3 / FR-002, FR-012)

1. Visit `http://techbits.test.local/tb-backroom-engine`
2. Enter `admin@techbits.lk` and `wrongpassword`, click **Log in**
3. Verify: generic "Invalid email or password." shown on login screen
4. Verify: `storage/logs/laravel.log` has NO new "Admin Panel Login Verification" entry

---

## Scenario 7 — Credential Lockout (FR-002)

1. Submit wrong password 5 times for `admin@techbits.lk`
2. Verify: generic error continues to show (no lockout-specific message)
3. Wait 15 minutes (or reset `locked_until` in DB)
4. Verify: admin can log in again

---

## Scenario 8 — Direct URL Access to OTP Screen (SC-005)

1. With no session, visit `http://techbits.test.local/tb-backroom-engine/otp` directly
2. Verify: redirected to `/tb-backroom-engine` (admin login screen)
