# Feature Specification: Deployment Script

**Feature Branch**: `004-deploy-script`

**Created**: 2026-07-22

**Status**: Draft

**Input**: User description: "Need to create a bash script to deploy this laravel application to a remote server. Initial setup is already completed. Needs to send updates getting from Git repo"

## Clarifications

### Session 2026-07-22

- Q: How does the remote server authenticate to the Git repository to pull updates? → A: No git pull on server. Source code lives on the developer's local machine at `/home/nissanka/MSC/techbits`. The script syncs files directly from local to remote server, then runs post-deploy steps (migrate, cache, permissions) remotely over SSH.
- Q: Should the script reload the PHP-FPM service after deployment to flush opcache? → A: No — skip PHP-FPM reload; Laravel's cache clear commands are sufficient for this setup.

## User Scenarios & Testing *(mandatory)*

### User Story 1 — Sync Local Code to Remote Server (Priority: P1)

A developer makes code changes on their local machine, runs the deployment script, and the remote server is automatically updated with the new files. Dependencies are updated, the database is migrated, and the application caches are refreshed — all without any manual remote commands.

**Why this priority**: This is the core purpose of the script. Every other story depends on files reaching the server correctly.

**Independent Test**: Make a small visible code change locally (e.g., a text tweak in a Blade view). Run the script. Verify the change appears on the live server without any manual SSH or server-side commands.

**Acceptance Scenarios**:

1. **Given** the developer has made local code changes, **When** they run `./deploy.sh`, **Then** the changed files are transferred to the remote server, dependencies are updated, pending migrations run, caches are rebuilt, and the updated application is accessible to users within 3 minutes.
2. **Given** no files have changed locally since the last deploy, **When** the developer runs `./deploy.sh`, **Then** the file sync reports nothing transferred, subsequent steps still run to completion, and no errors are thrown.
3. **Given** the remote server is unreachable (network issue), **When** the developer runs `./deploy.sh`, **Then** the script exits immediately with a clear error message and nothing on the remote server is changed.

---

### User Story 2 — Deployment Failure Leaves Application Intact (Priority: P2)

If any step in the deployment process fails (e.g., a migration error, a broken dependency), the running application continues serving users and the developer receives a clear error message identifying the failure point.

**Why this priority**: Broken deployments that take down the live application are worse than no deployment at all.

**Independent Test**: Introduce a deliberately broken migration locally, sync it, and verify the live application still responds while the script reports the migration error.

**Acceptance Scenarios**:

1. **Given** a migration fails during deployment, **When** the script encounters the error, **Then** the script stops immediately, outputs the failure reason, and the previously deployed version continues serving users.
2. **Given** a `composer install` step fails, **When** the script encounters the error, **Then** the script stops and the live application is unaffected.

---

### User Story 3 — Deployment Progress is Visible (Priority: P3)

The developer running the script can see real-time step-by-step progress in their terminal so they know what is happening and can identify which step completed or failed.

**Why this priority**: Observability reduces anxiety and speeds up incident diagnosis.

**Independent Test**: Run the script and verify each major step (file sync, composer, migrate, cache) prints a labelled status line to the terminal, and the final line confirms either success or the failed step.

**Acceptance Scenarios**:

1. **Given** the script is running, **When** each deployment step executes, **Then** the terminal prints a labelled line (e.g., `[✓] Files synced`, `[✓] Dependencies updated`).
2. **Given** the deployment succeeds, **When** the script finishes, **Then** the terminal prints a final summary confirming the application is live with the new code.
3. **Given** a step fails, **When** the script stops, **Then** the terminal clearly identifies which step failed and suggests a remediation.

---

### Edge Cases

- What happens if the SSH connection to the remote server times out mid-sync?
- What if a file is locked on the remote server during sync (e.g., a log file being written)?
- What if file permission changes are needed after syncing new code?
- What if the `.env` file is missing on the remote server?
- What if `php artisan migrate` prompts for confirmation in production mode?
- What if the sync partially completes before a network failure?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The script MUST be a single executable bash file runnable from the developer's local machine at `/home/nissanka/MSC/techbits`.
- **FR-002**: The script MUST transfer application files from the local source directory to the configured remote server directory over SSH using key-based authentication (no password prompts).
- **FR-003**: The file transfer MUST exclude files and directories that must not be overwritten on the server: `.env`, `storage/`, `vendor/`, `.git/`, and `node_modules/`.
- **FR-004**: The script MUST run `composer install --no-dev --optimize-autoloader` on the remote server after syncing files.
- **FR-005**: The script MUST run `php artisan migrate --force` on the remote server to apply any pending database migrations.
- **FR-006**: The script MUST clear and rebuild the application cache (`config`, `route`, `view`, `event` caches) after a successful migration.
- **FR-007**: The script MUST set correct file/directory permissions on `storage/` and `bootstrap/cache/` after deployment.
- **FR-008**: The script MUST stop immediately and exit with a non-zero code if any step fails, without proceeding to subsequent steps.
- **FR-009**: The script MUST print a labelled progress line to the terminal before and after each major step.
- **FR-010**: The script MUST put the application into maintenance mode before applying changes and take it out of maintenance mode only after all steps complete successfully.
- **FR-011**: If any step fails while the application is in maintenance mode, the script MUST attempt to bring the application back out of maintenance mode before exiting, so it does not remain offline indefinitely.
- **FR-012**: The script MUST read all configurable values (remote host, SSH user, remote app path, local source path) from a configuration section at the top of the file, so they can be changed without editing script logic.

### Key Entities

- **Deploy Configuration**: Remote host, SSH user, remote app directory path, local source directory path.
- **Excluded Paths**: Files/directories never overwritten on the server (`.env`, `storage/`, `vendor/`, `.git/`, `node_modules/`).
- **Deployment Steps**: Ordered operations (maintenance on → file sync → composer → migrate → cache → permissions → maintenance off).
- **Deployment Result**: Success/failure status, which step failed, elapsed time.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A complete deployment from running the script to the updated application being live takes under 3 minutes on a standard connection.
- **SC-002**: The script requires zero manual commands on the remote server — one local command deploys everything.
- **SC-003**: A failed deployment (any step) leaves the live application running without exception — 100% of failure scenarios tested leave the app intact.
- **SC-004**: Every major deployment step produces a visible terminal output line — 0 silent steps.
- **SC-005**: Deployment configuration (host, user, paths) can be changed in under 30 seconds by editing a single section at the top of the script.
- **SC-006**: The server's `.env` file, `storage/` directory, and `vendor/` directory are never overwritten by the sync — verified after every deployment.

## Assumptions

- The remote server already has PHP, Composer, and the application directory set up — this script handles updates only, not initial provisioning.
- SSH key-based access from the developer's machine (`/home/nissanka/MSC/techbits`) to the remote server is already configured and working.
- The `.env` file is already present on the remote server with correct production settings — the script never touches it.
- The application runs on a single remote server (no multi-server or load-balanced deployment).
- The script is run from a Linux/macOS developer machine with `rsync` and `ssh` available.
- There are no queue workers or supervisor processes to restart (out of scope for v1).
- PHP-FPM is not reloaded after deployment — Laravel's config, route, view, and event cache rebuilds are sufficient to serve the new code correctly.
- The `storage/` directory on the remote server holds production uploads and logs — it must never be overwritten by the sync.
