# Tasks: Admin URL Rename

**Input**: Design documents from `specs/002-admin-url-rename/`

**Organization**: Tasks are grouped by user story to enable independent implementation and testing.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to

---

## Phase 1: Setup

**Purpose**: No project initialization required — this is a two-line code change.

- [x] T001 Confirm current state: run `grep -rn "prefix.*admin\|is.*admin" bootstrap/app.php` and verify two `/admin` occurrences exist before proceeding

---

## Phase 2: Foundational (Blocking Prerequisite)

**Purpose**: The single code change that makes all three user stories work.

**⚠️ CRITICAL**: Both T002 and T003 must complete before any verification.

- [x] T002 [US1] In `bootstrap/app.php` line 17: change `->prefix('admin')` to `->prefix('tb-backroom-engine')`
- [x] T003 [US1] In `bootstrap/app.php` line 36: change `$request->is('admin/*')` to `$request->is('tb-backroom-engine/*')` (keeps admin 404s from rendering the storefront error page)

**Checkpoint**: After T002 + T003, clear route cache: `php artisan route:clear && php artisan cache:clear`

---

## Phase 3: User Story 1 — Admin Accesses Panel via New URL (Priority: P1) 🎯 MVP

**Goal**: Admin panel is fully accessible at `/tb-backroom-engine/*`; all internal links and redirects work.

**Independent Test**: Browse to `http://techbits.test.local/tb-backroom-engine` → redirected to `/tb-backroom-engine/login` → log in as `admin@techbits.lk` / `Admin@1234` → dashboard loads → all sidebar nav links resolve to `/tb-backroom-engine/*` URLs.

- [x] T004 [US1] Verify `http://techbits.test.local/tb-backroom-engine` loads the admin login page (A1)
- [x] T005 [US1] Log in as admin and verify all A1–A11 admin screens load under the `/tb-backroom-engine/` prefix with no broken links or redirect errors
- [x] T006 [US1] Verify post-login redirect lands on `/tb-backroom-engine/dashboard` (not `/admin/dashboard`)

**Checkpoint**: User Story 1 fully functional — admin panel works at new URL.

---

## Phase 4: User Story 2 — Old Admin URL Returns 404 (Priority: P2)

**Goal**: `/admin/*` returns 404; no redirect to the new URL is provided.

**Independent Test**: Visit `http://techbits.test.local/admin` and `http://techbits.test.local/admin/dashboard` (logged in or out) — both return 404 with no redirect header.

- [x] T007 [US2] Verify `http://techbits.test.local/admin` returns a 404 response (no redirect)
- [x] T008 [US2] Verify `http://techbits.test.local/admin/dashboard` returns a 404 response even while logged in as admin

**Checkpoint**: User Story 2 confirmed — old path is a dead end.

---

## Phase 5: User Story 3 — Customer and Guest Flows Unaffected (Priority: P3)

**Goal**: All storefront routes work normally; no `/admin` or `/tb-backroom-engine` strings appear in storefront-rendered HTML.

**Independent Test**: Browse home page, product listing, cart, and account pages as a guest and as a logged-in customer — all work normally and no admin URL strings appear in page source.

- [x] T009 [US3] Verify storefront home `http://techbits.test.local/` loads correctly
- [x] T010 [US3] Verify customer account `/account` loads for a logged-in customer with no admin URL references in rendered HTML

**Checkpoint**: User Story 3 confirmed — zero regressions on storefront.

---

## Phase 6: Polish — Documentation Updates

**Purpose**: Update all documentation files that reference `/admin` as the admin URL prefix.

- [x] T011 [P] In `.specify/memory/constitution.md` line 155: change `Admin routes: '/admin/*' prefix` to `Admin routes: '/tb-backroom-engine/*' prefix`
- [x] T012 [P] In `specs/001-techbits-ecommerce-spec/contracts/admin.md`: replace all `/admin/` path strings with `/tb-backroom-engine/` (affects the header line and all endpoint URL examples)
- [x] T013 [P] In `specs/001-techbits-ecommerce-spec/plan.md`: update the two `/admin` prefix references (lines ~248 and ~256)
- [x] T014 [P] In `specs/001-techbits-ecommerce-spec/quickstart.md`: update the two admin URL references (`/admin` → `/tb-backroom-engine`)
- [x] T015 [P] In `SCOPE.md`: update the three informational `/admin` references (lines ~264, ~334, ~623)
- [x] T016 Run final verification grep: `grep -rn "/admin" --include="*.php" . --exclude-dir=vendor --exclude-dir=.git` — expected result: zero matches in any PHP file

---

## Dependencies & Execution Order

### Phase Dependencies

- **Phase 1 (Setup)**: No dependencies — start immediately
- **Phase 2 (Foundational)**: Depends on Phase 1 — BLOCKS all user story verification
- **Phase 3 (US1)**: Depends on Phase 2
- **Phase 4 (US2)**: Depends on Phase 2; can run in parallel with Phase 3
- **Phase 5 (US3)**: Depends on Phase 2; can run in parallel with Phase 3 and 4
- **Phase 6 (Polish)**: Independent of Phases 3–5; all T011–T015 are [P] parallelizable

### Parallel Opportunities

```
Phase 2 completes
       ↓
Phase 3 + Phase 4 + Phase 5  ← all run in parallel
       ↓
Phase 6 (T011–T015 all in parallel)
       ↓
T016 final grep verification
```

---

## Implementation Strategy

### MVP (just US1)

1. Phase 1: T001 — confirm baseline
2. Phase 2: T002 + T003 — two-line code change
3. Phase 3: T004–T006 — verify admin panel works
4. **STOP and validate** — admin panel is fully functional at new URL

### Full delivery

Complete Phases 1–6 sequentially (or with parallelism in Phase 6). Total time estimate: ~15 minutes.

---

## Notes

- T002 and T003 are both in `bootstrap/app.php` — do them in sequence, not parallel
- After T002 + T003, always run `php artisan route:clear && php artisan cache:clear` before verification
- T011–T015 all touch different files — safe to do in parallel
- T016 grep is the acceptance test for FR-010 (SC-005 in spec)
