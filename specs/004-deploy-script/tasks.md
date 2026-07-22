# Tasks: Deployment Script

**Input**: Design documents from `specs/004-deploy-script/`

**Organization**: Tasks are grouped by user story to enable independent implementation and testing.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to

---

## Phase 1: Setup

**Purpose**: Confirm prerequisites and prepare the script file.

- [x] T001 Verify `~/.local/bin` exists and is on `$PATH` — run `echo $PATH | grep -o "$HOME/.local/bin"` and `ls ~/.local/bin 2>/dev/null`; if directory missing, create it with `mkdir -p ~/.local/bin`; if not on PATH, add `export PATH="$HOME/.local/bin:$PATH"` to `~/.bashrc` and `~/.profile`

---

## Phase 2: Foundational — Script Skeleton

**Purpose**: Create the script file with configuration block, helper functions, and error-handling wiring. All user story phases depend on this.

**⚠️ CRITICAL**: T002–T005 must all complete before any verification tasks.

- [x] T002 Create `/home/nissanka/.local/bin/update-tech-live` with the shebang line `#!/usr/bin/env bash`, `set -euo pipefail`, and the configuration block at the top:
  ```
  REMOTE_USER="techlive"
  REMOTE_HOST="47.131.59.99"
  REMOTE_PATH="/home/nginx/sites/techbits_live"
  LOCAL_PATH="/home/nissanka/MSC/techbits"
  RUN_SEEDS=false
  ```

- [x] T003 Add the `log_step()` helper and `on_error` ERR trap to `/home/nissanka/.local/bin/update-tech-live`:
  - `log_step()` prints a labelled progress line (`[→] $1...`) and sets `CURRENT_STEP="$1"`
  - `on_error` trap prints `[✗] Deployment FAILED at step: ${CURRENT_STEP}`, attempts `ssh "${REMOTE_USER}@${REMOTE_HOST}" "cd '${REMOTE_PATH}' && php artisan up 2>/dev/null || true"`, then exits 1
  - Wire trap: `trap on_error ERR`

- [x] T004 Add the `run_remote()` helper to `/home/nissanka/.local/bin/update-tech-live`: function that accepts a command string and executes it on the remote server via `ssh "${REMOTE_USER}@${REMOTE_HOST}" "cd '${REMOTE_PATH}' && $1"`

- [x] T005 Make the script executable: `chmod +x /home/nissanka/.local/bin/update-tech-live`; verify with `ls -la /home/nissanka/.local/bin/update-tech-live` — expect `-rwxr-xr-x`

**Checkpoint**: Run `update-tech-live` — script should start without "command not found" and, since no main body exists yet, exit cleanly (or with a "no steps defined" message).

---

## Phase 3: User Story 1 — Sync Local Code to Remote Server (Priority: P1) 🎯 MVP

**Goal**: Running `update-tech-live` transfers files from `/home/nissanka/MSC/techbits` to the remote server and runs all post-deploy steps.

**Independent Test**: Make a small local text change, run `update-tech-live`, verify the change is live on the remote server without any manual SSH commands.

- [x] T006 [US1] Add the `rsync` file-sync step to the main body of `/home/nissanka/.local/bin/update-tech-live`:
  ```bash
  log_step "Syncing files to remote server"
  rsync -az --delete \
    --exclude='.env' \
    --exclude='storage/' \
    --exclude='vendor/' \
    --exclude='.git/' \
    --exclude='node_modules/' \
    --info=progress2 \
    "${LOCAL_PATH}/" \
    "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/"
  echo "[✓] Files synced"
  ```

- [x] T007 [US1] Add the maintenance mode ON step after the rsync block in `/home/nissanka/.local/bin/update-tech-live`:
  ```bash
  log_step "Enabling maintenance mode"
  run_remote "php artisan down"
  echo "[✓] Maintenance mode ON"
  ```

- [x] T008 [US1] Add the `composer install` step after maintenance mode in `/home/nissanka/.local/bin/update-tech-live`:
  ```bash
  log_step "Installing dependencies"
  run_remote "composer install --no-dev --optimize-autoloader"
  echo "[✓] Dependencies installed"
  ```

- [x] T009 [US1] Add the `php artisan migrate` and optional `db:seed` steps after composer in `/home/nissanka/.local/bin/update-tech-live`:
  ```bash
  log_step "Running database migrations"
  run_remote "php artisan migrate --force"
  echo "[✓] Migrations complete"

  if [ "${RUN_SEEDS}" = "true" ]; then
    log_step "Running database seeders"
    run_remote "php artisan db:seed --force"
    echo "[✓] Seeders complete"
  fi
  ```

- [x] T010 [US1] Add the cache rebuild steps after migrations in `/home/nissanka/.local/bin/update-tech-live`:
  ```bash
  log_step "Rebuilding application cache"
  run_remote "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache"
  echo "[✓] Cache rebuilt"
  ```

- [x] T011 [US1] Add the permissions step and maintenance mode OFF step as the final two steps in `/home/nissanka/.local/bin/update-tech-live`:
  ```bash
  log_step "Setting file permissions"
  run_remote "chmod -R ug+rwx storage/ bootstrap/cache/"
  echo "[✓] Permissions set"

  log_step "Bringing application online"
  run_remote "php artisan up"
  echo ""
  echo "[✓] Deployment complete. App is live."
  ```

