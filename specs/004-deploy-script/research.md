# Research: Deployment Script

**Branch**: `004-deploy-script` | **Date**: 2026-07-22

## Decision 1 — File sync tool: rsync

**Decision**: Use `rsync` over SSH for file transfer.

**Rationale**: `rsync` sends only changed files (delta sync), making repeated deploys fast. It natively supports exclusion patterns (`.env`, `storage/`, `vendor/`, `.git/`, `node_modules/`) via `--exclude`. It shows transfer progress, preserves timestamps and permissions, and is available by default on Linux/macOS developer machines and Linux servers.

**Alternatives considered**: `scp` — rejected because it always transfers full files, has no native exclude support, and is slower for incremental updates. `git push + git pull on server` — rejected because the user confirmed the deployment model is local→remote sync, not server-side git.

---

## Decision 2 — Error handling strategy: `set -euo pipefail` + ERR trap

**Decision**: Use `set -euo pipefail` at the top of the script, combined with an `on_error` trap function that runs on any command failure.

**Rationale**: `set -e` causes the script to exit immediately on any non-zero return code, satisfying FR-008. `set -u` catches undefined variable references. `set -o pipefail` ensures pipe failures aren't silently ignored. The ERR trap enables cleanup (bringing the app out of maintenance mode, FR-011) and prints which step failed, satisfying US3/FR-009.

**Alternatives considered**: Manual `|| { echo "failed"; exit 1; }` after every command — rejected because it's verbose and easy to miss. No error handling — rejected because FR-008 requires immediate halt on failure.

---

## Decision 3 — Remote command execution: single SSH session via heredoc

**Decision**: Run all remote commands in a single SSH call using a heredoc (`ssh user@host bash -s << 'EOF' ... EOF`). Each logical step is a function call within the heredoc.

**Rationale**: A single SSH connection is faster than one SSH call per step. The heredoc passes the full remote script in one go. `set -e` inside the heredoc ensures the remote side also halts on failure. The exit code propagates back to the local script, so the local ERR trap fires correctly.

**Alternatives considered**: Multiple `ssh user@host "command"` calls — rejected because each call opens/closes a TCP connection (slower) and sharing state (e.g., the `STEP` variable) between calls requires workarounds.

---

## Decision 4 — Database seeders: opt-in config flag

**Decision**: Add a `RUN_SEEDS` variable at the top of the script (default `false`). When set to `true`, `php artisan db:seed --force` runs after migrations.

**Rationale**: Seeders are typically idempotent in development but can insert duplicate data in production if run on every deploy. Making seeding opt-in (flip one variable before running) gives the developer explicit control without requiring script edits.

**Alternatives considered**: Always run seeders — rejected because production re-seeding can corrupt data. Never include seeders — rejected because the user explicitly requested seeder support.

---

## Decision 5 — Script installation: `~/.local/bin/update-tech-live`

**Decision**: Install the script to `/home/nissanka/.local/bin/update-tech-live` with execute permission (`chmod +x`). The `~/.local/bin` directory is on `$PATH` by default on modern Ubuntu, Fedora, and Debian systems (added by `.profile`/`.bashrc`), so the command `update-tech-live` becomes available system-wide for the user without `sudo`.

**Rationale**: `~/.local/bin` is the standard XDG user-local binary directory. No root access needed. The script name matches the user's stated command name.

**Alternatives considered**: `/usr/local/bin` (requires sudo) — rejected. `~/bin` (older convention, less universal) — rejected in favour of the XDG standard.

---

## Decision 6 — Maintenance mode: Laravel `artisan down` / `artisan up`

**Decision**: Use `php artisan down` at the start of the remote steps and `php artisan up` at the end. The ERR trap always runs `php artisan up` before exiting on failure.

**Rationale**: This satisfies FR-010 and FR-011. Laravel's maintenance mode returns a 503 page to users during the deploy window rather than serving broken/half-updated code. The trap guarantees the app does not stay in maintenance mode if any step fails.

**Alternatives considered**: No maintenance mode — rejected because migrating the database while the app is serving live traffic can cause errors. Zero-downtime atomic swap (symlink approach) — rejected as over-engineering for a single-server academic deployment.

---

## Decision 7 — Composer and PHP path on remote

**Decision**: Call `composer` and `php` by name (no absolute path), relying on them being in the remote user's `$PATH`.

**Rationale**: The spec states the server is already set up with PHP, Composer, and a working application. If `composer` or `php` were not in `$PATH`, initial setup would have failed. Hardcoding paths (e.g., `/usr/bin/php8.3`) creates fragility when the server PHP version changes.

**Alternatives considered**: Absolute paths — rejected (fragile). Detecting the path at runtime — rejected (unnecessary complexity for a well-provisioned server).

---

## Deployment Step Order

```
[local]  rsync → remote (excluding .env, storage/, vendor/, .git/, node_modules/)
[remote] php artisan down
[remote] composer install --no-dev --optimize-autoloader
[remote] php artisan migrate --force
[remote] php artisan db:seed --force          ← only if RUN_SEEDS=true
[remote] php artisan config:cache
[remote] php artisan route:cache
[remote] php artisan view:cache
[remote] php artisan event:cache
[remote] chmod -R ug+rwx storage/ bootstrap/cache/
[remote] php artisan up
```

**Why maintenance mode comes after rsync**: Syncing files while the app is live is safe because PHP files are not executed during transfer — the web server serves them atomically per-request. Maintenance mode is activated before composer/migrate, which are the operations that can leave the app in a broken mid-state.

---

## Change Inventory

Files to **create** (1):

| File | Purpose |
|---|---|
| `/home/nissanka/.local/bin/update-tech-live` | The deployment script |

No application source files are modified by this feature — the script is standalone operational tooling outside the Laravel project.
