<!--
## Sync Impact Report

**Version Change**: (new) → 1.0.0

**Principles Defined (initial)**:
- I. Standard Laravel MVC
- II. Specification Fidelity
- III. Security by Convention
- IV. Scope Discipline
- V. UI Consistency
- VI. Task Execution Discipline

**Sections Added**:
- Core Principles (6 principles)
- Stack Constraints
- Task Execution Workflow
- Governance

**Templates Reviewed**:
- `.specify/templates/plan-template.md` ✅ aligned (Constitution Check gate present; path conventions are generic — TechBits implementation will override in plan.md)
- `.specify/templates/spec-template.md` ✅ aligned (user story + acceptance scenario structure matches SCOPE.md workflow)
- `.specify/templates/tasks-template.md` ✅ aligned (phase structure matches task discipline principle)

**Deferred TODOs**: None.
-->

# TechBits Constitution

## Core Principles

### I. Standard Laravel MVC

Every feature MUST follow the standard Laravel MVC pattern: routes → controllers → models →
Blade views. No additional architectural layers (repositories, service classes, domain objects)
are permitted unless Eloquent or a standard Laravel pattern genuinely cannot express the
requirement. Complexity must be justified — the simplest working implementation is preferred.

- Use Laravel Eloquent for all database interactions; raw SQL is only permitted where Eloquent
  cannot express the query.
- Use Laravel migrations for all schema changes — never modify the database directly.
- Use Laravel Form Requests for all input validation — never trust client-side validation alone.
- Use Laravel's Mail facade for outgoing email — no third-party email packages unless explicitly
  instructed.
- Store uploaded images in `storage/app/public` via Laravel's storage system with a symlink —
  no external CDN.
- Use Laravel's session for cart state (guests) and authenticated sessions; use
  database-backed sessions in production configuration.

### II. Specification Fidelity

`SCOPE.md` and `UI.md` are the authoritative source of truth for the entire project. Every
implementation decision MUST be traceable to one of these files.

- `SCOPE.md` governs functional requirements, business rules, user flows, data rules, and
  permissions.
- `UI.md` governs screen layouts, visual design, component conventions, and responsive behaviour.
- When implementing any feature, BOTH files MUST be consulted and BOTH MUST be satisfied.
- If `SCOPE.md` and `UI.md` conflict, the conflict MUST be flagged explicitly — never silently
  resolve in favour of one file.
- If a business rule in `SCOPE.md` conflicts with a standard Laravel pattern, follow `SCOPE.md`
  and note the deviation in the task summary.

### III. Security by Convention

The application MUST be built with standard, conventional Laravel security practices. No
deliberate vulnerabilities MUST be introduced, and no hardening beyond what a standard Laravel
build includes is required. This principle exists because the project will be scanned for
vulnerabilities after build.

- Use Laravel's bcrypt password hashing — never store plain-text passwords.
- Use CSRF protection on all forms (Laravel default — do not disable).
- Use Blade `{{ }}` escaping for all output — never use `{!! !!}` unless rendering
  trusted internal content (e.g., pre-sanitised admin-controlled HTML).
- Never store CVV — store only last 4 digits of card, cardholder name, and expiry per
  SCOPE.md §10.7.
- OTP and reset tokens MUST be single-use and time-limited per SCOPE.md §10.1.
- Account lockout MUST trigger after 5 failed login attempts with a 15-minute lockout, enforced
  per-account (not per-IP), per SCOPE.md §10.1.
- Auth failure messages MUST be generic — never disclose account existence or status per
  SCOPE.md §2.6.
- The contact form intentionally has no rate limiting — this is a deliberate scanner finding per
  SCOPE.md §10.12; do not add rate limiting to it.
- Middleware MUST enforce role on every protected route — no security by obscurity.

### IV. Scope Discipline

The project comprises exactly 41 screens: S1–S30 (storefront, auth, account, checkout, static)
and A1–A11 (admin). No screen outside this list MUST ever be created.

