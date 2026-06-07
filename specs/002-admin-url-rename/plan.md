# Implementation Plan: Admin URL Rename

**Branch**: `002-admin-url-rename` | **Date**: 2026-06-07 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/002-admin-url-rename/spec.md`

## Summary

Rename the admin URL prefix from `/admin` to `/tb-backroom-engine` across all route registration, middleware path checks, and documentation. No functional behaviour changes. Named routes remain unchanged so all `route()` helper calls in controllers and Blade views require no modification.

## Technical Context

**Language/Version**: PHP 8.4 / Laravel 11

**Primary Dependencies**: Laravel routing (`bootstrap/app.php`, `routes/admin.php`)

**Storage**: N/A — no schema changes

**Testing**: Manual browser verification + project-wide grep for `/admin`

**Target Platform**: Linux server (Nginx + PHP-FPM, SELinux enforcing)

**Project Type**: Laravel web application (full server-rendered MVC)

**Performance Goals**: N/A — routing change only

**Constraints**: Named routes (e.g., `admin.login`, `admin.dashboard`) must not change — only the URL path segment changes.

**Scale/Scope**: 2 PHP files changed, 4 documentation files updated

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|---|---|---|
| I. Standard Laravel MVC | ✅ PASS | Route prefix change via `bootstrap/app.php` is the standard Laravel pattern |
| II. Specification Fidelity | ✅ PASS | Change fully described in spec.md FR-001–FR-010 |
| III. Security by Convention | ✅ PASS | Old path returns 404 (no redirect); obscurity via non-guessable prefix |
| IV. Scope Discipline | ✅ PASS | Pure routing rename; zero functional change; zero new screens |
| V. UI Consistency | ✅ PASS | No view changes |
| VI. Task Execution Discipline | ✅ PASS | Minimal change set; no surrounding refactors |

**All gates pass.**

## Project Structure

### Documentation (this feature)

```text
specs/002-admin-url-rename/
├── plan.md              # This file
├── research.md          # Phase 0 output (see below — no unknowns)
└── tasks.md             # Phase 2 output (/speckit-tasks command)
```

### Source Code (files changed)

```text
bootstrap/app.php                                   ← prefix + is() path check
app/Http/Middleware/EnsureAdmin.php                 ← no change (uses route() helper)
app/Http/Middleware/RedirectIfAuthenticated.php     ← no change (uses route() helper)
.specify/memory/constitution.md                     ← Routing & Access Control section
specs/001-techbits-ecommerce-spec/contracts/admin.md ← all /admin/* route URLs
specs/001-techbits-ecommerce-spec/plan.md           ← admin prefix references
specs/001-techbits-ecommerce-spec/quickstart.md     ← admin URL in validation steps
SCOPE.md                                            ← /admin references (source of truth — informational update)
```

**Structure Decision**: Single-project Laravel app. Only `bootstrap/app.php` contains executable `/admin` path strings. All other changes are documentation.

## Phase 0: Research

No unknowns — all decisions are resolved in the spec.

### Findings

| Decision | Rationale |
|---|---|
| Change only `->prefix('admin')` in `bootstrap/app.php` | This is the single point of truth for the URL path; named routes derive from it |
| Also update `$request->is('admin/*')` in the 404 handler | This string match is a hardcoded path check, not a named route — must be kept in sync |
| No redirect from `/admin` → `/tb-backroom-engine` | FR-009 explicitly requires 404 for old path; a redirect would betray the obscurity intent |
| Named routes unchanged (`admin.login`, `admin.dashboard`, etc.) | Named routes are internal identifiers; renaming them would require changing all `route()` calls in 15+ controllers and views for zero benefit |
| Blade views require no changes | All admin views use `route()` helpers — confirmed by grep showing zero hardcoded `/admin` path strings in `resources/views/` |
| Middleware PHP files require no changes | `EnsureAdmin.php` and `RedirectIfAuthenticated.php` use `redirect()->route('admin.*')` — named routes, not paths |

## Phase 1: Design

### Complete Change Inventory

#### Executable code (must change for feature to work)

| File | Line(s) | Change |
|---|---|---|
| `bootstrap/app.php` | 17 | `->prefix('admin')` → `->prefix('tb-backroom-engine')` |
| `bootstrap/app.php` | 36 | `$request->is('admin/*')` → `$request->is('tb-backroom-engine/*')` |

#### Documentation (must change for accuracy)

| File | Change |
|---|---|
| `.specify/memory/constitution.md` | Line 155: `Admin routes: '/admin/*' prefix` → `'/tb-backroom-engine/*'` |
| `specs/001-techbits-ecommerce-spec/contracts/admin.md` | Replace all `/admin/` URL path strings with `/tb-backroom-engine/` |
| `specs/001-techbits-ecommerce-spec/plan.md` | Two references to `/admin` prefix |
| `specs/001-techbits-ecommerce-spec/quickstart.md` | Two references to admin URL (`/admin`) |
| `SCOPE.md` | Three informational `/admin` references (lines 264, 334, 623) |

#### Confirmed no-change files

| File | Reason |
|---|---|
| `routes/admin.php` | Routes defined relative to group prefix; no `/admin` path strings inside |
| `app/Http/Middleware/EnsureAdmin.php` | Uses `redirect()->route('admin.login')` |
| `app/Http/Middleware/RedirectIfAuthenticated.php` | Uses `redirect()->route('admin.dashboard')` |
| `app/Http/Middleware/EnsureCustomer.php` | No admin path references |
| All files in `resources/views/` | All admin links use `route()` helper — confirmed by grep |
| All files in `app/Http/Controllers/` | No hardcoded `/admin` path strings — confirmed by grep |

### Verification Command (post-implementation)

```bash
grep -rn "/admin" --include="*.php" --include="*.md" . \
  --exclude-dir=vendor --exclude-dir=.git \
  --exclude-dir="specs/002-admin-url-rename" \
  | grep -v "SCOPE.md" \
  | grep -v "specs/001-techbits-ecommerce-spec/tasks.md" \
  | grep -v "specs/001-techbits-ecommerce-spec/spec.md"
```

Expected result: zero matches in executable PHP files; only SCOPE.md and historical task/spec records (which are acceptable — they document what was built, not what to build).

> **Note on SCOPE.md**: SCOPE.md and UI.md are the project's original requirement documents — not generated artifacts. They are updated here for accuracy but are not authoritative for the running application. If SCOPE.md cannot be edited (e.g., academic submission constraint), skip those three lines; the application will still function correctly.

## Complexity Tracking

No constitution violations. No complexity justification required.
