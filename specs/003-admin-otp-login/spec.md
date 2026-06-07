# Feature Specification: Admin Login OTP Verification

**Feature Branch**: `003-admin-otp-login`

**Created**: 2026-06-07

**Status**: Draft

**Input**: User description: "It is required to have OTP verification to admin login. After inserting username and password in the first login screen, after submission it should load the OTP entering screen. OTP should send to the email like the normal customer login. So when authenticating there is 3 items to consider - Username/email, password and the OTP. Same OTP generation logic which is used in customer login, can be used in here."

## Clarifications

### Session 2026-06-07

- Q: After 5 consecutive wrong OTP entries, should the admin account be locked for 15 minutes or should the OTP simply be invalidated with return to login (matching customer OTP behaviour)? → A: Invalidate OTP + return to login only; no account lockout (matches customer OTP behaviour).
- Q: Should the OTP screen show a masked email hint (e.g., "a***@techbits.lk") or a generic message with no email reference? → A: Generic message only ("We've sent a code to your registered email address") — no partial email disclosure.

## User Scenarios & Testing *(mandatory)*

### User Story 1 — Admin Completes Two-Step Login (Priority: P1) 🎯 MVP

An admin enters their email and password on the admin login screen and submits the form. The system validates the credentials, sends a one-time passcode to the admin's registered email address, and presents the OTP entry screen. The admin retrieves the code from their inbox, enters it, and is granted access to the admin dashboard.

**Why this priority**: This is the core feature — without P1 there is no OTP-protected admin login.

**Independent Test**: Navigate to `/tb-backroom-engine`, enter valid credentials, receive OTP email, enter OTP, land on dashboard.

**Acceptance Scenarios**:

1. **Given** an admin visits the admin login page, **When** they submit a correct email and password, **Then** an OTP is dispatched to the admin's email address and the OTP entry screen is displayed.
2. **Given** the admin is on the OTP entry screen, **When** they enter the correct OTP within the validity window, **Then** their admin session is established and they are redirected to the admin dashboard.
3. **Given** the admin is on the OTP entry screen, **When** they enter an incorrect OTP, **Then** a generic error message is shown and the form remains available for retry (up to the attempt limit).
4. **Given** the admin has received an OTP email, **When** they do not use it within 10 minutes, **Then** the OTP expires and submission of that code is rejected with an expiry message.

---

### User Story 2 — OTP Resend and Attempt Limits (Priority: P2)

After landing on the OTP entry screen, the admin may need to request a fresh code (e.g., the first email was slow). The system allows a resend after a cooldown period. If too many incorrect OTP attempts are made, the pending OTP is invalidated and the admin is returned to the login screen.

**Why this priority**: Guards against brute-force OTP guessing and handles email delivery delays; important for security but P1 must function first.

**Independent Test**: On the OTP screen, click resend before cooldown → rejected; wait for cooldown → resend succeeds; enter wrong OTP 5 times → returned to login screen.

**Acceptance Scenarios**:

1. **Given** the admin is on the OTP entry screen within 60 seconds of the last send, **When** they click "Resend OTP", **Then** the request is rejected and the remaining cooldown time is displayed.
2. **Given** the admin is on the OTP entry screen after the 60-second cooldown, **When** they click "Resend OTP", **Then** a new OTP is dispatched, the previous OTP is invalidated, and the 10-minute validity window resets.
3. **Given** the admin has made 5 consecutive incorrect OTP entries, **When** the fifth incorrect attempt is submitted, **Then** the OTP is invalidated, the admin is returned to the login screen, and a generic error message is shown (no attempt count disclosed).
4. **Given** the admin navigates away from or closes the OTP entry screen, **When** they later return to the admin login page, **Then** any previously issued OTP is treated as expired and a fresh login flow begins.

---

### User Story 3 — Invalid Credentials Do Not Trigger OTP (Priority: P3)

When an admin submits incorrect credentials (wrong email or wrong password), the system rejects the attempt at the credential-check step and does not send an OTP email, keeping OTP dispatch gated behind valid credentials.

**Why this priority**: Prevents OTP email flooding and information leakage; the main login flow (P1) works correctly without this negative path being explicitly verified first.

**Independent Test**: Submit wrong password on admin login → receive generic error, no OTP email sent, no OTP record created.

**Acceptance Scenarios**:

1. **Given** an admin submits an email address that does not match any admin account, **When** the form is submitted, **Then** a generic "Invalid email or password" error is displayed and no OTP email is sent.
2. **Given** an admin submits a correct email but incorrect password, **When** the form is submitted, **Then** a generic "Invalid email or password" error is displayed and no OTP email is sent.
3. **Given** an admin account is in a locked state (exceeded failed credential attempts), **When** any credentials are submitted, **Then** a generic error is displayed and no OTP email is sent.

---

