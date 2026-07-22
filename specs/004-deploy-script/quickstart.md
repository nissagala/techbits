# Quickstart: Deployment Script

**Branch**: `004-deploy-script`

Run these scenarios after implementation to verify the script works end-to-end.

---

## Scenario 1 — Script is on PATH and executable

```bash
which update-tech-live
# Expected: /home/nissanka/.local/bin/update-tech-live

update-tech-live --help 2>&1 || update-tech-live
# Expected: script starts (or shows usage), not "command not found"
```

---

## Scenario 2 — Successful deploy (happy path)

1. Make a small visible change locally (e.g., edit a Blade view to add a comment or change a word).
2. Run:
   ```bash
   update-tech-live
   ```
3. **Expected terminal output** (in order):
   ```
   [→] Syncing files to remote server...
   [✓] Files synced
   [→] Enabling maintenance mode...
   [✓] Maintenance mode ON
   [→] Installing dependencies...
   [✓] Dependencies installed
   [→] Running database migrations...
   [✓] Migrations complete
   [→] Rebuilding application cache...
   [✓] Cache rebuilt
   [→] Setting file permissions...
   [✓] Permissions set
   [→] Bringing application online...
   [✓] Deployment complete. App is live.
   ```
4. Visit the live site — confirm the change is visible.
5. SSH into remote and confirm `.env` is unchanged: `ssh techlive@47.131.59.99 "cat /home/nginx/sites/techbits_live/.env | head -3"`

---

## Scenario 3 — Server `.env` is never overwritten

1. SSH into remote, note a value in `.env`:
   ```bash
   ssh techlive@47.131.59.99 "grep APP_KEY /home/nginx/sites/techbits_live/.env"
   ```
2. Run `update-tech-live`.
3. SSH in again and confirm the same `APP_KEY` value — it must not change.

---

## Scenario 4 — `storage/` directory is never overwritten

1. SSH into remote, create a marker file:
   ```bash
   ssh techlive@47.131.59.99 "touch /home/nginx/sites/techbits_live/storage/marker-test.txt"
   ```
2. Run `update-tech-live`.
3. Confirm marker still exists:
   ```bash
   ssh techlive@47.131.59.99 "ls /home/nginx/sites/techbits_live/storage/marker-test.txt"
   ```
4. Clean up: `ssh techlive@47.131.59.99 "rm /home/nginx/sites/techbits_live/storage/marker-test.txt"`

---

## Scenario 5 — App exits maintenance mode on failure

1. SSH into remote, temporarily break a migration (e.g., rename migrations table):
   ```bash
   ssh techlive@47.131.59.99 "cd /home/nginx/sites/techbits_live && php artisan tinker --execute=\"DB::statement('RENAME TABLE migrations TO migrations_bak');\""
   ```
2. Run `update-tech-live`.
3. **Expected**: Script prints `[✗] Deployment FAILED at step: Running database migrations...` and then brings app back online.
4. Confirm app is accessible (not stuck in maintenance mode): visit the live site — should not show maintenance page.
5. Restore: `ssh techlive@47.131.59.99 "cd /home/nginx/sites/techbits_live && php artisan tinker --execute=\"DB::statement('RENAME TABLE migrations_bak TO migrations');\""` then run `update-tech-live` again.

---

## Scenario 6 — Run seeders (opt-in)

1. Open `/home/nissanka/.local/bin/update-tech-live` and temporarily set `RUN_SEEDS=true`.
2. Run `update-tech-live`.
3. **Expected**: Terminal output includes a `[→] Running database seeders...` step after migrations.
4. Reset `RUN_SEEDS=false`.

---

## Scenario 7 — No changes (idempotent deploy)

1. Run `update-tech-live` twice in a row without making any local changes.
2. **Expected**: Both runs complete without error. Second run shows `rsync` reporting 0 files transferred but all remote steps still execute successfully.

---

## Scenario 8 — Configuration is at top of file

1. Open `/home/nissanka/.local/bin/update-tech-live`.
2. Confirm all four config values (`REMOTE_USER`, `REMOTE_HOST`, `REMOTE_PATH`, `LOCAL_PATH`) appear in a clearly labelled block at the top of the file, before any functions or logic.
3. Confirm changing `REMOTE_HOST` to a dummy value and running the script produces a connection error (not a script crash from undefined variable).
4. Restore the correct value.