- [x] T012 [US1] Verify US1 — run `update-tech-live` and confirm:
  - (a) all labelled progress lines print in the correct order
  - (b) the deployment completes without error
  - (c) make a visible local change (e.g., add a comment to a Blade view), re-run, confirm the change appears on the remote server
  - (d) confirm `.env` on remote is unchanged: `ssh techlive@47.131.59.99 "stat /home/nginx/sites/techbits_live/.env"`

**Checkpoint**: User Story 1 complete — one command deploys all code changes end-to-end.

---

## Phase 4: User Story 2 — Deployment Failure Leaves Application Intact (Priority: P2)

**Goal**: Any step failure stops the deploy, prints the failed step, and restores the app from maintenance mode.

**Independent Test**: Deliberately break something (e.g., rename migrations table), run the script, confirm the live app still responds and maintenance mode is not stuck ON.

- [x] T013 [US2] Verify the ERR trap restores maintenance mode on failure:
  - (a) SSH into remote and temporarily break migrations: `ssh techlive@47.131.59.99 "cd /home/nginx/sites/techbits_live && php artisan tinker --execute=\"DB::statement('RENAME TABLE migrations TO migrations_bak');\""` 
  - (b) run `update-tech-live` — expected: deploy fails at migrate step, prints `[✗] Deployment FAILED at step: Running database migrations`, then confirms app restored from maintenance mode
  - (c) verify the live site is accessible (not showing maintenance page)
  - (d) restore: `ssh techlive@47.131.59.99 "cd /home/nginx/sites/techbits_live && php artisan tinker --execute=\"DB::statement('RENAME TABLE migrations_bak TO migrations');\""`

**Checkpoint**: User Story 2 confirmed — failures are safe and visible.

---

## Phase 5: User Story 3 — Deployment Progress is Visible (Priority: P3)

**Goal**: Every major step emits a labelled line; success prints a final summary; failure names the step.

**Independent Test**: Run the script and read the terminal output — every step must be clearly identified.

- [x] T014 [US3] Verify all quickstart.md scenarios pass:
  - Scenario 1 — `which update-tech-live` returns correct path
  - Scenario 2 — full happy-path deploy with visible change, terminal shows all `[→]`/`[✓]` lines
  - Scenario 3 — `.env` not overwritten after deploy
  - Scenario 4 — `storage/marker-test.txt` survives a deploy (create before, check after)
  - Scenario 6 — temporarily set `RUN_SEEDS=true`, confirm seeder step appears in output, reset to `false`
  - Scenario 7 — two back-to-back deploys both succeed

**Checkpoint**: All three user stories verified.

---

## Phase 6: Polish

- [x] T015 Add a usage/help block at the top of `/home/nissanka/.local/bin/update-tech-live` (as a comment header) documenting: what the script does, the config variables, how to enable seeders, and the remote server details (IP and path only — no credentials)
- [x] T016 Run `bash -n /home/nissanka/.local/bin/update-tech-live` to syntax-check the script — expect 0 errors; fix any reported issues

---

## Dependencies & Execution Order

### Phase Dependencies

- **Phase 1 (Setup)**: No dependencies — start immediately
- **Phase 2 (Foundational)**: Depends on Phase 1 — BLOCKS all user story phases
- **Phase 3 (US1)**: Depends on Phase 2 — T006–T011 sequential (same file); T012 depends on T006–T011
- **Phase 4 (US2)**: Depends on Phase 3 checkpoint — verification only; requires working script
- **Phase 5 (US3)**: Depends on Phase 3 checkpoint — can run alongside Phase 4
- **Phase 6 (Polish)**: Depends on Phases 3–5 completing

### Within the Script File

T002–T011 all modify `/home/nissanka/.local/bin/update-tech-live` — run them sequentially:
- T002: shebang + config block
- T003: helper functions + ERR trap
- T004: `run_remote()` helper
- T005: `chmod +x`
- T006: rsync step
- T007: maintenance mode ON
- T008: composer
- T009: migrate + optional seed
- T010: cache rebuild
- T011: permissions + maintenance mode OFF

### Parallel Opportunities

```
Phase 1: T001
     ↓
Phase 2: T002 → T003 → T004 → T005  ← sequential (same file)
     ↓
Phase 3: T006 → T007 → T008 → T009 → T010 → T011 → T012  ← sequential (same file)
     ↓
Phase 4 (T013) + Phase 5 (T014)  ← can run in parallel after Phase 3 checkpoint
     ↓
Phase 6: T015 + T016  ← T015 and T016 can run in parallel
```

---

## Implementation Strategy

### MVP (US1 only)

1. Phase 1: T001 — confirm `~/.local/bin` is on PATH
2. Phase 2: T002–T005 — create script skeleton
3. Phase 3: T006–T012 — add all deployment steps + verify
4. **STOP and validate** — deployment fully functional

### Full delivery

Complete Phases 1–6 sequentially. Total estimate: ~30 minutes.