### Edge Cases

- What happens when the admin's email inbox is unreachable or delivery is delayed? The OTP screen still loads; the admin can use the resend function after the cooldown.
- What happens if the admin opens the OTP screen in two browser tabs simultaneously? Both tabs share the same pending OTP; using it in one tab invalidates it in the other.
- What happens if the OTP expires while the admin is still on the OTP entry screen? Submission is rejected with an expiry message and a resend option is available.
- What happens if a non-admin user's email is entered on the admin login? Generic "Invalid email or password" with no OTP sent (same as Story 3, SC-001).
- What happens if the admin submits the OTP entry form with an empty field? Standard validation — field is required, no OTP attempt is consumed.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST present a credential entry form (email + password) as the first step of admin login; this screen is the existing admin login screen (A1).
- **FR-002**: System MUST validate admin credentials before dispatching any OTP — OTP is only sent when credentials are confirmed correct.
- **FR-003**: System MUST generate and send a 6-digit numeric OTP to the admin's registered email address upon successful credential validation.
- **FR-004**: System MUST display an OTP entry screen immediately after successful credential validation; this screen is a new admin screen (A1-OTP) accessible only when a valid pending OTP state exists in the session.
- **FR-005**: The OTP MUST be valid for exactly 10 minutes from the time of generation; attempts submitted after expiry MUST be rejected with an expiry message.
- **FR-006**: The OTP MUST be single-use; it is invalidated immediately upon successful verification, upon expiry, or upon reaching the attempt limit, whichever comes first.
- **FR-007**: System MUST reject OTP entry and return the admin to the login screen after 5 consecutive incorrect OTP entries; error messages MUST be generic (no attempt count disclosed). OTP exhaustion does NOT trigger the 15-minute account lockout — it only invalidates the current OTP, consistent with customer OTP behaviour.
- **FR-008**: System MUST enforce a 60-second resend cooldown; resend requests submitted within the cooldown MUST be rejected and display a countdown to when resend becomes available.
- **FR-009**: A resend action MUST generate a new OTP, invalidate the previous OTP, and restart the 10-minute validity window.
- **FR-010**: Navigation away from or closing of the OTP entry screen MUST invalidate the pending OTP; direct URL access to the OTP screen without a pending session state MUST redirect to the admin login screen.
- **FR-011**: Successful OTP entry MUST establish the admin session and redirect to the admin dashboard.
- **FR-012**: All credential validation error messages MUST be generic ("Invalid email or password") — the system MUST NOT disclose whether the email exists, the account status, or any other account detail.
- **FR-013**: The OTP generation and validation logic MUST follow the same rules used for customer login OTP: 6-digit code, 10-minute expiry, 5-attempt invalidation, 60-second resend cooldown.
- **FR-014**: The admin OTP entry screen MUST use the admin visual layout (not the storefront layout).
- **FR-015**: The OTP entry screen MUST display a generic confirmation message with no partial email address disclosed (e.g., "We've sent a verification code to your registered email address") — consistent with the no-account-detail-disclosure principle.

### Key Entities

- **Admin OTP**: A time-limited, single-use 6-digit code linked to an admin user record; shares the same data structure as the customer login OTP; attributes include: code, user reference, expiry timestamp, attempt count, and creation timestamp.
- **Admin Session**: Established only after both credential validation and OTP verification succeed; uses the existing admin session mechanism; partial authentication state (credentials verified, OTP pending) MUST NOT grant admin access.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: An admin with valid credentials and inbox access can complete the full two-step login in under 3 minutes.
- **SC-002**: An incorrect OTP entry returns the error message and re-enables the form in under 2 seconds.
- **SC-003**: After 5 incorrect OTP attempts, the admin is returned to the login screen within 1 page transition with a visible error message.
- **SC-004**: No OTP email is sent for any credential submission that fails validation — verifiable by inspecting the mail log after submitting invalid credentials.
- **SC-005**: The OTP entry screen is unreachable without a valid pending credential state in session — direct URL navigation to the OTP screen without prior credential submission redirects to the admin login screen.

## Assumptions

- The existing OTP data model (`otps` table) and OTP generation code used for customer login are reusable without schema changes; if a schema change is required, it will be resolved during planning.
- The admin email address is the address stored in the `users` table for the admin account — no separate contact email is required.
- No "remember this device" or OTP bypass mechanism is in scope — every admin login session always requires OTP verification.
- The admin OTP email may share the customer OTP email template with appropriate subject and header text (e.g., "Admin Panel Login Verification").
- This feature adds one new admin screen (admin OTP entry, analogous to S9 for customers) beyond the original 41-screen count; this is an accepted scope extension for this feature.
- Account lockout rules from existing specification (5 failed credential attempts, 15-minute lockout, per-account) apply to the credential step and are independent of the OTP attempt limit.