The following are permanently out of scope — never implement them:
- Product reviews, ratings, wishlist, comparison, or recommendations.
- Discount codes, promotions, loyalty points, or gift cards.
- Real payment gateway — payment is always simulated (format validation only, always succeeds).
- SMS, push notifications, or in-app notification centre.
- Multi-vendor, sub-categories, or product variants.
- Stock reservation while items sit in the cart.
- Invoice PDF generation.
- Bulk import/export.
- Customer self-service account deletion.
- Any language other than English, any currency other than LKR, or delivery outside Sri Lanka.
- Dark mode.

### V. UI Consistency

All Blade views MUST conform to the visual conventions defined in `UI.md`. These are
non-negotiable and apply to every screen.

- Light theme only — no dark mode.
- Single clean sans-serif font family throughout.
- Primary colour: tech-blue. Accent: amber/orange. Neutrals: white/light-grey surfaces,
  dark-grey text.
- Every storefront page MUST include the storefront header, footer, and academic disclaimer:
  *"TechBits is an academic project. No real transactions are processed."*
- Admin pages MUST use a separate admin layout (left side nav + top bar) — no public footer.
- Status badges MUST always be pill-shaped, colour-coded, and always include text — never
  colour alone.
- One primary action per screen, rendered as a filled blue button.
- Currency format: `LKR 12,500` (no decimals, comma thousand-separators) — everywhere,
  no exceptions.
- Customer-facing date format: `27 May 2026`. Admin/order date+time: `27 May 2026, 14:35`
  (24-hour, Asia/Colombo timezone).

### VI. Task Execution Discipline

Implementation MUST proceed one task at a time from the approved plan. Scope expansion beyond
the current task is not permitted.

- After each task, provide a summary of: files changed, checks run, and follow-up risks.
- Do not implement features, refactor surrounding code, or add abstractions beyond what the
  task requires.
- Never add error handling, fallbacks, or validation for scenarios that cannot happen within
  this project's defined scope.
- Write no comments unless the *why* is non-obvious (a hidden constraint, a subtle invariant,
  a workaround for a specific bug). Never write comments that describe *what* the code does.

## Stack Constraints

These technology choices are non-negotiable. Any deviation requires an explicit amendment to
this constitution before implementation begins.

| Layer        | Technology                                      |
|--------------|------------------------------------------------|
| Backend      | Laravel (latest stable), PHP                   |
| Database     | MariaDB                                        |
| Frontend     | Laravel Blade (server-rendered), standard CSS/JS |
| Auth         | Laravel's built-in session auth                |
| Architecture | Full server-rendered pages — no SPA, no React, Vue, Inertia, or Livewire |

Do not use Breeze, Jetstream, or Sanctum unless a specific feature explicitly requires it.

## Routing & Access Control

- Storefront routes: `/` prefix — public, accessible to Guest and Customer roles.
- Admin routes: `/admin/*` prefix — admin-only, not linked from public navigation.
- Three roles: Guest, Customer, Admin. No role overlap. Admin is not a customer. Full
  definitions in SCOPE.md §5.
- Guest cart MUST merge into account cart on login, with quantities summed and capped per
  SCOPE.md §10.5.

## Governance

This constitution supersedes all other practices and conventions for the TechBits project.
All implementation decisions must be reconcilable with its principles.

**Amendment procedure**: Any amendment requires updating this file with a new version, updating
the Sync Impact Report comment, and verifying that all dependent templates remain consistent.
Non-trivial amendments (new principles, principle removals) require re-running the constitution
command before implementation continues.

**Versioning policy**:
- MAJOR: Backward-incompatible principle removals or redefinitions.
- MINOR: New principle or section added, or materially expanded guidance.
- PATCH: Clarifications, wording fixes, non-semantic refinements.

**Compliance**: Every implementation task MUST pass the Constitution Check gate in `plan.md`
before Phase 0 research begins. Re-check after Phase 1 design.

**Version**: 1.0.0 | **Ratified**: 2026-05-31 | **Last Amended**: 2026-05-31
