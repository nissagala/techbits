# Feature Specification: Admin URL Rename

**Feature Branch**: `002-admin-url-rename`

**Created**: 2026-06-07

**Status**: Draft

**Input**: User description: "Change request: Admin url should change from /admin to non guessable /tb-backroom-engine so it is less guessable. This will affect all the routes, middleware, redirects and hardcoded links in views, configs or CLAUDE.md. No functional change - Routing only"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Admin Accesses Panel via New URL (Priority: P1)

An administrator navigates to the renamed admin URL prefix. All admin screens, redirects, and internal links continue to work exactly as before — only the URL path segment changes.

**Why this priority**: Core deliverable. Every admin workflow depends on the URL prefix being correct.

**Independent Test**: Open `/tb-backroom-engine/dashboard` while logged in as admin — full admin panel is accessible and all navigation links use the new prefix.

**Acceptance Scenarios**:

1. **Given** an admin is not logged in, **When** they visit `/tb-backroom-engine/login`, **Then** the admin login page is displayed.
2. **Given** an admin is logged in, **When** they navigate to any `/tb-backroom-engine/*` route, **Then** the page loads correctly with all internal links pointing to `/tb-backroom-engine/*`.
3. **Given** an admin is logged in, **When** they perform an action that triggers a redirect (e.g., save a product), **Then** they are redirected to a `/tb-backroom-engine/*` URL, not `/admin/*`.

---

### User Story 2 - Old Admin URL Returns 404 (Priority: P2)

Any request to the old `/admin/*` URL prefix returns a 404 response. No redirect to the new URL is provided (security by obscurity — the old path should give no hint that a new path exists).

**Why this priority**: Security objective. If `/admin/*` silently redirects, the obscurity benefit is lost.

**Independent Test**: Attempt to visit `/admin/dashboard` (logged in or out) — the application returns a 404 page, not a redirect and not the admin panel.

**Acceptance Scenarios**:

1. **Given** any user (guest, customer, or admin), **When** they request `/admin` or any `/admin/*` path, **Then** a 404 response is returned.
2. **Given** an admin is logged in, **When** they are redirected after login, **Then** the redirect target is `/tb-backroom-engine/dashboard`, not `/admin/dashboard`.

---

### User Story 3 - Customer and Guest Flows Unaffected (Priority: P3)

All storefront routes (`/`, `/products`, `/cart`, `/checkout`, etc.) continue to function without any change. The rename is confined to the admin prefix only.

**Why this priority**: Regression guard — confirms the change is contained.

**Independent Test**: Complete a full guest browse → add to cart → login → checkout flow; all URLs remain on storefront paths with no `/tb-backroom-engine` or `/admin` leaking in.

**Acceptance Scenarios**:

1. **Given** a customer is logged in, **When** they visit `/account`, **Then** the page loads normally with no admin URL references.
2. **Given** a guest, **When** they browse the storefront, **Then** no page contains a link or reference to `/admin/*` or `/tb-backroom-engine/*`.

---

### Edge Cases

- What happens when a session holds a previously stored admin redirect URL with the old `/admin` prefix? — The old prefix no longer maps to any route, so the redirect will 404. Acceptable; sessions clear on logout.
- What happens if a hardcoded `/admin` link exists in a Blade view, email template, or config file? — It must be found and updated; any missed occurrence is a defect.
- What happens when the `CLAUDE.md` or constitution references the admin URL? — Both must be updated to reflect the new prefix so future AI-assisted development uses the correct path.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The route group prefix for all admin routes MUST change from `/admin` to `/tb-backroom-engine`.
- **FR-002**: All named admin routes (e.g., `admin.login`, `admin.dashboard`) MUST remain unchanged — only the URL path changes, not the route names.
- **FR-003**: All redirects within middleware (EnsureAdmin, RedirectIfAuthenticated, EnsureCustomer) that target admin paths MUST use the new prefix.
- **FR-004**: All `route()` helper calls in Blade views, controllers, and other PHP files that resolve to admin URLs MUST continue to work via the unchanged named routes (no hardcoded path strings need updating if `route()` is used throughout).
- **FR-005**: Any hardcoded `/admin` path strings (not using `route()`) found in views, controllers, config files, or documentation MUST be updated to `/tb-backroom-engine`.
- **FR-006**: The `CLAUDE.md` project instruction file MUST be updated to reference `/tb-backroom-engine` wherever it previously referenced `/admin` as the admin URL prefix.
- **FR-007**: The constitution file (`.specify/memory/constitution.md`) MUST be updated to reference `/tb-backroom-engine` in the Routing & Access Control section.
- **FR-008**: The project plan (`specs/001-techbits-ecommerce-spec/plan.md`) MUST be updated to reflect the new admin prefix in any URL references.
- **FR-009**: Requesting `/admin` or any `/admin/*` path MUST return a 404 — no redirect to the new URL.
- **FR-010**: No functional behaviour of the admin panel (authentication, CRUD operations, order management, redirects between admin screens) MUST change.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Zero admin routes are reachable via the `/admin/*` path — every request to `/admin/*` returns 404.
- **SC-002**: All admin screens are reachable via `/tb-backroom-engine/*` paths with no broken links or failed redirects.
- **SC-003**: All 11 admin screens (A1–A11) load correctly under the new prefix with no errors.
- **SC-004**: No storefront or customer-facing page contains any reference to `/admin` or `/tb-backroom-engine` in rendered HTML.
- **SC-005**: Running a project-wide search for the string `/admin` yields zero results in route files, middleware, controllers, Blade views, and documentation (excluding git history and this spec).

## Assumptions

- All admin route resolution in controllers and Blade views already uses Laravel's `route()` helper with named routes, so most URL generation requires no change — only the route group prefix definition needs updating.
- Named routes (e.g., `admin.login`, `admin.dashboard`) are not changing — the prefix change is purely at the route registration level.
- No external system (email templates with hardcoded URLs, third-party integrations) references the admin URL, as the application is self-contained.
- No redirect from `/admin` → `/tb-backroom-engine` is desired; a clean 404 is the correct outcome for the old path.
- The change is applied to the existing `master` branch codebase; no database schema changes are required.
