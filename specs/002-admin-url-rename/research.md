# Research: Admin URL Rename

**Date**: 2026-06-07 | **Feature**: 002-admin-url-rename

## Status: No Unknowns

This is a routing-only rename. All decisions were fully resolved during spec and plan phases. No external research required.

## Findings

### Single source of truth for URL path

- **Decision**: Change `->prefix('admin')` in `bootstrap/app.php` only
- **Rationale**: Laravel route prefix is defined once in `bootstrap/app.php`; all URLs are derived from it
- **Alternatives considered**: Changing `routes/admin.php` — not applicable, route definitions inside the file are relative to the group prefix

### Named routes strategy

- **Decision**: Keep all named routes unchanged (`admin.login`, `admin.dashboard`, etc.)
- **Rationale**: Named routes are internal identifiers resolved at runtime; the URL they generate changes automatically when the prefix changes; renaming them would require updating 15+ `route()` call sites for zero benefit
- **Alternatives considered**: Renaming named routes to `backroom.*` — rejected; unnecessary churn

### 404 handler path check

- **Decision**: Update `$request->is('admin/*')` in `bootstrap/app.php` line 36 to `$request->is('tb-backroom-engine/*')`
- **Rationale**: This is a hardcoded string match used to suppress the custom 404 page for the admin area; if not updated, admin 404s would render the storefront error page
- **Alternatives considered**: None — this must match the prefix

### No redirect from old path

- **Decision**: Requests to `/admin/*` return 404 with no redirect
- **Rationale**: A redirect to `/tb-backroom-engine/*` would reveal the new path to anyone probing the old URL, defeating the obscurity intent
- **Alternatives considered**: 301/302 redirect — rejected per spec FR-009

### Blade views

- **Decision**: No changes to any Blade view files
- **Rationale**: Grep confirms zero hardcoded `/admin` path strings in `resources/views/`; all admin links use `route()` helpers which auto-resolve via named routes
